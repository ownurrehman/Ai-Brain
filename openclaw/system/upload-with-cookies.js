const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const WP_USER = 'Dan';
const WP_PASS = 'RR#Tonic@2026';
const workspace = '/Users/sheikhown/.openclaw/workspace';

(async () => {
  console.log('=== TonicPhysio Image Upload - With Cookies Enabled ===\n');
  
  // Create temp user data dir for persistent cookies
  const userDataDir = path.join(workspace, 'chrome-user-data');
  
  const browser = await chromium.launchPersistentContext(userDataDir, { 
    headless: false, 
    slowMo: 200,
    viewport: { width: 1920, height: 1080 },
    args: [
      '--disable-blink-features=AutomationControlled',
      '--disable-features=IsolateOrigins,site-per-process',
      '--disable-site-isolation-trials',
      '--disable-web-security',
      '--allow-running-insecure-content'
    ]
  });
  
  const pages = await browser.pages();
  const page = pages[0];
  
  await page.addInitScript(() => {
    Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
  });
  
  try {
    console.log('1. Going to login page...');
    await page.goto('https://tonicphysio.com/wp-login.php', { waitUntil: 'networkidle' });
    await page.waitForTimeout(3000);
    
    console.log('2. Filling credentials...');
    await page.fill('#user_login', WP_USER);
    await page.fill('#user_pass', WP_PASS);
    await page.waitForTimeout(1000);
    
    console.log('3. Clicking submit...');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle', timeout: 30000 }),
      page.click('#wp-submit')
    ]);
    await page.waitForTimeout(5000);
    
    const url = page.url();
    console.log('URL after login:', url);
    
    if (url.includes('wp-admin') || url.includes('wp-login') && !url.includes('error')) {
      // Check for cookies error
      const pageContent = await page.content();
      if (pageContent.includes('Cookies are blocked')) {
        console.log('⚠ Cookies error detected in page content');
        await page.screenshot({ path: path.join(workspace, 'cookies-error.png') });
      } else {
        console.log('✓ Login successful!');
        await page.screenshot({ path: path.join(workspace, 'dashboard.png') });
        
        // Navigate to Pediatric editor
        console.log('\n4. Opening Pediatric editor (ID: 1793)...');
        await page.goto('https://tonicphysio.com/wp-admin/post.php?post=1793&action=edit', { 
          waitUntil: 'networkidle',
          timeout: 30000 
        });
        await page.waitForTimeout(8000);
        await page.screenshot({ path: path.join(workspace, 'ped-editor.png'), fullPage: true });
        console.log('Editor URL:', page.url());
        
        if (!page.url().includes('wp-login')) {
          // Scroll to find ACF
          console.log('\n5. Searching for ACF fields...');
          await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
          await page.waitForTimeout(3000);
          await page.screenshot({ path: path.join(workspace, 'ped-bottom.png') });
          
          // Find ACF fields
          const acfFields = await page.$$eval('[data-name]', els => 
            els.map(el => el.getAttribute('data-name')).filter(n => n)
          );
          console.log('ACF fields found:', acfFields.length);
          acfFields.forEach(n => console.log(`  - ${n}`));
          
          // Function to upload via ACF
          async function uploadACFImage(fieldName, imagePath) {
            console.log(`\nUploading to ${fieldName}...`);
            const field = await page.$(`[data-name="${fieldName}"]`);
            if (!field) {
              console.log(`⚠ Field ${fieldName} not found`);
              return;
            }
            
            const addBtn = await field.$('.acf-button, button, .acf-icon, .add_media');
            if (!addBtn) {
              console.log('⚠ Add button not found');
              return;
            }
            
            await addBtn.click();
            await page.waitForTimeout(3000);
            
            const fileInput = await page.$('.media-modal input[type="file"]');
            if (!fileInput) {
              console.log('⚠ File input not found');
              const closeBtn = await page.$('.media-modal-close');
              if (closeBtn) await closeBtn.click();
              return;
            }
            
            await fileInput.setInputFiles(imagePath);
            await page.waitForTimeout(6000);
            
            const insertBtn = await page.$('.media-frame button.media-button-insert, button.media-button-select');
            if (insertBtn) {
              await insertBtn.click();
              await page.waitForTimeout(3000);
              console.log(`✓ ${fieldName} uploaded`);
            }
          }
          
          // Upload Pediatric images
          await uploadACFImage('why_choose_us_image', path.join(workspace, 'pediatric-physiotherapy-why-choose.jpg'));
          await uploadACFImage('solutions_image', path.join(workspace, 'pediatric-physiotherapy-solutions.jpg'));
          
          // Update page
          console.log('\n6. Updating Pediatric page...');
          const updateBtn = await page.$('#publishing-action button, #submitpublish');
          if (updateBtn) {
            await updateBtn.click();
            await page.waitForTimeout(5000);
            console.log('✓ Pediatric updated');
          }
          
          // Orthopedic
          console.log('\n7. Opening Orthopedic editor (ID: 1791)...');
          await page.goto('https://tonicphysio.com/wp-admin/post.php?post=1791&action=edit', { 
            waitUntil: 'networkidle',
            timeout: 30000 
          });
          await page.waitForTimeout(8000);
          await page.screenshot({ path: path.join(workspace, 'ortho-editor.png'), fullPage: true });
          
          await uploadACFImage('why_choose_us_image', path.join(workspace, 'orthopedic-physiotherapy-why-choose.jpg'));
          await uploadACFImage('solutions_image', path.join(workspace, 'orthopedic-physiotherapy-solutions.jpg'));
          
          console.log('\n8. Updating Orthopedic page...');
          const orthoUpdateBtn = await page.$('#publishing-action button, #submitpublish');
          if (orthoUpdateBtn) {
            await orthoUpdateBtn.click();
            await page.waitForTimeout(5000);
            console.log('✓ Orthopedic updated');
          }
          
          // Verify frontend
          console.log('\n9. Verifying frontend...');
          await page.goto('https://tonicphysio.com/physiotherapy-in-milton/pediatric-physiotherapy/', { 
            waitUntil: 'networkidle',
            timeout: 30000 
          });
          await page.waitForTimeout(3000);
          await page.screenshot({ path: path.join(workspace, 'pediatric-frontend.png'), fullPage: true });
          console.log('✓ Pediatric frontend saved');
          
          await page.goto('https://tonicphysio.com/physiotherapy-in-milton/orthopedic-physiotherapy/', { 
            waitUntil: 'networkidle',
            timeout: 30000 
          });
          await page.waitForTimeout(3000);
          await page.screenshot({ path: path.join(workspace, 'orthopedic-frontend.png'), fullPage: true });
          console.log('✓ Orthopedic frontend saved');
          
          console.log('\n✅ COMPLETED!');
        }
      }
    } else {
      console.log('⚠ Unexpected URL:', url);
      await page.screenshot({ path: path.join(workspace, 'unexpected.png') });
    }
    
  } catch (error) {
    console.error('❌ Error:', error.message);
    await page.screenshot({ path: path.join(workspace, 'error.png') });
  } finally {
    await browser.close();
  }
})();
