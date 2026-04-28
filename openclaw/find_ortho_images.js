const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  
  // Try loading the page with domcontentloaded instead of networkidle
  await page.goto('https://www.pexels.com/search/orthopedic%20physiotherapy/', { 
    waitUntil: 'domcontentloaded', 
    timeout: 30000 
  });
  await page.waitForTimeout(5000);
  
  // Try to get images from the page
  const urls = await page.evaluate(() => {
    const imgs = document.querySelectorAll('img[src*="pexels"]');
    return Array.from(imgs).slice(0, 8).map(img => ({
      src: img.src,
      alt: img.alt,
      w: img.naturalWidth,
      h: img.naturalHeight
    }));
  });
  
  console.log('=== ORTHOPEDIC PHYSIOTHERAPY ===');
  console.log(JSON.stringify(urls, null, 2));
  
  // Also try pediatric
  await page.goto('https://www.pexels.com/search/pediatric%20physiotherapy/', { 
    waitUntil: 'domcontentloaded', 
    timeout: 30000 
  });
  await page.waitForTimeout(5000);
  
  const pediaUrls = await page.evaluate(() => {
    const imgs = document.querySelectorAll('img[src*="pexels"]');
    return Array.from(imgs).slice(0, 8).map(img => ({
      src: img.src,
      alt: img.alt,
      w: img.naturalWidth,
      h: img.naturalHeight
    }));
  });
  
  console.log('=== PEDIATRIC PHYSIOTHERAPY ===');
  console.log(JSON.stringify(pediaUrls, null, 2));
  
  await browser.close();
})();
