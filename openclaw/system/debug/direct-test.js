const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const WP_USER = 'Dan';
const WP_PASS = 'RR#Tonic@2026';
const workspace = '/Users/sheikhown/.openclaw/workspace';

(async () => {
  console.log('=== TonicPhysio - Direct Navigation Test ===\n');
  
  // Use regular context but with proper cookie settings
  const browser = await chromium.launch({ 
    headless: false, 
    slowMo: 100
  });
  
  const context = await browser.newContext({
    viewport: { width: 1920, height: 1080 },
    acceptDownloads: true,
    bypassCSP: true
  });
  
  const page = await context.newPage();
  
  // Disable automation detection
  await page.addInitScript(() => {
    Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
  });
  
  try {
    // First, check if site is accessible
    console.log('1. Testing site accessibility...');
    const response = await page.goto('https://tonicphysio.com/', { waitUntil: 'networkidle' });
    console.log('Status:', response.status());
    await page.screenshot({ path: path.join(workspace, 'home-test.png') });
    
    // Try direct admin access
    console.log('\n2. Trying direct wp-admin access...');
    const adminResponse = await page.goto('https://tonicphysio.com/wp-admin/', { waitUntil: 'networkidle' });
    console.log('Admin status:', adminResponse.status());
    console.log('Admin URL:', page.url());
    await page.screenshot({ path: path.join(workspace, 'admin-test.png') });
    
    // If redirected to login, proceed with login
    if (page.url().includes('wp-login')) {
      console.log('\n3. Logging in...');
      
      // Clear any existing cookies first
      await context.clearCookies();
      
      await page.fill('#user_login', WP_USER);
      await page.fill('#user_pass', WP_PASS);
      
      // Use keyboard Enter instead of click
      await page.press('#user_pass', 'Enter');
      await page.waitForTimeout(2000);
      
      console.log('URL after login attempt:', page.url());
      await page.screenshot({ path: path.join(workspace, 'after-login.png') });
      
      // Check if logged in
      if (!page.url().includes('wp-admin')) {
        console.log('⚠ Login failed, checking for error...');
        const errorMsg = await page.$('.notice, .error');
        if (errorMsg) {
          console.log('Error:', await errorMsg.textContent());
        }
      } else {
        console.log('✓ Login successful!');
      }
    }
    
    // Try to access editor directly
    console.log('\n4. Accessing Pediatric editor...');
    try {
      const editorResponse = await page.goto('https://tonicphysio.com/wp-admin/post.php?post=1793&action=edit', { 
        waitUntil: 'networkidle',
        timeout: 30000 
      });
      console.log('Editor status:', editorResponse.status());
      console.log('Editor URL:', page.url());
      await page.waitForTimeout(5000);
      await page.screenshot({ path: path.join(workspace, 'editor-test.png'), fullPage: true });
      
      if (page.url().includes('wp-admin') && !page.url().includes('wp-login')) {
        console.log('✓ Successfully in editor!');
        
        // Look for ACF fields
        console.log('\n5. Searching for ACF fields...');
        await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
        await page.waitForTimeout(3000);
        
        const acfFields = await page.$$eval('[data-name]', els => 
          els.map(el => el.getAttribute('data-name')).filter(n => n)
        );
        console.log('ACF fields:', acfFields.length);
        acfFields.forEach(n => console.log(`  - ${n}`));
        
        // Look for image fields specifically
        const whyChooseField = await page.$('[data-name="why_choose_us_image"]');
        const solutionsField = await page.$('[data-name="solutions_image"]');
        
        console.log('why_choose_us_image found:', !!whyChooseField);
        console.log('solutions_image found:', !!solutionsField);
        
        if (whyChooseField || solutionsField) {
          console.log('\n6. ACF fields detected - upload possible!');
          await page.screenshot({ path: path.join(workspace, 'acf-fields-found.png') });
        }
      }
    } catch (e) {
      console.log('Editor access error:', e.message);
      await page.screenshot({ path: path.join(workspace, 'editor-error.png') });
    }
    
    console.log('\n✅ Debug session complete. Check screenshots.');
    
  } catch (error) {
    console.error('❌ Error:', error.message);
    await page.screenshot({ path: path.join(workspace, 'final-error.png') });
  } finally {
    await browser.close();
  }
})();
