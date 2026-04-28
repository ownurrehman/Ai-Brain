import urllib.request, urllib.parse, json, re, os, ssl
ssl._create_default_https_context = ssl._create_unverified_context

# Try to scrape free stock images from various sources
# Pixabay allows direct image access without API for embedding

# Search for high-quality physiotherapy images
sources = [
    ("physical therapy patient knee exercise", "physiotherapy"),
    ("orthopedic rehabilitation exercise", "orthopedic"),
    ("joint mobilization therapy hands", "orthopedic"),
    ("child physical therapy pediatric", "pediatric"),
    ("kids physiotherapy exercise", "pediatric"),
]

for query, label in sources:
    encoded = urllib.parse.quote(query)
    
    # Try pixabay direct search
    url = f"https://pixabay.com/photos/search/{encoded}/?order=ec"
    try:
        req = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
        with urllib.request.urlopen(req, timeout=10) as resp:
            html = resp.read().decode('utf-8', errors='replace')
            
        # Extract image URLs from srcset
        imgs = re.findall(r'https://pixabay\.com/get/[^"\']+', html)[:5]
        
        # Extract alt text
        alts = re.findall(r'alt="([^"]*)"', html)[:10]
        
        print(f"\n=== {label}: {query} ===")
        for i, (src, alt) in enumerate(zip(imgs[:3], alts[:3])):
            print(f"  [{i}] {alt[:80]}")
            print(f"      {src[:120]}")
    except Exception as e:
        print(f"  Error: {e}")
