const { firefox } = require('playwright');
const path = require('path');
const fs = require('fs');

const WP_USER = 'Dan';
const WP_PASS = 'RR#Tonic@2026';
const workspace = '/Users/sheikhown/.openclaw/workspace';

(async () => {
  console.log('=== TonicPhysio - Firefox Attempt ===\n');
  
  const browser = await firefox.launch({ 
    headless: false, 
    slowMo: 100
  });
  
  const context = await browser.newContext({
    viewport: { width: 1920, height: 1080 }
  });
  
  const page = await context.newPage();
  
  try {
    console.log('1. Login page...');
    await page.goto('https://tonicphysio.com/wp-login.php', { waitUntil: 'networkidle' });
    await page.waitForTimeout(2000);
    
    console.log('2. Filling credentials...');
    await page.fill('#user_login', WP_USER);
    await page.fill('#user_pass', WP_PASS);
    await page.waitForTimeout(1000);
    
    console.log('3. Submitting...');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle', timeout: 30000 }),
      page.click('#wp-submit')
    ]);
    await page.waitForTimeout(5000);
    
    const url = page.url();
    console.log('URL:', url);
    
    const content = await page.content();
    if (content.includes('Cookies are blocked')) {
      console.log('⚠ Firefox also has cookies error');
      await page.screenshot({ path: path.join(workspace, 'firefox-cookies-error.png') });
    } else if (url.includes('wp-admin')) {
      console.log('✓ Firefox login worked!');
      await page.screenshot({ path: path.join(workspace, 'firefox-dashboard.png') });
      
      console.log('\n4. Editor...');
      await page.goto('https://tonicphysio.com/wp-admin/post.php?post=1793&action=edit', { 
        waitUntil: 'networkidle',
        timeout: 30000 
      });
      await page.waitForTimeout(8000);
      console.log('URL:', page.url());
      
      if (!page.url().includes('wp-login')) {
        await page.screenshot({ path: path.join(workspace, 'firefox-editor.png'), fullPage: true });
        console.log('✓ In editor!');
      }
    }
    
  } catch (error) {
    console.error('❌ Error:', error.message);
  } finally {
    await browser.close();
  }
})();
