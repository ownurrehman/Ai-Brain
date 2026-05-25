#!/usr/bin/env python3
"""
Create TonicPhysio Milton hub page — main local SEO landing page
"""
import json, base64, urllib.request, urllib.error

WP_URL = "https://tonicphysio.com"
USER = "Dan"
WP_PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"
auth_str = base64.b64encode(f"{USER}:{WP_PASS}".encode()).decode()

def create_page(data):
    req = urllib.request.Request(
        f"{WP_URL}/wp-json/wp/v2/pages",
        data=json.dumps(data).encode(),
        headers={
            "Authorization": f"Basic {auth_str}",
            "Content-Type": "application/json"
        }
    )
    with urllib.request.urlopen(req, timeout=60) as resp:
        return json.loads(resp.read().decode())

# Build comprehensive hub page content
content = """
<h1>Physiotherapy in Milton, Ontario: Complete Care at Tonic Physio</h1>

<p>Welcome to Tonic Physio, Milton's leading physiotherapy and rehabilitation centre. Whether you are recovering from an injury, managing chronic pain, or seeking preventive care, our team of registered physiotherapists, massage therapists, and manual osteopaths delivers personalized treatment designed around your goals. Located in the heart of Milton, we serve patients from across Halton Region including <a href="https://tonicphysio.com/physiotherapy-in-campbellville/">Campbellville</a>, <a href="https://tonicphysio.com/physiotherapy-in-acton/">Acton</a>, and <a href="https://tonicphysio.com/physiotherapy-in-georgetown/">Georgetown</a>.</p>

<h2>Why Choose Tonic Physio in Milton</h2>

<ul>
<li><strong>Award-winning clinic:</strong> Recognized as Milton's Best Rehabilitation Centre in 2025</li>
<li><strong>Multidisciplinary team:</strong> Physiotherapists, RMTs, osteopaths, and kinesiologists working together</li>
<li><strong>Direct billing:</strong> We bill your insurance directly — no paperwork, no waiting</li>
<li><strong>WSIB and MVA approved:</strong> Full coverage for workplace injuries and car accident rehabilitation</li>
<li><strong>Same-day appointments:</strong> Book today, start recovery today</li>
<li><strong>No referral needed:</strong> Ontario residents can self-refer to physiotherapy</li>
</ul>

<h2>Our Physiotherapy Services in Milton</h2>

<h3><a href="https://tonicphysio.com/physiotherapy-in-milton/">General Physiotherapy</a></h3>
<p>Comprehensive musculoskeletal assessment and treatment for pain, injuries, surgery recovery, and mobility improvement. Our physiotherapists use manual therapy, therapeutic exercise, electrotherapy, and education to restore function and prevent recurrence.</p>

<h3><a href="https://tonicphysio.com/registered-massage-therapy/">Registered Massage Therapy</a></h3>
<p>Therapeutic massage for muscle tension, stress relief, sports recovery, and chronic pain management. Available modalities include deep tissue massage, Swedish massage, hot stone massage, sports massage, prenatal massage, and lymphatic drainage.</p>

<h3><a href="https://tonicphysio.com/manual-osteopathy-milton/">Manual Osteopathy</a></h3>
<p>Gentle, hands-on treatment that restores balance to your musculoskeletal system. Manual osteopathy addresses joint restrictions, tissue tension, and circulatory issues to reduce pain and improve whole-body function.</p>

<h3><a href="https://tonicphysio.com/shockwave-therapy/">Shockwave Therapy</a></h3>
<p>Advanced non-invasive treatment for chronic tendon conditions including plantar fasciitis, tennis elbow, golfer's elbow, calcific shoulder tendinitis, and Achilles tendinopathy. Break through plateaus when other treatments have stalled.</p>

<h3><a href="https://tonicphysio.com/motor-vehicle-accident-physiotherapy/">Motor Vehicle Accident Rehabilitation</a></h3>
<p>Complete MVA recovery programs for whiplash, soft tissue injuries, concussions, and post-traumatic pain. We coordinate directly with your insurance provider and handle all documentation so you can focus on healing.</p>

<h3><a href="https://tonicphysio.com/wsib-care-programs/">WSIB Care Programs</a></h3>
<p>Workplace injury treatment and return-to-work planning approved by the Workplace Safety and Insurance Board. From initial assessment to functional capacity evaluation, we guide injured workers through every step of recovery.</p>

<h3><a href="https://tonicphysio.com/custom-orthotics/">Custom Orthotics</a></h3>
<p>Professional gait analysis and custom-fitted orthotic devices to correct biomechanical issues, reduce foot and knee pain, and improve alignment. Covered by most extended health insurance plans.</p>

<h3><a href="https://tonicphysio.com/custom-and-otc-bracing/">Custom and OTC Bracing</a></h3>
<p>Expert fitting for knee braces, ankle supports, back braces, and compression garments. Custom options available for post-surgical recovery and complex joint instability.</p>

<h3><a href="https://tonicphysio.com/compression-socks/">Compression Therapy</a></h3>
<p>Medical-grade compression socks and sleeves for circulation support, edema management, and athletic recovery. Proper fitting by trained professionals.</p>

<h3><a href="https://tonicphysio.com/tmj-treatment/">TMJ Treatment</a></h3>
<p>Specialized jaw pain and temporomandibular joint dysfunction treatment. Manual therapy, exercises, and modalities to relieve clicking, locking, and chronic facial pain.</p>

<h3><a href="https://tonicphysio.com/b-pulse-pelvic-floor-strengthening/">B-Pulse Pelvic Floor Strengthening</a></h3>
<p>Non-invasive, clothed pelvic floor rehabilitation using electromagnetic technology. Effective for incontinence, postpartum recovery, and pelvic weakness in men and women.</p>

<h2>Conditions We Treat in Milton</h2>

<ul>
<li><a href="https://tonicphysio.com/physiotherapy-in-milton/back-and-neck-pain/">Back and Neck Pain</a></li>
<li><a href="https://tonicphysio.com/physiotherapy-in-milton/sciatica-treatment/">Sciatica and Nerve Pain</a></li>
<li><a href="https://tonicphysio.com/physiotherapy-in-milton/sports-physiotherapy/">Sports Injuries</a></li>
<li><a href="https://tonicphysio.com/physiotherapy-in-milton/joint-pain-and-stiffness/">Joint Pain and Arthritis</a></li>
<li><a href="https://tonicphysio.com/physiotherapy-in-milton/headaches-and-migraines-treatment-2/">Headaches and Migraines</a></li>
<li><a href="https://tonicphysio.com/physiotherapy-in-milton/frozen-shoulder-treatment/">Frozen Shoulder</a></li>
<li><a href="https://tonicphysio.com/physiotherapy-in-milton/herniated-disc-treatment/">Herniated Disc</a></li>
<li><a href="https://tonicphysio.com/physiotherapy-in-milton/osteoporosis/">Osteoporosis</a></li>
<li><a href="https://tonicphysio.com/physiotherapy-in-milton/post-surgical-rehabilitation-2/">Post-Surgical Rehabilitation</a></li>
<li><a href="https://tonicphysio.com/physiotherapy-in-milton/vestibular-rehabilitation/">Dizziness and Balance Issues</a></li>
<li><a href="https://tonicphysio.com/physiotherapy-in-milton/concussion-management/">Concussion Management</a></li>
<li><a href="https://tonicphysio.com/physiotherapy-in-milton/geriatric-physiotherapy/">Seniors Mobility and Fall Prevention</a></li>
</ul>

<h2>Serving Milton and Surrounding Communities</h2>

<p>Tonic Physio proudly serves patients from across Halton Region and beyond. Our convenient Milton location makes us accessible to residents of:</p>

<ul>
<li><a href="https://tonicphysio.com/physiotherapy-in-campbellville/">Campbellville</a></li>
<li><a href="https://tonicphysio.com/physiotherapy-in-acton/">Acton</a></li>
<li><a href="https://tonicphysio.com/physiotherapy-in-georgetown/">Georgetown</a></li>
<li>Milton</li>
<li>Oakville</li>
<li>Burlington</li>
<li>Mississauga</li>
</ul>

<h2>Book Your Physiotherapy Appointment in Milton</h2>

<p>Ready to start your recovery? <a href="https://tonicphysio.com/contact/">Contact Tonic Physio today</a> to schedule your initial assessment. No referral is required, and most insurance plans cover our services with direct billing available.</p>

<p><strong>Phone:</strong> [Clinic Phone]<br/>
<strong>Address:</strong> [Clinic Address], Milton, ON<br/>
<strong>Hours:</strong> Monday to Friday 8am-7pm, Saturday 9am-3pm</p>

<p><em>Tonic Physio — Your trusted physiotherapy clinic in Milton, Ontario.</em></p>
"""

page_data = {
    "title": "Physiotherapy Milton | Complete Rehab Services | Tonic Physio",
    "slug": "physiotherapy-milton",
    "content": content,
    "status": "draft",
    "meta": {
        "_yoast_wpseo_title": "Physiotherapy Milton | Complete Rehab Services | Tonic Physio",
        "_yoast_wpseo_metadesc": "Expert physiotherapy in Milton at Tonic Physio. Physio, massage, osteopathy, shockwave, MVA rehab, WSIB. Same-day appointments. Direct billing.",
        "_yoast_wpseo_focuskw": "physiotherapy milton"
    }
}

print("Creating: Physiotherapy Milton Hub Page...")

try:
    result = create_page(page_data)
    if "id" in result:
        print(f"✅ ID {result['id']} created as DRAFT")
        print(f"   URL: https://tonicphysio.com/physiotherapy-milton/")
    else:
        print(f"❌ Error: {result.get('message', 'Unknown')}")
except Exception as e:
    print(f"❌ Exception: {e}")
