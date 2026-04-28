const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ 
    headless: true,
    args: ['--no-sandbox', '--disable-setuid-sandbox']
  });
  const context = await browser.newContext({
    viewport: { width: 1920, height: 1080 },
    userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
  });
  const page = await context.newPage();
  
  console.log('=== ALMIRAH.COM ANALYSIS ===\n');
  
  try {
    await page.goto('https://almirah.com', { 
      waitUntil: 'domcontentloaded', 
      timeout: 15000 
    });
    
    // Wait a bit for any JS to render
    await new Promise(resolve => setTimeout(resolve, 2000));
    
    // H1 tag
    const h1 = await page.$('h1');
    const h1Text = h1 ? await page.$eval('h1', el => el.textContent.trim()) : 'NOT FOUND';
    console.log('1. H1 Tag:', h1Text);
    
    // Meta title and description
    const metaTitle = await page.$eval('title', el => el.textContent.trim()).catch(() => 'NOT FOUND');
    const metaDesc = await page.$eval('meta[name="description"]', el => el.getAttribute('content')).catch(() => 'NOT FOUND');
    console.log('2. Meta Title:', metaTitle);
    console.log('   Meta Description:', metaDesc || 'MISSING');
    
    // Image count and alt text
    const images = await page.$$eval('img', imgs => 
      imgs.map(img => ({
        src: img.src,
        alt: img.alt || null
      }))
    );
    const totalImages = images.length;
    const imagesWithAlt = images.filter(img => img.alt && img.alt.trim() !== '').length;
    const altCoverage = totalImages > 0 ? Math.round((imagesWithAlt / totalImages) * 100) : 0;
    console.log(`3. Images: ${totalImages} total, ${imagesWithAlt} with alt text (${altCoverage}% coverage)`);
    
    // Blog/content section
    const blogLink = await page.$eval('a[href*="blog"], a[href*="articles"], a[href*="news"]', el => el.href).catch(() => null);
    const hasBlog = blogLink !== null;
    console.log('4. Blog Section:', hasBlog ? `Found - ${blogLink}` : 'NOT FOUND');
    
    // About/Team page
    const aboutLink = await page.$eval('a[href*="about"], a[href*="team"], a[href*="company"], a[href*="about-us"]', el => el.href).catch(() => null);
    console.log('5. About/Team Page:', aboutLink || 'NOT FOUND');
    
    // SEO gaps
    console.log('\n6. SEO Gaps:');
    
    // Check for schema
    const schema = await page.$('script[type="application/ld+json"]');
    console.log('   - Schema.org markup:', schema ? 'PRESENT' : 'MISSING');
    
    // Check for canonical
    const canonical = await page.$eval('link[rel="canonical"]', el => el.href).catch(() => null);
    console.log('   - Canonical URL:', canonical || 'MISSING');
    
    // Check for Open Graph
    const ogTitle = await page.$eval('meta[property="og:title"]', el => el.getAttribute('content')).catch(() => null);
    console.log('   - Open Graph tags:', ogTitle ? 'PRESENT' : 'MISSING');
    
    // Check H1 count
    const h1Count = await page.$$eval('h1', els => els.length);
    if (h1Count === 0) console.log('   - ⚠️ No H1 tag found');
    if (h1Count > 1) console.log(`   - ⚠️ Multiple H1 tags found (${h1Count})`);
    
    // Check for missing alt on images
    if (altCoverage < 100) {
      console.log(`   - ⚠️ ${totalImages - imagesWithAlt} images missing alt text`);
    }
    
    // Page load time (approximate)
    const timing = await page.evaluate(() => performance.timing);
    const loadTime = timing.loadEventEnd - timing.navigationStart;
    console.log(`   - Page load time: ~${loadTime}ms`);
    
    if (loadTime > 3000) {
      console.log('   - ⚠️ Slow page load (>3s)');
    }
    
  } catch (error) {
    console.error('Error analyzing almirah.com:', error.message);
  }
  
  await browser.close();
})();
