#!/usr/bin/env python3
"""
Update Yoast SEO fields using Playwright with persistent login.
"""

import asyncio
import json
from playwright.async_api import async_playwright

WP_ADMIN_URL = "https://tonicphysio.com/wp-admin"
WP_USERNAME = "rankrayagency@gmail.com"
WP_PASSWORD = "RR#Tonic@2026"

PAGES = [
    {
        "id": 11603,
        "name": "B-Pulse Pelvic Floor",
        "edit_url": "https://tonicphysio.com/wp-admin/post.php?post=11603&action=edit",
        "frontend_url": "https://tonicphysio.com/b-pulse-pelvic-floor-strengthening/",
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

async def main():
    print("🦞 Starting Yoast SEO update...")
    
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=False)
        context = await browser.new_context(viewport={'width': 1920, 'height': 1080})
        page = await context.new_page()
        
        # Login
        print("\n=== Logging in ===")
        await page.goto(WP_ADMIN_URL)
        await page.wait_for_load_state('networkidle')
        
        if 'wp-login' in page.url:
            await page.fill('#user_login', WP_USERNAME)
            await page.fill('#user_pass', WP_PASSWORD)
            await page.click('#wp-submit')
            await page.wait_for_load_state('networkidle')
            await asyncio.sleep(2)
        
        if 'wp-login' in page.url:
            print("✗ Login failed - please check credentials")
            await browser.close()
            return
        
        print("✓ Logged in")
        
        # Update each page
        results = []
        for page_data in PAGES:
            print(f"\n=== Page {page_data['id']}: {page_data['name']} ===")
            
            # Navigate to edit page
            await page.goto(page_data['edit_url'])
            await page.wait_for_load_state('networkidle')
            await asyncio.sleep(3)
            
            # Take screenshot for debugging
            await page.screenshot(path=f'/tmp/wp-page-{page_data["id"]}-before.png')
            
            # Find and click Yoast section
            print("Looking for Yoast SEO section...")
            
            # Scroll down
            await page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
            await asyncio.sleep(2)
            
            # Try to find Yoast panel
            yoast_selectors = [
                '.yoast-seo-settings',
                '[data-id="yoast-seo"]',
                '.wpseo-metabox',
                'text=Yoast SEO',
                'text=SEO'
            ]
            
            yoast_clicked = False
            for selector in yoast_selectors:
                try:
                    elements = await page.query_selector_all(selector)
                    for elem in elements:
                        await elem.scroll_into_view_if_needed()
                        await asyncio.sleep(0.5)
                        # Check if visible
                        is_visible = await elem.is_visible()
                        if is_visible:
                            print(f"Found: {selector}")
                            # Click to expand
                            await elem.click()
                            await asyncio.sleep(2)
                            yoast_clicked = True
                            break
                    if yoast_clicked:
                        break
                except Exception as e:
                    continue
            
            # Fill SEO title
            title_filled = False
            title_selectors = [
                'input[name="yoast_wpseo_focuskw"]',
                '#yoast_wpseo_focuskw',
                'input[aria-label*="SEO title"]',
                'input[id*="yoast"][id*="title"]'
            ]
            
            for selector in title_selectors:
                try:
                    elem = await page.query_selector(selector)
                    if elem:
                        await elem.fill(page_data['seo_title'])
                        print(f"✓ Filled title in {selector}")
                        title_filled = True
                        break
                except:
                    continue
            
            if not title_filled:
                print("⚠ Title field not found")
            
            # Fill meta description
            desc_filled = False
            desc_selectors = [
                'textarea[name="yoast_wpseo_metadesc"]',
                '#yoast_wpseo_metadesc',
                'textarea[aria-label*="meta description"]',
                'textarea[id*="yoast"][id*="desc"]'
            ]
            
            for selector in desc_selectors:
                try:
                    elem = await page.query_selector(selector)
                    if elem:
                        await elem.fill(page_data['meta_description'])
                        print(f"✓ Filled description in {selector}")
                        desc_filled = True
                        break
                except:
                    continue
            
            if not desc_filled:
                print("⚠ Description field not found")
            
            # Save
            print("Saving...")
            save_selectors = ['#publish', 'button[type="submit"]', 'text=Update']
            
            for selector in save_selectors:
                try:
                    elements = await page.query_selector_all(selector)
                    for elem in elements:
                        text = await elem.inner_text()
                        if 'update' in text.lower() or 'publish' in text.lower():
                            await elem.click()
                            print("✓ Clicked save")
                            await asyncio.sleep(3)
                            break
                except:
                    continue
            
            # Screenshot after
            await page.screenshot(path=f'/tmp/wp-page-{page_data["id"]}-after.png')
            
            results.append({
                "id": page_data['id'],
                "name": page_data['name'],
                "title_filled": title_filled,
                "desc_filled": desc_filled
            })
            
            await asyncio.sleep(2)
        
        # Verify frontends
        print("\n=== Verifying Frontends ===")
        for page_data in PAGES:
            print(f"\nPage {page_data['id']}: {page_data['frontend_url']}")
            await page.goto(page_data['frontend_url'])
            await page.wait_for_load_state('networkidle')
            
            content = await page.content()
            
            title_found = page_data['seo_title'] in content
            desc_found = page_data['meta_description'] in content
            tonic_found = "Tonic" in page_data['meta_description']
            
            print(f"  Title ({len(page_data['seo_title'])} chars): {'✓' if title_found else '⚠'}")
            print(f"  Description ({len(page_data['meta_description'])} chars): {'✓' if desc_found else '⚠'}")
            print(f"  'Tonic' in description: {'✓' if tonic_found else '✗'}")
        
        await browser.close()
        
        print("\n=== Summary ===")
        for r in results:
            print(f"Page {r['id']} ({r['name']}): title={r['title_filled']}, desc={r['desc_filled']}")

if __name__ == "__main__":
    asyncio.run(main())
