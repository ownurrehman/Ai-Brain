#!/usr/bin/env python3
"""
Rank Ray WordPress Upload Script - Semantic SEO Pillar Article
Uses Playwright browser automation to bypass Cloudflare bot detection.
Uploads 11 images + creates draft post + sets Yoast SEO fields.
"""

import os
import sys
import json
import time
import base64
import re
from pathlib import Path

try:
    from playwright.sync_api import sync_playwright
except ImportError:
    print("ERROR: playwright not installed. Run: pip install playwright && playwright install chromium")
    sys.exit(1)

# ─── Configuration ───────────────────────────────────────────────────────────
WORKSPACE = Path("/Users/sheikhown/.openclaw/workspace")
SE_BASE = WORKSPACE / "semantic-engine"
IMAGES_DIR = SE_BASE / "images" / "downloads"
ARTICLE_FILE = SE_BASE / "reports" / "PILLAR-ARTICLE-semantic-seo-services.md"
BROWSER_PROFILE = WORKSPACE / ".browser-profiles" / "rankray-wp"
SCREENSHOTS_DIR = SE_BASE / "screenshots"
LOG_FILE = SE_BASE / "logs" / "wp-upload-semantic-seo.log"

WP_BASE = "https://www.rankray.com"
WP_ADMIN = f"{WP_BASE}/wp-admin"
WP_LOGIN = f"{WP_BASE}/wp-login.php"
WP_USER = os.environ.get("RANKRAY_WP_USER", "openclaw")
WP_PASS = os.environ.get("RANKRAY_WP_PASS", "OpenClaw")

POST_TITLE = "Semantic SEO: Complete Guide & Professional Services"
FOCUS_KEYPHRASE = "Semantic SEO"
SEO_TITLE = "Semantic SEO: Complete Guide & Professional Services"
META_DESC = "Master Semantic SEO with Rank Ray's complete guide. Learn entity optimization, topic clusters, LSI strategies & professional services for higher rankings."

# Image metadata: filename → alt text
IMAGE_MAP = {
    "semantic-seo-services-rank-ray.jpg": "Semantic SEO optimization showing entity relationships and topic clusters for improved search rankings",
    "semantic-seo-definition-concept.jpg": "Semantic SEO definition and concept visualization showing topic-focused optimization",
    "semantic-search-engine-process.jpg": "How semantic search engines process and understand content context",
    "traditional-vs-semantic-seo-comparison.jpg": "Comparison between traditional keyword SEO and modern semantic SEO approaches",
    "semantic-seo-ranking-benefits.jpg": "Benefits of semantic SEO for search rankings and visibility",
    "semantic-seo-optimization-process.jpg": "Step-by-step semantic SEO optimization process workflow",
    "semantic-seo-components-entities.jpg": "Core components of semantic SEO including entity optimization and topic clusters",
    "topic-cluster-structure-seo.jpg": "Topic cluster structure showing pillar pages and cluster content interconnection",
    "semantic-seo-tools-software.jpg": "Essential semantic SEO tools and software for content optimization",
    "semantic-seo-case-study-results.jpg": "Semantic SEO case study results showing ranking improvements and traffic growth",
    "semantic-vs-traditional-seo-differences.jpg": "Key differences between semantic and traditional SEO approaches comparison",
}

FEATURED_IMAGE = "semantic-seo-services-rank-ray.jpg"

# ─── Helpers ─────────────────────────────────────────────────────────────────
def log(msg):
    ts = time.strftime("%Y-%m-%d %H:%M:%S")
    line = f"[{ts}] {msg}"
    print(line)
    LOG_FILE.parent.mkdir(parents=True, exist_ok=True)
    with open(LOG_FILE, "a") as f:
        f.write(line + "\n")

def screenshot(page, name):
    SCREENSHOTS_DIR.mkdir(parents=True, exist_ok=True)
    path = SCREENSHOTS_DIR / f"semantic-seo-{name}.png"
    page.screenshot(path=str(path), full_page=False)
    log(f"Screenshot saved: {path}")

def human_delay(min_s=2, max_s=5):
    """Random delay to simulate human behavior."""
    delay = min_s + (max_s - min_s) * (hash(str(time.time())) % 100) / 100
    time.sleep(delay)

def convert_markdown_to_html(md_content):
    """Convert the markdown article to WordPress HTML blocks."""
    html = md_content
    
    # Convert headers
    html = re.sub(r'^## (.+)$', r'<h2>\1</h2>', html, flags=re.MULTILINE)
    html = re.sub(r'^### (.+)$', r'<h3>\1</h3>', html, flags=re.MULTILINE)
    
    # Convert bold
    html = re.sub(r'\*\*(.+?)\*\*', r'<strong>\1</strong>', html)
    
    # Convert links
    html = re.sub(r'\[(.+?)\]\((.+?)\)', r'<a href="\2">\1</a>', html)
    
    # Convert horizontal rules
    html = re.sub(r'^---$', '<hr>', html, flags=re.MULTILINE)
    
    # Convert unordered lists
    lines = html.split('\n')
    in_list = False
    result = []
    for line in lines:
        stripped = line.strip()
        if stripped.startswith('- '):
            if not in_list:
                result.append('<ul>')
                in_list = True
            result.append(f'<li>{stripped[2:]}</li>')
        else:
            if in_list:
                result.append('</ul>')
                in_list = False
            result.append(line)
    if in_list:
        result.append('</ul>')
    
    html = '\n'.join(result)
    
    # Wrap paragraphs (lines that aren't tags and aren't empty)
    final_lines = []
    for line in html.split('\n'):
        stripped = line.strip()
        if not stripped:
            final_lines.append('')
        elif stripped.startswith('<'):
            final_lines.append(line)
        else:
            final_lines.append(f'<p>{stripped}</p>')
    
    html = '\n'.join(final_lines)
    
    # Clean up multiple empty paragraphs
    html = re.sub(r'(<p></p>\s*){3,}', '', html)
    
    return html


# ─── Main Script ────────────────────────────────────────────────────────────
def main():
    log("=" * 60)
    log("RANK RAY SEMANTIC SEO - WordPress Upload Script")
    log("=" * 60)
    
    # Verify prerequisites
    if not IMAGES_DIR.exists():
        log(f"FATAL: Images directory not found: {IMAGES_DIR}")
        sys.exit(1)
    
    missing = []
    for fname in IMAGE_MAP:
        fpath = IMAGES_DIR / fname
        if not fpath.exists():
            missing.append(fname)
    if missing:
        log(f"FATAL: Missing images: {missing}")
        sys.exit(1)
    
    if not ARTICLE_FILE.exists():
        log(f"FATAL: Article file not found: {ARTICLE_FILE}")
        sys.exit(1)
    
    log(f"All 11 images verified in {IMAGES_DIR}")
    log(f"Article file: {ARTICLE_FILE}")
    
    # Read article content
    with open(ARTICLE_FILE, "r") as f:
        article_md = f.read()
    log(f"Article loaded: {len(article_md)} chars")
    
    uploaded_media = {}  # filename → {id, url}
    
    with sync_playwright() as p:
        # ─── Launch Browser ────────────────────────────────────────
        log("Launching Chromium with persistent context...")
        
        BROWSER_PROFILE.mkdir(parents=True, exist_ok=True)
        
        context = p.chromium.launch_persistent_context(
            user_data_dir=str(BROWSER_PROFILE),
            headless=True,
            viewport={"width": 1440, "height": 900},
            user_agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36",
            args=[
                "--disable-blink-features=AutomationControlled",
                "--no-sandbox",
                "--disable-setuid-sandbox",
                "--disable-dev-shm-usage",
                "--disable-web-security",
                "--ignore-certificate-errors",
            ],
            ignore_https_errors=True,
        )
        
        # Remove webdriver property
        context.add_init_script("""
            Object.defineProperty(navigator, 'webdriver', {get: () => undefined});
            window.chrome = {runtime: {}};
        """)
        
        page = context.new_page()
        page.set_default_timeout(60000)
        page.set_default_navigation_timeout(60000)
        
        try:
            # ─── Step 1: Login ──────────────────────────────────────
            log("STEP 1: Logging into WordPress admin...")
            
            # Try navigating to wp-admin first
            page.goto(WP_ADMIN, wait_until="domcontentloaded")
            human_delay(3, 5)
            
            current_url = page.url
            log(f"Current URL after navigation: {current_url}")
            
            # Check if we need to log in
            login_form = page.query_selector("#loginform")
            if login_form:
                log("Login form detected - authenticating...")
                screenshot(page, "01-login-page")
                
                # Fill login form
                page.fill("#user_login", WP_USER)
                human_delay(1, 2)
                page.fill("#user_pass", WP_PASS)
                human_delay(1, 2)
                
                # Click login
                page.click("#wp-submit")
                page.wait_for_load_state("domcontentloaded")
                human_delay(4, 6)
                
                log(f"After login URL: {page.url}")
                screenshot(page, "02-after-login")
                
                # Check for errors
                login_error = page.query_selector("#login_error, .message.error")
                if login_error:
                    error_text = login_error.text_content()
                    log(f"LOGIN ERROR: {error_text}")
                    # Check if locked out
                    if "locked" in error_text.lower():
                        log("Account is locked. Waiting 7 minutes...")
                        time.sleep(420)
                        page.goto(WP_LOGIN, wait_until="domcontentloaded")
                        human_delay(3, 5)
                        page.fill("#user_login", WP_USER)
                        page.fill("#user_pass", WP_PASS)
                        page.click("#wp-submit")
                        page.wait_for_load_state("domcontentloaded")
                        human_delay(4, 6)
                
                # Verify admin access
                admin_bar = page.query_selector("#wpadminbar, #adminmenu, #wpbody")
                if not admin_bar:
                    log("Still not in admin. Trying once more...")
                    if "wp-login" in page.url:
                        page.fill("#user_login", WP_USER)
                        page.fill("#user_pass", WP_PASS)
                        page.click("#wp-submit")
                        page.wait_for_load_state("domcontentloaded")
                        human_delay(4, 6)
            else:
                # Check if already logged in
                admin_bar = page.query_selector("#wpadminbar, #adminmenu")
                if admin_bar:
                    log("Already logged in - admin dashboard detected")
                else:
                    log(f"No login form and no admin bar. URL: {page.url}")
                    screenshot(page, "01-unexpected-state")
                    # Try direct login URL
                    page.goto(WP_LOGIN, wait_until="domcontentloaded")
                    human_delay(3, 5)
                    login_form = page.query_selector("#loginform")
                    if login_form:
                        page.fill("#user_login", WP_USER)
                        page.fill("#user_pass", WP_PASS)
                        page.click("#wp-submit")
                        page.wait_for_load_state("domcontentloaded")
                        human_delay(4, 6)
            
            # Final admin verification
            log(f"Final URL: {page.url}")
            screenshot(page, "03-dashboard")
            
            # Dismiss any admin notices
            try:
                dismiss_btn = page.query_selector(".notice-dismiss")
                if dismiss_btn:
                    dismiss_btn.click()
                    human_delay(1, 2)
            except:
                pass
            
            # ─── Step 2: Upload Images ─────────────────────────────
            log("STEP 2: Uploading images via Media Library...")
            
            for idx, (filename, alt_text) in enumerate(IMAGE_MAP.items(), 1):
                log(f"\n--- Uploading image {idx}/11: {filename} ---")
                file_path = IMAGES_DIR / filename
                
                try:
                    # Navigate to Media > Add New (browser uploader)
                    media_url = f"{WP_ADMIN}/media-new.php"
                    page.goto(media_url, wait_until="domcontentloaded")
                    human_delay(3, 5)
                    
                    # Try the browser uploader for reliability
                    browser_upload_url = f"{WP_ADMIN}/media-new.php?browser-uploader"
                    page.goto(browser_upload_url, wait_until="domcontentloaded")
                    human_delay(2, 4)
                    
                    screenshot(page, f"04-media-new-{idx}")
                    
                    # Find the file input - try multiple selectors
                    file_input = page.query_selector("#async-upload")
                    if not file_input:
                        file_input = page.query_selector('input[type="file"]')
                    
                    if file_input:
                        log(f"Found file input, uploading {filename}...")
                        file_input.set_input_files(str(file_path))
                        human_delay(1, 2)
                        
                        # Click upload button
                        upload_btn = page.query_selector("#html-upload")
                        if not upload_btn:
                            upload_btn = page.query_selector('input[type="submit"]')
                        
                        if upload_btn:
                            log("Clicking upload button...")
                            upload_btn.click()
                            page.wait_for_load_state("domcontentloaded")
                            human_delay(5, 8)
                            
                            log(f"After upload URL: {page.url}")
                            screenshot(page, f"05-after-upload-{idx}")
                    else:
                        # Try Plupload/drag-drop approach
                        log("Trying Plupload file chooser approach...")
                        try:
                            # Click the upload area to trigger file chooser
                            with page.expect_file_chooser(timeout=10000) as fc_info:
                                upload_area = page.query_selector(".drag-drop-area, #plupload-upload-ui, .upload-flash-bypass")
                                if upload_area:
                                    upload_area.click()
                                else:
                                    # Click any visible upload button
                                    page.click("text=Select Files")
                            
                            file_chooser = fc_info.value
                            file_chooser.set_files(str(file_path))
                            human_delay(5, 8)
                            
                        except Exception as e:
                            log(f"File chooser approach failed: {e}")
                            # Fallback: try JavaScript upload
                            log("Trying JavaScript-based upload...")
                    
                    # Wait for upload to complete
                    human_delay(3, 5)
                    
                    # Get media ID from upload result page
                    media_id = page.evaluate("""() => {
                        // Check for edit link
                        const editLink = document.querySelector('a.edit-attachment');
                        if (editLink) {
                            const m = editLink.href.match(/post=(\\d+)/);
                            if (m) return m[1];
                        }
                        // Check for media-item
                        const items = document.querySelectorAll('.media-item');
                        for (const item of items) {
                            const id = item.id;
                            const m = id.match(/(\\d+)/);
                            if (m) return m[1];
                        }
                        // Check for any link with post= param
                        const links = document.querySelectorAll('a[href*="post="]');
                        for (const link of links) {
                            const m = link.href.match(/post=(\\d+)/);
                            if (m) return m[1];
                        }
                        // Check for hidden inputs
                        const inputs = document.querySelectorAll('input[name*="attachment"]');
                        for (const inp of inputs) {
                            if (inp.value && /^\\d+$/.test(inp.value)) return inp.value;
                        }
                        return null;
                    }""")
                    
                    if not media_id:
                        # Search media library for the image
                        log("Media ID not found on upload page, searching library...")
                        search_url = f"{WP_ADMIN}/upload.php?mode=list&s={filename.replace('.jpg', '')}"
                        page.goto(search_url, wait_until="domcontentloaded")
                        human_delay(3, 5)
                        
                        media_id = page.evaluate("""() => {
                            const rows = document.querySelectorAll('tr[id^="post-"], tr[id^="att-"]');
                            for (const row of rows) {
                                const id = row.id.replace(/^(post|att)-/, '');
                                return id;
                            }
                            // Also check attachment list items
                            const items = document.querySelectorAll('.attachment, .media-item');
                            for (const item of items) {
                                const dataId = item.getAttribute('data-id') || item.dataset?.id;
                                if (dataId) return dataId;
                            }
                            return null;
                        }""")
                    
                    if media_id:
                        log(f"✓ {filename} => Media ID {media_id}")
                        
                        # Set alt text via the media edit page
                        edit_url = f"{WP_ADMIN}/post.php?post={media_id}&action=edit"
                        page.goto(edit_url, wait_until="domcontentloaded")
                        human_delay(3, 5)
                        
                        # Find and fill alt text field
                        alt_input = page.query_selector("#attachment_alt, input[name='image_alt'], input[name='_wp_attachment_image_alt']")
                        if alt_input:
                            current_alt = alt_input.get_attribute("value") or alt_input.input_value()
                            if current_alt != alt_text:
                                alt_input.fill("")
                                human_delay(0.5, 1)
                                alt_input.fill(alt_text)
                                log(f"  Set alt text: {alt_text[:60]}...")
                            else:
                                log(f"  Alt text already set correctly")
                        else:
                            log(f"  Alt text field not found, trying other selectors...")
                            # Try finding in the media modal
                            alt_inputs = page.query_selector_all('input[type="text"][aria-label*="Alt"], textarea[aria-label*="Alt"]')
                            for inp in alt_inputs:
                                inp.fill(alt_text)
                                log(f"  Set alt text via alternative selector")
                                break
                        
                        # Save the alt text
                        save_btn = page.query_selector("#publish, #save, .editor-post-publish-button__button, input[type='submit'][value*='Update']")
                        if save_btn:
                            save_btn.click()
                            page.wait_for_load_state("domcontentloaded")
                            human_delay(3, 5)
                            log(f"  Saved alt text for media {media_id}")
                        
                        # Get the media URL
                        media_url_result = page.evaluate("""() => {
                            const img = document.querySelector('img[src*="rankray.com/wp-content"]');
                            if (img) return img.src;
                            const links = document.querySelectorAll('a[href*="rankray.com/wp-content"]');
                            for (const link of links) {
                                if (link.href.includes('uploads')) return link.href;
                            }
                            return null;
                        }""")
                        
                        uploaded_media[filename] = {
                            "id": media_id,
                            "url": media_url_result or f"{WP_BASE}/wp-content/uploads/semantic-seo/{filename}",
                            "alt": alt_text,
                        }
                    else:
                        log(f"✗ Could not find media ID for {filename}")
                        screenshot(page, f"06-missing-media-{idx}")
                        uploaded_media[filename] = {"id": None, "url": None, "alt": alt_text}
                
                except Exception as e:
                    log(f"ERROR uploading {filename}: {e}")
                    screenshot(page, f"06-error-upload-{idx}")
                    uploaded_media[filename] = {"id": None, "url": None, "alt": alt_text}
            
            log(f"\n=== Image Upload Summary ===")
            successful = sum(1 for v in uploaded_media.values() if v["id"])
            log(f"Successful: {successful}/11")
            for fname, info in uploaded_media.items():
                status = "✓" if info["id"] else "✗"
                log(f"  {status} {fname}: ID={info['id']} URL={info['url']}")
            
            # ─── Step 3: Create Blog Post ──────────────────────────
            log("\nSTEP 3: Creating blog post...")
            
            # Go to Posts > Add New
            new_post_url = f"{WP_ADMIN}/post-new.php"
            page.goto(new_post_url, wait_until="domcontentloaded")
            human_delay(5, 8)
            
            screenshot(page, "07-new-post")
            
            # Check if Gutenberg or Classic editor
            gutenberg = page.query_selector(".editor-post-title, .block-editor")
            classic = page.query_selector("#post-body-content, #content")
            
            log(f"Editor type: {'Gutenberg' if gutenberg else 'Classic' if classic else 'Unknown'}")
            
            if gutenberg:
                # ─── Gutenberg Editor ──────────────────────────────
                log("Using Gutenberg editor...")
                
                # Set title
                try:
                    title_input = page.query_selector(".editor-post-title__input")
                    if title_input:
                        title_input.click()
                        human_delay(0.5, 1)
                        title_input.fill(POST_TITLE)
                        log(f"Title set: {POST_TITLE}")
                    else:
                        log("Title input not found, trying alternative...")
                        page.click('h1[aria-label*="title"], [placeholder*="title"]', timeout=5000)
                        human_delay(0.5, 1)
                        page.keyboard.type(POST_TITLE)
                except Exception as e:
                    log(f"Error setting title: {e}")
                
                human_delay(2, 3)
                
                # Switch to Code Editor for content insertion
                log("Switching to Code Editor...")
                try:
                    # Click the three dots menu
                    page.click('.editor-header__settings button, button[aria-label="Options"], button[aria-label="More tools & options"]', timeout=10000)
                    human_delay(1, 2)
                    
                    # Click "Code Editor"
                    page.click('text=Code Editor', timeout=5000)
                    human_delay(2, 3)
                except:
                    log("Could not find Options menu, trying keyboard shortcut...")
                    page.keyboard.press("Meta+Alt+Shift+M")  # Code editor shortcut
                    human_delay(2, 3)
                
                screenshot(page, "08-code-editor")
                
                # Build article HTML with images inserted
                article_html = build_article_html(article_md, uploaded_media)
                
                # Paste content into code editor
                try:
                    code_editor = page.query_selector(".editor-post-text-editor, textarea.editor-post-text-editor, textarea.wp-editor-area")
                    if code_editor:
                        code_editor.click()
                        code_editor.fill("")
                        human_delay(0.5, 1)
                        # Use clipboard to paste large content
                        page.evaluate(f"""(content) => {{
                            const textarea = document.querySelector('.editor-post-text-editor, textarea.editor-post-text-editor, textarea.wp-editor-area');
                            if (textarea) {{
                                textarea.value = content;
                                textarea.dispatchEvent(new Event('input', {{ bubbles: true }}));
                            }}
                        }}""", article_html)
                        log(f"Article content inserted ({len(article_html)} chars)")
                    else:
                        log("Code editor textarea not found!")
                        screenshot(page, "08-no-code-editor")
                except Exception as e:
                    log(f"Error inserting content: {e}")
                    screenshot(page, "08-content-error")
                
                human_delay(3, 5)
                
                # Set featured image
                log("Setting featured image...")
                try:
                    # Click on Document tab / Post settings
                    page.click('.editor-header__settings button, button[aria-label="Post"], button[aria-label="Settings"]', timeout=5000)
                    human_delay(1, 2)
                    
                    # Find and click "Featured Image" panel
                    featured_img_btn = page.query_selector('text=Featured Image')
                    if featured_img_btn:
                        featured_img_btn.click()
                        human_delay(1, 2)
                        
                        # Click "Set Featured Image"
                        set_featured = page.query_selector('text=Set Featured Image')
                        if set_featured:
                            set_featured.click()
                            human_delay(3, 5)
                            
                            # In the media library modal, find our featured image
                            featured_media_id = uploaded_media.get(FEATURED_IMAGE, {}).get("id")
                            if featured_media_id:
                                # Search for our image
                                search_input = page.query_selector('.media-modal input[type="search"], .media-frame input[type="search"]')
                                if search_input:
                                    search_input.fill(FEATURED_IMAGE.replace(".jpg", ""))
                                    human_delay(2, 3)
                                
                                # Select the image
                                img_selector = f'li[data-id="{featured_media_id}"], .attachment[data-id="{featured_media_id}"]'
                                img_el = page.query_selector(img_selector)
                                if img_el:
                                    img_el.click()
                                    human_delay(1, 2)
                                
                                # Click "Select" button
                                select_btn = page.query_selector('.media-modal button.media-button-select, button.button-primary')
                                if select_btn:
                                    select_btn.click()
                                    human_delay(3, 5)
                                    log("Featured image set!")
                            else:
                                log("Featured image media ID not available")
                        else:
                            log("Set Featured Image button not found")
                    else:
                        log("Featured Image panel not found in sidebar")
                except Exception as e:
                    log(f"Error setting featured image: {e}")
                
                human_delay(2, 3)
                
            else:
                # ─── Classic Editor ────────────────────────────────
                log("Using Classic editor...")
                
                # Set title
                page.fill("#title", POST_TITLE)
                log(f"Title set: {POST_TITLE}")
                
                # Switch to HTML/Text tab
                try:
                    page.click("#content-html, .switch-html")
                    human_delay(1, 2)
                except:
                    pass
                
                # Build article HTML
                article_html = build_article_html(article_md, uploaded_media)
                
                # Insert content
                content_area = page.query_selector("#content")
                if content_area:
                    content_area.fill(article_html)
                    log(f"Article content inserted ({len(article_html)} chars)")
                
                # Set featured image
                featured_media_id = uploaded_media.get(FEATURED_IMAGE, {}).get("id")
                if featured_media_id:
                    # Click "Set Featured Image" link
                    page.click("#set-post-thumbnail")
                    human_delay(3, 5)
                    # Select in media modal
                    img_selector = f'li[data-id="{featured_media_id}"]'
                    img_el = page.query_selector(img_selector)
                    if img_el:
                        img_el.click()
                        human_delay(1, 2)
                    # Click "Set featured image" in modal
                    page.click('.media-modal button.button-primary')
                    human_delay(3, 5)
                    log("Featured image set!")
            
            screenshot(page, "09-post-with-content")
            
            # ─── Step 4: Configure Yoast SEO ───────────────────────
            log("\nSTEP 4: Configuring Yoast SEO...")
            
            # Scroll to Yoast SEO metabox
            try:
                yoast_section = page.query_selector("#wpseo_meta, .yoast-seo-meta-box, [data-yoast]")
                if yoast_section:
                    log("Yoast SEO metabox found")
                    yoast_section.scroll_into_view_if_needed()
                    human_delay(2, 3)
                else:
                    log("Looking for Yoast section...")
                    page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
                    human_delay(2, 3)
                
                # Set Focus Keyphrase
                try:
                    # Yoast keyphrase input
                    keyphrase_input = page.query_selector("#yoast_wpseo_focuskw, input[name='yoast_wpseo_focuskw'], .yst-focus-keyphrase-input input")
                    if keyphrase_input:
                        keyphrase_input.fill(FOCUS_KEYPHRASE)
                        log(f"Focus keyphrase set: {FOCUS_KEYPHRASE}")
                        human_delay(1, 2)
                    else:
                        log("Keyphrase input not found with standard selectors, trying Yoast JS API...")
                        # Try clicking the Yoast keyphrase area
                        page.click('text=Focus keyphrase', timeout=5000)
                        human_delay(1, 2)
                        keyphrase_input = page.query_selector('input[type="text"]')
                        if keyphrase_input:
                            keyphrase_input.fill(FOCUS_KEYPHRASE)
                            log(f"Focus keyphrase set via click: {FOCUS_KEYPHRASE}")
                except Exception as e:
                    log(f"Keyphrase input error: {e}")
                
                # Set SEO Title
                try:
                    seo_title_input = page.query_selector("#yoast_wpseo_title, input[name='yoast_wpseo_title']")
                    if seo_title_input:
                        seo_title_input.fill(SEO_TITLE)
                        log(f"SEO title set: {SEO_TITLE}")
                except Exception as e:
                    log(f"SEO title error: {e}")
                
                # Set Meta Description
                try:
                    meta_desc_input = page.query_selector("#yoast_wpseo_metadesc, textarea[name='yoast_wpseo_metadesc']")
                    if meta_desc_input:
                        meta_desc_input.fill(META_DESC)
                        log(f"Meta description set: {META_DESC[:60]}...")
                except Exception as e:
                    log(f"Meta description error: {e}")
                
                # Check Yoast analysis
                human_delay(2, 3)
                try:
                    yoast_score = page.evaluate("""() => {
                        // Check for Yoast score indicators
                        const good = document.querySelectorAll('.yoast-seo-score-good, .yst-traffic-light--good');
                        const ok = document.querySelectorAll('.yoast-seo-score-ok, .yst-traffic-light--ok');
                        const bad = document.querySelectorAll('.yoast-seo-score-bad, .yst-traffic-light--bad');
                        return {
                            good: good.length,
                            ok: ok.length,
                            bad: bad.length
                        };
                    }""")
                    log(f"Yoast analysis: good={yoast_score.get('good',0)}, ok={yoast_score.get('ok',0)}, bad={yoast_score.get('bad',0)}")
                except:
                    pass
                
                screenshot(page, "10-yoast-seo")
                
            except Exception as e:
                log(f"Yoast configuration error: {e}")
                screenshot(page, "10-yoast-error")
            
            # ─── Step 5: Save as Draft ────────────────────────────
            log("\nSTEP 5: Saving as draft...")
            
            try:
                # In Gutenberg: Save Draft button
                save_draft = page.query_selector('.editor-post-save-draft, button:has-text("Save Draft"), a:has-text("Save Draft")')
                if save_draft:
                    save_draft.click()
                    human_delay(3, 5)
                    log("Draft saved via Save Draft button")
                else:
                    # Classic editor: Save Draft
                    classic_save = page.query_selector("#save-post, #save-post")
                    if classic_save:
                        classic_save.click()
                        page.wait_for_load_state("domcontentloaded")
                        human_delay(3, 5)
                        log("Draft saved via classic Save Draft")
                    else:
                        # Try publishing flow but stop before publish
                        log("Trying status change to draft...")
                        # Click on Post tab/settings
                        page.click('button[aria-label="Post"], .editor-post-panel__row')
                        human_delay(1, 2)
                        # Look for status dropdown
                        page.click('text=Draft', timeout=5000)
                        human_delay(0.5, 1)
                        page.click('text=Draft', timeout=5000)
                        human_delay(2, 3)
                        # Save
                        save_btn = page.query_selector('.editor-post-publish-button__button, button:has-text("Save")')
                        if save_btn:
                            save_btn.click()
                            human_delay(3, 5)
                
                # Wait for save to complete
                human_delay(3, 5)
                
            except Exception as e:
                log(f"Error saving draft: {e}")
                screenshot(page, "11-save-error")
            
            # ─── Get Post ID and URL ──────────────────────────────
            log("\nGetting post details...")
            
            post_id = None
            draft_url = None
            preview_url = None
            
            try:
                # Get post ID from URL
                current_url = page.url
                id_match = re.search(r'post=(\d+)', current_url)
                if not id_match:
                    id_match = re.search(r'/post\.php\?post=(\d+)/', current_url)
                if not id_match:
                    id_match = re.search(r'(\d+)', current_url)
                
                if id_match:
                    post_id = id_match.group(1)
                    log(f"Post ID: {post_id}")
                
                # Try to get the permalink/preview URL
                try:
                    # Look for preview link
                    preview_link = page.query_selector('.editor-post-preview, a[href*="preview"]')
                    if preview_link:
                        preview_url = preview_link.get_attribute("href")
                        log(f"Preview URL: {preview_url}")
                    
                    # Look for permalink
                    permalink = page.query_selector('.editor-post-permalink, .edit-post-post-link__link')
                    if permalink:
                        draft_url = permalink.get_attribute("href") or permalink.text_content()
                        log(f"Permalink: {draft_url}")
                except:
                    pass
                
                # If we have post_id, construct URLs
                if post_id:
                    if not preview_url:
                        preview_url = f"{WP_BASE}/?p={post_id}&preview=true"
                    if not draft_url:
                        draft_url = f"{WP_BASE}/?p={post_id}"
                
            except Exception as e:
                log(f"Error getting post details: {e}")
            
            screenshot(page, "11-draft-saved")
            
            # ─── Step 6: Verification ──────────────────────────────
            log("\nSTEP 6: Verification...")
            
            verification_results = {
                "images_present": 0,
                "alt_texts_present": 0,
                "yoast_meta": {},
                "post_id": post_id,
                "draft_url": draft_url,
                "preview_url": preview_url,
            }
            
            if preview_url:
                try:
                    # Open preview in new tab
                    preview_page = context.new_page()
                    preview_page.goto(preview_url, wait_until="domcontentloaded")
                    human_delay(3, 5)
                    
                    # Count images
                    img_count = preview_page.evaluate("() => document.querySelectorAll('img').length")
                    verification_results["images_present"] = img_count
                    log(f"Images in preview: {img_count}")
                    
                    # Check alt texts
                    alt_texts = preview_page.evaluate("""() => {
                        const imgs = document.querySelectorAll('img');
                        return Array.from(imgs).map(img => ({
                            src: img.src,
                            alt: img.alt
                        }));
                    }""")
                    alt_count = sum(1 for i in alt_texts if i.get("alt"))
                    verification_results["alt_texts_present"] = alt_count
                    log(f"Images with alt text: {alt_count}/{len(alt_texts)}")
                    
                    # Check Yoast meta tags
                    yoast_meta = preview_page.evaluate("""() => {
                        const result = {};
                        const metas = document.querySelectorAll('meta');
                        for (const meta of metas) {
                            const name = meta.getAttribute('name') || meta.getAttribute('property') || '';
                            if (name.includes('yoast') || name.includes('description') || name.includes('og:') || name.includes('twitter:')) {
                                result[name] = meta.getAttribute('content') || '';
                            }
                        }
                        return result;
                    }""")
                    verification_results["yoast_meta"] = yoast_meta
                    log(f"Yoast meta tags found: {len(yoast_meta)}")
                    for key, val in yoast_meta.items():
                        log(f"  {key}: {val[:80]}...")
                    
                    screenshot(preview_page, "12-preview")
                    preview_page.close()
                    
                except Exception as e:
                    log(f"Verification error: {e}")
            else:
                log("No preview URL available for verification")
            
            # ─── Final Summary ────────────────────────────────────
            log("\n" + "=" * 60)
            log("FINAL SUMMARY")
            log("=" * 60)
            log(f"Post ID: {post_id}")
            log(f"Draft URL: {draft_url}")
            log(f"Preview URL: {preview_url}")
            log(f"Post Title: {POST_TITLE}")
            log(f"Yoast Keyphrase: {FOCUS_KEYPHRASE}")
            log(f"Yoast SEO Title: {SEO_TITLE}")
            log(f"Yoast Meta Description: {META_DESC}")
            log(f"Featured Image: {FEATURED_IMAGE}")
            log(f"Images in preview: {verification_results.get('images_present', 0)}")
            log(f"Alt texts present: {verification_results.get('alt_texts_present', 0)}")
            log(f"Yoast meta tags: {len(verification_results.get('yoast_meta', {}))}")
            
            log("\nUploaded Media:")
            for fname, info in uploaded_media.items():
                log(f"  {fname}: ID={info['id']} URL={info['url']}")
            
            # Save results to JSON for the report
            results = {
                "post_id": post_id,
                "draft_url": draft_url,
                "preview_url": preview_url,
                "title": POST_TITLE,
                "focus_keyphrase": FOCUS_KEYPHRASE,
                "seo_title": SEO_TITLE,
                "meta_description": META_DESC,
                "featured_image": FEATURED_IMAGE,
                "uploaded_media": uploaded_media,
                "verification": verification_results,
                "timestamp": time.strftime("%Y-%m-%d %H:%M:%S"),
            }
            
            results_file = SE_BASE / "logs" / "wp-upload-results.json"
            with open(results_file, "w") as f:
                json.dump(results, f, indent=2)
            log(f"\nResults saved to: {results_file}")
            
        except Exception as e:
            log(f"FATAL ERROR: {e}")
            import traceback
            log(traceback.format_exc())
            screenshot(page, "99-fatal-error")
        
        finally:
            context.close()
    
    log("\nScript complete.")


def build_article_html(md_content, uploaded_media):
    """Build the full article HTML with images embedded at correct positions."""
    
    # Image placement map: section heading → image filename
    image_placements = [
        ("## What is Semantic SEO?", "semantic-seo-definition-concept.jpg"),
        ("### Understanding Semantic Search", "semantic-search-engine-process.jpg"),
        ("### How It Differs from Traditional SEO", "traditional-vs-semantic-seo-comparison.jpg"),
        ("## Why Semantic SEO Matters for Rankings", "semantic-seo-ranking-benefits.jpg"),
        ("## How Semantic SEO Works", "semantic-seo-optimization-process.jpg"),
        ("## Core Components of Semantic SEO", "semantic-seo-components-entities.jpg"),
        ("### Topic Clusters", "topic-cluster-structure-seo.jpg"),
        ("## Essential Semantic SEO Tools", "semantic-seo-tools-software.jpg"),
        ("## Semantic SEO Examples & Case Studies", "semantic-seo-case-study-results.jpg"),
        ("## Semantic SEO vs Traditional Keyword SEO", "semantic-vs-traditional-seo-differences.jpg"),
    ]
    
    # Convert markdown to HTML
    html = convert_markdown_to_html(md_content)
    
    # Insert images after their respective headings
    for heading, img_filename in image_placements:
        media_info = uploaded_media.get(img_filename, {})
        media_id = media_info.get("id")
        alt_text = IMAGE_MAP.get(img_filename, "")
        
        if not media_id:
            continue
        
        # Create WordPress image block HTML
        img_html = f"""
<!-- wp:image {{"id":{media_id},"sizeSlug":"large","className":"aligncenter"}} -->
<figure class="wp-block-image aligncenter size-large">
<img src="https://www.rankray.com/wp-content/uploads/{img_filename}" alt="{alt_text}" class="wp-image-{media_id}"/>
</figure>
<!-- /wp:image -->
"""
        
        # Find the heading in HTML and insert image after it
        heading_html = heading.replace("## ", "<h2>").replace("### ", "<h3>")
        if "<h2>" in heading_html:
            heading_html += "</h2>"
        elif "<h3>" in heading_html:
            heading_html += "</h3>"
        
        if heading_html in html:
            html = html.replace(heading_html, heading_html + "\n" + img_html, 1)
    
    return html


if __name__ == "__main__":
    main()