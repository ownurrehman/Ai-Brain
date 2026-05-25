const { openPersistent } = require('./common');
(async() => {
  const siteName = process.argv[2];
  const { site, page } = await openPersistent(siteName, { headless: false, slowMo: 40 });
  await page.goto(site.adminUrl, { waitUntil: 'domcontentloaded' });
  console.log(`Opened admin for ${site.label}: ${page.url()}`);
})();
