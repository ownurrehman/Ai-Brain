import re,json,subprocess

for pid in [13034]:
    result = subprocess.run(
        ['curl','-s','-u','Dan:NMwZ 1LyJ YgbE fUjs pUYn 4SoZ',
         f'https://tonicphysio.com/wp-json/wp/v2/posts/{pid}?context=edit'],
        capture_output=True, text=True
    )
    d = json.loads(result.stdout)
    html = d['content']['raw']
    # Strip ez-toc spans
    html = re.sub(r'<span class="ez-toc-section"[^>]*>([^<]*)</span>', r'\1', html)
    html = re.sub(r'<span class="ez-toc-section-end"></span>', '', html)
    
    # Show intro section structure
    first_h2 = re.search(r'<h2[^>]*>', html, re.I)
    if first_h2:
        intro = html[:first_h2.start()]
        # Show everything from h1 onwards
        h1 = re.search(r'<h1[^>]*>', html, re.I)
        if h1:
            section = html[h1.start():first_h2.start()]
            print(section)
    
    # Show CTA area
    cta = re.search(r'<h2[^>]*>.*?Help Your Child Thrive.*?</h2>', html, re.S|re.I)
    if cta:
        print()
        print('=== CTA SECTION ===')
        start = max(0, cta.start()-200)
        end = cta.end()+300
        print(html[start:end])
