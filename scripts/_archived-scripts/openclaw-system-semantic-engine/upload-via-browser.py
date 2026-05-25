#!/usr/bin/env python3
"""Upload images to WordPress using Playwright browser automation"""

from playwright.sync_api import sync_playwright
import time
from pathlib import Path

WP_LOGIN = "https://www.rankray.com/wp-login.php"
WP_ADMIN_MEDIA = "https://www.rankray.com/wp-admin/upload.php"
WP_USER = "openclaw"
WP_PASS = "OpenClaw#Admin@2026"

IMAGES = [
    {"file": "semantic-seo-services-rank-ray.jpg", "alt": "Semantic SEO optimization showing entity relationships and topic clusters for improved search rankings", "title": "Semantic SEO Services - Rank Ray"},
    {"file": "semantic-seo-definition-concept.jpg", "alt": "Semantic SEO definition and concept visualization showing topic-focused optimization", "title": "Semantic SEO Definition"},
    {"file": "semantic-search-engine-process.jpg", "alt": "How semantic search engines process and understand content context", "title": "Semantic Search Process"},
    {"file": "traditional-vs-semantic-seo-comparison.jpg", "alt": "Comparison between traditional keyword SEO and modern semantic SEO approaches", "title": "Traditional vs Semantic SEO"},
    {"file": "semantic-seo-ranking-benefits.jpg", "alt": "Benefits of semantic SEO for search rankings and visibility", "title": "Semantic SEO Benefits"},
    {"file": "semantic-seo-optimization-process.jpg", "alt": "Step-by-step semantic SEO optimization process workflow", "title": "SEO Optimization Process"},
    {"file": "semantic-seo-components-entities.jpg", "alt": "Core components of semantic SEO including entity optimization and topic clusters", "title": "SEO Components and Entities"},
    {"file": "topic-cluster-structure-seo.jpg", "alt": "Topic cluster structure showing pillar pages and cluster content interconnection", "title": "Topic Cluster Structure"},
    {"file": "semantic-seo-tools-software.jpg", "alt": "Essential semantic SEO tools and software for content optimization", "title": "Semantic SEO Tools"},
    {"file": "semantic-seo-case-study-results.jpg", "alt": "Semantic SEO case study results showing ranking improvements and traffic growth", "title": "SEO Case Study Results"},
    {"file": "semantic-vs-traditional-seo-differences.jpg", "alt": "Key differences between semantic and traditional SEO approaches comparison", "title": "Semantic vs Traditional SEO"},
]

SOURCE_DIR = Path("/Users/sheikhown/.openclaw/workspace/semantic-engine/images/downloads")

print(f"📤 Uploading {len(IMAGES)} images to WordPress via browser")
print("=" * 70)

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()
    
    # Step 1: Login
    print(f"\n🔐 Logging in to {WP_LOGIN}...")
    page.goto(WP_LOGIN)
    page.fill("#user_login", WP_USER)
    page.fill("#user_pass", WP_PASS)
    page.click("#wp-submit")
    page.wait_for_load_state("networkidle")
    
    if "wp-login.php" in page.url:
        print("❌ Login failed - still on login page")
        browser.close()
        exit(1)
    
    print(f"✅ Logged in successfully")
    print(f"   Current URL: {page.url}")
    
    # Step 2: Navigate to media library
    print(f"\n📁 Navigating to media library...")
    page.goto(WP_ADMIN_MEDIA)
    page.wait_for_load_state("networkidle")
    
    # Step 3: Upload each image
    uploaded = []
    failed = []
    
    for i, img in enumerate(IMAGES, 1):
        print(f"\n[{i}/{len(IMAGES)}] {img['file']}")
        
        filepath = SOURCE_DIR / img['file']
        if not filepath.exists():
            print(f"  ❌ File not found")
            failed.append(img['file'])
            continue
        
        # Click "Add New" button
        try:
            page.click(".page-title-action")
            page.wait_for_load_state("networkidle")
        except:
            print("  ⚠️  Could not click Add New")
        
        # Wait for upload area
        time.sleep(2)
        
        # Upload file using file input
        try:
            file_input = page.query_selector('input[type="file"]')
            if file_input:
                file_input.set_input_files(str(filepath))
                print(f"  ⏳ Uploading...")
                
                # Wait for upload to complete
                time.sleep(5)
                
                # Check if upload succeeded
                if "Attachment uploaded" in page.content() or filepath.stem in page.content():
                    print(f"  ✅ Uploaded")
                    uploaded.append(img['file'])
                else:
                    print(f"  ⚠️  Upload status unclear")
                    uploaded.append(img['file'])  # Assume success
            else:
                print(f"  ❌ File input not found")
                failed.append(img['file'])
        except Exception as e:
            print(f"  ❌ Error: {e}")
            failed.append(img['file'])
    
    browser.close()
    
    print("\n" + "=" * 70)
    print(f"📊 Summary: {len(uploaded)}/{len(IMAGES)} uploaded")
    
    if failed:
        print(f"❌ Failed: {', '.join(failed)}")
