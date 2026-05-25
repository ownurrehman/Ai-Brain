#!/usr/bin/env python3
"""Simple cookie-based upload - login first, save cookies, then upload"""

import requests
from pathlib import Path
import json
import http.cookiejar as cookielib
import re

WP_URL = "https://rankray.com"
WP_LOGIN = "https://www.rankray.com/wp-login.php"
WP_USER = "openclaw"
WP_PASS = "OpenClaw#Admin@2026"

IMAGES = [
    {"file": "semantic-seo-services-rank-ray.jpg", "alt": "Semantic SEO optimization", "title": "Semantic SEO Services"},
    {"file": "semantic-seo-definition-concept.jpg", "alt": "Semantic SEO definition", "title": "Semantic SEO Definition"},
    {"file": "semantic-search-engine-process.jpg", "alt": "Search engine process", "title": "Search Process"},
    {"file": "traditional-vs-semantic-seo-comparison.jpg", "alt": "Traditional vs semantic", "title": "SEO Comparison"},
    {"file": "semantic-seo-ranking-benefits.jpg", "alt": "SEO ranking benefits", "title": "SEO Benefits"},
    {"file": "semantic-seo-optimization-process.jpg", "alt": "SEO optimization", "title": "Optimization Process"},
    {"file": "semantic-seo-components-entities.jpg", "alt": "SEO components", "title": "SEO Components"},
    {"file": "topic-cluster-structure-seo.jpg", "alt": "Topic cluster", "title": "Topic Clusters"},
    {"file": "semantic-seo-tools-software.jpg", "alt": "SEO tools", "title": "SEO Tools"},
    {"file": "semantic-seo-case-study-results.jpg", "alt": "Case study results", "title": "Case Study Results"},
    {"file": "semantic-vs-traditional-seo-differences.jpg", "alt": "SEO differences", "title": "SEO Differences"},
]

SOURCE_DIR = Path("/Users/sheikhown/.openclaw/workspace/semantic-engine/images/downloads")

print("📤 WordPress Image Upload via Cookie Session")
print("=" * 70)

# Create session with cookie jar
session = requests.Session()
session.headers.update({
    "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36",
})

# Step 1: Get login page to retrieve cookies and nonces
print("\n🔐 Step 1: Getting login page...")
resp = session.get(WP_LOGIN)
print(f"   Status: {resp.status_code}")

# Extract WordPress nonces if present
login_html = resp.text
wp_nonce = re.search(r'name="_wpnonce" value="([^"]+)"', login_html)
redirect_to = re.search(r'name="redirect_to" value="([^"]+)"', login_html)

if wp_nonce:
    print(f"   ✅ Found nonce: {wp_nonce.group(1)[:20]}...")
else:
    print(f"   ⚠️  No nonce found (may not be required)")

# Step 2: Login
print("\n🔐 Step 2: Logging in...")
login_data = {
    "log": WP_USER,
    "pwd": WP_PASS,
    "wp-submit": "Log In",
    "redirect_to": WP_URL + "/wp-admin/",
    "testcookie": "1",
}

if wp_nonce:
    login_data["_wpnonce"] = wp_nonce.group(1)

resp = session.post(WP_LOGIN, data=login_data, allow_redirects=True)
print(f"   Status: {resp.status_code}")
print(f"   Final URL: {resp.url}")

# Check if login succeeded
if "wp-login.php" in resp.url or "login" in resp.url.lower():
    print("   ❌ Login failed - still on login page")
    print(f"   Response preview: {resp.text[:300]}")
    exit(1)

print("   ✅ Login successful!")

# Step 3: Test API access with session
print("\n🔐 Step 3: Testing REST API with session...")
resp = session.get(f"{WP_URL}/wp-json/wp/v2/users/me")
print(f"   Status: {resp.status_code}")
if resp.status_code == 200:
    user = resp.json()
    print(f"   ✅ Authenticated as: {user.get('name', 'N/A')}")
else:
    print(f"   ⚠️  API access limited (status {resp.status_code})")

# Step 4: Upload images
print("\n📤 Step 4: Uploading images...")
uploaded = []
failed = []

for i, img in enumerate(IMAGES, 1):
    print(f"\n[{i}/{len(IMAGES)}] {img['file']}")
    
    filepath = SOURCE_DIR / img['file']
    if not filepath.exists():
        print(f"  ❌ File not found")
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
            
            # Update alt text and title
            update_url = f"{WP_URL}/wp-json/wp/v2/media/{media_id}"
            update_data = {"alt_text": img["alt"], "title": {"raw": img["title"]}}
            session.post(update_url, json=update_data, timeout=10)
            
            print(f"  ✅ Uploaded (ID: {media_id})")
            print(f"     URL: {media_url}")
            
            uploaded.append({
                "filename": img['file'],
                "media_id": media_id,
                "url": media_url,
                "alt": img['alt'],
                "title": img['title'],
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
    print("\n📝 Uploaded Media:")
    for u in uploaded:
        print(f"  - {u['filename']} (ID: {u['media_id']})")
