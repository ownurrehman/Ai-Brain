# HEARTBEAT.md

Quiet hours: 1:00–05:00 Asia/Karachi — report urgent failures only.

## On each heartbeat (every 15 min):

1) **Daily memory file check:**
   - Check if `memory/$(date +%Y-%m-%d).md` exists
   - If missing: create with header `# $(date +%Y-%m-%d) Activity Log`

2) **Cron sanity:**
   - List running jobs with elapsed time
   - Flag >30m as "long-running", >60m as "stuck"

3) **Channel health:**
   - WhatsApp + Discord connected?
   - If not: minimal fix steps only

4) **Token economy:**
   - Context huge? Recommend compaction or new session
   - Report token usage vs limit

## Output format:
- All fine: **HEARTBEAT_OK**
- Jobs running: "2 jobs: [name] [time] | [name] [time]"
- Issues: 3 bullets max + next action

## Activity logging:
- Log key events to today's memory file
- Keep entries concise: `[HH:MM] Event: detail`
- Also post task progress to Discord bot-logs channel (1476131657663909970)
- Format: `[STARTED/COMPLETED/BLOCKED/FAILED] Task - detail`
- Use curl to Discord API: `curl -X POST https://discord.com/api/v10/channels/1476131657663909970/messages -H "Authorization: Bot $DISCORD_BOT_TOKEN" -H "Content-Type: application/json" -d '{"content":"..."}'`