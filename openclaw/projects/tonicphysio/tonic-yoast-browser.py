#!/usr/bin/env python3
"""Update Rank Math SEO fields for 5 Tonic Physio pages via browser automation."""

from playwright.sync_api import sync_playwright
import time

WP_URL = "https://tonicphysio.com/wp-admin"
USERNAME = "rankrayagency@gmail.com"
APP_PASSWORD = "4isf Zcbd pvGI O1fp lQKB Jz2M"

PAGES = [
    {
        "id": 11603,
        "name": "B-Pulse Pelvic Floor",
        "title": "B-Pulse Pelvic Floor Strengthening Milton | Tonic Physio",
        "description": "B-Pulse pelvic floor strengthening in Milton at Tonic Physio. Expert treatment for postpartum recovery, incontinence & pelvic pain. Book consultation."
    },
    {
        "id": 6971,
        "name": "Joint Pain and Stiffness",
        "title": "Joint Pain Treatment Milton | Tonic Physio",
        "description": "Relieve joint pain and stiffness in Milton at Tonic Physio. Expert physiotherapy for arthritis, injury & chronic pain. Book your appointment."
    },
    {
        "id": 1791,
        "name": "Orthopedic Physiotherapy",
        "title": "Orthopedic Physiotherapy Milton | Tonic Physio",
        "description": "Expert orthopedic physiotherapy in Milton at Tonic Physio. Joint & muscle rehab, post-surgery recovery & pain relief. Book assessment today."
    },
    {
        "id": 1793,
        "name": "Pediatric Physiotherapy",
        "title": "Pediatric Physiotherapy Milton | Tonic Physio",
        "description": "Pediatric physiotherapy in Milton at Tonic Physio. Expert care for children with developmental delays, injuries & mobility issues. Book now."
    },
    {
        "id": 6587,
        "name": "Hot Stone Massage",
        "title": "Hot Stone Massage Milton | Tonic Physio",
        "description": "Hot stone massage in Milton at Tonic Physio. Therapeutic heat therapy for muscle tension, stress relief & relaxation. Book your session."
    }
]

def update_page(page, browser):
    """Update a single page's SEO fields."""
    context = browser.new_context()
    p = context.new_page()
    
    try:
        # Navigate to edit page
        edit_url = f"https://tonicphysio.com/wp-admin/post.php?post={page['id']}&action=edit"
        print(f"  Navigating to {edit_url}...")
        p.goto(edit_url, wait_until="networkidle", timeout=60000)
        time.sleep(3)
        
        # Check if we need to log in
        if "wp-login" in p.url:
            print("  Logging in...")
            p.fill("#user_login", USERNAME)
            p.fill("#user_pass", APP_PASSWORD)
            p.click("#wp-submit")
            p.wait_for_load_state("networkidle", timeout=30000)
            time.sleep(3)
        
        # Look for Rank Math meta box
        print("  Looking for Rank Math fields...")
        
        # Try to find and click the Rank Math SEO score/metabox
        rank_math_selectors = [
            '[data-testid="rank-math-meta-box"]',
            '.rank-math-meta-box',
            '#rank_math_meta',
            '[class*="rank-math"]',
            'button[class*="rank-math"]',
            'div[class*="rank-math"]'
        ]
        
        # Scroll down to find the meta box
        p.evaluate("window.scrollTo(0, document.body.scrollHeight)")
        time.sleep(2)
        
        # Try to find Rank Math section
        found = False
        for selector in rank_math_selectors:
            if p.is_visible(selector):
                print(f"  Found Rank Math with selector: {selector}")
                found = True
                break
        
        # Look for SEO title and description fields
        # Rank Math typically uses these field names
        seo_title_field = p.locator('input[name*="seo_title"], input[id*="seo_title"], input[placeholder*="SEO title"]')
        meta_desc_field = p.locator('textarea[name*="meta_description"], textarea[id*="meta_description"], textarea[placeholder*="meta description"]')
        
        # Alternative: look for Rank Math snippet editor
        snippet_button = p.locator('button[class*="snippet-editor"], button[data-testid*="snippet"]')
        if snippet_button.is_visible():
            print("  Clicking snippet editor...")
            snippet_button.click()
            time.sleep(2)
        
        # Try generic approach: find inputs near "SEO" or "Meta" labels
        if not seo_title_field.is_visible():
            print("  Searching for SEO title field...")
            # Look for any input after text containing "SEO Title"
            seo_title_field = p.locator('input[aria-label*="title"], input[name*="title"]').first
        
        if not meta_desc_field.is_visible():
            print("  Searching for meta description field...")
            meta_desc_field = p.locator('textarea[aria-label*="description"], textarea[name*="description"]').first
        
        # Fill the fields
        if seo_title_field.is_visible():
            print(f"  Setting title: {page['title']}")
            seo_title_field.fill(page['title'])
            time.sleep(1)
        else:
            print("  WARNING: Could not find SEO title field")
        
        if meta_desc_field.is_visible():
            print(f"  Setting description: {page['description']}")
            meta_desc_field.fill(page['description'])
            time.sleep(1)
        else:
            print("  WARNING: Could not find meta description field")
        
        # Click Update button
        print("  Saving page...")
        update_button = p.locator('#publishing-action .button-primary, button[type="submit"].button-primary')
        if update_button.is_visible():
            update_button.click()
            p.wait_for_load_state("networkidle", timeout=30000)
            time.sleep(2)
            print(f"  ✓ Page {page['id']} updated successfully")
            return True
        else:
            print(f"  ✗ Could not find Update button for page {page['id']}")
            return False
            
    except Exception as e:
        print(f"  ✗ Error updating page {page['id']}: {str(e)}")
        return False
    finally:
        context.close()

def main():
    print("Starting browser automation for Tonic Physio SEO updates...")
    print(f"Target: {WP_URL}")
    print(f"Pages to update: {len(PAGES)}")
    print()
    
    with sync_playwright() as playwright:
        browser = playwright.chromium.launch(headless=True)
        print("Browser launched")
        
        success_count = 0
        for page_info in PAGES:
            print(f"\nUpdating: {page_info['name']} (ID: {page_info['id']})")
            if update_page(page_info, browser):
                success_count += 1
            time.sleep(2)
        
        browser.close()
        
        print(f"\n{'='*50}")
        print(f"Completed: {success_count}/{len(PAGES)} pages updated successfully")
        return success_count == len(PAGES)

if __name__ == "__main__":
    success = main()
    exit(0 if success else 1)
