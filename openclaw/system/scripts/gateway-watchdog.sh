#!/bin/bash
#
# Gateway Auto-Restart Watchdog
# Checks if OpenClaw gateway is responding every 60 seconds
# If unresponsive for 15 minutes, restarts it
#
set -euo pipefail

MAX_FAILS=15          # 15 * 60s = 15 minutes
CHECK_INTERVAL=60     # seconds
RESTART_DELAY=60      # seconds after restart
FAIL_COUNT=0
LOG="/Users/sheikhown/.openclaw/workspace/logs/gateway-watchdog.log"
PID_FILE="/tmp/openclaw-gateway-watchdog.pid"

mkdir -p "$(dirname "$LOG")"
log() { printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$1" | tee -a "$LOG"; }

# Prevent duplicate instances
if [ -f "$PID_FILE" ] && kill -0 "$(cat "$PID_FILE")" 2>/dev/null; then
    log "Watchdog already running (PID $(cat "$PID_FILE")). Exiting."
    exit 0
fi
echo $$ > "$PID_FILE"
log "Gateway watchdog started (PID $$)"

# Cleanup on exit
trap 'rm -f "$PID_FILE"; log "Watchdog stopped"; exit 0' INT TERM EXIT

while true; do
    if openclaw gateway status >/dev/null 2>&1; then
        if [ "$FAIL_COUNT" -gt 0 ]; then
            log "Gateway recovered after $FAIL_COUNT failed check(s)"
            FAIL_COUNT=0
        fi
    else
        FAIL_COUNT=$((FAIL_COUNT + 1))
        log "Gateway check failed ($FAIL_COUNT/$MAX_FAILS)"
        
        if [ "$FAIL_COUNT" -ge "$MAX_FAILS" ]; then
            log "Gateway down for 15 minutes — restarting..."
            if openclaw gateway restart >> "$LOG" 2>&1; then
                log "Gateway restarted successfully"
                FAIL_COUNT=0
            else
                log "Gateway restart FAILED"
            fi
            sleep "$RESTART_DELAY"
        fi
    fi
    
    sleep "$CHECK_INTERVAL"
done