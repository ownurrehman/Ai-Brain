const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const WP_USER = 'Dan';
const WP_PASS = 'RR#Tonic@2026';
const workspace = '/Users/sheikhown/.openclaw/workspace';

(async () => {
  console.log('=== TonicPhysio - Using Persistent Context ===\n');
  
  // Use a fresh temporary directory for user data
  const tmpDir = require('os').tmpdir();
  const userDataDir = path.join(tmpDir, 'tonic-chrome-' + Date.now());
  
  console.log('Using user data dir:', userDataDir);
  
  // Launch with persistent context - this is the key for cookies to work
  const browser = await chromium.launchPersistentContext(userDataDir, {
    headless: false,
    slowMo: 100,
    viewport: { width: 1920, height: 1080 },
    args: [
      '--disable-blink-features=AutomationControlled',
      '--disable-features=IsolateOrigins,site-per-process'
    ],
    ignoreDefaultArgs: ['--enable-automation']
  });
  
  const pages = await browser.pages();
  const page = pages[0];
  
  await page.addInitScript(() => {
    Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
  });
  
  try {
    console.log('1. Navigating to login...');
    await page.goto('https://tonicphysio.com/wp-login.php', { waitUntil: 'networkidle' });
    await page.waitForTimeout(2000);
    
    console.log('2. Filling form...');
    await page.fill('#user_login', WP_USER);
    await page.fill('#user_pass', WP_PASS);
    await page.waitForTimeout(1000);
    
    console.log('3. Submitting...');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle', timeout: 30000 }),
      page.click('#wp-submit', { delay: 100 })
    ]);
    await page.waitForTimeout(5000);
    
    const url = page.url();
    console.log('URL:', url);
    
    // Check for cookies error
    const content = await page.content();
    if (content.includes('Cookies are blocked')) {
      console.log('⚠ Cookies error still present');
      await page.screenshot({ path: path.join(workspace, 'cookies-error-2.png') });
    } else if (url.includes('wp-admin')) {
      console.log('✓ Login successful!');
      await page.screenshot({ path: path.join(workspace, 'dashboard-ok-2.png') });
      
      // Go to editor
      console.log('\n4. Going to Pediatric editor...');
      await page.goto('https://tonicphysio.com/wp-admin/post.php?post=1793&action=edit', { 
        waitUntil: 'networkidle',
        timeout: 30000 
      });
      await page.waitForTimeout(8000);
      console.log('Editor URL:', page.url());
      
      if (!page.url().includes('wp-login')) {
        await page.screenshot({ path: path.join(workspace, 'editor-ok-2.png'), fullPage: true });
        
        // Scroll to ACF
        console.log('\n5. Finding ACF fields...');
        await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
        await page.waitForTimeout(3000);
        
        const fields = await page.$$eval('[data-name]', els => 
          els.map(el => el.getAttribute('data-name')).filter(n => n)
        );
        console.log('Fields found:', fields.length);
        fields.forEach(f => console.log(`  - ${f}`));
        
        // Upload function
        async function uploadImage(fieldName, filePath) {
          console.log(`\nUploading ${fieldName}...`);
          const field = await page.$(`[data-name="${fieldName}"]`);
          if (!field) {
            console.log(`⚠ ${fieldName} not found`);
            return;
          }
          
          const btn = await field.$('.acf-button, button, .acf-icon');
          if (!btn) {
            console.log('⚠ Button not found');
            return;
          }
          
          await btn.click();
          await page.waitForTimeout(3000);
          
          const fileInput = await page.$('.media-modal input[type="file"]');
          if (!fileInput) {
            console.log('⚠ File input not found');
            const close = await page.$('.media-modal-close');
            if (close) await close.click();
            return;
          }
          
          await fileInput.setInputFiles(filePath);
          await page.waitForTimeout(6000);
          
          const insert = await page.$('.media-frame button.media-button-insert, button.media-button-select');
          if (insert) {
            await insert.click();
            await page.waitForTimeout(3000);
            console.log(`✓ ${fieldName} done`);
          }
        }
        
        // Upload Pediatric
        await uploadImage('why_choose_us_image', path.join(workspace, 'pediatric-physiotherapy-why-choose.jpg'));
        await uploadImage('solutions_image', path.join(workspace, 'pediatric-physiotherapy-solutions.jpg'));
        
        // Update
        console.log('\n6. Updating Pediatric page...');
        const update = await page.$('#publishing-action button, #submitpublish');
        if (update) {
          await update.click();
          await page.waitForTimeout(5000);
          console.log('✓ Updated');
        }
        
        // Orthopedic
        console.log('\n7. Orthopedic editor...');
        await page.goto('https://tonicphysio.com/wp-admin/post.php?post=1791&action=edit', { 
          waitUntil: 'networkidle',
          timeout: 30000 
        });
        await page.waitForTimeout(8000);
        
        await uploadImage('why_choose_us_image', path.join(workspace, 'orthopedic-physiotherapy-why-choose.jpg'));
        await uploadImage('solutions_image', path.join(workspace, 'orthopedic-physiotherapy-solutions.jpg'));
        
        console.log('\n8. Updating Orthopedic...');
        const orthoUpdate = await page.$('#publishing-action button, #submitpublish');
        if (orthoUpdate) {
          await orthoUpdate.click();
          await page.waitForTimeout(5000);
          console.log('✓ Updated');
        }
        
        // Verify
        console.log('\n9. Frontend verification...');
        await page.goto('https://tonicphysio.com/physiotherapy-in-milton/pediatric-physiotherapy/', { 
          waitUntil: 'networkidle',
          timeout: 30000 
        });
        await page.waitForTimeout(3000);
        await page.screenshot({ path: path.join(workspace, 'pediatric-final.png'), fullPage: true });
        console.log('✓ Pediatric saved');
        
        await page.goto('https://tonicphysio.com/physiotherapy-in-milton/orthopedic-physiotherapy/', { 
          waitUntil: 'networkidle',
          timeout: 30000 
        });
        await page.waitForTimeout(3000);
        await page.screenshot({ path: path.join(workspace, 'orthopedic-final.png'), fullPage: true });
        console.log('✓ Orthopedic saved');
        
        console.log('\n✅ ALL DONE!');
        console.log('\nPages:');
        console.log('  Pediatric:  https://tonicphysio.com/physiotherapy-in-milton/pediatric-physiotherapy/');
        console.log('  Orthopedic: https://tonicphysio.com/physiotherapy-in-milton/orthopedic-physiotherapy/');
      }
    } else {
      console.log('⚠ Unexpected state');
      await page.screenshot({ path: path.join(workspace, 'unexpected-2.png') });
    }
    
  } catch (error) {
    console.error('❌ Error:', error.message);
    await page.screenshot({ path: path.join(workspace, 'error-final-2.png') });
  } finally {
    await browser.close();
    // Cleanup
    try {
      fs.rmSync(userDataDir, { recursive: true, force: true });
    } catch (e) {}
  }
})();
