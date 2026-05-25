#!/usr/bin/env python3
"""
Fix 3 TonicPhysio blog posts.

FAQ items appear as bare H3s either:
 a) between the last content H2 and the final CTA (13034), OR
 b) after the final CTA paragraph and before the closing blockquote (13035, 13036).

Strategy: Extract all bare H3 + <p> answer blocks that come after a known 
"cutoff point" (either the CTA or the last content H2), remove them, and
reinsert as H2 "Common Questions Answered" before the final CTA.
"""
import re, subprocess, json

BASE = "https://tonicphysio.com/wp-json/wp/v2"
USER = "Dan"
PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"

POSTS = {
    13034: {
        "title": "Pediatric Physiotherapy: When Your Child Needs Help in Milton",
        "blockquote": "Pediatric physiotherapy in Milton provides specialized, age-appropriate care to help children overcome developmental delays, injuries, and movement challenges. At Tonic Physio, our experienced therapists use play-based techniques to improve motor skills, strength, and coordination in a supportive environment.",
        "cta_text": "contact Tonic Physio today",
    },
    13035: {
        "title": "Rheumatoid Arthritis and Physiotherapy Management in Milton",
        "blockquote": "Rheumatoid arthritis physiotherapy in Milton focuses on reducing joint pain, maintaining mobility, and improving quality of life through personalized exercise and hands-on treatment. At Tonic Physio, our team helps patients manage symptoms and stay active with evidence-based care plans.",
        "cta_text": "contact Tonic Physio today",
    },
    13036: {
        "title": "Deep Tissue Massage Benefits for Athletes in Milton",
        "blockquote": "Deep tissue massage for athletes in Milton targets chronic muscle tension, improves flexibility, and accelerates recovery after intense training. At Tonic Physio, our skilled therapists deliver personalized sports massage therapy to help you perform at your best.",
        "cta_text": "Book your deep tissue massage",
    },
}


def strip_ez_toc_spans(html):
    html = re.sub(r'<span class="ez-toc-section"[^>]*>([^<]*)</span>', r'\1', html)
    html = re.sub(r'<span class="ez-toc-section-end"></span>', '', html)
    return html


def dedup_repeated_paragraphs(html):
    h2_match = re.search(r'(<h2[^>]*>.*?</h2>)', html, re.S)
    if not h2_match:
        return html
    before = html[:h2_match.start()]
    after = html[h2_match.start():]
    pattern = re.compile(r'((<p>[^<]+</p>\s*){3,})', re.S)
    def repl(m):
        block = m.group(1)
        paras = re.findall(r'<p>([^<]+)</p>', block)
        if len(paras) >= 3 and len(set(paras)) == 1:
            return f'<p>{paras[0]}</p>\n'
        return block
    after = pattern.sub(repl, after)
    return before + after


def extract_faq(html, cta_text):
    """
    Find all bare H3 + <p> blocks that appear after the CTA paragraph.
    Remove them and return (html_without_faq, faq_html).
    """
    cta_pattern = re.compile(
        rf'(<p>.*?{re.escape(cta_text)}.*?</p>)',
        re.S | re.I
    )
    cta_match = cta_pattern.search(html)
    if not cta_match:
        print(f"  CTA not found: {cta_text}")
        return html, None

    cta_end = cta_match.end()

    # Look for a sequence of H3 + <p> after the CTA
    tail = html[cta_end:]

    # Strip trailing blockquote/div noise from tail
    tail_clean = tail.lstrip()
    bq_match = re.search(r'<blockquote>.*?</blockquote>\s*$', tail_clean, re.S)
    if bq_match:
        tail_clean = tail_clean[:bq_match.start()]
    if tail_clean.endswith('</div>'):
        tail_clean = tail_clean[:-6].rstrip()

    # Find all H3 items in this tail
    h3_items = list(re.finditer(r'<h3[^>]*>.*?</h3>', tail_clean, re.S | re.I))
    if not h3_items:
        # Try looking BEFORE the CTA (13034 style)
        before_cta = html[:cta_match.start()]
        # Find last content h2 before CTA
        last_h2 = re.search(r'<h2[^>]*>([^<]*)</h2>[^\n]*\n', before_cta, re.S)
        if last_h2:
            block = before_cta[last_h2.end():cta_match.start()]
            h3_items = list(re.finditer(r'<h3[^>]*>.*?</h3>', block, re.S | re.I))
            if h3_items:
                first_h3 = h3_items[0].start()
                faq_block = block[first_h3:]
                content_without = html[:last_h2.end()] + block[:first_h3] + html[cta_match.start():]
                return content_without, faq_block
        return html, None

    # FAQ is after CTA
    first_h3 = h3_items[0].start()
    faq_block = tail_clean[first_h3:]
    content_without = html[:cta_end] + tail[:first_h3]
    # Remove trailing noise
    content_without = re.sub(r'(</div>)?\s*<blockquote>.*?</blockquote>\s*$', '', content_without, flags=re.S)
    content_without = content_without.rstrip()
    if not content_without.endswith('</div>'):
        # Check if there's a missing div close
        if html.count('<div') > html.count('</div'):
            content_without += '</div>'
    return content_without, faq_block


def add_blockquote_after_intro(html, blockquote_text):
    first_h2 = re.search(r'<h2[^>]*>', html, re.I)
    if not first_h2:
        return html
    intro = html[:first_h2.start()]
    rest = html[first_h2.start():]
    last_p = intro.rfind('</p>')
    if last_p == -1:
        return html
    insert_at = last_p + len('</p>')
    blockquote = f'\n<blockquote><p>{blockquote_text}</p></blockquote>\n'
    return intro[:insert_at] + blockquote + intro[insert_at:] + rest


def insert_faq_before_cta(html, faq_html, cta_text):
    if not faq_html:
        return html
    cq_html = '<h2>Common Questions Answered</h2>\n' + faq_html
    cta_pattern = re.compile(
        rf'(<p>.*?{re.escape(cta_text)}.*?</p>)',
        re.S | re.I
    )
    cta_match = cta_pattern.search(html)
    if cta_match:
        return html[:cta_match.start()] + cq_html + "\n" + html[cta_match.start():]
    return html + "\n" + cq_html


def update_post(pid, content, title):
    payload = {"content": content, "title": title}
    data = json.dumps(payload)
    result = subprocess.run(
        ["curl", "-s", "-u", f"{USER}:{PASS}", "-X", "POST",
         "-H", "Content-Type: application/json",
         "-d", data,
         f"{BASE}/posts/{pid}"],
        capture_output=True, text=True, timeout=60
    )
    try:
        return json.loads(result.stdout)
    except:
        print(f"  Raw response: {result.stdout[:500]}")
        return None


def process_post(pid, cfg):
    print(f"\n=== Processing post {pid}: {cfg['title']} ===")
    with open(f"post_{pid}_raw.html", "r") as f:
        html = f.read()

    # 1. Strip ez-toc spans
    html = strip_ez_toc_spans(html)

    # 2. Deduplicate repeated paragraphs
    html = dedup_repeated_paragraphs(html)

    # 3. Extract FAQ
    html_without_faq, faq_html = extract_faq(html, cfg["cta_text"])
    if faq_html:
        print(f"  Extracted FAQ ({len(faq_html)} chars)")
    else:
        print("  No FAQ found")

    # 4. Add blockquote after intro
    html_without_faq = add_blockquote_after_intro(html_without_faq, cfg["blockquote"])

    # 5. Insert transformed FAQ before CTA
    html_final = insert_faq_before_cta(html_without_faq, faq_html, cfg["cta_text"])

    # Save preview
    with open(f"post_{pid}_fixed.html", "w") as f:
        f.write(html_final)

    # 6. Update post
    result = update_post(pid, html_final, cfg["title"])
    if result and "id" in result:
        print(f"  Updated successfully. Modified: {result.get('modified')}")
        return {"id": pid, "status": "ok", "modified": result.get("modified")}
    else:
        print(f"  UPDATE FAILED")
        return {"id": pid, "status": "failed"}


if __name__ == "__main__":
    results = []
    for pid, cfg in POSTS.items():
        try:
            r = process_post(pid, cfg)
            results.append(r)
        except Exception as e:
            print(f"ERROR: {e}")
            results.append({"id": pid, "error": str(e)})
    print("\n=== SUMMARY ===")
    for r in results:
        print(r)
