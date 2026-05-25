#!/usr/bin/env python3
"""
Fix TOC contamination: Move blockquotes from inside TOC div to proper body position.
"""
import requests, base64, json, re
from bs4 import BeautifulSoup

BASE_URL = "https://tonicphysio.com/wp-json/wp/v2/"
USER = "Dan"
APP_PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"
CREDS = base64.b64encode(f"{USER}:{APP_PASS}".encode()).decode()
HEADERS = {
    "Authorization": f"Basic {CREDS}",
    "Content-Type": "application/json",
}

POST_IDS = [13030, 13032, 13033, 13034, 13035, 13036, 13037, 13039, 13040]

def process_post(post_id):
    print(f"\nProcessing Post ID: {post_id}")
    
    resp = requests.get(f"{BASE_URL}posts/{post_id}", headers=HEADERS, timeout=60)
    if resp.status_code != 200:
        print(f"ERROR: Failed to fetch")
        return False
    
    data = resp.json()
    title = data.get('title', {}).get('rendered', '')
    content = data.get('content', {}).get('rendered', '')
    
    soup = BeautifulSoup(content, 'html.parser')
    
    # Find TOC container
    toc = soup.find(id='ez-toc-container')
    if not toc:
        print(f"  No TOC found, skipping")
        return True
    
    # Find blockquote inside TOC
    bq_in_toc = toc.find('blockquote')
    if not bq_in_toc:
        print(f"  No blockquote in TOC")
        # Still remove any blockquotes in body and add fresh one
    else:
        print(f"  Found blockquote in TOC, removing...")
        bq_in_toc.extract()
    
    # Remove ALL blockquotes from body
    for bq in list(soup.find_all('blockquote')):
        bq.extract()
    
    # Determine summary
    title_lower = title.lower()
    if ' vs ' in title_lower or ' vs. ' in title_lower or 'difference' in title_lower:
        summary_label = "Quick Comparison"
    elif 'benefits' in title_lower:
        summary_label = "Key Benefits at a Glance"
    elif 'exercises' in title_lower or 'steps' in title_lower or 'how to' in title_lower:
        summary_label = "What You'll Learn"
    else:
        summary_label = "Key Takeaway"
    
    summaries = {
        'acupuncture': "Acupuncture is a clinically supported treatment for chronic pain that stimulates natural healing by inserting thin needles at specific points. Research shows it effectively reduces pain intensity and improves function for conditions like back pain, osteoarthritis, and migraines.",
        'cervical': "Cervical spondylosis exercises focus on gentle neck stretches, strengthening, and posture correction to relieve pain and improve mobility. Regular physiotherapy-guided exercises can significantly reduce symptoms and prevent further degeneration.",
        'orthopedic': "Orthopedic physiotherapy specializes in treating musculoskeletal conditions including bones, joints, muscles, and ligaments, while regular physiotherapy addresses a broader range of physical issues including neurological and cardiovascular rehabilitation.",
        'pediatric': "Pediatric physiotherapy provides specialized, age-appropriate treatment for children with developmental delays, injuries, or neurological conditions. It uses play-based techniques to improve motor skills, strength, and coordination.",
        'rheumatoid': "Rheumatoid arthritis physiotherapy management includes targeted exercises, joint protection strategies, and pain relief techniques to maintain mobility and reduce inflammation. Early intervention helps preserve joint function and quality of life.",
        'deep tissue': "Deep tissue massage benefits athletes by releasing chronic muscle tension, breaking down scar tissue, improving range of motion, and speeding up recovery after intense training or competition.",
        'hot stone': "Hot stone massage uses heated stones to deeply relax muscles and improve circulation, while Swedish massage employs long, flowing strokes for overall relaxation. Hot stone is ideal for deep tension, whereas Swedish suits those seeking gentle stress relief.",
        'lymphatic': "Lymphatic drainage massage is a gentle technique that stimulates the lymphatic system to reduce swelling, detoxify the body, and support immune function. It benefits post-surgical patients, those with lymphedema, and individuals seeking immune support.",
        'post-natal': "Post-natal massage supports recovery after birth by reducing muscle tension, improving circulation, promoting hormonal balance, and aiding uterine contraction. It addresses the physical demands placed on a mother's body during pregnancy and delivery.",
    }
    
    summary = None
    for key, val in summaries.items():
        if key in title_lower:
            summary = val
            break
    if not summary:
        summary = f"This article provides evidence-based information about {title.split(':')[0]} to help you make informed decisions about your health and treatment options."
    
    new_bq = soup.new_tag('blockquote')
    new_p = soup.new_tag('p')
    new_strong = soup.new_tag('strong')
    new_strong.string = f"{summary_label}: "
    new_p.append(new_strong)
    new_p.append(summary)
    new_bq.append(new_p)
    
    # Insert after first body paragraph (not inside TOC)
    first_p = None
    for p in soup.find_all('p'):
        if not p.find_parent(id='ez-toc-container'):
            first_p = p
            break
    
    if first_p:
        first_p.insert_after(new_bq)
        print(f"  Added blockquote after first body paragraph")
    else:
        soup.append(new_bq)
        print(f"  Added blockquote at end")
    
    new_content = str(soup)
    
    payload = {"content": new_content}
    update_resp = requests.post(
        f"{BASE_URL}posts/{post_id}",
        headers=HEADERS,
        json=payload,
        timeout=60
    )
    
    if update_resp.status_code in (200, 201):
        print(f"  SUCCESS")
        return True
    else:
        print(f"  ERROR: HTTP {update_resp.status_code}")
        return False

def main():
    print("="*70)
    print("FIX TOC BLOCKQUOTES FINAL")
    print("="*70)
    
    success = 0
    for post_id in POST_IDS:
        if process_post(post_id):
            success += 1
    
    print(f"\n{'='*70}")
    print(f"SUMMARY: {success}/{len(POST_IDS)} posts fixed")
    print(f"{'='*70}")

if __name__ == "__main__":
    main()
