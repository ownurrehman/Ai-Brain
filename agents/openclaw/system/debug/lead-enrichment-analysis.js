const { chromium } = require('playwright');

const sites = [
  { name: 'Khaadi', url: 'https://khaadi.com' },
  { name: 'Gul Ahmed', url: 'https://gulahmed.com' },
  { name: 'Saphire', url: 'https://www.saphire.com.pk' }
];

async function analyzeSite(page, site) {
  console.log(`\n${'='.repeat(60)}`);
  console.log(`ANALYZING: ${site.name} (${site.url})`);
  console.log('='.repeat(60));
  
  const result = {
    name: site.name,
    url: site.url,
    metaTitle: '',
    metaDescription: '',
    h1Count: 0,
    h1Texts: [],
    h2Count: 0,
    h2Texts: [],
    imageCount: 0,
    imagesWithoutAlt: 0,
    hasBlog: false,
    blogUrl: '',
    aboutPageUrl: '',
    teamPageUrl: '',
    decisionMakers: [],
    technicalGaps: [],
    personalizationHook: ''
  };
  
  try {
    await page.goto(site.url, { waitUntil: 'domcontentloaded', timeout: 15000 });
    await page.waitForTimeout(3000);
    
    // Meta data
    result.metaTitle = await page.title();
    result.metaDescription = await page.$eval('meta[name="description"]', el => el.getAttribute('content')).catch(() => '');
    
    // H1 structure
    result.h1Texts = await page.$$eval('h1', els => els.map(el => el.textContent.trim()).filter(t => t));
    result.h1Count = result.h1Texts.length;
    
    // H2 structure
    result.h2Texts = await page.$$eval('h2', els => els.map(el => el.textContent.trim()).filter(t => t));
    result.h2Count = result.h2Texts.length;
    
    // Images
    const allImages = await page.$$('img');
    result.imageCount = allImages.length;
    result.imagesWithoutAlt = await page.$$eval('img', els => els.filter(img => !img.hasAttribute('alt') || img.getAttribute('alt').trim() === '').length);
    
    // Blog detection
    const blogLink = await page.$eval('a[href*="blog"], a[href*="article"], a[href*="news"], a:has-text("Blog"), a:has-text("Articles")', el => el.href).catch(() => '');
    if (blogLink) {
      result.hasBlog = true;
      result.blogUrl = blogLink;
    }
    
    // About/Team pages
    const aboutLink = await page.$eval('a[href*="about"], a[href*="team"], a[href*="company"], a:has-text("About"), a:has-text("Our Story"), a:has-text("Company")', el => el.href).catch(() => '');
    if (aboutLink) result.aboutPageUrl = aboutLink;
    
    // Extract decision makers from About page if found
    if (result.aboutPageUrl) {
      try {
        await page.goto(result.aboutPageUrl, { waitUntil: 'domcontentloaded', timeout: 10000 });
        await page.waitForTimeout(2000);
        const names = await page.$$eval('h1, h2, h3, h4, p', els => {
          const text = els.map(el => el.textContent.trim()).join(' ');
          const namePatterns = text.match(/(?:CEO|Founder|Director|Manager|Head of|Chief)[^,.\n]{0,50}/gi);
          return namePatterns ? namePatterns.slice(0, 3) : [];
        });
        result.decisionMakers = names;
        await page.goto(site.url, { waitUntil: 'domcontentloaded', timeout: 10000 });
      } catch (e) {
        console.log(`Could not extract from about page: ${e.message}`);
      }
    }
    
    // Technical gaps
    if (result.h1Count === 0) result.technicalGaps.push('Missing H1 tag');
    if (result.h1Count > 1) result.technicalGaps.push(`Multiple H1 tags (${result.h1Count})`);
    if (result.imagesWithoutAlt > 0) result.technicalGaps.push(`${result.imagesWithoutAlt} images missing alt text`);
    if (!result.hasBlog) result.technicalGaps.push('No blog section detected');
    
    // Personalization hook
    if (result.h2Texts.length > 0) {
      result.personalizationHook = `Focus on ${result.h2Texts[0]} - shows their current priority`;
    } else if (result.imageCount > 50) {
      result.personalizationHook = `Heavy visual merchandising (${result.imageCount} images) - likely value product photography`;
    } else {
      result.personalizationHook = 'E-commerce focus with standard structure';
    }
    
    console.log(`Meta Title: ${result.metaTitle}`);
    console.log(`Meta Description: ${result.metaDescription || '(missing)'}`);
    console.log(`H1 Tags: ${result.h1Count} - ${result.h1Texts.slice(0, 2).join(', ')}`);
    console.log(`H2 Tags: ${result.h2Count}`);
    console.log(`Images: ${result.imageCount} total, ${result.imagesWithoutAlt} missing alt`);
    console.log(`Blog: ${result.hasBlog ? result.blogUrl : 'Not found'}`);
    console.log(`About Page: ${result.aboutPageUrl || 'Not found'}`);
    console.log(`Team Page: ${result.teamPageUrl || 'Not found'}`);
    console.log(`Technical Gaps: ${result.technicalGaps.join('; ') || 'None detected'}`);
    console.log(`Personalization Hook: ${result.personalizationHook}`);
    
  } catch (error) {
    console.log(`Error analyzing ${site.name}: ${error.message}`);
    result.technicalGaps.push(`Page load error: ${error.message}`);
  }
  
  return result;
}

async function main() {
  console.log('Starting Lead Enrichment Analysis');
  console.log(`Target: ${sites.map(s => s.name).join(', ')}`);
  console.log(`Started: ${new Date().toISOString()}`);
  
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  
  const results = [];
  for (const site of sites) {
    const result = await analyzeSite(page, site);
    results.push(result);
    await page.waitForTimeout(1000);
  }
  
  await browser.close();
  
  console.log('\n' + '='.repeat(60));
  console.log('SUMMARY REPORT');
  console.log('='.repeat(60));
  console.log(JSON.stringify(results, null, 2));
  
  return results;
}

main().catch(console.error);
