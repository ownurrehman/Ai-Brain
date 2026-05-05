#!/bin/bash
# Rank Ray Internal Link Processor - Shell Version
# This runs entirely in background

API="https://rankray.com/wp-json/wp/v2"
AUTH="openclaw:6Zz9 5gJL 8uyA QH4g RQDH GV1j"
LOG="/tmp/rankray-link-batch.log"
URLS_FILE="/tmp/rankray-post-urls.txt"

echo "[$(date)] Starting internal link batch processing" > "$LOG"

# Get all post IDs first
echo "[$(date)] Fetching post IDs..." >> "$LOG"
COUNTER=0
while IFS= read -r url; do
    [ -z "$url" ] && continue
    slug=$(echo "$url" | sed 's|https://rankray.com/||g' | sed 's|/$||g')
    
    # Get post ID
    response=$(curl -s -u "$AUTH" "${API}/posts?slug=${slug}&per_page=1" 2>/dev/null)
    post_id=$(echo "$response" | python3 -c "import sys,json; d=json.load(sys.stdin); print(d[0]['id'] if d else 'NONE')" 2>/dev/null)
    
    if [ "$post_id" != "NONE" ] && [ -n "$post_id" ]; then
        echo "${slug}|${post_id}" >> /tmp/rankray-slug-id-map.txt
        echo "  ✓ ${slug}: ${post_id}" >> "$LOG"
    else
        echo "  ✗ ${slug}: NOT FOUND" >> "$LOG"
    fi
    
    COUNTER=$((COUNTER + 1))
    if [ $((COUNTER % 5)) -eq 0 ]; then
        echo "[$(date)] Processed ${COUNTER} posts..." >> "$LOG"
        sleep 3
    fi
done < "$URLS_FILE"

TOTAL_FOUND=$(wc -l < /tmp/rankray-slug-id-map.txt 2>/dev/null || echo 0)
echo "[$(date)] Found ${TOTAL_FOUND} posts with IDs" >> "$LOG"
echo "[$(date)] ID mapping saved to /tmp/rankray-slug-id-map.txt" >> "$LOG"
