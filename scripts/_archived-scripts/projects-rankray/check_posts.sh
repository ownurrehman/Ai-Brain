#!/bin/bash

# Load credentials from master-env.env (never hardcode passwords)
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
ENV_FILE="$SCRIPT_DIR/../../master-env.env"
if [ -f "$ENV_FILE" ]; then
    export $(grep -E '^RANKRAY_WP' "$ENV_FILE" | xargs)
fi

WP_USER="${RANKRAY_WP_USER:?ERROR: RANKRAY_WP_USER not set in master-env.env}"
WP_PASS="${RANKRAY_WP_APP_PASS:?ERROR: RANKRAY_WP_APP_PASS not set in master-env.env}"
AUTH=$(echo -n "$WP_USER:$WP_PASS" | base64)

while IFS= read -r url; do
    [ -z "$url" ] && continue
    slug=$(echo "$url" | sed 's|https://rankray.com/||' | sed 's|/$||')
    
    # Check post
    resp=$(curl -s -H "Authorization: Basic $AUTH" "https://rankray.com/wp-json/wp/v2/posts?slug=$slug&per_page=1")
    if echo "$resp" | grep -q '"id":'; then
        echo "POST: $slug"
    else
        # Check page
        resp2=$(curl -s -H "Authorization: Basic $AUTH" "https://rankray.com/wp-json/wp/v2/pages?slug=$slug&per_page=1")
        if echo "$resp2" | grep -q '"id":'; then
            echo "PAGE: $slug"
        else
            echo "NOT_FOUND: $slug"
        fi
    fi
done < /tmp/rankray-post-urls.txt
