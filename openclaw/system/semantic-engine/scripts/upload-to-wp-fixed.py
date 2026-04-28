#!/usr/bin/env python3
"""Upload images to WordPress - Fixed auth"""

import requests
from requests.auth import HTTPBasicAuth
import os
from pathlib import Path
import json

# WordPress credentials - CORRECTED
WP_URL = "https://rankray.com"
WP_USER = os.getenv("RANKRAY_WP_USER", "openclaw")
WP_APP_PASS = os.getenv("RANKRAY_WP_APP_PASSWORD", "OpenClaw#Admin@2026")

print(f"📤 Uploading images to {WP_URL}")
print(f"   User: {WP_USER}")
print("=" * 70)

# Test auth first
print("\n🔐 Testing authentication...")
try:
    resp = requests.get(f"{WP_URL}/wp-json/wp/v2/users/me", auth=HTTPBasicAuth(WP_USER, WP_APP_PASS), timeout=10)
    if resp.status_code == 200:
        user = resp.json()
        print(f"   ✅ Auth OK: {user.get('name', WP_USER)}")
    else:
        print(f"   ⚠️  Auth issue: {resp.status_code}")
        print(f"   Trying upload anyway...")
except Exception as e:
    print(f"   ⚠️  Error: {e}")

# All 11 images
IMAGES = [
    {"filename": "semantic-seo-services-rank-ray.jpg", "alt": "Semantic SEO optimization showing entity relationships and topic clusters for improved search rankings", "title": "Semantic SEO Services - Rank Ray"},
    {"filename": "semantic-seo-definition-concept.jpg", "alt": "Semantic SEO definition and concept visualization showing topic-focused optimization", "title": "Semantic SEO Definition"},
    {"filename": "semantic-search-engine-process.jpg", "alt": "How semantic search engines process and understand content context", "title": "Semantic Search Process"},
    {"filename": "traditional-vs-semantic-seo-comparison.jpg", "alt": "Comparison between traditional keyword SEO and modern semantic SEO approaches", "title": "Traditional vs Semantic SEO"},
    {"filename": "semantic-seo-ranking-benefits.jpg", "alt": "Benefits of semantic SEO for search rankings and visibility", "title": "Semantic SEO Benefits"},
    {"filename": "semantic-seo-optimization-process.jpg", "alt": "Step-by-step semantic SEO optimization process workflow", "title": "SEO Optimization Process"},
    {"filename": "semantic-seo-components-entities.jpg", "alt": "Core components of semantic SEO including entity optimization and topic clusters", "title": "SEO Components and Entities"},
    {"filename": "topic-cluster-structure-seo.jpg", "alt": "Topic cluster structure showing pillar pages and cluster content interconnection", "title": "Topic Cluster Structure"},
    {"filename": "semantic-seo-tools-software.jpg", "alt": "Essential semantic SEO tools and software for content optimization", "title": "Semantic SEO Tools"},
    {"filename": "semantic-seo-case-study-results.jpg", "alt": "Semantic SEO case study results showing ranking improvements and traffic growth", "title": "SEO Case Study Results"},
    {"filename": "semantic-vs-traditional-seo-differences.jpg", "alt": "Key differences between semantic and traditional SEO approaches comparison", "title": "Semantic vs Traditional SEO"},
]

SOURCE_DIR = Path("/Users/sheikhown/.openclaw/workspace/semantic-engine/images/downloads")

uploaded = []
failed = []

for i, img in enumerate(IMAGES, 1):
    print(f"\n[{i}/{len(IMAGES)}] {img['filename']}")
    
    filepath = SOURCE_DIR / img['filename']
    if not filepath.exists():
        print(f"  ❌ File not found")
        failed.append(img['filename'])
        continue
    
    with open(filepath, 'rb') as f:
        file_content = f.read()
    
    upload_url = f"{WP_URL}/wp-json/wp/v2/media"
    headers = {
        "Content-Disposition": f'attachment; filename="{img["filename"]}"',
        "Content-Type": "image/jpeg",
    }
    
    try:
        resp = requests.post(upload_url, data=file_content, headers=headers, auth=HTTPBasicAuth(WP_USER, WP_APP_PASS), timeout=60)
        
        if resp.status_code == 201:
            media_data = resp.json()
            media_id = media_data["id"]
            media_url = media_data["source_url"]
            
            # Update alt text
            update_url = f"{WP_URL}/wp-json/wp/v2/media/{media_id}"
            update_data = {"alt_text": img["alt"], "title": {"raw": img["title"]}}
            requests.post(update_url, json=update_data, auth=HTTPBasicAuth(WP_USER, WP_APP_PASS), timeout=10)
            
            print(f"  ✅ Uploaded (ID: {media_id})")
            print(f"     URL: {media_url}")
            
            uploaded.append({
                "filename": img['filename'],
                "media_id": media_id,
                "url": media_url,
                "alt": img['alt'],
                "title": img['title'],
            })
        else:
            print(f"  ❌ HTTP {resp.status_code}")
            print(f"     {resp.text[:150]}")
            failed.append(img['filename'])
            
    except Exception as e:
        print(f"  ❌ Error: {e}")
        failed.append(img['filename'])

print("\n" + "=" * 70)
print(f"📊 Summary: {len(uploaded)}/{len(IMAGES)} uploaded")

if failed:
    print(f"❌ Failed: {', '.join(failed)}")

if uploaded:
    print("\n📝 Media Index:")
    for u in uploaded:
        print(f"  - {u['filename']}: ID {u['media_id']}")
