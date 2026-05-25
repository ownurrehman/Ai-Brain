#!/usr/bin/env python3
"""
Push pre-generated ACF content to RankRay service pages.
Uses chunked payloads (5 fields per request) to avoid server size limits.
"""
import json, requests, base64, time

USER = "openclaw"
PASS = "6Zz9 5gJL 8uyA QH4g RQDH GV1j"
AUTH = base64.b64encode(f"{USER}:{PASS}".encode()).decode()
HEADERS = {"Authorization": f"Basic {AUTH}", "Content-Type": "application/json"}
WP = "https://rankray.com/wp-json/wp/v2/"

# Pages to update
PAGES = {
    13348: {
        "name": "Enterprise SEO Audit",
        "file": "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/rankray/acf-content/enterprise-seo-audit-acf.json",
        "focuskw": "enterprise seo audit",
        "yoast_title": "Enterprise SEO Audit | Scale Your Enterprise with Rank Ray",
        "yoast_desc": "We help large enterprises identify core search engine ranking gaps through our enterprise SEO audit. We only focus on what moves the needle. | Rank Ray"
    },
    11366: {
        "name": "Digital Marketing Strategy",
        "file": "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/rankray/acf-content/digital-marketing-strategy-acf.json",
        "focuskw": "digital marketing strategy",
        "yoast_title": "Digital Marketing Strategy | Data-Driven Growth | Rank Ray",
        "yoast_desc": "Data-driven digital marketing strategies that align with your business goals. Rank Ray creates custom roadmaps for sustainable growth. | Rank Ray"
    },
    15037: {
        "name": "Custom Website Design",
        "file": "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/rankray/acf-content/custom-website-design-acf.json",
        "focuskw": "custom website design",
        "yoast_title": "Custom Website Design | Bespoke Web Solutions | Rank Ray",
        "yoast_desc": "Bespoke custom website design services that convert visitors into customers. Rank Ray builds responsive, high-performance websites. | Rank Ray"
    }
}

CHUNK_SIZE = 5
SLEEP = 2

def push_chunk(page_id, chunk, chunk_num):
    """Push one chunk of ACF fields."""
    payload = {"acf": chunk}
    resp = requests.post(f"{WP}pages/{page_id}", headers=HEADERS, json=payload, timeout=30)
    if resp.status_code == 200:
        print(f"  Chunk {chunk_num}: OK ({len(chunk)} fields)")
        return True
    else:
        print(f"  Chunk {chunk_num}: FAILED {resp.status_code}")
        print(f"    Error: {resp.text[:200]}")
        return False

def push_yoast(page_id, title, desc, kw):
    payload = {
        "meta": {
            "_yoast_wpseo_title": title,
            "_yoast_wpseo_metadesc": desc,
            "_yoast_wpseo_focuskw": kw
        }
    }
    resp = requests.post(f"{WP}pages/{page_id}", headers=HEADERS, json=payload, timeout=30)
    if resp.status_code == 200:
        print(f"  Yoast: OK (title={len(title)}c, desc={len(desc)}c)")
        return True
    else:
        print(f"  Yoast: FAILED {resp.status_code}")
        return False

def process_page(page_id, config):
    print(f"\n{'='*60}")
    print(f"Processing: {config['name']} (ID {page_id})")
    print(f"{'='*60}")
    
    # Load content
    with open(config['file']) as f:
        data = json.load(f)
    
    fields = data.get('fields', data) if isinstance(data, dict) else {}
    
    # Skip non-text fields (image IDs etc.)
    text_fields = {k:v for k,v in fields.items() if isinstance(v, str)}
    print(f"Text fields to push: {len(text_fields)}")
    
    # Build chunks
    items = list(text_fields.items())
    total_chunks = (len(items) + CHUNK_SIZE - 1) // CHUNK_SIZE
    print(f"Chunks: {total_chunks} (max {CHUNK_SIZE} fields each)")
    
    # Push chunks
    success = 0
    for i in range(0, len(items), CHUNK_SIZE):
        chunk = dict(items[i:i+CHUNK_SIZE])
        if push_chunk(page_id, chunk, i//CHUNK_SIZE + 1):
            success += len(chunk)
        else:
            print(f"  Stopping - chunk failed")
            break
        time.sleep(SLEEP)
    
    print(f"  Fields pushed: {success}/{len(text_fields)}")
    
    # Push Yoast
    if push_yoast(page_id, config['yoast_title'], config['yoast_desc'], config['focuskw']):
        print(f"  Yoast updated: ✓")
    
    return success == len(text_fields)

if __name__ == "__main__":
    print("RankRay Service Page ACF Push — Chunked Mode")
    print(f"Chunk size: {CHUNK_SIZE} | Sleep: {SLEEP}s per call")
    
    all_ok = True
    for page_id, config in PAGES.items():
        ok = process_page(page_id, config)
        if not ok:
            all_ok = False
        time.sleep(3)
    
    print(f"\n{'='*60}")
    print(f"Complete: {'All pages pushed OK' if all_ok else 'Some pages had failures'}")
    print(f"{'='*60}")
