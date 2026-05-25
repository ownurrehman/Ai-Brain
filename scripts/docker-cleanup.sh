#!/bin/zsh
# Docker Weekly Cleanup — Rank Ray
# Starts Docker if off, cleans everything unused 7+ days, then stops Docker
# Runs via cron weekly

LOGFILE="/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/logs/docker-cleanup.log"
DATE=$(date '+%Y-%m-%d %H:%M:%S')
DOCKER_STARTED_BY_SCRIPT=false

# Create log dir if missing
mkdir -p "$(dirname "$LOGFILE")"

echo "[$DATE] === Docker Weekly Cleanup Started ===" >> "$LOGFILE"

# Check if Docker daemon is reachable
if ! docker system info > /dev/null 2>&1; then
    echo "[$DATE] Docker not running, starting Docker Desktop..." >> "$LOGFILE"
    open -a "Docker Desktop"
    DOCKER_STARTED_BY_SCRIPT=true
    
    # Wait for Docker to be ready (max 5 minutes / 300 seconds)
    for i in $(seq 1 300); do
        if docker system info > /dev/null 2>&1; then
            echo "[$DATE] Docker ready after ${i}s" >> "$LOGFILE"
            break
        fi
        sleep 1
    done
    
    # If still not ready, abort
    if ! docker system info > /dev/null 2>&1; then
        echo "[$DATE] Docker failed to start after 5 minutes, aborting" >> "$LOGFILE"
        exit 1
    fi
fi

# Show before stats
echo "[$DATE] Before cleanup:" >> "$LOGFILE"
docker system df >> "$LOGFILE" 2>&1

# 1. Prune containers stopped for > 7 days
echo "[$DATE] Pruning containers unused > 7 days..." >> "$LOGFILE"
docker container prune -f --filter "until=168h" >> "$LOGFILE" 2>&1

# 2. Prune images unused for > 7 days (including dangling)
echo "[$DATE] Pruning images unused > 7 days..." >> "$LOGFILE"
docker image prune -a -f --filter "until=168h" >> "$LOGFILE" 2>&1

# 3. Prune volumes unused for > 7 days
echo "[$DATE] Pruning volumes unused > 7 days..." >> "$LOGFILE"
docker volume prune -f --filter "until=168h" >> "$LOGFILE" 2>&1

# 4. Prune networks unused
echo "[$DATE] Pruning networks..." >> "$LOGFILE"
docker network prune -f >> "$LOGFILE" 2>&1

# 5. Prune build cache
echo "[$DATE] Pruning build cache..." >> "$LOGFILE"
docker builder prune -f --filter "until=168h" >> "$LOGFILE" 2>&1

# Show after stats
echo "[$DATE] After cleanup:" >> "$LOGFILE"
docker system df >> "$LOGFILE" 2>&1

# Stop Docker if we started it
if [ "$DOCKER_STARTED_BY_SCRIPT" = true ]; then
    echo "[$DATE] Stopping Docker Desktop..." >> "$LOGFILE"
    osascript -e 'quit app "Docker Desktop"' >> "$LOGFILE" 2>&1
    echo "[$DATE] Docker Desktop stopped" >> "$LOGFILE"
fi

echo "[$DATE] === Docker Weekly Cleanup Complete ===" >> "$LOGFILE"
echo "" >> "$LOGFILE"
