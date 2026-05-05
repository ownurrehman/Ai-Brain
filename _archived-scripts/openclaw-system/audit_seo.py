import requests
from bs4 import BeautifulSoup
import json
import re

urls = [
    "https://tonicphysio.com/physiotherapy-in-milton/",
    "https://tonicphysio.com/registered-massage-therapy/",
    "https://tonicphysio.com/tmj-treatment/",
    "https://tonicphysio.com/wsib-care-programs/",
    "https://tonicphysio.com/motor-vehicle-accident-physiotherapy/",
    "https://tonicphysio.com/compression-socks/",
    "https://tonicphysio.com/custom-and-otc-bracing/",
    "https://tonicphysio.com/custom-orthotics/",
    "https://tonicphysio.com/shockwave-therapy/",
    "https://tonicphysio.com/manual-osteopathy-milton/",
    "https://tonicphysio.com/physiotherapy-in-milton/back-and-neck-pain/",
    "https://tonicphysio.com/physiotherapy-in-milton/acupuncture-therapy/",
    "https://tonicphysio.com/physiotherapy-in-milton/frozen-shoulder-treatment/",
    "https://tonicphysio.com/physiotherapy-in-milton/neurological-physiotherapy/",
    "https://tonicphysio.com/physiotherapy-in-milton/sciatica-treatment/",
    "https://tonicphysio.com/physiotherapy-in-milton/sports-physiotherapy/",
    "https://tonicphysio.com/registered-massage-therapy/indie-head-massage/",
    "https://tonicphysio.com/registered-massage-therapy/relaxation-massage-in-milton/",
    "https://tonicphysio.com/physiotherapy-in-milton/herniated-disc-treatment/",
    "https://tonicphysio.com/physiotherapy-in-milton/joint-pain-and-stiffness/",
    "https://tonicphysio.com/physiotherapy-in-milton/orthopedic-physiotherapy/",
    "https://tonicphysio.com/b-pulse-pelvic-floor-strengthening/",
    "https://tonicphysio.com/physiotherapy-in-milton/cervical-spondylosis/",
    "https://tonicphysio.com/physiotherapy-in-milton/osteoarthritis-treatment/",
    "https://tonicphysio.com/physiotherapy-in-milton/rheumatoid-arthritis-therapy-treatment/",
    "https://tonicphysio.com/registered-massage-therapy/deep-tissue-massage-therapy/",
    "https://tonicphysio.com/registered-massage-therapy/hot-stone-massage-milton/",
    "https://tonicphysio.com/registered-massage-therapy/lymphatic-drainage-massage-milton/",
    "https://tonicphysio.com/registered-massage-therapy/post-natal-massage-milton/",
    "https://tonicphysio.com/registered-massage-therapy/pre-natal-massage-milton/",
    "https://tonicphysio.com/registered-massage-therapy/sports-massage/",
    "https://tonicphysio.com/physiotherapy-in-milton/pediatric-physiotherapy/"
]

results = []

for url in urls:
    try:
        response = requests.get(url, timeout=10)
        soup = BeautifulSoup(response.text, 'html.parser')
        
        title = soup.title.string if soup.title else ""
        
        meta_desc = ""
        desc_tag = soup.find("meta", attrs={"name": "description"})
        if desc_tag:
            meta_desc = desc_tag.get("content", "")
            
        h1s = soup.find_all('h1')
        h1_text = h1s[0].text.strip() if h1s else ""
        h1_count = len(h1s)
        
        h2s = [h2.text.strip() for h2 in soup.find_all('h2')]
        h3s = [h3.text.strip() for h3 in soup.find_all('h3')]
        
        schema = []
        scripts = soup.find_all('script', type='application/ld+json')
        for script in scripts:
            schema.append(script.string)
            
        links = soup.find_all('a', href=True)
        internal_links = [link['href'] for link in links if 'tonicphysio.com' in link['href']]
        
        imgs = soup.find_all('img')
        img_count = len(imgs)
        alt_missing = sum(1 for img in imgs if not img.get('alt'))
        
        text = soup.get_text()
        words = len(text.split())
        
        canonical = ""
        can_tag = soup.find("link", rel="canonical")
        if can_tag:
            canonical = can_tag.get("href", "")
            
        results.append({
            "url": url,
            "title": title,
            "meta_description": meta_desc,
            "h1": h1_text,
            "h1_count": h1_count,
            "h2_count": len(h2s),
            "h3_count": len(h3s),
            "schema_found": len(schema) > 0,
            "internal_link_count": len(internal_links),
            "img_count": img_count,
            "alt_missing": alt_missing,
            "word_count": words,
            "canonical": canonical
        })
    except Exception as e:
        results.append({"url": url, "error": str(e)})

print(json.dumps(results, indent=2))
