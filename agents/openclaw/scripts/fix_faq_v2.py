#!/usr/bin/env python3
"""
Fix broken FAQ sections in TonicPhysio posts.
Current state: questions paired with themselves.
Need to: properly pair questions with real answer paragraphs.
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

def fix_faq_section(content):
    """
    Find the Common Questions/FAQ section, properly extract Q&A,
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

    # Determine section boundaries
    section_start = faq_h2.start()
    if faq_h2_idx + 1 < len(h2_matches):
        section_end = h2_matches[faq_h2_idx + 1].start()
    else:
        section_end = len(content)

    faq_html = content[section_start:section_end]

    # Extract questions from <strong> tags
    strong_pattern = re.compile(r'<strong>(.*?)</strong>')
    questions = [strip_tags(m.group(1)) for m in strong_pattern.finditer(faq_html)]
    questions = [q for q in questions if q and '?' in q]

    # Extract paragraphs that do NOT contain <strong> (these are answers)
    p_pattern = re.compile(r'<p>(.*?)</p>', re.DOTALL)
    all_paragraphs = list(p_pattern.finditer(faq_html))
    
    answers = []
    for m in all_paragraphs:
        p_content = m.group(1)
        # Skip if paragraph contains <strong> (it's a question paragraph)
        if '<strong>' not in p_content:
            a_text = strip_tags(p_content)
            if a_text and a_text not in [q for q in questions]:
                answers.append(a_text)

    print(f"  Found {len(questions)} questions, {len(answers)} real answers")
    for q in questions:
        print(f"    Q: {q[:70]}")
    for a in answers:
        print(f"    A: {a[:70]}")

    if not questions:
        return content, False, []

    # Pair questions with answers using semantic keyword matching
    used_answers = set()
    qa_pairs = []

    for q in questions:
        best_idx = None
        best_score = -1
        q_words = set(w.lower() for w in q.split() if len(w) > 3 and w not in ['does', 'will', 'should', 'could', 'would', 'what', 'when', 'where', 'which'])
        
        for i, a in enumerate(answers):
            if i in used_answers:
                continue
            a_text = a.lower()
            score = 0
            for w in q_words:
                if w in a_text:
                    score += 1
            # Bonus for specific keyword matches
            if 'referral' in q.lower() and 'referral' in a_text:
                score += 10
            if 'massage' in q.lower() and 'massage' in a_text:
                score += 10
            if 'safe' in q.lower() and ('safe' in a_text or 'contraindication' in a_text or 'risk' in a_text):
                score += 10
            if 'session' in q.lower() and ('session' in a_text or 'treatment' in a_text):
                score += 10
            if 'hurt' in q.lower() and ('pain' in a_text or 'sensation' in a_text or 'minimal' in a_text):
                score += 10
            if 'exercise' in q.lower() and ('exercise' in a_text or 'movement' in a_text):
                score += 10
            if 'home' in q.lower() and ('home' in a_text):
                score += 10
            if 'sleep' in q.lower() and ('sleep' in a_text or 'position' in a_text):
                score += 10
            if 'physiotherapist' in q.lower() and ('physiotherapist' in a_text or 'professional' in a_text):
                score += 10
            if 'child' in q.lower() and ('child' in a_text or 'pediatric' in a_text or 'age' in a_text):
                score += 10
            if 'pain' in q.lower() and ('pain' in a_text or 'discomfort' in a_text):
                score += 10
            if 'book' in q.lower() and ('book' in a_text or 'appointment' in a_text):
                score += 10

            if score > best_score:
                best_score = score
                best_idx = i

        if best_idx is not None:
            used_answers.add(best_idx)
            qa_pairs.append({'question': q, 'answer': answers[best_idx]})
        else:
            # Fallback
            for i in range(len(answers)):
                if i not in used_answers:
                    used_answers.add(i)
                    qa_pairs.append({'question': q, 'answer': answers[i]})
                    break

    # Build new FAQ section
    new_section = '\n<h2><span class="ez-toc-section" id="Common_Questions"></span>Common Questions<span class="ez-toc-section-end"></span></h2>\n\n'
    for qa in qa_pairs:
        q_clean = qa['question'].replace('"', '"').replace("'", "'")
        a_clean = qa['answer'].replace('"', '"').replace("'", "'")
        new_section += f'<p><strong>{q_clean}</strong></p>\n'
        new_section += f'<p>{a_clean}</p>\n\n'

    new_content = content[:section_start] + new_section + content[section_end:]
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

        new_content, was_fixed, qa_pairs = fix_faq_section(content)
        if not was_fixed:
            print("No FAQ section found. Skipping.")
            return True

        print(f"Q&A pairs fixed: {len(qa_pairs)}")
        for qa in qa_pairs:
            print(f"  Q: {qa['question'][:50]}")
            print(f"  A: {qa['answer'][:50]}")

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
    print("TonicPhysio FAQ Fix v2")
    print("Posts:", POST_IDS)
    print("=" * 60)
    success = 0
    for pid in POST_IDS:
        if process_post(pid):
            success += 1
    print(f"\n{'='*60}")
    print(f"DONE: {success}/{len(POST_IDS)} posts fixed")
    print("=" * 60)

if __name__ == "__main__":
    main()
