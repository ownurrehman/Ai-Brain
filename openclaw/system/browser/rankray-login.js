const { chromium } = require('playwright');
const { config, ensureDirs } = require('./rankray-common');

(async() => {
  ensureDirs();
  const context = await chromium.launchPersistentContext(config.userDataDir, {
    headless: false,
    viewport: { width: 1440, height: 1000 },
    slowMo: 80
  });
  const page = context.pages()[0] || await context.newPage();
  await page.goto(config.loginUrl, { waitUntil: 'domcontentloaded' });
  await page.screenshot({ path: `${config.artifactsDir}/rankray-login-open.png`, fullPage: true });
  console.log('Persistent browser opened at login page. Complete login in this browser window once, then reuse the same profile for edits.');
})();
