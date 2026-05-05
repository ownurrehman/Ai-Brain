#!/bin/bash
# Scan remaining 115 posts and fix low-link ones

API="https://rankray.com/wp-json/wp/v2"
AUTH="openclaw:6Zz9 5gJL 8uyA QH4g RQDH GV1j"
LOG="/tmp/rankray-auto-fix.log"

# Skip first 40 already checked
TAIL_COUNT=$(expr $(wc -l < /tmp/rankray-slug-id-map.txt) - 40)

echo "[$(date)] Scanning ${TAIL_COUNT} remaining posts..." > "$LOG"

tail -n $TAIL_COUNT /tmp/rankray-slug-id-map.txt | while IFS='|' read -r slug post_id; do
    # Get content and count links
    response=$(curl -s -u "$AUTH" "${API}/posts/${post_id}?_fields=content" 2>/dev/null)
    link_count=$(echo "$response" | python3 -c "
import sys,json,re
c=json.load(sys.stdin).get('content',{}).get('rendered','')
links=re.findall(r'href=\"([^\"]+)\"', c)
internal=[l for l in links if 'rankray.com' in l]
print(len(set(internal)))
" 2>/dev/null)
    
    if [ -z "$link_count" ]; then
        link_count=0
    fi
    
    if [ "$link_count" -lt 10 ]; then
        echo "NEEDS_LINKS|${slug}|${post_id}|${link_count}" >> /tmp/rankray-needs-links.txt
        echo "  ⚠️ ${slug}: ${link_count} links" >> "$LOG"
    else
        echo "  ✓ ${slug}: ${link_count} links" >> "$LOG"
    fi
    
    # Rate limit
    sleep 2
done

echo "[$(date)] Scan complete" >> "$LOG
echo "Results saved to /tmp/rankray-needs-links.txt" >> "$LOG
