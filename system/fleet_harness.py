"""
fleet_harness.py — Phase 6: unified fleet command.

One entry point that exercises all 5 prior phases:

  python fleet_harness.py status      -> full fleet dashboard
  python fleet_harness.py health       -> processes + parity + orphans + stalled
  python fleet_harness.py dispatch ...-> queue a task
  python fleet_harness.py oauth ...   -> run loopback OAuth
  python fleet_harness.py heartbeat ...-> ping
  python fleet_harness.py tail ...     -> transcript

This is the "run all the things" surface. The other 5 scripts remain
standalone for power users; this just composes them.
"""
from __future__ import annotations

import argparse
import subprocess
import sys
import time
from datetime import datetime
from pathlib import Path

ROOT = Path(__file__).parent
DB_PATH = Path.home() / ".hermes" / "coordinator.db"

COORDS = ["hermes", "chronos", "enigma", "emilia", "scout", "nemo", "alpha"]


def _run(script: str, *args: str, timeout: int = 15) -> str:
    """Run a sibling script and return its stdout."""
    cmd = ["python3", str(ROOT / script), *args]
    try:
        r = subprocess.run(cmd, capture_output=True, text=True, timeout=timeout)
        return (r.stdout or "") + (("\n" + r.stderr) if r.stderr else "")
    except subprocess.TimeoutExpired:
        return f"(timeout after {timeout}s)"
    except Exception as e:
        return f"(error: {e})"


def _section(title: str):
    print()
    print(f"=== {title} ===")


# ---------- Commands ----------
def cmd_status(args):
    """Compact dashboard across all phases."""
    print(f"# Hermes Fleet Status — {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    _section("Local-exec supervisor (Phase 4)")
    print(_run("local_exec_supervisor.py", "status", timeout=10).rstrip())
    _section("Fleet coordinator (Phase 2)")
    print(_run("fleet_coordinator.py", "status", timeout=10).rstrip())
    _section("Transcript parity (Phase 5)")
    print(_run("transcript_parity.py", "check", "--all", timeout=10).rstrip())
    print(_run("transcript_parity.py", "orphans", timeout=10).rstrip())


def cmd_health(args):
    """Same as status but with --verbose for failed checks."""
    cmd_status(args)
    _section("Stalled agents (Phase 5)")
    print(_run("transcript_parity.py", "stalled", "--threshold", str(args.threshold), timeout=10).rstrip())


def cmd_dispatch(args):
    sys.stdout.write(_run(
        "fleet_coordinator.py", "dispatch",
        "--agent", args.agent, "--task", args.task,
        timeout=10,
    ))


def cmd_heartbeat(args):
    cmd = ["heartbeat", "--agent", args.agent]
    if args.status:
        cmd += ["--status", args.status]
    if args.task:
        cmd += ["--task", args.task]
    sys.stdout.write(_run("fleet_coordinator.py", *cmd, timeout=10))


def cmd_complete(args):
    cmd = ["complete", "--agent", args.agent, "--dispatch", args.dispatch]
    if args.note:
        cmd += ["--note", args.note]
    sys.stdout.write(_run("fleet_coordinator.py", *cmd, timeout=10))


def cmd_oauth(args):
    """Run the MCP OAuth loopback."""
    cmd = ["--timeout", str(args.timeout), "--provider", args.provider, "--print-code"]
    if args.port:
        cmd += ["--port", str(args.port)]
    if args.state:
        cmd += ["--expected-state", args.state]
    sys.stdout.write(_run("mcp_oauth_loopback.py", *cmd, timeout=args.timeout + 5))


def cmd_tail(args):
    sys.stdout.write(_run(
        "transcript_parity.py", "tail",
        "--agent", args.agent, "--last", str(args.last),
        timeout=10,
    ))


def cmd_route(args):
    """Route a message to mentioned agents (Phase 7)."""
    subargs = [args.text, "--channel", args.channel]
    if args.execute:
        subargs.append("--execute")
    sys.stdout.write(_run("mention_router.py", "route", *subargs, timeout=10))


def cmd_reply(args):
    """Mark a dispatch done (Phase 7)."""
    subargs = ["--agent", args.agent, "--dispatch", args.dispatch]
    if args.result:
        subargs += ["--result", args.result]
    sys.stdout.write(_run("mention_router.py", "reply", *subargs, timeout=10))


def cmd_mention_status(args):
    """Show pending vs done dispatches (Phase 7)."""
    import sqlite3
    DB = Path.home() / ".hermes" / "coordinator.db"
    if not DB.exists():
        print("(no coordinator.db)")
        return
    with sqlite3.connect(DB) as db:
        db.row_factory = sqlite3.Row
        rows = db.execute("""
            SELECT id, agent, task, source, status, created_at_ms, completed_at_ms
            FROM dispatches ORDER BY created_at_ms DESC LIMIT ?
        """, (args.last,)).fetchall()
    if not rows:
        print("(no dispatches)")
        return
    for r in rows:
        from datetime import datetime
        ts = datetime.fromtimestamp(r["created_at_ms"] / 1000).strftime("%H:%M:%S")
        flag = "✓" if r["status"] == "done" else "⋯"
        print(f"  {flag} {ts}  {r['agent']:<10} {r['id']}  {r['task'][:60]}")


def cmd_tui(args):
    """Live terminal dashboard (Phase 8)."""
    # Re-exec into the TUI module so it gets the right imports
    import subprocess
    cmd = ["python3", str(ROOT / "fleet_tui.py")]
    if args.once:
        cmd.append("--once")
    if args.interval != 1.0:
        cmd += ["--interval", str(args.interval)]
    r = subprocess.run(cmd)
    sys.exit(r.returncode)


def cmd_daemon(args):
    import asyncio
    sys.path.insert(0, str(ROOT))
    from policies import polling

    print(f"# fleet harness daemon started (polling every {args.interval}s)")
    print(f"#   phases 1-5 active; one supervisor to rule them all")

    p = polling("harness", interval_ms=args.interval * 1000, leading=True)

    async def tick():
        ts = datetime.now().strftime("%H:%M:%S")
        sup = _run("local_exec_supervisor.py", "status", timeout=10)
        bad_proc = [line for line in sup.splitlines()
                    if "running" not in line and "STATE" not in line
                    and line.strip() and "---" not in line
                    and any(name in line for name, _, _, en in [
                        ("hermes-gateway", 0, 0, 0), ("fleet-coordinator", 0, 0, 0),
                        ("9router", 0, 0, 0)])]
        orphans = _run("transcript_parity.py", "orphans", timeout=10)
        stalled = _run("transcript_parity.py", "stalled", "--threshold", "300", timeout=10)
        alerts = []
        if "✗" in sup:
            alerts.append(f"{sup.count(chr(0x2717))} process issue(s)")
        if "✗" in orphans:
            alerts.append(f"{orphans.count(chr(0x2717))} orphan dispatch(es)")
        if "✗" in stalled:
            alerts.append(f"{stalled.count(chr(0x2717))} stalled agent(s)")
        if alerts:
            print(f"[{ts}] ALERT: " + " | ".join(alerts))
            for line in (sup + orphans + stalled).splitlines():
                if "✗" in line:
                    print(f"    {line.strip()}")
        else:
            print(f"[{ts}] OK: all systems green")

    try:
        asyncio.run(p.start(tick))
    except (KeyboardInterrupt, asyncio.CancelledError):
        print("\ndaemon stopped")


def main():
    ap = argparse.ArgumentParser(
        description="Hermes Fleet Harness — unified entry point for all 6 phases",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
  %(prog)s status                  # full dashboard
  %(prog)s health                  # same + stalled agents
  %(prog)s dispatch -a chronos -t 'restart 9router'
  %(prog)s heartbeat -a hermes -s busy -T 'audit'
  %(prog)s tail -a enigma -n 10
  %(prog)s oauth -p Google -t 300
  %(prog)s daemon --interval 60    # long-lived supervisor
        """,
    )
    sub = ap.add_subparsers(dest="cmd", required=True)

    s = sub.add_parser("status", help="Full fleet dashboard")
    s.set_defaults(func=cmd_status)

    h = sub.add_parser("health", help="Status + stalled agents")
    h.add_argument("--threshold", type=int, default=300, help="stall threshold sec")
    h.set_defaults(func=cmd_health)

    d = sub.add_parser("dispatch", help="Queue a task to an agent")
    d.add_argument("-a", "--agent", required=True, choices=COORDS)
    d.add_argument("-t", "--task", required=True)
    d.set_defaults(func=cmd_dispatch)

    hb = sub.add_parser("heartbeat", help="Record an agent heartbeat")
    hb.add_argument("-a", "--agent", required=True, choices=COORDS)
    hb.add_argument("-s", "--status", choices=["idle", "busy", "stuck", "offline"])
    hb.add_argument("-T", "--task")
    hb.set_defaults(func=cmd_heartbeat)

    c = sub.add_parser("complete", help="Mark a dispatch done")
    c.add_argument("-a", "--agent", required=True, choices=COORDS)
    c.add_argument("-d", "--dispatch", required=True)
    c.add_argument("-n", "--note")
    c.set_defaults(func=cmd_complete)

    o = sub.add_parser("oauth", help="Run MCP OAuth loopback")
    o.add_argument("-p", "--provider", default="OAuth")
    o.add_argument("-t", "--timeout", type=int, default=300)
    o.add_argument("--port", type=int)
    o.add_argument("--state", help="expected state token")
    o.set_defaults(func=cmd_oauth)

    tl = sub.add_parser("tail", help="Show merged transcript")
    tl.add_argument("-a", "--agent", required=True, choices=COORDS)
    tl.add_argument("-n", "--last", type=int, default=20)
    tl.set_defaults(func=cmd_tail)

    rt = sub.add_parser("route", help="Parse message + dispatch to mentioned agents (Phase 7)")
    rt.add_argument("text", help="user message to parse")
    rt.add_argument("--channel", default="claw-chat")
    rt.add_argument("--execute", action="store_true", help="actually create dispatches")
    rt.set_defaults(func=cmd_route)

    rp = sub.add_parser("reply", help="Mark a dispatch done (Phase 7)")
    rp.add_argument("-a", "--agent", required=True, choices=COORDS)
    rp.add_argument("-d", "--dispatch", required=True)
    rp.add_argument("-r", "--result", default="")
    rp.set_defaults(func=cmd_reply)

    ms = sub.add_parser("dispatches", help="Show recent dispatches (Phase 7)")
    ms.add_argument("-n", "--last", type=int, default=10)
    ms.set_defaults(func=cmd_mention_status)

    tu = sub.add_parser("tui", help="Live terminal dashboard (Phase 8)")
    tu.add_argument("--interval", type=float, default=1.0)
    tu.add_argument("--once", action="store_true", help="render once + exit")
    tu.set_defaults(func=cmd_tui)

    dm = sub.add_parser("daemon", help="Long-lived fleet supervisor")
    dm.add_argument("--interval", type=int, default=60)
    dm.set_defaults(func=cmd_daemon)

    args = ap.parse_args()
    args.func(args)


if __name__ == "__main__":
    main()
