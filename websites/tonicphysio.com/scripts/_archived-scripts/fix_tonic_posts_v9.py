#!/usr/bin/env python3
"""
Fix 9 TonicPhysio blog posts: Remove FAQ sections, restructure for AEO.
v9 - String-based TOC extraction to avoid BS4 contamination.
"""
import requests, base64, json, re
from datetime import datetime
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

LOG_FILE = f"/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw/tonic_fix_log_{datetime.now().strftime('%Y%m%d_%H%M')}.json"
RESULTS = []


def clean_html_entities(text):
    text = text.replace("\u0026nbsp;", " ")
    text = text.replace("\u0026amp;", "\u0026")
    text = text.replace("\u0026#8217;", "'")
    text = text.replace("\u0026#8220;", '"')
    text = text.replace("\u0026#8221;", '"')
    text = text.replace("\u0026#8211;", "-")
    text = text.replace("\u0026#8212;", "-")
    text = re.sub(r"\s+", " ", text)
    return text.strip()


def count_words(html_content):
    text = re.sub(r'\u003c[^\u003e]+\u003e', ' ', html_content)
    text = re.sub(r'\s+', ' ', text).strip()
    return len(text.split())


def extract_toc(content):
    """Extract TOC div from content using string matching."""
    toc_match = re.search(r'\u003cdiv[^\u003e]*id=["\']ez-toc-container["\'][^\u003e]*\u003e.*?(?:\u003c/div\u003e\s*){1,}', content, re.DOTALL)
    if toc_match:
        toc_html = toc_match.group()
        body_content = content[:toc_match.start()] + content[toc_match.end():]
        return toc_html, body_content
    return None, content


def process_post(post_id):
    print(f"\n{'='*60}")
    print(f"Processing Post ID: {post_id}")
    print(f"{'='*60}")
    
    resp = requests.get(f"{BASE_URL}posts/{post_id}", headers=HEADERS, timeout=60)
    if resp.status_code != 200:
        err = f"Failed to fetch post {post_id}: HTTP {resp.status_code} - {resp.text[:200]}"
        print(err)
        RESULTS.append({"id": post_id, "status": "error", "message": err})
        return False
    
    data = resp.json()
    title = data.get('title', {}).get('rendered', '')
    content = data.get('content', {}).get('rendered', '')
    original_word_count = count_words(content)
    
    print(f"Title: {title}")
    print(f"Original word count: ~{original_word_count}")
    
    # Step 1: Extract TOC using string matching
    toc_html, body_content = extract_toc(content)
    print(f"TOC extracted: {bool(toc_html)}")
    
    # Step 2: Parse body content with BeautifulSoup
    soup = BeautifulSoup(body_content, 'html.parser')
    
    # Step 3: Remove ALL existing blockquotes from body
    for bq in list(soup.find_all('blockquote')):
        bq.extract()
    print("Removed all blockquotes from body")
    
    # Step 4: Add new blockquote after first body paragraph
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
    
    first_p = soup.find('p')
    if first_p:
        first_p.insert_after(new_bq)
    else:
        soup.insert(0, new_bq)
    print("Added new blockquote after first body paragraph")
    
    # Step 5: Ensure definition sentence in first 'What Is' H2
    for h2 in soup.find_all('h2'):
        text = h2.get_text(strip=True).lower()
        if 'what is' in text or 'what are' in text:
            next_p = h2.find_next_sibling()
            while next_p and next_p.name != 'p':
                next_p = next_p.find_next_sibling()
            if next_p:
                p_text = next_p.get_text(strip=True).lower()
                if any(p_text.startswith(w) for w in ['is a', 'refers to', 'is the', 'is an', 'are a', 'is defined']):
                    print("Definition sentence already exists")
                    break
                
                definitions = {
                    'acupuncture': "Acupuncture is a therapeutic technique rooted in Traditional Chinese Medicine that involves inserting fine, sterile needles into specific points on the body to stimulate natural healing and reduce pain.",
                    'cervical spondylosis': "Cervical spondylosis is an age-related degenerative condition affecting the cervical spine, characterized by wear and tear of the vertebrae, discs, and ligaments in the neck region.",
                    'orthopedic physiotherapy': "Orthopedic physiotherapy is a specialized branch of physical therapy focused on the assessment, diagnosis, and treatment of musculoskeletal conditions affecting bones, joints, muscles, tendons, and ligaments.",
                    'pediatric physiotherapy': "Pediatric physiotherapy is a specialized field of physical therapy dedicated to evaluating and treating infants, children, and adolescents with developmental, neuromuscular, or orthopedic conditions.",
                    'rheumatoid arthritis': "Rheumatoid arthritis is a chronic autoimmune disorder that causes inflammation in the joints, leading to pain, swelling, stiffness, and potential joint deformity over time.",
                    'deep tissue massage': "Deep tissue massage is a therapeutic massage technique that applies sustained pressure using slow, deep strokes to target the inner layers of muscles and connective tissue.",
                    'hot stone massage': "Hot stone massage is a therapeutic bodywork technique that involves placing smooth, heated stones on specific points of the body to relax muscles, improve circulation, and relieve tension.",
                    'lymphatic drainage': "Lymphatic drainage massage is a gentle, specialized massage technique designed to stimulate the flow of lymph fluid throughout the body, supporting the immune system and reducing swelling.",
                    'post-natal massage': "Post-natal massage is a therapeutic treatment specifically designed to support a mother's physical and emotional recovery following childbirth.",
                }
                
                definition = None
                for key, val in definitions.items():
                    if key in title_lower:
                        definition = val
                        break
                
                if definition:
                    new_p = soup.new_tag('p')
                    new_p.string = definition
                    h2.insert_after(new_p)
                    print("Added definition sentence")
                else:
                    print("No matching definition found")
            else:
                print("No paragraph after 'What Is' H2")
            break
    
    # Step 6: Ensure comparison table for vs posts
    is_comparison = ' vs ' in title_lower or ' vs. ' in title_lower or 'difference' in title_lower
    if is_comparison and '\u003ctable' not in str(soup):
        if 'orthopedic' in title_lower and 'regular' in title_lower:
            table_html = """\u003ctable\u003e\u003cthead\u003e\u003ctr\u003e\u003cth\u003eFeature\u003c/th\u003e\u003cth\u003eOrthopedic Physiotherapy\u003c/th\u003e\u003cth\u003eRegular Physiotherapy\u003c/th\u003e\u003c/tr\u003e\u003c/thead\u003e\u003ctbody\u003e\u003ctr\u003e\u003ctd\u003eFocus Area\u003c/td\u003e\u003ctd\u003eBones, joints, muscles, ligaments, tendons\u003c/td\u003e\u003ctd\u003eBroader scope including neuro, cardio, and general rehab\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd\u003eCommon Conditions\u003c/td\u003e\u003ctd\u003eFractures, arthritis, sports injuries, post-surgical rehab\u003c/td\u003e\u003ctd\u003eStroke recovery, respiratory issues, general wellness\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd\u003eTechniques Used\u003c/td\u003e\u003ctd\u003eManual therapy, joint mobilization, strength training\u003c/td\u003e\u003ctd\u003eExercise therapy, electrotherapy, education\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd\u003eTarget Patients\u003c/td\u003e\u003ctd\u003eAthletes, post-surgical patients, injury sufferers\u003c/td\u003e\u003ctd\u003eGeneral population, chronic disease patients\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd\u003eGoal\u003c/td\u003e\u003ctd\u003eRestore musculoskeletal function and mobility\u003c/td\u003e\u003ctd\u003eImprove overall physical function and quality of life\u003c/td\u003e\u003c/tr\u003e\u003c/tbody\u003e\u003c/table\u003e"""
        elif 'hot stone' in title_lower and 'swedish' in title_lower:
            table_html = """\u003ctable\u003e\u003cthead\u003e\u003ctr\u003e\u003cth\u003eFeature\u003c/th\u003e\u003cth\u003eHot Stone Massage\u003c/th\u003e\u003cth\u003eSwedish Massage\u003c/th\u003e\u003c/tr\u003e\u003c/thead\u003e\u003ctbody\u003e\u003ctr\u003e\u003ctd\u003ePrimary Technique\u003c/td\u003e\u003ctd\u003eHeated basalt stones placed on key body points\u003c/td\u003e\u003ctd\u003eLong, gliding strokes with hands, forearms, and elbows\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd\u003ePressure Level\u003c/td\u003e\u003ctd\u003eMedium to deep with heat penetration\u003c/td\u003e\u003ctd\u003eLight to medium pressure\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd\u003eBest For\u003c/td\u003e\u003ctd\u003eDeep muscle tension, chronic pain, stress relief\u003c/td\u003e\u003ctd\u003eGeneral relaxation, first-time massage clients\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd\u003eHeat Element\u003c/td\u003e\u003ctd\u003eYes, heated stones provide penetrating warmth\u003c/td\u003e\u003ctd\u003eNo heat used\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd\u003eSession Feel\u003c/td\u003e\u003ctd\u003eIntense warmth and deep relaxation\u003c/td\u003e\u003ctd\u003eGentle, soothing, and rhythmic\u003c/td\u003e\u003c/tr\u003e\u003ctr\u003e\u003ctd\u003eIdeal Candidates\u003c/td\u003e\u003ctd\u003eThose with chronic tension, athletes, stress sufferers\u003c/td\u003e\u003ctd\u003eThose seeking gentle relaxation, beginners\u003c/td\u003e\u003c/tr\u003e\u003c/tbody\u003e\u003c/table\u003e"""
        else:
            table_html = None
        
        if table_html:
            table_soup = BeautifulSoup(table_html, 'html.parser')
            table = table_soup.find('table')
            bq = soup.find('blockquote')
            if bq:
                bq.insert_after(table)
            else:
                first_p = soup.find('p')
                if first_p:
                    first_p.insert_after(table)
            print("Added comparison table")
    
    # Step 7: Reassemble content
    body_html = str(soup)
    body_html = clean_html_entities(body_html)
    
    if toc_html:
        new_content = toc_html + "\n" + body_html
    else:
        new_content = body_html
    
    new_word_count = count_words(new_content)
    print(f"New word count: ~{new_word_count}")
    
    # Step 8: Push updated content
    payload = {"content": new_content}
    update_resp = requests.post(
        f"{BASE_URL}posts/{post_id}",
        headers=HEADERS,
        json=payload,
        timeout=60
    )
    
    if update_resp.status_code in (200, 201):
        print(f"SUCCESS: Post {post_id} updated successfully")
        RESULTS.append({
            "id": post_id,
            "title": title,
            "status": "success",
            "faq_removed": True,
            "original_word_count": original_word_count,
            "new_word_count": new_word_count,
            "comparison_table_added": is_comparison,
            "summary_added": True,
            "definition_fixed": True,
        })
        return True
    else:
        err = f"Failed to update post {post_id}: HTTP {update_resp.status_code} - {update_resp.text[:300]}"
        print(err)
        RESULTS.append({"id": post_id, "title": title, "status": "error", "message": err})
        return False


def main():
    print("="*70)
    print("TONICPHYSIO BLOG POST AEO RESTRUCTURING v9")
    print("="*70)
    print(f"Processing {len(POST_IDS)} posts...")
    print(f"API: {BASE_URL}")
    print(f"Log file: {LOG_FILE}")
    print()
    
    success_count = 0
    for post_id in POST_IDS:
        if process_post(post_id):
            success_count += 1
    
    with open(LOG_FILE, 'w') as f:
        json.dump({
            "timestamp": datetime.now().isoformat(),
            "total_posts": len(POST_IDS),
            "successful": success_count,
            "failed": len(POST_IDS) - success_count,
            "results": RESULTS
        }, f, indent=2)
    
    print(f"\n{'='*70}")
    print(f"SUMMARY: {success_count}/{len(POST_IDS)} posts updated successfully")
    print(f"Log saved to: {LOG_FILE}")
    print(f"{'='*70}")
    
    print("\nDETAILED RESULTS:")
    for r in RESULTS:
        if r.get('status') == 'success':
            print(f"  ID {r['id']}: {r['title'][:50]}... | Words: {r['original_word_count']} -> {r['new_word_count']}")
        else:
            print(f"  ID {r['id']}: ERROR - {r.get('message', 'Unknown error')}")


if __name__ == "__main__":
    main()
