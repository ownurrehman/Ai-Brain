#!/usr/bin/env python3
"""
Fix 3 TonicPhysio blog posts.

Issues found:
- FAQ items are H3s without an H2 parent; they sit between the last content H2 and the final CTA.
- We need to extract those H3 Q&A blocks, remove them from their current position,
  and reinsert them as a new H2 "Common Questions Answered" before the final CTA.
"""
import re, subprocess, json

BASE = "https://tonicphysio.com/wp-json/wp/v2"
USER = "Dan"
PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"

POSTS = {
    13034: {
        "title": "Pediatric Physiotherapy: When Your Child Needs Help in Milton",
        "blockquote": "Pediatric physiotherapy in Milton provides specialized, age-appropriate care to help children overcome developmental delays, injuries, and movement challenges. At Tonic Physio, our experienced therapists use play-based techniques to improve motor skills, strength, and coordination in a supportive environment.",
        "last_content_h2": "The Benefits of Pediatric Physiotherapy",
        "cta_heading": "Help Your Child Thrive with Pediatric Physiotherapy in Milton",
    },
    13035: {
        "title": "Rheumatoid Arthritis and Physiotherapy Management in Milton",
        "blockquote": "Rheumatoid arthritis physiotherapy in Milton focuses on reducing joint pain, maintaining mobility, and improving quality of life through personalized exercise and hands-on treatment. At Tonic Physio, our team helps patients manage symptoms and stay active with evidence-based care plans.",
        "last_content_h2": "When to Seek Professional Help",
        "cta_heading": None,
    },
    13036: {
        "title": "Deep Tissue Massage Benefits for Athletes in Milton",
        "blockquote": "Deep tissue massage for athletes in Milton targets chronic muscle tension, improves flexibility, and accelerates recovery after intense training. At Tonic Physio, our skilled therapists deliver personalized sports massage therapy to help you perform at your best.",
        "last_content_h2": "Aftercare Tips for Athletes",
        "cta_heading": None,
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


def extract_faq_h3s(html, last_content_h2, cta_heading=None):
    """
    Find the sequence of H3 Q&As that appears after last_content_h2 H2 and before final CTA.
    Returns (content_without_faq, faq_html).
    """
    # Find the last content H2
    h2_pattern = re.compile(
        rf'(<h2[^>]*>.*?{re.escape(last_content_h2)}.*?</h2>)',
        re.S | re.I
    )
    h2_match = h2_pattern.search(html)
    if not h2_match:
        print(f"  Could not find last content H2: {last_content_h2}")
        return html, None

    start_idx = h2_match.end()

    # Find where CTA starts after this point
    if cta_heading:
        cta_pattern = re.compile(
            rf'(<h2[^>]*>.*?{re.escape(cta_heading)}.*?</h2>)',
            re.S | re.I
        )
        cta_match = cta_pattern.search(html, start_idx)
        if cta_match:
            end_idx = cta_match.start()
        else:
            end_idx = len(html)
    else:
        cta_match = re.search(
            r'(<p>.*?(?:contact Tonic Physio|Book your deep tissue massage|schedule your personalized consultation).*?</p>)',
            html[start_idx:], re.S | re.I
        )
        if cta_match:
            end_idx = start_idx + cta_match.start()
        else:
            end_idx = len(html)

    # Extract the block between last H2 and CTA
    block = html[start_idx:end_idx]

    # Find all H3 items in this block that are Q&A (each H3 followed by <p>)
    h3_items = list(re.finditer(r'<h3[^>]*>.*?</h3>', block, re.S | re.I))
    if not h3_items:
        return html, None

    # Extract from first H3 to end of block
    first_h3_start = h3_items[0].start()
    faq_block = block[first_h3_start:]
    faq_html = faq_block

    # Remove the FAQ block from the original content
    new_block = block[:first_h3_start]
    content_without = html[:start_idx] + new_block + html[end_idx:]

    return content_without, faq_html


def build_common_questions(faq_html):
    if not faq_html:
        return ""
    return '<h2>Common Questions Answered</h2>\n' + faq_html


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


def remove_trailing_blockquote(html):
    html = html.rstrip()
    if html.endswith('</div>'):
        inner = html[:-6].rstrip()
        bq_match = re.search(r'<blockquote>.*?</blockquote>\s*$', inner, re.S)
        if bq_match:
            html = inner[:bq_match.start()] + '</div>'
    else:
        bq_match = re.search(r'<blockquote>.*?</blockquote>\s*$', html, re.S)
        if bq_match:
            html = html[:bq_match.start()]
    return html.rstrip()


def insert_faq_before_cta(html, faq_html, cta_heading=None):
    if not faq_html:
        return html
    cq_html = build_common_questions(faq_html)

    if cta_heading:
        cta_pattern = re.compile(
            rf'(<h2[^>]*>.*?{re.escape(cta_heading)}.*?</h2>)',
            re.S | re.I
        )
        cta_match = cta_pattern.search(html)
        if cta_match:
            return html[:cta_match.start()] + cq_html + "\n" + html[cta_match.start():]
    else:
        cta_match = re.search(
            r'(<p>.*?(?:contact Tonic Physio|Book your deep tissue massage|schedule your personalized consultation).*?</p>)',
            html, re.S | re.I
        )
        if cta_match:
            return html[:cta_match.start()] + cq_html + "\n" + html[cta_match.start():]
    # Fallback: append at end
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

    # 1. Remove trailing blockquote
    html = remove_trailing_blockquote(html)

    # 2. Strip ez-toc spans
    html = strip_ez_toc_spans(html)

    # 3. Deduplicate repeated paragraphs under first H2
    html = dedup_repeated_paragraphs(html)

    # 4. Extract FAQ H3s
    html_without_faq, faq_html = extract_faq_h3s(
        html, cfg["last_content_h2"], cfg.get("cta_heading")
    )
    if faq_html:
        print(f"  Extracted FAQ H3s ({len(faq_html)} chars)")
    else:
        print("  No FAQ H3s found")

    # 5. Add blockquote after intro
    html_without_faq = add_blockquote_after_intro(html_without_faq, cfg["blockquote"])

    # 6. Insert transformed FAQ before CTA
    html_final = insert_faq_before_cta(
        html_without_faq, faq_html, cfg.get("cta_heading")
    )

    # 7. Remove any trailing blockquote again
    html_final = remove_trailing_blockquote(html_final)

    # Save preview
    with open(f"post_{pid}_fixed.html", "w") as f:
        f.write(html_final)

    # 8. Update post
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
