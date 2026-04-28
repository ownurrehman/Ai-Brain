const { chromium } = require('playwright');

const config = {
  siteName: "Tonic Physio",
  loginUrl: "https://tonicphysio.com/wp-admin/",
  userDataDir: "/Users/sheikhown/.openclaw/workspace/.browser-profiles/tonic-wp",
  artifactsDir: "/Users/sheikhown/.openclaw/workspace/reports/browser-artifacts"
};

const { mkdirSync } = require('fs');
const { join } = require('path');

function ensureDirs() {
  try { mkdirSync(config.userDataDir, { recursive: true }); } catch(e) {}
  try { mkdirSync(config.artifactsDir, { recursive: true }); } catch(e) {}
}

(async() => {
  ensureDirs();
  const context = await chromium.launchPersistentContext(config.userDataDir, {
    headless: false,
    viewport: { width: 1440, height: 1000 },
    slowMo: 80
  });
  const page = context.pages()[0] || await context.newPage();
  await page.goto(config.loginUrl, { waitUntil: 'domcontentloaded' });
  await page.screenshot({ path: join(config.artifactsDir, 'tonic-login-open.png'), fullPage: true });
  console.log('Tonic Physio: Persistent browser opened at WordPress admin login. Complete login in this browser window, then reuse the same profile for edits.');
  console.log('Credentials: Dan / App Password: 4vFk 18fN UlLB twaw B2hU 0kRE');
})();
