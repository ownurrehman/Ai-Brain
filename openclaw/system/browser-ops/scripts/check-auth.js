const { openPersistent } = require('./common');
(async() => {
  const siteName = process.argv[2];
  const { site, context, page } = await openPersistent(siteName, { headless: true });
  await page.goto(site.adminUrl, { waitUntil: 'domcontentloaded' });
  const authenticated = !page.url().includes('wp-login.php');
  console.log(JSON.stringify({ site: site.label, url: page.url(), authenticated }, null, 2));
  await context.close();
})();
