#!/usr/bin/env python3
"""
publish-tonicphysio-blogs.py — Unified WordPress Blog Publisher for Tonic Physio
Consolidates publish-tonicphysio-blogs.py, push_posts.py, and push_blogs.sh.
Supports both Markdown (.md) and HTML (.html) source files, auto-injects internal links,
and uploads them directly to WordPress as DRAFT.

Usage:
  python3 publish-tonicphysio-blogs.py --list
  python3 publish-tonicphysio-blogs.py --all
  python3 publish-tonicphysio-blogs.py --id [1-12]
"""

import json
import base64
import urllib.request
import urllib.error
import re
import os
import sys
import argparse
from pathlib import Path

# Paths
SITE_ROOT = Path(__file__).resolve().parent.parent

# Credentials
WP_URL = "https://tonicphysio.com"
USER = "Dan"
PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"
auth_str = base64.b64encode(f"{USER}:{PASS}".encode()).decode()

# Service page slugs for natural internal linking
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

def read_file(file_name):
    path = SITE_ROOT / file_name
    if not path.exists():
        print(f"❌ ERROR: File not found: {path}")
        return None
    with open(path, 'r', encoding='utf-8') as f:
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

def create_post(title, slug, html_content, yoast_title, yoast_desc, yoast_kw):
    data = {
        "title": title,
        "slug": slug,
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

# Master Blog Catalog
BLOGS = {
    1: {
        "type": "markdown",
        "file": "tonicphysio-blog-01-custom-bracing-fitting.md",
        "slug": "custom-bracing-fitting-milton",
        "title": "What to Expect During Custom Bracing Fitting in Milton",
        "yoast_title": "What to Expect During Custom Bracing Fitting in Milton | Tonic Physio",
        "yoast_desc": "Learn what happens during a custom bracing fitting at Tonic Physio in Milton. Step-by-step guide to support, comfort, and recovery.",
        "yoast_kw": "custom bracing fitting milton"
    },
    2: {
        "type": "markdown",
        "file": "tonicphysio-blog-02-custom-vs-otc-bracing.md",
        "slug": "custom-vs-otc-bracing-milton",
        "title": "Custom vs OTC Bracing in Milton: Which Do You Need",
        "yoast_title": "Custom vs OTC Bracing in Milton: Which Do You Need | Tonic Physio",
        "yoast_desc": "Not sure if you need custom or OTC bracing? Tonic Physio in Milton explains when each option makes sense for your recovery.",
        "yoast_kw": "custom vs otc bracing milton"
    },
    3: {
        "type": "markdown",
        "file": "tonicphysio-blog-03-mva-recovery-timeline.md",
        "slug": "car-accident-recovery-timeline-milton",
        "title": "Car Accident Recovery Timeline in Milton",
        "yoast_title": "Car Accident Recovery Timeline in Milton | Tonic Physio",
        "yoast_desc": "Week-by-week car accident recovery timeline with physiotherapy at Tonic Physio in Milton. Know what to expect after a collision.",
        "yoast_kw": "car accident recovery timeline milton"
    },
    4: {
        "type": "markdown",
        "file": "tonicphysio-blog-04-what-to-do-after-car-accident.md",
        "slug": "what-to-do-after-car-accident-milton",
        "title": "What to Do After a Car Accident in Milton",
        "yoast_title": "What to Do After a Car Accident in Milton | Tonic Physio",
        "yoast_desc": "Steps to take after a car accident in Milton. From medical care to physiotherapy recovery at Tonic Physio. Protect your health.",
        "yoast_kw": "what to do after car accident milton"
    },
    5: {
        "type": "markdown",
        "file": "tonicphysio-blog-05-wsib-claims-process.md",
        "slug": "wsib-claims-process-milton",
        "title": "WSIB Claims Process in Milton: How Physiotherapy Helps",
        "yoast_title": "WSIB Claims Process in Milton: How Physiotherapy Helps | Tonic Physio",
        "yoast_desc": "Navigate the WSIB claims process in Milton with Tonic Physio. Expert help for workplace injury recovery and return to work.",
        "yoast_kw": "wsib claims process milton"
    },
    6: {
        "type": "markdown",
        "file": "tonicphysio-blog-07-acupuncture-vs-dry-needling.md",
        "slug": "acupuncture-vs-dry-needling-milton",
        "title": "Acupuncture vs Dry Needling in Milton: Key Differences",
        "yoast_title": "Acupuncture vs Dry Needling in Milton: Key Differences | Tonic Physio",
        "yoast_desc": "Confused about acupuncture vs dry needling? Tonic Physio in Milton explains the differences and which treatment fits your needs.",
        "yoast_kw": "acupuncture vs dry needling milton"
    },
    7: {
        "type": "html",
        "file": "blog2-cervical-spondylosis.html",
        "slug": "cervical-spondylosis-exercises-neck-relief-milton",
        "title": "Cervical Spondylosis Exercises for Neck Relief in Milton",
        "yoast_title": "Cervical Spondylosis Exercises for Neck Relief in Milton | Tonic Physio",
        "yoast_desc": "Gentle exercises for cervical spondylosis in Milton. Reduce neck pain with physiotherapy guidance at Tonic Physio.",
        "yoast_kw": "cervical spondylosis exercises milton"
    },
    8: {
        "type": "html",
        "file": "blog3-orthopedic-vs-regular.html",
        "slug": "orthopedic-physiotherapy-vs-regular-physiotherapy",
        "title": "Orthopedic Physiotherapy vs Regular Physiotherapy: What's the Difference",
        "yoast_title": "Orthopedic Physiotherapy vs Regular Physiotherapy | Tonic Physio",
        "yoast_desc": "Discover the difference between orthopedic and regular physiotherapy. Expert care at Tonic Physio in Milton.",
        "yoast_kw": "orthopedic physiotherapy vs regular"
    },
    9: {
        "type": "html",
        "file": "blog4-pediatric-physiotherapy.html",
        "slug": "pediatric-physiotherapy-when-your-child-needs-help-milton",
        "title": "Pediatric Physiotherapy: When Your Child Needs Help in Milton",
        "yoast_title": "Pediatric Physiotherapy Milton | When Your Child Needs Help",
        "yoast_desc": "Pediatric physiotherapy in Milton for children. Gentle, evidence-based care at Tonic Physio.",
        "yoast_kw": "pediatric physiotherapy milton"
    },
    10: {
        "type": "html",
        "file": "blog5_ra_physiotherapy.html",
        "slug": "rheumatoid-arthritis-physiotherapy-milton",
        "title": "Rheumatoid Arthritis and Physiotherapy Management in Milton",
        "yoast_title": "Rheumatoid Arthritis Physiotherapy Management Milton | Tonic Physio",
        "yoast_desc": "Expert rheumatoid arthritis physiotherapy in Milton. Reduce joint pain with personalized care at Tonic Physio.",
        "yoast_kw": "rheumatoid arthritis physiotherapy milton"
    },
    11: {
        "type": "html",
        "file": "blog6_deep_tissue_athletes.html",
        "slug": "deep-tissue-massage-benefits-athletes-milton",
        "title": "Deep Tissue Massage Benefits for Athletes in Milton",
        "yoast_title": "Deep Tissue Massage Benefits for Athletes in Milton | Tonic Physio",
        "yoast_desc": "Deep tissue massage for athletes in Milton. Speed recovery and prevent injury at Tonic Physio.",
        "yoast_kw": "deep tissue massage athletes milton"
    },
    12: {
        "type": "html",
        "file": "blog7_hot_stone_vs_swedish.html",
        "slug": "hot-stone-massage-vs-swedish-milton",
        "title": "Hot Stone Massage vs Swedish Massage: Which is Right for You",
        "yoast_title": "Hot Stone Massage vs Swedish Massage Milton | Tonic Physio",
        "yoast_desc": "Hot stone vs Swedish massage in Milton. Find the right therapy for you at Tonic Physio.",
        "yoast_kw": "hot stone vs swedish massage"
    }
}

def publish_blog(blog_id, details):
    file_content = read_file(details["file"])
    if not file_content:
        return False
        
    print(f"Publishing post {blog_id}: '{details['title']}' from {details['file']} ({details['type']})...")
    
    # Process content
    if details["type"] == "markdown":
        title = extract_title(file_content)
        html_content = md_to_html(file_content)
    else:
        title = details["title"]
        html_content = file_content
        
    html_content = add_internal_links(html_content)
    
    try:
        res = create_post(
            title=title,
            slug=details["slug"],
            html_content=html_content,
            yoast_title=details["yoast_title"],
            yoast_desc=details["yoast_desc"],
            yoast_kw=details.get("yoast_kw", "")
        )
        if "id" in res:
            print(f"  ✅ SUCCESS: Created draft ID: {res['id']}")
            print(f"  Link: {res.get('link')}\n")
            return True
        else:
            print(f"  ❌ FAILED: {res.get('message', 'Unknown error')}\n")
            return False
    except Exception as e:
        print(f"  ❌ EXCEPTION: {e}\n")
        return False

def main():
    parser = argparse.ArgumentParser(description="Unified WordPress Blog Publisher for Tonic Physio")
    group = parser.add_mutually_exclusive_group(required=True)
    group.add_argument("--list", action="store_true", help="List all available blog posts")
    group.add_argument("--all", action="store_true", help="Publish all blog posts")
    group.add_argument("--id", type=int, choices=range(1, 13), help="Publish a specific blog post ID")
    
    args = parser.parse_args()
    
    if args.list:
        print("=== AVAILABLE BLOG POSTS ===")
        for bid, d in BLOGS.items():
            print(f"[{bid:2d}] {d['title']}")
            print(f"     Source: {d['file']} ({d['type']}) | Slug: {d['slug']}\n")
        return
        
    if args.id:
        publish_blog(args.id, BLOGS[args.id])
        return
        
    if args.all:
        success_count = 0
        for bid, d in BLOGS.items():
            if publish_blog(bid, d):
                success_count += 1
        print(f"Completed! Successfully published {success_count} of {len(BLOGS)} blogs.")

if __name__ == "__main__":
    main()
