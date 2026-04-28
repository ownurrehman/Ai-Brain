#!/usr/bin/env python3
"""Download final 3 failed images from alternative sources"""

import requests
from pathlib import Path

# Final failed images - using Pixabay (truly free, no attribution required)
FINAL = [
    {"filename": "semantic-seo-definition-concept.jpg", "alt": "Semantic SEO definition and concept visualization showing topic-focused optimization", "url": "https://cdn.pixabay.com/photo/2016/11/29/05/45/astronomy-1867616_1280.jpg"},
    {"filename": "semantic-seo-ranking-benefits.jpg", "alt": "Benefits of semantic SEO for search rankings and visibility", "url": "https://cdn.pixabay.com/photo/2016/11/19/14/00/chart-1839390_1280.jpg"},
    {"filename": "semantic-seo-optimization-process.jpg", "alt": "Step-by-step semantic SEO optimization process workflow", "url": "https://cdn.pixabay.com/photo/2016/11/29/13/28/laptop-1869622_1280.jpg"},
]

OUTPUT_DIR = Path("/Users/sheikhown/.openclaw/workspace/semantic-engine/images/downloads")
OUTPUT_DIR.mkdir(parents=True, exist_ok=True)

print(f"📥 Downloading final {len(FINAL)} images from Pixabay")
print("=" * 70)

downloaded = []

for i, img in enumerate(FINAL, 1):
    print(f"[{i}/{len(FINAL)}] {img['filename']}")
    
    try:
        resp = requests.get(img['url'], timeout=15)
        resp.raise_for_status()
        
        if len(resp.content) > 50000:
            filepath = OUTPUT_DIR / img["filename"]
            with open(filepath, "wb") as f:
                f.write(resp.content)
            
            size_kb = len(resp.content) / 1024
            print(f"  ✅ Downloaded: {size_kb:.1f} KB")
            downloaded.append({**img, "path": str(filepath), "size_kb": round(size_kb, 1)})
        else:
            print(f"  ❌ File too small: {len(resp.content)} bytes")
            
    except Exception as e:
        print(f"  ❌ Error: {e}")
    
    print()

print("=" * 70)
print(f"📊 Downloaded: {len(downloaded)}/{len(FINAL)}")
