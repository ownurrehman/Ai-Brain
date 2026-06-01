#!/usr/bin/env python3
"""
Batch fix: Add "Milton" to Yoast SEO fields on TonicPhysio blog posts
"""
import json, base64, urllib.request, urllib.error, time

WP_URL = "https://tonicphysio.com"
USER = "Dan"
PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"
auth_str = base64.b64encode(f"{USER}:{PASS}".encode()).decode()

def api_get(path):
    req = urllib.request.Request(f"{WP_URL}/wp-json/wp/v2/{path}", headers={
        "Authorization": f"Basic {auth_str}",
        "Content-Type": "application/json"
    })
    with urllib.request.urlopen(req, timeout=30) as resp:
        return json.loads(resp.read().decode())

def api_put(path, data):
    req = urllib.request.Request(
        f"{WP_URL}/wp-json/wp/v2/{path}",
        data=json.dumps(data).encode(),
        headers={
            "Authorization": f"Basic {auth_str}",
            "Content-Type": "application/json"
        },
        method="PUT"
    )
    with urllib.request.urlopen(req, timeout=30) as resp:
        return json.loads(resp.read().decode())

def smart_add_milton(text, is_title=False):
    """Add Milton naturally to text without duplicating"""
    if not text:
        return text
    lower = text.lower()
    if 'milton' in lower:
        return text
    if is_title:
        # Append | Tonic Physio in Milton or in Milton
        if 'Tonic Physio' in text:
            return text.replace('Tonic Physio', 'Tonic Physio in Milton')
        elif '|' in text:
            return text + ' in Milton'
        else:
            return text + ' in Milton'
    else:
        # Description - add naturally
        if text.endswith('.'):
            return text[:-1] + ' in Milton.'
        else:
            return text + ' in Milton'

def smart_kw_add_milton(kw):
    """Add Milton to keyword string"""
    if not kw:
        return 'Milton physiotherapy'
    lower = kw.lower()
    if 'milton' in lower:
        return kw
    return kw + ', Milton'

# Get all posts
print("Fetching all published posts...")
posts = api_get("posts?per_page=100&status=publish")
print(f"Found {len(posts)} posts\n")

fixed = []
skipped = []
errors = []

for p in posts:
    pid = p['id']
    title = p['title']['rendered']
    slug = p['slug']
    meta = p.get('meta', {})
    
    yoast_title = meta.get('_yoast_wpseo_title', '') or ''
    yoast_desc = meta.get('_yoast_wpseo_metadesc', '') or ''
    yoast_kw = meta.get('_yoast_wpseo_focuskw', '') or ''
    
    combined = f"{yoast_title} {yoast_kw} {slug}".lower()
    
    if 'milton' in combined:
        skipped.append(f"ID {pid}: {title[:50]}... (already has Milton)")
        continue
    
    # Build updates
    new_title = smart_add_milton(yoast_title, is_title=True)
    new_desc = smart_add_milton(yoast_desc, is_title=False)
    new_kw = smart_kw_add_milton(yoast_kw)
    
    # Safety: keep under limits
    if len(new_title) > 60:
        new_title = new_title[:57] + '...'
    if len(new_desc) > 155:
        new_desc = new_desc[:152] + '...'
    
    update_data = {
        "meta": {
            "_yoast_wpseo_title": new_title,
            "_yoast_wpseo_metadesc": new_desc,
            "_yoast_wpseo_focuskw": new_kw
        }
    }
    
    try:
        result = api_put(f"posts/{pid}", update_data)
        if 'code' in result:
            errors.append(f"ID {pid}: {result.get('message', 'Unknown error')}")
        else:
            fixed.append(f"ID {pid}: {title[:50]}...")
            print(f"  Fixed: {title[:55]}... | Title: {new_title[:50]} | KW: {new_kw}")
    except Exception as e:
        errors.append(f"ID {pid}: {str(e)}")
    
    time.sleep(0.3)  # Rate limit

print(f"\n{'='*60}")
print(f"Fixed: {len(fixed)}")
print(f"Skipped (already has Milton): {len(skipped)}")
print(f"Errors: {len(errors)}")

if errors:
    print("\nErrors:")
    for e in errors[:10]:
        print(f"  {e}")
