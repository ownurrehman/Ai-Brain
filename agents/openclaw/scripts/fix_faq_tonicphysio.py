#!/usr/bin/env python3
"""
Fix FAQ sections in TonicPhysio posts.
Current state: H3s converted to <strong> but Q&A pairing is offset by 1.
Strategy: Remove broken FAQ section, reconstruct with correct pairing using semantic matching.
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

POST_IDS = [13030, 13032, 13033, 13034, 13035, 13036, 13037, 13039, 13040]

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

def semantic_match(question, answers):
    """Match a question to the most relevant answer using keyword overlap."""
    q_words = set(strip_tags(question).lower().split())
    best_score = -1
    best_idx = 0
    for i, ans in enumerate(answers):
        a_text = strip_tags(ans).lower()
        # Count keyword matches
        score = sum(1 for w in q_words if len(w) > 3 and w in a_text)
        if score > best_score:
            best_score = score
            best_idx = i
    return best_idx

def fix_faq_section(content, post_title=""):
    """
    Find the Common Questions/FAQ section, extract and fix Q&A pairing,
    return (new_content, was_fixed, qa_pairs).
    """
    h2_pattern = re.compile(r'(<h2[^>]*>)(.*?)(</h2>)', re.DOTALL)
    h2_matches = list(h2_pattern.finditer(content))

    # Find the FAQ H2
    faq_h2 = None
    faq_h2_idx = None
    for i, h2 in enumerate(h2_matches):
        text = strip_tags(h2.group(2))
        if any(kw in text.lower() for kw in ['common question', 'faq', 'frequently asked']):
            faq_h2 = h2
            faq_h2_idx = i
            break

    if not faq_h2:
        return content, False, []

    # Determine section boundaries: from FAQ H2 start to next H2 start (or end)
    section_start = faq_h2.start()
    if faq_h2_idx + 1 < len(h2_matches):
        section_end = h2_matches[faq_h2_idx + 1].start()
    else:
        section_end = len(content)

    faq_html = content[section_start:section_end]

    # Extract <strong>text</strong> (questions) and <p>text</p> (answers) from section
    strong_pattern = re.compile(r'<strong>(.*?)</strong>')
    p_pattern = re.compile(r'<p>(.*?)</p>', re.DOTALL)

    questions = [m.group(1) for m in strong_pattern.finditer(faq_html)]
    answers = [m.group(1) for m in p_pattern.finditer(faq_html)]

    # Remove empty/whitespace-only answers
    answers = [a for a in answers if strip_tags(a)]

    print(f"  Found {len(questions)} questions, {len(answers)} paragraphs in FAQ section")
    for q in questions:
        print(f"    Q: {strip_tags(q)[:70]}")
    for a in answers:
        print(f"    A: {strip_tags(a)[:70]}")

    if not questions:
        # No <strong> questions found, just remove the section
        new_content = content[:section_start] + content[section_end:]
        return new_content, True, []

    # Fix pairing using semantic matching
    # For each question, find the best matching answer from the pool
    used_answers = set()
    qa_pairs = []

    for q in questions:
        q_text = strip_tags(q)
        best_idx = None
        best_score = -1
        for i, a in enumerate(answers):
            if i in used_answers:
                continue
            a_text = strip_tags(a).lower()
            score = 0
            # Keyword matching
            q_words = set(w.lower() for w in q_text.split() if len(w) > 3)
            for w in q_words:
                if w in a_text:
                    score += 1
            if score > best_score:
                best_score = score
                best_idx = i

        if best_idx is not None:
            used_answers.add(best_idx)
            qa_pairs.append({
                'question': q_text,
                'answer': strip_tags(answers[best_idx])
            })
        else:
            # Fallback: pair with first unused answer
            for i in range(len(answers)):
                if i not in used_answers:
                    used_answers.add(i)
                    qa_pairs.append({
                        'question': q_text,
                        'answer': strip_tags(answers[i])
                    })
                    break

    # Build new FAQ section with correct pairing
    new_section = '<h2><span class="ez-toc-section" id="Common_Questions"></span>Common Questions<span class="ez-toc-section-end"></span></h2>\n\n'
    for qa in qa_pairs:
        q_clean = qa['question'].replace('"', '"').replace("'", "'")
        a_clean = qa['answer'].replace('"', '"').replace("'", "'")
        new_section += f'<p><strong>{q_clean}</strong></p>\n'
        new_section += f'<p>{a_clean}</p>\n\n'

    # Replace old section with new section
    new_content = content[:section_start] + '\n' + new_section + content[section_end:]
    return new_content, True, qa_pairs

def process_post(post_id):
    print(f"\n{'='*60}")
    print(f"Processing post {post_id}...")

    try:
        post = fetch_post(post_id)
        title = post['title']['rendered']
        content = post['content']['rendered']

        print(f"Title: {title}")
        print(f"Content length: {len(content)} chars")

        new_content, was_fixed, qa_pairs = fix_faq_section(content, title)

        if not was_fixed:
            print("No FAQ section found. Skipping.")
            return True

        print(f"Q&A pairs reconstructed: {len(qa_pairs)}")
        print(f"Content delta: {len(new_content) - len(content)} chars")

        # Update post as draft
        result = update_post(post_id, title, new_content)
        print(f"Updated successfully! Status: {result.get('status', 'unknown')}")
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
    print("TonicPhysio FAQ Fix Script")
    print("Posts:", POST_IDS)
    print("=" * 60)

    success = 0
    failed = []
    for pid in POST_IDS:
        if process_post(pid):
            success += 1
        else:
            failed.append(pid)

    print(f"\n{'='*60}")
    print(f"DONE: {success}/{len(POST_IDS)} posts updated")
    if failed:
        print(f"Failed: {failed}")
    print("All updates saved as DRAFT.")
    print("=" * 60)

if __name__ == "__main__":
    main()
