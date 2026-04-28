const { chromium } = require('playwright');
(async() => {
  const endpoint = process.argv[2] || 'http://127.0.0.1:9222';
  const browser = await chromium.connectOverCDP(endpoint);
  const contexts = browser.contexts();
  const pages = contexts.flatMap(c => c.pages());
  console.log(JSON.stringify({ endpoint, contexts: contexts.length, pages: pages.map(p => p.url()) }, null, 2));
  await browser.close();
})();
