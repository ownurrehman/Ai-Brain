#!/usr/bin/env python3
"""Debug script for 13034."""
import re

with open('orig_13034.html') as f:
    html = f.read()

# Strip ez-toc spans
html = re.sub(r'<span class="ez-toc-section"[^>]*>([^<]*)</span>', r'\1', html)
html = re.sub(r'<span class="ez-toc-section-end"></span>', '', html)

# Extract FAQ
faq_match = re.search(
    r'<h2[^>]*>\s*Frequently Asked Questions\s*</h2>',
    html, re.S | re.I
)
print(f"FAQ H2 found at: {faq_match.start() if faq_match else 'NOT FOUND'}")

if faq_match:
    start_idx = faq_match.start()
    next_h2 = re.search(r'<h2[^>]*>', html[faq_match.end():], re.I)
    if next_h2:
        end_idx = faq_match.end() + next_h2.start()
    else:
        end_idx = len(html)
    faq_html = html[start_idx:end_idx]
    content_without = html[:start_idx] + html[end_idx:]
    print(f"FAQ extracted, length={len(faq_html)}")
    print(f"Content without FAQ length={len(content_without)}")
else:
    content_without = html
    faq_html = None

# Add blockquote
blockquote_text = "Pediatric physiotherapy in Milton provides specialized, age-appropriate care to help children overcome developmental delays, injuries, and movement challenges. At Tonic Physio, our experienced therapists use play-based techniques to improve motor skills, strength, and coordination in a supportive environment."
first_h2 = re.search(r'<h2[^>]*>', content_without, re.I)
if first_h2:
    intro = content_without[:first_h2.start()]
    rest = content_without[first_h2.start():]
    last_p = intro.rfind('</p>')
    insert_at = last_p + len('</p>')
    blockquote = f'\n<blockquote><p>{blockquote_text}</p></blockquote>\n'
    content_without = intro[:insert_at] + blockquote + intro[insert_at:] + rest
    print(f"Blockquote inserted at position {insert_at}")

# Find CTA
cta_match = re.search(
    r'(<h2[^>]*>.*?Help Your Child Thrive with Pediatric Physiotherapy in Milton.*?</h2>)',
    content_without, re.S | re.I
)
print(f"CTA H2 found: {bool(cta_match)}")
if cta_match:
    print(f"CTA H2 at position: {cta_match.start()}")
    print(f"CTA H2 content: {cta_match.group(0)[:100]}")

# Check if there are other h2s before CTA
h2s = list(re.finditer(r'<h2[^>]*>', content_without, re.I))
print(f"Total H2s found: {len(h2s)}")
for i, h in enumerate(h2s):
    print(f"  H2 {i} at {h.start()}: {content_without[h.start():h.start()+80]}")
