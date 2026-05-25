import re

with open('orig_13034.html') as f:
    html = f.read()

# Strip ez-toc spans
html = re.sub(r'<span class="ez-toc-section"[^>]*>([^<]*)</span>', r'\1', html)
html = re.sub(r'<span class="ez-toc-section-end"></span>', '', html)

# Find FAQ H2
faq_match = re.search(r'<h2[^>]*>\s*Frequently Asked Questions\s*</h2>', html, re.S|re.I)
print(f'FAQ H2 found: {bool(faq_match)}')
if faq_match:
    print(f'FAQ H2 start: {faq_match.start()}')
    print(f'FAQ H2 end: {faq_match.end()}')
    
    # Find next H2
    next_h2 = re.search(r'<h2[^>]*>', html[faq_match.end():], re.I)
    print(f'Next H2 found: {bool(next_h2)}')
    if next_h2:
        next_pos = faq_match.end() + next_h2.start()
        print(f'Next H2 start: {next_pos}')
        print(f'Next H2 content: {html[next_pos:next_pos+80]}')
        
        faq_html = html[faq_match.start():next_pos]
        content_without = html[:faq_match.start()] + html[next_pos:]
        print(f'FAQ length: {len(faq_html)}')
        print(f'Content without FAQ length: {len(content_without)}')
        
        # Find CTA H2 in content_without
        cta_match = re.search(r'<h2[^>]*>.*?Help Your Child Thrive.*?</h2>', content_without, re.S|re.I)
        print(f'CTA H2 found in content_without: {bool(cta_match)}')
        if cta_match:
            print(f'CTA H2 start: {cta_match.start()}')
            print(f'CTA H2 content: {cta_match.group(0)[:80]}')
            
            # Verify positions
            print(f'FAQ starts at {faq_match.start()}')
            print(f'Next H2 (CTA) starts at {next_pos}')
            print(f'Content between FAQ and CTA: {len(html[faq_match.end():next_pos])}')
