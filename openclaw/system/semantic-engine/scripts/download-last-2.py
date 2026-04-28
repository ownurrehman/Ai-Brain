#!/usr/bin/env python3
"""Download final 2 images from alternative source"""

import requests
from pathlib import Path

FINAL = [
    {"filename": "semantic-seo-ranking-benefits.jpg", "alt": "Benefits of semantic SEO for search rankings and visibility", "url": "https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200&auto=format&fit=crop"},
    {"filename": "semantic-seo-optimization-process.jpg", "alt": "Step-by-step semantic SEO optimization process workflow", "url": "https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200&auto=format&fit=crop"},
]

OUTPUT_DIR = Path("/Users/sheikhown/.openclaw/workspace/semantic-engine/images/downloads")

print("📥 Downloading final 2 images from Unsplash")
print("=" * 70)

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
        else:
            print(f"  ❌ File too small")
            
    except Exception as e:
        print(f"  ❌ Error: {e}")
    
    print()

print("Done!")
