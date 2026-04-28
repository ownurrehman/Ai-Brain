const { chromium } = require('playwright');
const { config, ensureDirs } = require('./rankray-common');

(async() => {
  ensureDirs();
  const context = await chromium.launchPersistentContext(config.userDataDir, {
    headless: true,
    viewport: { width: 1440, height: 1000 }
  });
  const page = context.pages()[0] || await context.newPage();
  await page.goto(config.editPostUrl, { waitUntil: 'domcontentloaded' });
  console.log(JSON.stringify({ url: page.url(), authenticated: !page.url().includes('wp-login.php') }, null, 2));
  await context.close();
})();
