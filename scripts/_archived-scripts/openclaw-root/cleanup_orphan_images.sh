#!/bin/bash
# Cleanup orphaned duplicate images after 6-hour safety threshold
# Usage: ./cleanup_orphan_images.sh --dry-run (preview) or ./cleanup_orphan_images.sh --delete (execute)

# Load credentials from master-env.env (never hardcode passwords)
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
ENV_FILE="$SCRIPT_DIR/../master-env.env"
if [ -f "$ENV_FILE" ]; then
    export $(grep -E '^RANKRAY_WP' "$ENV_FILE" | xargs)
fi

WP_URL="https://rankray.com/wp-json/wp/v2"
WP_USER="${RANKRAY_WP_USER:?ERROR: RANKRAY_WP_USER not set in master-env.env}"
WP_PASS="${RANKRAY_WP_APP_PASS:?ERROR: RANKRAY_WP_APP_PASS not set in master-env.env}"

# Orphaned media IDs from May 2026 batch (not linked to any posts)
ORPHAN_IDS=(
    21132 21133 21134 21135 21136 21137 21138 21139 21140 21141
    21142 21143 21144 21145 21146 21147 21148 21149 21150 21151
    21152 21153 21154 21155 21156 21157 21158 21159 21160 21161
    21162 21163
)

MODE="${1:---dry-run}"

echo "Orphaned Media Cleanup"
echo "======================"
echo "Mode: $MODE"
echo "Total orphaned items: ${#ORPHAN_IDS[@]}"
echo ""

DELETED=0
SKIPPED=0

for media_id in "${ORPHAN_IDS[@]}"; do
    # Check media age
    response=$(curl -s -u "$WP_USER:$WP_PASS" "$WP_URL/media/$media_id?_fields=id,date,title" 2>/dev/null)
    
    if [ -z "$response" ]; then
        echo "[$media_id] ERROR: No response from API"
        continue
    fi
    
    # Parse date
    date_str=$(echo "$response" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('date',''))" 2>/dev/null)
    title=$(echo "$response" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d.get('title',{}).get('rendered','Unknown'))" 2>/dev/null)
    
    if [ -z "$date_str" ]; then
        echo "[$media_id] ERROR: Could not parse date"
        continue
    fi
    
    # Calculate age in hours (server time)
    age_hours=$(python3 -c "
from datetime import datetime, timezone
now = datetime.now(timezone.utc)
media_date = datetime.strptime('$date_str', '%Y-%m-%dT%H:%M:%S').replace(tzinfo=timezone.utc)
diff = (now - media_date).total_seconds() / 3600
print(f'{diff:.1f}')
" 2>/dev/null)
    
    # Check if older than 6 hours (accounting for server time being ahead)
    if (( $(echo "$age_hours > 6" | bc -l) )); then
        if [ "$MODE" == "--delete" ]; then
            # Delete the media item
            delete_resp=$(curl -s -o /dev/null -w "%{http_code}" -u "$WP_USER:$WP_PASS" -X DELETE "$WP_URL/media/$media_id?force=true" 2>/dev/null)
            if [ "$delete_resp" == "200" ] || [ "$delete_resp" == "202" ]; then
                echo "[$media_id] ✓ DELETED ($age_hours hours old) - $title"
                ((DELETED++))
            else
                echo "[$media_id] ✗ DELETE FAILED (HTTP $delete_resp) - $title"
            fi
        else
            echo "[$media_id] → Would delete ($age_hours hours old) - $title"
            ((DELETED++))
        fi
    else
        echo "[$media_id] → Skipped ($age_hours hours old, < 6h threshold) - $title"
        ((SKIPPED++))
    fi
done

echo ""
echo "======================"
if [ "$MODE" == "--dry-run" ]; then
    echo "DRY RUN COMPLETE"
    echo "$DELETED items would be deleted"
    echo "$SKIPPED items would be skipped (too young)"
    echo ""
    echo "To execute deletions, run:"
    echo "  ./cleanup_orphan_images.sh --delete"
else
    echo "CLEANUP COMPLETE"
    echo "$DELETED items deleted"
    echo "$SKIPPED items skipped (too young)"
fi
