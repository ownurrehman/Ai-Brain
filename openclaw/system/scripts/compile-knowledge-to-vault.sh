#!/bin/bash
set -euo pipefail

WORKSPACE="/Users/sheikhown/.openclaw/workspace"
BRAIN="/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain"
TIMESTAMP=$(date '+%Y-%m-%d %H:%M')
TODAY=$(date +%Y-%m-%d)
LOG="$WORKSPACE/logs/knowledge-compiler.log"

mkdir -p "$(dirname "$LOG")"
log() { printf '%s %s\n' "$TIMESTAMP" "$1" | tee -a "$LOG"; }

log "=== Knowledge Compiler Started ==="

# Ensure vault is set
obsidian-cli print-default --path-only > /dev/null 2>&1 || obsidian-cli set-default "Ai Brain"

# Create knowledge directories
mkdir -p "$BRAIN/projects/openclaw-ops"/{lessons-learned,seo-strategies,technical-fixes,implementations,reports-archive,audits}

# 1. EXTRACT LESSONS LEARNED FROM MEMORY
if [ -f "$WORKSPACE/memory/$TODAY.md" ]; then
    grep -A20 -iE "(lesson|root cause|what went wrong|fixed|solution)" "$WORKSPACE/memory/$TODAY.md" | head -100 > "$BRAIN/projects/openclaw-ops/lessons-learned/$TODAY-lessons.md" || true
    [ -s "$BRAIN/projects/openclaw-ops/lessons-learned/$TODAY-lessons.md" ] && log "  Lessons extracted" || log "  No lessons found"
fi

# 2. COMPILE SEO STRATEGIES & RESEARCH
SEO_COUNT=0
for file in $(ls -t "$WORKSPACE/reports"/*-seo-gaps.md 2>/dev/null | head -5); do
    cp "$file" "$BRAIN/projects/openclaw-ops/seo-strategies/$(basename "$file")"
    SEO_COUNT=$((SEO_COUNT + 1))
    log "  SEO: $(basename "$file")"
done
[ "$SEO_COUNT" -eq 0 ] && log "  No SEO reports found"

# 3. DOCUMENT TECHNICAL FIXES
if [ -f "$WORKSPACE/memory/$TODAY.md" ]; then
    grep -A30 -iE "(wordpress|rest api|authentication|credentials|blocked|fixed)" "$WORKSPACE/memory/$TODAY.md" | head -150 > "$BRAIN/projects/openclaw-ops/technical-fixes/$TODAY-wordpress-api-fixes.md" || true
    [ -s "$BRAIN/projects/openclaw-ops/technical-fixes/$TODAY-wordpress-api-fixes.md" ] && log "  Tech fixes documented" || log "  No tech fixes found"
fi

# 4. SYNC CORE FILES
for file in MEMORY.md HEARTBEAT.md IDENTITY.md USER.md ENIGMA-MASTER-AGENT.md; do
    if [ -f "$WORKSPACE/$file" ]; then
        cp "$WORKSPACE/$file" "$BRAIN/projects/openclaw-ops/$file"
        log "  Sync: $file"
    else
        log "  Skip: $file (not found)"
    fi
done

# 5. CREATE KNOWLEDGE INDEX
{
    echo "# OpenClaw Knowledge Base"
    echo ""
    echo "**Last Updated:** $TIMESTAMP"
    echo ""
    echo "| Category | Path |"
    echo "|----------|------|"
    echo "| Lessons Learned | \`lessons-learned/\` |"
    echo "| SEO Strategies | \`seo-strategies/\` |"
    echo "| Technical Fixes | \`technical-fixes/\` |"
    echo "| Implementations | \`implementations/\` |"
    echo "| Reports Archive | \`reports-archive/\` |"
    echo "| Audits | \`audits/\` |"
    echo ""
    echo "## Today's Updates ($TODAY)"
    echo ""
    echo "### Lessons Learned"
    ls -1 "$BRAIN/projects/openclaw-ops/lessons-learned/" 2>/dev/null | grep "$TODAY" || echo "- No lessons today"
    echo ""
    echo "### Technical Fixes"
    ls -1 "$BRAIN/projects/openclaw-ops/technical-fixes/" 2>/dev/null | grep "$TODAY" || echo "- No tech fixes today"
    echo ""
    echo "### SEO Strategies"
    ls -1 "$BRAIN/projects/openclaw-ops/seo-strategies/" 2>/dev/null | grep "$TODAY" || echo "- No SEO updates today"
} > "$BRAIN/projects/openclaw-ops/KNOWLEDGE-INDEX.md"

log "=== Compilation Complete ==="
