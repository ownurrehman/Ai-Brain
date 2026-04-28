const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();

  try {
    console.log('Navigating to https://cms.khanllp.com/login...');
    await page.goto('https://cms.khanllp.com/login');

    console.log('Attempting login...');
    // I'll use selectors based on common CMS patterns, but I'll first take a snapshot if I can't find them.
    // Since I can't see the page, I'll try to find inputs by label or common names.
    await page.fill('input[name="username"], input[id="username"], input[type="text"]', 'own');
    await page.fill('input[name="password"], input[id="password"], input[type="password"]', 'Rank#kLLP@2025.ray');
    
    // Click the login button - looking for common login button types
    await page.click('button[type="submit"], input[type="submit"], .login-button, #login-btn');

    await page.waitForURL(/dashboard|admin|home/, { timeout: 15000 }).catch(() => {
       console.log('URL did not change to expected dashboard, checking content...');
    });

    // Verify login success by checking for a logout button or user profile
    const isLoggedIn = await page.isVisible('text=Logout, text=Sign Out, .user-profile');
    if (!isLoggedIn) {
      // If not found, maybe it's a different layout. Check title or common elements.
      const content = await page.content();
      if (content.includes('Invalid') || content.includes('Error')) {
        throw new Error('Login failed: Invalid credentials or error message found on page.');
      }
    }
    console.log('Login successful (or navigated to internal page).');

    console.log('Navigating to Blogs...');
    // Look for "Blogs" in the sidebar
    await page.click('text=Blogs, a[href*="blogs"], .sidebar-link-blogs');
    
    console.log('Checking for "Add New" blog option...');
    const addNewVisible = await page.isVisible('text=Add New, .add-new-blog, a[href*="add-new"]');
    
    if (addNewVisible) {
      console.log('SUCCESS: "Add New" blog option is accessible.');
    } else {
      throw new Error('FAILED: "Add New" blog option not found on the Blogs page.');
    }

  } catch (error) {
    console.error('ERROR:', error.message);
    await page.screenshot({ path: 'error_screenshot.png' });
    process.exit(1);
  } finally {
    await browser.close();
  }
})();
