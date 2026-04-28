const puppeteer = require('puppeteer');

(async () => {
  const wpAdminUrl = 'https://www.rankray.com/wp-admin';
  const username = 'openclaw';
  const password = 'OC#admin@2026';

  console.log('Attempting to log into WordPress...');
  let browser;
  try {
    browser = await puppeteer.launch({
      executablePath: '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome',
      headless: false, // Keep it visible for debugging and observation
      args: [
        '--no-sandbox',
        '--disable-setuid-sandbox',
      ]
    });
    const page = await browser.newPage();
    console.log(`Navigating to ${wpAdminUrl}...`);
    await page.goto(wpAdminUrl, { waitUntil: 'domcontentloaded' });

    // Check if we are on the login page by looking for common WordPress login form elements
    const isLoginPage = await page.$('#loginform');
    if (!isLoginPage) {
      console.log('Already logged in or page not as expected. Skipping login.');
      // For now, assume if no login form, we're past it.
    } else {
      console.log('Filling in credentials...');
      await page.type('#user_login', username);
      await page.type('#user_pass', password);

      console.log('Clicking login button...');
      await Promise.all([
        page.waitForNavigation({ waitUntil: 'domcontentloaded' }), // Wait for navigation after click
        page.click('#wp-submit'),
      ]);
    }

    // Verify successful login (e.g., check for an element only visible after login)
    const dashboardElement = await page.$('#wpadminbar'); // Common admin bar element
    if (dashboardElement) {
      console.log('Successfully logged into WordPress dashboard!');
      const currentUrl = page.url();
      console.log('Current URL:', currentUrl);
    } else {
      console.error('Login failed or dashboard element not found.');
      // Capture screenshot for debugging if login fails
      await page.screenshot({ path: 'wp_login_failure.png' });
      console.log('Screenshot wp_login_failure.png saved for debugging.');
    }

  } catch (error) {
    console.error('An error occurred during WordPress login automation:');
    console.error(error);
  } finally {
    if (browser) await browser.close();
    console.log('Browser closed.');
  }
})();
