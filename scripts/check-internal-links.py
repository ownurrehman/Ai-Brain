#!/usr/bin/env python3
"""
Find internal links to target pages and identify linking gaps
"""
import json, base64, urllib.request, urllib.error, re

WP_URL = "https://tonicphysio.com"
USER = "Dan"
PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"
auth_str = base64.b64encode(f"{USER}:{PASS}".encode()).decode()

def api_get(path):
    req = urllib.request.Request(f"{WP_URL}/wp-json/wp/v2/{path}", headers={
        "Authorization": f"Basic {auth_str}",
        "Content-Type": "application/json"
    })
    with urllib.request.urlopen(req, timeout=30) as resp:
        return json.loads(resp.read().decode())

# Target pages that need more internal links
targets = {
    "physiotherapy-in-milton": ["physiotherapy milton", "milton physiotherapy", "physiotherapy in milton"],
    "registered-massage-therapy": ["massage milton", "massage therapy milton", "milton massage"],
    "lymphatic-drainage-massage-milton": ["lymphatic drainage massage", "lymphatic massage"],
    "indie-head-massage": ["head massage", "head massage near me"],
    "pediatric-physiotherapy": ["pediatric physiotherapy", "kids physiotherapy"],
    "tmj-treatment": ["tmj massage", "tmj treatment"],
    "motor-vehicle-accident-physiotherapy": ["mva physiotherapy"],
}

# Get all pages and posts
pages = api_get("pages?per_page=100&status=publish")
posts = api_get("posts?per_page=100&status=publish")
all_content = pages + posts

print(f"Analyzing {len(all_content)} pages/posts for internal links...\n")

for slug, keywords in targets.items():
    target_url = f"https://tonicphysio.com/{slug}/"
    if not slug.endswith("/"):
        target_url = f"https://tonicphysio.com/{slug}"
    
    links_found = []
    for item in all_content:
        content = item.get("content", {}).get("rendered", "")
        if not content:
            continue
        # Find links to target
        pattern = re.compile(rf'href="[^"]*{re.escape(slug)}[^"]*"[^>]*>([^<]+)', re.IGNORECASE)
        matches = pattern.findall(content)
        if matches:
            links_found.append({
                "id": item["id"],
                "title": item["title"]["rendered"],
                "type": item["type"],
                "anchors": matches
            })
    
    print(f"=== Target: /{slug}/ ===")
    print(f"Keywords: {', '.join(keywords)}")
    print(f"Internal links found: {len(links_found)}")
    if links_found:
        for link in links_found[:5]:
            print(f"  From: {link['title'][:50]} ({link['type']} ID:{link['id']})")
            print(f"    Anchors: {link['anchors'][:3]}")
    else:
        print("  ⚠️ NO INTERNAL LINKS FOUND")
    print()
