const { chromium } = require('playwright');
const fs = require('fs');

const USERNAME = 'openclaw';
const PASSWORD = 'OC#admin@2026';
const LOGIN_URL = 'https://www.rankray.com/wp-login.php';
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
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage({ viewport: { width: 1440, height: 1200 } });
  await page.goto(LOGIN_URL, { waitUntil: 'domcontentloaded' });
  await page.fill('#user_login', USERNAME);
  await page.fill('#user_pass', PASSWORD);
  await page.click('#wp-submit');
  await page.waitForLoadState('networkidle');
  await page.goto(EDIT_URL, { waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(5000);

  for (const sel of ['button:has-text("Continue")', 'button:has-text("Close")']) {
    try { await page.locator(sel).click({ timeout: 2000 }); } catch {}
  }

  try { await page.locator('button[aria-label="Settings"]').click({ timeout: 3000 }); } catch {}

  try {
    await page.evaluate((html) => {
      const wp = window.wp;
      if (!wp || !wp.data || !wp.blocks) return false;
      const blocks = wp.blocks.parse(html);
      wp.data.dispatch('core/block-editor').resetBlocks(blocks);
      return true;
    }, HTML);
  } catch (e) {}

  try {
    await page.fill('textarea.editor-post-title__input', TITLE, { timeout: 5000 });
  } catch {}

  try {
    const switchBtn = page.locator('button:has-text("Switch to draft")').first();
    if (await switchBtn.isVisible({ timeout: 3000 })) {
      await switchBtn.click();
      await page.waitForTimeout(1500);
      const confirm = page.locator('button:has-text("Switch to draft")').nth(1);
      try { await confirm.click({ timeout: 3000 }); } catch {}
    }
  } catch {}

  const allTextareas = await page.locator('textarea').evaluateAll(els => els.map(e => ({id:e.id, aria:e.getAttribute('aria-label') || '', cls:e.className || ''})));
  console.log('TEXTAREAS', JSON.stringify(allTextareas));

  for (const sel of ['textarea[aria-label="Meta description input"]', 'textarea#yoast-google-preview-description-metabox', 'textarea']) {
    try {
      const loc = page.locator(sel).filter({ hasNotText: 'Password' }).last();
      await loc.fill(META_DESC, { timeout: 1000 });
      console.log('filled meta using', sel);
      break;
    } catch {}
  }

  let saved = false;
  for (const txt of ['Save draft', 'Update']) {
    try {
      const btn = page.locator(`button:has-text("${txt}")`).first();
      await btn.click({ timeout: 5000 });
      saved = true;
      console.log('clicked', txt);
      break;
    } catch {}
  }
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(5000);
  await page.screenshot({ path: '/Users/sheikhown/.openclaw/workspace/reports/rankray-draft-result.png', fullPage: true });
  console.log('URL', page.url());
  console.log(saved ? 'DONE' : 'NOT_SAVED');
  await browser.close();
})();
