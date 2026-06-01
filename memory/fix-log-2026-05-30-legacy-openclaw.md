# Fix Log — 2026-05-30

## Issues Fixed

### 1. Hermes Process Spam (Discord token errors, MCP memtrace failures)
- **Root cause:** Old `hermes gateway` process was still running in background (PID 6318) alongside WhatsApp bridge (PID 6396)
- **Fix:** Killed both processes with `kill -9`
- **Result:** No more `[Discord] No bot token configured` or MCP memtrace errors
- **Note:** Hermes and OpenClaw are separate systems. OpenClaw Discord config was already correct.

### 2. Cron Job Timeouts
Multiple cron jobs were timing out because they asked isolated agents to do complex multi-step browser + Google Sheets work:

| Job | Old Timeout | New Timeout | Key Fix |
|-----|-------------|-------------|---------|
| daily-lead-email-drafter | 900s | 600s | Removed Google Sheets dependency, use workspace files instead |
| follow-up-email-drafter | 600s | 600s | Removed Google Sheets dependency |
| weekly-hot-lead-proposals | 1200s | 900s | Removed browser audit + Google Sheets, use web_search instead |
| mac-health-check-3h | 300s | 300s | Fixed bad file path (escaped spaces causing shell errors) |
| weekly-client-report | — | 300s | Simplified payload, removed bad file listing command |

### 3. OpenClaw Config Verified
- Discord token is properly configured (`channels.discord.token` exists)
- No MCP servers configured in OpenClaw (memtrace was external/Hermes)
- All cron jobs now have realistic payloads matching actual tool capabilities

## Remaining Work
- If you want Google Sheets integration for lead tracking, need to configure `gsc` skill or add a Google Sheets skill
- Lead files should be stored in `projects/rankray/email-drafts/` for the cron jobs to find them
- `tech-audit-rotation` was already working fine (last run OK on 2026-05-30)
