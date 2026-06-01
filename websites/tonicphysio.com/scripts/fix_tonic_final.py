#!/usr/bin/env python3
"""
Clean script to fix 3 TonicPhysio blog posts.

Reads original content from orig_{pid}.html (WP revision), then:
1. Strips ez-toc spans
2. Extracts FAQ section (H2 "Frequently Asked Questions" + all H3 Q&A until next H2 or end)
3. Removes FAQ from content
4. Deduplicates repeated definition paragraphs
5. Adds <blockquote> after intro
6. Reinserts FAQ as H2 "Common Questions Answered" before final CTA
7. Updates post via WP REST API
"""
import re, subprocess, json

BASE = "https://tonicphysio.com/wp-json/wp/v2"
USER = "Dan"
PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"

POSTS = {
    13034: {
        "title": "Pediatric Physiotherapy: When Your Child Needs Help in Milton",
        "blockquote": "Pediatric physiotherapy in Milton provides specialized, age-appropriate care to help children overcome developmental delays, injuries, and movement challenges. At Tonic Physio, our experienced therapists use play-based techniques to improve motor skills, strength, and coordination in a supportive environment.",
    },
    13035: {
        "title": "Rheumatoid Arthritis and Physiotherapy Management in Milton",
        "blockquote": "Rheumatoid arthritis physiotherapy in Milton focuses on reducing joint pain, maintaining mobility, and improving quality of life through personalized exercise and hands-on treatment. At Tonic Physio, our team helps patients manage symptoms and stay active with evidence-based care plans.",
    },
    13036: {
        "title": "Deep Tissue Massage Benefits for Athletes in Milton",
        "blockquote": "Deep tissue massage for athletes in Milton targets chronic muscle tension, improves flexibility, and accelerates recovery after intense training. At Tonic Physio, our skilled therapists deliver personalized sports massage therapy to help you perform at your best.",
    },
}


def strip_ez_toc_spans(html):
    html = re.sub(r'<span class="ez-toc-section"[^\u003e]*\u003e([^\u003c]*)\u003c/span\u003e', r'\1', html)
    html = re.sub(r'<span class="ez-toc-section-end"\u003e\u003c/span\u003e', '', html)
    return html


def dedup_repeated_paragraphs(html):
    h2_match = re.search(r'(<h2[^\u003e]*\u003e.*?\u003c/h2\u003e)', html, re.S)
    if not h2_match:
        return html
    before = html[:h2_match.start()]
    after = html[h2_match.start():]
    pattern = re.compile(r'((\u003cp\u003e[^\u003c]+\u003c/p\u003e\s*){3,})', re.S)
    def repl(m):
        block = m.group(1)
        paras = re.findall(r'<p\u003e([^\u003c]+)\u003c/p\u003e', block)
        if len(paras) >= 3 and len(set(paras)) == 1:
            return f'<p\u003e{paras[0]}\u003c/p\u003e\n'
        return block
    after = pattern.sub(repl, after)
    return before + after


def extract_faq(html):
    """
    Extract the FAQ section: H2 "Frequently Asked Questions" plus all following
    H3 + <p> blocks until the next H2 or end of content.
    Returns (html_without_faq, faq_html).
    """
    faq_match = re.search(
        r'<h2[^\u003e]*\u003e\s*Frequently Asked Questions\s*</h2\u003e',
        html, re.S | re.I
    )
    if not faq_match:
        return html, None

    start_idx = faq_match.start()
    # Find the next H2 after the FAQ heading
    next_h2 = re.search(r'<h2[^\u003e]*\u003e', html[faq_match.end():], re.I)
    if next_h2:
        end_idx = faq_match.end() + next_h2.start()
    else:
        end_idx = len(html)

    faq_html = html[start_idx:end_idx]
    content_without = html[:start_idx] + html[end_idx:]
    return content_without, faq_html


def transform_faq(faq_html):
    if not faq_html:
        return ""
    # Replace H2 heading
    faq_html = re.sub(
        r'<h2[^\u003e]*\u003e\s*Frequently Asked Questions\s*</h2\u003e',
        '<h2\u003eCommon Questions Answered</h2\u003e',
        faq_html, count=1, flags=re.S | re.I
    )
    return faq_html


def add_blockquote_after_intro(html, blockquote_text):
    first_h2 = re.search(r'<h2[^\u003e]*\u003e', html, re.I)
    if not first_h2:
        return html
    intro = html[:first_h2.start()]
    rest = html[first_h2.start():]
    last_p = intro.rfind('</p\u003e')
    if last_p == -1:
        return html
    insert_at = last_p + len('</p\u003e')
    blockquote = f'\n<blockquote\u003e<p\u003e{blockquote_text}</p\u003e</blockquote\u003e\n'
    return intro[:insert_at] + blockquote + intro[insert_at:] + rest


def insert_faq_before_cta(html, faq_html):
    if not faq_html:
        return html
    cq_html = transform_faq(faq_html)
    # Find the final CTA: either an H2 CTA heading or a CTA paragraph
    # Try H2 first (13034)
    cta_h2 = re.search(
        r'(<h2[^\u003e]*\u003e(?:[^\u003c]*(?:contact|Book|schedule)[^\u003c]*)\u003c/h2\u003e)',
        html, re.S | re.I
    )
    if cta_h2:
        return html[:cta_h2.start()] + cq_html + "\n" + html[cta_h2.start():]

    # Try CTA paragraph (13035, 13036)
    cta_p = re.search(
        r'(<p\u003e.*?(?:contact Tonic Physio|Book your deep tissue massage|schedule your personalized consultation).*?\u003c/p\u003e)',
        html, re.S | re.I
    )
    if cta_p:
        return html[:cta_p.start()] + cq_html + "\n" + html[cta_p.start():]

    # Fallback: append at end
    return html + "\n" + cq_html


def remove_trailing_blockquote(html):
    html = html.rstrip()
    if html.endswith('</div\u003e'):
        inner = html[:-6].rstrip()
        bq_match = re.search(r'<blockquote\u003e.*?\u003c/blockquote\u003e\s*$', inner, re.S)
        if bq_match:
            html = inner[:bq_match.start()] + '</div\u003e'
    else:
        bq_match = re.search(r'<blockquote\u003e.*?\u003c/blockquote\u003e\s*$', html, re.S)
        if bq_match:
            html = html[:bq_match.start()]
    return html.rstrip()


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
    with open(f"orig_{pid}.html", "r") as f:
        html = f.read()

    # 1. Strip ez-toc spans
    html = strip_ez_toc_spans(html)

    # 2. Deduplicate repeated paragraphs
    html = dedup_repeated_paragraphs(html)

    # 3. Extract FAQ
    html_without_faq, faq_html = extract_faq(html)
    if faq_html:
        print(f"  Extracted FAQ ({len(faq_html)} chars)")
    else:
        print("  No FAQ found")

    # 4. Add blockquote after intro
    html_without_faq = add_blockquote_after_intro(html_without_faq, cfg["blockquote"])

    # 5. Insert transformed FAQ before CTA
    html_final = insert_faq_before_cta(html_without_faq, faq_html)

    # 6. Remove trailing blockquote if any
    html_final = remove_trailing_blockquote(html_final)

    # Save preview
    with open(f"final_{pid}.html", "w") as f:
        f.write(html_final)

    # 7. Update post
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
