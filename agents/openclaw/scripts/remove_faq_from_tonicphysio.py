#!/usr/bin/env python3
"""
Batch remove FAQ sections from TonicPhysio blog posts.
Extracts Q&A pairs, removes H2 + H3 headings, and reformats content professionally.
"""
import json
import re
import sys
import base64
from urllib.request import Request, urlopen
from urllib.error import HTTPError

# TonicPhysio credentials
WP_URL = "https://tonicphysio.com/wp-json/wp/v2"
WP_USER = "Dan"
WP_APP_PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"

POST_IDS = [
    13030,  # Acupuncture
    13032,  # Cervical Spondylosis
    13033,  # Orthopedic vs Regular
    13034,  # Pediatric Physio
    13035,  # Rheumatoid Arthritis
    13036,  # Deep Tissue Massage
    13037,  # Hot Stone vs Swedish
    13039,  # Lymphatic Drainage
    13040,  # Post-Natal Massage
]

def strip_tags(html):
    return re.sub(r'<[^>]+>', '', html).strip()

def clean_html(html):
    """Remove duplicate ez-toc-section spans from headings"""
    # Remove duplicate ez-toc-section spans but keep one
    html = re.sub(r'(<span class="ez-toc-section"[^>]*>.*?</span>)+', r'\1', html, count=1)
    return html

HEADERS = {
    "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    "Accept": "application/json",
    "Referer": "https://tonicphysio.com/",
}

def fetch_post(post_id):
    url = f"{WP_URL}/posts/{post_id}"
    req = Request(url)
    creds = base64.b64encode(f"{WP_USER}:{WP_APP_PASS}".encode()).decode()
    req.add_header("Authorization", f"Basic {creds}")
    for k, v in HEADERS.items():
        req.add_header(k, v)
    with urlopen(req, timeout=30) as resp:
        return json.loads(resp.read().decode())

def update_post(post_id, title, content):
    url = f"{WP_URL}/posts/{post_id}"
    data = json.dumps({
        "title": title,
        "content": content,
        "status": "draft"  # Push as draft per MEMORY.md rule
    }).encode()
    req = Request(url, data=data, method="POST")
    creds = base64.b64encode(f"{WP_USER}:{WP_APP_PASS}".encode()).decode()
    req.add_header("Authorization", f"Basic {creds}")
    req.add_header("Content-Type", "application/json")
    for k, v in HEADERS.items():
        req.add_header(k, v)
    with urlopen(req, timeout=30) as resp:
        return json.loads(resp.read().decode())

def extract_faq_section(content):
    """
    Find and extract FAQ section.
    Returns (content_without_faq, faq_qa_pairs, faq_h2_text)
    """
    h3_pattern = re.compile(r'(<h3[^>]*>)(.*?)(</h3>)', re.DOTALL)
    h2_pattern = re.compile(r'(<h2[^>]*>)(.*?)(</h2>)', re.DOTALL)

    matches = list(h3_pattern.finditer(content))

    # Find first FAQ H3 (with ?)
    faq_start_idx = None
    faq_start_pos = None
    for i, m in enumerate(matches):
        text = strip_tags(m.group(2))
        if '?' in text:
            faq_start_idx = i
            faq_start_pos = m.start()
            break

    if faq_start_idx is None:
        return content, [], None

    # Look for H2 before FAQ H3s
    h2_matches = list(h2_pattern.finditer(content))
    faq_h2 = None
    faq_h2_text = None
    for h2 in h2_matches:
        if h2.end() <= faq_start_pos and h2.end() > faq_start_pos - 1000:
            text = strip_tags(h2.group(2))
            if any(kw in text.lower() for kw in ['question', 'faq', 'common', 'answer']):
                faq_h2 = h2
                faq_h2_text = text
                break

    # Determine FAQ section boundaries
    if faq_h2:
        faq_section_start = faq_h2.start()
    else:
        faq_section_start = faq_start_pos

    # Find last FAQ H3
    last_faq = matches[faq_start_idx]
    for i in range(faq_start_idx + 1, len(matches)):
        text = strip_tags(matches[i].group(2))
        if '?' in text:
            last_faq = matches[i]
        else:
            break

    faq_section_end = last_faq.end()
    # Extend to include content after last h3 until next h2 or end
    next_h2 = h2_pattern.search(content, last_faq.end())
    if next_h2:
        faq_section_end = next_h2.start()
    else:
        faq_section_end = len(content)

    # Extract Q&A pairs from FAQ HTML
    faq_html = content[faq_section_start:faq_section_end]

    # Find all H3s in FAQ section and their following content
    faq_h3s = list(h3_pattern.finditer(faq_html))
    qa_pairs = []

    # Also check for orphan paragraph before first H3 (after H2)
    if faq_h2:
        # Get content between H2 end and first H3
        h2_end_in_faq = faq_h2.end() - faq_section_start
        first_h3_in_faq = faq_h3s[0].start()
        orphan = faq_html[h2_end_in_faq:first_h3_in_faq]
        orphan_text = strip_tags(orphan)
        if orphan_text:
            # This orphan paragraph likely answers the first question
            pass  # We'll handle this below

    # Extract Q&A pairs
    for i, m in enumerate(faq_h3s):
        question = strip_tags(m.group(2))
        q_start = m.end()
        if i + 1 < len(faq_h3s):
            q_end = faq_h3s[i + 1].start()
        else:
            q_end = len(faq_html)

        answer_html = faq_html[q_start:q_end]
        answer_text = strip_tags(answer_html)
        answer_html_clean = answer_html.strip()

        if question and answer_text:
            qa_pairs.append({
                'question': question,
                'answer_text': answer_text,
                'answer_html': answer_html_clean
            })

    # Handle orphan paragraph - it belongs to first question
    if faq_h2 and qa_pairs:
        orphan_start = faq_h2.end() - faq_section_start
        orphan_end = faq_h3s[0].start()
        orphan_html = faq_html[orphan_start:orphan_end]
        orphan_text = strip_tags(orphan_html)
        if orphan_text:
            # Prepend orphan answer to first Q&A pair
            qa_pairs[0]['answer_text'] = orphan_text + ' ' + qa_pairs[0]['answer_text']
            qa_pairs[0]['answer_html'] = orphan_html.strip() + '\n' + qa_pairs[0]['answer_html']

    # Build new content without FAQ section
    new_content = content[:faq_section_start] + content[faq_section_end:]

    return new_content, qa_pairs, faq_h2_text

def reformat_qa_professionally(qa_pairs):
    """Reformat Q&A as professional body content - no H3 headings"""
    if not qa_pairs:
        return ""

    lines = []
    lines.append('<h2><span class="ez-toc-section" id="Common_Questions"></span>Common Questions<span class="ez-toc-section-end"></span></h2>')
    lines.append('')

    for qa in qa_pairs:
        # Use bold text instead of H3 for the question
        q_clean = qa['question'].replace("'", "'").replace("'", "'")
        lines.append(f'<p><strong>{q_clean}</strong></p>')
        lines.append(qa['answer_html'])
        lines.append('')

    return '\n'.join(lines)

def process_post(post_id):
    print(f"\n{'='*60}")
    print(f"Processing post {post_id}...")

    try:
        post = fetch_post(post_id)
        title = post['title']['rendered']
        content = post['content']['rendered']

        print(f"Title: {title}")
        print(f"Content length: {len(content)} chars")

        # Extract FAQ section
        new_content, qa_pairs, faq_h2 = extract_faq_section(content)

        print(f"FAQ H2: {faq_h2}")
        print(f"Q&A pairs found: {len(qa_pairs)}")

        if not qa_pairs:
            print("No FAQ section found. Skipping.")
            return True

        for i, qa in enumerate(qa_pairs):
            print(f"  Q{i+1}: {qa['question'][:60]}...")

        # Reformat Q&A professionally
        reformatted = reformat_qa_professionally(qa_pairs)

        # Append reformatted Q&A to body
        new_content = new_content.rstrip() + '\n\n' + reformatted

        print(f"New content length: {len(new_content)} chars")
        print(f"Removed: {len(content) - len(new_content) + len(reformatted)} chars of FAQ structure")

        # Update post (as DRAFT per MEMORY.md)
        result = update_post(post_id, title, new_content)
        print(f"Updated successfully! Status: {result.get('status', 'unknown')}")
        print(f"Post URL: {result.get('link', 'N/A')}")

        return True

    except HTTPError as e:
        print(f"HTTP Error {e.code}: {e.reason}")
        try:
            body = e.read().decode()
            print(f"Response: {body[:500]}")
        except:
            pass
        return False
    except Exception as e:
        print(f"Error: {e}")
        import traceback
        traceback.print_exc()
        return False

def main():
    print("=" * 60)
    print("TonicPhysio FAQ Removal Script")
    print("Posts to process:", POST_IDS)
    print("=" * 60)

    success_count = 0
    failed_ids = []

    for post_id in POST_IDS:
        if process_post(post_id):
            success_count += 1
        else:
            failed_ids.append(post_id)

    print(f"\n{'='*60}")
    print(f"COMPLETE: {success_count}/{len(POST_IDS)} posts updated")
    if failed_ids:
        print(f"Failed IDs: {failed_ids}")
    print("All updates pushed as DRAFT.")
    print("=" * 60)

if __name__ == "__main__":
    main()
