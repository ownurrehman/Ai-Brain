const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const WP_USER = 'Dan';
const WP_PASS = 'RR#Tonic@2026';
const workspace = '/Users/sheikhown/.openclaw/workspace';

(async () => {
  console.log('=== TonicPhysio Image Upload - Debug Mode ===\n');
  
  const browser = await chromium.launch({ headless: false, slowMo: 200 });
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
    console.log('✓ Logged in, URL:', page.url());
    await page.screenshot({ path: path.join(workspace, '01-dashboard.png') });
    
    // Go to Pediatric page editor
    console.log('\n2. Opening Pediatric page editor (ID: 1793)...');
    await page.goto('https://tonicphysio.com/wp-admin/post.php?post=1793&action=edit');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(5000);
    console.log('✓ Editor loaded, URL:', page.url());
    await page.screenshot({ path: path.join(workspace, '02-ped-editor-full.png'), fullPage: true });
    
    // Check if we're in Gutenberg or Classic editor
    const isGutenberg = await page.$('.editor-styles-wrapper, .block-editor-writing-flow');
    console.log('Is Gutenberg editor:', !!isGutenberg);
    
    // Scroll through entire page and screenshot sections
    const scrollHeight = await page.evaluate(() => document.body.scrollHeight);
    console.log('Page height:', scrollHeight);
    
    // Scroll to bottom to find ACF
    await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
    await page.waitForTimeout(2000);
    await page.screenshot({ path: path.join(workspace, '03-ped-bottom.png') });
    
    // Look for ACF meta box
    const acfMetaBox = await page.$('#acf-group_1793, .acf-fields');
    console.log('ACF meta box found:', !!acfMetaBox);
    
    // Look for any file inputs
    const fileInputs = await page.$$eval('input[type="file"], input[type="hidden"][name*="acf"]', els => 
      els.map(el => ({ tag: el.tagName, type: el.type, name: el.name, id: el.id, value: el.value?.substring(0,50) }))
    );
    console.log('File/ACF inputs found:', fileInputs.length);
    if (fileInputs.length > 0) {
      console.log('First 5:', JSON.stringify(fileInputs.slice(0, 5), null, 2));
    }
    
    // Look for ACF image uploader buttons
    const acfButtons = await page.$$eval('.acf-button, .acf-icon, button[data-name*="image"]', els =>
      els.map(el => ({ text: el.textContent?.trim(), class: el.className, tag: el.tagName }))
    );
    console.log('ACF buttons found:', acfButtons.length);
    if (acfButtons.length > 0) {
      console.log('ACF buttons:', JSON.stringify(acfButtons.slice(0, 10), null, 2));
    }
    
    // Look for all elements with data-name attribute (ACF uses this)
    const acfElements = await page.$$eval('[data-name]', els =>
      els.map(el => ({ name: el.getAttribute('data-name'), tag: el.tagName, class: el.className?.substring(0,100) }))
    );
    console.log('Elements with data-name:', acfElements.length);
    acfElements.forEach(el => console.log(`  - ${el.name} (${el.tag})`));
    
    // Try to find why_choose_us_image field
    const whyChooseField = await page.$('[data-name="why_choose_us_image"]');
    console.log('why_choose_us_image field found:', !!whyChooseField);
    
    const solutionsField = await page.$('[data-name="solutions_image"]');
    console.log('solutions_image field found:', !!solutionsField);
    
    if (whyChooseField) {
      console.log('\n3. Attempting to upload why_choose_us_image...');
      const uploadBtn = await whyChooseField.$('.acf-button, button, .acf-icon');
      if (uploadBtn) {
        console.log('Found upload button');
        await uploadBtn.click();
        await page.waitForTimeout(3000);
        await page.screenshot({ path: path.join(workspace, '04-media-modal.png') });
        
        // Try to upload in media modal
        const fileInput = await page.$('.media-modal input[type="file"]');
        if (fileInput) {
          await fileInput.setInputFiles(path.join(workspace, 'pediatric-physiotherapy-why-choose.jpg'));
          await page.waitForTimeout(5000);
          await page.screenshot({ path: path.join(workspace, '05-uploading.png') });
          
          // Click insert
          const insertBtn = await page.$('.media-frame button.media-button-insert, button.media-button-select');
          if (insertBtn) {
            await insertBtn.click();
            await page.waitForTimeout(2000);
            console.log('✓ Image inserted');
          }
        }
      }
    }
    
    if (solutionsField) {
      console.log('\n4. Attempting to upload solutions_image...');
      const uploadBtn = await solutionsField.$('.acf-button, button, .acf-icon');
      if (uploadBtn) {
        console.log('Found upload button');
        await uploadBtn.click();
        await page.waitForTimeout(3000);
        
        const fileInput = await page.$('.media-modal input[type="file"]');
        if (fileInput) {
          await fileInput.setInputFiles(path.join(workspace, 'pediatric-physiotherapy-solutions.jpg'));
          await page.waitForTimeout(5000);
          
          const insertBtn = await page.$('.media-frame button.media-button-insert, button.media-button-select');
          if (insertBtn) {
            await insertBtn.click();
            await page.waitForTimeout(2000);
            console.log('✓ Image inserted');
          }
        }
      }
    }
    
    // Update page
    console.log('\n5. Updating Pediatric page...');
    const updateBtn = await page.$('#publishing-action button, #submitpublish, .editor-post-publish-button');
    if (updateBtn) {
      await updateBtn.click();
      await page.waitForTimeout(5000);
      console.log('✓ Page updated');
      await page.screenshot({ path: path.join(workspace, '06-ped-updated.png') });
    }
    
    // Go to Orthopedic page
    console.log('\n6. Opening Orthopedic page editor (ID: 1791)...');
    await page.goto('https://tonicphysio.com/wp-admin/post.php?post=1791&action=edit');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(5000);
    await page.screenshot({ path: path.join(workspace, '07-ortho-editor-full.png'), fullPage: true });
    
    // Repeat for orthopedic
    const orthoWhyChoose = await page.$('[data-name="why_choose_us_image"]');
    const orthoSolutions = await page.$('[data-name="solutions_image"]');
    
    if (orthoWhyChoose) {
      console.log('\n7. Uploading orthopedic why_choose_us_image...');
      const uploadBtn = await orthoWhyChoose.$('.acf-button, button, .acf-icon');
      if (uploadBtn) {
        await uploadBtn.click();
        await page.waitForTimeout(3000);
        const fileInput = await page.$('.media-modal input[type="file"]');
        if (fileInput) {
          await fileInput.setInputFiles(path.join(workspace, 'orthopedic-physiotherapy-why-choose.jpg'));
          await page.waitForTimeout(5000);
          const insertBtn = await page.$('.media-frame button.media-button-insert, button.media-button-select');
          if (insertBtn) {
            await insertBtn.click();
            await page.waitForTimeout(2000);
            console.log('✓ Image inserted');
          }
        }
      }
    }
    
    if (orthoSolutions) {
      console.log('\n8. Uploading orthopedic solutions_image...');
      const uploadBtn = await orthoSolutions.$('.acf-button, button, .acf-icon');
      if (uploadBtn) {
        await uploadBtn.click();
        await page.waitForTimeout(3000);
        const fileInput = await page.$('.media-modal input[type="file"]');
        if (fileInput) {
          await fileInput.setInputFiles(path.join(workspace, 'orthopedic-physiotherapy-solutions.jpg'));
          await page.waitForTimeout(5000);
          const insertBtn = await page.$('.media-frame button.media-button-insert, button.media-button-select');
          if (insertBtn) {
            await insertBtn.click();
            await page.waitForTimeout(2000);
            console.log('✓ Image inserted');
          }
        }
      }
    }
    
    // Update page
    console.log('\n9. Updating Orthopedic page...');
    const orthoUpdateBtn = await page.$('#publishing-action button, #submitpublish, .editor-post-publish-button');
    if (orthoUpdateBtn) {
      await orthoUpdateBtn.click();
      await page.waitForTimeout(5000);
      console.log('✓ Page updated');
      await page.screenshot({ path: path.join(workspace, '08-ortho-updated.png') });
    }
    
    // Verify frontend
    console.log('\n10. Verifying frontend...');
    await page.goto('https://tonicphysio.com/physiotherapy-in-milton/pediatric-physiotherapy/');
    await page.waitForTimeout(3000);
    await page.screenshot({ path: path.join(workspace, '09-pediatric-frontend.png'), fullPage: true });
    console.log('✓ Pediatric frontend screenshot saved');
    
    await page.goto('https://tonicphysio.com/physiotherapy-in-milton/orthopedic-physiotherapy/');
    await page.waitForTimeout(3000);
    await page.screenshot({ path: path.join(workspace, '10-orthopedic-frontend.png'), fullPage: true });
    console.log('✓ Orthopedic frontend screenshot saved');
    
    console.log('\n✅ COMPLETED! Check screenshots in workspace.');
    
  } catch (error) {
    console.error('❌ Error:', error.message);
    await page.screenshot({ path: path.join(workspace, 'error-debug.png') });
    throw error;
  } finally {
    await browser.close();
  }
})();
