#!/usr/bin/env python3
"""Upload images to WordPress media library via REST API"""

import requests
import os
from pathlib import Path
import json

# WordPress credentials
WP_URL = "https://rankray.com"
WP_USER = "OpenClaw"
WP_APP_PASS = "OpenClaw#Admin@2026"

# All 11 images with metadata
IMAGES = [
    {"filename": "semantic-seo-services-rank-ray.jpg", "alt": "Semantic SEO optimization showing entity relationships and topic clusters for improved search rankings", "title": "Semantic SEO Services - Rank Ray"},
    {"filename": "semantic-seo-definition-concept.jpg", "alt": "Semantic SEO definition and concept visualization showing topic-focused optimization", "title": "Semantic SEO Definition Concept"},
    {"filename": "semantic-search-engine-process.jpg", "alt": "How semantic search engines process and understand content context", "title": "Semantic Search Engine Process"},
    {"filename": "traditional-vs-semantic-seo-comparison.jpg", "alt": "Comparison between traditional keyword SEO and modern semantic SEO approaches", "title": "Traditional vs Semantic SEO Comparison"},
    {"filename": "semantic-seo-ranking-benefits.jpg", "alt": "Benefits of semantic SEO for search rankings and visibility", "title": "Semantic SEO Ranking Benefits"},
    {"filename": "semantic-seo-optimization-process.jpg", "alt": "Step-by-step semantic SEO optimization process workflow", "title": "Semantic SEO Optimization Process"},
    {"filename": "semantic-seo-components-entities.jpg", "alt": "Core components of semantic SEO including entity optimization and topic clusters", "title": "Semantic SEO Components and Entities"},
    {"filename": "topic-cluster-structure-seo.jpg", "alt": "Topic cluster structure showing pillar pages and cluster content interconnection", "title": "Topic Cluster Structure for SEO"},
    {"filename": "semantic-seo-tools-software.jpg", "alt": "Essential semantic SEO tools and software for content optimization", "title": "Semantic SEO Tools and Software"},
    {"filename": "semantic-seo-case-study-results.jpg", "alt": "Semantic SEO case study results showing ranking improvements and traffic growth", "title": "Semantic SEO Case Study Results"},
    {"filename": "semantic-vs-traditional-seo-differences.jpg", "alt": "Key differences between semantic and traditional SEO approaches comparison", "title": "Semantic vs Traditional SEO Differences"},
]

SOURCE_DIR = Path("/Users/sheikhown/.openclaw/workspace/semantic-engine/images/downloads")
MEDIA_INDEX_FILE = Path("/Users/sheikhown/.openclaw/workspace/semantic-engine/images/media-index.json")

print(f"📤 Uploading {len(IMAGES)} images to WordPress media library")
print("=" * 70)

uploaded = []
failed = []
media_index = []

for i, img in enumerate(IMAGES, 1):
    print(f"[{i}/{len(IMAGES)}] {img['filename']}")
    
    filepath = SOURCE_DIR / img['filename']
    if not filepath.exists():
        print(f"  ❌ File not found: {filepath}")
        failed.append(img['filename'])
        continue
    
    # Read file
    with open(filepath, 'rb') as f:
        file_content = f.read()
    
    # Upload to WordPress
    upload_url = f"{WP_URL}/wp-json/wp/v2/media"
    filename = img['filename']
    
    headers = {
        "Content-Disposition": f'attachment; filename="{filename}"',
        "Content-Type": "image/jpeg",
    }
    
    try:
        resp = requests.post(upload_url, data=file_content, headers=headers, auth=(WP_USER, WP_APP_PASS), timeout=60)
        resp.raise_for_status()
        
        media_data = resp.json()
        media_id = media_data["id"]
        media_url = media_data["source_url"]
        
        print(f"  ✅ Uploaded: ID {media_id}")
        print(f"     URL: {media_url}")
        
        uploaded.append({
            "filename": img['filename'],
            "media_id": media_id,
            "url": media_url,
            "alt": img['alt'],
            "title": img['title'],
        })
        
        media_index.append({
            "filename": img['filename'],
            "media_id": media_id,
            "url": media_url,
            "alt_text": img['alt'],
            "title": img['title'],
            "uploaded_at": requests.get(f"{WP_URL}/wp-json/wp/v2/media/{media_id}", auth=(WP_USER, WP_APP_PASS)).json().get("date", ""),
        })
        
    except Exception as e:
        print(f"  ❌ Error: {e}")
        failed.append(img['filename'])
    
    print()

print("=" * 70)
print(f"📊 Summary: {len(uploaded)}/{len(IMAGES)} uploaded")

if failed:
    print(f"❌ Failed: {', '.join(failed)}")

# Save media index
if media_index:
    with open(MEDIA_INDEX_FILE, 'w') as f:
        json.dump(media_index, f, indent=2)
    print(f"💾 Media index saved to: {MEDIA_INDEX_FILE}")

print("\n📝 Uploaded images:")
for u in uploaded:
    print(f"  - {u['filename']} (ID: {u['media_id']})")
