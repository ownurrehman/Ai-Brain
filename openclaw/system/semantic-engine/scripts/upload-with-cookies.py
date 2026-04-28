#!/usr/bin/env python3
"""Upload images using session cookies from browser login"""

from playwright.sync_api import sync_playwright
import requests
from pathlib import Path
import json
import time

WP_URL = "https://rankray.com"
WP_LOGIN = "https://www.rankray.com/wp-login.php"
WP_USER = "openclaw"
WP_PASS = "OpenClaw#Admin@2026"

IMAGES = [
    {"file": "semantic-seo-services-rank-ray.jpg", "alt": "Semantic SEO optimization showing entity relationships", "title": "Semantic SEO Services"},
    {"file": "semantic-seo-definition-concept.jpg", "alt": "Semantic SEO definition concept", "title": "Semantic SEO Definition"},
    {"file": "semantic-search-engine-process.jpg", "alt": "Semantic search engine process", "title": "Search Process"},
    {"file": "traditional-vs-semantic-seo-comparison.jpg", "alt": "Traditional vs semantic SEO", "title": "SEO Comparison"},
    {"file": "semantic-seo-ranking-benefits.jpg", "alt": "Semantic SEO ranking benefits", "title": "SEO Benefits"},
    {"file": "semantic-seo-optimization-process.jpg", "alt": "SEO optimization process", "title": "Optimization Process"},
    {"file": "semantic-seo-components-entities.jpg", "alt": "SEO components entities", "title": "SEO Components"},
    {"file": "topic-cluster-structure-seo.jpg", "alt": "Topic cluster structure", "title": "Topic Clusters"},
    {"file": "semantic-seo-tools-software.jpg", "alt": "Semantic SEO tools", "title": "SEO Tools"},
    {"file": "semantic-seo-case-study-results.jpg", "alt": "SEO case study results", "title": "Case Study Results"},
    {"file": "semantic-vs-traditional-seo-differences.jpg", "alt": "Semantic vs traditional SEO", "title": "SEO Differences"},
]

SOURCE_DIR = Path("/Users/sheikhown/.openclaw/workspace/semantic-engine/images/downloads")

print("📤 Uploading images using session cookies")
print("=" * 70)

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()
    
    # Login
    print("\n🔐 Logging in...")
    page.goto(WP_LOGIN)
    page.fill("#user_login", WP_USER)
    page.fill("#user_pass", WP_PASS)
    page.click("#wp-submit")
    page.wait_for_load_state("networkidle")
    time.sleep(3)
    
    if "login" in page.url.lower():
        print("❌ Login failed")
        browser.close()
        exit(1)
    
    print("✅ Logged in")
    
    # Get cookies
    cookies = context.cookies()
    print(f"🍪 Got {len(cookies)} cookies")
    
    # Create requests session with cookies
    session = requests.Session()
    for cookie in cookies:
        session.cookies.set(cookie['name'], cookie['value'], domain=cookie['domain'], path=cookie.get('path', '/'))
    
    browser.close()
    
    # Upload images using session
    print("\n📤 Uploading images...")
    uploaded = []
    failed = []
    
    for i, img in enumerate(IMAGES, 1):
        print(f"\n[{i}/{len(IMAGES)}] {img['file']}")
        
        filepath = SOURCE_DIR / img['file']
        if not filepath.exists():
            print(f"  ❌ Not found")
            failed.append(img['file'])
            continue
        
        with open(filepath, 'rb') as f:
            content = f.read()
        
        upload_url = f"{WP_URL}/wp-json/wp/v2/media"
        headers = {
            "Content-Disposition": f'attachment; filename="{img["file"]}"',
            "Content-Type": "image/jpeg",
        }
        
        try:
            resp = session.post(upload_url, data=content, headers=headers, timeout=60)
            
            if resp.status_code == 201:
                media = resp.json()
                media_id = media["id"]
                media_url = media["source_url"]
                
                # Update alt text
                update_url = f"{WP_URL}/wp-json/wp/v2/media/{media_id}"
                session.post(update_url, json={"alt_text": img["alt"], "title": {"raw": img["title"]}}, timeout=10)
                
                print(f"  ✅ Uploaded (ID: {media_id})")
                uploaded.append({
                    "filename": img['file'],
                    "media_id": media_id,
                    "url": media_url,
                    "alt": img['alt'],
                })
            else:
                print(f"  ❌ HTTP {resp.status_code}")
                print(f"     {resp.text[:150]}")
                failed.append(img['file'])
        except Exception as e:
            print(f"  ❌ Error: {e}")
            failed.append(img['file'])
    
    # Save media index
    if uploaded:
        with open("/Users/sheikhown/.openclaw/workspace/semantic-engine/images/media-index-final.json", 'w') as f:
            json.dump(uploaded, f, indent=2)
        print(f"\n💾 Media index saved")
    
    print("\n" + "=" * 70)
    print(f"📊 Summary: {len(uploaded)}/{len(IMAGES)} uploaded")
    
    if failed:
        print(f"❌ Failed: {', '.join(failed)}")
    
    if uploaded:
        print("\n📝 Media IDs:")
        for u in uploaded:
            print(f"  - {u['filename']}: {u['media_id']}")
