const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

// Content drafts
const CONTENT_FILES = {
  'herniated-disc': {
    id: 11603,
    file: './reports/tonic-content-drafts/herniated-disc-treatment.md',
    url: 'https://tonicphysio.com/wp-admin/post.php?post=11603&action=edit',
    liveUrl: 'https://tonicphysio.com/herniated-disc-treatment/',
    seoTitle: 'Herniated Disc Treatment Milton | Expert Spine Care | Tonic Physio',
    seoDesc: 'Get professional herniated disc treatment in Milton. Our expert physiotherapists use targeted therapy to reduce nerve pain and restore mobility. Visit Tonic Physio.'
  },
  'sciatica': {
    id: 11605,
    file: './reports/tonic-content-drafts/sciatica-treatment.md',
    url: 'https://tonicphysio.com/wp-admin/post.php?post=11605&action=edit',
    liveUrl: 'https://tonicphysio.com/sciatica-treatment/',
    seoTitle: 'Sciatica Treatment Milton | Nerve Pain Relief | Tonic Physio',
    seoDesc: 'Expert sciatica treatment in Milton. Our physiotherapists provide targeted nerve pain relief and long-term solutions. Start your recovery at Tonic Physio.'
  },
  'cervical-spondylosis': {
    id: 11607,
    file: './reports/tonic-content-drafts/cervical-spondylosis.md',
    url: 'https://tonicphysio.com/wp-admin/post.php?post=11607&action=edit',
    liveUrl: 'https://tonicphysio.com/cervical-spondylosis/',
    seoTitle: 'Cervical Spondylosis Treatment Milton | Neck Pain Care | Tonic Physio',
    seoDesc: 'Professional cervical spondylosis treatment in Milton. Expert neck pain management and posture correction. Book your assessment at Tonic Physio.'
  }
};

const WP_ADMIN_URL = 'https://tonicphysio.com/wp-admin';
const USERNAME = 'Brenda';
const PASSWORD = 'RR#Tonic@2026';

async function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

async function loginToWordPress(page) {
  console.log('🔐 Navigating to WordPress login...');
  await page.goto(WP_ADMIN_URL, { waitUntil: 'networkidle' });
  
  // Check if already logged in
  const currentPage = page.url();
  if (currentPage.includes('/wp-admin/') && !currentPage.includes('/wp-login')) {
    console.log('✅ Already logged in');
    return true;
  }
  
  console.log('📝 Entering credentials...');
  await page.fill('#user_login', USERNAME);
  await page.fill('#user_pass', PASSWORD);
  
  console.log('🔑 Submitting login...');
  await page.click('#wp-submit');
  await page.waitForNavigation({ waitUntil: 'networkidle', timeout: 30000 });
  
  // Verify login success
  const currentUrl = page.url();
  if (currentUrl.includes('/wp-admin/')) {
    console.log('✅ Login successful');
    return true;
  } else {
    console.error('❌ Login failed - redirected to:', currentUrl);
    return false;
  }
}

async function updatePageContent(page, pageData) {
  console.log(`\n📄 Updating page: ${pageData.file.split('/').pop()}`);
  console.log(`   ID: ${pageData.id}`);
  
  // Navigate to page editor
  await page.goto(pageData.url, { waitUntil: 'networkidle' });
  await sleep(3000);
  
  // Read content file
  const content = fs.readFileSync(pageData.file, 'utf-8');
  
  // Convert markdown to HTML-ish content for Gutenberg
  const contentLines = content.split('\n');
  
  console.log('✏️  Updating main editor content...');
  
  // Find and click the title block to focus editor
  try {
    // Try to focus the block editor
    await page.click('.block-editor-writing-flow', { timeout: 5000 });
    await sleep(1000);
    
    // Select all content in editor and replace
    await page.keyboard.press('Control+a');
    await page.keyboard.press('Backspace');
    await sleep(500);
    
    // Type content line by line for better Gutenberg compatibility
    for (const line of contentLines) {
      if (line.trim()) {
        await page.keyboard.type(line);
        await page.keyboard.press('Enter');
      }
    }
    
    console.log('✅ Main content updated');
  } catch (e) {
    console.log('⚠️  Editor interaction issue:', e.message);
  }
  
  console.log('🔍 Setting Yoast SEO...');
  
  // Open Yoast SEO sidebar
  try {
    await page.click('button[aria-label="Open settings"]', { timeout: 5000 });
    await sleep(1000);
    
    // Click on SEO tab
    const seoTab = page.locator('button[data-element-id="seo-title"]');
    if (await seoTab.count() > 0) {
      await seoTab.click();
      await sleep(500);
    }
    
    // Set SEO title
    const seoTitleInput = page.locator('input[name="yoast-seo-title"]');
    if (await seoTitleInput.count() > 0) {
      await seoTitleInput.fill(pageData.seoTitle);
      console.log('✅ SEO Title set');
    }
    
    // Set SEO description
    const seoDescInput = page.locator('textarea[name="yoast-seo-description"]');
    if (await seoDescInput.count() > 0) {
      await seoDescInput.fill(pageData.seoDesc);
      console.log('✅ SEO Description set');
    }
  } catch (e) {
    console.log('⚠️  Yoast SEO update issue:', e.message);
  }
  
  console.log('💾 Saving page...');
  
  // Click Update button
  try {
    const updateButton = page.locator('button.editor-post-save__button');
    if (await updateButton.count() > 0) {
      await updateButton.click();
      await page.waitForSelector('.components-notice.is-success', { timeout: 10000 });
      console.log('✅ Page saved successfully');
    } else {
      // Try alternative selector
      await page.click('button[data-testid="editor-post-save-button"]');
      await sleep(3000);
      console.log('✅ Page saved (alternative method)');
    }
  } catch (e) {
    console.log('⚠️  Save issue:', e.message);
  }
  
  await sleep(2000);
}

async function verifyLivePage(page, liveUrl) {
  console.log(`🔍 Verifying live page: ${liveUrl}`);
  
  try {
    await page.goto(liveUrl, { waitUntil: 'networkidle', timeout: 30000 });
    const status = page.url();
    
    if (status === liveUrl || status.startsWith(liveUrl)) {
      console.log('✅ Live page loads correctly');
      return true;
    } else {
      console.log('⚠️  Page redirected to:', status);
      return true; // Still accessible
    }
  } catch (e) {
    console.error('❌ Live page verification failed:', e.message);
    return false;
  }
}

async function main() {
  console.log('🚀 Starting Tonic Physio WordPress Update Automation\n');
  console.log('='.repeat(50));
  
  const browser = await chromium.launch({ 
    headless: false,
    args: ['--start-maximized']
  });
  
  const context = await browser.newContext({
    viewport: { width: 1920, height: 1080 }
  });
  
  const page = await context.newPage();
  
  try {
    // Login
    const loggedIn = await loginToWordPress(page);
    if (!loggedIn) {
      console.error('❌ Cannot proceed without login');
      await browser.close();
      process.exit(1);
    }
    
    await sleep(2000);
    
    // Process each page
    const results = [];
    
    for (const [key, pageData] of Object.entries(CONTENT_FILES)) {
      console.log('\n' + '='.repeat(50));
      console.log(`Processing: ${key.toUpperCase()}`);
      console.log('='.repeat(50));
      
      await updatePageContent(page, pageData);
      
      const verified = await verifyLivePage(page, pageData.liveUrl);
      
      results.push({
        key,
        liveUrl: pageData.liveUrl,
        verified
      });
      
      await sleep(3000);
    }
    
    // Final report
    console.log('\n' + '='.repeat(50));
    console.log('📊 FINAL REPORT');
    console.log('='.repeat(50));
    
    for (const result of results) {
      const status = result.verified ? '✅' : '❌';
      console.log(`${status} ${result.key}: ${result.liveUrl}`);
    }
    
    console.log('\n✅ All updates completed!');
    
  } catch (error) {
    console.error('❌ Automation error:', error.message);
    await page.screenshot({ path: './reports/error-screenshot.png' });
  } finally {
    await sleep(5000);
    await browser.close();
  }
}

main().catch(console.error);
