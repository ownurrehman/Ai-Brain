#!/usr/bin/env python3
"""
Batch quick fixes for ACF pages with only 'since 2019' or 'forced link' issues.
No full rewrites, just targeted fixes.
"""

import requests, base64, json, time

USER = "openclaw"
PASS = "6Zz9 5gJL 8uyA QH4g RQDH GV1j"
AUTH = base64.b64encode(f"{USER}:{PASS}".encode()).decode()
HEADERS = {"Authorization": f"Basic {AUTH}", "Content-Type": "application/json"}

def fix_page(page_id, title):
    """Apply quick fixes to a page."""
    resp = requests.get(f"https://rankray.com/wp-json/wp/v2/pages/{page_id}?_fields=acf", headers=HEADERS, timeout=30)
    if resp.status_code != 200:
        return False, f"Fetch error: {resp.status_code}"
    
    data = resp.json()
    acf = data.get("acf", {})
    fixes = {}
    
    # Fix 1: since 2019 in h2_paragraph_3
    h2_p3 = acf.get("h2_paragraph_3", "")
    if "since 2019" in h2_p3.lower():
        # Replace with generic results text based on service
        h2_p3 = h2_p3.replace(
            f"Rank Ray has built {title.lower()} systems for healthcare, finance, real estate, ecommerce, SaaS, and franchise brands since 2019.",
            f"Our {title.lower()} systems have delivered proven results for healthcare, finance, real estate, ecommerce, SaaS, and franchise brands through strategic execution and continuous optimization."
        )
        fixes["h2_paragraph_3"] = h2_p3
    
    # Fix 2: since 2019 in why_us_h3_paragraph
    why_intro = acf.get("why_us_h3_paragraph", "")
    if "since 2019" in why_intro.lower():
        why_intro = why_intro.replace(
            f"Rank Ray has managed {title.lower()} for businesses across healthcare, finance, technology, ecommerce, franchise, and professional services since 2019.",
            f"Rank Ray designs {title.lower()} systems that drive measurable business results. Our team combines strategic expertise with execution excellence to deliver outcomes that matter."
        )
        fixes["why_us_h3_paragraph"] = why_intro
    
    # Fix 3: forced link in h2_paragraph_2
    h2_p2 = acf.get("h2_paragraph_2", "")
    if "integrates with" in h2_p2 and "for unified" in h2_p2:
        # Find the pattern and replace with natural language
        import re
        pattern = r'<p>Our [^<]+ integrates with <a href="([^"]+)">([^<]+)</a> for unified [^<]+\.</p>'
        match = re.search(pattern, h2_p2)
        if match:
            url, anchor = match.groups()
            h2_p2 = re.sub(pattern, f'', h2_p2)
            # Add natural sentence before closing </p> of previous paragraph
            h2_p2 = h2_p2.replace('</p><p>', f'. Our approach works alongside <a href="{url}">{anchor}</a> to create comprehensive solutions that address your full marketing needs.</p><p>')
        fixes["h2_paragraph_2"] = h2_p2
    
    if not fixes:
        return True, "No fixes needed"
    
    # Push fixes
    payload = {"acf": fixes}
    resp = requests.post(f"https://rankray.com/wp-json/wp/v2/pages/{page_id}", headers=HEADERS, json=payload, timeout=30)
    if resp.status_code == 200:
        return True, f"Fixed: {', '.join(fixes.keys())}"
    else:
        return False, f"Push error: {resp.status_code}"

# Load quick fix pages
with open("/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/rankray.com/pages-categorized.json") as f:
    data = json.load(f)

quick_fix_pages = data.get("needs_quick_fix", [])

print(f"Processing {len(quick_fix_pages)} quick-fix pages...")
for p in quick_fix_pages:
    page_id = p["id"]
    title = p["title"]
    success, msg = fix_page(page_id, title)
    status = "✅" if success else "❌"
    print(f"{status} {page_id}: {title[:40]} | {msg}")
    time.sleep(200)  # 200 second gap

print("\nBatch quick fixes complete!")
