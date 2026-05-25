#!/bin/bash
# OpenClaw Gateway Watchdog
# Checks if gateway is running. If down for 15+ minutes, restarts it.
# Run via launchd every 15 minutes.

LOG="/Users/sheikhown/.openclaw/logs/watchdog.log"
GATEWAY_CMD="openclaw-gateway"
MAX_DOWN_FILE="/tmp/openclaw-gateway-down-since"
ALERT_MINS=15

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" >> "$LOG"
}

# Check if gateway process is running
if pgrep -f "$GATEWAY_CMD" > /dev/null 2>&1; then
    # Gateway is running - clear down timer if it was set
    if [ -f "$MAX_DOWN_FILE" ]; then
        log "Gateway recovered. Removing down-since marker."
        rm -f "$MAX_DOWN_FILE"
    fi
    log "Gateway is running. No action needed."
    exit 0
fi

# Gateway is NOT running
if [ ! -f "$MAX_DOWN_FILE" ]; then
    # First time detecting it's down
    date +%s > "$MAX_DOWN_FILE"
    log "Gateway is DOWN. Starting 15-minute timer."
    exit 0
fi

# Check how long it's been down
DOWN_SINCE=$(cat "$MAX_DOWN_FILE")
NOW=$(date +%s)
DOWN_SECS=$((NOW - DOWN_SINCE))
DOWN_MINS=$((DOWN_SECS / 60))

if [ "$DOWN_MINS" -ge "$ALERT_MINS" ]; then
    log "Gateway has been down for ${DOWN_MINS} minutes. Attempting restart."
    
    # Kill any leftover processes
    pkill -f "$GATEWAY_CMD" 2>/dev/null
    sleep 2
    
    # Restart gateway
    cd /Users/sheikhown/.openclaw
    nohup openclaw gateway start >> /Users/sheikhown/.openclaw/logs/gateway-restart.log 2>&1 &
    
    sleep 5
    
    # Verify it came back
    if pgrep -f "$GATEWAY_CMD" > /dev/null 2>&1; then
        log "Gateway restarted successfully."
        rm -f "$MAX_DOWN_FILE"
    else
        log "Gateway restart FAILED. Will retry next cycle."
    fi
else
    log "Gateway is down for ${DOWN_MINS} minutes. Waiting for ${ALERT_MINS}-minute threshold."
fi