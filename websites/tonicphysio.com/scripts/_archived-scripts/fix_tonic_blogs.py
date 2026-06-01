#!/usr/bin/env python3
"""
Fix 3 TonicPhysio blog posts:
1. Remove FAQ section (H2 "Frequently Asked Questions" + all H3 Q&A after it, up to final CTA)
2. Deduplicate repeated definition paragraphs under first H2
3. Add <blockquote> summary after intro paragraph
4. Reinsert FAQ content as new H2 "Common Questions Answered" with H3 subheadings before final CTA
5. PUT updated content back via WP REST API
"""

import json, re, sys
import urllib.request

BASE = "https://tonicphysio.com/wp-json/wp/v2"
AUTH = ("Dan", "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ")

POSTS = [
    {
        "id": 13034,
        "title": "Pediatric Physiotherapy: When Your Child Needs Help in Milton",
        "blockquote": "Pediatric physiotherapy in Milton provides specialized, age-appropriate care to help children overcome developmental delays, injuries, and movement challenges. At Tonic Physio, our experienced therapists use play-based techniques to improve motor skills, strength, and coordination in a supportive environment.",
        "cta_heading": "Help Your Child Thrive with Pediatric Physiotherapy in Milton",
    },
    {
        "id": 13035,
        "title": "Rheumatoid Arthritis and Physiotherapy Management in Milton",
        "blockquote": "Rheumatoid arthritis physiotherapy in Milton focuses on reducing joint pain, maintaining mobility, and improving quality of life through personalized exercise and hands-on treatment. At Tonic Physio, our team helps patients manage symptoms and stay active with evidence-based care plans.",
        "cta_heading": None,  # final CTA is a <p> not an <h2>
    },
    {
        "id": 13036,
        "title": "Deep Tissue Massage Benefits for Athletes in Milton",
        "blockquote": "Deep tissue massage for athletes in Milton targets chronic muscle tension, improves flexibility, and accelerates recovery after intense training. At Tonic Physio, our skilled therapists deliver personalized sports massage therapy to help you perform at your best.",
        "cta_heading": None,  # final CTA is a <p> not an <h2>
    },
]

def get_post(pid):
    req = urllib.request.Request(f"{BASE}/posts/{pid}?context=edit")
    import base64
    creds = base64.b64encode(f"{AUTH[0]}:{AUTH[1]}".encode()).decode()
    req.add_header("Authorization", f"Basic {creds}")
    with urllib.request.urlopen(req, timeout=30) as resp:
        return json.loads(resp.read().decode())

def put_post(pid, data):
    payload = json.dumps(data).encode()
    req = urllib.request.Request(f"{BASE}/posts/{pid}", data=payload, method="POST",
                                  headers={"Content-Type": "application/json"})
    import base64
    creds = base64.b64encode(f"{AUTH[0]}:{AUTH[1]}".encode()).decode()
    req.add_header("Authorization", f"Basic {creds}")
    with urllib.request.urlopen(req, timeout=30) as resp:
        return json.loads(resp.read().decode())

def strip_ez_toc_spans(html):
    """Remove ez-toc-section spans and ez-toc-section-end spans while keeping inner text."""
    # Remove opening spans like <span class="ez-toc-section" id="...">text</span> -> keep text
    html = re.sub(r'<span class="ez-toc-section"[^>]*>([^<]*)</span>', r'\1', html)
    # Remove closing spans
    html = re.sub(r'<span class="ez-toc-section-end"></span>', '', html)
    return html

def dedup_repeated_paragraphs(html):
    """Under first H2, if same <p> text repeats 3+ times consecutively, keep only one."""
    # Find first H2
    h2_match = re.search(r'(<h2[^>]*>.*?</h2>)', html, re.S)
    if not h2_match:
        return html
    before = html[:h2_match.start()]
    after = html[h2_match.start():]
    # In the after section, find consecutive identical <p>...</p> blocks
    pattern = re.compile(r'((<p>[^<]+</p>\s*){3,})', re.S)
    def repl(m):
        block = m.group(1)
        # Extract individual <p> texts
        paras = re.findall(r'<p>([^<]+)</p>', block)
        if len(paras) >= 3 and len(set(paras)) == 1:
            return f'<p>{paras[0]}</p>\n'
        return block
    after = pattern.sub(repl, after)
    return before + after

def extract_faq(html, cta_heading=None):
    """
    Extract FAQ section: from H2 'Frequently Asked Questions' up to (but not including)
    either the final CTA heading/paragraph or end of content.
    Returns (content_without_faq, faq_html)
    """
    faq_start = re.search(
        r'<h2[^>]*>\s*<span[^>]*>\s*Frequently Asked Questions\s*</span>\s*</h2>',
        html, re.S | re.I
    )
    if not faq_start:
        # Try without inner span
        faq_start = re.search(
            r'<h2[^>]*>\s*Frequently Asked Questions\s*</h2>',
            html, re.S | re.I
        )
    if not faq_start:
        return html, None

    start_idx = faq_start.start()
    # Find where CTA starts after FAQ
    if cta_heading:
        cta_pattern = re.compile(
            rf'<h2[^>]*>.*?{re.escape(cta_heading)}.*?</h2>',
            re.S | re.I
        )
        cta_match = cta_pattern.search(html, start_idx)
        if cta_match:
            end_idx = cta_match.start()
        else:
            end_idx = len(html)
    else:
        # Look for final CTA paragraph containing "contact Tonic Physio" or "Book your"
        cta_match = re.search(
            r'<p>.*?(?:contact Tonic Physio|Book your deep tissue massage|schedule your personalized consultation).*?</p>',
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
    """Transform FAQ H2 into 'Common Questions Answered' and keep H3 Q&As."""
    if not faq_html:
        return ""
    # Replace the H2 heading
    faq_html = re.sub(
        r'<h2[^>]*>.*?(Frequently Asked Questions|FAQ).*?</h2>',
        '<h2>Common Questions Answered</h2>',
        faq_html, count=1, flags=re.S | re.I
    )
    # Ensure H3s are clean (they already are question text)
    return faq_html

def add_blockquote_after_intro(html, blockquote_text):
    """Insert <blockquote> after the first intro paragraph(s), before first H2."""
    first_h2 = re.search(r'<h2[^>]*>', html, re.I)
    if not first_h2:
        return html
    # Find a good insertion point: after the first </p> that is before first H2
    # But we want after the intro paragraph(s). The intro is everything before first H2.
    # Find last </p> before first H2
    intro = html[:first_h2.start()]
    rest = html[first_h2.start():]
    # Find last </p> in intro
    last_p = intro.rfind('</p>')
    if last_p == -1:
        return html
    insert_at = last_p + len('</p>')
    blockquote = f'\n<blockquote><p>{blockquote_text}</p></blockquote>\n'
    return intro[:insert_at] + blockquote + intro[insert_at:] + rest

def remove_trailing_blockquote(html):
    """Remove any <blockquote> at the very end of content."""
    # Strip trailing whitespace/newlines/div closings
    html = html.rstrip()
    if html.endswith('</div>'):
        # Might be inside a div wrapper; check before </div>
        inner = html[:-6].rstrip()
        bq_match = re.search(r'<blockquote>.*?</blockquote>\s*$', inner, re.S)
        if bq_match:
            html = inner[:bq_match.start()] + '</div>'
    else:
        bq_match = re.search(r'<blockquote>.*?</blockquote>\s*$', html, re.S)
        if bq_match:
            html = html[:bq_match.start()]
    return html.rstrip()

def process_post(post_cfg):
    pid = post_cfg["id"]
    print(f"\n=== Processing post {pid}: {post_cfg['title']} ===")
    data = get_post(pid)
    content = data["content"]["raw"]

    # 1. Remove existing trailing blockquote (it will be moved)
    content = remove_trailing_blockquote(content)

    # 2. Strip ez-toc spans to simplify processing
    content = strip_ez_toc_spans(content)

    # 3. Deduplicate repeated paragraphs under first H2
    content = dedup_repeated_paragraphs(content)

    # 4. Extract FAQ
    content_without_faq, faq_html = extract_faq(content, post_cfg.get("cta_heading"))
    if faq_html:
        print(f"  Extracted FAQ section ({len(faq_html)} chars)")
    else:
        print("  No FAQ section found!")

    # 5. Add blockquote after intro
    content_without_faq = add_blockquote_after_intro(content_without_faq, post_cfg["blockquote"])

    # 6. Transform FAQ and insert before final CTA
    if faq_html:
        transformed_faq = transform_faq_to_common_questions(faq_html)
        # Find final CTA in content_without_faq
        if post_cfg.get("cta_heading"):
            cta_pattern = re.compile(
                rf'(<h2[^>]*>.*?{re.escape(post_cfg["cta_heading"])}.*?</h2>)',
                re.S | re.I
            )
            cta_match = cta_pattern.search(content_without_faq)
            if cta_match:
                content_without_faq = (
                    content_without_faq[:cta_match.start()] +
                    transformed_faq + "\n" +
                    content_without_faq[cta_match.start():]
                )
        else:
            # Find final CTA paragraph
            cta_match = re.search(
                r'(<p>.*?(?:contact Tonic Physio|Book your deep tissue massage|schedule your personalized consultation).*?</p>)',
                content_without_faq, re.S | re.I
            )
            if cta_match:
                content_without_faq = (
                    content_without_faq[:cta_match.start()] +
                    transformed_faq + "\n" +
                    content_without_faq[cta_match.start():]
                )
            else:
                # Append at end if no CTA found
                content_without_faq += "\n" + transformed_faq

    # 7. Clean up: remove any existing blockquote that might still be at end
    content_without_faq = remove_trailing_blockquote(content_without_faq)

    # 8. Update post
    update_data = {"content": content_without_faq}
    result = put_post(pid, update_data)
    print(f"  Updated post {pid}. Status: {result.get('status', 'unknown')}")
    return {
        "id": pid,
        "faq_found": faq_html is not None,
        "faq_length": len(faq_html) if faq_html else 0,
        "status": result.get("status"),
        "modified": result.get("modified"),
    }

if __name__ == "__main__":
    results = []
    for cfg in POSTS:
        try:
            r = process_post(cfg)
            results.append(r)
        except Exception as e:
            print(f"ERROR processing {cfg['id']}: {e}")
            results.append({"id": cfg["id"], "error": str(e)})
    print("\n=== SUMMARY ===")
    for r in results:
        print(r)
