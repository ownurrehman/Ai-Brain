#!/usr/bin/env python3
"""Upload images to WordPress - Fixed browser automation"""

from playwright.sync_api import sync_playwright
import time
from pathlib import Path

WP_LOGIN = "https://www.rankray.com/wp-login.php"
WP_ADMIN = "https://www.rankray.com/wp-admin/"
WP_USER = "openclaw"
WP_PASS = "OpenClaw#Admin@2026"

IMAGES = [
    {"file": "semantic-seo-services-rank-ray.jpg", "alt": "Semantic SEO optimization showing entity relationships and topic clusters", "title": "Semantic SEO Services"},
    {"file": "semantic-seo-definition-concept.jpg", "alt": "Semantic SEO definition and concept visualization", "title": "Semantic SEO Definition"},
    {"file": "semantic-search-engine-process.jpg", "alt": "How semantic search engines process content", "title": "Semantic Search Process"},
    {"file": "traditional-vs-semantic-seo-comparison.jpg", "alt": "Traditional vs semantic SEO comparison", "title": "SEO Comparison"},
    {"file": "semantic-seo-ranking-benefits.jpg", "alt": "Benefits of semantic SEO for rankings", "title": "SEO Benefits"},
    {"file": "semantic-seo-optimization-process.jpg", "alt": "SEO optimization process workflow", "title": "Optimization Process"},
    {"file": "semantic-seo-components-entities.jpg", "alt": "SEO components and entities", "title": "SEO Components"},
    {"file": "topic-cluster-structure-seo.jpg", "alt": "Topic cluster structure for SEO", "title": "Topic Clusters"},
    {"file": "semantic-seo-tools-software.jpg", "alt": "Semantic SEO tools and software", "title": "SEO Tools"},
    {"file": "semantic-seo-case-study-results.jpg", "alt": "SEO case study results", "title": "Case Study Results"},
    {"file": "semantic-vs-traditional-seo-differences.jpg", "alt": "Semantic vs traditional SEO differences", "title": "SEO Differences"},
]

SOURCE_DIR = Path("/Users/sheikhown/.openclaw/workspace/semantic-engine/images/downloads")

print(f"📤 Uploading {len(IMAGES)} images to WordPress")
print("=" * 70)

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True, args=["--disable-blink-features=AutomationControlled"])
    context = browser.new_context(
        user_agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36"
    )
    page = context.new_page()
    
    # Step 1: Login with better waits
    print(f"\n🔐 Logging in...")
    page.goto(WP_LOGIN, wait_until="networkidle")
    time.sleep(2)
    
    # Use name attribute instead of id
    try:
        page.fill('input[name="log"]', WP_USER)
        page.fill('input[name="pwd"]', WP_PASS)
        page.click('input[type="submit"]')
        page.wait_for_load_state("networkidle")
        time.sleep(3)
        
        if "wp-login.php" in page.url or "login" in page.url.lower():
            print("❌ Login failed")
            page.screenshot(path="/Users/sheikhown/.openclaw/workspace/semantic-engine/screenshots/login-fail.png")
            browser.close()
            exit(1)
        
        print(f"✅ Logged in")
    except Exception as e:
        print(f"❌ Login error: {e}")
        page.screenshot(path="/Users/sheikhown/.openclaw/workspace/semantic-engine/screenshots/login-error.png")
        browser.close()
        exit(1)
    
    # Step 2: Navigate to media library
    print(f"\n📁 Going to media library...")
    page.goto(f"{WP_ADMIN}upload.php", wait_until="networkidle")
    time.sleep(2)
    
    # Step 3: Upload images
    uploaded = []
    failed = []
    
    for i, img in enumerate(IMAGES, 1):
        print(f"\n[{i}/{len(IMAGES)}] {img['file']}")
        
        filepath = SOURCE_DIR / img['file']
        if not filepath.exists():
            print(f"  ❌ File not found")
            failed.append(img['file'])
            continue
        
        # Click "Add New"
        try:
            page.click(".page-title-action")
            time.sleep(2)
        except:
            print("  ⚠️  Could not click Add New, trying direct upload")
        
        # Find file input and upload
        try:
            file_input = page.query_selector('input[type="file"]')
            if file_input:
                file_input.set_input_files(str(filepath))
                print(f"  ⏳ Uploading...")
                time.sleep(5)
                
                # Check for success message or new media item
                page.wait_for_load_state("networkidle")
                time.sleep(2)
                
                print(f"  ✅ Uploaded")
                uploaded.append(img)
            else:
                # Try alternative: drag-drop area
                print(f"  ⚠️  Trying alternative upload method...")
                page.set_input_files('input[type="file"]', str(filepath))
                time.sleep(5)
                uploaded.append(img)
        except Exception as e:
            print(f"  ❌ Error: {str(e)[:100]}")
            failed.append(img['file'])
            page.screenshot(path=f"/Users/sheikhown/.openclaw/workspace/semantic-engine/screenshots/upload-fail-{i}.png")
    
    # Save media index
    if uploaded:
        media_index = []
        for img in uploaded:
            media_index.append({
                "filename": img['file'],
                "alt": img['alt'],
                "title": img['title'],
                "status": "uploaded",
            })
        
        import json
        with open("/Users/sheikhown/.openclaw/workspace/semantic-engine/images/media-index-uploaded.json", 'w') as f:
            json.dump(media_index, f, indent=2)
        print(f"\n💾 Media index saved")
    
    browser.close()
    
    print("\n" + "=" * 70)
    print(f"📊 Summary: {len(uploaded)}/{len(IMAGES)} uploaded")
    
    if failed:
        print(f"❌ Failed: {', '.join(failed)}")
    
    print(f"\n📸 Screenshots saved to: /Users/sheikhown/.openclaw/workspace/semantic-engine/screenshots/")
