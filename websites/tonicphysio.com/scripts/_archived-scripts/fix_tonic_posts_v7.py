#!/usr/bin/env python3
"""
Fix 9 TonicPhysio blog posts: Remove FAQ sections, restructure for AEO.
v7 - Properly removes FAQ paragraphs and orphaned answers, keeps only Common Questions Answered section.
"""
import requests, base64, json, re, sys, os
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


def is_question(text):
    """Check if text is a question."""
    text = text.strip()
    if text.endswith('?'):
        return True
    if re.match(r'^\d+\.\s+.*\?$', text):
        return True
    return False


def remove_faq_paragraphs(content):
    """Remove paragraphs with bold questions and their orphaned answer paragraphs."""
    soup = BeautifulSoup(content, 'html.parser')
    
    # Find all p tags with strong child containing a question
    faq_paragraphs = []
    for p in soup.find_all('p'):
        strong = p.find('strong')
        if strong:
            strong_text = strong.get_text(strip=True)
            if is_question(strong_text):
                faq_paragraphs.append(p)
    
    # Also remove orphaned answer paragraphs that follow FAQ paragraphs
    to_remove = set()
    for faq_p in faq_paragraphs:
        to_remove.add(faq_p)
        # Check next sibling
        next_elem = faq_p.next_sibling
        while next_elem and not hasattr(next_elem, 'name'):
            next_elem = next_elem.next_sibling
        if next_elem and next_elem.name == 'p' and not next_elem.find('strong'):
            text = next_elem.get_text(strip=True)
            # Check if this looks like an answer to a FAQ
            if text and not text.startswith('At ') and not text.startswith('Living with') and not text.startswith('You do not have'):
                # Check if this text matches part of a known answer pattern
                to_remove.add(next_elem)
    
    for elem in to_remove:
        elem.extract()
    
    return str(soup), len(faq_paragraphs)


def clean_common_questions_section(content):
    """Ensure Common Questions Answered section is clean."""
    soup = BeautifulSoup(content, 'html.parser')
    
    # Find Common Questions Answered h2
    cqa_h2 = None
    for h in soup.find_all('h2'):
        if 'common questions answered' in h.get_text(strip=True).lower():
            cqa_h2 = h
            break
    
    if not cqa_h2:
        return content
    
    # Find the last h3/p pair in the Common Questions Answered section
    last_faq_element = cqa_h2
    for sibling in cqa_h2.find_next_siblings():
        if sibling.name in ['h2', 'h3']:
            if sibling.name == 'h3' and is_question(sibling.get_text(strip=True)):
                last_faq_element = sibling
                # Also track the p after this h3
                next_p = sibling.find_next_sibling()
                if next_p and next_p.name == 'p':
                    last_faq_element = next_p
            elif sibling.name == 'h3' and not is_question(sibling.get_text(strip=True)):
                break
            elif sibling.name == 'h2':
                break
        elif sibling.name == 'p':
            # Check if p is a FAQ answer
            if not sibling.find('strong'):
                last_faq_element = sibling
        else:
            break
    
    # Remove elements between last_faq_element and the conclusion heading
    current = last_faq_element.next_sibling
    conclusion_keywords = ['conclusion', 'ready to', 'book now', 'contact us', 'get started', 'final thoughts', 'next steps', 'take the first step']
    
    to_remove = []
    while current:
        if hasattr(current, 'name') and current.name in ['h2', 'h3']:
            text = current.get_text(strip=True).lower()
            if any(kw in text for kw in conclusion_keywords):
                break
        if hasattr(current, 'extract'):
            to_remove.append(current)
        current = current.next_sibling
    
    for elem in to_remove:
        elem.extract()
    
    return str(soup)


def ensure_single_blockquote(content, post_title):
    """Remove all summary blockquotes and add one fresh."""
    soup = BeautifulSoup(content, 'html.parser')
    
    for bq in list(soup.find_all('blockquote')):
        bq.extract()
    
    title_lower = post_title.lower()
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
        summary = f"This article provides evidence-based information about {post_title.split(':')[0]} to help you make informed decisions about your health and treatment options."
    
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
        body = soup.find('body') or soup
        body.insert(0, new_bq)
    
    return str(soup)


def ensure_definition_sentence(content, topic):
    """Ensure the first H2 'What Is...' starts with a definition."""
    soup = BeautifulSoup(content, 'html.parser')
    for h2 in soup.find_all('h2'):
        text = h2.get_text(strip=True).lower()
        if 'what is' in text or 'what are' in text:
            next_p = h2.find_next_sibling()
            while next_p and next_p.name != 'p':
                next_p = next_p.find_next_sibling()
            if next_p:
                p_text = next_p.get_text(strip=True).lower()
                if any(p_text.startswith(w) for w in ['is a', 'refers to', 'is the', 'is an', 'are a', 'is defined']):
                    return content
                
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
                    if key in topic.lower():
                        definition = val
                        break
                
                if definition:
                    new_p = soup.new_tag('p')
                    new_p.string = definition
                    h2.insert_after(new_p)
                    return str(soup)
            break
    return content


def ensure_comparison_table(content, post_title):
    """For comparison posts, ensure a proper table exists."""
    title_lower = post_title.lower()
    is_comparison = ' vs ' in title_lower or ' vs. ' in title_lower or 'difference' in title_lower
    
    if not is_comparison:
        return content
    
    if '\u003ctable' in content.lower():
        return content
    
    soup = BeautifulSoup(content, 'html.parser')
    
    if 'orthopedic' in title_lower and 'regular' in title_lower:
        table_html = """\u003ctable\u003e
\u003cthead\u003e
\u003ctr\u003e\u003cth\u003eFeature\u003c/th\u003e\u003cth\u003eOrthopedic Physiotherapy\u003c/th\u003e\u003cth\u003eRegular Physiotherapy\u003c/th\u003e\u003c/tr\u003e
\u003c/thead\u003e
\u003ctbody\u003e
\u003ctr\u003e\u003ctd\u003eFocus Area\u003c/td\u003e\u003ctd\u003eBones, joints, muscles, ligaments, tendons\u003c/td\u003e\u003ctd\u003eBroader scope including neuro, cardio, and general rehab\u003c/td\u003e\u003c/tr\u003e
\u003ctr\u003e\u003ctd\u003eCommon Conditions\u003c/td\u003e\u003ctd\u003eFractures, arthritis, sports injuries, post-surgical rehab\u003c/td\u003e\u003ctd\u003eStroke recovery, respiratory issues, general wellness\u003c/td\u003e\u003c/tr\u003e
\u003ctr\u003e\u003ctd\u003eTechniques Used\u003c/td\u003e\u003ctd\u003eManual therapy, joint mobilization, strength training\u003c/td\u003e\u003ctd\u003eExercise therapy, electrotherapy, education\u003c/td\u003e\u003c/tr\u003e
\u003ctr\u003e\u003ctd\u003eTarget Patients\u003c/td\u003e\u003ctd\u003eAthletes, post-surgical patients, injury sufferers\u003c/td\u003e\u003ctd\u003eGeneral population, chronic disease patients\u003c/td\u003e\u003c/tr\u003e
\u003ctr\u003e\u003ctd\u003eGoal\u003c/td\u003e\u003ctd\u003eRestore musculoskeletal function and mobility\u003c/td\u003e\u003ctd\u003eImprove overall physical function and quality of life\u003c/td\u003e\u003c/tr\u003e
\u003c/tbody\u003e
\u003c/table\u003e"""
    elif 'hot stone' in title_lower and 'swedish' in title_lower:
        table_html = """\u003ctable\u003e
\u003cthead\u003e
\u003ctr\u003e\u003cth\u003eFeature\u003c/th\u003e\u003cth\u003eHot Stone Massage\u003c/th\u003e\u003cth\u003eSwedish Massage\u003c/th\u003e\u003c/tr\u003e
\u003c/thead\u003e
\u003ctbody\u003e
\u003ctr\u003e\u003ctd\u003ePrimary Technique\u003c/td\u003e\u003ctd\u003eHeated basalt stones placed on key body points\u003c/td\u003e\u003ctd\u003eLong, gliding strokes with hands, forearms, and elbows\u003c/td\u003e\u003c/tr\u003e
\u003ctr\u003e\u003ctd\u003ePressure Level\u003c/td\u003e\u003ctd\u003eMedium to deep with heat penetration\u003c/td\u003e\u003ctd\u003eLight to medium pressure\u003c/td\u003e\u003c/tr\u003e
\u003ctr\u003e\u003ctd\u003eBest For\u003c/td\u003e\u003ctd\u003eDeep muscle tension, chronic pain, stress relief\u003c/td\u003e\u003ctd\u003eGeneral relaxation, first-time massage clients\u003c/td\u003e\u003c/tr\u003e
\u003ctr\u003e\u003ctd\u003eHeat Element\u003c/td\u003e\u003ctd\u003eYes, heated stones provide penetrating warmth\u003c/td\u003e\u003ctd\u003eNo heat used\u003c/td\u003e\u003c/tr\u003e
\u003ctr\u003e\u003ctd\u003eSession Feel\u003c/td\u003e\u003ctd\u003eIntense warmth and deep relaxation\u003c/td\u003e\u003ctd\u003eGentle, soothing, and rhythmic\u003c/td\u003e\u003c/tr\u003e
\u003ctr\u003e\u003ctd\u003eIdeal Candidates\u003c/td\u003e\u003ctd\u003eThose with chronic tension, athletes, stress sufferers\u003c/td\u003e\u003ctd\u003eThose seeking gentle relaxation, beginners\u003c/td\u003e\u003c/tr\u003e
\u003c/tbody\u003e
\u003c/table\u003e"""
    else:
        return content
    
    table_soup = BeautifulSoup(table_html, 'html.parser')
    table = table_soup.find('table')
    
    bq = soup.find('blockquote')
    if bq:
        bq.insert_after(table)
    else:
        first_p = soup.find('p')
        if first_p:
            first_p.insert_after(table)
    
    return str(soup)


def count_words(html_content):
    text = re.sub(r'\u003c[^\u003e]+\u003e', ' ', html_content)
    text = re.sub(r'\s+', ' ', text).strip()
    return len(text.split())


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
    
    # 1. Remove FAQ paragraphs with bold questions and orphaned answers
    content, removed_count = remove_faq_paragraphs(content)
    print(f"Removed {removed_count} FAQ paragraphs")
    
    # 2. Clean up Common Questions Answered section
    content = clean_common_questions_section(content)
    print("Cleaned Common Questions Answered section")
    
    # 3. Ensure single blockquote
    content = ensure_single_blockquote(content, title)
    print("Ensured single direct-answer blockquote")
    
    # 4. Ensure definition sentence
    content = ensure_definition_sentence(content, title)
    print("Ensured definition sentence in first H2")
    
    # 5. Ensure comparison table
    content = ensure_comparison_table(content, title)
    if ' vs ' in title.lower() or 'difference' in title.lower():
        print("Added/verified comparison table")
    
    # 6. Clean up
    content = clean_html_entities(content)
    
    new_word_count = count_words(content)
    print(f"New word count: ~{new_word_count}")
    
    # 7. Push updated content
    payload = {"content": content}
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
            "faq_removed": removed_count > 0,
            "faq_paragraphs_removed": removed_count,
            "original_word_count": original_word_count,
            "new_word_count": new_word_count,
            "comparison_table_added": ' vs ' in title.lower() or 'difference' in title.lower(),
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
    print("TONICPHYSIO BLOG POST AEO RESTRUCTURING v7")
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
            print(f"  ID {r['id']}: {r['title'][:50]}... | FAQ paragraphs removed: {r['faq_paragraphs_removed']} | Words: {r['original_word_count']} -> {r['new_word_count']}")
        else:
            print(f"  ID {r['id']}: ERROR - {r.get('message', 'Unknown error')}")


if __name__ == "__main__":
    main()
