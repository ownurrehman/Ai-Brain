"""
fleet_tui.py — Phase 8: live terminal dashboard for the Hermes fleet.

A clean curses-based TUI that shows:
  - Process row per launchd plist (running/down/healing)        [from local_exec_supervisor]
  - Agent row per profile (idle/busy/stuck/offline)              [from coordinator.db heartbeats]
  - Recent dispatches with status                                [from mention_router dispatches]
  - Orphan/stalled alerts                                        [from transcript_parity]

Refreshes every 1s. Colors: green=running, yellow=warning, red=alert,
cyan=info, dim=idle.

Usage:
  python3 fleet_tui.py
  python3 fleet_tui.py --interval 1 --once
"""
from __future__ import annotations

import argparse
import curses
import json
import sqlite3
import subprocess
import sys
import time
from datetime import datetime
from pathlib import Path

ROOT = Path(__file__).parent
DB_PATH = Path.home() / ".hermes" / "coordinator.db"

# (display_name, profile, role)
AGENTS = [
    ("hermes",  "hermes",  "Manager"),
    ("chronos", "chronos", "Dev / Infra"),
    ("enigma",  "enigma",  "Content"),
    ("emilia",  "emilia",  "Outreach"),
    ("scout",   "scout",   "Research"),
    ("nemo",    "nemo",    "Elite Code"),
    ("alpha",   "alpha",   "Sandbox"),
]

# (display_name, plist_label)
PROCESSES = [
    ("hermes-gateway",        "ai.hermes.gateway"),
    ("fleet-coordinator",     "ai.hermes.fleet-coordinator"),
    ("9router",               "ai.9router"),
    ("local-exec-supervisor", "ai.hermes.local-exec-supervisor"),
    ("mention-router",        "ai.hermes.mention-router"),
]


# ---------- Data ----------
def get_process_state() -> list[tuple[str, str, int, int]]:
    """Run local_exec_supervisor status, return [(name, state, pid, fails)]."""
    out = subprocess.run(
        ["python3", str(ROOT / "local_exec_supervisor.py"), "status"],
        capture_output=True, text=True, timeout=10,
    )
    rows = []
    for line in out.stdout.splitlines():
        parts = line.split()
        # expected: name state pid exit fails [healed]
        # name in fleet, len >= 5
        if len(parts) < 5:
            continue
        if parts[0] not in [n for n, _ in PROCESSES] + ["hermes-alpha"]:
            continue
        name = parts[0]
        state = parts[1]
        try:
            pid = int(parts[2])
        except (ValueError, IndexError):
            pid = 0
        try:
            fails = int(parts[4])
        except (ValueError, IndexError):
            fails = 0
        rows.append((name, state, pid, fails))
    return rows


def get_agent_status() -> dict[str, dict]:
    """Read heartbeats from coordinator.db, return {agent: {status, task, age}}."""
    if not DB_PATH.exists():
        return {a: {"status": "?", "task": "", "age": 0} for a, _, _ in AGENTS}
    out = {}
    try:
        with sqlite3.connect(DB_PATH) as db:
            db.row_factory = sqlite3.Row
            for agent, _, _ in AGENTS:
                row = db.execute(
                    """SELECT payload, created_at_ms FROM transcript
                       WHERE agent = ? AND kind = 'heartbeat'
                       ORDER BY id DESC LIMIT 1""", (agent,),
                ).fetchone()
                if row:
                    age = (int(time.time() * 1000) - row["created_at_ms"]) / 1000
                    try:
                        p = json.loads(row["payload"]) if row["payload"] else {}
                    except Exception:
                        p = {}
                    out[agent] = {
                        "status": p.get("status", "idle"),
                        "task": p.get("task", "") or "",
                        "age": age,
                    }
                else:
                    out[agent] = {"status": "no-heartbeat", "task": "", "age": 0}
    except Exception:
        out = {a: {"status": "?", "task": "", "age": 0} for a, _, _ in AGENTS}
    return out


def get_recent_dispatches(last: int = 5) -> list[dict]:
    if not DB_PATH.exists():
        return []
    try:
        with sqlite3.connect(DB_PATH) as db:
            db.row_factory = sqlite3.Row
            rows = db.execute("""
                SELECT id, agent, task, status, created_at_ms, completed_at_ms, result
                FROM dispatches ORDER BY created_at_ms DESC LIMIT ?
            """, (last,)).fetchall()
    except Exception:
        return []
    return [dict(r) for r in rows]


def get_alerts() -> list[str]:
    """Combined alert list from supervisor, parity, stalled."""
    alerts = []
    proc = get_process_state()
    for name, state, pid, fails in proc:
        if "running" not in state:
            alerts.append(f"process {name} is {state}")
    if DB_PATH.exists():
        try:
            with sqlite3.connect(DB_PATH) as db:
                db.row_factory = sqlite3.Row
                # orphans
                dispatches = db.execute(
                    "SELECT id, agent, task, created_at_ms FROM dispatches WHERE status='queued'"
                ).fetchall()
                now = int(time.time() * 1000)
                for d in dispatches:
                    age_min = (now - d["created_at_ms"]) / 60000
                    if age_min > 5:
                        alerts.append(f"orphaned: {d['agent']} {d['id']} ({age_min:.0f}m)")
                # stalled
                stalled = db.execute("""
                    SELECT agent, MAX(created_at_ms) as last_at FROM transcript
                    WHERE kind='heartbeat' GROUP BY agent
                """).fetchall()
                for s in stalled:
                    if not s["last_at"]:
                        continue
                    age_min = (now - s["last_at"]) / 60000
                    if age_min > 10:
                        alerts.append(f"stalled: {s['agent']} (last seen {age_min:.0f}m ago)")
        except Exception:
            pass
    return alerts


# ---------- Formatting ----------
def human_age(sec: float) -> str:
    if sec < 60: return f"{sec:.0f}s"
    if sec < 3600: return f"{sec/60:.0f}m"
    if sec < 86400: return f"{sec/3600:.1f}h"
    return f"{sec/86400:.1f}d"


# ---------- Curses TUI ----------
def run_tui(stdscr, interval: float, once: bool):
    curses.start_color()
    curses.use_default_colors()
    curses.init_pair(1, curses.COLOR_GREEN,  -1)  # running/ok
    curses.init_pair(2, curses.COLOR_YELLOW, -1)  # warning
    curses.init_pair(3, curses.COLOR_RED,    -1)  # alert
    curses.init_pair(4, curses.COLOR_CYAN,   -1)  # info
    curses.init_pair(5, curses.COLOR_WHITE,  -1)  # normal
    curses.init_pair(6, curses.COLOR_BLACK,  curses.COLOR_CYAN)  # header
    curses.curs_set(0)

    stdscr.nodelay(True)  # non-blocking input

    while True:
        stdscr.erase()
        maxy, maxx = stdscr.getmaxyx()

        # ---- Header ----
        header = f" 🛰️  HERMES FLEET  —  {datetime.now().strftime('%Y-%m-%d %H:%M:%S')} "
        stdscr.addstr(0, 0, header.ljust(maxx), curses.color_pair(6) | curses.A_BOLD)

        row = 2

        # ---- Agents ----
        stdscr.addstr(row, 0, "AGENTS", curses.color_pair(4) | curses.A_BOLD)
        row += 1
        agents = get_agent_status()
        for name, profile, role in AGENTS:
            a = agents.get(name, {})
            status = a.get("status", "?")
            age = a.get("age", 0)
            task = a.get("task", "") or ""
            if "busy" in status or "working" in status:
                color = curses.color_pair(2)
                marker = "●"
            elif "stuck" in status or "no-heartbeat" in status:
                color = curses.color_pair(3)
                marker = "✗"
            elif "offline" in status:
                color = curses.color_pair(3)
                marker = "○"
            else:
                color = curses.color_pair(1)
                marker = "✓"
            line = f"  {marker} {name:<8} {role:<12} {status:<14} {human_age(age):<6} {task[:maxx-50]}"
            stdscr.addnstr(row, 0, line, maxx, color)
            row += 1

        row += 1
        # ---- Processes ----
        stdscr.addstr(row, 0, "PROCESSES (launchd)", curses.color_pair(4) | curses.A_BOLD)
        row += 1
        procs = get_process_state()
        if not procs:
            stdscr.addstr(row, 0, "  (none reporting)", curses.A_DIM)
            row += 1
        for name, state, pid, fails in procs:
            if "running" in state:
                color = curses.color_pair(1)
                marker = "✓"
            elif "intentionally" in state:
                color = curses.color_pair(5) | curses.A_DIM
                marker = "—"
            else:
                color = curses.color_pair(3)
                marker = "✗"
            pid_str = f"pid={pid}" if pid else ""
            line = f"  {marker} {name:<24} {state:<35} {pid_str}"
            stdscr.addnstr(row, 0, line, maxx, color)
            row += 1

        row += 1
        # ---- Recent dispatches ----
        stdscr.addstr(row, 0, "RECENT DISPATCHES (mention router)", curses.color_pair(4) | curses.A_BOLD)
        row += 1
        dispatches = get_recent_dispatches(5)
        if not dispatches:
            stdscr.addstr(row, 0, "  (none)", curses.A_DIM)
            row += 1
        for d in dispatches:
            ts = datetime.fromtimestamp(d["created_at_ms"] / 1000).strftime("%H:%M:%S")
            mark = "✓" if d["status"] == "done" else "⋯"
            color = curses.color_pair(1) if d["status"] == "done" else curses.color_pair(2)
            line = f"  {mark} {ts}  {d['agent']:<8} {d['id']}  {d['task'][:maxx-40]}"
            stdscr.addnstr(row, 0, line, maxx, color)
            row += 1

        row += 1
        # ---- Alerts ----
        stdscr.addstr(row, 0, "ALERTS", curses.color_pair(4) | curses.A_BOLD)
        row += 1
        alerts = get_alerts()
        if not alerts:
            stdscr.addstr(row, 0, "  ✓ all systems green", curses.color_pair(1))
            row += 1
        else:
            for a in alerts:
                stdscr.addnstr(row, 0, f"  ✗ {a}", maxx, curses.color_pair(3))
                row += 1

        # ---- Footer ----
        footer = " [q] quit  [r] refresh  [h] help"
        stdscr.addstr(maxy - 1, 0, footer.ljust(maxx), curses.A_DIM)

        stdscr.refresh()

        if once:
            return

        # Wait for interval or keypress
        deadline = time.time() + interval
        while time.time() < deadline:
            try:
                key = stdscr.getch()
                if key in (ord("q"), ord("Q"), 27):
                    return
                if key in (ord("r"), ord("R")):
                    break
                if key in (ord("h"), ord("H")):
                    show_help(stdscr)
                    break
                time.sleep(0.1)
            except KeyboardInterrupt:
                return


def show_help(stdscr):
    maxy, maxx = stdscr.getmaxyx()
    stdscr.erase()
    lines = [
        "  HERMES FLEET TUI — Help",
        "",
        "  This dashboard shows the live state of:",
        "    - All 7 Hermes agents (heartbeat + current task)",
        "    - All 5 launchd-managed daemon processes",
        "    - Recent dispatches (mention router Phase 7)",
        "    - Active alerts (orphans, stalled agents, down procs)",
        "",
        "  Keys:",
        "    q  quit",
        "    r  refresh now",
        "    h  show this help",
        "",
        "  Color legend:",
        "    green  ok / running / done",
        "    yellow warning / busy / in-progress",
        "    red    error / offline / orphaned",
        "    cyan   headers",
        "",
        "  Press any key to return...",
    ]
    for i, line in enumerate(lines):
        stdscr.addnstr(i, 0, line, maxx, curses.color_pair(4) if i == 0 else curses.color_pair(5))
    stdscr.getch()


def main():
    ap = argparse.ArgumentParser(description="Hermes fleet live TUI (Phase 8)")
    ap.add_argument("--interval", type=float, default=1.0, help="refresh seconds")
    ap.add_argument("--once", action="store_true", help="render once and exit (non-interactive)")
    args = ap.parse_args()
    if args.once:
        # Print plain-text version
        print(f"# HERMES FLEET — {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
        print("\n## AGENTS")
        agents = get_agent_status()
        for name, profile, role in AGENTS:
            a = agents.get(name, {})
            print(f"  {name:<8} {role:<12} {a.get('status','?'):<14} {human_age(a.get('age',0)):<6} {a.get('task','')}")
        print("\n## PROCESSES")
        for name, state, pid, fails in get_process_state():
            print(f"  {name:<24} {state:<35} pid={pid} fails={fails}")
        print("\n## DISPATCHES")
        for d in get_recent_dispatches(5):
            ts = datetime.fromtimestamp(d["created_at_ms"] / 1000).strftime("%H:%M:%S")
            mark = "✓" if d["status"] == "done" else "⋯"
            print(f"  {mark} {ts}  {d['agent']:<8} {d['id']}  {d['task']}")
        print("\n## ALERTS")
        alerts = get_alerts()
        if not alerts:
            print("  ✓ all systems green")
        for a in alerts:
            print(f"  ✗ {a}")
        return
    curses.wrapper(lambda s: run_tui(s, args.interval, once=False))


if __name__ == "__main__":
    main()
