import re

with open('orig_13034.html') as f:
    html = f.read()

# Strip ez-toc spans
html = re.sub(r'<span class="ez-toc-section"[^>]*>([^<]*)</span>', r'\1', html)
html = re.sub(r'<span class="ez-toc-section-end"></span>', '', html)

faq_match = re.search(r'<h2[^>]*>\s*Frequently Asked Questions\s*</h2>', html, re.S|re.I)
next_h2 = re.search(r'<h2[^>]*>', html[faq_match.end():], re.I)
next_pos = faq_match.end() + next_h2.start()

content_without = html[:faq_match.start()] + html[next_pos:]

# Find all H2s in content_without
h2s = list(re.finditer(r'<h2[^>]*>', content_without, re.I))
print(f"H2s in content_without: {len(h2s)}")
for i, h in enumerate(h2s):
    # Get the h2 content
    end_tag = content_without.find('</h2>', h.start())
    h2_content = content_without[h.start():end_tag+5]
    print(f"  H2 {i} at {h.start()}: {h2_content}")

# Try to find CTA
check_pos = 11810
print(f"\nContent at position {check_pos}:")
print(content_without[check_pos:check_pos+200])
