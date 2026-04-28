const { chromium } = require('playwright');
(async() => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  await page.goto('https://www.rankray.com/wp-login.php', { waitUntil: 'domcontentloaded' });
  console.log('start', page.url());
  await page.fill('#user_login', 'openclaw');
  await page.fill('#user_pass', 'OC#admin@2026');
  await page.screenshot({ path: '/Users/sheikhown/.openclaw/workspace/reports/login-before-submit.png' });
  await page.click('#wp-submit');
  await page.waitForTimeout(5000);
  console.log('after', page.url());
  console.log('content snippet', (await page.content()).slice(0,1000));
  await page.screenshot({ path: '/Users/sheikhown/.openclaw/workspace/reports/login-after-submit.png', fullPage: true });
  await browser.close();
})();
