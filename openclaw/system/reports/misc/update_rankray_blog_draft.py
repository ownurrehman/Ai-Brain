from playwright.sync_api import sync_playwright
from pathlib import Path
import re

USERNAME = 'openclaw'
PASSWORD = 'OC#admin@2026'
LOGIN_URL = 'https://www.rankray.com/wp-login.php'
EDIT_URL = 'https://www.rankray.com/wp-admin/post.php?post=12055&action=edit'
TITLE = 'Why SEO Takes So Long: 8 Reasons Your Rankings Stall'
META_DESC = 'Why SEO takes so long often comes down to indexing, intent, authority, and technical issues. Learn 8 fixes to improve rankings faster with Rank Ray.'
SLUG = 'factors-slowing-down-your-seo-efforts'
CONTENT = Path('/Users/sheikhown/.openclaw/workspace/reports/rankray-latest-blog-draft-copy-2026-03-27.md').read_text()
body = CONTENT.split('## Updated article body\n',1)[1].strip()
paragraphs = [p.strip() for p in body.split('\n\n') if p.strip()]
html_parts=[]
for p in paragraphs:
    if p.startswith('## '):
        html_parts.append(f'<h2>{p[3:].strip()}</h2>')
    elif p.startswith('### '):
        html_parts.append(f'<h3>{p[4:].strip()}</h3>')
    elif re.match(r'^- ', p):
        items=''.join(f'<li>{line[2:].strip()}</li>' for line in p.split('\n') if line.startswith('- '))
        html_parts.append(f'<ul>{items}</ul>')
    elif re.match(r'^\d+\. ', p):
        items=''.join(f'<li>{re.sub(r"^\d+\.\s*", "", line).strip()}</li>' for line in p.split('\n') if re.match(r'^\d+\. ', line))
        html_parts.append(f'<ol>{items}</ol>')
    else:
        html_parts.append('<p>' + p.replace('\n', '<br>') + '</p>')
HTML = '\n'.join(html_parts)

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={"width": 1440, "height": 1200})
    page.goto(LOGIN_URL, wait_until='domcontentloaded')
    page.fill('#user_login', USERNAME)
    page.fill('#user_pass', PASSWORD)
    page.click('#wp-submit')
    page.wait_for_load_state('networkidle')
    page.goto(EDIT_URL, wait_until='domcontentloaded')
    page.wait_for_timeout(4000)

    try:
        page.locator('button:has-text("Continue")').click(timeout=2000)
    except:
        pass
    try:
        page.locator('button:has-text("Close")').click(timeout=2000)
    except:
        pass

    try:
        page.locator('button[aria-label="Options"]').click(timeout=3000)
        page.locator('button:has-text("Code editor")').click(timeout=3000)
        page.locator('button:has-text("Exit code editor")').wait_for_timeout(500)
    except:
        pass

    try:
        page.fill('textarea.editor-post-title__input', TITLE, timeout=5000)
    except:
        pass

    content_selectors = [
        'textarea[aria-label="Empty block; start writing or type forward slash to choose a block"]',
        'textarea.block-editor-plain-text',
        '[role="textbox"][aria-multiline="true"]'
    ]
    inserted = False
    for sel in content_selectors:
        try:
            loc = page.locator(sel).first
            loc.click(timeout=3000)
            page.keyboard.press('Meta+a')
            page.keyboard.press('Backspace')
            page.keyboard.insert_text(body)
            inserted = True
            break
        except:
            continue

    if not inserted:
        page.evaluate("""
        (html) => {
          const wp = window.wp;
          if (!wp || !wp.data || !wp.blocks) return false;
          const blocks = wp.blocks.parse(html);
          wp.data.dispatch('core/block-editor').resetBlocks(blocks);
          return true;
        }
        """, HTML)

    page.wait_for_timeout(2000)

    # open status panel and set draft if possible
    try:
        page.locator('button[aria-label="Settings"]').click(timeout=3000)
    except:
        pass
    try:
        page.locator('button:has-text("Switch to draft")').click(timeout=5000)
        try:
            page.locator('button:has-text("Switch to draft")').nth(1).click(timeout=3000)
        except:
            pass
    except:
        pass

    # try Yoast meta description
    try:
        page.locator('button:has-text("Yoast SEO")').click(timeout=3000)
    except:
        pass
    for sel in ['textarea[aria-label="Meta description input"]', 'textarea#yoast-google-preview-description-metabox', 'textarea[class*="snippet-editor__meta-description"]']:
        try:
            page.fill(sel, META_DESC, timeout=3000)
            break
        except:
            continue

    # save/update
    clicked = False
    for txt in ['Save draft', 'Update']:
        try:
            page.locator(f'button:has-text("{txt}")').click(timeout=5000)
            clicked = True
            break
        except:
            continue
    if not clicked:
        page.screenshot(path='/Users/sheikhown/.openclaw/workspace/reports/rankray-draft-failed.png', full_page=True)
        raise RuntimeError('Could not find Save draft or Update button')

    page.wait_for_load_state('networkidle')
    page.wait_for_timeout(5000)
    page.screenshot(path='/Users/sheikhown/.openclaw/workspace/reports/rankray-draft-result.png', full_page=True)
    print(page.url)
    print('DONE')
    browser.close()
