const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();
  
  try {
    console.log('Navigating to login page...');
    await page.goto('https://cms.khanllp.com/login', { waitUntil: 'networkidle' });
    
    console.log('Filling credentials...');
    // Try common WordPress/CMS selectors
    const userField = await page.locator('input[name="username"], input[name="user_login"], input[type="text"]').first();
    const passField = await page.locator('input[name="password"], input[name="user_pass"], input[type="password"]').first();
    
    await userField.fill('own');
    await passField.fill('Rank#kLLP@2025.ray');
    
    console.log('Clicking login...');
    const loginBtn = await page.locator('button[type="submit"], input[type="submit"], text=Log In, text=Login').first();
    await loginBtn.click();
    
    // Wait for navigation to a page that isn't the login page
    await page.waitForURL(url => !url.includes('/login'), { timeout: 15000 });
    console.log('Login successful. Current URL: ' + page.url());

    console.log('Navigating to Blogs section...');
    // Look for a link or menu item containing "Blogs"
    const blogsLink = await page.locator('a:has-text("Blogs"), span:has-text("Blogs"), div:has-text("Blogs")').first();
    await blogsLink.click();
    
    console.log('Looking for "Add New" button...');
    // Look for "Add New" link within the context of Blogs
    const addNewBtn = await page.locator('a:has-text("Add New"), button:has-text("Add New")').first();
    await addNewBtn.click();
    
    await page.waitForLoadState('networkidle');
    console.log('Successfully reached Add New page!');
    console.log('Final URL: ' + page.url());
    process.exit(0);
  } catch (e) {
    console.error('Verification failed: ', e);
    process.exit(1);
  } finally {
    await browser.close();
  }
})();
