"""
fleet_coordinator.py — Phase 2: Agent Roster + Coordinator.

Long-lived supervisor for the Hermes agent fleet. Inspired by Sand's
node-agent-coordinator. Provides:

- Roster: which agents exist, what they do, how to reach them
- Status tracking: idle | busy | stuck | offline (heartbeat-driven)
- Heartbeat: each agent pings every N seconds; stale = offline
- Task dispatch: queue a task to an agent, get back a handle
- Transcript tail: every dispatch is logged with a sequence number for parity
- CLI: `python fleet_coordinator.py status` / `dispatch` / `tail`

Architecture mirrors Sand's node-agent-coordinator:
- Roster of named harnesses (chronos, enigma, nemo, etc.)
- Each harness has metadata: model, role, discord channel, memory path
- State: active agent, transcript (sequence-numbered), per-agent heartbeat
- Polling policy on heartbeats (uses our policies.py from Phase 1)
- Idle watchdog: if any agent goes silent > 60s, mark stuck

Storage: SQLite at ~/.hermes/coordinator.db. Survives restart.

CLI usage:
    python fleet_coordinator.py status
    python fleet_coordinator.py dispatch --agent chronos --task "restart gateway"
    python fleet_coordinator.py tail --agent enigma --last 5
    python fleet_coordinator.py heartbeat --agent hermes
    python fleet_coordinator.py daemon  # start the supervisor
"""
from __future__ import annotations

import argparse
import asyncio
import json
import os
import sqlite3
import sys
import time
import uuid
from dataclasses import dataclass, asdict, field
from pathlib import Path
from typing import Optional, Dict, List, Any

# Phase 1: shared policy primitives
sys.path.insert(0, "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system")
from policies import polling, idle_watchdog, expiry, retry  # noqa: E402


# ---------- Constants ----------
DB_PATH = Path.home() / ".hermes" / "coordinator.db"
HEARTBEAT_STALE_SEC = 60  # mark offline if no ping in 60s
STUCK_AFTER_SEC = 180     # mark stuck if no progress in 3 min
TRANSCRIPT_RETENTION = 500  # last N entries per agent

# Agent roster — single source of truth for the fleet
ROSTER: Dict[str, dict] = {
    "hermes": {
        "role": "Chief of Staff / Strategist",
        "model": "glm-5.3-flash:cloud",
        "profile_dir": "~/.hermes/profiles/hermes",  # implicit (default)
        "memory": "Ai Brain/agents/hermes/MEMORY.md",
        "discord_channel": "1476025453599789191",
        "discord_name": "#claw-chat",
    },
    "chronos": {
        "role": "Dev / Infra / Scheduler",
        "model": "kimi-k2.7-code",
        "profile_dir": "~/.hermes/profiles/chronos",
        "memory": "Ai Brain/agents/chronos/MEMORY.md",
        "discord_channel": "1272860753535307817",
        "discord_name": "#claw-chronos",
    },
    "enigma": {
        "role": "Semantic SEO Content Architect",
        "model": "qwen3.5:397b",
        "profile_dir": "~/.hermes/profiles/enigma",
        "memory": "Ai Brain/agents/enigma/MEMORY.md",
        "discord_channel": "1482488418532589712",
        "discord_name": "#claw-enigma",
    },
    "emilia": {
        "role": "B2B Outreach & Conversion",
        "model": "minimax-m3",
        "profile_dir": "~/.sheikhown/.hermes/profiles/emilia" if False else "~/.hermes/profiles/emilia",
        "memory": "Ai Brain/agents/emilia/MEMORY.md",
        "discord_channel": "1496584632026796112",
        "discord_name": "#claw-emilea",
    },
    "scout": {
        "role": "SERP Competitor Intelligence",
        "model": "gpt-oss:20b",
        "profile_dir": "~/.hermes/profiles/scout",
        "memory": "Ai Brain/agents/scout/MEMORY.md",
        "discord_channel": "1541761805469225021",
        "discord_name": "#claw-scout",
    },
    "nemo": {
        "role": "Observability & Code Guard",
        "model": "nemotron-3-ultra",
        "profile_dir": "~/.hermes/profiles/nemo",
        "memory": "Ai Brain/agents/nemo/MEMORY.md",
        "discord_channel": "1521550430654431324",
        "discord_name": "#claw-nemo",
    },
    "alpha": {
        "role": "Tactical / Autonomous (isolated)",
        "model": "minimax-m3:free",
        "profile_dir": "~/.hermes/profiles/alpha",
        "memory": None,  # alpha is isolated, no Ai Brain access
        "discord_channel": "1541753228105093241",
        "discord_name": "#claw-alpha",
    },
}


# ---------- Storage ----------
class FleetDB:
    def __init__(self, path: Path = DB_PATH):
        self.path = path
        path.parent.mkdir(parents=True, exist_ok=True)
        self._init_schema()

    def _init_schema(self):
        with sqlite3.connect(self.path) as db:
            db.executescript("""
                CREATE TABLE IF NOT EXISTS agents (
                    name TEXT PRIMARY KEY,
                    role TEXT,
                    model TEXT,
                    discord_channel TEXT,
                    memory_path TEXT,
                    status TEXT DEFAULT 'unknown',  -- offline | idle | busy | stuck
                    last_heartbeat_ms INTEGER DEFAULT 0,
                    last_task TEXT,
                    last_task_started_ms INTEGER,
                    last_task_finished_ms INTEGER,
                    updated_at_ms INTEGER
                );
                CREATE TABLE IF NOT EXISTS transcript (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    agent TEXT NOT NULL,
                    kind TEXT NOT NULL,        -- heartbeat | dispatch | reply | status_change
                    payload TEXT,              -- JSON
                    created_at_ms INTEGER NOT NULL
                );
                CREATE INDEX IF NOT EXISTS idx_transcript_agent
                    ON transcript(agent, id DESC);
            """)
            # seed roster
            now = int(time.time() * 1000)
            for name, meta in ROSTER.items():
                db.execute(
                    "INSERT OR IGNORE INTO agents (name, role, model, discord_channel, memory_path, updated_at_ms) VALUES (?,?,?,?,?,?)",
                    (name, meta["role"], meta["model"], meta["discord_channel"],
                     meta["memory"], now),
                )
            db.commit()

    def record(self, agent: str, kind: str, payload: Optional[dict] = None):
        with sqlite3.connect(self.path) as db:
            db.execute(
                "INSERT INTO transcript (agent, kind, payload, created_at_ms) VALUES (?,?,?,?)",
                (agent, kind, json.dumps(payload) if payload else None, int(time.time() * 1000)),
            )
            # retention: keep last N per agent
            db.execute("""
                DELETE FROM transcript WHERE id IN (
                    SELECT id FROM transcript WHERE agent = ? ORDER BY id DESC LIMIT -1 OFFSET ?
                )
            """, (agent, TRANSCRIPT_RETENTION))
            db.commit()

    def heartbeats(self) -> List[dict]:
        with sqlite3.connect(self.path) as db:
            db.row_factory = sqlite3.Row
            rows = db.execute("SELECT * FROM agents ORDER BY name").fetchall()
            return [dict(r) for r in rows]

    def tail(self, agent: str, last: int = 10) -> List[dict]:
        with sqlite3.connect(self.path) as db:
            db.row_factory = sqlite3.Row
            rows = db.execute(
                "SELECT * FROM transcript WHERE agent = ? ORDER BY id DESC LIMIT ?",
                (agent, last),
            ).fetchall()
            return [
                {"id": r["id"], "kind": r["kind"],
                 "payload": json.loads(r["payload"]) if r["payload"] else None,
                 "created_at_ms": r["created_at_ms"]}
                for r in rows
            ]

    def heartbeat(self, agent: str, status: str = "idle", last_task: Optional[str] = None):
        if agent not in ROSTER:
            raise ValueError(f"unknown agent: {agent}")
        now = int(time.time() * 1000)
        with sqlite3.connect(self.path) as db:
            old = db.execute("SELECT status FROM agents WHERE name = ?", (agent,)).fetchone()
            db.execute(
                "UPDATE agents SET status=?, last_heartbeat_ms=?, last_task=?, updated_at_ms=? WHERE name=?",
                (status, now, last_task, now, agent),
            )
            db.commit()
        if old and old[0] != status:
            self.record(agent, "status_change", {"from": old[0], "to": status})
        else:
            self.record(agent, "heartbeat", {"status": status, "last_task": last_task})

    def dispatch(self, agent: str, task: str) -> str:
        """Record a dispatch + return dispatch ID for tracking."""
        if agent not in ROSTER:
            raise ValueError(f"unknown agent: {agent}")
        dispatch_id = str(uuid.uuid4())[:8]
        self.record(agent, "dispatch", {"id": dispatch_id, "task": task, "status": "queued"})
        with sqlite3.connect(self.path) as db:
            now = int(time.time() * 1000)
            db.execute(
                "UPDATE agents SET status='busy', last_task=?, last_task_started_ms=?, updated_at_ms=? WHERE name=?",
                (task, now, now, agent),
            )
            db.commit()
        return dispatch_id

    def complete(self, agent: str, dispatch_id: str, result: Optional[dict] = None):
        self.record(agent, "reply", {"id": dispatch_id, "result": result, "status": "done"})
        with sqlite3.connect(self.path) as db:
            now = int(time.time() * 1000)
            db.execute(
                "UPDATE agents SET status='idle', last_task_finished_ms=?, updated_at_ms=? WHERE name=?",
                (now, now, agent),
            )
            db.commit()


# ---------- Status computation (uses Phase 1 policies) ----------
def compute_status(agent_row: dict) -> str:
    """Recompute status from heartbeat age. Pure function — used by status command."""
    last_ms = agent_row.get("last_heartbeat_ms", 0) or 0
    if last_ms == 0:
        return "unknown"
    age_sec = (time.time() * 1000 - last_ms) / 1000
    if age_sec > HEARTBEAT_STALE_SEC * 3:
        return "offline"
    if age_sec > HEARTBEAT_STALE_SEC:
        return "stuck"
    # respect explicit status from heartbeat
    return agent_row.get("status", "unknown")


# ---------- CLI ----------
def cmd_status(args):
    db = FleetDB()
    rows = db.heartbeats()
    print(f"{'AGENT':<10} {'STATUS':<10} {'MODEL':<22} {'AGE_SEC':<8} {'LAST_TASK'}")
    print("-" * 80)
    for r in rows:
        age = (time.time() * 1000 - (r["last_heartbeat_ms"] or 0)) / 1000
        # auto-refresh stale
        real_status = compute_status(r)
        if real_status != r["status"]:
            db.heartbeats()  # noop, we just display
        print(f"{r['name']:<10} {real_status:<10} {r['model']:<22} {age:<8.0f} "
              f"{(r['last_task'] or '-')[:40]}")


def cmd_heartbeat(args):
    db = FleetDB()
    db.heartbeat(args.agent, status=args.status or "idle", last_task=args.task)
    print(f"[{args.agent}] heartbeat recorded (status={args.status or 'idle'})")


def cmd_dispatch(args):
    db = FleetDB()
    dispatch_id = db.dispatch(args.agent, args.task)
    print(f"[{args.agent}] dispatch {dispatch_id} queued: {args.task[:60]}")
    print(f"  run agent:  hermes -p {args.agent} chat -q \"{args.task}\"")
    print(f"  mark done:  python fleet_coordinator.py complete --agent {args.agent} --dispatch {dispatch_id}")


def cmd_complete(args):
    db = FleetDB()
    db.complete(args.agent, args.dispatch, result={"note": args.note} if args.note else None)
    print(f"[{args.agent}] dispatch {args.dispatch} marked done")


def cmd_tail(args):
    db = FleetDB()
    rows = db.tail(args.agent, args.last)
    if not rows:
        print(f"(no transcript for {args.agent})")
        return
    for r in rows:
        ts = time.strftime("%H:%M:%S", time.localtime(r["created_at_ms"] / 1000))
        payload_s = json.dumps(r["payload"]) if r["payload"] else ""
        print(f"  {ts}  {r['kind']:<14}  {payload_s[:80]}")


async def _supervisor_tick(db: FleetDB):
    """One tick of the supervisor: check for stale heartbeats, mark stuck/offline."""
    rows = db.heartbeats()
    now_ms = int(time.time() * 1000)
    for r in rows:
        if not r["last_heartbeat_ms"]:
            continue
        age_sec = (now_ms - r["last_heartbeat_ms"]) / 1000
        new_status = r["status"]
        if age_sec > HEARTBEAT_STALE_SEC * 3:
            new_status = "offline"
        elif age_sec > HEARTBEAT_STALE_SEC and r["status"] == "busy":
            new_status = "stuck"
        if new_status != r["status"]:
            db.heartbeat(r["name"], status=new_status,
                         last_task=r.get("last_task"))


async def cmd_daemon(args):
    db = FleetDB()
    print(f"fleet coordinator daemon started (DB: {DB_PATH})")
    print(f"  roster: {len(ROSTER)} agents")
    print(f"  stale threshold: {HEARTBEAT_STALE_SEC}s, stuck: {STUCK_AFTER_SEC}s")
    print(f"  polling every 30s... (Ctrl-C to stop)")

    # Use the polling policy from Phase 1
    p = polling("supervisor", interval_ms=30_000, leading=False)

    async def tick():
        await _supervisor_tick(db)
        # could also emit a status digest here

    try:
        await p.start(tick)
    except asyncio.CancelledError:
        print("\ndaemon stopped")


def main():
    ap = argparse.ArgumentParser(description="Hermes Fleet Coordinator (Phase 2)")
    sub = ap.add_subparsers(dest="cmd", required=True)

    s = sub.add_parser("status", help="Show all agents + status")
    s.set_defaults(func=cmd_status)

    h = sub.add_parser("heartbeat", help="Record an agent heartbeat")
    h.add_argument("--agent", required=True)
    h.add_argument("--status", choices=["idle", "busy", "stuck", "offline"])
    h.add_argument("--task", help="current task description")
    h.set_defaults(func=cmd_heartbeat)

    d = sub.add_parser("dispatch", help="Queue a task to an agent")
    d.add_argument("--agent", required=True)
    d.add_argument("--task", required=True)
    d.set_defaults(func=cmd_dispatch)

    c = sub.add_parser("complete", help="Mark a dispatch done")
    c.add_argument("--agent", required=True)
    c.add_argument("--dispatch", required=True)
    c.add_argument("--note")
    c.set_defaults(func=cmd_complete)

    t = sub.add_parser("tail", help="Show recent transcript for an agent")
    t.add_argument("--agent", required=True)
    t.add_argument("--last", type=int, default=10)
    t.set_defaults(func=cmd_tail)

    dm = sub.add_parser("daemon", help="Run the supervisor (long-lived)")
    dm.set_defaults(func=lambda a: asyncio.run(cmd_daemon(a)))

    args = ap.parse_args()
    args.func(args)


if __name__ == "__main__":
    main()
