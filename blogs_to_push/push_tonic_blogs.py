#!/usr/bin/env python3
"""
Tonic Physio Blog Batch Pusher
Pushes all locally written blogs to WordPress as drafts
Usage: python tonicphysio_blog_pusher.py
"""
import os, json, requests, re, time, sys

WP_URL = "https://tonicphysio.com/wp-json/wp/v2"
WP_USER = "openclaw"
WP_PASS = "6Zz9 5gJL 8uyA QH4g RQDH GV1j"

AUTH = requests.auth.HTTPBasicAuth(WP_USER, WP_PASS)
BLOG_PATHS = [f"/Users/sheikhown/blogs_to_push/tonicphysio-blog-{i:02d}-*.md" for i in range(1, 43)]

HEADERS = {"Content-Type": "application/json"}
TAGS = ["milton-physio", "tonic-physio", "physiotherapy"]

# Get category: blog = 2
CATEGORY_BLOG = 2

def extract_title(content):
    """Extract title from markdown H1"""
    match = re.search(r'^#\s+(.+)$', content, re.MULTILINE)
    return match.group(1).strip() if match else "Untitled"

def extract_excerpt(content):
    """Extract first 2 paragraphs as excerpt"""
    paras = [p.strip() for p in content.split('\n\n') if p.strip() and not p.strip().startswith('#') and not p.strip().startswith('<')]
    if len(paras) >= 2:
        return paras[0] + ' ' + paras[1]
    elif paras:
        return paras[0]
    return ""

def generate_slug(title):
    """Generate WP slug from title"""
    slug = re.sub(r'[^a-zA-Z0-9\s-]', '', title.lower())
    slug = re.sub(r'\s+', '-', slug.strip())
    return slug[:60]

def push_blog(filepath):
    """Push a single blog to WP as draft"""
    with open(filepath, 'r') as f:
        content = f.read()
    
    title = extract_title(content)
    excerpt = extract_excerpt(content)
    slug = generate_slug(title)
    
    payload = {
        "title": title,
        "content": content,
        "excerpt": excerpt[:200],
        "status": "draft",
        "categories": [CATEGORY_BLOG],
        "tags": TAGS,
        "slug": slug
    }
    
    print(f"Pushing: {title}")
    
    try:
        resp = requests.post(
            f"{WP_URL}/posts",
            auth=AUTH,
            headers=HEADERS,
            json=payload,
            timeout=30
        )
        if resp.status_code == 201:
            data = resp.json()
            print(f"  SUCCESS: ID={data['id']}, Link={data['link']}")
            return True
        else:
            print(f"  FAILED: {resp.status_code} - {resp.text[:200]}")
            return False
    except Exception as e:
        print(f"  ERROR: {str(e)[:200]}")
        return False

def main():
    import glob
    
    files = sorted(glob.glob("/Users/sheikhown/blogs_to_push/tonicphysio-blog-*.md"))
    total = len(files)
    
    print(f"Found {total} blogs to push.")
    print("=" * 60)
    
    success = 0
    failed = 0
    
    for filepath in files:
        result = push_blog(filepath)
        if result:
            success += 1
        else:
            failed += 1
        
        # Rate limiting: 5 sec between requests
        time.sleep(5)
    
    print("=" * 60)
    print(f"RESULTS: {success} pushed, {failed} failed, {total} total")

if __name__ == "__main__":
    main()
