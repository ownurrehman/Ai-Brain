#!/usr/bin/env python3
"""
Fix 3 tasks for Rank Ray Digital PR page:
1. Custom opening paragraphs (template for future use)
2. Fix Digital PR images (replace app dev images with PR images)
3. Remove "Services Services" from Yoast title
"""

import requests, base64, json, sys

USER = "openclaw"
PASS = "6Zz9 5gJL 8uyA QH4g RQDH GV1j"
AUTH = base64.b64encode(f"{USER}:{PASS}".encode()).decode()
HEADERS = {"Authorization": f"Basic {AUTH}", "Content-Type": "application/json"}

PAGE_ID = 14782  # Digital PR Services

# Task 2: Fix images - replace app dev images with Digital PR images
IMAGE_H2_P2 = 22571  # digital-pr-backlinks-editorial.jpg
IMAGE_H2_P3 = 17453  # digital-pr-service-Rank-Ray.jpg

# Task 3: Fix Yoast title - remove duplicate "Services"
# Current: "Digital PR Services Services: Strategy & Results | Rank Ray"
# Fixed:   "Digital PR Services: Strategy & Results | Rank Ray"
YOAST_TITLE = "Digital PR Services: Strategy & Results | Rank Ray"
YOAST_DESC = "Expert digital PR services from Rank Ray. Data-driven media outreach, original research, and editorial link building that earns authority backlinks and drives measurable growth."

def fix_digital_pr():
    # Build payload with fixes
    payload = {
        "acf": {
            "image_for_h2_para_2_": IMAGE_H2_P2,
            "image_h2_paragraph_3": IMAGE_H2_P3
        },
        "meta": {
            "_yoast_wpseo_title": YOAST_TITLE,
            "_yoast_wpseo_metadesc": YOAST_DESC
        }
    }
    
    resp = requests.post(
        f"https://rankray.com/wp-json/wp/v2/pages/{PAGE_ID}",
        headers=HEADERS,
        json=payload,
        timeout=30
    )
    
    if resp.status_code == 200:
        print(f"✅ Page {PAGE_ID} updated successfully")
        print(f"   - Images fixed: h2_para_2 → {IMAGE_H2_P2}, h2_para_3 → {IMAGE_H2_P3}")
        print(f"   - Yoast title fixed: '{YOAST_TITLE}'")
        print(f"   - Yoast desc updated")
        return True
    else:
        print(f"❌ Failed: {resp.status_code} - {resp.text[:200]}")
        return False

def generate_custom_opening_templates():
    """Task 1: Generate custom opening paragraph templates by service category"""
    
    templates = {
        "seo_services": {
            "h1_paragraph": "Your website deserves traffic that converts. Rank Ray builds SEO strategies that capture high-intent searches at every stage of the buyer journey — from first research to final purchase decision.",
            "h2_first": "SEO That Captures Intent, Not Just Clicks"
        },
        "ppc_services": {
            "h1_paragraph": "Every click costs money. Rank Ray builds paid campaigns where every dollar targets buyers who are ready to act — not browsers who bounce.",
            "h2_first": "PPC Campaigns Built Around Revenue, Not Traffic"
        },
        "web_design": {
            "h1_paragraph": "Your website has 3 seconds to earn trust. Rank Ray designs conversion-centered layouts that guide visitors from first impression to form submission without friction.",
            "h2_first": "Web Design That Converts Visitors Into Leads"
        },
        "content_marketing": {
            "h1_paragraph": "Content that ranks but never converts is just expensive publishing. Rank Ray creates topic-driven content that captures search demand and moves readers toward action.",
            "h2_first": "Content Marketing That Builds Authority and Pipeline"
        },
        "social_media": {
            "h1_paragraph": "Likes do not pay bills. Rank Ray builds social strategies that turn engagement into qualified leads through platform-specific funnels and conversion-optimized creative.",
            "h2_first": "Social Media Marketing That Drives Revenue, Not Just Engagement"
        },
        "local_seo": {
            "h1_paragraph": "Nearby customers are searching right now. Rank Ray optimizes your presence in map packs, local directories, and geo-targeted searches to drive foot traffic and calls.",
            "h2_first": "Local SEO That Puts You on the Map — Literally"
        },
        "link_building": {
            "h1_paragraph": "Not all backlinks move rankings. Rank Ray earns editorial links from publications your audience trusts, building domain authority that drives sustainable organic growth.",
            "h2_first": "Link Building That Earns Authority, Not Just Links"
        },
        "digital_pr": {
            "h1_paragraph": "Digital PR is the most powerful and sustainable link building strategy in modern search engine optimization. Rank Ray combines data journalism, original research, and strategic media outreach to generate high-quality backlinks, brand mentions, and referral traffic that improve your search rankings and establish industry authority.",
            "h2_first": "Why Digital PR Outperforms Traditional Link Building and Outreach"
        },
        "ai_automation": {
            "h1_paragraph": "Manual processes drain resources and limit how fast your business can scale. Rank Ray designs intelligent automation systems that replace repetitive tasks with workflows, freeing your team to focus on strategy while reducing operational costs and accelerating revenue growth.",
            "h2_first": "Intelligent Workflows That Work Around the Clock"
        },
        "cro": {
            "h1_paragraph": "Traffic without conversions is just expensive hosting. Rank Ray identifies friction points in your user journey and redesigns experiences that turn more visitors into customers.",
            "h2_first": "CRO That Turns Traffic Into Revenue"
        },
        "email_marketing": {
            "h1_paragraph": "Your email list is your most valuable owned audience. Rank Ray builds automated sequences that nurture leads from first signup to final purchase with personalized messaging that drives repeat revenue.",
            "h2_first": "Email Marketing That Converts Subscribers Into Buyers"
        },
        "enterprise": {
            "h1_paragraph": "Large websites drown in technical debt, fragmented content, and competing internal pages. Rank Ray builds enterprise systems that align search visibility with business revenue, turning your site scale into a competitive advantage.",
            "h2_first": "Enterprise Marketing That Scales Revenue Systems"
        },
        "franchise": {
            "h1_paragraph": "Franchises burn budget on marketing that never reaches local customers. Rank Ray builds multi-location systems that drive qualified leads to every store through localized search and territory-specific campaigns.",
            "h2_first": "Franchise Marketing That Grows Every Location"
        },
        "audit": {
            "h1_paragraph": "Most audits surface obvious problems anyone could spot. Rank Ray conducts forensic analysis that finds the hidden issues — crawl budget waste, indexation gaps, and rendering failures — that actually hold back your rankings.",
            "h2_first": "Audits That Uncover Hidden Ranking Barriers"
        }
    }
    
    # Save templates for future use
    output_path = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/rankray.com/acf-opening-templates.json"
    with open(output_path, 'w') as f:
        json.dump(templates, f, indent=2)
    
    print(f"\n📋 Task 1: Custom opening templates generated")
    print(f"   Saved to: {output_path}")
    print(f"   Categories: {len(templates)}")
    
    return templates

if __name__ == "__main__":
    print("=" * 60)
    print("Rank Ray ACF Fix — 3 Tasks")
    print("=" * 60)
    
    # Task 1: Generate templates
    templates = generate_custom_opening_templates()
    
    # Tasks 2 & 3: Fix Digital PR page
    print("\n🔧 Tasks 2 & 3: Fixing Digital PR page...")
    success = fix_digital_pr()
    
    if success:
        print("\n" + "=" * 60)
        print("✅ All 3 tasks complete!")
        print("=" * 60)
        print("\nNext steps:")
        print("1. Clear LiteSpeed cache for page 14782")
        print("2. Verify changes at: https://rankray.com/digital-marketing-services/digital-pr-services/")
        print("3. Apply custom templates to other pages using the generated JSON")
    else:
        print("\n❌ Fix failed. Check error above.")
        sys.exit(1)
