const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();

  try {
    console.log('Navigating to https://cms.khanllp.com/login...');
    await page.goto('https://cms.khanllp.com/login', { waitUntil: 'networkidle' });
    
    console.log('Taking screenshot...');
    await page.screenshot({ path: 'login_page.png' });
    
    console.log('Getting HTML content...');
    const content = await page.content();
    console.log('HTML Snippet:');
    console.log(content.substring(0, 5000)); // Print first 5k chars to avoid flooding

  } catch (error) {
    console.error('ERROR:', error.message);
  } finally {
    await browser.close();
  }
})();
