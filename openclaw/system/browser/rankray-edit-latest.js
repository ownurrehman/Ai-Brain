const { chromium } = require('playwright');
const { config, ensureDirs, loadDraft } = require('./rankray-common');

(async() => {
  ensureDirs();
  const draft = loadDraft();
  const context = await chromium.launchPersistentContext(config.userDataDir, {
    headless: false,
    viewport: { width: 1440, height: 1000 },
    slowMo: 50
  });
  const page = context.pages()[0] || await context.newPage();
  await page.goto(config.editPostUrl, { waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(5000);

  if (page.url().includes('wp-login.php')) {
    await page.screenshot({ path: `${config.artifactsDir}/rankray-edit-auth-required.png`, fullPage: true });
    console.log(JSON.stringify({ ok: false, reason: 'auth_required', url: page.url() }, null, 2));
    await context.close();
    process.exit(1);
  }

  for (const sel of ['button:has-text("Continue")', 'button:has-text("Close")', 'button[aria-label="Close dialog"]']) {
    try { await page.locator(sel).click({ timeout: 1500 }); } catch {}
  }

  const titleInput = page.locator('textarea.editor-post-title__input');
  try {
    await titleInput.fill(draft.title, { timeout: 5000 });
  } catch {}

  const resetOk = await page.evaluate((html) => {
    const wp = window.wp;
    if (!wp || !wp.data || !wp.blocks) return false;
    const blocks = wp.blocks.parse(html);
    wp.data.dispatch('core/block-editor').resetBlocks(blocks);
    return true;
  }, draft.html).catch(() => false);

  let draftSwitched = false;
  try {
    const btn = page.locator('button:has-text("Switch to draft")').first();
    if (await btn.isVisible({ timeout: 2500 })) {
      await btn.click();
      await page.waitForTimeout(1200);
      const buttons = page.locator('button:has-text("Switch to draft")');
      const count = await buttons.count();
      if (count > 1) await buttons.nth(1).click({ timeout: 2500 });
      draftSwitched = true;
    }
  } catch {}

  let metaFilled = false;
  for (const sel of [
    'textarea[aria-label="Meta description input"]',
    'textarea#yoast-google-preview-description-metabox',
    'textarea[class*="snippet-editor__meta-description"]'
  ]) {
    try {
      await page.fill(sel, draft.meta, { timeout: 1500 });
      metaFilled = true;
      break;
    } catch {}
  }

  let savedWith = null;
  for (const label of ['Save draft', 'Update']) {
    try {
      await page.locator(`button:has-text("${label}")`).first().click({ timeout: 5000 });
      savedWith = label;
      break;
    } catch {}
  }

  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(5000);
  await page.screenshot({ path: `${config.artifactsDir}/rankray-edit-result.png`, fullPage: true });

  const bodyText = await page.locator('body').innerText().catch(() => '');
  const looksDraft = /Draft/i.test(bodyText);
  console.log(JSON.stringify({
    ok: !!savedWith,
    resetOk,
    draftSwitched,
    metaFilled,
    savedWith,
    looksDraft,
    url: page.url()
  }, null, 2));

  await context.close();
})();
