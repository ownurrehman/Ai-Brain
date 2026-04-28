#!/usr/bin/env python3
"""Download remaining failed images from Pexels"""

import requests
from pathlib import Path

# Failed images - using verified working Pexels URLs
REMAINING = [
    {"term": "data visualization", "filename": "semantic-seo-definition-concept.jpg", "alt": "Semantic SEO definition and concept visualization showing topic-focused optimization", "url": "https://images.pexels.com/photos/590022/pexels-photo-590022.jpeg?auto=compress&cs=tinysrgb&w=1200"},
    {"term": "growth graph", "filename": "semantic-seo-ranking-benefits.jpg", "alt": "Benefits of semantic SEO for search rankings and visibility", "url": "https://images.pexels.com/photos/95922/pexels-photo-95922.jpeg?auto=compress&cs=tinysrgb&w=1200"},
    {"term": "workflow diagram", "filename": "semantic-seo-optimization-process.jpg", "alt": "Step-by-step semantic SEO optimization process workflow", "url": "https://images.pexels.com/photos/590023/pexels-photo-590023.jpeg?auto=compress&cs=tinysrgb&w=1200"},
    {"term": "network diagram", "filename": "semantic-seo-components-entities.jpg", "alt": "Core components of semantic SEO including entity optimization and topic clusters", "url": "https://images.pexels.com/photos/2101856/pexels-photo-2101856.jpeg?auto=compress&cs=tinysrgb&w=1200"},
    {"term": "strategy planning", "filename": "semantic-vs-traditional-seo-differences.jpg", "alt": "Key differences between semantic and traditional SEO approaches comparison", "url": "https://images.pexels.com/photos/3183150/pexels-photo-3183150.jpeg?auto=compress&cs=tinysrgb&w=1200"},
]

OUTPUT_DIR = Path("/Users/sheikhown/.openclaw/workspace/semantic-engine/images/downloads")
OUTPUT_DIR.mkdir(parents=True, exist_ok=True)

print(f"📥 Downloading {len(REMAINING)} remaining images")
print("=" * 70)

downloaded = []

for i, img in enumerate(REMAINING, 1):
    print(f"[{i}/{len(REMAINING)}] {img['filename']}")
    
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
print(f"📊 Downloaded: {len(downloaded)}/{len(REMAINING)}")
