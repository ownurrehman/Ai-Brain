"""Patch agency_growth_flow.py in memory: inject TonicPhysio mission context.

We do NOT permanently modify the canonical flow file. Instead this module
monkey-patches run-time strings so the swarm targets tonicphysio.com with the
real audit data from the 97-post crawl.
"""
import re

# Real audit data gathered from live WP API (2026-08-28)
MISSION = (
    "Create a cannibalization-free SEO content plan for tonicphysio.com (Tonic Physio, "
    "Milton, Ontario physiotherapy clinic). GOAL: strengthen service page rankings "
    "(physiotherapy, massage therapy, shockwave, MVA, WSIB, TMJ, osteopathy, pelvic floor, "
    "custom bracing, orthotics, acupuncture) with supporting blog clusters that pass link "
    "authority to service pages WITHOUT competing for the same keywords. "
    "EXISTING CONTENT FACTS (from live 97-post crawl): 97 published posts, avg 2251 words, "
    "zero thin posts. 17 service pages have ZERO supporting blogs: Graston Technique, "
    "Kinesio Taping, Electrical Stimulation, Return to Sport Program, Mulligan Concept, "
    "McKenzie Method, Myofascial Release, Running Injury Assessment, Golf Physiotherapy, "
    "Functional Movement Assessment, Balance and Fall Prevention, Cupping Therapy, "
    "Geriatric Physiotherapy, Relaxation Massage, Sports Massage, Nutrition Coaching, "
    "Pre-Surgical Rehabilitation. "
    "CANNIBALIZATION HAZARDS (blog-vs-blog and blog-vs-service overlap): WSIB posts (12441 vs 12892); "
    "Custom vs OTC Bracing (12438 vs 12889 vs 12437); Car Accident timeline (12439, 12890, 12440, 12372); "
    "Compression therapy (8315, 11750, 12844); Postpartum/Pelvic floor (11820, 13496, 13500, 11812); "
    "Clinic-choosing posts (10327, 11326, 13476); Osteopathy (8840, 13466, 11397, 11488); "
    "Shockwave (7748, 12840, 13470, 11194); Deep tissue massage (13036, 13474); TMJ (10929, 13516, 13482); "
    "Lymphatic massage (13039, 13478); Pediatric (13034, 13480); Posture (11649, 13514); "
    "Back pain (11302, 11842, 12725, 11847); Pregnancy massage (11308, 13040, 13468); "
    "Chronic pain (9245, 12698, 11635, 13030). "
    "CONTENT HYGIENE: 67 posts have duplicate internal links, 14 have FAQ headings (deprecated), "
    "11 have generic Conclusion headings. New plan must avoid repeating these."
)
NICHE = "Local healthcare: physiotherapy & rehabilitation clinic (Milton, Ontario, Canada)"
DOMAIN = "tonicphysio.com"


def build_context_block():
    return (
        f"\n\nCRITICAL CONTEXT FROM LIVE SITE CRAWL (use this, do not invent):\n{MISSION}\n"
        f"Content rules: no FAQ sections, no FAQ schema, no generic 'Conclusion' headings, "
        f"no duplicate internal links per post, max 3-column tables, no em-dashes, "
        f"each new post must link to its primary service page with a natural contextual anchor. "
        f"Local intent: Milton / Halton region Ontario."
    )