const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();

  try {
    console.log('Navigating to https://cms.khanllp.com/login...');
    await page.goto('https://cms.khanllp.com/login', { waitUntil: 'networkidle' });

    console.log('Filling credentials...');
    await page.fill('#username', 'own');
    await page.fill('#password', 'Rank#kLLP@2025.ray');

    console.log('Clicking login button...');
    await page.click('button.btn-login');

    console.log('Waiting for response...');
    await page.waitForLoadState('networkidle');

    const content = await page.content();
    console.log('--- Page Content Snippet ---');
    console.log(content.substring(0, 10000));
    console.log('---------------------------');

    if (content.includes('Invalid') || content.includes('incorrect') || content.includes('Error')) {
      console.log('Detected login error in page content.');
      await page.screenshot({ path: 'login_error.png' });
    } else {
      console.log('No obvious error message found. Checking for dashboard markers...');
      if (content.includes('Dashboard') || content.includes('Logout')) {
        console.log('Login seems successful.');
      } else {
        console.log('Neither error nor success markers found.');
      }
    }

  } catch (error) {
    console.error('CRITICAL ERROR:', error.message);
  } finally {
    await browser.close();
  }
})();
