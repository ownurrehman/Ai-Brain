#!/usr/bin/env python3
"""
Push all TonicPhysio blogs to WordPress via REST API.
Username: Dan (admin)
App Pass: NMwZ 1LyJ YgbE fUjs pUYn 4SoZ
"""
import requests, re, os, glob, json, time

WP_URL = "https://tonicphysio.com/wp-json/wp/v2/posts"
WP_USER = "Dan"
WP_PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"
BLOG_DIR = "/Users/sheikhown/blogs_to_push"
headers = {"User-Agent": "Mozilla/5.0", "Content-Type": "application/json", "Accept": "application/json"}

def md_to_html(md_text):
    """Convert markdown to basic HTML."""
    lines_in = md_text.splitlines()
    # Remove internal header
    if lines_in and lines_in[0].startswith("# Tonic Physio Blog Content"):
        lines_in = lines_in[1:]
    
    # Skip first real heading (it's the WP title)
    content_lines = []
    skipped_heading = False
    for line in lines_in:
        if not skipped_heading and line.strip().startswith(("## ", "# ")):
            skipped_heading = True
            continue
        content_lines.append(line)
    
    # Convert
    blocks = "\n".join(content_lines)
    blocks = re.sub(r'^#### (.+)$', r'<h4>\1</h4>', blocks, flags=re.MULTILINE)
    blocks = re.sub(r'^### (.+)$', r'<h3>\1</h3>', blocks, flags=re.MULTILINE)
    blocks = re.sub(r'^## (.+)$', r'<h2>\1</h2>', blocks, flags=re.MULTILINE)
    blocks = re.sub(r'\*\*(.+?)\*\*', r'<strong>\1</strong>', blocks)
    blocks = re.sub(r'\*(.+?)\*', r'<em>\1</em>', blocks)
    blocks = re.sub(r'\[(.+?)\]\((.+?)\)', r'<a href="\2" target="_blank" rel="noopener noreferrer">\1</a>', blocks)
    
    # Lists and paragraphs
    lines_out = []
    in_list = False
    for line in blocks.splitlines():
        stripped = line.lstrip()
        if stripped.startswith('- ') or stripped.startswith('* '):
            item = stripped[2:].strip()
            if not in_list:
                lines_out.append('<ul>')
                in_list = True
            lines_out.append(f'<li>{item}</li>')
        else:
            if in_list and stripped:
                lines_out.append('</ul>')
                in_list = False
            if line.strip():
                if line.strip().startswith('<'):
                    lines_out.append(line.strip())
                else:
                    lines_out.append(f'<p>{line.strip()}</p>')
    if in_list:
        lines_out.append('</ul>')
    return '\n'.join(lines_out)

def parse_blog(filepath):
    with open(filepath, "r", encoding="utf-8") as f:
        md = f.read()
    
    lines = md.splitlines()
    
    # Find WP Title
    wp_title = ""
    title_idx = 0
    for i, line in enumerate(lines):
        stripped = line.strip()
        if stripped.startswith("## "):
            wp_title = stripped.replace("## ", "").strip()
            title_idx = i
            break
        elif stripped.startswith("# ") and not stripped.startswith("# Tonic"):
            wp_title = stripped.replace("# ", "").strip()
            title_idx = i
            break
    
    # Slug from filename
    slug = os.path.basename(filepath).replace("tonicphysio-blog-", "").replace(".md", "")
    
    # Excerpt: first paragraph after title
    excerpt = ""
    for i in range(title_idx + 1, len(lines)):
        stripped = lines[i].strip()
        if stripped and not stripped.startswith("#"):
            excerpt = stripped[:160]
            break
    
    # Content
    body_md = "\n".join(lines[title_idx:])
    body_html = md_to_html(body_md)
    
    return wp_title, slug, excerpt, body_html

def create_post(filepath):
    title, slug, excerpt, body_html = parse_blog(filepath)
    
    payload = {
        "title": title,
        "content": body_html,
        "slug": slug,
        "status": "draft",
        "excerpt": excerpt,
        "format": "standard"
    }
    
    resp = requests.post(WP_URL, json=payload, auth=(WP_USER, WP_PASS), headers=headers, timeout=30)
    if resp.status_code in (200, 201):
        data = resp.json()
        return True, data.get('id'), data.get('link'), slug
    else:
        try:
            err = resp.json()
            return False, resp.status_code, err.get('message', resp.text[:200]), slug
        except:
            return False, resp.status_code, resp.text[:200], slug

def main():
    files = sorted(glob.glob(os.path.join(BLOG_DIR, "tonicphysio-blog-*.md")))
    print(f"Found {len(files)} blogs in {BLOG_DIR}\n")
    
    results = []
    for i, filepath in enumerate(files, 1):
        filename = os.path.basename(filepath)
        print(f"[{i}/{len(files)}] Pushing {filename}...", end=" ")
        
        try:
            success, post_id, link_or_err, slug = create_post(filepath)
            if success:
                print(f"OK — ID {post_id} ({slug})")
                results.append((filename, "ok", post_id, link_or_err))
            else:
                print(f"FAIL — HTTP {post_id}: {link_or_err}")
                results.append((filename, "fail", post_id, link_or_err))
        except Exception as e:
            print(f"EXCEPTION: {e}")
            results.append((filename, "exception", str(e), ""))
        
        # Small delay
        time.sleep(0.5)
    
    # Summary
    print("\n" + "="*60)
    ok = [r for r in results if r[1] == "ok"]
    fail = [r for r in results if r[1] != "ok"]
    print(f"DONE: {len(ok)} succeeded, {len(fail)} failed")
    
    if fail:
        print("\nFailed:")
        for r in fail:
            print(f"  {r[0]}: {r[2]}")
    
    return results

if __name__ == "__main__":
    main()
