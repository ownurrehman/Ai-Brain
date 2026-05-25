const { openPersistent } = require('./common');
(async() => {
  const siteName = process.argv[2];
  const { site, page } = await openPersistent(siteName, { headless: false, slowMo: 60 });
  await page.goto(site.loginUrl, { waitUntil: 'domcontentloaded' });
  console.log(`Opened persistent profile for ${site.label}`);
})();
