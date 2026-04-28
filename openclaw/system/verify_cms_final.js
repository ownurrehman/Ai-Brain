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

    // Wait for navigation or a sign that we are logged in
    console.log('Waiting for login to complete...');
    try {
      await page.waitForURL(/dashboard|admin|home|index/, { timeout: 15000 });
    } catch (e) {
      console.log('URL wait timed out, checking page content for success markers...');
    }

    const content = await page.content();
    if (content.includes('Invalid') || content.includes('incorrect') || content.includes('Error')) {
      throw new Error('Login failed: Error message found on page.');
    }

    console.log('Login successful. Looking for "Blogs" in the sidebar...');
    
    // The user wants to navigate to 'Blogs' in the left sidebar.
    // I'll look for any link or element containing "Blogs"
    const blogsLink = page.locator('text=Blogs, a:has-text("Blogs"), .sidebar a:has-text("Blogs")').first();
    
    if (await blogsLink.isVisible()) {
      console.log('Blogs link found. Clicking...');
      await blogsLink.click();
      await page.waitForLoadState('networkidle');
    } else {
      throw new Error('FAILED: "Blogs" option not found in the sidebar.');
    }

    console.log('Checking for "Add New" blog option...');
    const addNewLink = page.locator('text=Add New, a:has-text("Add New"), .btn-add-new').first();
    
    if (await addNewLink.isVisible()) {
      console.log('SUCCESS: "Add New" blog option is accessible.');
    } else {
      throw new Error('FAILED: "Add New" blog option not found on the Blogs page.');
    }

  } catch (error) {
    console.error('ERROR:', error.message);
    await page.screenshot({ path: 'failure_state.png' });
    process.exit(1);
  } finally {
    await browser.close();
  }
})();
