#!/bin/bash
# OpenClaw Health Check - every 4 hours
# Reports to WhatsApp if any issues detected

REPORT=""

# 1. Check if gateway is running
GATEWAY_STATUS=$(openclaw gateway status 2>&1)
if echo "$GATEWAY_STATUS" | grep -qi "not running\|error\|stopped"; then
    REPORT="${REPORT}Gateway: DOWN
"
fi

# 2. Check if cron service is responding
CRON_LIST=$(openclaw cron list 2>&1)
if echo "$CRON_LIST" | grep -qi "error\|unreachable\|failed"; then
    REPORT="${REPORT}Cron service: ERROR
"
fi

# 3. Check disk space
DISK_USAGE=$(df -h /Users | awk 'NR==2 {print $5}' | sed 's/%//')
if [ "$DISK_USAGE" -gt 90 ]; then
    REPORT="${REPORT}Disk usage: ${DISK_USAGE}%
"
fi

# 4. Check subagents for stuck jobs
STUCK_SUBAGENTS=$(openclaw subagents list 2>&1 | grep -c "running" || echo "0")

# Build final message
if [ -n "$REPORT" ] || [ "$STUCK_SUBAGENTS" -gt 5 ]; then
    MESSAGE="⚠️ OpenClaw Alert

Health Check: ISSUES DETECTED
Time: $(date '+%Y-%m-%d %H:%M %Z')

${REPORT}"
    if [ "$STUCK_SUBAGENTS" -gt 5 ]; then
        MESSAGE="${MESSAGE}Stuck subagents: ${STUCK_SUBAGENTS}
"
    fi
    MESSAGE="${MESSAGE}
Run: openclaw status"
    echo "$MESSAGE" | openclaw whatsapp send --to "me" --message - 2>&1 || true
else
    # Optional: daily all-clear summary at 8 AM
    HOUR=$(date +%H)
    if [ "$HOUR" == "08" ]; then
        MESSAGE="✅ OpenClaw Health Check

All systems operational
Time: $(date '+%Y-%m-%d %H:%M %Z')

Gateway: UP
Cron: OK
Disk: ${DISK_USAGE}%
Subagents: ${STUCK_SUBAGENTS} running

Next check in 4 hours."
        echo "$MESSAGE" | openclaw whatsapp send --to "me" --message - 2>&1 || true
    fi
fi

echo "Health check completed at $(date)"
