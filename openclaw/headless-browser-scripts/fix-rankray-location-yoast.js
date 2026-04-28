#!/usr/bin/env node
const puppeteer = require('puppeteer');
const fs = require('fs');

const CONFIG = {
  wpLogin: 'https://rankray.com/wp-admin',
  username: 'openclaw',
  password: 'OpenClaw#Admin@2026',
  chromePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'
};

const PAGES = [
  { id: 19311, slug: 'seo-agency-seattle', keyphrase: 'seo agency seattle', meta: 'Top-rated SEO agency in Seattle helping businesses grow traffic, rankings, and leads with technical SEO, content, and local search by Rank Ray.' },
  { id: 19255, slug: 'seo-agency-chicago', keyphrase: 'seo agency chicago', meta: 'Top-rated SEO agency in Chicago helping businesses grow traffic, rankings, and leads with technical SEO, content, and local search by Rank Ray.' },
  { id: 19254, slug: 'seo-agency-los-angeles', keyphrase: 'seo agency los angeles', meta: 'Top-rated SEO agency in Los Angeles helping businesses grow traffic, rankings, and leads with technical SEO, content, and local search by Rank Ray.' },
  { id: 19253, slug: 'seo-agency-new-york', keyphrase: 'seo agency new york', meta: 'Top-rated SEO agency in New York helping businesses grow traffic, rankings, and leads with technical SEO, content, and local search by Rank Ray.' },
  { id: 19252, slug: 'seo-agency-vancouver', keyphrase: 'seo agency vancouver', meta: 'Top-rated SEO agency in Vancouver helping businesses grow traffic, rankings, and leads with technical SEO, content, and local search by Rank Ray.' }
];

function delay(ms){ return new Promise(r => setTimeout(r, ms)); }

async function login(page){
  await page.goto(CONFIG.wpLogin, { waitUntil: 'networkidle2', timeout: 30000 });
  if (page.url().includes('wp-login.php')) {
    await page.waitForSelector('#user_login', { timeout: 10000 });
    await page.type('#user_login', CONFIG.username);
    await page.type('#user_pass', CONFIG.password);
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 30000 }),
      page.click('#wp-submit')
    ]);
  }
}

async function trySetYoast(page, keyphrase, meta){
  const result = { keyphrase:false, meta:false, save:false };

  // open Yoast/metabox if collapsed or in sidebar
  const yoastButtons = [
    '#wpseo_meta',
    '#wpseo-meta-section',
    'button[aria-label*="Yoast"]',
    'button[aria-label*="SEO"]',
    '.components-button[aria-label*="Yoast"]',
    '.components-button[aria-label*="SEO"]'
  ];
  for (const sel of yoastButtons) {
    const btn = await page.$(sel);
    if (btn) { try { await btn.click(); await delay(1500); } catch {} }
  }

  // try direct field selectors old/new Yoast
  const keySelectors = [
    'input[aria-label="Focus keyphrase"]',
    'input[placeholder="Focus keyphrase"]',
    'input[name*="focuskw"]',
    'input#focus-keyword-input',
    'input#keyword-input',
    '#yoast-google-preview-description-metabox + * input'
  ];
  for (const sel of keySelectors) {
    const el = await page.$(sel);
    if (el) {
      try { await el.click({ clickCount: 3 }); await el.type(keyphrase); result.keyphrase = true; break; } catch {}
    }
  }

  const metaSelectors = [
    'textarea[aria-label="Meta description"]',
    'textarea[placeholder="Meta description"]',
    'textarea[name*="metadesc"]',
    'textarea#wpseo_meta-description',
    'textarea#yoast-google-preview-description-textarea',
    '#yoast-google-preview-description-metabox textarea'
  ];
  for (const sel of metaSelectors) {
    const el = await page.$(sel);
    if (el) {
      try { await el.click({ clickCount: 3 }); await el.type(meta); result.meta = true; break; } catch {}
    }
  }

  const saveSelectors = [
    'button.editor-post-save-draft',
    'button.editor-post-publish-button',
    'button.components-button.editor-post-save-draft',
    '#publish',
    'input#publish'
  ];
  for (const sel of saveSelectors) {
    const el = await page.$(sel);
    if (el) {
      try { await el.click(); await delay(5000); result.save = true; break; } catch {}
    }
  }

  return result;
}

(async () => {
  const browser = await puppeteer.launch({
    headless: false,
    executablePath: CONFIG.chromePath,
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
    defaultViewport: { width: 1600, height: 1000 }
  });

  const page = await browser.newPage();
  const results = [];
  try {
    await login(page);
    for (const p of PAGES) {
      const row = { id:p.id, slug:p.slug };
      try {
        await page.goto(`https://rankray.com/wp-admin/post.php?post=${p.id}&action=edit`, { waitUntil: 'networkidle2', timeout: 30000 });
        await delay(4000);
        row.url = page.url();
        row.loggedIn = !row.url.includes('wp-login.php');
        if (!row.loggedIn) throw new Error('not logged in');
        const write = await trySetYoast(page, p.keyphrase, p.meta);
        Object.assign(row, write);
      } catch (e) {
        row.error = String(e.message || e);
      }
      results.push(row);
    }
  } finally {
    fs.writeFileSync('/Users/sheikhown/.openclaw/workspace/headless-browser-scripts/fix-rankray-location-yoast-results.json', JSON.stringify(results, null, 2));
    console.log(JSON.stringify(results, null, 2));
    await browser.close();
  }
})();