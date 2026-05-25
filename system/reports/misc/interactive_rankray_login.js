const { chromium } = require('playwright');
(async() => {
  const browser = await chromium.launch({ headless: false, slowMo: 100 });
  const context = await browser.newContext({ viewport: { width: 1440, height: 1000 } });
  const page = await context.newPage();
  await page.goto('https://www.rankray.com/wp-login.php', { waitUntil: 'domcontentloaded' });
  await page.fill('#user_login', 'openclaw');
  await page.fill('#user_pass', 'OC#admin@2026');
  console.log('Login page loaded and credentials filled.');
  console.log('If Turnstile requires interaction, complete it in the opened browser window.');
  await page.click('#wp-submit');
  await page.waitForTimeout(15000);
  console.log('Current URL:', page.url());
  await page.screenshot({ path: '/Users/sheikhown/.openclaw/workspace/reports/interactive-login-state.png', fullPage: true });
  await page.pause();
})();
