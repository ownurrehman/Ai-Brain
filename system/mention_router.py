"""
mention_router.py — Phase 7: Inter-agent @-mention routing for #claw-chat.

The hub-and-spoke model. Hermes is the only bot in #claw-chat that the
user talks to. When the user @-mentions another agent (or hermes decides
another agent should handle something), this module:

  1. Detects @-mentions in incoming messages
  2. Dispatches the task to the right agent via fleet_coordinator
  3. Subscribes to the reply event for that dispatch
  4. When the reply arrives, posts a clear "via @agent" message in #claw-chat

User experience:
  user in #claw-chat: "chronos, restart 9router please"
  hermes: "→ dispatching to @chronos (id abc12345)"
  chronos runs, returns: "Done. 9router running on 20128"
  hermes: "← @chronos: Done. 9router running on 20128"

This is the "agents talk to each other" feature that Sand has, but
without needing a separate bot per agent in the same channel.
"""
from __future__ import annotations

import argparse
import json
import re
import sqlite3
import sys
import time
from pathlib import Path
from typing import Optional

DB_PATH = Path.home() / ".hermes" / "coordinator.db"

# Map of @-mention tokens -> agent profile name
AGENT_TOKENS = {
    "hermes":  "hermes",
    "chronos": "chronos",
    "enigma":  "enigma",
    "emilia":  "emilia",
    "emilea":  "emilia",  # misspelling in memory
    "scout":   "scout",
    "nemo":    "nemo",
    "alpha":   "alpha",
    "atlas":   "atlas",
}

# Detect @-mention or "<Agent>, ..." prefix
MENTION_PAT = re.compile(
    r"@(" + "|".join(AGENT_TOKENS.keys()) + r")\b,?",
    re.IGNORECASE,
)
PREFIX_PAT = re.compile(
    r"^\s*(" + "|".join(AGENT_TOKENS.keys()) + r")\s*[,;:\-]\s*",
    re.IGNORECASE,
)


def parse_targets(text: str) -> tuple[list[str], str]:
    """Return (list_of_agents, remaining_text_without_mentions)."""
    targets = []
    for m in MENTION_PAT.finditer(text):
        agent = AGENT_TOKENS[m.group(1).lower()]
        if agent not in targets:
            targets.append(agent)
    cleaned = MENTION_PAT.sub("", text).strip()
    # also strip leading "chronos, ..." style
    for _ in range(3):
        m2 = PREFIX_PAT.match(cleaned)
        if not m2:
            break
        agent = AGENT_TOKENS[m2.group(1).lower()]
        if agent not in targets:
            targets.append(agent)
        cleaned = PREFIX_PAT.sub("", cleaned, count=1).strip()
    return targets, cleaned


def new_dispatch(agent: str, task: str, source_channel: str = "claw-chat") -> str:
    """Create a dispatch in coordinator.db. Returns dispatch_id."""
    import secrets
    dispatch_id = secrets.token_hex(4)
    with sqlite3.connect(DB_PATH) as db:
        db.execute("""
            INSERT INTO transcript (agent, kind, payload, created_at_ms)
            VALUES (?, 'dispatch', ?, ?)
        """, (agent, json.dumps({
            "id": dispatch_id,
            "task": task,
            "source": source_channel,
            "status": "queued",
        }), int(time.time() * 1000)))
        db.execute("""
            INSERT INTO dispatches (id, agent, task, source, status, created_at_ms)
            VALUES (?, ?, ?, ?, 'queued', ?)
        """, (dispatch_id, agent, task, source_channel, int(time.time() * 1000)))
        db.commit()
    return dispatch_id


def mark_done(agent: str, dispatch_id: str, result: str = ""):
    with sqlite3.connect(DB_PATH) as db:
        db.execute("""
            UPDATE dispatches SET status='done', completed_at_ms=?, result=?
            WHERE id=? AND agent=?
        """, (int(time.time() * 1000), result, dispatch_id, agent))
        db.execute("""
            INSERT INTO transcript (agent, kind, payload, created_at_ms)
            VALUES (?, 'reply', ?, ?)
        """, (agent, json.dumps({
            "id": dispatch_id,
            "result": result,
            "status": "done",
        }), int(time.time() * 1000)))
        db.commit()


def get_reply(dispatch_id: str) -> Optional[dict]:
    with sqlite3.connect(DB_PATH) as db:
        db.row_factory = sqlite3.Row
        row = db.execute(
            "SELECT * FROM dispatches WHERE id = ?", (dispatch_id,)
        ).fetchone()
    if not row:
        return None
    return dict(row)


# ---------- Bootstrap dispatches table ----------
def ensure_schema():
    with sqlite3.connect(DB_PATH) as db:
        # Add dispatches table if missing (fleet_coordinator doesn't create it)
        db.executescript("""
            CREATE TABLE IF NOT EXISTS dispatches (
                id TEXT PRIMARY KEY,
                agent TEXT NOT NULL,
                task TEXT NOT NULL,
                source TEXT,
                status TEXT DEFAULT 'queued',
                created_at_ms INTEGER NOT NULL,
                completed_at_ms INTEGER,
                result TEXT
            );
            CREATE INDEX IF NOT EXISTS idx_dispatches_status ON dispatches(status);
            CREATE INDEX IF NOT EXISTS idx_dispatches_agent ON dispatches(agent);
        """)
        db.commit()


# ---------- Daemon ----------
def cmd_route(args):
    """Simulate routing: parse a message, dispatch the targets, return JSON plan."""
    ensure_schema()
    text = args.text
    targets, cleaned = parse_targets(text)
    if not targets:
        print(json.dumps({"targets": [], "remaining": cleaned, "action": "hermes-self"}))
        return
    if args.execute:
        dispatched = []
        for t in targets:
            if t == "hermes":
                # user is talking to hermes directly, no dispatch needed
                continue
            did = new_dispatch(t, cleaned, source_channel=args.channel)
            dispatched.append({"agent": t, "dispatch_id": did, "task": cleaned})
        print(json.dumps({
            "targets": targets,
            "remaining": cleaned,
            "dispatched": dispatched,
            "action": "dispatched",
        }, indent=2))
    else:
        print(json.dumps({
            "targets": targets,
            "remaining": cleaned,
            "action": "would-dispatch (use --execute to commit)",
        }, indent=2))


def cmd_reply(args):
    ensure_schema()
    mark_done(args.agent, args.dispatch, args.result or "")
    print(f"✓ marked {args.agent}/{args.dispatch} done")


def cmd_watch(args):
    """Daemon: poll for completed dispatches and emit a JSON line per event.
    Use this with `jq` or a Discord bot to forward replies to #claw-chat."""
    ensure_schema()
    import select
    import os
    last_seen = int(time.time() * 1000)
    print(f"# watching for dispatch completions on {DB_PATH}", flush=True)
    while True:
        with sqlite3.connect(DB_PATH) as db:
            db.row_factory = sqlite3.Row
            rows = db.execute("""
                SELECT id, agent, task, result, completed_at_ms
                FROM dispatches
                WHERE status = 'done' AND completed_at_ms > ?
                ORDER BY completed_at_ms ASC
            """, (last_seen,)).fetchall()
        for r in rows:
            payload = {
                "event": "dispatch.done",
                "dispatch_id": r["id"],
                "agent": r["agent"],
                "task": r["task"],
                "result": r["result"],
                "completed_at_ms": r["completed_at_ms"],
            }
            print(json.dumps(payload), flush=True)
            last_seen = max(last_seen, r["completed_at_ms"])
        time.sleep(args.interval)


# ---------- CLI ----------
def main():
    ap = argparse.ArgumentParser(description="Hermes inter-agent mention router (Phase 7)")
    sub = ap.add_subparsers(dest="cmd", required=True)

    r = sub.add_parser("route", help="Parse message + dispatch to mentioned agents")
    r.add_argument("text", help="user message to parse")
    r.add_argument("--channel", default="claw-chat")
    r.add_argument("--execute", action="store_true", help="actually create dispatches")
    r.set_defaults(func=cmd_route)

    re = sub.add_parser("reply", help="Mark a dispatch done (called when agent finishes)")
    re.add_argument("--agent", required=True)
    re.add_argument("--dispatch", required=True)
    re.add_argument("--result", default="")
    re.set_defaults(func=cmd_reply)

    w = sub.add_parser("watch", help="Daemon: emit JSON events when dispatches complete")
    w.add_argument("--interval", type=float, default=1.0)
    w.set_defaults(func=cmd_watch)

    args = ap.parse_args()
    args.func(args)


if __name__ == "__main__":
    main()
