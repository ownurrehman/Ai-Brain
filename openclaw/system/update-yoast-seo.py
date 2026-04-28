#!/usr/bin/env python3
"""
Update Yoast SEO fields on 5 Tonic Physio WordPress pages.
Uses Playwright for browser automation.
"""

import asyncio
from playwright.async_api import async_playwright

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

WP_USERNAME = "rankrayagency@gmail.com"
WP_PASSWORD = "RR#Tonic@2026"

async def update_page(page, context):
    """Update Yoast SEO fields for a single page."""
    print(f"\n=== Updating Page {page['id']}: {page['name']} ===")
    
    # Navigate to edit page
    print(f"Navigating to: {page['edit_url']}")
    await context.goto(page['edit_url'])
    await context.wait_for_load_state('networkidle')
    
    # Wait for editor to load
    await context.wait_for_selector('body.post-php', timeout=30000)
    print("Editor loaded")
    
    # Scroll down to find Yoast SEO section
    # Yoast is typically below the Gutenberg editor
    await context.evaluate("window.scrollTo(0, document.body.scrollHeight)")
    await asyncio.sleep(2)
    
    # Look for Yoast SEO metabox
    # Try to find the Yoast SEO section - it may be collapsed
    yoast_selectors = [
        '#yoast-seo-post-scraper-options-section',
        '[data-id="yoast-seo"]',
        '.yoast',
        'div[id*="yoast"]',
        '.wpseo-metabox'
    ]
    
    yoast_found = False
    for selector in yoast_selectors:
        try:
            element = await context.query_selector(selector)
            if element:
                print(f"Found Yoast element with selector: {selector}")
                yoast_found = True
                # If it's a collapsible section, expand it
                await element.click()
                await asyncio.sleep(1)
                break
        except:
            continue
    
    if not yoast_found:
        print("Yoast section not immediately visible, scrolling and searching...")
        # Scroll through the page to find Yoast
        for i in range(5):
            await context.evaluate(f"window.scrollTo(0, {i * 500})")
            await asyncio.sleep(1)
    
    # Look for "Edit snippet" button or SEO fields
    # Yoast typically has fields for SEO title and meta description
    seo_title_selector = 'input[name="yoast_wpseo_focuskw"]'
    meta_desc_selector = 'textarea[name="yoast_wpseo_metadesc"]'
    
    # Alternative selectors for Yoast SEO
    alt_title_selectors = [
        'input[id*="yoast"][id*="title"]',
        'input[name*="yoast"][name*="title"]',
        '.yoast-seo-title input',
        '#yoast_wpseo_focuskw'
    ]
    
    alt_desc_selectors = [
        'textarea[id*="yoast"][id*="desc"]',
        'textarea[name*="yoast"][name*="desc"]',
        '.yoast-seo-meta-desc textarea',
        '#yoast_wpseo_metadesc'
    ]
    
    # Try to find and fill SEO title
    title_filled = False
    for selector in alt_title_selectors:
        try:
            element = await context.query_selector(selector)
            if element:
                print(f"Found title field: {selector}")
                await element.fill(page['seo_title'])
                title_filled = True
                break
        except Exception as e:
            continue
    
    if not title_filled:
        print(f"WARNING: Could not find SEO title field for page {page['id']}")
    
    # Try to find and fill meta description
    desc_filled = False
    for selector in alt_desc_selectors:
        try:
            element = await context.query_selector(selector)
            if element:
                print(f"Found description field: {selector}")
                await element.fill(page['meta_description'])
                desc_filled = True
                break
        except Exception as e:
            continue
    
    if not desc_filled:
        print(f"WARNING: Could not find meta description field for page {page['id']}")
    
    # Click Update/Save button
    update_selectors = [
        '#publish',
        'button[type="submit"]',
        '.editor-post-publish-button',
        'input[id="publish"]'
    ]
    
    for selector in update_selectors:
        try:
            element = await context.query_selector(selector)
            if element:
                print(f"Clicking update button: {selector}")
                await element.click()
                break
        except:
            continue
    
    # Wait for update confirmation
    print("Waiting for update confirmation...")
    await asyncio.sleep(3)
    
    # Check for success message
    success_selectors = [
        '.notice-success',
        '.is-success',
        'text=updated',
        'text=Page updated'
    ]
    
    updated = False
    for selector in success_selectors:
        try:
            element = await context.query_selector(selector)
            if element:
                text = await element.inner_text()
                if 'update' in text.lower() or 'success' in text.lower():
                    print(f"✓ Page updated successfully")
                    updated = True
                    break
        except:
            continue
    
    if not updated:
        print(f"⚠ Could not confirm update for page {page['id']}")
    
    return {
        "id": page['id'],
        "name": page['name'],
        "title_filled": title_filled,
        "desc_filled": desc_filled,
        "updated": updated
    }

async def verify_frontend(page_data, context):
    """Verify meta tags on frontend."""
    print(f"\n=== Verifying Frontend for Page {page_data['id']} ===")
    
    try:
        await context.goto(page_data['frontend_url'])
        await context.wait_for_load_state('networkidle')
        
        # Get page source
        content = await context.content()
        
        # Check for title
        title_match = False
        if page_data['seo_title'] in content:
            title_match = True
            print(f"✓ SEO title found in page source")
        
        # Check for meta description
        desc_match = False
        if page_data['meta_description'] in content:
            desc_match = True
            print(f"✓ Meta description found in page source")
        
        # Check for "Tonic" in description
        tonic_match = "Tonic" in page_data['meta_description']
        
        # Calculate lengths
        title_len = len(page_data['seo_title'])
        desc_len = len(page_data['meta_description'])
        
        return {
            "id": page_data['id'],
            "name": page_data['name'],
            "title_length": title_len,
            "desc_length": desc_len,
            "tonic_in_desc": tonic_match,
            "verified": title_match and desc_match
        }
    except Exception as e:
        print(f"Error verifying frontend: {e}")
        return {
            "id": page_data['id'],
            "name": page_data['name'],
            "error": str(e)
        }

async def main():
    print("Starting Yoast SEO update script...")
    print(f"Username: {WP_USERNAME}")
    
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=False)
        context = await browser.new_context(
            viewport={'width': 1920, 'height': 1080}
        )
        page = await context.new_page()
        
        # Login to WordPress
        print("\n=== Logging into WordPress ===")
        await page.goto("https://tonicphysio.com/wp-admin")
        await page.wait_for_load_state('networkidle')
        
        # Check if already logged in
        current_url = page.url
        if 'wp-admin' in current_url and 'login' not in current_url:
            print("Already logged in!")
        else:
            # Find login form
            print("Logging in...")
            await page.fill('#user_login', WP_USERNAME)
            
            if WP_PASSWORD:
                await page.fill('#user_pass', WP_PASSWORD)
                await page.click('#wp-submit')
                await page.wait_for_load_state('networkidle')
                print("Login submitted")
            else:
                print("⚠ No password provided. Please login manually in the browser window.")
                print("Script will pause for 60 seconds for manual login...")
                await asyncio.sleep(60)
        
        # Verify we're logged in
        if 'wp-login' in page.url:
            print("ERROR: Login failed. Please check credentials.")
            await browser.close()
            return
        
        print("✓ Logged in successfully")
        
        # Update each page
        results = []
        for page_data in PAGES:
            result = await update_page(page_data, page)
            results.append(result)
            await asyncio.sleep(2)  # Brief pause between pages
        
        print("\n=== Update Summary ===")
        for r in results:
            status = "✓" if r['updated'] else "⚠"
            print(f"{status} Page {r['id']} ({r['name']}): title={r['title_filled']}, desc={r['desc_filled']}, updated={r['updated']}")
        
        # Verify frontends
        print("\n=== Frontend Verification ===")
        verification_results = []
        for page_data in PAGES:
            v_result = await verify_frontend(page_data, page)
            verification_results.append(v_result)
        
        print("\n=== Final Verification Report ===")
        for v in verification_results:
            if 'error' in v:
                print(f"Page {v['id']} ({v['name']}): ERROR - {v['error']}")
            else:
                status = "✓" if v['verified'] else "⚠"
                print(f"{status} Page {v['id']}: title_len={v['title_length']}, desc_len={v['desc_length']}, tonic_in_desc={v['tonic_in_desc']}")
        
        await browser.close()
        print("\n✓ Script completed")

if __name__ == "__main__":
    asyncio.run(main())
