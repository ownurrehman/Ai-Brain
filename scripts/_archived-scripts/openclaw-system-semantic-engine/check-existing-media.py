#!/usr/bin/env python3
"""Check WordPress media library for existing images we can reuse"""

import requests
import os
import re

# WordPress credentials
WP_URL = "https://rankray.com"
WP_USER = os.getenv("RANKRAY_WP_USERNAME", "")
WP_APP_PASS = os.getenv("RANKRAY_WP_APP_PASSWORD", "")

if not WP_USER or not WP_APP_PASS:
    print("❌ Missing WordPress credentials (RANKRAY_WP_USERNAME or RANKRAY_WP_APP_PASSWORD)")
    exit(1)

# Required images for semantic SEO article
REQUIRED_TERMS = [
    "semantic seo",
    "seo analytics",
    "seo dashboard",
    "seo graph",
    "seo workflow",
    "seo process",
    "topic cluster",
    "seo tools",
    "seo comparison",
    "seo ranking",
    "entity seo",
]

print("🔍 Checking WordPress media library for existing images...")
print("=" * 70)

# Fetch media library
media_url = f"{WP_URL}/wp-json/wp/v2/media"
params = {"per_page": 100}

try:
    resp = requests.get(media_url, params=params, auth=(WP_USER, WP_APP_PASS), timeout=30)
    resp.raise_for_status()
    media_items = resp.json()
    
    print(f"📦 Total media items fetched: {len(media_items)}")
    print()
    
    matches = []
    
    for item in media_items:
        title = item.get("title", {}).get("rendered", "").lower()
        alt_text = item.get("alt_text", "").lower()
        description = item.get("description", {}).get("rendered", "").lower()
        
        # Check if any required term matches
        for term in REQUIRED_TERMS:
            if term in title or term in alt_text or term in description:
                matches.append({
                    "id": item["id"],
                    "title": item["title"]["rendered"],
                    "alt": item.get("alt_text", ""),
                    "url": item["source_url"],
                    "matched_term": term,
                })
                break
    
    if matches:
        print(f"✅ Found {len(matches)} potentially reusable images:\n")
        for m in matches:
            print(f"  ID: {m['id']}")
            print(f"  Title: {m['title']}")
            print(f"  Alt: {m['alt'] or '(none)'}")
            print(f"  URL: {m['url']}")
            print(f"  Matched: '{m['matched_term']}'")
            print()
    else:
        print("⚠️  No existing images match our semantic SEO requirements")
        print("\n💡 Need to source new images from external free sources")
        
except Exception as e:
    print(f"❌ Error: {e}")
