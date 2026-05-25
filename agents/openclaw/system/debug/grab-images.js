const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  
  // Orthopedic physio
  const orthoPage = await browser.newPage();
  await orthoPage.goto('https://pixabay.com/photos/search/orthopedic%20physiotherapy/', { waitUntil: 'networkidle', timeout: 30000 });
  await orthoPage.waitForTimeout(3000);
  const orthoLinks = await orthoPage.$$eval('img[src*="pixabay"]', imgs => 
    imgs.slice(0, 5).map(img => ({
      src: img.getAttribute('src'),
      alt: img.getAttribute('alt')
    }))
  );
  console.log('=== ORTHOPEDIC ===');
  console.log(JSON.stringify(orthoLinks, null, 2));
  
  // Pediatric physio
  await orthoPage.goto('https://pixabay.com/photos/search/pediatric%20physiotherapy%20child/', { waitUntil: 'networkidle', timeout: 30000 });
  await orthoPage.waitForTimeout(3000);
  const pediaLinks = await orthoPage.$$eval('img[src*="pixabay"]', imgs => 
    imgs.slice(0, 5).map(img => ({
      src: img.getAttribute('src'),
      alt: img.getAttribute('alt')
    }))
  );
  console.log('=== PEDIATRIC ===');
  console.log(JSON.stringify(pediaLinks, null, 2));
  
  await browser.close();
})();
