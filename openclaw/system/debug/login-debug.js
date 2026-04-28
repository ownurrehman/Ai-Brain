const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const WP_USER = 'Dan';
const WP_PASS = 'RR#Tonic@2026';
const workspace = '/Users/sheikhown/.openclaw/workspace';

(async () => {
  console.log('=== TonicPhysio Login Debug ===\n');
  
  const browser = await chromium.launch({ 
    headless: false, 
    slowMo: 100,
    args: ['--disable-blink-features=AutomationControlled']
  });
  
  const context = await browser.newContext({
    viewport: { width: 1920, height: 1080 }
  });
  const page = await context.newPage();
  
  await page.addInitScript(() => {
    Object.defineProperty(navigator, 'webdriver', { get: () => undefined });
  });
  
  try {
    console.log('1. Going to login page...');
    await page.goto('https://tonicphysio.com/wp-login.php', { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(3000);
    await page.screenshot({ path: path.join(workspace, 'step1-login-page.png') });
    
    console.log('2. Checking login form...');
    const loginForm = await page.$('#loginform');
    console.log('Login form exists:', !!loginForm);
    
    const userField = await page.$('#user_login');
    const passField = await page.$('#user_pass');
    const submitBtn = await page.$('#wp-submit');
    
    console.log('Username field:', !!userField);
    console.log('Password field:', !!passField);
    console.log('Submit button:', !!submitBtn);
    
    console.log('\n3. Filling credentials...');
    if (userField) {
      await userField.fill(WP_USER);
      console.log('✓ Username filled');
    }
    
    if (passField) {
      await passField.fill(WP_PASS);
      console.log('✓ Password filled');
    }
    
    await page.waitForTimeout(2000);
    await page.screenshot({ path: path.join(workspace, 'step2-filled.png') });
    
    console.log('\n4. Clicking submit...');
    if (submitBtn) {
      // Try different click methods
      await Promise.race([
        (async () => {
          await submitBtn.click();
          console.log('✓ Clicked with .click()');
        })(),
        new Promise(resolve => setTimeout(resolve, 15000))
      ]);
      
      await page.waitForTimeout(5000);
      await page.screenshot({ path: path.join(workspace, 'step3-after-click.png') });
      
      const url = page.url();
      console.log('URL after click:', url);
      
      // Check if we're on dashboard or still on login
      if (url.includes('wp-admin')) {
        console.log('✓ Successfully logged in!');
        
        // Now navigate to editor
        console.log('\n5. Navigating to Pediatric editor...');
        await page.goto('https://tonicphysio.com/wp-admin/post.php?post=1793&action=edit', { 
          waitUntil: 'networkidle',
          timeout: 30000 
        });
        await page.waitForTimeout(5000);
        await page.screenshot({ path: path.join(workspace, 'step4-editor.png'), fullPage: true });
        console.log('✓ Editor page loaded');
        console.log('Editor URL:', page.url());
        
      } else if (url.includes('wp-login')) {
        console.log('⚠ Still on login page');
        
        // Check for error messages
        const errorMsg = await page.$('.notice, .error, #login_error');
        if (errorMsg) {
          const errorText = await errorMsg.textContent();
          console.log('Error message:', errorText.trim());
        }
      }
    }
    
  } catch (error) {
    console.error('❌ Error:', error.message);
    await page.screenshot({ path: path.join(workspace, 'error-debug.png') });
  } finally {
    await browser.close();
  }
})();
