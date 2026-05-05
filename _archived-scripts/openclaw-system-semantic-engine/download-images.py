#!/usr/bin/env python3
"""Download stock images for Semantic SEO pillar article"""

import requests
import os
from pathlib import Path

# Image plan from IMAGE-PLAN-semantic-seo-services.md
IMAGES = [
    {"type": "featured", "term": "SEO analytics dashboard", "filename": "semantic-seo-services-rank-ray.jpg", "alt": "Semantic SEO optimization showing entity relationships and topic clusters for improved search rankings"},
    {"type": "body", "term": "SEO concept diagram", "filename": "semantic-seo-definition-concept.jpg", "alt": "Semantic SEO definition and concept visualization showing topic-focused optimization"},
    {"type": "body", "term": "search engine process", "filename": "semantic-search-engine-process.jpg", "alt": "How semantic search engines process and understand content context"},
    {"type": "body", "term": "SEO comparison", "filename": "traditional-vs-semantic-seo-comparison.jpg", "alt": "Comparison between traditional keyword SEO and modern semantic SEO approaches"},
    {"type": "body", "term": "SEO rankings graph", "filename": "semantic-seo-ranking-benefits.jpg", "alt": "Benefits of semantic SEO for search rankings and visibility"},
    {"type": "body", "term": "SEO workflow", "filename": "semantic-seo-optimization-process.jpg", "alt": "Step-by-step semantic SEO optimization process workflow"},
    {"type": "body", "term": "SEO components", "filename": "semantic-seo-components-entities.jpg", "alt": "Core components of semantic SEO including entity optimization and topic clusters"},
    {"type": "body", "term": "topic cluster model", "filename": "topic-cluster-structure-seo.jpg", "alt": "Topic cluster structure showing pillar pages and cluster content interconnection"},
    {"type": "body", "term": "SEO tools dashboard", "filename": "semantic-seo-tools-software.jpg", "alt": "Essential semantic SEO tools and software for content optimization"},
    {"type": "body", "term": "SEO results graph", "filename": "semantic-seo-case-study-results.jpg", "alt": "Semantic SEO case study results showing ranking improvements and traffic growth"},
    {"type": "body", "term": "SEO strategy comparison", "filename": "semantic-vs-traditional-seo-differences.jpg", "alt": "Key differences between semantic and traditional SEO approaches comparison"},
]

OUTPUT_DIR = Path("/Users/sheikhown/.openclaw/workspace/semantic-engine/images/downloads")
OUTPUT_DIR.mkdir(parents=True, exist_ok=True)

print(f"📥 Downloading {len(IMAGES)} images for Semantic SEO pillar article")
print("=" * 70)

# Using Pexels API (free tier)
PEXELS_API_KEY = os.getenv("PEXELS_API_KEY", "")

if not PEXELS_API_KEY:
    print("⚠️  PEXELS_API_KEY not set. Using Unsplash source URLs instead.")
    print()

downloaded = []

for i, img in enumerate(IMAGES, 1):
    print(f"[{i}/{len(IMAGES)}] {img['type'].upper()}: {img['term']}")
    print(f"  → {img['filename']}")
    
    if PEXELS_API_KEY:
        # Search Pexels
        search_url = "https://api.pexels.com/v1/search"
        params = {"query": img["term"], "per_page": 1, "orientation": "landscape"}
        headers = {"Authorization": PEXELS_API_KEY}
        
        try:
            resp = requests.get(search_url, params=params, headers=headers, timeout=10)
            resp.raise_for_status()
            data = resp.json()
            
            if data.get("photos"):
                photo = data["photos"][0]
                img_url = photo["src"]["large2x"]
                
                # Download image
                img_resp = requests.get(img_url, timeout=10)
                img_resp.raise_for_status()
                
                filepath = OUTPUT_DIR / img["filename"]
                with open(filepath, "wb") as f:
                    f.write(img_resp.content)
                
                size_kb = len(img_resp.content) / 1024
                print(f"  ✅ Downloaded: {size_kb:.1f} KB")
                downloaded.append({**img, "path": str(filepath), "size_kb": round(size_kb, 1)})
            else:
                print(f"  ⚠️  No results from Pexels")
                
        except Exception as e:
            print(f"  ❌ Error: {e}")
    else:
        # Fallback: Unsplash source (deprecated but may still work)
        unsplash_url = f"https://source.unsplash.com/1200x630/?{img['term'].replace(' ', ',')}"
        print(f"  ⏳ Fetching from Unsplash: {unsplash_url}")
        
        try:
            resp = requests.get(unsplash_url, timeout=15)
            if resp.status_code == 200 and len(resp.content) > 10000:
                filepath = OUTPUT_DIR / img["filename"]
                with open(filepath, "wb") as f:
                    f.write(resp.content)
                
                size_kb = len(resp.content) / 1024
                print(f"  ✅ Downloaded: {size_kb:.1f} KB")
                downloaded.append({**img, "path": str(filepath), "size_kb": round(size_kb, 1)})
            else:
                print(f"  ⚠️  Unsplash returned {resp.status_code} or small file")
        except Exception as e:
            print(f"  ❌ Error: {e}")
    
    print()

print("=" * 70)
print(f"📊 Summary: {len(downloaded)}/{len(IMAGES)} images downloaded")
print(f"📁 Location: {OUTPUT_DIR}")

if downloaded:
    print("\n📝 Files ready for upload:")
    for d in downloaded:
        print(f"  - {d['filename']} ({d['size_kb']} KB) - {d['alt'][:60]}...")
