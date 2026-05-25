const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({
    userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
  });
  const page = await context.newPage();
  
  // Try free stock sites
  const searches = [
    { url: 'https://www.pexels.com/search/physiotherapy%20exercise/', label: 'PHYSIO EXERCISE' },
    { url: 'https://www.pexels.com/search/orthopedic/', label: 'ORTHOPEDIC' },
  ];
  
  for (const s of searches) {
    console.log(`\n=== ${s.label} ===`);
    try {
      await page.goto(s.url, { waitUntil: 'domcontentloaded', timeout: 20000 });
      await page.waitForTimeout(3000);
      
      // Scroll a bit
      await page.evaluate(() => window.scrollTo(0, 500));
      await page.waitForTimeout(2000);
      
      const results = await page.evaluate(() => {
        return Array.from(document.querySelectorAll('article img')).slice(0, 3).map(img => ({
          src: img.src || img.dataset.src || '',
          alt: img.alt || ''
        }));
      });
      console.log(JSON.stringify(results, null, 2));
    } catch(e) {
      console.log(`Error: ${e.message}`);
    }
  }
  
  await browser.close();
})();
