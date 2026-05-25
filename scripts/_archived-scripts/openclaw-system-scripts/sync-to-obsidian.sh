#!/bin/bash
#
# OpenClaw → Obsidian Vault Auto-Sync
# Runs every 4 hours via cron
#
set -euo pipefail

WORKSPACE="/Users/sheikhown/.openclaw/workspace"
BRAIN="/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain"
TODAY=$(date +%Y-%m-%d)
TIMESTAMP=$(date '+%Y-%m-%d %H:%M')
LOG="$WORKSPACE/logs/obsidian-sync.log"

mkdir -p "$(dirname "$LOG")"
log() { printf '%s %s\n' "$TIMESTAMP" "$1" | tee -a "$LOG"; }

# Ensure vault is set
obsidian-cli print-default --path-only >/dev/null 2>&1 || obsidian-cli set-default "Ai Brain"

# Create directories
mkdir -p "$BRAIN/memory/openclaw"
mkdir -p "$BRAIN/projects/openclaw-ops"
mkdir -p "$BRAIN/projects/openclaw-ops/leads"

# Sync today's memory file
SYNCED=0
if [ -f "$WORKSPACE/memory/$TODAY.md" ]; then
    cp "$WORKSPACE/memory/$TODAY.md" "$BRAIN/memory/openclaw/$TODAY.md"
    log "  ✓ Synced $TODAY.md"
    SYNCED=1
fi

# Sync lead files
if [ -d "$WORKSPACE/leads" ]; then
    cp -r "$WORKSPACE/leads/"* "$BRAIN/projects/openclaw-ops/leads/"
    log "  ✓ Synced leads directory"
fi
# Sync root lead JSONs
for f in "$WORKSPACE"/leads-*.json; do
    [ -e "$f" ] && cp "$f" "$BRAIN/projects/openclaw-ops/leads/" && log "  ✓ Synced $(basename "$f")"
done

# Sync core files
for file in MEMORY.md HEARTBEAT.md IDENTITY.md USER.md ENIGMA-MASTER-AGENT.md; do
    [ -f "$WORKSPACE/$file" ] && cp "$WORKSPACE/$file" "$BRAIN/projects/openclaw-ops/$file"
done

# Log result
if [ "$SYNCED" -eq 1 ]; then
    log "  ✅ Sync completed"
else
    log "  ⚠️ No new memory file to sync"
fi
