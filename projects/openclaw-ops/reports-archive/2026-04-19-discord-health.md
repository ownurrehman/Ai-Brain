# Discord Health Check Report
**Date:** 2026-04-19T01:47+05:00

## Config Status
- **Token:** Present in env (DISCORD_BOT_TOKEN, starts with MTQ3NTgwOD)
- **Token source:** env file (~/.openclaw/.env)
- **Discord enabled:** true
- **Guild configured:** Yes (973109476129402900, alias: Rank Ray)
- **Guild user allowlist:** 402262209423605760
- **Channel mapping:** Resolved at startup (bot-logs channel: 1476131657663909970)
- **Binding:** main agent bound to discord channel

## Send Test Result
- **Target channel:** 1476131657663909970 (bot-logs)
- **HTTP status:** 200 (message delivered successfully)
- **Test message:** [HEALTH-CHECK] Discord connectivity test - 2026-04-19T20:47Z

## Gateway Status
- **Process:** Running (pid 89474, state active)
- **RPC probe:** OK
- **Listening:** 127.0.0.1:18789
- **Service:** LaunchAgent loaded

## Log Findings
- Gateway restarted successfully at 20:39:27 UTC
- Discord provider started as @Rank Ray Bot (client ID: 1475808868725428277)
- Guild resolved: 973109476129402900 (Rank Ray)
- Message Content Intent: limited (normal for bots under 100 servers)
- One prior error: GatewayDrainingError on channel 1476025453599789191 during a restart (expected, not a persistent issue)
- No ongoing errors detected

## Verdict
**Discord is FUNCTIONAL.** Bot token valid, gateway running, message delivery confirmed (HTTP 200), no active errors.

## Notes
- The GatewayDrainingError in logs was from a restart cycle, not a persistent failure
- Message Content Intent is limited but operational for bots under 100 servers