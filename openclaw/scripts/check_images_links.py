#!/usr/bin/env python3
import json, base64, re
from urllib.request import Request, urlopen

WP_URL = "https://tonicphysio.com/wp-json/wp/v2"
WP_USER = "Dan"
WP_APP_PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"
HEADERS = {
    "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    "Accept": "application/json",
    "Referer": "https://tonicphysio.com/",
}

POST_IDS = [13030, 13032, 13033, 13034, 13039, 13040]

def fetch(url):
    req = Request(url)
    creds = base64.b64encode(f"{WP_USER}:{WP_APP_PASS}".encode()).decode()
    req.add_header("Authorization", f"Basic {creds}")
    for k, v in HEADERS.items():
        req.add_header(k, v)
    with urlopen(req, timeout=30) as resp:
        return json.loads(resp.read().decode())

# Fetch all service pages
print("=== SERVICE PAGES ===")
try:
    services = fetch(f"{WP_URL}/pages?per_page=100")
    for s in services:
        print(f"  {s['id']}: {s['title']['rendered']} - {s['link']}")
except Exception as e:
    print(f"Error fetching pages: {e}")

print("\n=== POST ANALYSIS ===")
for pid in POST_IDS:
    try:
        post = fetch(f"{WP_URL}/posts/{pid}")
        content = post['content']['rendered']
        title = post['title']['rendered']
        featured = post.get('featured_media', 0)
        
        img_count = len(re.findall(r'<img', content))
        links = re.findall(r'href="(https://tonicphysio.com/[^"]+)"', content)
        
        print(f"\nPost {pid}: {title}")
        print(f"  Featured image: {featured}")
        print(f"  Inline images: {img_count}")
        print(f"  Internal links ({len(links)}):")
        for l in links[:10]:
            print(f"    - {l}")
    except Exception as e:
        print(f"Error fetching post {pid}: {e}")
