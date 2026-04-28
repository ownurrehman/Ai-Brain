const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const BASE_URL = 'https://tonicphysio.com';
const WP_USER = 'Dan';
const WP_PASS = 'RR#Tonic@2026';
const workspace = '/Users/sheikhown/.openclaw/workspace/tonicphysio';

(async () => {
  console.log('=== TonicPhysio Image Upload via Browser Automation ===\n');
  
  const browser = await chromium.launch({ headless: false, slowMo: 500 });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    // Login to WordPress
    console.log('Step 1: Logging into WordPress...');
    await page.goto('https://tonicphysio.com/wp-login.php');
    await page.waitForSelector('#user_login');
    await page.fill('#user_login', WP_USER);
    await page.fill('#user_pass', WP_PASS);
    await page.click('#wp-submit');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(5000);
    console.log('✓ Logged in. Current URL:', page.url());
    
    // Function to upload image via Media Library
    async function uploadImageViaMediaLibrary(imagePath) {
      // Go to media library
      await page.goto('https://tonicphysio.com/wp-admin/upload.php?mode=list');
      await page.waitForLoadState('networkidle');
      await page.waitForTimeout(2000);
      
      // Click "Add New"
      const addNewBtn = page.locator('.page-title-action').first();
      if (await addNewBtn.count() > 0) {
        await addNewBtn.click();
        await page.waitForTimeout(2000);
      }
      
      // Find file input in media library
      const fileInput = page.locator('input[type="file"][id^="html5"]');
      if (await fileInput.count() > 0) {
        await fileInput.setInputFiles(imagePath);
        await page.waitForTimeout(5000); // Wait for upload
        
        // Get the media ID from the attachment details
        await page.waitForTimeout(2000);
        const url = page.url();
        const match = url.match(/item=(\d+)/);
        const mediaId = match ? match[1] : null;
        
        if (mediaId) {
          console.log(`✓ Uploaded, Media ID: ${mediaId}`);
          return parseInt(mediaId);
        }
      }
      return null;
    }
    
    // Function to update page ACF fields
    async function updatePageACF(pageId, pageName, whyChooseId, solutionsId) {
      console.log(`\nUpdating ${pageName} page (ID: ${pageId})...`);
      
      // Go to page editor
      await page.goto(`https://tonicphysio.com/wp-admin/post.php?post=${pageId}&action=edit`);
      await page.waitForLoadState('networkidle');
      await page.waitForTimeout(5000);
      
      // Take screenshot to see the editor
      await page.screenshot({ path: path.join(workspace, `${pageName}-editor.png`), fullPage: true });
      console.log(`Screenshot saved: ${pageName}-editor.png`);
      
      // Scroll to find ACF fields
      await page.evaluate(() => window.scrollBy(0, 3000));
      await page.waitForTimeout(2000);
      
      // Look for ACF image fields - they have specific data attributes
      const acfFields = await page.$$eval('[data-name]', els => 
        els.filter(el => el.getAttribute('data-name')?.includes('why_choose') || el.getAttribute('data-name')?.includes('solutions'))
           .map(el => ({ name: el.getAttribute('data-name'), key: el.getAttribute('data-key') }))
      );
      console.log('Found ACF fields:', acfFields);
      
      // Try to find image attachment IDs in the form
      const hiddenInputs = await page.$$eval('input[type="hidden"]', els =>
        els.filter(el => el.name?.includes('acf['))
           .map(el => ({ name: el.name, value: el.value }))
      );
      console.log('ACF hidden inputs:', hiddenInputs.slice(0, 5));
      
      // Look for the actual image ID input fields
      const imageIdInputs = await page.$$eval('input', els =>
        els.filter(el => el.name?.includes('why_choose_us_image') || el.name?.includes('solutions_image'))
           .map(el => ({ name: el.name, value: el.value, type: el.type }))
      );
      console.log('Image ID inputs:', imageIdInputs);
      
      // Update the image ID fields
      for (const input of imageIdInputs) {
        if (input.name.includes('why_choose_us_image') && whyChooseId) {
          const selector = `input[name="${input.name}"]`;
          await page.fill(selector, whyChooseId.toString());
          console.log(`✓ Set why_choose_us_image to ${whyChooseId}`);
        } else if (input.name.includes('solutions_image') && solutionsId) {
          const selector = `input[name="${input.name}"]`;
          await page.fill(selector, solutionsId.toString());
          console.log(`✓ Set solutions_image to ${solutionsId}`);
        }
      }
      
      // Update/Publish page
      const updateBtn = page.locator('#publishing-action button, #submitpublish, .editor-post-publish-button').first();
      if (await updateBtn.count() > 0) {
        await updateBtn.click();
        await page.waitForTimeout(5000);
        console.log(`✓ ${pageName} page updated`);
        return true;
      } else {
        console.log('⚠ Update button not found');
        return false;
      }
    }
    
    // Upload Pediatric images
    console.log('\n=== Pediatric Physiotherapy ===');
    const pediatricWhyChoose = await uploadImageViaMediaLibrary(path.join(workspace, 'pediatric-physiotherapy-why-choose.jpg'));
    const pediatricSolutions = await uploadImageViaMediaLibrary(path.join(workspace, 'pediatric-physiotherapy-solutions.jpg'));
    
    if (pediatricWhyChoose && pediatricSolutions) {
      await updatePageACF(1793, 'pediatric', pediatricWhyChoose, pediatricSolutions);
    }
    
    // Upload Orthopedic images
    console.log('\n=== Orthopedic Physiotherapy ===');
    const orthoWhyChoose = await uploadImageViaMediaLibrary(path.join(workspace, 'orthopedic-physiotherapy-why-choose.jpg'));
    const orthoSolutions = await uploadImageViaMediaLibrary(path.join(workspace, 'orthopedic-physiotherapy-solutions.jpg'));
    
    if (orthoWhyChoose && orthoSolutions) {
      await updatePageACF(1791, 'orthopedic', orthoWhyChoose, orthoSolutions);
    }
    
    // Verify frontend
    console.log('\n=== Verifying Frontend ===');
    await page.goto('https://tonicphysio.com/physiotherapy-in-milton/pediatric-physiotherapy/');
    await page.waitForTimeout(3000);
    await page.screenshot({ path: path.join(workspace, 'pediatric-verify.png'), fullPage: true });
    console.log('✓ Pediatric frontend screenshot saved');
    
    await page.goto('https://tonicphysio.com/physiotherapy-in-milton/orthopedic-physiotherapy/');
    await page.waitForTimeout(3000);
    await page.screenshot({ path: path.join(workspace, 'orthopedic-verify.png'), fullPage: true });
    console.log('✓ Orthopedic frontend screenshot saved');
    
    console.log('\n✅ Upload process completed!');
    console.log('Check screenshots to verify images are visible.');
    
  } catch (error) {
    console.error('❌ Error:', error.message);
    await page.screenshot({ path: path.join(workspace, 'error-screenshot.png') });
    console.log('Error screenshot saved');
    throw error;
  } finally {
    await browser.close();
  }
})();
