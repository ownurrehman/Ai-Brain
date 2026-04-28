const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const WP_USER = 'Dan';
const WP_PASS = 'RR#Tonic@2026';
const workspace = '/Users/sheikhown/.openclaw/workspace';

(async () => {
  console.log('=== TonicPhysio Image Upload ===\n');
  
  const browser = await chromium.launch({ headless: false, slowMo: 300 });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    // Login
    console.log('1. Logging in...');
    await page.goto('https://tonicphysio.com/wp-login.php');
    await page.fill('#user_login', WP_USER);
    await page.fill('#user_pass', WP_PASS);
    await page.click('#wp-submit');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(3000);
    console.log('✓ Logged in');
    
    // Go to Pediatric page editor
    console.log('\n2. Editing Pediatric Physiotherapy (ID: 1793)...');
    await page.goto('https://tonicphysio.com/wp-admin/post.php?post=1793&action=edit');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(5000);
    
    // Take screenshot
    await page.screenshot({ path: path.join(workspace, 'ped-editor.png'), fullPage: true });
    console.log('✓ Screenshot saved: ped-editor.png');
    
    // Scroll down to find ACF fields
    await page.evaluate(() => window.scrollBy(0, 1500));
    await page.waitForTimeout(2000);
    
    // Look for ACF image fields
    const acfFields = await page.$$eval('.acf-field', els => 
      els.map(el => ({
        name: el.getAttribute('data-name'),
        key: el.getAttribute('data-key'),
        hasImage: !!el.querySelector('.acf-image-uploader')
      }))
    );
    console.log('ACF fields found:', acfFields.length);
    acfFields.forEach(f => console.log(`  - ${f.name}: ${f.hasImage ? 'has image uploader' : 'no uploader'}`));
    
    // Find and upload to why_choose_us_image
    const whyChooseField = acfFields.find(f => f.name === 'why_choose_us_image');
    if (whyChooseField) {
      console.log('\n3. Uploading pediatric-physiotherapy-why-choose.jpg...');
      const uploadBtn = page.locator(`[data-name="why_choose_us_image"] .acf-button`).first();
      if (await uploadBtn.count() > 0) {
        await uploadBtn.click();
        await page.waitForTimeout(2000);
        
        // Switch to media library modal and upload
        const fileInput = page.locator('.media-modal input[type="file"]');
        if (await fileInput.count() > 0) {
          await fileInput.setInputFiles(path.join(workspace, 'pediatric-physiotherapy-why-choose.jpg'));
          await page.waitForTimeout(5000);
          
          // Insert image
          const insertBtn = page.locator('.media-frame button.media-button-insert');
          if (await insertBtn.count() > 0) {
            await insertBtn.click();
            await page.waitForTimeout(2000);
            console.log('✓ Image uploaded');
          }
        }
      }
    }
    
    // Find and upload to solutions_image
    const solutionsField = acfFields.find(f => f.name === 'solutions_image');
    if (solutionsField) {
      console.log('\n4. Uploading pediatric-physiotherapy-solutions.jpg...');
      const uploadBtn = page.locator(`[data-name="solutions_image"] .acf-button`).first();
      if (await uploadBtn.count() > 0) {
        await uploadBtn.click();
        await page.waitForTimeout(2000);
        
        const fileInput = page.locator('.media-modal input[type="file"]');
        if (await fileInput.count() > 0) {
          await fileInput.setInputFiles(path.join(workspace, 'pediatric-physiotherapy-solutions.jpg'));
          await page.waitForTimeout(5000);
          
          const insertBtn = page.locator('.media-frame button.media-button-insert');
          if (await insertBtn.count() > 0) {
            await insertBtn.click();
            await page.waitForTimeout(2000);
            console.log('✓ Image uploaded');
          }
        }
      }
    }
    
    // Update page
    console.log('\n5. Updating Pediatric page...');
    const updateBtn = page.locator('#publishing-action button, #submitpublish').first();
    if (await updateBtn.count() > 0) {
      await updateBtn.click();
      await page.waitForTimeout(5000);
      console.log('✓ Page updated');
    }
    
    // Go to Orthopedic page editor
    console.log('\n6. Editing Orthopedic Physiotherapy (ID: 1791)...');
    await page.goto('https://tonicphysio.com/wp-admin/post.php?post=1791&action=edit');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(5000);
    
    await page.screenshot({ path: path.join(workspace, 'ortho-editor.png'), fullPage: true });
    console.log('✓ Screenshot saved: ortho-editor.png');
    
    await page.evaluate(() => window.scrollBy(0, 1500));
    await page.waitForTimeout(2000);
    
    const orthoAcfFields = await page.$$eval('.acf-field', els => 
      els.map(el => ({
        name: el.getAttribute('data-name'),
        key: el.getAttribute('data-key')
      }))
    );
    console.log('ACF fields found:', orthoAcfFields.length);
    
    // Upload orthopedic images
    const orthoWhyChoose = orthoAcfFields.find(f => f.name === 'why_choose_us_image');
    if (orthoWhyChoose) {
      console.log('\n7. Uploading orthopedic-physiotherapy-why-choose.jpg...');
      const uploadBtn = page.locator(`[data-name="why_choose_us_image"] .acf-button`).first();
      if (await uploadBtn.count() > 0) {
        await uploadBtn.click();
        await page.waitForTimeout(2000);
        
        const fileInput = page.locator('.media-modal input[type="file"]');
        if (await fileInput.count() > 0) {
          await fileInput.setInputFiles(path.join(workspace, 'orthopedic-physiotherapy-why-choose.jpg'));
          await page.waitForTimeout(5000);
          
          const insertBtn = page.locator('.media-frame button.media-button-insert');
          if (await insertBtn.count() > 0) {
            await insertBtn.click();
            await page.waitForTimeout(2000);
            console.log('✓ Image uploaded');
          }
        }
      }
    }
    
    const orthoSolutions = orthoAcfFields.find(f => f.name === 'solutions_image');
    if (orthoSolutions) {
      console.log('\n8. Uploading orthopedic-physiotherapy-solutions.jpg...');
      const uploadBtn = page.locator(`[data-name="solutions_image"] .acf-button`).first();
      if (await uploadBtn.count() > 0) {
        await uploadBtn.click();
        await page.waitForTimeout(2000);
        
        const fileInput = page.locator('.media-modal input[type="file"]');
        if (await fileInput.count() > 0) {
          await fileInput.setInputFiles(path.join(workspace, 'orthopedic-physiotherapy-solutions.jpg'));
          await page.waitForTimeout(5000);
          
          const insertBtn = page.locator('.media-frame button.media-button-insert');
          if (await insertBtn.count() > 0) {
            await insertBtn.click();
            await page.waitForTimeout(2000);
            console.log('✓ Image uploaded');
          }
        }
      }
    }
    
    // Update page
    console.log('\n9. Updating Orthopedic page...');
    const orthoUpdateBtn = page.locator('#publishing-action button, #submitpublish').first();
    if (await orthoUpdateBtn.count() > 0) {
      await orthoUpdateBtn.click();
      await page.waitForTimeout(5000);
      console.log('✓ Page updated');
    }
    
    // Verify frontend
    console.log('\n10. Verifying frontend...');
    await page.goto('https://tonicphysio.com/physiotherapy-in-milton/pediatric-physiotherapy/');
    await page.waitForTimeout(3000);
    await page.screenshot({ path: path.join(workspace, 'pediatric-verify.png'), fullPage: true });
    console.log('✓ Pediatric screenshot saved');
    
    await page.goto('https://tonicphysio.com/physiotherapy-in-milton/orthopedic-physiotherapy/');
    await page.waitForTimeout(3000);
    await page.screenshot({ path: path.join(workspace, 'orthopedic-verify.png'), fullPage: true });
    console.log('✓ Orthopedic screenshot saved');
    
    console.log('\n✅ COMPLETED!');
    console.log('\nPages:');
    console.log('  Pediatric: https://tonicphysio.com/physiotherapy-in-milton/pediatric-physiotherapy/');
    console.log('  Orthopedic: https://tonicphysio.com/physiotherapy-in-milton/orthopedic-physiotherapy/');
    
  } catch (error) {
    console.error('❌ Error:', error.message);
    await page.screenshot({ path: path.join(workspace, 'error.png') });
    throw error;
  } finally {
    await browser.close();
  }
})();
