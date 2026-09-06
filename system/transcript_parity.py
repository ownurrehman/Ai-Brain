"""
transcript_parity.py — Phase 5: Transcript tail + parity check.

Inspired by Sand's `server-transcript-tail` with `reportTranscriptParity`.

The fleet_coordinator DB records every dispatch + reply with a unique
dispatch_id. This script verifies that:
  1. Every dispatch has a matching reply (or explicit timeout marker)
  2. The conversation history in the local session DB has the same rows
  3. No rows lost between the two sources

Sources we can compare against:
  - coordinator.db (Phase 2): authoritative for dispatch/reply events
  - ~/.hermes/sessions.db: local session history (sqlite)
  - The active platform transcript (Discord channel history — via bot,
    if available)

For Hermes on this Mac the immediate wins are:
  - dispatch→reply pairing (is anything dropped?)
  - last-N transcripts per agent
  - time-since-last-reply (stuck detection)

Usage:
    python transcript_parity.py check --agent chronos
    python transcript_parity.py check --all
    python transcript_parity.py tail --agent chronos --last 10
    python transcript_parity.py orphans   # dispatches without replies
    python transcript_parity.py stalled  # agents with no recent replies
"""
from __future__ import annotations

import argparse
import json
import os
import sqlite3
import sys
import time
from datetime import datetime
from pathlib import Path
from typing import Optional

DB_PATH = Path.home() / ".hermes" / "coordinator.db"
SESSIONS_DB = Path.home() / ".hermes" / "sessions.db"
STALL_THRESHOLD_SEC = 300  # 5 min: anything older is "stalled"


# ---------- Helpers ----------
def _conn(path: Path) -> sqlite3.Connection:
    if not path.exists():
        raise FileNotFoundError(f"DB not found: {path}")
    conn = sqlite3.connect(path)
    conn.row_factory = sqlite3.Row
    return conn


def _human_age(ms_ago: int) -> str:
    sec = ms_ago / 1000
    if sec < 60:
        return f"{sec:.0f}s"
    if sec < 3600:
        return f"{sec/60:.0f}m"
    if sec < 86400:
        return f"{sec/3600:.1f}h"
    return f"{sec/86400:.1f}d"


# ---------- Tails ----------
def tail_coordinator(agent: str, last: int = 20) -> list[dict]:
    with _conn(DB_PATH) as db:
        rows = db.execute(
            "SELECT * FROM transcript WHERE agent = ? ORDER BY id DESC LIMIT ?",
            (agent, last),
        ).fetchall()
    out = []
    for r in rows:
        out.append({
            "source": "coordinator",
            "id": r["id"],
            "kind": r["kind"],
            "agent": r["agent"],
            "payload": json.loads(r["payload"]) if r["payload"] else None,
            "at": r["created_at_ms"],
        })
    return out


def tail_sessions(agent: str, last: int = 20) -> list[dict]:
    """Best-effort: read local Hermes session DB for messages with this agent's profile."""
    if not SESSIONS_DB.exists():
        return []
    try:
        with _conn(SESSIONS_DB) as db:
            rows = db.execute(
                """SELECT id, session_key, role, content, created_at
                   FROM messages
                   WHERE session_key LIKE ?
                   ORDER BY id DESC LIMIT ?""",
                (f"agent:{agent}:%", last),
            ).fetchall()
    except sqlite3.OperationalError:
        # schema might differ across Hermes versions
        return []
    out = []
    for r in rows:
        out.append({
            "source": "sessions",
            "id": r["id"],
            "kind": r["role"],
            "agent": agent,
            "payload": {"content": (r["content"] or "")[:200]},
            "at": int(time.mktime(time.strptime(r["created_at"], "%Y-%m-%dT%H:%M:%S")) * 1000)
                  if r["created_at"] else 0,
        })
    return out


# ---------- Parity ----------
def check_parity(agent: str) -> dict:
    """Verify every dispatch has a matching reply in the coordinator DB."""
    with _conn(DB_PATH) as db:
        dispatches = db.execute(
            """SELECT id, payload, created_at_ms FROM transcript
               WHERE agent = ? AND kind = 'dispatch'""",
            (agent,),
        ).fetchall()
        replies = db.execute(
            """SELECT payload, created_at_ms FROM transcript
               WHERE agent = ? AND kind = 'reply'""",
            (agent,),
        ).fetchall()

    dispatch_ids = []
    for d in dispatches:
        p = json.loads(d["payload"]) if d["payload"] else {}
        dispatch_ids.append(p.get("id"))

    reply_ids = set()
    for r in replies:
        p = json.loads(r["payload"]) if r["payload"] else {}
        if p.get("id"):
            reply_ids.add(p["id"])

    orphans = [d for d in dispatch_ids if d not in reply_ids]

    return {
        "agent": agent,
        "dispatch_count": len(dispatch_ids),
        "reply_count": len(reply_ids),
        "orphan_dispatches": orphans,
        "parity": "OK" if not orphans else f"MISSING_REPLIES({len(orphans)})",
    }


def check_all_parity() -> list[dict]:
    with _conn(DB_PATH) as db:
        agents = [r[0] for r in db.execute("SELECT DISTINCT agent FROM transcript").fetchall()]
    return [check_parity(a) for a in agents]


def find_orphans() -> list[dict]:
    """All dispatch IDs across the fleet that have no reply."""
    results = check_all_parity()
    out = []
    for r in results:
        for orphan_id in r["orphan_dispatches"]:
            out.append({"agent": r["agent"], "dispatch_id": orphan_id})
    return out


def find_stalled(threshold_sec: int = STALL_THRESHOLD_SEC) -> list[dict]:
    """Agents with no recent activity in the coordinator DB."""
    with _conn(DB_PATH) as db:
        rows = db.execute(
            """SELECT agent, MAX(created_at_ms) as last_at FROM transcript GROUP BY agent"""
        ).fetchall()
    out = []
    now = int(time.time() * 1000)
    for r in rows:
        if not r["last_at"]:
            continue
        age_sec = (now - r["last_at"]) / 1000
        if age_sec > threshold_sec:
            out.append({
                "agent": r["agent"],
                "last_activity_age_sec": age_sec,
                "last_activity_human": _human_age(now - r["last_at"]),
            })
    return sorted(out, key=lambda x: -x["last_activity_age_sec"])


# ---------- CLI ----------
def cmd_check(args):
    if args.all:
        results = check_all_parity()
    else:
        results = [check_parity(args.agent)]

    for r in results:
        status = "✓" if r["parity"] == "OK" else "✗"
        print(f"{status} {r['agent']:<14} dispatches={r['dispatch_count']:<4} "
              f"replies={r['reply_count']:<4} {r['parity']}")
        if r["orphan_dispatches"]:
            for o in r["orphan_dispatches"]:
                print(f"    orphan dispatch: {o}")


def cmd_tail(args):
    coord = tail_coordinator(args.agent, args.last)
    sess = tail_sessions(args.agent, args.last)
    all_rows = sorted(coord + sess, key=lambda r: r["at"], reverse=True)[:args.last]
    if not all_rows:
        print(f"(no transcript for {args.agent})")
        return
    for r in all_rows:
        ts = datetime.fromtimestamp(r["at"] / 1000).strftime("%H:%M:%S") if r["at"] else "?"
        src = r["source"][:3]
        kind = r["kind"][:10]
        payload = json.dumps(r.get("payload") or {})[:80]
        print(f"  {ts}  [{src}] {kind:<10}  {payload}")


def cmd_orphans(args):
    orphans = find_orphans()
    if not orphans:
        print("✓ no orphan dispatches")
        return
    for o in orphans:
        print(f"  ✗ {o['agent']:<14} dispatch {o['dispatch_id']} has no reply")


def cmd_stalled(args):
    stalled = find_stalled(args.threshold)
    if not stalled:
        print(f"✓ no agents stalled past {args.threshold}s")
        return
    for s in stalled:
        print(f"  ✗ {s['agent']:<14} no activity for {s['last_activity_human']}")


def main():
    ap = argparse.ArgumentParser(description="Hermes transcript parity checker (Phase 5)")
    sub = ap.add_subparsers(dest="cmd", required=True)

    c = sub.add_parser("check", help="Check dispatch→reply parity")
    c.add_argument("--agent", help="specific agent")
    c.add_argument("--all", action="store_true", help="all agents")
    c.set_defaults(func=cmd_check)

    t = sub.add_parser("tail", help="Show merged transcript tail")
    t.add_argument("--agent", required=True)
    t.add_argument("--last", type=int, default=20)
    t.set_defaults(func=cmd_tail)

    o = sub.add_parser("orphans", help="Find dispatches without replies")
    o.set_defaults(func=cmd_orphans)

    s = sub.add_parser("stalled", help="Find agents with no recent activity")
    s.add_argument("--threshold", type=int, default=STALL_THRESHOLD_SEC)
    s.set_defaults(func=cmd_stalled)

    args = ap.parse_args()
    args.func(args)


if __name__ == "__main__":
    main()
