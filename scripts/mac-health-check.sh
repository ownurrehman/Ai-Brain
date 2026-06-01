#!/bin/bash
# mac-health-check.sh — System health check for OpenClaw

OUTPUT=""
OUTPUT+="🩺 **Mac Health Check** — $(date '+%Y-%m-%d %H:%M %Z')\n"
OUTPUT+="━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n"

# Memory
OUTPUT+="📊 **Memory**\n"
OUTPUT+="\`\`\`\n"
OUTPUT+=$(vm_stat 2>/dev/null | awk 'BEGIN{PAGESIZE=16384} /free:/{FREE=$3} /inactive:/{INACTIVE=$3} /active:/{ACTIVE=$3} /wired:/{WIRED=$4} END{gsub(/\\./,"",FREE);gsub(/\\./,"",INACTIVE);gsub(/\\./,"",ACTIVE);gsub(/\\./,"",WIRED); TOTAL=(FREE+INACTIVE+ACTIVE+WIRED)*PAGESIZE/1024/1024/1024; printf "Free: %.1f GB\nInactive: %.1f GB\nActive: %.1f GB\nWired: %.1f GB\nTotal Used: %.1f GB\n", FREE*PAGESIZE/1024/1024/1024, INACTIVE*PAGESIZE/1024/1024/1024, ACTIVE*PAGESIZE/1024/1024/1024, WIRED*PAGESIZE/1024/1024/1024, TOTAL}')
OUTPUT+="\`\`\`\n\n"

# Disk
OUTPUT+="💾 **Disk Usage**\n"
OUTPUT+="\`\`\`\n"
OUTPUT+=$(df -h / | tail -1 | awk '{print "Total: "$2"\nUsed:  "$3"\nAvail: "$4"\nUsage: "$5}')
OUTPUT+="\`\`\`\n\n"

# Load
OUTPUT+="⚡ **Load Average**\n"
OUTPUT+="\`\`\`\n"
OUTPUT+=$(uptime | awk -F'load averages:' '{print $2}' | sed 's/^ //')
OUTPUT+="\n\`\`\`\n\n"

# Top CPU consumers
OUTPUT+="🔥 **Top CPU Hogs**\n"
OUTPUT+="\`\`\`\n"
OUTPUT+=$(ps aux | awk 'NR==1 || ($3 > 10) {printf "%-10s %5s %5s %5s %s\n", $1, $2, $3, $4, $11}' | head -8)
OUTPUT+="\n\`\`\`\n\n"

# Zombie count
ZOMBIE_COUNT=$(ps aux | awk '$8 ~ /^Z/ {count++} END {print count+0}')
OUTPUT+="🧟 **Zombie Processes**\n"
if [ "$ZOMBIE_COUNT" -gt 0 ]; then
  OUTPUT+="⚠️  Found: $ZOMBIE_COUNT zombie(s)\n"
  OUTPUT+=$(ps aux | awk '$8 ~ /^Z/ {printf "PID: %s, PPID: %s, CMD: %s\n", $2, $3, $11}')
else
  OUTPUT+="✅ None found\n"
fi
OUTPUT+="\n"

# MCP processes
OUTPUT+="🔌 **MCP Processes**\n"
MCP_PIDS=$(pgrep -f "mcp" 2>/dev/null)
if [ -n "$MCP_PIDS" ]; then
  OUTPUT+=$(echo "$MCP_PIDS" | while read pid; do ps -p "$pid" -o pid,pcpu,pmem,comm,etime 2>/dev/null | tail -1 | awk '{printf "PID %s | CPU %s%% | MEM %s%% | %s | Uptime %s\n", $1, $2, $3, $4, $5}'; done)
else
  OUTPUT+="None running\n"
fi
OUTPUT+="\n"

# Session dir size
SESSION_SIZE=$(du -sh "$HOME/Ai Works - Local/Ai Codes/Ai Brain/openclaw" 2>/dev/null | cut -f1)
OUTPUT+="📁 **Session Dir Size**: $SESSION_SIZE\n\n"

OUTPUT+="━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"
OUTPUT+="Check complete."

echo "$OUTPUT"
