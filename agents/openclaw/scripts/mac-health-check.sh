#!/bin/bash
# Mac Health & Maintenance - Kills memory hogs, zombies, runaway processes
# Runs every 3 hours via cron

REPORT=""
KILLED=0
WARNED=0

# Timestamp
TS=$(date '+%Y-%m-%d %H:%M:%S')

# Add timeout wrappers for slow commands
PS_CMD="ps aux"
FIND_CMD="find /tmp -type f -mtime +7"

# 1. Check for zombie processes (defunct/Z state) — 5s timeout
ZOMBIES=$(timeout 5 sh -c "$PS_CMD" | awk '$8 ~ /Z/ {print $2, $4, $11}' | head -10)
if [ -n "$ZOMBIES" ]; then
  REPORT+="🧟 Zombies found:\n$ZOMBIES\n\n"
  while read pid rest; do
    if [ -n "$pid" ]; then
      kill -9 "$pid" 2>/dev/null && ((KILLED++))
    fi
  done <<< "$ZOMBIES"
else
  REPORT+="✅ No zombie processes\n\n"
fi

# 2. Find runaway Node.js scripts (not openclaw gateway, high CPU for >30min) — 5s timeout
RUNAWAY=$(timeout 5 sh -c "$PS_CMD" | awk '$11 ~ /node/ && $3 > 15.0 {print $2, $3, $4, $9, $11}' | grep -v "openclaw-gateway" | head -10)
if [ -n "$RUNAWAY" ]; then
  REPORT+="🔥 Runaway Node scripts (>15% CPU):\n$RUNAWAY\n\n"
  while read pid cpu mem time cmd; do
    if [ -n "$pid" ]; then
      kill -9 "$pid" 2>/dev/null && ((KILLED++))
    fi
  done <<< "$RUNAWAY"
fi

# 3. Find memory hogs (>2GB RSS, not critical system processes) — 5s timeout
MEMORY_HOGS=$(timeout 5 sh -c "$PS_CMD" | awk '$6 > 2000000 && $11 !~ /kernel_task|WindowServer|mds|logd|bluetoothd|coreaudiod|loginwindow|Dock|Finder/ {print $2, $4, $6, $11}' | head -10)
if [ -n "$MEMORY_HOGS" ]; then
  REPORT+="💾 Memory hogs (>2GB):\n$MEMORY_HOGS\n\n"
  while read pid pct_mem rss cmd; do
    if [ -n "$pid" ]; then
      # Don't kill critical apps - just report
      if echo "$cmd" | grep -qiE "chrome|discord|obsidian|whatsapp|antigravity|vscode"; then
        ((WARNED++))
      else
        kill -9 "$pid" 2>/dev/null && ((KILLED++))
      fi
    fi
  done <<< "$MEMORY_HOGS"
fi

# 4. Clean old browser tabs/profiles that may be leaking (Chrome helpers >500MB) — 5s timeout
CHROME_HOGS=$(timeout 5 sh -c "$PS_CMD" | awk '$11 ~ /Chrome Helper/ && $6 > 500000 {print $2, $4, $6}' | head -5)
if [ -n "$CHROME_HOGS" ]; then
  REPORT+="🌐 Chrome helpers (>500MB): $(echo "$CHROME_HOGS" | wc -l) tabs\n"
  # Don't auto-kill Chrome tabs - just report
  ((WARNED+=$(echo "$CHROME_HOGS" | wc -l)))
fi

# 5. Clean /tmp of old files (>7 days) — 10s timeout
TMP_CLEANED=$(timeout 10 sh -c "$FIND_CMD 2>/dev/null" | wc -l)
timeout 15 sh -c "$FIND_CMD -delete 2>/dev/null" || true
if [ "$TMP_CLEANED" -gt 0 ]; then
  REPORT+="🗑 Cleaned $TMP_CLEANED old /tmp files\n\n"
fi

# 6. Check disk space
DISK_USAGE=$(df -h / | tail -1 | awk '{print $5}' | tr -d '%')
if [ "$DISK_USAGE" -gt 85 ]; then
  REPORT+="⚠️ Disk usage: ${DISK_USAGE}%\n"
else
  REPORT+="✅ Disk usage: ${DISK_USAGE}%\n"
fi

# 7. Check load average
LOAD=$(uptime | awk -F'load averages:' '{print $2}' | awk '{print $1}' | tr -d ',')
REPORT+="📊 Load avg: $LOAD\n\n"

# 8. Memory summary
MEM_USED=$(vm_stat | awk '/Pages active/ {active=$3} /Pages wired/ {wired=$4} /Pages inactive/ {inactive=$3} END {gsub(/\./,"",active); gsub(/\./,"",wired); gsub(/\./,"",inactive); print int((active+wired+inactive)*4096/1024/1024/1024)}')
REPORT+="🧠 Memory used: ${MEM_USED}GB\n\n"

# Summary
REPORT+="📋 Summary: $KILLED killed, $WARNED warned\n"

# Output report
echo -e "$REPORT"

# Send to Discord #claw-status if webhook available
if [ -n "$DISCORD_WEBHOOK" ]; then
  curl -s -X POST "$DISCORD_WEBHOOK" \
    -H "Content-Type: application/json" \
    -d "{\"content\":\"🦞 **Mac Health Check** ($TS)\n\n$(echo -e "$REPORT" | sed 's/"/\\"/g')\"}" > /dev/null
fi

exit 0
