#!/usr/bin/env python3
"""Download images from Pexels for Semantic SEO article"""

import requests
import os
from pathlib import Path

# Pexels API (free tier, no key needed for basic usage via direct image URLs)
# Using Pexels free stock photos via direct URLs

IMAGES = [
    {"type": "featured", "term": "analytics dashboard", "filename": "semantic-seo-services-rank-ray.jpg", "alt": "Semantic SEO optimization showing entity relationships and topic clusters for improved search rankings"},
    {"type": "body", "term": "data visualization", "filename": "semantic-seo-definition-concept.jpg", "alt": "Semantic SEO definition and concept visualization showing topic-focused optimization"},
    {"type": "body", "term": "search engine technology", "filename": "semantic-search-engine-process.jpg", "alt": "How semantic search engines process and understand content context"},
    {"type": "body", "term": "comparison chart", "filename": "traditional-vs-semantic-seo-comparison.jpg", "alt": "Comparison between traditional keyword SEO and modern semantic SEO approaches"},
    {"type": "body", "term": "growth graph", "filename": "semantic-seo-ranking-benefits.jpg", "alt": "Benefits of semantic SEO for search rankings and visibility"},
    {"type": "body", "term": "workflow diagram", "filename": "semantic-seo-optimization-process.jpg", "alt": "Step-by-step semantic SEO optimization process workflow"},
    {"type": "body", "term": "network diagram", "filename": "semantic-seo-components-entities.jpg", "alt": "Core components of semantic SEO including entity optimization and topic clusters"},
    {"type": "body", "term": "content structure", "filename": "topic-cluster-structure-seo.jpg", "alt": "Topic cluster structure showing pillar pages and cluster content interconnection"},
    {"type": "body", "term": "software interface", "filename": "semantic-seo-tools-software.jpg", "alt": "Essential semantic SEO tools and software for content optimization"},
    {"type": "body", "term": "performance metrics", "filename": "semantic-seo-case-study-results.jpg", "alt": "Semantic SEO case study results showing ranking improvements and traffic growth"},
    {"type": "body", "term": "strategy planning", "filename": "semantic-vs-traditional-seo-differences.jpg", "alt": "Key differences between semantic and traditional SEO approaches comparison"},
]

OUTPUT_DIR = Path("/Users/sheikhown/.openclaw/workspace/semantic-engine/images/downloads")
OUTPUT_DIR.mkdir(parents=True, exist_ok=True)

# Pexels free image URLs (curated from their free collection)
PEXELS_IMAGES = {
    "analytics dashboard": "https://images.pexels.com/photos/1181675/pexels-photo-1181675.jpeg?auto=compress&cs=tinysrgb&w=1200",
    "data visualization": "https://images.pexels.com/photos/590022/pexels-photo-590022.jpeg?auto=compress&cs=tinysrgb&w=1200",
    "search engine technology": "https://images.pexels.com/photos/5468231/pexels-photo-5468231.jpeg?auto=compress&cs=tinysrgb&w=1200",
    "comparison chart": "https://images.pexels.com/photos/669615/pexels-photo-669615.jpeg?auto=compress&cs=tinysrgb&w=1200",
    "growth graph": "https://images.pexels.com/photos/95922/pexels-photo-95922.jpeg?auto=compress&cs=tinysrgb&w=1200",
    "workflow diagram": "https://images.pexels.com/photos/590023/pexels-photo-590023.jpeg?auto=compress&cs=tinysrgb&w=1200",
    "network diagram": "https://images.pexels.com/photos/2092805/pexels-photo-2092805.jpeg?auto=compress&cs=tinysrgb&w=1200",
    "content structure": "https://images.pexels.com/photos/669611/pexels-photo-669611.jpeg?auto=compress&cs=tinysrgb&w=1200",
    "software interface": "https://images.pexels.com/photos/5468197/pexels-photo-5468197.jpeg?auto=compress&cs=tinysrgb&w=1200",
    "performance metrics": "https://images.pexels.com/photos/1181244/pexels-photo-1181244.jpeg?auto=compress&cs=tinysrgb&w=1200",
    "strategy planning": "https://images.pexels.com/photos/159711/books-reading-book-literature-paper-159711.jpeg?auto=compress&cs=tinysrgb&w=1200",
}

print(f"📥 Downloading {len(IMAGES)} images from Pexels")
print("=" * 70)

downloaded = []
failed = []

for i, img in enumerate(IMAGES, 1):
    print(f"[{i}/{len(IMAGES)}] {img['type'].upper()}: {img['term']}")
    print(f"  → {img['filename']}")
    
    # Get Pexels URL
    pexels_url = PEXELS_IMAGES.get(img['term'])
    if not pexels_url:
        # Fallback to first available
        pexels_url = list(PEXELS_IMAGES.values())[0]
        print(f"  ⚠️  Using fallback image")
    
    try:
        resp = requests.get(pexels_url, timeout=15)
        resp.raise_for_status()
        
        if len(resp.content) > 50000:  # At least 50KB
            filepath = OUTPUT_DIR / img["filename"]
            with open(filepath, "wb") as f:
                f.write(resp.content)
            
            size_kb = len(resp.content) / 1024
            print(f"  ✅ Downloaded: {size_kb:.1f} KB")
            downloaded.append({**img, "path": str(filepath), "size_kb": round(size_kb, 1), "source_url": pexels_url})
        else:
            print(f"  ❌ File too small: {len(resp.content)} bytes")
            failed.append(img['filename'])
            
    except Exception as e:
        print(f"  ❌ Error: {e}")
        failed.append(img['filename'])
    
    print()

print("=" * 70)
print(f"📊 Summary: {len(downloaded)}/{len(IMAGES)} images downloaded")
print(f"📁 Location: {OUTPUT_DIR}")

if failed:
    print(f"❌ Failed: {', '.join(failed)}")

if downloaded:
    print("\n📝 Files ready for WordPress upload:")
    for d in downloaded:
        print(f"  - {d['filename']} ({d['size_kb']} KB)")
        print(f"    Alt: {d['alt'][:70]}...")
