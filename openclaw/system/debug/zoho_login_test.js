const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ 
    headless: true,
    args: ['--disable-blink-features=AutomationControlled'] 
  });
  const context = await browser.newContext({
    viewport: { width: 1280, height: 720 },
    userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36'
  });
  const page = await context.newPage();

  try {
    console.log('Navigating to https://mail.zoho.com...');
    await page.goto('https://mail.zoho.com', { waitUntil: 'networkidle' });
    await page.screenshot({ path: 'zoho_debug_1.png' });

    console.log('Looking for "Sign in" link or input...');
    // Try to find a login field directly on the mail.zoho.com page
    const loginField = page.locator('input[name="login"], input[id="login"], input[type="text"]').first();
    
    if (await loginField.isVisible()) {
      await loginField.fill('enigma@rankray.com');
      console.log('Email filled.');
      await page.locator('button:has-text("Next"), input[type="submit"]').first().click();
      await page.waitForTimeout(2000);
    } else {
      console.log('No login field found on home page. Looking for "Sign In" button...');
      const signInLink = page.locator('a:has-text("Sign In"), button:has-text("Sign In")').first();
      if (await signInLink.isVisible()) {
        await signInLink.click();
        console.log('Sign In clicked.');
        await page.waitForTimeout(3000);
      } else {
        console.log('No sign in link found. No login attempt possible.');
        }
    }

    await page.screenshot({ path: 'zoho_debug_2.png' });

    // Now we are hopefully on the login page
    console.log('Trying to enter password...');
    const passwordField = page.locator('input[type="password"]').first();
    if (await passwordField.isVisible()) {
      await passwordField.fill('RR#Tonic@2026');
      console.log('Password filled.');
      await page.locator('button:has-text("Sign in"), input[type="submit"]').first().click();
      await page.waitForTimeout(5000);
    } else {
      console.log('console.log("Password field not found.")');
    }

    const url = page.url();
    console.log('Final URL: ' + url);
    if (url.includes('mail.zoho.com/zb/all')) {
      console.log('SUCCESS: Reached the inbox.');
    } else {
      console.log('console.log("FAILED: Did not reach inbox.")');
    }

  } catch (error) {
    console.error('Error: ' + error.message);
  } finally {
    await browser.close();
  }
})();
