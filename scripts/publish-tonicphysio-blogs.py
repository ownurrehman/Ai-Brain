#!/usr/bin/env python3
"""
Publish TonicPhysio blog drafts to WordPress as DRAFT
Simple markdown to HTML (no external deps)
"""
import json, base64, urllib.request, urllib.error, re, os

WP_URL = "https://tonicphysio.com"
USER = "Dan"
PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"
auth_str = base64.b64encode(f"{USER}:{PASS}".encode()).decode()

# Service page slugs for internal linking
SERVICE_LINKS = [
    ("custom bracing", "https://tonicphysio.com/custom-and-otc-bracing/"),
    ("otc bracing", "https://tonicphysio.com/custom-and-otc-bracing/"),
    ("bracing", "https://tonicphysio.com/custom-and-otc-bracing/"),
    ("motor vehicle accident", "https://tonicphysio.com/motor-vehicle-accident-physiotherapy/"),
    ("car accident", "https://tonicphysio.com/motor-vehicle-accident-physiotherapy/"),
    ("mva", "https://tonicphysio.com/motor-vehicle-accident-physiotherapy/"),
    ("wsib", "https://tonicphysio.com/wsib-care-programs/"),
    ("workplace injury", "https://tonicphysio.com/wsib-care-programs/"),
    ("work injury", "https://tonicphysio.com/wsib-care-programs/"),
    ("acupuncture", "https://tonicphysio.com/physiotherapy-in-milton/acupuncture-therapy/"),
    ("dry needling", "https://tonicphysio.com/physiotherapy-in-milton/dry-needling/"),
    ("physiotherapy in milton", "https://tonicphysio.com/physiotherapy-in-milton/"),
    ("tonic physio", "https://tonicphysio.com/physiotherapy-in-milton/"),
]

def read_markdown(path):
    with open(path, 'r') as f:
        return f.read()

def md_to_html(md_text):
    """Simple markdown to HTML converter"""
    lines = md_text.split('\n')
    html_lines = []
    in_para = False
    
    for line in lines:
        stripped = line.strip()
        if not stripped:
            if in_para:
                html_lines.append('</p>')
                in_para = False
            continue
        
        # Headers
        if stripped.startswith('# '):
            if in_para:
                html_lines.append('</p>')
                in_para = False
            text = stripped[2:]
            html_lines.append(f'<h1>{text}</h1>')
        elif stripped.startswith('## '):
            if in_para:
                html_lines.append('</p>')
                in_para = False
            text = stripped[3:]
            html_lines.append(f'<h2>{text}</h2>')
        elif stripped.startswith('### '):
            if in_para:
                html_lines.append('</p>')
                in_para = False
            text = stripped[4:]
            html_lines.append(f'<h3>{text}</h3>')
        elif stripped.startswith('- ') or stripped.startswith('* '):
            if in_para:
                html_lines.append('</p>')
                in_para = False
            text = stripped[2:]
            # Handle bold
            text = re.sub(r'\*\*(.+?)\*\*', r'<strong>\1</strong>', text)
            html_lines.append(f'<li>{text}</li>')
        else:
            # Paragraph text
            if not in_para:
                html_lines.append('<p>')
                in_para = True
            # Handle bold
            text = re.sub(r'\*\*(.+?)\*\*', r'<strong>\1</strong>', stripped)
            html_lines.append(text + ' ')
    
    if in_para:
        html_lines.append('</p>')
    
    return ' '.join(html_lines)

def add_internal_links(html, used_links=None):
    """Add internal links naturally, max 3 per post"""
    if used_links is None:
        used_links = set()
    
    count = 0
    for phrase, url in SERVICE_LINKS:
        if url in used_links or count >= 3:
            continue
        # Find first occurrence (case insensitive, not inside a tag)
        pattern = re.compile(rf'(?!<[^>]*)(\b{re.escape(phrase)}\b)', re.IGNORECASE)
        match = pattern.search(html)
        if match:
            start, end = match.span()
            html = html[:start] + f'<a href="{url}">{match.group(1)}</a>' + html[end:]
            used_links.add(url)
            count += 1
    return html

def extract_title(md_text):
    lines = md_text.split('\n')
    for line in lines:
        if line.startswith('# '):
            return line.replace('# ', '').strip()
        if line.startswith('## '):
            return line.replace('## ', '').strip()
    return "Tonic Physio Blog Post"

def create_post(title, html_content, yoast_title, yoast_desc, yoast_kw):
    data = {
        "title": title,
        "content": html_content,
        "status": "draft",
        "meta": {
            "_yoast_wpseo_title": yoast_title,
            "_yoast_wpseo_metadesc": yoast_desc,
            "_yoast_wpseo_focuskw": yoast_kw
        }
    }
    
    req = urllib.request.Request(
        f"{WP_URL}/wp-json/wp/v2/posts",
        data=json.dumps(data).encode(),
        headers={
            "Authorization": f"Basic {auth_str}",
            "Content-Type": "application/json"
        }
    )
    with urllib.request.urlopen(req, timeout=60) as resp:
        return json.loads(resp.read().decode())

# Blog files to publish
BLOGS = [
    {
        "file": "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/tonicphysio/tonicphysio-blog-01-custom-bracing-fitting.md",
        "yoast_title": "What to Expect During Custom Bracing Fitting in Milton | Tonic Physio",
        "yoast_desc": "Learn what happens during a custom bracing fitting at Tonic Physio in Milton. Step-by-step guide to support, comfort, and recovery.",
        "yoast_kw": "custom bracing fitting milton"
    },
    {
        "file": "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/tonicphysio/tonicphysio-blog-02-custom-vs-otc-bracing.md",
        "yoast_title": "Custom vs OTC Bracing in Milton: Which Do You Need | Tonic Physio",
        "yoast_desc": "Not sure if you need custom or OTC bracing? Tonic Physio in Milton explains when each option makes sense for your recovery.",
        "yoast_kw": "custom vs otc bracing milton"
    },
    {
        "file": "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/tonicphysio/tonicphysio-blog-03-mva-recovery-timeline.md",
        "yoast_title": "Car Accident Recovery Timeline in Milton | Tonic Physio",
        "yoast_desc": "Week-by-week car accident recovery timeline with physiotherapy at Tonic Physio in Milton. Know what to expect after a collision.",
        "yoast_kw": "car accident recovery timeline milton"
    },
    {
        "file": "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/tonicphysio/tonicphysio-blog-04-what-to-do-after-car-accident.md",
        "yoast_title": "What to Do After a Car Accident in Milton | Tonic Physio",
        "yoast_desc": "Steps to take after a car accident in Milton. From medical care to physiotherapy recovery at Tonic Physio. Protect your health.",
        "yoast_kw": "what to do after car accident milton"
    },
    {
        "file": "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/tonicphysio/tonicphysio-blog-05-wsib-claims-process.md",
        "yoast_title": "WSIB Claims Process in Milton: How Physiotherapy Helps | Tonic Physio",
        "yoast_desc": "Navigate the WSIB claims process in Milton with Tonic Physio. Expert help for workplace injury recovery and return to work.",
        "yoast_kw": "wsib claims process milton"
    },
    {
        "file": "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/tonicphysio/tonicphysio-blog-07-acupuncture-vs-dry-needling.md",
        "yoast_title": "Acupuncture vs Dry Needling in Milton: Key Differences | Tonic Physio",
        "yoast_desc": "Confused about acupuncture vs dry needling? Tonic Physio in Milton explains the differences and which treatment fits your needs.",
        "yoast_kw": "acupuncture vs dry needling milton"
    }
]

results = []
for blog in BLOGS:
    fname = os.path.basename(blog["file"])
    print(f"Publishing: {fname}...")
    
    md = read_markdown(blog["file"])
    title = extract_title(md)
    html = md_to_html(md)
    html = add_internal_links(html)
    
    try:
        result = create_post(
            title=title,
            html_content=html,
            yoast_title=blog["yoast_title"],
            yoast_desc=blog["yoast_desc"],
            yoast_kw=blog["yoast_kw"]
        )
        if "id" in result:
            results.append(f"✅ {fname} → ID: {result['id']} — DRAFT")
            print(f"  ✅ ID {result['id']} created as DRAFT")
        else:
            results.append(f"❌ {fname} → Error: {result.get('message', 'Unknown')}")
            print(f"  ❌ Error: {result.get('message', 'Unknown')}")
    except Exception as e:
        results.append(f"❌ {fname} → {str(e)}")
        print(f"  ❌ Exception: {e}")

print(f"\n{'='*60}")
print(f"Published {len([r for r in results if '✅' in r])} of {len(BLOGS)} blogs")
for r in results:
    print(r)
