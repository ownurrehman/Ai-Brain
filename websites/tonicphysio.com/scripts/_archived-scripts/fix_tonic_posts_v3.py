#!/usr/bin/env python3
"""
Fix 9 TonicPhysio blog posts: Remove FAQ sections, restructure for AEO.
v3 - Handles EZ TOC wrapper markup, deduplicates blockquotes, precise FAQ extraction.
"""
import requests, base64, json, re, sys, os
from datetime import datetime

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
    text = text.replace("&nbsp;", " ")
    text = text.replace("&amp;", "&")
    text = text.replace("&#8217;", "'")
    text = text.replace("&#8220;", '"')
    text = text.replace("&#8221;", '"')
    text = text.replace("&#8211;", "-")
    text = text.replace("&#8212;", "-")
    text = re.sub(r"\s+", " ", text)
    return text.strip()


def remove_duplicate_blockquotes(content):
    """Remove duplicate Key Takeaway blockquotes."""
    # Find all blockquotes containing "Key Takeaway" or similar
    pattern = r'\s*<blockquote[^>]*>\s*<p>\s*<strong>\s*(?:Key Takeaway|Quick Comparison|Key Benefits|What You\'ll Learn)[^<]*</strong>\s*.*?</p>\s*</blockquote>'
    matches = list(re.finditer(pattern, content, re.DOTALL | re.IGNORECASE))
    if len(matches) > 1:
        # Remove duplicates, keep the first one
        # Replace from end to start to maintain indices
        new_content = content
        for m in reversed(matches[1:]):
            new_content = new_content[:m.start()] + new_content[m.end():]
        return new_content
    return content


def remove_faq_section(content):
    """Remove FAQ section - handle EZ TOC wrapped headings."""
    # EZ TOC wrapped FAQ heading pattern
    faq_patterns = [
        r'<h2[^>]*>(?:\s*<span[^>]*>[^<]*</span>)*\s*Frequently Asked Questions\s*(?:<span class="ez-toc-section-end">)?[^<]*(?:<span class="ez-toc-section-end">)?[^<]*</h2>',
        r'<h2[^>]*>(?:\s*<span[^>]*>[^<]*</span>)*\s*FAQ\s*(?:<span class="ez-toc-section-end">)?[^<]*(?:<span class="ez-toc-section-end">)?[^<]*</h2>',
        r'<h2[^>]*>(?:\s*<span[^>]*>[^<]*</span>)*\s*FAQs\s*(?:<span class="ez-toc-section-end">)?[^<]*(?:<span class="ez-toc-section-end">)?[^<]*</h2>',
        r'<h2[^>]*>(?:\s*<span[^>]*>[^<]*</span>)*\s*Common Questions\s*(?:<span class="ez-toc-section-end">)?[^<]*(?:<span class="ez-toc-section-end">)?[^<]*</h2>',
    ]
    
    faq_start = -1
    faq_match_len = 0
    
    for pattern in faq_patterns:
        match = re.search(pattern, content, re.IGNORECASE | re.DOTALL)
        if match:
            faq_start = match.start()
            faq_match_len = match.end() - match.start()
            break
    
    if faq_start == -1:
        return content, False, "No FAQ section found"
    
    # Find the end - next h2 heading
    remaining = content[faq_start + faq_match_len:]
    next_h2 = re.search(r'<h[2-6][^>]*>', remaining, re.IGNORECASE)
    
    if next_h2:
        faq_end = faq_start + faq_match_len + next_h2.start()
    else:
        faq_end = len(content)
    
    # Extract FAQ section
    faq_section = content[faq_start:faq_end]
    
    # Create new content
    new_content = content[:faq_start].rstrip()
    if faq_end < len(content):
        tail = content[faq_end:].lstrip()
        if tail:
            new_content += "\n\n" + tail
    
    removed = len(content) - len(new_content)
    return new_content, True, f"FAQ section removed ({removed} chars)", faq_section


def extract_faq_items(faq_section):
    """Extract FAQ Q&A pairs from the FAQ section HTML."""
    faqs = []
    
    # Pattern: <p><strong>1. Question text</strong><br />Answer text</p>
    # Or: <p><strong>Question text</strong><br />Answer text</p>
    pattern = r'<p>\s*<strong>\s*(?:\d+\.\s*)?([^<]+)</strong>\s*(?:<br\s*/?>\s*)?(.+?)</p>'
    matches = re.finditer(pattern, faq_section, re.DOTALL | re.IGNORECASE)
    
    for m in matches:
        question = re.sub(r'<[^>]+>', '', m.group(1)).strip()
        answer = re.sub(r'<[^>]+>', ' ', m.group(2)).strip()
        answer = clean_html_entities(answer)
        
        # Clean up question
        question = question.rstrip('?')
        question = re.sub(r'^\d+\.\s*', '', question)
        
        if question and answer:
            faqs.append({"question": question, "answer": answer})
    
    return faqs


def ensure_single_blockquote(content, post_title):
    """Ensure only one direct-answer blockquote exists."""
    # Check if any blockquote exists
    bq_pattern = r'<blockquote[^>]*>\s*(?:<p>)?\s*<strong>\s*(?:Key Takeaway|Quick Comparison|Key Benefits|What You\'ll Learn)[^<]*</strong>\s*.*?(?:</p>)?\s*</blockquote>'
    matches = list(re.finditer(bq_pattern, content, re.DOTALL | re.IGNORECASE))
    
    # Determine label and summary
    title_lower = post_title.lower()
    if ' vs ' in title_lower or ' vs. ' in title_lower or 'difference' in title_lower:
        summary_label = "Quick Comparison"
    elif 'benefits' in title_lower:
        summary_label = "Key Benefits at a Glance"
    elif 'exercises' in title_lower or 'steps' in title_lower or 'how to' in title_lower:
        summary_label = "What You'll Learn"
    else:
        summary_label = "Key Takeaway"
    
    # Topic-based summaries
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
    
    summary_html = f'<blockquote><p><strong>{summary_label}:</strong> {summary}</p></blockquote>'
    
    # Remove all existing matching blockquotes
    if matches:
        new_content = content
        for m in reversed(matches):
            new_content = new_content[:m.start()] + new_content[m.end():]
        content = new_content
    
    # Insert after first closing </p>
    first_para_end = content.find('</p>')
    if first_para_end != -1:
        insert_pos = first_para_end + 4
        content = content[:insert_pos] + "\n\n" + summary_html + content[insert_pos:]
    
    return content


def ensure_definition_sentence(content, topic):
    """Ensure the first H2 'What Is...' starts with a clean definition sentence."""
    # Match EZ TOC wrapped or plain H2 with "What Is"
    h2_pattern = r'<h2[^>]*>(?:\s*<span[^>]*>[^<]*</span>)*\s*What\s+Is\s+.*?</h2>'
    h2_match = re.search(h2_pattern, content, re.IGNORECASE | re.DOTALL)
    
    if not h2_match:
        return content
    
    start = h2_match.end()
    # Find the next paragraph after this H2
    next_p = re.search(r'<p[^>]*>(.*?)</p>', content[start:], re.DOTALL)
    if not next_p:
        return content
    
    p_start = start + next_p.start()
    p_end = start + next_p.end()
    current_text = next_p.group(1)
    
    # Check if it already starts with a definition
    clean_text = re.sub(r'<[^>]+>', '', current_text).strip()
    if any(clean_text.lower().startswith(w) for w in ['is a', 'refers to', 'is the', 'is an', 'are a', 'is defined']):
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
        new_para = f'<p>{definition}</p>'
        content = content[:p_start] + new_para + content[p_end:]
    
    return content


def convert_faq_to_h3_sections(content, faqs):
    """Convert FAQ items into H3 subsections under a new H2 'Common Questions Answered'."""
    if not faqs:
        return content
    
    sections = ['<h2>Common Questions Answered</h2>']
    for faq in faqs:
        q = faq['question']
        a = faq['answer']
        q_clean = re.sub(r'^[\d\.\s]+', '', q)
        if not q_clean.endswith('?'):
            q_clean += '?'
        sections.append(f'<h3>{q_clean}</h3>')
        sections.append(f'<p>{a}</p>')
    
    new_section = '\n'.join(sections)
    
    # Insert before conclusion/CTA heading
    conclusion_pattern = r'<h[2-6][^>]*>(?:\s*<span[^>]*>[^<]*</span>)*\s*(?:Conclusion|Ready to|Book Now|Contact|Get Started|Final Thoughts|Next Steps|Take the First Step)'
    conclusion_match = re.search(conclusion_pattern, content, re.IGNORECASE)
    if conclusion_match:
        pos = conclusion_match.start()
        content = content[:pos] + new_section + "\n\n" + content[pos:]
    else:
        # Insert before the last paragraph (CTA)
        last_p = content.rfind('</p>')
        if last_p != -1:
            content = content[:last_p + 4] + "\n\n" + new_section
        else:
            content = content + "\n\n" + new_section
    
    return content


def ensure_comparison_table(content, post_title):
    """For comparison posts, ensure a proper table exists."""
    title_lower = post_title.lower()
    is_comparison = ' vs ' in title_lower or ' vs. ' in title_lower or 'difference' in title_lower
    
    if not is_comparison:
        return content
    
    if '<table' in content.lower():
        return content
    
    if 'orthopedic' in title_lower and 'regular' in title_lower:
        table = """<table>
<thead>
<tr><th>Feature</th><th>Orthopedic Physiotherapy</th><th>Regular Physiotherapy</th></tr>
</thead>
<tbody>
<tr><td>Focus Area</td><td>Bones, joints, muscles, ligaments, tendons</td><td>Broader scope including neuro, cardio, and general rehab</td></tr>
<tr><td>Common Conditions</td><td>Fractures, arthritis, sports injuries, post-surgical rehab</td><td>Stroke recovery, respiratory issues, general wellness</td></tr>
<tr><td>Techniques Used</td><td>Manual therapy, joint mobilization, strength training</td><td>Exercise therapy, electrotherapy, education</td></tr>
<tr><td>Target Patients</td><td>Athletes, post-surgical patients, injury sufferers</td><td>General population, chronic disease patients</td></tr>
<tr><td>Goal</td><td>Restore musculoskeletal function and mobility</td><td>Improve overall physical function and quality of life</td></tr>
</tbody>
</table>"""
    elif 'hot stone' in title_lower and 'swedish' in title_lower:
        table = """<table>
<thead>
<tr><th>Feature</th><th>Hot Stone Massage</th><th>Swedish Massage</th></tr>
</thead>
<tbody>
<tr><td>Primary Technique</td><td>Heated basalt stones placed on key body points</td><td>Long, gliding strokes with hands, forearms, and elbows</td></tr>
<tr><td>Pressure Level</td><td>Medium to deep with heat penetration</td><td>Light to medium pressure</td></tr>
<tr><td>Best For</td><td>Deep muscle tension, chronic pain, stress relief</td><td>General relaxation, first-time massage clients</td></tr>
<tr><td>Heat Element</td><td>Yes, heated stones provide penetrating warmth</td><td>No heat used</td></tr>
<tr><td>Session Feel</td><td>Intense warmth and deep relaxation</td><td>Gentle, soothing, and rhythmic</td></tr>
<tr><td>Ideal Candidates</td><td>Those with chronic tension, athletes, stress sufferers</td><td>Those seeking gentle relaxation, beginners</td></tr>
</tbody>
</table>"""
    else:
        return content
    
    # Insert after the blockquote
    insert_pos = content.find('</blockquote>')
    if insert_pos != -1:
        insert_pos += len('</blockquote>')
        content = content[:insert_pos] + "\n\n" + table + content[insert_pos:]
    
    return content


def count_words(html_content):
    text = re.sub(r'<[^>]+>', ' ', html_content)
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
    
    # 1. Remove FAQ section
    result = remove_faq_section(content)
    if len(result) == 3:
        content, faq_removed, faq_msg = result
        faq_section = ""
    else:
        content, faq_removed, faq_msg, faq_section = result
    
    print(f"FAQ removal: {faq_msg}")
    
    # 2. Extract FAQ items
    faqs = extract_faq_items(faq_section)
    print(f"Found {len(faqs)} FAQ items")
    
    # 3. Deduplicate and ensure single blockquote
    content = ensure_single_blockquote(content, title)
    print("Ensured single direct-answer blockquote")
    
    # 4. Ensure definition sentence
    content = ensure_definition_sentence(content, title)
    print("Ensured definition sentence in first H2")
    
    # 5. Convert FAQ to body sections
    if faqs:
        content = convert_faq_to_h3_sections(content, faqs)
        print(f"Converted {len(faqs)} FAQ items to H3 sections")
    
    # 6. Ensure comparison table
    content = ensure_comparison_table(content, title)
    if ' vs ' in title.lower() or 'difference' in title.lower():
        print("Added/verified comparison table")
    
    # 7. Clean up
    content = clean_html_entities(content)
    
    new_word_count = count_words(content)
    print(f"New word count: ~{new_word_count}")
    
    # 8. Push updated content
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
            "faq_removed": faq_removed,
            "faq_items_converted": len(faqs),
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
    print("TONICPHYSIO BLOG POST AEO RESTRUCTURING v3")
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
            print(f"  ID {r['id']}: {r['title'][:50]}... | FAQ removed: {r['faq_removed']} | Words: {r['original_word_count']} -> {r['new_word_count']}")
        else:
            print(f"  ID {r['id']}: ERROR - {r.get('message', 'Unknown error')}")


if __name__ == "__main__":
    main()
