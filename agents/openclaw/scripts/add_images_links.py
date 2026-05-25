#!/usr/bin/env python3
"""
Add featured images and service page links to 6 TonicPhysio blog posts.
"""
import json, base64, re
from urllib.request import Request, urlopen

WP_URL = "https://tonicphysio.com/wp-json/wp/v2"
WP_USER = "Dan"
WP_APP_PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"
HEADERS = {
    "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    "Accept": "application/json",
    "Referer": "https://tonicphysio.com/",
}

POST_CONFIG = {
    13030: {
        "featured_media": 12852,
        "inline_img": {
            "id": 12852,
            "url": "https://tonicphysio.com/wp-content/uploads/2026/05/acupuncture_dryneedling.jpg",
            "alt": "Acupuncture treatment at Tonic Physio in Milton"
        },
        "services": [
            ("Acupuncture Therapy", "https://tonicphysio.com/physiotherapy-in-milton/acupuncture-therapy/"),
            ("Registered Massage Therapy", "https://tonicphysio.com/registered-massage-therapy/"),
            ("Physiotherapy in Milton", "https://tonicphysio.com/physiotherapy-in-milton/"),
        ]
    },
    13032: {
        "featured_media": 12727,
        "inline_img": {
            "id": 12727,
            "url": "https://tonicphysio.com/wp-content/uploads/2026/05/back-pain-featured-scaled.jpg",
            "alt": "Back and neck pain relief at Tonic Physio in Milton"
        },
        "services": [
            ("Cervical Spondylosis Treatment", "https://tonicphysio.com/physiotherapy-in-milton/cervical-spondylosis/"),
            ("Back and Neck Pain Relief", "https://tonicphysio.com/physiotherapy-in-milton/back-and-neck-pain/"),
            ("Physiotherapy in Milton", "https://tonicphysio.com/physiotherapy-in-milton/"),
        ]
    },
    13033: {
        "featured_media": 12850,
        "inline_img": {
            "id": 12850,
            "url": "https://tonicphysio.com/wp-content/uploads/2026/05/chronic_pain.jpg",
            "alt": "Chronic pain management at Tonic Physio"
        },
        "services": [
            ("Orthopedic Physiotherapy", "https://tonicphysio.com/physiotherapy-in-milton/orthopedic-physiotherapy/"),
            ("Physiotherapy in Milton", "https://tonicphysio.com/physiotherapy-in-milton/"),
            ("Joint Pain and Stiffness Treatment", "https://tonicphysio.com/physiotherapy-in-milton/joint-pain-and-stiffness/"),
        ]
    },
    13034: {
        "featured_media": 12849,
        "inline_img": {
            "id": 12849,
            "url": "https://tonicphysio.com/wp-content/uploads/2026/05/sports_physio.jpg",
            "alt": "Physiotherapy services at Tonic Physio in Milton"
        },
        "services": [
            ("Pediatric Physiotherapy", "https://tonicphysio.com/physiotherapy-in-milton/pediatric-physiotherapy/"),
            ("Physiotherapy in Milton", "https://tonicphysio.com/physiotherapy-in-milton/"),
            ("Sports Physiotherapy", "https://tonicphysio.com/physiotherapy-in-milton/sports-physiotherapy/"),
        ]
    },
    13039: {
        "featured_media": 12835,
        "inline_img": {
            "id": 12835,
            "url": "https://tonicphysio.com/wp-content/uploads/2026/05/massage-recovery-unique.jpg",
            "alt": "Massage therapy recovery at Tonic Physio"
        },
        "services": [
            ("Lymphatic Drainage Massage", "https://tonicphysio.com/registered-massage-therapy/lymphatic-drainage-massage-milton/"),
            ("Registered Massage Therapy", "https://tonicphysio.com/registered-massage-therapy/"),
            ("Relaxation Massage", "https://tonicphysio.com/registered-massage-therapy/relaxation-massage-in-milton/"),
        ]
    },
    13040: {
        "featured_media": 13173,
        "inline_img": {
            "id": 13173,
            "url": "https://tonicphysio.com/wp-content/uploads/2026/05/Pelvic-floor-Physiotherapy-in-milton.png",
            "alt": "Pelvic floor physiotherapy at Tonic Physio in Milton"
        },
        "services": [
            ("Post-Natal Massage", "https://tonicphysio.com/registered-massage-therapy/post-natal-massage-milton/"),
            ("Pre-Natal Massage", "https://tonicphysio.com/registered-massage-therapy/pre-natal-massage-milton/"),
            ("Pelvic Floor Physiotherapy", "https://tonicphysio.com/physiotherapy-in-milton/pelvic-floor-physiotherapy/"),
        ]
    },
}

def fetch(url):
    req = Request(url)
    creds = base64.b64encode(f"{WP_USER}:{WP_APP_PASS}".encode()).decode()
    req.add_header("Authorization", f"Basic {creds}")
    for k, v in HEADERS.items():
        req.add_header(k, v)
    with urlopen(req, timeout=30) as resp:
        return json.loads(resp.read().decode())

def update_post(post_id, payload):
    url = f"{WP_URL}/posts/{post_id}"
    data = json.dumps(payload).encode()
    req = Request(url, data=data, method="POST")
    creds = base64.b64encode(f"{WP_USER}:{WP_APP_PASS}".encode()).decode()
    req.add_header("Authorization", f"Basic {creds}")
    req.add_header("Content-Type", "application/json")
    for k, v in HEADERS.items():
        req.add_header(k, v)
    with urlopen(req, timeout=30) as resp:
        return json.loads(resp.read().decode())

def build_related_services(services):
    lines = []
    lines.append('\n<h2><span class="ez-toc-section" id="Related_Services"></span>Related Services<span class="ez-toc-section-end"></span></h2>')
    lines.append('<p>If you are looking for professional support, explore our related services at Tonic Physio:</p>')
    lines.append('<ul>')
    for name, url in services:
        lines.append(f'<li><a href="{url}">{name}</a></li>')
    lines.append('</ul>')
    return '\n'.join(lines)

def add_inline_image(content, img_url, img_alt):
    """Add an inline image after the first paragraph in the body."""
    # Find the first paragraph after the TOC
    toc_end = content.rfind('</nav>')
    if toc_end > 0:
        body_start = content.find('<p>', toc_end)
    else:
        body_start = content.find('<p>')
    
    if body_start < 0:
        return content
    
    # Find end of first paragraph
    first_p_end = content.find('</p>', body_start)
    if first_p_end < 0:
        return content
    
    img_html = f'\n<figure><img src="{img_url}" alt="{img_alt}" style="max-width:100%;height:auto;" /></figure>\n'
    return content[:first_p_end + 4] + img_html + content[first_p_end + 4:]

def process_post(post_id):
    print(f"\n{'='*60}")
    print(f"Processing post {post_id}...")
    
    config = POST_CONFIG[post_id]
    
    # Fetch current post
    post = fetch(f"{WP_URL}/posts/{post_id}")
    title = post['title']['rendered']
    content = post['content']['rendered']
    
    print(f"Title: {title}")
    print(f"Current featured media: {post.get('featured_media', 0)}")
    
    # 1. Set featured image
    featured_id = config['featured_media']
    
    # 2. Add inline image
    img = config['inline_img']
    new_content = add_inline_image(content, img['url'], img['alt'])
    
    # 3. Add Related Services section (before last H2 or at end)
    services_html = build_related_services(config['services'])
    
    # Find the last H2 and insert before it
    h2_pattern = re.compile(r'<h2[^>]*>')
    h2_matches = list(h2_pattern.finditer(new_content))
    
    if h2_matches:
        # Insert before last H2
        last_h2_pos = h2_matches[-1].start()
        new_content = new_content[:last_h2_pos] + services_html + '\n' + new_content[last_h2_pos:]
    else:
        # Append at end
        new_content = new_content.rstrip() + '\n' + services_html
    
    print(f"Content length: {len(content)} -> {len(new_content)}")
    
    # Update post
    payload = {
        "featured_media": featured_id,
        "content": new_content,
        "status": "publish"
    }
    
    result = update_post(post_id, payload)
    print(f"Updated! Featured media: {result.get('featured_media', 0)} | Status: {result.get('status', 'unknown')}")
    return True

def main():
    print("=" * 60)
    print("Adding Images and Service Links to TonicPhysio Posts")
    print("=" * 60)
    
    success = 0
    for pid in POST_CONFIG:
        try:
            if process_post(pid):
                success += 1
        except Exception as e:
            print(f"Error processing post {pid}: {e}")
            import traceback
            traceback.print_exc()
    
    print(f"\n{'='*60}")
    print(f"DONE: {success}/{len(POST_CONFIG)} posts updated")
    print("=" * 60)

if __name__ == "__main__":
    main()
