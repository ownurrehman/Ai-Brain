#!/usr/bin/env python3
"""Rebuild WSIB FAQs with ElementsKit accordion, then clone that Elementor layout onto empty program pages."""
from __future__ import annotations

import copy
import hashlib
import json
import urllib.error
import urllib.request
from http.cookiejar import MozillaCookieJar
from pathlib import Path

BASE = Path(__file__).resolve().parent / "program-pages"
COOKIE = Path("/tmp/tonic-wp-cookies.txt")
NONCE = Path("/tmp/tonic-nonce.txt").read_text().strip()
ROOT = "https://tonicphysio.com/wp-json/wp/v2/pages"
UA = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36"
JANE = "https://tonicphysio.janeapp.com/"

TYPO = [
    {
        "eael_vto_writing_gradient_color": "#7C62FF",
        "eael_vto_writing_gradient_color_location": {"unit": "%", "size": 50},
        "_id": "647f783",
    },
    {
        "eael_vto_writing_gradient_color": "#FF6464",
        "eael_vto_writing_gradient_color_location": {"unit": "%", "size": 90},
        "_id": "0a4dff0",
    },
]


def hid(seed: str) -> str:
    return hashlib.md5(seed.encode()).hexdigest()[:7]


def ptag(text: str) -> str:
    return f'<p><span style="font-size: 16px;">{text}</span></p>'


def editor_intro(*paras: str) -> str:
    return "<div>" + "".join(ptag(p) for p in paras) + "</div>"


def editor_section(title: str, paras: list[str], bullets: list[str] | None = None) -> str:
    parts = [f"<div><h2>{title}</h2>"]
    for i, para in enumerate(paras):
        if bullets and i == 1:
            parts.append("<ul>" + "".join(f"<li>{b}</li>" for b in bullets) + "</ul>")
        parts.append(f"<p>{para}</p>")
    if bullets and len(paras) <= 1:
        parts.append("<ul>" + "".join(f"<li>{b}</li>" for b in bullets) + "</ul>")
    parts.append("</div>")
    return "".join(parts)


def faq_items(pairs: list[tuple[str, str]]) -> list[dict]:
    items = []
    for i, (q, a) in enumerate(pairs):
        items.append(
            {
                "acc_title": q,
                "acc_content": f"<p>{a}</p>",
                "ekit_acc_is_active": "yes" if i == 0 else "",
                "_id": hid(q),
            }
        )
    return items


def text_widget(eid: str, html: str) -> dict:
    return {
        "id": eid,
        "elType": "widget",
        "settings": {
            "editor": html,
            "typography_typography": "custom",
            "typography_font_family": "Plus Jakarta Sans",
            "typography_font_weight": "400",
            "align": "left",
            "text_color": "#000000",
            "typography_font_size": {"unit": "px", "size": 16, "sizes": []},
            "eael_vto_writing_gradient_color_repeater": copy.deepcopy(TYPO),
        },
        "elements": [],
        "widgetType": "text-editor",
    }


def image_widget(eid: str, media_id: int, url: str, alt: str) -> dict:
    return {
        "id": eid,
        "elType": "widget",
        "settings": {
            "image": {"url": url, "id": media_id, "size": "", "alt": alt, "source": "library"},
            "image_size": "full",
            "align": "center",
            "_margin": {"unit": "px", "top": "10", "right": "0", "bottom": "20", "left": "0", "isLinked": False},
        },
        "elements": [],
        "widgetType": "image",
    }


def accordion_widget(eid: str, pairs: list[tuple[str, str]]) -> dict:
    return {
        "id": eid,
        "elType": "widget",
        "elements": [],
        "widgetType": "elementskit-accordion",
        "settings": {
            "ekit_accordion_items": faq_items(pairs),
            "ekit_accordian_faq_schema": "yes",
            "ekit_accordion_background_color": "#473872",
            "ekit_accordion_title_color_close": "#FFFFFF",
            "ekit_accordion_background_close_background": "classic",
            "ekit_accordion_background_close_color": "#473872",
            "__globals__": {
                "ekit_accordion_title_color_close": "globals/colors?id=0712892",
                "ekit_accordion_background_close_color": "globals/colors?id=accent",
                "ekit_accordion_content_background_color": "globals/colors?id=text",
                "ekit_accordion_content_color": "globals/colors?id=text",
                "ekit_accordion_content_typography_typography": "globals/typography?id=text",
            },
            "ekit_accordion_content_border_radious": {
                "unit": "px",
                "top": "10",
                "right": "10",
                "bottom": "10",
                "left": "10",
                "isLinked": True,
            },
            "ekit_accordion_content_padding": {
                "unit": "px",
                "top": "10",
                "right": "10",
                "bottom": "10",
                "left": "10",
                "isLinked": True,
            },
        },
    }


def button_widget(eid: str, label: str) -> dict:
    return {
        "id": eid,
        "elType": "widget",
        "settings": {
            "text": label,
            "link": {"url": JANE, "is_external": "", "nofollow": "", "custom_attributes": ""},
            "align": "center",
            "__globals__": {
                "background_color": "globals/colors?id=accent",
                "button_background_hover_color": "globals/colors?id=secondary",
            },
            "_margin": {"unit": "px", "top": "10", "right": "0", "bottom": "30", "left": "0", "isLinked": False},
        },
        "elements": [],
        "widgetType": "button",
    }


def heading_widget(eid: str, title: str, size: str = "h2", align: str = "center") -> dict:
    return {
        "id": eid,
        "elType": "widget",
        "settings": {
            "title": title,
            "header_size": size,
            "align": align,
            "title_color": "#000000",
            "typography_typography": "custom",
            "typography_font_family": "Plus Jakarta Sans",
            "typography_font_weight": "500",
            "eael_vto_writing_gradient_color_repeater": copy.deepcopy(TYPO),
        },
        "elements": [],
        "widgetType": "heading",
    }


PAGES = {
    1798: {
        "slug": "wsib-care-programs",
        "h1": "WSIB Care Programs",
        "faq_heading": "FAQs About WSIB Care Programs",
        "yoast_title": "WSIB Care Programs in Milton | Tonic Physio",
        "yoast_desc": "Explore our WSIB care programs in Milton at Tonic Physio. We provide physiotherapy to help you recover from workplace injuries and get back to health.",
        "focus": "WSIB Care Programs",
        "keep_copy": True,
        "image": (5360, "https://tonicphysio.com/wp-content/uploads/2024/11/wsib-care-programs-points.png", "WSIB care program recovery points"),
        "benefits_title": "What WSIB Care at Tonic Physio Includes",
        "benefits": [
            "Customized rehabilitation programs aimed at a safe return to work.",
            "Timely assessment and treatment to limit long-term complications.",
            "Hands-on therapy and exercise to reduce pain and restore mobility.",
            "Guidance on workplace modifications and gradual return-to-work plans.",
            "Regular progress reviews shared with your care team as needed.",
            "Support for the physical and functional effects of workplace injury.",
        ],
        "intro": None,
        "sec1": None,
        "sec2": None,
        "why": None,
        "faqs": [
            ("What are WSIB Care Programs?", "WSIB Care Programs are structured rehabilitation programs funded by Ontario’s Workplace Safety and Insurance Board to help injured workers recover and return to work safely. They give you access to physiotherapy and other evidence-based care from WSIB-approved providers, based on your injury type."),
            ("What types of injuries does a WSIB program cover?", "Coverage commonly includes musculoskeletal injuries, repetitive strain injuries, fractures, sprains and strains, post-surgical rehabilitation, mild traumatic brain injuries (concussions), and related workplace conditions. Your specific coverage depends on your approved claim."),
            ("What is the WSIB Musculoskeletal Program of Care?", "This program provides structured physiotherapy for workers with musculoskeletal injuries, with a focus on pain reduction, restoring function, and preparing you to return to work."),
            ("What is the WSIB Mild Traumatic Brain Injury Program of Care?", "This program is for workers recovering from concussions and mild brain injuries. Care may include vestibular work, graded activity, and a supervised return to work."),
            ("How do I access WSIB Care Programs?", "File a claim with the WSIB first. Once your claim is approved, you can attend a WSIB-approved clinic such as Tonic Physio in Milton for assessment and treatment."),
            ("How long will I need WSIB physiotherapy?", "Length of care depends on the injury, your job demands, and how you progress. Your physiotherapist and the WSIB review the plan and can adjust it as recovery continues."),
            ("Are there costs for WSIB Care Programs?", "Approved WSIB treatment is typically billed directly to the WSIB, so you should not pay out of pocket for covered services. Care outside the approved plan may not be covered."),
            ("How do I report a workplace injury?", "Tell your employer as soon as you can and seek medical care if needed. Employers must report lost-time injuries or injuries needing more than first aid to the WSIB within the required timeframe."),
            ("What benefits can the WSIB provide?", "Depending on the claim, benefits may include health-care coverage, loss-of-earnings support, return-to-work assistance, and help with lasting impairment."),
            ("How can I appeal a WSIB decision?", "If you disagree with a WSIB decision, you can file a written objection, generally within six months of the decision date. Details are on the WSIB website."),
            ("What is a clearance certificate?", "A clearance certificate shows a business is registered with the WSIB and up to date on premiums. Principals hiring contractors often request one to limit liability for the contractor’s premiums."),
            ("What if I still need treatment after WSIB coverage ends?", "Your provider can request an extension when more care is medically needed. Approval is decided by the WSIB."),
            ("Can I choose my physiotherapy clinic for WSIB treatment?", "Yes. You can choose a WSIB-approved clinic. Tonic Physio in Milton provides WSIB physiotherapy and reports."),
        ],
    },
    12517: {
        "slug": "functional-movement-assessment",
        "h1": "Functional Movement Assessment in Milton",
        "faq_heading": "FAQs About Functional Movement Assessment",
        "yoast_title": "Functional Movement Assessment Milton | Tonic Physio",
        "yoast_desc": "Find how your body moves, reduce injury risk, and train with a clearer plan. Book a functional movement assessment at Tonic Physio in Milton.",
        "focus": "functional movement assessment Milton",
        "image": (12774, "https://tonicphysio.com/wp-content/uploads/2026/05/Functional-Movement-Assessment-balance-tonic-physio.jpg", "Functional movement and balance assessment at Tonic Physio Milton"),
        "benefits_title": "What Your Assessment Covers",
        "benefits": [
            "Full-body movement screen of squat, hinge, lunge, push, pull, and rotation.",
            "Balance, control, and single-leg stability testing.",
            "Identification of mobility restrictions and strength imbalances.",
            "Sport- or job-specific tasks when they matter to your goals.",
            "A written plan for training, rehab, or return to activity.",
            "Clear next steps you can use in the gym, on the field, or at work.",
        ],
        "intro": [
            "Understanding how your body moves is the foundation of injury prevention and better performance. A functional movement assessment at Tonic Physio in Milton looks at how you squat, hinge, lunge, rotate, and stabilize — not just where it hurts today.",
            "Our physiotherapists use this screen for athletes, active adults, and people returning from injury. The goal is to find the weak links that load joints and tissues the wrong way, then build a plan you can follow.",
            "If you want a clearer picture before starting a new sport, ramping up training, or returning after time off, this assessment gives you a structured starting point at our Milton clinic.",
        ],
        "sec1": (
            "Who Should Book a Movement Screen",
            [
                "This visit is useful if you keep getting the same niggle, feel unstable in sport, or want to train harder without guessing. It also helps before a return-to-sport block or after a long layoff.",
                "We often see runners, gym-goers, field-sport athletes, and people whose work involves lifting, reaching, or long hours on their feet. You do not need to be in competitive sport to benefit.",
            ],
            ["Recurring strains or “tweaks” that never fully settle", "Return from injury, surgery, or time away from training", "Performance goals where technique and capacity both matter"],
        ),
        "sec2": (
            "What Happens During the Assessment",
            [
                "We start with your history, sport or work demands, and any previous injuries. Then we watch quality of movement under control and under load, rather than chasing a single “tight” muscle.",
                "Findings are explained in plain language. You leave with priorities: what to restore, what to strengthen, and what to stop compensating for.",
            ],
            ["Mobility of hips, ankles, shoulders, and spine", "Core and hip control during single-leg and squat patterns", "Painful versus restricted versus weak movement"],
        ),
        "why": (
            "Why Have This Done at Tonic Physio",
            [
                "A movement screen is only useful if it changes what you do next. We connect the findings to physiotherapy, a <a href=\"https://tonicphysio.com/physiotherapy-in-milton/return-to-sport-program/\">return to sport program</a>, or a <a href=\"https://tonicphysio.com/physiotherapy-in-milton/running-injury-assessment/\">running assessment</a> when that is the right path.",
                "You get clinic-based testing in Milton with clinicians who treat these patterns every week — not a one-off checklist with no follow-up.",
                "Book online or call the clinic if you want a baseline before your next training block.",
            ],
            None,
        ),
        "faqs": [
            ("What is a functional movement assessment?", "It is a structured physiotherapy exam of how you move in common patterns — squat, lunge, hinge, reach, and balance — to find restrictions, weakness, and compensations that raise injury risk or limit performance."),
            ("Do I need to be injured to book this?", "No. Many people book it as a baseline before a season, a new gym program, or a return from time off. It is also useful when pain keeps returning in the same area."),
            ("How long is the appointment?", "Plan for a standard physiotherapy assessment visit. Time is spent on history, movement testing, and a clear plan rather than a rushed screen."),
            ("Is this the same as a running gait analysis?", "They overlap, but a functional movement assessment looks at whole-body control. A <a href=\"https://tonicphysio.com/physiotherapy-in-milton/running-injury-assessment/\">running injury assessment</a> focuses more on running mechanics and load."),
            ("Will I get exercises the same day?", "Yes. You should leave with the first priorities to work on, and a plan for follow-up if treatment or a supervised program is needed."),
            ("Can this help me return to sport?", "Yes. We use it to decide whether you are ready for higher-speed or sport-specific work, or whether mobility and strength still need to come first."),
            ("Do I need a doctor’s referral?", "No referral is required to book at Tonic Physio. Some insurance plans still ask for one for reimbursement, so check your policy."),
            ("Where is the clinic?", "Tonic Physio is at 100 Nipissing Rd #5 in Milton. Parking is available and we are open Monday to Saturday."),
        ],
    },
    12509: {
        "slug": "return-to-sport-program",
        "h1": "Return to Sport Program in Milton",
        "faq_heading": "FAQs About Our Return to Sport Program",
        "yoast_title": "Return to Sport Program Milton | Tonic Physio",
        "yoast_desc": "Return to sport with a staged physiotherapy program in Milton. Strength, plyometrics, and sport-specific drills after injury. Book at Tonic Physio.",
        "focus": "return to sport physiotherapy Milton",
        "image": (12603, "https://tonicphysio.com/wp-content/uploads/2026/04/Return-to-Sport-Program-Sport-Specific-Drills-and-Linear-Mechanics-tonic-physio.jpg", "Sport-specific return to sport drills at Tonic Physio Milton"),
        "benefits_title": "How We Progress You Back to Sport",
        "benefits": [
            "Clear criteria instead of “see how it feels on game day.”",
            "Strength and power rebuilding after time off or injury.",
            "Plyometric and change-of-direction work when you are ready.",
            "Linear mechanics and sport-specific drills.",
            "Load management so you do not spike volume too fast.",
            "Communication with coaches or trainers when useful.",
        ],
        "intro": [
            "Getting back to sport is more than waiting until pain settles. A proper return to sport program rebuilds strength, power, confidence, and match-speed control so you are not guessing on the first game back.",
            "At Tonic Physio in Milton, we stage this work after sprains, muscle strains, post-surgical rehab, and overuse injuries. The plan is built around your sport, position, and the demands of practice versus competition.",
            "If you have been cleared medically but still feel hesitant, slow, or unstable, this program is designed to close that gap.",
        ],
        "sec1": (
            "When a Staged Return Matters",
            [
                "Pain-free daily walking is not the same as cutting, landing, or sprinting. Returning too early is one of the most common reasons injuries repeat.",
                "We use this program after ankle and knee injuries, hamstring and calf strains, shoulder injuries in overhead athletes, and after a block of physiotherapy when the next step is sport, not just the clinic gym.",
            ],
            ["You feel fine in daily life but not at training speed", "You have had the same injury more than once", "A coach or surgeon has asked for a structured return plan"],
        ),
        "sec2": (
            "What the Program Includes",
            [
                "We start with a <a href=\"https://tonicphysio.com/physiotherapy-in-milton/functional-movement-assessment/\">functional movement assessment</a> and sport history, then progress through strength, reactive work, and drills that look like your sport.",
                "Sessions are progressed only when quality and symptoms allow. You will know what “ready” looks like for each stage rather than relying on a calendar alone.",
            ],
            ["Strength and capacity for the injured region and the chain around it", "Plyometrics and landing control when appropriate", "Acceleration, deceleration, and change of direction", "Sport-specific skill under fatigue"],
        ),
        "why": (
            "Why Athletes Choose Tonic Physio",
            [
                "You stay in a clinic that already treats the injury, rather than being handed a generic printout. We also treat <a href=\"https://tonicphysio.com/physiotherapy-in-milton/\">physiotherapy</a> needs that still sit underneath sport performance.",
                "Milton athletes and active adults use this program to return to field sports, court sports, running, and gym training with a plan that matches real session demands.",
                "Book an assessment to see which stage you are actually in — not which week you hoped to be in.",
            ],
            None,
        ),
        "faqs": [
            ("When can I start a return to sport program?", "Once acute pain and swelling are under control and you can complete basic strength and movement tests without a flare. We decide the start point at assessment, not from a generic timeline."),
            ("Is this only for competitive athletes?", "No. Recreational players, runners, and gym athletes use the same staged approach. The drills change; the principle does not."),
            ("How is this different from regular physiotherapy?", "Physiotherapy restores movement and daily function. Return to sport adds speed, power, and sport-specific load so you can train and compete, not just walk without pain."),
            ("Do you work with my coach?", "When it helps, yes. We can share stage goals and restrictions so practice load does not undo clinic progress."),
            ("What if I still have pain?", "Then we treat that first or in parallel. Pushing sport drills on an irritable injury usually delays return."),
            ("How many sessions will I need?", "It depends on the injury, how long you have been away, and your sport. Some people need a short block; others need several stages over weeks."),
            ("Can I keep training while I am in the program?", "Often yes, with modified load. We will tell you what to keep, what to drop, and what to delay."),
            ("Do I need a referral?", "No. Book directly at Tonic Physio. Check your insurance if they require a referral for coverage."),
        ],
    },
    12580: {
        "slug": "nutrition-coaching",
        "h1": "Nutrition Coaching in Milton",
        "faq_heading": "FAQs About Nutrition Coaching",
        "yoast_title": "Nutrition Coaching Milton | Recovery and Performance | Tonic Physio",
        "yoast_desc": "Support recovery, energy, and inflammation with practical nutrition coaching at Tonic Physio in Milton. Personalized guidance you can actually follow.",
        "focus": "nutrition coaching Milton",
        "image": (12644, "https://tonicphysio.com/wp-content/uploads/2026/04/Nutrition-Coaching-Weekly-Meal-Plan-tonic-physio.jpg", "Nutrition coaching meal planning at Tonic Physio Milton"),
        "benefits_title": "What Nutrition Coaching Can Help With",
        "benefits": [
            "Fueling around physiotherapy, training, and workdays.",
            "Practical meal structure instead of extreme diets.",
            "Support for recovery after injury or surgery.",
            "Guidance when inflammation, energy, or sleep are off.",
            "Simple shopping and prep habits that fit a busy week.",
            "Coordination with your physiotherapy goals at the same clinic.",
        ],
        "intro": [
            "Food will not replace physiotherapy, but it does affect how you recover, train, and feel through a work week. Nutrition coaching at Tonic Physio in Milton is practical: meals you can cook, pack, and repeat — not a rigid plan that collapses on a busy Thursday.",
            "We work with people recovering from injury, managing energy for sport or shift work, and trying to support an anti-inflammatory pattern without cutting out entire food groups for no reason.",
            "If you already attend the clinic for physio, coaching can sit alongside that care so your rehab and your plate are not pulling in different directions.",
        ],
        "sec1": (
            "Who This Is For",
            [
                "This is a good fit if you are in rehab and unsure what to eat around sessions, if training has increased, or if you keep starting over with diets that are too strict to keep.",
                "It is not a medical nutrition clinic for complex disease management. If you have a condition that needs a registered dietitian or physician-led care, we will say so and keep the advice within a sensible scope.",
            ],
            ["Injury recovery and return to activity", "Busy workdays with skipped meals and late-night catch-up eating", "Athletes who need more consistent fueling, not more rules"],
        ),
        "sec2": (
            "What You Work On Together",
            [
                "Sessions focus on your actual week: breakfasts you will eat, lunches you can pack, and how to handle takeout or family meals without a full reset every Monday.",
                "We may talk about protein around rehab sessions, hydration, fibre, and reducing ultra-processed defaults — always in the context of what you already like and can afford.",
            ],
            ["A realistic weekly meal pattern", "Recovery-friendly options around physiotherapy or training", "How to adjust on travel, overtime, or game weeks"],
        ),
        "why": (
            "Why Have Nutrition Support at a Physio Clinic",
            [
                "Your coach understands you are also in the clinic for <a href=\"https://tonicphysio.com/physiotherapy-in-milton/\">physiotherapy</a>, a <a href=\"https://tonicphysio.com/physiotherapy-in-milton/return-to-sport-program/\">return to sport program</a>, or workplace recovery. Advice can match that load rather than fighting it.",
                "You still get the same Milton location, booking, and team. Start with an appointment and bring a typical day of eating — photos on your phone are enough.",
                "Book online if you want a plan you can follow next week, not next January.",
            ],
            None,
        ),
        "faqs": [
            ("Is this the same as seeing a dietitian?", "Not always. Nutrition coaching here is practical education and habit support alongside physiotherapy. Some medical conditions need a registered dietitian or your physician. We will tell you if that is the better next step."),
            ("Do I have to follow a strict meal plan?", "No. We build around foods you already eat and the time you actually have. Strict plans that you cannot keep are not the goal."),
            ("Can nutrition help my injury recover faster?", "Adequate protein, energy, sleep, and hydration support tissue repair. Food is one part of recovery, not a substitute for assessment and rehab."),
            ("Will you sell me supplements?", "The focus is food first. If a supplement is discussed, it is optional and should not replace meals or prescribed care."),
            ("How many sessions do people book?", "Some people need a single planning session. Others prefer a short follow-up block while they change weekday habits."),
            ("Can I do this while I am already in physiotherapy?", "Yes. That is often the best time, because training and rehab load are already on the calendar."),
            ("Do you help with sports nutrition?", "We can help recreational and club athletes eat more consistently around training. High-performance sport nutrition may still need a specialist."),
            ("How do I book?", "Book through Jane or call Tonic Physio in Milton. Mention nutrition coaching when you schedule."),
        ],
    },
    12563: {
        "slug": "work-injuries",
        "h1": "Work Injury Physiotherapy in Milton",
        "faq_heading": "FAQs About Work Injury Care",
        "yoast_title": "Work Injury Physiotherapy Milton | Tonic Physio",
        "yoast_desc": "Physiotherapy for work injuries in Milton, including WSIB claims, lifting injuries, and return-to-work planning. Book Tonic Physio today.",
        "focus": "work injury physiotherapy Milton",
        "image": (12752, "https://tonicphysio.com/wp-content/uploads/2026/05/Work-Injuries-initial-assesment-tonic-physio.jpg", "Work injury physiotherapy assessment at Tonic Physio Milton"),
        "benefits_title": "How We Help You Get Back to Work",
        "benefits": [
            "Assessment tied to your actual job tasks, not just the painful spot.",
            "Treatment for sprains, strains, back and neck injuries, and overuse.",
            "Graded strengthening for lifting, reaching, and standing tolerance.",
            "Return-to-work planning with modified duties when needed.",
            "WSIB-related care when your claim is approved.",
            "Clear home exercises you can do around shift work.",
        ],
        "intro": [
            "A work injury does not have to wait until you cannot finish a shift. Early physiotherapy for lifting injuries, slips, repetitive strain, and postural overload can shorten time off and reduce the chance the problem becomes chronic.",
            "Tonic Physio in Milton treats workplace injuries for people on WSIB claims and for those using extended health benefits or paying privately. Care is built around the job you need to return to — warehouse, trades, office, health care, or driving.",
            "If you already have a WSIB claim, see our <a href=\"https://tonicphysio.com/wsib-care-programs/\">WSIB Care Programs</a> page as well. This page is for the broader work-injury pathway, including the first days after an incident.",
        ],
        "sec1": (
            "Common Workplace Injuries We Treat",
            [
                "Most work injuries we see are musculoskeletal: backs from lifting, shoulders from overhead work, wrists from tools or typing, and knees or ankles from slips and uneven ground.",
                "Repetitive strain can look “small” until it stops you gripping, lifting, or sitting through a full day. Those cases still need a proper exam and a plan, not just rest until Monday.",
            ],
            ["Low back and neck strain from lifting or prolonged sitting", "Shoulder and elbow overload from repetitive or overhead tasks", "Slips, trips, and falls", "Gradual onset repetitive strain"],
        ),
        "sec2": (
            "Return to Work, Not Just Pain Relief",
            [
                "We treat pain, but we also test the tasks your job requires: lifting, carrying, reaching, standing, and sitting tolerance. That is how we decide when modified duties make sense.",
                "When a WSIB program of care applies, we follow that structure. When it does not, we still use the same principle: restore capacity for work, then build it up.",
            ],
            ["Job-demand focused exercise", "Advice on temporary modifications", "Progress reviews you can share with your workplace or case manager"],
        ),
        "why": (
            "Why Injured Workers Come to Tonic Physio",
            [
                "You get a Milton clinic that already handles WSIB reporting when needed, plus everyday work injuries that never become a claim. We also offer <a href=\"https://tonicphysio.com/physiotherapy-in-milton/workplace-ergonomics-assessment/\">workplace ergonomics assessments</a> when the job setup is part of the problem.",
                "Appointments are available Monday to Saturday so shift workers can attend without missing a full week of care.",
                "Book promptly after an incident when you can — earlier assessment usually means a simpler plan.",
            ],
            None,
        ),
        "faqs": [
            ("Should I see physio after a workplace incident?", "Yes, especially if pain, swelling, or limited movement lasts more than a day or two, or if you cannot do your normal job tasks. Early care helps you document the injury and start the right loading."),
            ("Is this the same as WSIB Care Programs?", "WSIB programs are a specific funded pathway after a claim is approved. This page covers work injuries more broadly, including private and benefits-covered care. Approved claims are treated through our <a href=\"https://tonicphysio.com/wsib-care-programs/\">WSIB Care Programs</a>."),
            ("Do I need to report the injury to my employer?", "Yes. Tell your employer as soon as you can. That protects you if the injury later needs time off or a WSIB claim."),
            ("Can I come in if I am still working modified duties?", "Yes. Many people attend while on modified work. We plan sessions around your remaining job tasks."),
            ("What should I bring to the first visit?", "A list of job tasks, any incident reports, claim numbers if you have them, and imaging or doctor notes if they exist."),
            ("Will you tell me to stay off work?", "Only if the job is unsafe given your current capacity. The usual goal is a graded return, not prolonged rest."),
            ("Do you treat office workers as well as trades?", "Yes. Desk-related neck, back, and arm pain is a work injury too. Ergonomic advice is part of that care."),
            ("How do I book?", "Book online through Jane or call 905-878-7775. Mention that it is a work injury so we can allow enough time for the first visit."),
        ],
    },
    12515: {
        "slug": "workplace-ergonomics-assessment",
        "h1": "Workplace Ergonomics Assessment in Milton",
        "faq_heading": "FAQs About Workplace Ergonomics Assessment",
        "yoast_title": "Workplace Ergonomics Assessment Milton | Tonic Physio",
        "yoast_desc": "Fix desk setup, lifting technique, and workstation strain with a workplace ergonomics assessment at Tonic Physio in Milton. Book your visit today.",
        "focus": "workplace ergonomics assessment Milton",
        "image": (13463, "https://tonicphysio.com/wp-content/uploads/2026/08/workplace-ergonomics-desk-setup-milton.jpg", "Ergonomic desk setup assessment in Milton at Tonic Physio"),
        "benefits_title": "What We Review in an Ergonomics Visit",
        "benefits": [
            "Chair, desk, screen, keyboard, and mouse setup.",
            "Laptop and hybrid-work setups that cause neck strain.",
            "Lifting, reaching, and standing tasks for non-desk jobs.",
            "Breaks and movement you can actually do on a shift.",
            "A short list of changes ranked by impact.",
            "Exercises for the tissues already irritated by the job.",
        ],
        "intro": [
            "Long hours at a desk, repetitive tasks, and a poor workstation add up. Neck pain, headaches, shoulder tension, and low back stiffness are often the result of setup and habits — not a “weak core” you need to live with.",
            "A workplace ergonomics assessment at Tonic Physio in Milton looks at how you sit, stand, reach, and lift, then gives you a short list of changes that matter. We treat the tissues that are already sore and reduce the load that keeps irritating them.",
            "This is useful for office teams, hybrid workers, and people whose job is not a desk but still has a repeated posture or lift.",
        ],
        "sec1": (
            "Desk and Hybrid Work Setups",
            [
                "Most desk problems come from a screen that sits too low, a chair that does not support the low back, and a mouse that sits too far forward. Laptops used for hours without a stand and external keyboard are a common source of neck pain.",
                "We will show you target positions for the monitor, elbows, and feet, and how to alternate sitting and standing if you have a sit-stand desk. Standing all day is not the fix either.",
            ],
            ["Monitor at or slightly below eye level", "Elbows near 90 degrees, wrists closer to neutral", "Feet supported, lumbar curve supported", "Movement breaks every 30–60 minutes"],
        ),
        "sec2": (
            "When the Job Is Not a Desk",
            [
                "Trades, warehousing, health care, and driving have their own ergonomic problems: repeated flexion, awkward reaches, and tools held too far from the body. We assess those tasks the same way — reduce the load, then condition the tissues that still have to do the work.",
                "If pain is already limiting your job, combine this visit with <a href=\"https://tonicphysio.com/physiotherapy-in-milton/work-injuries/\">work injury physiotherapy</a> rather than only changing the chair.",
            ],
            ["Task analysis of your real lifts and reaches", "Simple tool and posture changes", "Strength and mobility work for the overloaded region"],
        ),
        "why": (
            "Why Book This at Tonic Physio",
            [
                "You get a clinician who treats the pain and the setup. A checklist without treatment often fails; treatment without changing the desk often fails too.",
                "We can also support employers who want a practical session for staff, not a binder that never gets used.",
                "Bring photos of your workstation if you cannot be assessed on site. Many hybrid workers do this successfully.",
            ],
            None,
        ),
        "faqs": [
            ("Do I need to bring my chair to the clinic?", "No. Photos or a short video of your desk from the side and from behind you are enough for most office assessments. On-site visits can be discussed for teams."),
            ("Is this only for office workers?", "No. Anyone with a repeated work posture or lift can benefit, including trades and standing jobs."),
            ("Will you sell me a new chair?", "We recommend setup changes first. If a chair or desk is clearly the problem, we will say what to look for. We are not a furniture store."),
            ("Can ergonomics fix pain I already have?", "It reduces the ongoing strain. If tissues are already irritated, you still need physiotherapy. The two work together."),
            ("How long is the appointment?", "Treat it as a physiotherapy visit with extra time on your work tasks and setup, not a five-minute checklist."),
            ("Can my employer book this for the team?", "Yes. Contact the clinic to discuss a practical session for staff."),
            ("Do standing desks prevent back pain?", "They help some people if you alternate positions. Standing all day can create different problems. Variation matters more than one “perfect” posture."),
            ("How do I book?", "Book online or call Tonic Physio in Milton and ask for a workplace ergonomics assessment."),
        ],
    },
    12511: {
        "slug": "running-injury-assessment",
        "h1": "Running Injury Assessment in Milton",
        "faq_heading": "FAQs About Running Injury Assessment",
        "yoast_title": "Running Injury Assessment Milton | Gait and Rehab | Tonic Physio",
        "yoast_desc": "Get a running injury assessment in Milton: gait, load, and strength. Treat shin, knee, Achilles, and hip pain at Tonic Physio.",
        "focus": "running injury assessment Milton",
        "image": (12762, "https://tonicphysio.com/wp-content/uploads/2026/05/Running-Injury-Assessment-leg-checking-tonic-physio.jpg", "Running injury assessment and gait review at Tonic Physio Milton"),
        "benefits_title": "What We Look at in Runners",
        "benefits": [
            "History of mileage, surfaces, shoes, and previous injuries.",
            "Strength of hips, calves, and foot control.",
            "Cadence, overstride, and obvious gait inefficiencies.",
            "Training-load errors that keep tissues irritated.",
            "A plan to keep you running when it is safe, not always a full stop.",
            "Footwear and orthotic discussion when structure is part of the picture.",
        ],
        "intro": [
            "Running injuries are usually training-load problems wearing a local diagnosis: shin pain, knee pain, Achilles irritation, plantar fascia, IT band, or a calf that keeps going. A running injury assessment at Tonic Physio in Milton looks at the tissue and the way you load it.",
            "We combine a physiotherapy exam with a practical look at gait, cadence, strength, and how fast your mileage changed. The aim is to keep you running when it is safe, and to stop the cycle of rest-then-repeat.",
            "Whether you are training for a race or running to stay well, you do not need to guess between “push through” and “stop everything.”",
        ],
        "sec1": (
            "Injuries We Commonly See in Runners",
            [
                "Patellofemoral pain, IT band irritation, Achilles tendinopathy, plantar fasciitis, shin splints, hamstring and calf strains, and hip-related pain all show up in this clinic. Many share the same drivers: too much too soon, weak hips or calves, and a gait that overloads one side.",
                "If the same injury keeps returning after you “rest it,” the missing piece is usually capacity and mechanics, not another week off.",
            ],
            ["Knee pain that starts after a mileage jump", "Achilles or plantar pain in the first morning steps", "Shin pain that appears as speed or hills increase"],
        ),
        "sec2": (
            "Gait, Strength, and Load",
            [
                "We look at step rate, overstriding, hip control, and calf capacity. Small changes in cadence or strength often matter more than a dramatic form overhaul.",
                "You may also need a broader <a href=\"https://tonicphysio.com/physiotherapy-in-milton/functional-movement-assessment/\">functional movement assessment</a> if squatting, landing, or single-leg control is clearly limited off the road.",
            ],
            ["Strength testing for the chain that supports running", "Guidance on shoes and when custom orthotics are worth considering", "A return-to-run ladder you can follow between visits"],
        ),
        "why": (
            "Why Runners Come to Tonic Physio",
            [
                "You get a Milton clinic that treats running injuries weekly and will tell you whether to keep easy running, switch to cross-training, or stop impact for a short window.",
                "We also connect this visit to <a href=\"https://tonicphysio.com/physiotherapy-in-milton/\">physiotherapy</a> and, when you are ready, a staged return to faster work.",
                "Bring your current shoes and a short description of your last four weeks of training.",
            ],
            None,
        ),
        "faqs": [
            ("Should I stop running completely?", "Not always. Many runners can keep a reduced, easy volume while we treat the tissue. We will be specific. Complete rest is used when running clearly keeps the injury irritable."),
            ("Do I need a video of my gait?", "Helpful, not required. A phone video from the side and behind on a treadmill or path is enough for a first look."),
            ("Is this only for marathoners?", "No. Beginners, return-to-running after time off, and recreational runners are the majority of visits."),
            ("Can you help with shoe choice?", "We can discuss what your foot and gait suggest. We do not replace a proper running-store fitting, but we will flag shoes that are clearly working against you."),
            ("Will I get a training plan?", "You get a return-to-run structure and load rules. We are not your running coach, but rehab only works if training load is part of the plan."),
            ("How soon can I race again?", "That depends on the tissue, how long it has been irritated, and the race demand. We would rather you start the race prepared than start it early."),
            ("Do you treat walk-to-run beginners?", "Yes. Many “injuries” in new runners are simply load progressing faster than tissue capacity."),
            ("How do I book?", "Book a running injury assessment at Tonic Physio in Milton online or by phone. Wear shorts and bring your running shoes."),
        ],
    },
}


def find(nodes, eid):
    for n in nodes or []:
        if n.get("id") == eid:
            return n
        r = find(n.get("elements") or [], eid)
        if r:
            return r
    return None


def set_editor(tree, eid, html):
    n = find(tree, eid)
    n["settings"]["editor"] = html


def apply_page(tree, spec, page_id):
    find(tree, "b26ef64")["settings"]["title"] = spec["h1"]
    find(tree, "782bdc1")["settings"]["title"] = spec["faq_heading"]

    if not spec.get("keep_copy"):
        set_editor(tree, "fb2de65", editor_intro(*spec["intro"]))
        t1, p1, b1 = spec["sec1"]
        set_editor(tree, "c285d5e", editor_section(t1, p1, b1))
        t2, p2, b2 = spec["sec2"]
        set_editor(tree, "5b7f42b", editor_section(t2, p2, b2))
        t3, p3, b3 = spec["why"]
        set_editor(tree, "9f8a561", editor_section(t3, p3, b3))

    left = find(tree, "14185019")
    # Remove previously injected nodes if re-run
    left["elements"] = [e for e in left["elements"] if e.get("id") not in ("imgbnft", "txtbnft") and not str(e.get("id", "")).startswith("bn")]
    mid, url, alt = spec["image"]
    img = image_widget(hid(f"{page_id}-img"), mid, url, alt)
    bullets_html = editor_section(spec["benefits_title"], ["These are the practical pieces we build into your plan:"], spec["benefits"])
    txt = text_widget(hid(f"{page_id}-bn"), bullets_html)
    # Insert before why-choose (9f8a561)
    els = left["elements"]
    idx = next(i for i, e in enumerate(els) if e.get("id") == "9f8a561")
    left["elements"] = els[:idx] + [img, txt] + els[idx:]

    faq_wrap = find(tree, "4249e5c")
    faq_wrap["elements"] = [e for e in faq_wrap["elements"] if e.get("widgetType") != "elementskit-accordion"]
    faq_wrap["elements"].append(accordion_widget(hid(f"{page_id}-faq"), spec["faqs"]))

    appt = find(tree, "60186c47")
    appt["settings"].update(
        {
            "min_height": {"unit": "px", "size": 280, "sizes": []},
            "background_background": "classic",
            "background_image": {
                "url": "https://tonicphysio.com/wp-content/uploads/2024/11/appointment.png",
                "id": 5716,
                "size": "",
                "alt": "Book a physiotherapy appointment in Milton",
                "source": "library",
            },
            "background_size": "cover",
            "background_position": "center center",
            "padding": {"unit": "px", "top": "40", "right": "20", "bottom": "40", "left": "20", "isLinked": False},
            "flex_align_items": "center",
            "flex_justify_content": "center",
        }
    )
    appt["elements"] = [
        heading_widget(hid(f"{page_id}-ah"), "Start Your Care With Us", "h2", "center"),
        heading_widget(hid(f"{page_id}-as"), "Book a visit at our Milton clinic — Monday to Saturday.", "h3", "center"),
        button_widget(hid(f"{page_id}-ab"), "Book Appointment"),
    ]
    return tree


def opener():
    cj = MozillaCookieJar(str(COOKIE))
    cj.load(ignore_discard=True, ignore_expires=True)
    return urllib.request.build_opener(urllib.request.HTTPCookieProcessor(cj))


def update_page(op, page_id, tree, spec):
    payload = {
        "meta": {
            "_elementor_edit_mode": "builder",
            "_elementor_template_type": "wp-page",
            "_elementor_data": json.dumps(tree, separators=(",", ":")),
            "_elementor_page_settings": {"eael_ext_toc_title": "Table of Contents"},
            "_yoast_wpseo_title": spec["yoast_title"],
            "_yoast_wpseo_metadesc": spec["yoast_desc"],
            "_yoast_wpseo_focuskw": spec["focus"],
        }
    }
    data = json.dumps(payload).encode()
    req = urllib.request.Request(
        f"{ROOT}/{page_id}",
        data=data,
        method="POST",
        headers={
            "User-Agent": UA,
            "Accept": "application/json",
            "Content-Type": "application/json",
            "X-WP-Nonce": NONCE,
            "Referer": "https://tonicphysio.com/wp-admin/",
            "Origin": "https://tonicphysio.com",
        },
    )
    try:
        with op.open(req, timeout=90) as resp:
            body = json.loads(resp.read().decode())
            return resp.status, body.get("id"), body.get("modified"), body.get("link")
    except urllib.error.HTTPError as e:
        err = e.read().decode("utf-8", "replace")[:500]
        raise RuntimeError(f"HTTP {e.code} page {page_id}: {err}") from e


def main():
    src = json.loads((BASE / "slug-wsib-care-programs.json").read_text())[0]
    base_tree = json.loads(src["meta"]["_elementor_data"])
    op = opener()
    results = []
    for page_id, spec in PAGES.items():
        tree = apply_page(copy.deepcopy(base_tree), spec, page_id)
        out = BASE / f"built-{page_id}.json"
        out.write_text(json.dumps(tree, indent=2))
        status, pid, modified, link = update_page(op, page_id, tree, spec)
        results.append((status, pid, spec["slug"], modified, link, out.stat().st_size))
        print(f"OK {status} id={pid} {spec['slug']} modified={modified} bytes={out.stat().st_size}")
    return results


if __name__ == "__main__":
    main()
