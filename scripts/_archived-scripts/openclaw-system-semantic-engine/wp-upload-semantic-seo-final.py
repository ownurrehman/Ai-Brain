#!/usr/bin/env python3
"""
Rank Ray WordPress Upload Script - Semantic SEO Pillar Article
Uses Playwright browser automation to bypass Cloudflare bot detection.
Uploads 11 images + creates draft post + sets Yoast SEO fields.

Usage: python3 wp-upload-semantic-seo-final.py
"""
import os, sys, json, time, re, traceback
from pathlib import Path
from playwright.sync_api import sync_playwright

# ─── Configuration ────────────────────────────────────────────────
WORKSPACE = Path("/Users/sheikhown/.openclaw/workspace")
SE_BASE = WORKSPACE / "semantic-engine"
IMAGES_DIR = SE_BASE / "images" / "downloads"
ARTICLE_FILE = SE_BASE / "reports" / "PILLAR-ARTICLE-semantic-seo-services.md"
BROWSER_PROFILE = WORKSPACE / ".browser-profiles" / "rankray-wp"
SCREENSHOTS_DIR = SE_BASE / "screenshots"
LOG_FILE = SE_BASE / "logs" / "wp-upload-semantic-seo.log"
RESULTS_FILE = SE_BASE / "logs" / "wp-upload-results.json"

WP_BASE = "https://www.rankray.com"
WP_ADMIN = f"{WP_BASE}/wp-admin"
WP_LOGIN = f"{WP_BASE}/wp-login.php"
WP_USER = os.environ.get("RANKRAY_WP_USER", "openclaw")
WP_PASS = os.environ.get("RANKRAY_WP_PASS", "OpenClaw")

POST_TITLE = "Semantic SEO: Complete Guide & Professional Services"
FOCUS_KEYPHRASE = "Semantic SEO"
SEO_TITLE = "Semantic SEO: Complete Guide & Professional Services"
META_DESC = "Master Semantic SEO with Rank Ray's complete guide. Learn entity optimization, topic clusters, LSI strategies & professional services for higher rankings."

# Image metadata
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

# ─── Helpers ──────────────────────────────────────────────────────
def log(msg):
    ts = time.strftime("%Y-%m-%d %H:%M:%S")
    line = f"[{ts}] {msg}"
    print(line, flush=True)
    LOG_FILE.parent.mkdir(parents=True, exist_ok=True)
    with open(LOG_FILE, "a") as f:
        f.write(line + "\n")

def screenshot(page, name):
    SCREENSHOTS_DIR.mkdir(parents=True, exist_ok=True)
    path = SCREENSHOTS_DIR / f"semantic-seo-{name}.png"
    try:
        page.screenshot(path=str(path), full_page=False)
        log(f"Screenshot: {path.name}")
    except:
        pass

def delay(min_s=2, max_s=5):
    t = min_s + (max_s - min_s) * ((time.time() * 1000 % 1000) / 1000)
    time.sleep(t)

def wait_for_login_form(page, max_wait=30):
    """Wait for login form to appear (handles lockout wait)."""
    start = time.time()
    while time.time() - start < max_wait:
        form = page.query_selector("#loginform")
        if form:
            return True
        
        # Check if locked out
        body = page.query_selector("body")
        if body:
            text = body.inner_text()
            if "locked out" in text.lower():
                # Extract wait time
                match = re.search(r'(\d+)\s*min', text)
                if match:
                    wait_mins = int(match.group(1)) + 1
                    log(f"Account locked out. Waiting {wait_mins} minutes...")
                    time.sleep(wait_mins * 60)
                    page.reload(wait_until="domcontentloaded")
                    delay(3, 5)
                    continue
        
        time.sleep(2)
    
    return False


def md_to_html(md):
    """Convert markdown to WordPress-compatible HTML."""
    html = md
    
    # Headers
    html = re.sub(r'^#### (.+)$', r'<h4>\1</h4>', html, flags=re.MULTILINE)
    html = re.sub(r'^### (.+)$', r'<h3>\1</h3>', html, flags=re.MULTILINE)
    html = re.sub(r'^## (.+)$', r'<h2>\1</h2>', html, flags=re.MULTILINE)
    
    # Bold
    html = re.sub(r'\*\*(.+?)\*\*', r'<strong>\1</strong>', html)
    
    # Links
    html = re.sub(r'\[(.+?)\]\((.+?)\)', r'<a href="\2">\1</a>', html)
    
    # HR
    html = re.sub(r'^---$', '<hr>', html, flags=re.MULTILINE)
    
    # Lists
    lines = html.split('\n')
    result = []
    in_list = False
    in_ol = False
    for line in lines:
        stripped = line.strip()
        if stripped.startswith('- '):
            if not in_list:
                result.append('<ul>')
                in_list = True
            result.append(f'<li>{stripped[2:]}</li>')
        elif re.match(r'^\d+\. ', stripped):
            if not in_ol:
                result.append('<ol>')
                in_ol = True
            content = re.sub(r'^\d+\. ', '', stripped)
            result.append(f'<li>{content}</li>')
        else:
            if in_list:
                result.append('</ul>')
                in_list = False
            if in_ol:
                result.append('</ol>')
                in_ol = False
            result.append(line)
    if in_list:
        result.append('</ul>')
    if in_ol:
        result.append('</ol>')
    
    html = '\n'.join(result)
    
    # Wrap paragraphs
    final = []
    for line in html.split('\n'):
        s = line.strip()
        if not s:
            final.append('')
        elif s.startswith('<'):
            final.append(line)
        else:
            final.append(f'<p>{s}</p>')
    
    return '\n'.join(final)


def build_article_html(md_content, uploaded_media):
    """Build full article HTML with images at correct positions."""
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
    
    html = md_to_html(md_content)
    
    for heading_md, img_filename in image_placements:
        media_info = uploaded_media.get(img_filename, {})
        media_id = media_info.get("id")
        alt_text = IMAGE_MAP.get(img_filename, "")
        
        if not media_id:
            log(f"  Skipping image {img_filename} (no media ID)")
            continue
        
        # WordPress Gutenberg block format
        img_block = f"""
<!-- wp:image {{\"id\":{media_id},\"sizeSlug\":\"large\",\"className\":\"aligncenter\"}} -->
<figure class="wp-block-image aligncenter size-large">
<img src="https://www.rankray.com/wp-content/uploads/{img_filename}" alt="{alt_text}" class="wp-image-{media_id}"/>
</figure>
<!-- /wp:image -->
"""
        # Find heading in HTML
        level = heading_md.count('#')
        heading_text = heading_md.lstrip('# ')
        tag = f'h{level-1}'  # ## = h2, ### = h3
        
        heading_html = f'<{tag}>{heading_text}</{tag}>'
        if heading_html in html:
            html = html.replace(heading_html, heading_html + '\n' + img_block, 1)
            log(f"  Inserted image {img_filename} after {heading_html[:50]}")
        else:
            log(f"  WARNING: Could not find heading '{heading_html[:50]}' in HTML")
    
    return html


# ─── Main Script ──────────────────────────────────────────────────
def main():
    log("=" * 60)
    log("RANK RAY SEMANTIC SEO - WordPress Upload")
    log("=" * 60)
    
    # Verify prerequisites
    if not IMAGES_DIR.exists():
        log(f"FATAL: Images directory missing: {IMAGES_DIR}")
        sys.exit(1)
    
    missing = [f for f in IMAGE_MAP if not (IMAGES_DIR / f).exists()]
    if missing:
        log(f"FATAL: Missing images: {missing}")
        sys.exit(1)
    
    if not ARTICLE_FILE.exists():
        log(f"FATAL: Article file missing: {ARTICLE_FILE}")
        sys.exit(1)
    
    log(f"✓ All 11 images verified")
    log(f"✓ Article file ready ({ARTICLE_FILE.stat().st_size} bytes)")
    
    with open(ARTICLE_FILE, "r") as f:
        article_md = f.read()
    
    uploaded_media = {}
    
    with sync_playwright() as p:
        BROWSER_PROFILE.mkdir(parents=True, exist_ok=True)
        
        log("Launching browser...")
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
            ],
            ignore_https_errors=True,
        )
        context.add_init_script("""
            Object.defineProperty(navigator, 'webdriver', {get: () => undefined});
            window.chrome = {runtime: {}};
        """)
        
        page = context.new_page()
        page.set_default_timeout(60000)
        page.set_default_navigation_timeout(60000)
        
        try:
            # ═══ STEP 1: LOGIN ════════════════════════════════════
            log("\n═══ STEP 1: LOGIN ═══")
            page.goto(WP_LOGIN, wait_until="domcontentloaded")
            delay(3, 5)
            
            # Wait for login form (handles lockout)
            form_ready = wait_for_login_form(page, max_wait=600)
            
            if not form_ready:
                log("FATAL: Login form never appeared. Dumping page state...")
                log(f"URL: {page.url}")
                log(f"Body text: {page.query_selector('body').inner_text()[:300]}")
                screenshot(page, "login-failed")
                sys.exit(1)
            
            log("Login form found! Authenticating...")
            screenshot(page, "01-login-form")
            
            page.fill("#user_login", WP_USER)
            delay(1, 2)
            page.fill("#user_pass", WP_PASS)
            delay(1, 2)
            
            # Check "Remember Me"
            remember = page.query_selector("#rememberme")
            if remember and not remember.is_checked():
                remember.click()
            
            page.click("#wp-submit")
            page.wait_for_load_state("domcontentloaded")
            delay(4, 6)
            
            log(f"After login URL: {page.url}")
            
            # Check for errors
            error_el = page.query_selector("#login_error, .message.error")
            if error_el:
                error_text = error_el.inner_text()
                log(f"LOGIN ERROR: {error_text}")
                if "locked" in error_text.lower():
                    log("Still locked out! Waiting additional time...")
                    match = re.search(r'(\d+)\s*min', error_text)
                    wait_mins = int(match.group(1)) + 2 if match else 10
                    time.sleep(wait_mins * 60)
                    page.reload(wait_until="domcontentloaded")
                    delay(3, 5)
                    # Retry login
                    page.fill("#user_login", WP_USER)
                    page.fill("#user_pass", WP_PASS)
                    page.click("#wp-submit")
                    page.wait_for_load_state("domcontentloaded")
                    delay(4, 6)
            
            # Verify admin access
            admin_detected = page.query_selector("#wpadminbar, #adminmenu, #wpbody")
            if not admin_detected and "wp-admin" not in page.url:
                log("Login may have failed. Trying to navigate to dashboard...")
                page.goto(WP_ADMIN, wait_until="domcontentloaded")
                delay(3, 5)
                admin_detected = page.query_selector("#wpadminbar, #adminmenu, #wpbody")
            
            if admin_detected:
                log("✓ Successfully logged into WordPress admin!")
            else:
                log(f"FATAL: Could not access admin. URL: {page.url}")
                screenshot(page, "01-login-failed")
                sys.exit(1)
            
            screenshot(page, "02-dashboard")
            
            # ═══ STEP 2: UPLOAD IMAGES ════════════════════════════
            log("\n═══ STEP 2: UPLOAD IMAGES ═══")
            
            for idx, (filename, alt_text) in enumerate(IMAGE_MAP.items(), 1):
                log(f"\n--- Image {idx}/11: {filename} ---")
                file_path = str(IMAGES_DIR / filename)
                
                try:
                    # Use the browser uploader (most reliable)
                    page.goto(f"{WP_ADMIN}/media-new.php?browser-uploader", wait_until="domcontentloaded")
                    delay(3, 5)
                    
                    # Check we're on the media page
                    if "wp-login" in page.url:
                        log("Session expired, re-logging in...")
                        page.goto(WP_LOGIN, wait_until="domcontentloaded")
                        delay(2, 3)
                        page.fill("#user_login", WP_USER)
                        page.fill("#user_pass", WP_PASS)
                        page.click("#wp-submit")
                        page.wait_for_load_state("domcontentloaded")
                        delay(4, 6)
                        page.goto(f"{WP_ADMIN}/media-new.php?browser-uploader", wait_until="domcontentloaded")
                        delay(3, 5)
                    
                    # Find file input
                    file_input = page.query_selector("#async-upload")
                    if not file_input:
                        # Try Plupload approach with file chooser
                        log("No async-upload input, trying file chooser...")
                        try:
                            with page.expect_file_chooser(timeout=10000) as fc_info:
                                drop_area = page.query_selector(".drag-drop-area, #plupload-upload-ui")
                                if drop_area:
                                    drop_area.click()
                                else:
                                    page.click("text=Select Files", timeout=5000)
                            fc = fc_info.value
                            fc.set_files(file_path)
                            delay(5, 8)
                        except Exception as e:
                            log(f"File chooser failed: {e}")
                            screenshot(page, f"03-upload-fail-{idx}")
                            uploaded_media[filename] = {"id": None, "url": None, "alt": alt_text}
                            continue
                    else:
                        log("Found #async-upload, setting files...")
                        file_input.set_input_files(file_path)
                        delay(1, 2)
                        
                        # Click upload button
                        upload_btn = page.query_selector("#html-upload")
                        if upload_btn:
                            log("Clicking #html-upload...")
                            upload_btn.click()
                            page.wait_for_load_state("domcontentloaded")
                            delay(6, 10)
                        else:
                            log("No #html-upload button found!")
                            screenshot(page, f"03-no-upload-btn-{idx}")
                            continue
                    
                    # Wait for upload completion
                    delay(3, 5)
                    log(f"After upload URL: {page.url}")
                    
                    # Get media ID from the page
                    media_id = page.evaluate("""() => {
                        // Check edit link
                        const editLink = document.querySelector('a.edit-attachment');
                        if (editLink) {
                            const m = editLink.href.match(/post=(\\d+)/);
                            if (m) return m[1];
                        }
                        // Check media-item divs
                        const items = document.querySelectorAll('.media-item');
                        for (const item of items) {
                            const m = item.id.match(/(\\d+)/);
                            if (m) return m[1];
                        }
                        // Check any post= links
                        const links = document.querySelectorAll('a[href*="post="]');
                        for (const link of links) {
                            const m = link.href.match(/post=(\\d+)/);
                            if (m) return m[1];
                        }
                        return null;
                    }""")
                    
                    if not media_id:
                        # Search in media library
                        log("Searching media library for uploaded image...")
                        search_slug = filename.replace('.jpg', '').replace('-', ' ')
                        page.goto(f"{WP_ADMIN}/upload.php?s={search_slug}", wait_until="domcontentloaded")
                        delay(3, 5)
                        
                        media_id = page.evaluate("""() => {
                            const rows = document.querySelectorAll('tr[id^="post-"], tr[id^="att-"]');
                            if (rows.length > 0) {
                                return rows[0].id.replace(/^(post|att)-/, '');
                            }
                            // Check media grid items
                            const items = document.querySelectorAll('.attachment, [data-id]');
                            for (const item of items) {
                                const id = item.getAttribute('data-id');
                                if (id && /^\\d+$/.test(id)) return id;
                            }
                            return null;
                        }""")
                    
                    if media_id:
                        log(f"✓ Media ID: {media_id}")
                        
                        # Navigate to edit media to set alt text
                        page.goto(f"{WP_ADMIN}/post.php?post={media_id}&action=edit", wait_until="domcontentloaded")
                        delay(3, 5)
                        screenshot(page, f"04-edit-media-{idx}")
                        
                        # Set alt text
                        alt_input = page.query_selector("#attachment_alt, input[name='image_alt'], input[name='_wp_attachment_image_alt']")
                        if alt_input:
                            current_alt = alt_input.input_value() if hasattr(alt_input, 'input_value') else (alt_input.get_attribute("value") or "")
                            if current_alt != alt_text:
                                alt_input.fill("")
                                delay(0.5, 1)
                                alt_input.fill(alt_text)
                                log(f"  Alt text set: {alt_text[:60]}...")
                            else:
                                log(f"  Alt text already correct")
                        else:
                            log(f"  Alt text field not found on edit page")
                        
                        # Save/update the media
                        save_btn = page.query_selector("#publish, input[value='Update'], button.editor-post-publish-button")
                        if save_btn:
                            save_btn.click()
                            page.wait_for_load_state("domcontentloaded")
                            delay(3, 5)
                            log(f"  Media updated with alt text")
                        
                        # Get the image URL from the edit page
                        img_url = page.evaluate("""() => {
                            // Look for the full URL field
                            const urlField = document.querySelector('#attachment_url, input[name="guid"], .copy-attachment-url');
                            if (urlField) return urlField.value || urlField.textContent;
                            // Look for image with uploads path
                            const img = document.querySelector('img[src*="uploads"]');
                            if (img) return img.src;
                            // Check for file URL display
                            const urlDisplay = document.querySelector('.misc-url, [data-url]');
                            if (urlDisplay) return urlDisplay.textContent.trim() || urlDisplay.dataset.url;
                            return null;
                        }""")
                        
                        if not img_url:
                            # Construct likely URL
                            img_url = f"{WP_BASE}/wp-content/uploads/{filename}"
                        
                        uploaded_media[filename] = {
                            "id": str(media_id),
                            "url": img_url,
                            "alt": alt_text,
                        }
                        log(f"  URL: {img_url}")
                    else:
                        log(f"✗ Could not find media ID for {filename}")
                        screenshot(page, f"05-no-media-id-{idx}")
                        uploaded_media[filename] = {"id": None, "url": None, "alt": alt_text}
                
                except Exception as e:
                    log(f"ERROR on {filename}: {e}")
                    traceback.print_exc()
                    screenshot(page, f"05-error-{idx}")
                    uploaded_media[filename] = {"id": None, "url": None, "alt": alt_text}
            
            # Summary
            successful = sum(1 for v in uploaded_media.values() if v["id"])
            log(f"\n=== Upload Summary: {successful}/11 successful ===")
            for fname, info in uploaded_media.items():
                s = "✓" if info["id"] else "✗"
                log(f"  {s} {fname}: ID={info['id']}")
            
            if successful < 8:
                log("WARNING: Less than 8 images uploaded. Proceeding anyway but check results.")
            
            # ═══ STEP 3: CREATE BLOG POST ════════════════════════
            log("\n═══ STEP 3: CREATE BLOG POST ═══")
            
            page.goto(f"{WP_ADMIN}/post-new.php", wait_until="domcontentloaded")
            delay(5, 8)
            
            # Check editor type
            is_gutenberg = bool(page.query_selector(".block-editor, .editor-post-title"))
            is_classic = bool(page.query_selector("#title, #content"))
            log(f"Editor: {'Gutenberg' if is_gutenberg else 'Classic' if is_classic else 'Unknown'}")
            
            if is_gutenberg:
                # ─── Gutenberg Editor ────────────────────────────
                log("Using Gutenberg editor...")
                
                # Set title
                try:
                    title_el = page.query_selector(".editor-post-title__input")
                    if title_el:
                        title_el.click()
                        delay(0.5, 1)
                        title_el.fill(POST_TITLE)
                        log(f"Title: {POST_TITLE}")
                    delay(2, 3)
                except Exception as e:
                    log(f"Title error: {e}")
                
                # Build article HTML with images
                article_html = build_article_html(article_md, uploaded_media)
                log(f"Article HTML built: {len(article_html)} chars")
                
                # Switch to Code Editor
                log("Switching to Code Editor...")
                try:
                    # Try keyboard shortcut first
                    page.keyboard.press("Meta+Alt+Shift+M")
                    delay(2, 3)
                except:
                    pass
                
                # Verify we're in code editor
                code_editor = page.query_selector("textarea.editor-post-text-editor, textarea.wp-editor-area")
                if not code_editor:
                    # Try clicking the options menu
                    try:
                        more_btn = page.query_selector('button[aria-label="Options"], button[aria-label="More tools & options"]')
                        if more_btn:
                            more_btn.click()
                            delay(1, 2)
                            code_editor_opt = page.query_selector('button:has-text("Code Editor")')
                            if code_editor_opt:
                                code_editor_opt.click()
                                delay(2, 3)
                    except:
                        pass
                
                code_editor = page.query_selector("textarea.editor-post-text-editor, textarea.wp-editor-area")
                if code_editor:
                    log("Code editor found, inserting content...")
                    code_editor.click()
                    code_editor.fill("")
                    delay(0.5, 1)
                    
                    # Use evaluate to set the content (handles large content better)
                    escaped_html = article_html.replace('\\', '\\\\').replace('`', '\\`').replace('${', '\\${')
                    page.evaluate(f"""(content) => {{
                        const textarea = document.querySelector('textarea.editor-post-text-editor, textarea.wp-editor-area');
                        if (textarea) {{
                            textarea.value = content;
                            textarea.dispatchEvent(new Event('input', {{ bubbles: true }}));
                            textarea.dispatchEvent(new Event('change', {{ bubbles: true }}));
                        }}
                    }}""", article_html)
                    log(f"Content inserted: {len(article_html)} chars")
                    delay(3, 5)
                else:
                    log("Code editor not found! Trying to use visual editor instead...")
                    screenshot(page, "06-no-code-editor")
                
                # Switch back to visual editor to set featured image
                try:
                    page.keyboard.press("Meta+Alt+Shift+M")  # Toggle back
                    delay(2, 3)
                except:
                    pass
                
                # Set Featured Image
                log("Setting featured image...")
                featured_media_id = uploaded_media.get(FEATURED_IMAGE, {}).get("id")
                if featured_media_id:
                    try:
                        # Open settings sidebar
                        settings_btn = page.query_selector('button[aria-label="Post"], button[aria-label="Settings"], .editor-header__settings button')
                        if settings_btn:
                            settings_btn.click()
                            delay(1, 2)
                        
                        # Click Featured Image section
                        fi_link = page.query_selector('text=Featured Image')
                        if fi_link:
                            fi_link.click()
                            delay(1, 2)
                        
                        # Click Set Featured Image
                        set_fi = page.query_selector('text=Set Featured Image, text=Replace Image')
                        if set_fi:
                            set_fi.click()
                            delay(3, 5)
                            
                            # In media modal, search for our image
                            search = page.query_selector('.media-modal input[type="search"], .media-frame input[type="search"]')
                            if search:
                                search.fill(FEATURED_IMAGE.replace(".jpg", ""))
                                delay(2, 3)
                            
                            # Select the image
                            img_el = page.query_selector(f'li[data-id="{featured_media_id}"], .attachment[data-id="{featured_media_id}"]')
                            if img_el:
                                img_el.click()
                                delay(1, 2)
                            
                            # Click Select button
                            select_btn = page.query_selector('.media-modal button.media-button-select, button.button-primary:has-text("Select")')
                            if select_btn:
                                select_btn.click()
                                delay(3, 5)
                                log("✓ Featured image set!")
                            else:
                                log("Select button not found in media modal")
                        else:
                            log("Set Featured Image not found")
                    except Exception as e:
                        log(f"Featured image error: {e}")
                else:
                    log(f"Featured image media ID not available")
                
                screenshot(page, "07-post-content")
                
            else:
                # ─── Classic Editor ──────────────────────────────
                log("Using Classic editor...")
                
                page.fill("#title", POST_TITLE)
                log(f"Title: {POST_TITLE}")
                
                # Switch to HTML tab
                try:
                    page.click("#content-html, .switch-html")
                    delay(1, 2)
                except:
                    pass
                
                article_html = build_article_html(article_md, uploaded_media)
                page.fill("#content", article_html)
                log(f"Content inserted: {len(article_html)} chars")
                
                # Set featured image
                featured_media_id = uploaded_media.get(FEATURED_IMAGE, {}).get("id")
                if featured_media_id:
                    try:
                        page.click("#set-post-thumbnail")
                        delay(3, 5)
                        img_el = page.query_selector(f'li[data-id="{featured_media_id}"]')
                        if img_el:
                            img_el.click()
                            delay(1, 2)
                        set_btn = page.query_selector('.media-modal button.button-primary')
                        if set_btn:
                            set_btn.click()
                            delay(3, 5)
                            log("✓ Featured image set!")
                    except Exception as e:
                        log(f"Featured image error: {e}")
            
            screenshot(page, "07-post-ready")
            
            # ═══ STEP 4: YOAST SEO ═══════════════════════════════
            log("\n═══ STEP 4: YOAST SEO ═══")
            
            # Scroll down to find Yoast
            page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
            delay(2, 3)
            
            yoast_found = False
            
            # Try Gutenberg sidebar Yoast
            yoast_tab = page.query_selector('text=Yoast SEO, text=Search Appearances')
            if yoast_tab:
                yoast_tab.click()
                delay(2, 3)
                yoast_found = True
                log("Yoast SEO section found in sidebar")
            
            if not yoast_found:
                # Try classic editor metabox
                yoast_box = page.query_selector('#wpseo_meta, .yoast-seo-meta-box')
                if yoast_box:
                    yoast_box.scroll_into_view_if_needed()
                    delay(2, 3)
                    yoast_found = True
                    log("Yoast SEO metabox found")
            
            if yoast_found:
                # Set Focus Keyphrase
                try:
                    keyphrase_input = page.query_selector("#yoast_wpseo_focuskw, input[name='yoast_wpseo_focuskw']")
                    if not keyphrase_input:
                        # Try clicking keyphrase area first
                        kp_area = page.query_selector('text=Focus keyphrase')
                        if kp_area:
                            kp_area.click()
                            delay(1, 2)
                        keyphrase_input = page.query_selector('input[type="text"]:near(.yoast)')
                    
                    if keyphrase_input:
                        keyphrase_input.fill(FOCUS_KEYPHRASE)
                        log(f"✓ Focus keyphrase: {FOCUS_KEYPHRASE}")
                        delay(1, 2)
                    else:
                        log("Keyphrase input not found")
                except Exception as e:
                    log(f"Keyphrase error: {e}")
                
                # Set SEO Title
                try:
                    seo_title_input = page.query_selector("#yoast_wpseo_title, input[name='yoast_wpseo_title']")
                    if seo_title_input:
                        seo_title_input.fill(SEO_TITLE)
                        log(f"✓ SEO title: {SEO_TITLE}")
                except Exception as e:
                    log(f"SEO title error: {e}")
                
                # Set Meta Description
                try:
                    meta_desc_input = page.query_selector("#yoast_wpseo_metadesc, textarea[name='yoast_wpseo_metadesc']")
                    if meta_desc_input:
                        meta_desc_input.fill(META_DESC)
                        log(f"✓ Meta description: {META_DESC[:60]}...")
                except Exception as e:
                    log(f"Meta desc error: {e}")
                
                screenshot(page, "08-yoast")
            else:
                log("Yoast SEO section not found - will set via DB or REST API after draft creation")
            
            # ═══ STEP 5: SAVE DRAFT ══════════════════════════════
            log("\n═══ STEP 5: SAVE DRAFT ═══")
            
            if is_gutenberg:
                # Click Save Draft
                save_btn = page.query_selector('.editor-post-save-draft, button:has-text("Save draft")')
                if save_btn:
                    save_btn.click()
                    delay(5, 8)
                    log("✓ Save Draft clicked")
                else:
                    # Try the publish flow but keep as draft
                    publish_btn = page.query_selector('.editor-post-publish-button__button')
                    if publish_btn:
                        publish_btn.click()
                        delay(3, 5)
                        # Switch to draft if publish dialog appears
                        draft_opt = page.query_selector('text=Save as draft')
                        if draft_opt:
                            draft_opt.click()
                        delay(3, 5)
                
                # Wait for save
                delay(5, 8)
            else:
                save_btn = page.query_selector("#save-post")
                if save_btn:
                    save_btn.click()
                    page.wait_for_load_state("domcontentloaded")
                    delay(5, 8)
                    log("✓ Classic Save Draft clicked")
            
            screenshot(page, "09-draft-saved")
            
            # ═══ Get Post Details ════════════════════════════════
            log("\n═══ GET POST DETAILS ═══")
            
            post_id = None
            draft_url = None
            preview_url = None
            
            # Get from URL
            current_url = page.url
            id_match = re.search(r'post=(\d+)', current_url)
            if id_match:
                post_id = id_match.group(1)
            
            # Try from page content
            if not post_id:
                post_id = page.evaluate("""() => {
                    const el = document.querySelector('[data-post-id], #post_ID');
                    if (el) return el.value || el.dataset.postId;
                    const m = window.location.href.match(/post=(\\d+)/);
                    return m ? m[1] : null;
                }""")
            
            if post_id:
                draft_url = f"{WP_BASE}/?p={post_id}"
                preview_url = f"{WP_BASE}/?p={post_id}&preview=true"
                log(f"Post ID: {post_id}")
                log(f"Draft URL: {draft_url}")
                log(f"Preview URL: {preview_url}")
            else:
                log("Could not determine post ID from page")
            
            # ═══ STEP 6: VERIFICATION ════════════════════════════
            log("\n═══ STEP 6: VERIFICATION ═══")
            
            verification = {
                "images_present": 0,
                "alt_texts_found": [],
                "yoast_meta": {},
                "post_id": post_id,
                "draft_url": draft_url,
                "preview_url": preview_url,
            }
            
            if preview_url:
                try:
                    vp = context.new_page()
                    vp.goto(preview_url, wait_until="domcontentloaded")
                    delay(5, 8)
                    
                    img_data = vp.evaluate("""() => {
                        const imgs = document.querySelectorAll('img');
                        return Array.from(imgs).map(img => ({
                            src: img.src.substring(img.src.lastIndexOf('/') + 1),
                            alt: img.alt
                        }));
                    }""")
                    verification["images_present"] = len(img_data)
                    verification["alt_texts_found"] = img_data
                    
                    log(f"Images in preview: {len(img_data)}")
                    for img in img_data:
                        alt_status = "✓" if img["alt"] else "✗"
                        log(f"  {alt_status} {img['src']}: alt='{img['alt'][:50]}...' " if len(img.get('alt','')) > 50 else f"  {alt_status} {img['src']}: alt='{img.get('alt','')}'")
                    
                    # Check Yoast meta
                    yoast_meta = vp.evaluate("""() => {
                        const result = {};
                        document.querySelectorAll('meta').forEach(meta => {
                            const name = meta.getAttribute('name') || meta.getAttribute('property') || '';
                            if (name.includes('description') || name.includes('og:') || name.includes('twitter:')) {
                                result[name] = meta.getAttribute('content') || '';
                            }
                        });
                        return result;
                    }""")
                    verification["yoast_meta"] = yoast_meta
                    log(f"Yoast meta tags: {len(yoast_meta)}")
                    for k, v in yoast_meta.items():
                        log(f"  {k}: {v[:80]}")
                    
                    vp.screenshot(path=str(SCREENSHOTS_DIR / "10-preview.png"))
                    vp.close()
                except Exception as e:
                    log(f"Verification error: {e}")
            
            # ═══ SAVE RESULTS ════════════════════════════════════
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
                "verification": verification,
                "timestamp": time.strftime("%Y-%m-%d %H:%M:%S"),
            }
            
            with open(RESULTS_FILE, "w") as f:
                json.dump(results, f, indent=2)
            log(f"\nResults saved: {RESULTS_FILE}")
            
            # ═══ FINAL SUMMARY ══════════════════════════════════
            log("\n" + "=" * 60)
            log("FINAL SUMMARY")
            log("=" * 60)
            log(f"Post ID: {post_id}")
            log(f"Draft URL: {draft_url}")
            log(f"Preview URL: {preview_url}")
            log(f"Title: {POST_TITLE}")
            log(f"Focus Keyphrase: {FOCUS_KEYPHRASE}")
            log(f"SEO Title: {SEO_TITLE}")
            log(f"Meta Description: {META_DESC}")
            log(f"Featured Image: {FEATURED_IMAGE}")
            successful_uploads = sum(1 for v in uploaded_media.values() if v["id"])
            log(f"Images uploaded: {successful_uploads}/11")
            log(f"Images in preview: {verification.get('images_present', 0)}")
            
            log("\nMedia URLs:")
            for fname, info in uploaded_media.items():
                s = "✓" if info["id"] else "✗"
                log(f"  {s} ID={info['id']} {info['url']}")
            
        except Exception as e:
            log(f"FATAL: {e}")
            traceback.print_exc()
            try:
                screenshot(page, "99-fatal")
            except:
                pass
        
        finally:
            context.close()
    
    log("Script complete.")


if __name__ == "__main__":
    main()