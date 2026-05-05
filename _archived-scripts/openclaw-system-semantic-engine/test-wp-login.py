#!/usr/bin/env python3
"""Quick test: Login to Rank Ray WordPress and verify access."""
import os, time, json
from pathlib import Path

from playwright.sync_api import sync_playwright

WORKSPACE = Path("/Users/sheikhown/.openclaw/workspace")
BROWSER_PROFILE = WORKSPACE / ".browser-profiles" / "rankray-wp"

WP_BASE = "https://www.rankray.com"
WP_ADMIN = f"{WP_BASE}/wp-admin"
WP_USER = os.environ.get("RANKRAY_WP_USER", "openclaw")
WP_PASS = os.environ.get("RANKRAY_WP_PASS", "OpenClaw")

with sync_playwright() as p:
    BROWSER_PROFILE.mkdir(parents=True, exist_ok=True)
    
    context = p.chromium.launch_persistent_context(
        user_data_dir=str(BROWSER_PROFILE),
        headless=True,
        viewport={"width": 1440, "height": 900},
        user_agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36",
        args=["--disable-blink-features=AutomationControlled", "--no-sandbox"],
        ignore_https_errors=True,
    )
    context.add_init_script("Object.defineProperty(navigator, 'webdriver', {get: () => undefined});")
    
    page = context.new_page()
    page.set_default_timeout(45000)
    
    print("[1] Navigating to wp-admin...")
    page.goto(WP_ADMIN, wait_until="domcontentloaded")
    time.sleep(3)
    print(f"[1] URL: {page.url}")
    
    login_form = page.query_selector("#loginform")
    if login_form:
        print("[1] Login form found, authenticating...")
        page.fill("#user_login", WP_USER)
        time.sleep(1)
        page.fill("#user_pass", WP_PASS)
        time.sleep(1)
        page.click("#wp-submit")
        page.wait_for_load_state("domcontentloaded")
        time.sleep(4)
        print(f"[1] After login URL: {page.url}")
        
        # Check for errors
        error = page.query_selector("#login_error, .message.error")
        if error:
            print(f"[1] LOGIN ERROR: {error.text_content()}")
        
        admin_bar = page.query_selector("#wpadminbar, #adminmenu")
        print(f"[1] Admin bar present: {bool(admin_bar)}")
    else:
        admin_bar = page.query_selector("#wpadminbar, #adminmenu")
        if admin_bar:
            print("[1] Already logged in!")
        else:
            print(f"[1] No login form, no admin bar. URL: {page.url}")
    
    page.screenshot(path=str(WORKSPACE / "semantic-engine" / "screenshots" / "test-login.png"))
    print(f"[1] Screenshot saved")
    
    # Test media upload page
    print("[2] Testing media-new page...")
    page.goto(f"{WP_ADMIN}/media-new.php", wait_until="domcontentloaded")
    time.sleep(3)
    print(f"[2] Media URL: {page.url}")
    
    file_input = page.query_selector("#async-upload")
    print(f"[2] File input found: {bool(file_input)}")
    
    upload_btn = page.query_selector("#html-upload")
    print(f"[2] HTML upload button found: {bool(upload_btn)}")
    
    page.screenshot(path=str(WORKSPACE / "semantic-engine" / "screenshots" / "test-media.png"))
    print("[2] Screenshot saved")
    
    # Test post-new page
    print("[3] Testing post-new page...")
    page.goto(f"{WP_ADMIN}/post-new.php", wait_until="domcontentloaded")
    time.sleep(5)
    print(f"[3] Post-new URL: {page.url}")
    
    # Check editor type
    gutenberg = page.query_selector(".editor-post-title, .block-editor")
    classic = page.query_selector("#post-body-content, #content")
    print(f"[3] Gutenberg: {bool(gutenberg)}, Classic: {bool(classic)}")
    
    page.screenshot(path=str(WORKSPACE / "semantic-engine" / "screenshots" / "test-post-new.png"))
    print("[3] Screenshot saved")
    
    context.close()
    print("[DONE] Test complete.")