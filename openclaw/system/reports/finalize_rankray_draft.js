const { chromium } = require('playwright');
const fs = require('fs');

const USER_DATA_DIR = '/Users/sheikhown/.openclaw/workspace/.pw-rankray';
const EDIT_URL = 'https://www.rankray.com/wp-admin/post.php?post=12055&action=edit';
const TITLE = 'Why SEO Takes So Long: 8 Reasons Your Rankings Stall';
const META_DESC = 'Why SEO takes so long often comes down to indexing, intent, authority, and technical issues. Learn 8 fixes to improve rankings faster with Rank Ray.';
const content = fs.readFileSync('/Users/sheikhown/.openclaw/workspace/reports/rankray-latest-blog-draft-copy-2026-03-27.md', 'utf8');
const body = content.split('## Updated article body\n')[1].trim();
const paragraphs = body.split(/\n\n+/).map(s => s.trim()).filter(Boolean);
const htmlParts = [];
for (const p of paragraphs) {
  if (p.startsWith('## ')) htmlParts.push(`<h2>${p.slice(3).trim()}</h2>`);
  else if (p.startsWith('### ')) htmlParts.push(`<h3>${p.slice(4).trim()}</h3>`);
  else if (/^- /m.test(p)) {
    const items = p.split('\n').filter(l => l.startsWith('- ')).map(l => `<li>${l.slice(2).trim()}</li>`).join('');
    htmlParts.push(`<ul>${items}</ul>`);
  } else if (/^\d+\. /m.test(p)) {
    const items = p.split('\n').filter(l => /^\d+\. /.test(l)).map(l => `<li>${l.replace(/^\d+\.\s*/, '').trim()}</li>`).join('');
    htmlParts.push(`<ol>${items}</ol>`);
  } else htmlParts.push(`<p>${p.replace(/\n/g, '<br>')}</p>`);
}
const HTML = htmlParts.join('\n');

(async() => {
  const context = await chromium.launchPersistentContext(USER_DATA_DIR, { headless: false, viewport: { width: 1440, height: 1000 } });
  const page = context.pages()[0] || await context.newPage();
  await page.goto(EDIT_URL, { waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(6000);

  if (page.url().includes('wp-login.php')) {
    console.log('Still not authenticated:', page.url());
    await page.screenshot({ path: '/Users/sheikhown/.openclaw/workspace/reports/rankray-auth-still-blocked.png', fullPage: true });
    await context.close();
    process.exit(1);
  }

  for (const sel of ['button:has-text("Continue")', 'button:has-text("Close")', 'button[aria-label="Close dialog"]']) {
    try { await page.locator(sel).click({ timeout: 2000 }); } catch {}
  }

  try {
    await page.evaluate((title) => {
      const el = document.querySelector('textarea.editor-post-title__input');
      if (el) {
        el.value = title;
        el.dispatchEvent(new Event('input', { bubbles: true }));
      }
    }, TITLE);
  } catch {}

  const resetOk = await page.evaluate((html) => {
    const wp = window.wp;
    if (!wp || !wp.data || !wp.blocks) return false;
    const blocks = wp.blocks.parse(html);
    wp.data.dispatch('core/block-editor').resetBlocks(blocks);
    return true;
  }, HTML).catch(() => false);
  console.log('resetBlocks', resetOk);
  await page.waitForTimeout(2000);

  try {
    await page.locator('button[aria-label="Settings"]').click({ timeout: 3000 });
  } catch {}

  try {
    const switchBtn = page.locator('button:has-text("Switch to draft")').first();
    if (await switchBtn.isVisible({ timeout: 3000 })) {
      await switchBtn.click();
      await page.waitForTimeout(1000);
      const buttons = page.locator('button:has-text("Switch to draft")');
      const count = await buttons.count();
      if (count > 1) await buttons.nth(1).click({ timeout: 3000 });
      await page.waitForTimeout(2000);
    }
  } catch {}

  let filledMeta = false;
  for (const sel of [
    'textarea[aria-label="Meta description input"]',
    'textarea#yoast-google-preview-description-metabox',
    'textarea[class*="snippet-editor__meta-description"]'
  ]) {
    try {
      await page.fill(sel, META_DESC, { timeout: 3000 });
      filledMeta = true;
      console.log('filled meta', sel);
      break;
    } catch {}
  }
  console.log('metaFilled', filledMeta);

  let clicked = false;
  for (const txt of ['Save draft', 'Update']) {
    try {
      const btn = page.locator(`button:has-text("${txt}")`).first();
      await btn.click({ timeout: 5000 });
      console.log('clicked', txt);
      clicked = true;
      break;
    } catch {}
  }
  if (!clicked) {
    await page.screenshot({ path: '/Users/sheikhown/.openclaw/workspace/reports/rankray-save-button-missing.png', fullPage: true });
    throw new Error('No save button found');
  }

  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(6000);
  const statusText = await page.locator('text=/Draft|Published|Pending/').allTextContents().catch(() => []);
  await page.screenshot({ path: '/Users/sheikhown/.openclaw/workspace/reports/rankray-draft-final.png', fullPage: true });
  console.log('URL', page.url());
  console.log('STATUS', JSON.stringify(statusText));
  await context.close();
})();
