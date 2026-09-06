# Hermes Fleet Harness — 8 Phases Complete (2026-09-05/06)

Inspired by Cursor's "Sand" agent harness. We ported the architectural
wins into Python + launchd + SQLite, integrated with the Hermes fleet.

## What got built

| Phase | File | Purpose |
|---|---|---|
| 1 | `system/policies.py` + `test_policies.py` | Shared policy primitives: deadline, retry, polling, idle_watchdog, expiry. All later phases use this. |
| 2 | `system/fleet_coordinator.py` | Agent roster (7 agents), heartbeats, dispatch/queue, status tracking. Daemon under launchd (`ai.hermes.fleet-coordinator`). |
| 3 | `system/mcp_oauth_loopback.py` | OAuth callback listener (replaces manual HTML redirect + WhatsApp). Friendly HTML pages, state validation, one-shot server. |
| 4 | `system/local_exec_supervisor.py` | launchd plist health check + auto-heal. Watches 4 fleet processes, kicks failing ones, audit log. Daemon under launchd (`ai.hermes.local-exec-supervisor`). |
| 5 | `system/transcript_parity.py` | dispatch→reply parity check, orphan detection, stalled-agent detection. |
| 6 | `system/fleet_harness.py` | Unified entry point — one command, all 5 phases. Daemon mode for continuous monitoring. |
| 7 | `system/mention_router.py` | Inter-agent @-mention routing (the "agents talk to each other" feature). Parses user messages, dispatches to mentioned agents, watches for completions. Daemon under launchd (`ai.hermes.mention-router`). |
| 8 | `system/fleet_tui.py` | Live curses-based TUI showing agents + processes + dispatches + alerts. `--once` for scripts, default for live monitoring. |

## Quick start

```bash
# One command, full dashboard:
python3 /Users/sheikhown/Ai\ Works\ -\ Local/Ai\ Codes/Ai\ Brain/system/fleet_harness.py status

# Dispatch a task to an agent:
python3 fleet_harness.py dispatch -a chronos -t "restart 9router"

# Record a heartbeat:
python3 fleet_harness.py heartbeat -a enigma -s busy -T "writing 3 blogs"

# Mark a dispatch complete:
python3 fleet_harness.py complete -a enigma -d <dispatch-id> -n "done"

# Run OAuth loopback:
python3 fleet_harness.py oauth -p Google -t 300 --print-code

# Long-lived monitor (replaces cron):
python3 fleet_harness.py daemon --interval 60
```

## What's running

| Process | launchd label | PID | State |
|---|---|---|---|
| Hermes gateway | `ai.hermes.gateway` | 97035 | running |
| Fleet coordinator | `ai.hermes.fleet-coordinator` | 54902 | running |
| 9Router | `ai.9router` | 22299 | running |
| Local-exec supervisor | `ai.hermes.local-exec-supervisor` | 93493 | running |
| Alpha (isolated) | `ai.hermes.gateway-alpha` | - | intentionally-disabled |

## Key design decisions

1. **SQLite at `~/.hermes/coordinator.db`** — single source of truth, shared
   between phases 2, 4, 5. Survives restart. No external DB needed.

2. **launchd over systemd/Docker** — matches your existing infrastructure
   pattern. Each daemon is one plist, easy to inspect/restart/log.

3. **Python over Node** — Hermes already runs on Python; no new runtime
   to install. policies.py + everything else is stdlib + sqlite3.

4. **Standalone scripts + unified harness** — power users can run any
   phase directly (`python3 fleet_coordinator.py dispatch ...`). The
   harness just composes them. No lock-in.

5. **Self-aware supervisor** — `local-exec-supervisor` watches itself.
   If it crashes, launchd `KeepAlive` restarts it; if it stays alive
   but loses other processes, it heals those.

6. **`enabled=False` for intentionally-disabled agents** — alpha is
   sandbox-only, no auto-heal. Prevents the "frantic restart loop" the
   old gateway watchdog caused.

## What this replaces

| Old pattern | New way |
|---|---|
| `python3 -m http.server 8080` + HTML redirect for OAuth | `python3 mcp_oauth_loopback.py --port 8765` |
| Manual `pkill -f gateway && launchctl load ...` | `python3 local_exec_supervisor.py watch` (auto) |
| WhatsApp "send me the URL" then user copy-pastes the code | Loopback + on-screen success page + `print-code` |
| Cron-driven fleet-status-report | `python3 fleet_harness.py daemon --interval 60` |
| ad-hoc `for i in range(3): try: requests.get(...)` | `from policies import retry, deadline; with retry(...):` |

## What this does NOT do (yet)

1. **No real transcript tail from Discord** — we only read our own DB.
   Adding the Discord API pull would let us verify the user actually
   saw our messages, not just that we sent them.

2. **No gateway parity check** — Sand compares server tail (their
   backend) vs gateway tail (local). We don't have a separate "server"
   to compare against; we just verify local DB consistency.

3. **No WebAuthn subsystem** — we use service accounts for everything
   that needs Google auth. WebAuthn would matter if you wanted to use
   personal Google logins.

4. **No multi-region failover** — single Mac. The architecture
   supports it (roster entries have a `host` field if we add it), but
   no current need.

5. **No active MCP servers registered** — we use 9Router (Phase 1
   of 9Router work) as a model proxy, not an MCP server. The loopback
   in Phase 3 is OAuth infrastructure, not an MCP server itself.

## Next steps if you want more

1. **Wire Hermes agent to auto-heartbeat** — add 1 line to the session
   start hook: `python3 fleet_harness.py heartbeat -a hermes -s idle`
2. **Replace the Fleet Status Report cron with `fleet_harness.py daemon`**
3. **Add Discord transcript tail** — pull last N messages from each
   `#claw-*` channel, store in coordinator DB, extend parity check
4. **Build a simple TUI** for live monitoring — `curses`-based, reads
   coordinator.db + local-exec-supervisor status in real time

## Lessons applied from Sand (and our own)

- **Sand uses a single shared policy library** — we copied that
  verbatim in Phase 1. Reusing retry/timeout primitives across all
  scripts eliminates the "every script reinvents backoff" problem.

- **Sand's coordinator is long-lived, not per-request** — we run
  fleet-coordinator as a daemon. It keeps state in SQLite so the next
  Hermes session sees the same fleet status as the last.

- **Sand has `enabled` flags for sandbox processes** — we copied that
  for alpha. Without it, supervisors will fight the user's intent.

- **Sand's auto-heal triggers after N consecutive failures, not 1** —
  we use 2. Avoids the "1 transient blip → restart loop" pattern.

- **Our own lesson: the Hermes gateway watchdog killed the gateway
  every 10 minutes for months** — Sand-style supervisor with
  intelligent auto-heal (only after 2 failures, only on enabled
  processes) prevents that pattern.

## Evidence gate (added 2026-09-06)
See [[system/harness-evidence-gate|harness-evidence-gate.md]]. Harness must not mark audits complete without script artifacts + sourced metrics.
