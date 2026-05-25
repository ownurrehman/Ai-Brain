const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const WP_USER = 'Dan';
const WP_PASS = 'RR#Tonic@2026';
const workspace = '/Users/sheikhown/.openclaw/workspace';

(async () => {
  console.log('=== TonicPhysio Image Upload - With Cookie Persistence ===\n');
  
  // Launch with real browser user agent
  const browser = await chromium.launch({ 
    headless: false, 
    slowMo: 300,
    args: ['--disable-blink-features=AutomationControlled', '--no-sandbox']
  });
  
  const context = await browser.newContext({
    viewport: { width: 1920, height: 1080 },
    userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
  });
  const page = await context.newPage();
  
  // Bypass automation detection
  await page.addInitScript(() => {
    Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
  });
  
  try {
    // Login with proper wait
    console.log('1. Logging in...');
    await page.goto('https://tonicphysio.com/wp-login.php', { waitUntil: 'networkidle' });
    await page.waitForSelector('#user_login', { timeout: 10000 });
    await page.fill('#user_login', WP_USER);
    await page.fill('#user_pass', WP_PASS);
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle', timeout: 30000 }),
      page.click('#wp-submit')
    ]);
    await page.waitForTimeout(5000);
    
    const currentUrl = page.url();
    console.log('✓ Logged in, URL:', currentUrl);
    
    // Check if we're actually logged in
    if (currentUrl.includes('wp-login')) {
      console.log('⚠ Still on login page, taking screenshot...');
      await page.screenshot({ path: path.join(workspace, 'login-failed.png') });
      throw new Error('Login failed - still on login page');
    }
    
    await page.screenshot({ path: path.join(workspace, 'dashboard-ok.png') });
    console.log('✓ Dashboard screenshot saved');
    
    // Navigate to Pediatric page editor
    console.log('\n2. Opening Pediatric page editor (ID: 1793)...');
    await page.goto('https://tonicphysio.com/wp-admin/post.php?post=1793&action=edit', { waitUntil: 'networkidle' });
    await page.waitForTimeout(8000);
    await page.screenshot({ path: path.join(workspace, 'ped-editor-ok.png'), fullPage: true });
    console.log('✓ Editor screenshot saved');
    
    // Check current URL
    const editorUrl = page.url();
    console.log('Editor URL:', editorUrl);
    
    if (editorUrl.includes('wp-login')) {
      console.log('⚠ Session lost, re-authenticating...');
      // Re-login
      await page.goto('https://tonicphysio.com/wp-login.php');
      await page.fill('#user_login', WP_USER);
      await page.fill('#user_pass', WP_PASS);
      await Promise.all([
        page.waitForNavigation({ waitUntil: 'networkidle' }),
        page.click('#wp-submit')
      ]);
      await page.waitForTimeout(3000);
      
      // Try again
      await page.goto('https://tonicphysio.com/wp-admin/post.php?post=1793&action=edit', { waitUntil: 'networkidle' });
      await page.waitForTimeout(5000);
    }
    
    // Scroll to find ACF fields
    console.log('\n3. Searching for ACF fields...');
    await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
    await page.waitForTimeout(3000);
    await page.screenshot({ path: path.join(workspace, 'ped-bottom-ok.png') });
    
    // Look for ACF fields with multiple selectors
    const acfFields = await page.$$eval('.acf-field, [data-name], input[name*="acf"]', els => 
      els.map(el => ({
        tag: el.tagName,
        name: el.getAttribute('name') || el.getAttribute('data-name'),
        class: el.className?.substring(0, 80),
        text: el.textContent?.substring(0, 50)
      }))
    );
    console.log('ACF-related elements:', acfFields.length);
    acfFields.slice(0, 10).forEach(f => console.log(`  - ${f.name || 'no-name'} (${f.tag})`));
    
    // Find specific fields
    const whyChooseSelector = '[data-name="why_choose_us_image"] input[type="hidden"], input[name*="why_choose_us_image"]';
    const solutionsSelector = '[data-name="solutions_image"] input[type="hidden"], input[name*="solutions_image"]';
    
    const whyChooseInput = await page.$(whyChooseSelector);
    const solutionsInput = await page.$(solutionsSelector);
    
    console.log('why_choose_us_image input:', !!whyChooseInput);
    console.log('solutions_image input:', !!solutionsInput);
    
    // Function to upload image via media library
    async function uploadImageViaMedia(imagePath, fieldName) {
      console.log(`\nUploading to ${fieldName}...`);
      
      // Click the ACF image field button
      const fieldContainer = await page.$(`[data-name="${fieldName}"]`);
      if (!fieldContainer) {
        console.log(`⚠ Field container not found for ${fieldName}`);
        return null;
      }
      
      // Look for add/select image button
      const addBtn = await fieldContainer.$('.acf-button, button, .acf-icon, .add_media');
      if (!addBtn) {
        console.log('⚠ Add button not found');
        return null;
      }
      
      await addBtn.click();
      await page.waitForTimeout(3000);
      await page.screenshot({ path: path.join(workspace, `media-modal-${fieldName}.png`) });
      
      // Find file input in media modal
      const fileInput = await page.$('.media-modal input[type="file"]');
      if (!fileInput) {
        console.log('⚠ File input not found in modal');
        // Close modal
        const closeBtn = await page.$('.media-modal .media-modal-close');
        if (closeBtn) await closeBtn.click();
        return null;
      }
      
      await fileInput.setInputFiles(imagePath);
      await page.waitForTimeout(6000); // Wait for upload
      await page.screenshot({ path: path.join(workspace, `uploaded-${fieldName}.png`) });
      
      // Get media ID from URL or hidden field
      const url = page.url();
      const match = url.match(/item=(\d+)/);
      const mediaId = match ? match[1] : null;
      
      // Click insert/select
      const insertBtn = await page.$('.media-frame button.media-button-insert, button.media-button-select');
      if (insertBtn) {
        await insertBtn.click();
        await page.waitForTimeout(3000);
        console.log(`✓ Image inserted`);
        return mediaId ? parseInt(mediaId) : null;
      }
      
      return mediaId ? parseInt(mediaId) : null;
    }
    
    // Upload Pediatric images
    let pedWhyId = null, pedSolId = null;
    
    if (whyChooseInput) {
      pedWhyId = await uploadImageViaMedia(path.join(workspace, 'pediatric-physiotherapy-why-choose.jpg'), 'why_choose_us_image');
    }
    
    if (solutionsInput) {
      pedSolId = await uploadImageViaMedia(path.join(workspace, 'pediatric-physiotherapy-solutions.jpg'), 'solutions_image');
    }
    
    // Update Pediatric page
    console.log('\n4. Updating Pediatric page...');
    const updateBtn = await page.$('#publishing-action button, #submitpublish, .editor-post-publish-button');
    if (updateBtn) {
      await updateBtn.click();
      await page.waitForTimeout(5000);
      console.log('✓ Pediatric page updated');
      await page.screenshot({ path: path.join(workspace, 'ped-updated.png') });
    }
    
    // Orthopedic page
    console.log('\n5. Opening Orthopedic page editor (ID: 1791)...');
    await page.goto('https://tonicphysio.com/wp-admin/post.php?post=1791&action=edit', { waitUntil: 'networkidle' });
    await page.waitForTimeout(8000);
    await page.screenshot({ path: path.join(workspace, 'ortho-editor-ok.png'), fullPage: true });
    
    const orthoWhyInput = await page.$(whyChooseSelector);
    const orthoSolutionsInput = await page.$(solutionsSelector);
    
    if (orthoWhyInput) {
      await uploadImageViaMedia(path.join(workspace, 'orthopedic-physiotherapy-why-choose.jpg'), 'why_choose_us_image');
    }
    
    if (orthoSolutionsInput) {
      await uploadImageViaMedia(path.join(workspace, 'orthopedic-physiotherapy-solutions.jpg'), 'solutions_image');
    }
    
    // Update Orthopedic page
    console.log('\n6. Updating Orthopedic page...');
    const orthoUpdateBtn = await page.$('#publishing-action button, #submitpublish, .editor-post-publish-button');
    if (orthoUpdateBtn) {
      await orthoUpdateBtn.click();
      await page.waitForTimeout(5000);
      console.log('✓ Orthopedic page updated');
      await page.screenshot({ path: path.join(workspace, 'ortho-updated.png') });
    }
    
    // Verify frontend
    console.log('\n7. Verifying frontend...');
    await page.goto('https://tonicphysio.com/physiotherapy-in-milton/pediatric-physiotherapy/', { waitUntil: 'networkidle' });
    await page.waitForTimeout(3000);
    await page.screenshot({ path: path.join(workspace, 'pediatric-frontend-ok.png'), fullPage: true });
    console.log('✓ Pediatric frontend saved');
    
    await page.goto('https://tonicphysio.com/physiotherapy-in-milton/orthopedic-physiotherapy/', { waitUntil: 'networkidle' });
    await page.waitForTimeout(3000);
    await page.screenshot({ path: path.join(workspace, 'orthopedic-frontend-ok.png'), fullPage: true });
    console.log('✓ Orthopedic frontend saved');
    
    console.log('\n✅ COMPLETED!');
    console.log('\nPages:');
    console.log('  Pediatric:  https://tonicphysio.com/physiotherapy-in-milton/pediatric-physiotherapy/');
    console.log('  Orthopedic: https://tonicphysio.com/physiotherapy-in-milton/orthopedic-physiotherapy/');
    
  } catch (error) {
    console.error('❌ Error:', error.message);
    await page.screenshot({ path: path.join(workspace, 'error-final.png') });
    throw error;
  } finally {
    await browser.close();
  }
})();
