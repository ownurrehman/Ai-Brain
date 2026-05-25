#!/usr/bin/env python3
"""
Restore FAQ sections in TonicPhysio posts from earliest revision.
Strategy: Fetch earliest revision -> extract correct Q&A -> update current post.
"""
import json
import re
import base64
from urllib.request import Request, urlopen
from urllib.error import HTTPError

WP_URL = "https://tonicphysio.com/wp-json/wp/v2"
WP_USER = "Dan"
WP_APP_PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"

HEADERS = {
    "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    "Accept": "application/json",
    "Referer": "https://tonicphysio.com/",
}

POST_IDS = [13030, 13032, 13033, 13034, 13039, 13040]

def strip_tags(html):
    return re.sub(r'<[^>]+>', '', html).strip()

def fetch_post(post_id):
    url = f"{WP_URL}/posts/{post_id}"
    req = Request(url)
    creds = base64.b64encode(f"{WP_USER}:{WP_APP_PASS}".encode()).decode()
    req.add_header("Authorization", f"Basic {creds}")
    for k, v in HEADERS.items():
        req.add_header(k, v)
    with urlopen(req, timeout=30) as resp:
        return json.loads(resp.read().decode())

def fetch_revision(post_id, rev_id):
    url = f"{WP_URL}/posts/{post_id}/revisions/{rev_id}"
    req = Request(url)
    creds = base64.b64encode(f"{WP_USER}:{WP_APP_PASS}".encode()).decode()
    req.add_header("Authorization", f"Basic {creds}")
    for k, v in HEADERS.items():
        req.add_header(k, v)
    with urlopen(req, timeout=30) as resp:
        return json.loads(resp.read().decode())

def get_earliest_revision_id(post_id):
    """Fetch revision list and return the earliest (last in list) revision ID."""
    url = f"{WP_URL}/posts/{post_id}/revisions"
    req = Request(url)
    creds = base64.b64encode(f"{WP_USER}:{WP_APP_PASS}".encode()).decode()
    req.add_header("Authorization", f"Basic {creds}")
    for k, v in HEADERS.items():
        req.add_header(k, v)
    with urlopen(req, timeout=30) as resp:
        revisions = json.loads(resp.read().decode())
        if revisions:
            return revisions[-1]['id']
    return None

def update_post(post_id, title, content):
    url = f"{WP_URL}/posts/{post_id}"
    data = json.dumps({
        "title": title,
        "content": content,
        "status": "draft"
    }).encode()
    req = Request(url, data=data, method="POST")
    creds = base64.b64encode(f"{WP_USER}:{WP_APP_PASS}".encode()).decode()
    req.add_header("Authorization", f"Basic {creds}")
    req.add_header("Content-Type", "application/json")
    for k, v in HEADERS.items():
        req.add_header(k, v)
    with urlopen(req, timeout=30) as resp:
        return json.loads(resp.read().decode())

def extract_faq_from_revision(rev_content):
    """
    Extract Q&A pairs from revision content.
    The original format is:
      <h2>Frequently Asked Questions</h2>
      <h3>Question?</h3>
      <p>Answer text</p>
      <h3>Question 2?</h3>
      <p>Answer 2 text</p>
    Returns list of (question, answer) tuples.
    """
    # Find FAQ H2
    h2_pattern = re.compile(r'(<h2[^>]*>)(.*?)(</h2>)', re.DOTALL)
    h3_pattern = re.compile(r'(<h3[^>]*>)(.*?)(</h3>)', re.DOTALL)
    h2_matches = list(h2_pattern.finditer(rev_content))

    faq_h2 = None
    faq_h2_idx = None
    for i, h2 in enumerate(h2_matches):
        text = strip_tags(h2.group(2))
        if any(kw in text.lower() for kw in ['frequently asked', 'common question', 'faq']):
            faq_h2 = h2
            faq_h2_idx = i
            break

    if not faq_h2:
        return []

    # Find section boundaries
    section_start = faq_h2.end()
    if faq_h2_idx + 1 < len(h2_matches):
        section_end = h2_matches[faq_h2_idx + 1].start()
    else:
        section_end = len(rev_content)

    faq_section = rev_content[section_start:section_end]

    # Extract H3s and their following P tags
    h3_matches = list(h3_pattern.finditer(faq_section))
    qa_pairs = []

    for i, h3 in enumerate(h3_matches):
        question = strip_tags(h3.group(2))
        q_end = h3.end()
        if i + 1 < len(h3_matches):
            p_end = h3_matches[i + 1].start()
        else:
            p_end = len(faq_section)

        # Find the first <p> after this H3
        p_match = re.search(r'<p[^>]*>(.*?)</p>', faq_section[q_end:p_end], re.DOTALL)
        if p_match:
            answer = strip_tags(p_match.group(1))
            if question and answer:
                qa_pairs.append((question, answer))

    return qa_pairs

def rebuild_faq_section(qa_pairs):
    """Build FAQ section with <strong> questions and <p> answers (no H2/H3)."""
    if not qa_pairs:
        return ""

    lines = []
    for q, a in qa_pairs:
        q_clean = q.replace('"', '"').replace("'", "'")
        a_clean = a.replace('"', '"').replace("'", "'")
        lines.append(f'<p><strong>{q_clean}</strong></p>')
        lines.append(f'<p>{a_clean}</p>')
        lines.append('')

    return '\n'.join(lines)

def process_post(post_id):
    print(f"\n{'='*60}")
    print(f"Processing post {post_id}...")

    try:
        # Fetch current post
        post = fetch_post(post_id)
        title = post['title']['rendered']
        current_content = post['content']['rendered']
        print(f"Title: {title}")

        # Get earliest revision
        rev_id = get_earliest_revision_id(post_id)
        if not rev_id:
            print("No revisions found. Skipping.")
            return False

        print(f"Fetching revision {rev_id}...")
        rev = fetch_revision(post_id, rev_id)
        rev_content = rev['content']['rendered']

        # Extract Q&A from revision
        qa_pairs = extract_faq_from_revision(rev_content)
        print(f"Extracted {len(qa_pairs)} Q&A pairs from revision")

        if not qa_pairs:
            print("No FAQ section in revision. Skipping.")
            return True

        for q, a in qa_pairs:
            print(f"  Q: {q[:60]}")
            print(f"  A: {a[:60]}")

        # Find FAQ section in current content
        h2_pattern = re.compile(r'(<h2[^>]*>)(.*?)(</h2>)', re.DOTALL)
        h2_matches = list(h2_pattern.finditer(current_content))

        faq_h2 = None
        faq_h2_idx = None
        for i, h2 in enumerate(h2_matches):
            text = strip_tags(h2.group(2))
            if any(kw in text.lower() for kw in ['common question', 'frequently asked', 'faq']):
                faq_h2 = h2
                faq_h2_idx = i
                break

        if not faq_h2:
            print("No FAQ section in current content. Appending Q&A at end.")
            # Append before the last </div> or at the very end
            new_content = current_content.rstrip() + '\n\n' + rebuild_faq_section(qa_pairs)
        else:
            section_start = faq_h2.start()
            if faq_h2_idx + 1 < len(h2_matches):
                section_end = h2_matches[faq_h2_idx + 1].start()
            else:
                section_end = len(current_content)

            new_section = rebuild_faq_section(qa_pairs)
            new_content = current_content[:section_start] + new_section + current_content[section_end:]

        print(f"Content length: {len(current_content)} -> {len(new_content)}")

        # Update post
        result = update_post(post_id, title, new_content)
        print(f"Updated! Status: {result.get('status', 'unknown')}")
        return True

    except Exception as e:
        print(f"Error: {e}")
        import traceback
        traceback.print_exc()
        return False

def main():
    print("=" * 60)
    print("TonicPhysio FAQ Restoration")
    print("Posts:", POST_IDS)
    print("=" * 60)

    success = 0
    for pid in POST_IDS:
        if process_post(pid):
            success += 1

    print(f"\n{'='*60}")
    print(f"DONE: {success}/{len(POST_IDS)} posts restored")
    print("=" * 60)

if __name__ == "__main__":
    main()
