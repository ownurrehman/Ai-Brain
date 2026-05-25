#!/bin/bash
# graphify-watch.sh — Auto-update graphify when code files change
# Usage: ./graphify-watch.sh & (runs in background)
# Or run manually after any code change session

PROJECT_DIR="/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw"
GRAPHIFY_OUT="$PROJECT_DIR/graphify-out"

cd "$PROJECT_DIR" || exit 1

echo "[graphify-watch] Checking if update needed..."

# Get last graphify timestamp
LAST_GRAPHIFY=0
if [ -f "$GRAPHIFY_OUT/graph.json" ]; then
    LAST_GRAPHIFY=$(stat -f %m "$GRAPHIFY_OUT/graph.json" 2>/dev/null || stat -c %Y "$GRAPHIFY_OUT/graph.json" 2>/dev/null || echo 0)
fi

# Check if any source file is newer than graph
NEED_UPDATE=false
while IFS= read -r file; do
    FILE_MTIME=$(stat -f %m "$file" 2>/dev/null || stat -c %Y "$file" 2>/dev/null || echo 0)
    if [ "$FILE_MTIME" -gt "$LAST_GRAPHIFY" ]; then
        NEED_UPDATE=true
        break
    fi
done < <(find "$PROJECT_DIR" -type f \( -name "*.js" -o -name "*.ts" -o -name "*.py" -o -name "*.json" -o -name "*.md" \) -not -path "*/node_modules/*" -not -path "*/graphify-out/*" 2>/dev/null)

if [ "$NEED_UPDATE" = true ]; then
    echo "[graphify-watch] Code changes detected since last graph update. Rebuilding..."
    graphify update . 2>&1 | tail -5
    echo "[graphify-watch] Graph updated at $(date)"
else
    echo "[graphify-watch] No code changes. Graph is current."
fi
