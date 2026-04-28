#!/usr/bin/env python3
"""
Update Yoast SEO fields on 5 Tonic Physio WordPress pages using Playwright browser automation.
"""

import asyncio
from playwright.async_api import async_playwright

# WordPress admin credentials
WP_ADMIN_URL = "https://tonicphysio.com/wp-admin"
WP_USERNAME = "rankrayagency@gmail.com"
WP_PASSWORD = "RR#Tonic@2026"

# Page data
PAGES = [
    {
        "id": 11603,
        "name": "B-Pulse Pelvic Floor",
        "edit_url": "https://tonicphysio.com/wp-admin/post.php?post=11603&action=edit",
        "frontend_url": "https://tonicphysio.com/services/b-pulse-pelvic-floor-strengthening/",
        "seo_title": "B-Pulse Pelvic Floor Strengthening Milton | Tonic Physio",
        "meta_description": "B-Pulse pelvic floor strengthening in Milton at Tonic Physio. Expert treatment for postpartum recovery, incontinence & pelvic pain. Book consultation."
    },
    {
        "id": 6971,
        "name": "Joint Pain",
        "edit_url": "https://tonicphysio.com/wp-admin/post.php?post=6971&action=edit",
        "frontend_url": "https://tonicphysio.com/services/joint-pain-treatment/",
        "seo_title": "Joint Pain Treatment Milton | Tonic Physio",
        "meta_description": "Relieve joint pain and stiffness in Milton at Tonic Physio. Expert physiotherapy for arthritis, injury & chronic pain. Book your appointment."
    },
    {
        "id": 1791,
        "name": "Orthopedic Physiotherapy",
        "edit_url": "https://tonicphysio.com/wp-admin/post.php?post=1791&action=edit",
        "frontend_url": "https://tonicphysio.com/services/orthopedic-physiotherapy/",
        "seo_title": "Orthopedic Physiotherapy Milton | Tonic Physio",
        "meta_description": "Expert orthopedic physiotherapy in Milton at Tonic Physio. Joint & muscle rehab, post-surgery recovery & pain relief. Book assessment today."
    },
    {
        "id": 1793,
        "name": "Pediatric Physiotherapy",
        "edit_url": "https://tonicphysio.com/wp-admin/post.php?post=1793&action=edit",
        "frontend_url": "https://tonicphysio.com/services/pediatric-physiotherapy/",
        "seo_title": "Pediatric Physiotherapy Milton | Tonic Physio",
        "meta_description": "Pediatric physiotherapy in Milton at Tonic Physio. Expert care for children with developmental delays, injuries & mobility issues. Book now."
    },
    {
        "id": 6587,
        "name": "Hot Stone Massage",
        "edit_url": "https://tonicphysio.com/wp-admin/post.php?post=6587&action=edit",
        "frontend_url": "https://tonicphysio.com/services/hot-stone-massage/",
        "seo_title": "Hot Stone Massage Milton | Tonic Physio",
        "meta_description": "Hot stone massage in Milton at Tonic Physio. Therapeutic heat therapy for muscle tension, stress relief & relaxation. Book your session."
    }
]

async def login_to_wordpress(page):
    """Login to WordPress admin."""
    print("\n=== Logging into WordPress ===")
    await page.goto(WP_ADMIN_URL)
    await page.wait_for_load_state('networkidle')
    
    # Check if already logged in
    if 'wp-admin' in page.url and 'login' not in page.url:
        print("✓ Already logged in!")
        return True
    
    # Login form
    print("Logging in...")
    await page.fill('#user_login', WP_USERNAME)
    await page.fill('#user_pass', WP_PASSWORD)
    await page.click('#wp-submit')
    await page.wait_for_load_state('networkidle')
    await asyncio.sleep(2)
    
    # Check if login succeeded
    if 'wp-login' in page.url:
        print("✗ Login failed")
        return False
    
    print("✓ Logged in successfully")
    return True

async def update_yoast_fields(page, page_data):
    """Update Yoast SEO fields for a single page."""
    print(f"\n=== Updating Page {page_data['id']}: {page_data['name']} ===")
    
    # Navigate to edit page
    await page.goto(page_data['edit_url'])
    await page.wait_for_load_state('networkidle')
    await asyncio.sleep(2)
    
    # Wait for editor to load
    try:
        await page.wait_for_selector('.edit-post-layout', timeout=30000)
        print("✓ Editor loaded")
    except:
        print("⚠ Editor may not have loaded properly")
    
    # Scroll down to find Yoast SEO section
    print("Scrolling to find Yoast SEO section...")
    await page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
    await asyncio.sleep(2)
    
    # Look for Yoast SEO section - try multiple approaches
    yoast_found = False
    
    # Approach 1: Look for Yoast metabox and expand it
    yoast_buttons = [
        '.yoast-seo-settings-panel button',
        '.wpseo-metabox button',
        'button[class*="yoast"]',
        '[aria-label*="Yoast"]',
        'text=SEO',
        'text=Yoast'
    ]
    
    for selector in yoast_buttons:
        try:
            elements = await page.query_selector_all(selector)
            if elements:
                print(f"Found potential Yoast element: {selector}")
                # Click to expand
                await elements[0].click()
                await asyncio.sleep(1)
                yoast_found = True
                break
        except:
            continue
    
    # Approach 2: Look for the settings icon in Yoast
    if not yoast_found:
        try:
            settings_btn = await page.query_selector('[aria-label*="settings"]')
            if settings_btn:
                await settings_btn.click()
                await asyncio.sleep(1)
                yoast_found = True
                print("✓ Found settings button")
        except:
            pass
    
    # Approach 3: Scroll through page sections
    if not yoast_found:
        print("Searching page sections...")
        sections = await page.query_selector_all('.components-panel__body')
        for i, section in enumerate(sections):
            text = await section.inner_text()
            if 'yoast' in text.lower() or 'seo' in text.lower():
                print(f"✓ Found Yoast section at index {i}")
                # Click to expand
                await section.click()
                await asyncio.sleep(1)
                yoast_found = True
                break
    
    # Now find and fill the SEO fields
    # Yoast typically uses these field names/IDs
    seo_title_filled = False
    meta_desc_filled = False
    
    # Try common Yoast field selectors
    title_selectors = [
        'input[name="yoast_wpseo_focuskw"]',
        'input[id="yoast_wpseo_focuskw"]',
        'input[aria-label*="SEO title"]',
        'input[placeholder*="SEO title"]',
        '#yoast_wpseo_focuskw',
        'input[name*="seo_title"]',
        'input[id*="seo_title"]'
    ]
    
    for selector in title_selectors:
        try:
            element = await page.query_selector(selector)
            if element:
                print(f"✓ Found title field: {selector}")
                await element.fill(page_data['seo_title'])
                seo_title_filled = True
                break
        except:
            continue
    
    if not seo_title_filled:
        print("⚠ Could not find SEO title field")
    
    # Meta description selectors
    desc_selectors = [
        'textarea[name="yoast_wpseo_metadesc"]',
        'textarea[id="yoast_wpseo_metadesc"]',
        'textarea[aria-label*="meta description"]',
        'textarea[placeholder*="meta description"]',
        '#yoast_wpseo_metadesc',
        'textarea[name*="meta_desc"]',
        'textarea[id*="meta_desc"]'
    ]
    
    for selector in desc_selectors:
        try:
            element = await page.query_selector(selector)
            if element:
                print(f"✓ Found description field: {selector}")
                await element.fill(page_data['meta_description'])
                meta_desc_filled = True
                break
        except:
            continue
    
    if not meta_desc_filled:
        print("⚠ Could not find meta description field")
    
    # Save/Update the page
    print("Saving page...")
    update_buttons = [
        '#publish',
        'button[type="submit"]',
        '.editor-post-publish-button__button',
        'text=Update',
        'text=Save'
    ]
    
    for selector in update_buttons:
        try:
            elements = await page.query_selector_all(selector)
            for elem in elements:
                text = await elem.inner_text()
                if 'update' in text.lower() or 'save' in text.lower() or 'publish' in text.lower():
                    print(f"✓ Clicking update button")
                    await elem.click()
                    await asyncio.sleep(3)
                    break
        except:
            continue
    
    # Check for success message
    success = False
    success_indicators = [
        'text=updated',
        'text=Page updated',
        '.notice-success',
        '.is-success',
        'text=Changes published'
    ]
    
    for selector in success_indicators:
        try:
            elements = await page.query_selector_all(selector)
            if elements:
                print("✓ Page updated successfully")
                success = True
                break
        except:
            continue
    
    if not success:
        print("⚠ Could not confirm update")
    
    return {
        "id": page_data['id'],
        "name": page_data['name'],
        "seo_title": page_data['seo_title'],
        "meta_description": page_data['meta_description'],
        "title_filled": seo_title_filled,
        "desc_filled": meta_desc_filled,
        "updated": success
    }

async def verify_frontend(page, page_data):
    """Verify meta tags on frontend."""
    print(f"\n=== Verifying Frontend: Page {page_data['id']} ===")
    
    try:
        await page.goto(page_data['frontend_url'])
        await page.wait_for_load_state('networkidle')
        
        # Get page source
        content = await page.content()
        
        # Check for title in meta tags
        title_in_meta = page_data['seo_title'] in content
        desc_in_meta = page_data['meta_description'] in content
        
        # Also check page source for meta tags
        title_match = False
        desc_match = False
        
        if f'<title>{page_data["seo_title"]}</title>' in content:
            title_match = True
            print("✓ SEO title found in <title> tag")
        
        if f'meta name="description" content="{page_data["meta_description"]}"' in content:
            desc_match = True
            print("✓ Meta description found")
        
        # Check for "Tonic" in description
        tonic_in_desc = "Tonic" in page_data['meta_description']
        
        return {
            "id": page_data['id'],
            "name": page_data['name'],
            "title_length": len(page_data['seo_title']),
            "desc_length": len(page_data['meta_description']),
            "tonic_in_desc": tonic_in_desc,
            "title_in_source": title_in_meta or title_match,
            "desc_in_source": desc_in_meta or desc_match,
            "verified": (title_in_meta or title_match) and (desc_in_meta or desc_match)
        }
    except Exception as e:
        print(f"⚠ Error verifying: {e}")
        return {
            "id": page_data['id'],
            "name": page_data['name'],
            "error": str(e),
            "verified": False
        }

async def main():
    print("🦞 Starting Yoast SEO update script...")
    print(f"WordPress: {WP_ADMIN_URL}")
    print(f"Username: {WP_USERNAME}")
    
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=False)
        context = await browser.new_context(
            viewport={'width': 1920, 'height': 1080}
        )
        page = await context.new_page()
        
        # Login
        logged_in = await login_to_wordpress(page)
        if not logged_in:
            print("✗ Login failed. Aborting.")
            await browser.close()
            return
        
        # Update each page
        print("\n" + "="*60)
        print("UPDATING PAGES")
        print("="*60)
        
        results = []
        for page_data in PAGES:
            result = await update_yoast_fields(page, page_data)
            results.append(result)
            await asyncio.sleep(2)  # Pause between pages
        
        # Verify frontends
        print("\n" + "="*60)
        print("VERIFYING FRONTENDS")
        print("="*60)
        
        verification_results = []
        for page_data in PAGES:
            v_result = await verify_frontend(page, page_data)
            verification_results.append(v_result)
        
        # Final report
        print("\n" + "="*60)
        print("FINAL REPORT")
        print("="*60)
        
        for v in verification_results:
            status = "✓" if v.get('verified') else "⚠"
            print(f"\n{status} Page {v['id']} ({v['name']})")
            if 'error' not in v:
                print(f"   Title length: {v['title_length']} chars")
                print(f"   Description length: {v['desc_length']} chars")
                print(f"   'Tonic' in description: {v['tonic_in_desc']}")
                print(f"   Title in source: {v.get('title_in_source', False)}")
                print(f"   Description in source: {v.get('desc_in_source', False)}")
            else:
                print(f"   Error: {v['error']}")
        
        await browser.close()
        print("\n✓ Script completed")

if __name__ == "__main__":
    asyncio.run(main())
