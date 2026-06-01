#!/usr/bin/env python3
"""
Create TonicPhysio local landing pages for Campbellville, Acton, Georgetown
"""
import json, base64, urllib.request, urllib.error

WP_URL = "https://tonicphysio.com"
USER = "Dan"
PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"
auth_str = base64.b64encode(f"{USER}:{PASS}".encode()).decode()

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

PAGES = [
    {
        "title": "Physiotherapy in Campbellville, Ontario",
        "slug": "physiotherapy-in-campbellville",
        "content": """
<h1>Physiotherapy in Campbellville, Ontario</h1>

<p>If you live in Campbellville or the surrounding Halton Hills area and need professional physiotherapy care, Tonic Physio in nearby Milton offers comprehensive rehabilitation services without the long drive to a major city. Our clinic is conveniently located just minutes from Campbellville, making it easy to access expert physiotherapy, massage therapy, and specialized treatments.</p>

<h2>Why Campbellville Residents Choose Tonic Physio</h2>

<p>Campbellville is a quiet rural community with limited healthcare options. While the village charm is part of what makes it special, finding specialized physiotherapy services locally can be challenging. Tonic Physio bridges this gap by providing Campbellville residents with the same high-quality care available in larger urban centers, right here in Milton.</p>

<p>Our clinic understands the needs of rural and semi-rural patients. We offer flexible appointment times, direct billing to most insurance providers, and WSIB coverage for workplace injuries. Whether you are recovering from a farm-related injury, a sports accident, or dealing with chronic pain, our team creates personalized treatment plans designed around your lifestyle.</p>

<h2>Services Available for Campbellville Patients</h2>

<ul>
<li><strong>Physiotherapy</strong> — Manual therapy, exercise prescription, and rehabilitation for injuries, surgery recovery, and chronic conditions.</li>
<li><strong>Registered Massage Therapy</strong> — Deep tissue, sports massage, relaxation massage, and prenatal massage to relieve tension and promote healing.</li>
<li><strong>Manual Osteopathy</strong> — Gentle hands-on techniques to restore mobility, reduce pain, and improve overall body function.</li>
<li><strong>Shockwave Therapy</strong> — Advanced treatment for chronic tendon pain, plantar fasciitis, and calcified shoulders.</li>
<li><strong>Custom Orthotics and Bracing</strong> — Professional fitting for foot orthotics, knee braces, and compression garments.</li>
<li><strong>Motor Vehicle Accident Recovery</strong> — Complete MVA rehabilitation with direct insurance billing.</li>
<li><strong>WSIB Care Programs</strong> — Workplace injury treatment and return-to-work planning.</li>
</ul>

<h2>Getting to Tonic Physio from Campbellville</h2>

<p>Our clinic is located at <a href="https://tonicphysio.com/contact/">[Clinic Address in Milton]</a>, approximately a 15-minute drive from Campbellville via Regional Road 25. We offer ample parking and ground-floor accessibility for patients with mobility concerns.</p>

<p><strong>Driving directions:</strong> Take Regional Road 25 south toward Milton. Turn left onto Main Street and continue to our location in the heart of Milton's medical district.</p>

<h2>Book Your Appointment Today</h2>

<p>Campbellville residents do not need a doctor's referral to start physiotherapy. Call us today or <a href="https://tonicphysio.com/contact/">book online</a> to schedule your initial assessment. Most insurance plans cover physiotherapy services, and we handle direct billing so you can focus on recovery.</p>

<p><em>Tonic Physio — Milton's trusted physiotherapy clinic, proudly serving Campbellville and all of Halton Region.</em></p>
""",
        "yoast_title": "Physiotherapy in Campbellville | Tonic Physio Milton",
        "yoast_desc": "Expert physiotherapy near Campbellville at Tonic Physio in Milton. No referral needed. Direct billing. Book your appointment today.",
        "yoast_kw": "physiotherapy Campbellville Ontario"
    },
    {
        "title": "Physiotherapy in Acton, Ontario",
        "slug": "physiotherapy-in-acton",
        "content": """
<h1>Physiotherapy in Acton, Ontario</h1>

<p>Acton residents looking for professional physiotherapy care no longer need to travel far. Tonic Physio in Milton serves the Acton community with comprehensive rehabilitation services, including physiotherapy, massage therapy, osteopathy, and specialized treatments for sports injuries, workplace accidents, and chronic conditions.</p>

<h2>Physiotherapy Care Close to Home</h2>

<p>Acton, known as Leathertown, is a growing community in Halton Hills with an active population that values health and wellness. From weekend hockey players to factory workers and seniors staying active, Acton residents need accessible physiotherapy that fits their busy schedules. Tonic Physio is just a short drive away, offering same-day and next-day appointments for urgent needs.</p>

<p>Our team of registered physiotherapists, massage therapists, and manual osteopaths treats a wide range of conditions. Whether you are dealing with back pain from desk work, recovering from shoulder surgery, or managing arthritis, we develop evidence-based treatment plans that get results.</p>

<h2>What Acton Patients Can Expect</h2>

<ul>
<li><strong>Same-day appointments</strong> available for acute injuries and flare-ups</li>
<li><strong>Direct insurance billing</strong> — no upfront payment for most plans</li>
<li><strong>WSIB approved</strong> for workplace injury claims</li>
<li><strong>MVA rehabilitation</strong> with full insurance coordination</li>
<li><strong>Evening and weekend hours</strong> to fit your schedule</li>
</ul>

<h2>Our Services for the Acton Community</h2>

<p><a href="https://tonicphysio.com/physiotherapy-in-milton/">Physiotherapy</a> — Personalized rehabilitation programs for pain relief, injury recovery, and mobility improvement. We treat everything from sprains and strains to post-surgical rehabilitation and neurological conditions.</p>

<p><a href="https://tonicphysio.com/registered-massage-therapy/">Registered Massage Therapy</a> — Therapeutic massage for muscle tension, stress relief, sports recovery, and prenatal care. Our RMTs are trained in deep tissue, Swedish, hot stone, and sports massage techniques.</p>

<p><a href="https://tonicphysio.com/manual-osteopathy-milton/">Manual Osteopathy</strong> — Gentle, hands-on treatment that restores balance to the musculoskeletal system, improves circulation, and reduces pain without medication.</p>

<p><a href="https://tonicphysio.com/shockwave-therapy/">Shockwave Therapy</a> — Non-invasive treatment for chronic tendon conditions like tennis elbow, golfer's elbow, plantar fasciitis, and calcific shoulder tendinitis.</p>

<h2>How to Reach Us from Acton</h2>

<p>Tonic Physio is located in central Milton, approximately 20 minutes from Acton via Highway 7 and Regional Road 25. We offer free parking and are fully wheelchair accessible.</p>

<p><strong>Address:</strong> [Clinic Address], Milton, ON<br/>
<strong>Phone:</strong> [Clinic Phone]<br/>
<strong>Hours:</strong> Monday to Friday 8am-7pm, Saturday 9am-3pm</p>

<h2>Start Your Recovery Today</h2>

<p>You do not need a referral to begin physiotherapy in Ontario. Contact Tonic Physio today to book your initial assessment. We will evaluate your condition, explain your treatment options, and create a plan that gets you back to doing what you love.</p>

<p><em>Serving Acton, Georgetown, Campbellville, and all of Halton Hills from our Milton location.</em></p>
""",
        "yoast_title": "Physiotherapy in Acton | Tonic Physio Milton",
        "yoast_desc": "Professional physiotherapy near Acton at Tonic Physio in Milton. Same-day appointments. Direct billing. Call or book online today.",
        "yoast_kw": "physiotherapy Acton Ontario"
    },
    {
        "title": "Physiotherapy in Georgetown, Ontario",
        "slug": "physiotherapy-in-georgetown",
        "content": """
<h1>Physiotherapy in Georgetown, Ontario</h1>

<p>Georgetown residents searching for high-quality physiotherapy need look no further than Tonic Physio in Milton. Just minutes from Georgetown, our clinic provides the full spectrum of rehabilitation services — from physiotherapy and massage therapy to shockwave treatment and custom orthotics — all under one roof.</p>

<h2>Why Georgetown Patients Travel to Tonic Physio</h2>

<p>Georgetown is one of the largest communities in Halton Hills, with a diverse population that includes young families, active retirees, industrial workers, and competitive athletes. While Georgetown has some healthcare services, finding specialized physiotherapy with advanced treatments like shockwave therapy, manual osteopathy, and comprehensive MVA rehabilitation often requires traveling to a larger center.</p>

<p>Tonic Physio fills this gap. Our Milton clinic offers Georgetown residents access to specialized care without the drive to Brampton, Mississauga, or Toronto. We combine evidence-based physiotherapy with advanced modalities and a patient-first approach that prioritizes your goals.</p>

<h2>Comprehensive Services for Georgetown</h2>

<ul>
<li><strong><a href="https://tonicphysio.com/physiotherapy-in-milton/">Physiotherapy</a></strong> — Injury assessment, manual therapy, therapeutic exercise, and rehabilitation for all musculoskeletal conditions.</li>
<li><strong><a href="https://tonicphysio.com/registered-massage-therapy/">Massage Therapy</a></strong> — Deep tissue, relaxation, sports, prenatal, and post-natal massage for pain relief and recovery.</li>
<li><strong><a href="https://tonicphysio.com/manual-osteopathy-milton/">Manual Osteopathy</a></strong> — Holistic hands-on treatment for joint mobility, pain reduction, and whole-body health.</li>
<li><strong><a href="https://tonicphysio.com/shockwave-therapy/">Shockwave Therapy</a></strong> — Breakthrough treatment for chronic tendon pain and calcification.</li>
<li><strong><a href="https://tonicphysio.com/custom-orthotics/">Custom Orthotics</a></strong> — Professional gait analysis and custom-fitted orthotic devices for foot, knee, and hip alignment.</li>
<li><strong><a href="https://tonicphysio.com/motor-vehicle-accident-physiotherapy/">MVA Rehabilitation</a></strong> — Complete car accident recovery with direct insurance billing and documentation support.</li>
<li><strong><a href="https://tonicphysio.com/wsib-care-programs/">WSIB Programs</a></strong> — Workplace injury treatment, return-to-work planning, and claims assistance.</li>
</ul>

<h2>Convenient Location for Georgetown Residents</h2>

<p>Tonic Physio is located in central Milton, approximately 15-20 minutes from most Georgetown neighborhoods. Take Regional Road 25 or Highway 7 east to Main Street Milton. Our clinic is easily accessible with ample parking and is located on the ground floor for patients with mobility needs.</p>

<h2>Book Your Georgetown Physiotherapy Appointment</h2>

<p>No referral is required to see a physiotherapist in Ontario. Call us directly or <a href="https://tonicphysio.com/contact/">book your appointment online</a>. We offer flexible scheduling including early morning, evening, and Saturday appointments to accommodate Georgetown commuters and busy families.</p>

<p><strong>New patients welcome.</strong> Most extended health insurance plans cover physiotherapy and massage therapy. We offer direct billing to save you time and paperwork.</p>

<p><em>Tonic Physio — Expert physiotherapy for Georgetown, Milton, and all of Halton Region.</em></p>
""",
        "yoast_title": "Physiotherapy in Georgetown | Tonic Physio Milton",
        "yoast_desc": "Trusted physiotherapy near Georgetown at Tonic Physio in Milton. Full-service rehab, direct billing, same-day appointments. Book now.",
        "yoast_kw": "physiotherapy Georgetown Ontario"
    }
]

results = []
for page_data in PAGES:
    print(f"Creating: {page_data['title']}...")
    
    data = {
        "title": page_data["title"],
        "slug": page_data["slug"],
        "content": page_data["content"],
        "status": "draft",
        "meta": {
            "_yoast_wpseo_title": page_data["yoast_title"],
            "_yoast_wpseo_metadesc": page_data["yoast_desc"],
            "_yoast_wpseo_focuskw": page_data["yoast_kw"]
        }
    }
    
    try:
        result = create_page(data)
        if "id" in result:
            results.append(f"✅ {page_data['title']} → ID: {result['id']} — DRAFT")
            print(f"  ✅ ID {result['id']} created")
        else:
            results.append(f"❌ {page_data['title']} → {result.get('message', 'Error')}")
            print(f"  ❌ Error: {result.get('message', 'Unknown')}")
    except Exception as e:
        results.append(f"❌ {page_data['title']} → {str(e)}")
        print(f"  ❌ Exception: {e}")

print(f"\n{'='*60}")
print(f"Created {len([r for r in results if '✅' in r])} of {len(PAGES)} local pages")
for r in results:
    print(r)
