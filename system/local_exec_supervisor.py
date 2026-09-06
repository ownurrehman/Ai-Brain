"""
local_exec_supervisor.py — Phase 4: Local-exec supervisor for the fleet.

Watches every launchd process related to the Hermes agent fleet and:
  - detects stuck/zombie processes (CPU 0%, running but no log activity)
  - auto-restarts failed plists via `launchctl kickstart -k`
  - reports health to the fleet coordinator DB (Phase 2)
  - keeps an audit log of every intervention

Mirrors Sand's `local-exec` subsystem (y_/supervisor-of-supervisors) but
focused on launchd plists since that's how Hermes runs.

Usage:
    python local_exec_supervisor.py status
    python local_exec_supervisor.py watch        # run forever, poll every 30s
    python local_exec_supervisor.py restart hermes
    python local_exec_supervisor.py audit --last 20
"""
from __future__ import annotations

import argparse
import json
import os
import sqlite3
import subprocess
import sys
import time
from datetime import datetime
from pathlib import Path
from typing import Optional

sys.path.insert(0, "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system")
from policies import polling  # noqa: E402

# Same DB as fleet_coordinator so they share state
DB_PATH = Path.home() / ".hermes" / "coordinator.db"

# Known fleet launchd plists
# enabled=False means "do not auto-heal" (intentionally disabled, sandbox, etc.)
FLEET_PLISTS = [
    ("hermes-gateway",        "ai.hermes.gateway",              "/tmp/hermes-gateway.out.log",          True),
    ("fleet-coordinator",     "ai.hermes.fleet-coordinator",    "/tmp/fleet-coordinator.out.log",       True),
    ("9router",               "ai.9router",                     "/tmp/9router.out.log",                 True),
    ("hermes-alpha",          "ai.hermes.gateway-alpha",        "/tmp/alpha.out.log",                   False),  # intentionally isolated
]


# ---------- launchd helpers ----------
def _launchctl(*args) -> tuple[int, str, str]:
    r = subprocess.run(["launchctl", *args], capture_output=True, text=True, timeout=10)
    return r.returncode, r.stdout.strip(), r.stderr.strip()


def plist_status(label: str) -> dict:
    """Check if a launchd plist is loaded + last exit code + PID."""
    # Try multiple domains: user GUI, user DB, system
    domains = [f"gui/{os.getuid()}", f"user/{os.getuid()}"]
    info = {"label": label, "loaded": False, "pid": None, "last_exit": None, "raw": ""}
    for domain in domains:
        rc, out, err = _launchctl("print", f"{domain}/{label}")
        if rc == 0:
            info["loaded"] = True
            info["raw"] = out
            for line in out.splitlines():
                s = line.strip()
                if s.startswith("pid ="):
                    try:
                        info["pid"] = int(s.split("=")[1].strip())
                    except ValueError:
                        pass
                elif s.startswith("last exit code ="):
                    try:
                        info["last_exit"] = int(s.split("=")[1].strip())
                    except ValueError:
                        pass
            return info
    info["error"] = f"not found in domains: {domains}"
    return info


def plist_restart(label: str) -> tuple[bool, str]:
    """Force-kill + restart a launchd plist. Returns (success, message)."""
    rc, out, err = _launchctl("kickstart", "-k", f"gui/{os.getuid()}/{label}")
    if rc == 0:
        return True, out or "restarted"
    return False, err or f"exit {rc}"


def plist_log_tail(path: str, lines: int = 20) -> str:
    try:
        with open(path) as f:
            return "".join(f.readlines()[-lines:]).strip()
    except (FileNotFoundError, PermissionError):
        return ""


# ---------- Supervisor ----------
class LocalExecSupervisor:
    def __init__(self):
        self._init_db()

    def _init_db(self):
        with sqlite3.connect(DB_PATH) as db:
            db.executescript("""
                CREATE TABLE IF NOT EXISTS supervised (
                    name TEXT PRIMARY KEY,
                    label TEXT NOT NULL,
                    log_path TEXT,
                    enabled INTEGER DEFAULT 1,
                    last_check_ms INTEGER,
                    last_state TEXT,
                    last_pid INTEGER,
                    consecutive_failures INTEGER DEFAULT 0
                );
                CREATE TABLE IF NOT EXISTS supervisor_audit (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    name TEXT,
                    action TEXT,
                    ok INTEGER,
                    detail TEXT,
                    created_at_ms INTEGER NOT NULL
                );
            """)
            now = int(time.time() * 1000)
            for name, label, log, enabled in FLEET_PLISTS:
                db.execute(
                    "INSERT OR IGNORE INTO supervised (name, label, log_path, enabled, last_check_ms) VALUES (?,?,?,?,?)",
                    (name, label, log, 1 if enabled else 0, now),
                )
            db.commit()

    def audit(self, name: str, action: str, ok: bool, detail: str = ""):
        with sqlite3.connect(DB_PATH) as db:
            db.execute(
                "INSERT INTO supervisor_audit (name, action, ok, detail, created_at_ms) VALUES (?,?,?,?,?)",
                (name, action, 1 if ok else 0, detail, int(time.time() * 1000)),
            )
            db.commit()

    def check_one(self, name: str) -> dict:
        with sqlite3.connect(DB_PATH) as db:
            db.row_factory = sqlite3.Row
            row = db.execute("SELECT * FROM supervised WHERE name = ?", (name,)).fetchone()
        if not row:
            return {"error": f"unknown: {name}"}

        info = plist_status(row["label"])
        now_ms = int(time.time() * 1000)
        log_tail = plist_log_tail(row["log_path"], 5)
        state = "unknown"
        consecutive_failures = row["consecutive_failures"]
        if not info["loaded"]:
            state = "not-loaded"
            consecutive_failures += 1
        elif info["pid"] is None:
            state = "loaded-no-pid"  # plist loaded but no process
            consecutive_failures += 1
        elif info["last_exit"] not in (None, 0):
            state = f"crashed-exit-{info['last_exit']}"
            consecutive_failures += 1
        else:
            state = "running"
            consecutive_failures = 0

        # Auto-heal: if 2 consecutive failures, try restart
        healed = False
        if consecutive_failures >= 2 and state != "running":
            ok, msg = plist_restart(row["label"])
            self.audit(name, "auto-restart", ok, f"state={state} msg={msg}")
            if ok:
                state = "healing"
                consecutive_failures = 0
                healed = True

        with sqlite3.connect(DB_PATH) as db:
            db.execute("""
                UPDATE supervised SET last_check_ms=?, last_state=?, last_pid=?, consecutive_failures=?
                WHERE name=?
            """, (now_ms, state, info["pid"], consecutive_failures, name))
            db.commit()

        return {
            "name": name,
            "label": row["label"],
            "state": state,
            "pid": info["pid"],
            "last_exit": info["last_exit"],
            "consecutive_failures": consecutive_failures,
            "log_tail": log_tail,
            "healed": healed,
        }

    def check_all(self) -> list[dict]:
        return [self.check_one(name) for name, _, _ in FLEET_PLISTS]

    def audit_tail(self, n: int = 20) -> list[dict]:
        with sqlite3.connect(DB_PATH) as db:
            db.row_factory = sqlite3.Row
            rows = db.execute(
                "SELECT * FROM supervisor_audit ORDER BY id DESC LIMIT ?", (n,)
            ).fetchall()
            return [dict(r) for r in rows]


# ---------- CLI ----------
def cmd_status(args):
    s = LocalExecSupervisor()
    rows = s.check_all()
    print(f"{'NAME':<20} {'STATE':<22} {'PID':<8} {'EXIT':<6} {'FAILS':<6} HEALED")
    print("-" * 75)
    for r in rows:
        print(f"{r['name']:<20} {r['state']:<22} "
              f"{(r.get('pid') or '-'):<8} {(r.get('last_exit') or '-'):<6} "
              f"{r['consecutive_failures']:<6} {'YES' if r.get('healed') else ''}")
        if args.logs and r.get("log_tail"):
            print("  log tail:")
            for line in r["log_tail"].splitlines()[:5]:
                print(f"    {line}")


def cmd_watch(args):
    s = LocalExecSupervisor()
    print(f"supervisor watching {len(FLEET_PLISTS)} processes...")
    p = polling("supervisor", interval_ms=args.interval * 1000, leading=True)

    async def tick():
        ts = datetime.now().strftime("%H:%M:%S")
        rows = s.check_all()
        bad = [r for r in rows if r["state"] != "running"]
        if bad:
            print(f"[{ts}] ALERT: {len(bad)} process(es) not running:")
            for r in bad:
                print(f"  - {r['name']}: {r['state']} (fails={r['consecutive_failures']})"
                      f"{' [HEALED]' if r.get('healed') else ''}")
        else:
            print(f"[{ts}] OK: all {len(rows)} processes running")

    import asyncio
    asyncio.run(p.start(tick))


def cmd_restart(args):
    ok, msg = plist_restart(args.label)
    s = LocalExecSupervisor()
    s.audit(args.label, "manual-restart", ok, msg)
    print(f"[{args.label}] {'OK' if ok else 'FAIL'}: {msg}")
    sys.exit(0 if ok else 1)


def cmd_audit(args):
    s = LocalExecSupervisor()
    for r in s.audit_tail(args.last):
        ts = datetime.fromtimestamp(r["created_at_ms"] / 1000).strftime("%H:%M:%S")
        ok = "OK" if r["ok"] else "FAIL"
        print(f"  {ts}  {r['name']:<22} {r['action']:<16} {ok}  {r['detail']}")


def main():
    ap = argparse.ArgumentParser(description="Hermes local-exec supervisor (Phase 4)")
    sub = ap.add_subparsers(dest="cmd", required=True)

    s = sub.add_parser("status", help="Check all fleet processes")
    s.add_argument("--logs", action="store_true", help="include last 5 log lines")
    s.set_defaults(func=cmd_status)

    w = sub.add_parser("watch", help="Poll forever, alert on failures")
    w.add_argument("--interval", type=int, default=30, help="seconds between polls")
    w.set_defaults(func=cmd_watch)

    r = sub.add_parser("restart", help="Manually restart a process")
    r.add_argument("label", help="launchd label (e.g. ai.hermes.gateway)")
    r.set_defaults(func=cmd_restart)

    a = sub.add_parser("audit", help="Show recent interventions")
    a.add_argument("--last", type=int, default=20)
    a.set_defaults(func=cmd_audit)

    args = ap.parse_args()
    args.func(args)


if __name__ == "__main__":
    main()
