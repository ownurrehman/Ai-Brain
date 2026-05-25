#!/usr/bin/env python3
"""
Fix 3 TonicPhysio blog posts using local HTML files and curl for updates.
"""
import re, subprocess, json

BASE = "https://tonicphysio.com/wp-json/wp/v2"
USER = "Dan"
PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"

POSTS = {
    13034: {
        "title": "Pediatric Physiotherapy: When Your Child Needs Help in Milton",
        "blockquote": "Pediatric physiotherapy in Milton provides specialized, age-appropriate care to help children overcome developmental delays, injuries, and movement challenges. At Tonic Physio, our experienced therapists use play-based techniques to improve motor skills, strength, and coordination in a supportive environment.",
        "cta_heading": "Help Your Child Thrive with Pediatric Physiotherapy in Milton",
    },
    13035: {
        "title": "Rheumatoid Arthritis and Physiotherapy Management in Milton",
        "blockquote": "Rheumatoid arthritis physiotherapy in Milton focuses on reducing joint pain, maintaining mobility, and improving quality of life through personalized exercise and hands-on treatment. At Tonic Physio, our team helps patients manage symptoms and stay active with evidence-based care plans.",
        "cta_heading": None,
    },
    13036: {
        "title": "Deep Tissue Massage Benefits for Athletes in Milton",
        "blockquote": "Deep tissue massage for athletes in Milton targets chronic muscle tension, improves flexibility, and accelerates recovery after intense training. At Tonic Physio, our skilled therapists deliver personalized sports massage therapy to help you perform at your best.",
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

def extract_faq(html, cta_heading=None):
    faq_start = re.search(
        r'<h2[^>]*>\s*(?:<span[^>]*>)?\s*Frequently Asked Questions\s*(?:</span>)?\s*</h2>',
        html, re.S | re.I
    )
    if not faq_start:
        return html, None
    start_idx = faq_start.start()
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
    faq_html = html[start_idx:end_idx]
    content_without = html[:start_idx] + html[end_idx:]
    return content_without, faq_html

def transform_faq_to_common_questions(faq_html):
    if not faq_html:
        return ""
    faq_html = re.sub(
        r'<h2[^>]*>.*?(Frequently Asked Questions|FAQ).*?</h2>',
        '<h2>Common Questions Answered</h2>',
        faq_html, count=1, flags=re.S | re.I
    )
    return faq_html

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

def update_post(pid, content, title=None):
    payload = {"content": content}
    if title:
        payload["title"] = title
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
        print(f"  Stderr: {result.stderr[:500]}")
        return None

def process_post(pid, cfg):
    print(f"\n=== Processing post {pid}: {cfg['title']} ===")
    with open(f"post_{pid}_raw.html", "r") as f:
        html = f.read()

    # 1. Remove trailing blockquote
    html = remove_trailing_blockquote(html)

    # 2. Strip ez-toc spans
    html = strip_ez_toc_spans(html)

    # 3. Deduplicate repeated paragraphs
    html = dedup_repeated_paragraphs(html)

    # 4. Extract FAQ
    html_without_faq, faq_html = extract_faq(html, cfg.get("cta_heading"))
    if faq_html:
        print(f"  Extracted FAQ ({len(faq_html)} chars)")
    else:
        print("  No FAQ found")

    # 5. Add blockquote after intro
    html_without_faq = add_blockquote_after_intro(html_without_faq, cfg["blockquote"])

    # 6. Transform FAQ and insert before final CTA
    if faq_html:
        transformed_faq = transform_faq_to_common_questions(faq_html)
        if cfg.get("cta_heading"):
            cta_pattern = re.compile(
                rf'(</h2>\s*<p>.*?{re.escape(cfg["cta_heading"])}.*?</p>)',
                re.S | re.I
            )
            # Actually the CTA heading is an h2 itself; find it
            cta_match = re.search(
                rf'(<h2[^>]*>.*?{re.escape(cfg["cta_heading"])}.*?</h2>)',
                html_without_faq, re.S | re.I
            )
            if cta_match:
                html_without_faq = (
                    html_without_faq[:cta_match.start()] +
                    transformed_faq + "\n" +
                    html_without_faq[cta_match.start():]
                )
        else:
            cta_match = re.search(
                r'(<p>.*?(?:contact Tonic Physio|Book your deep tissue massage|schedule your personalized consultation).*?</p>)',
                html_without_faq, re.S | re.I
            )
            if cta_match:
                html_without_faq = (
                    html_without_faq[:cta_match.start()] +
                    transformed_faq + "\n" +
                    html_without_faq[cta_match.start():]
                )
            else:
                html_without_faq += "\n" + transformed_faq

    # 7. Clean up trailing blockquote again
    html_without_faq = remove_trailing_blockquote(html_without_faq)

    # Save preview
    with open(f"post_{pid}_fixed.html", "w") as f:
        f.write(html_without_faq)

    # 8. Update
    result = update_post(pid, html_without_faq, cfg["title"])
    if result and "id" in result:
        print(f"  Updated successfully. Modified: {result.get('modified')}")
        return {"id": pid, "status": "ok", "modified": result.get("modified")}
    else:
        print(f"  UPDATE FAILED")
        return {"id": pid, "status": "failed", "response": str(result)[:200]}

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
