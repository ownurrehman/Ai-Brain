const { chromium } = require('playwright');

(async () => {
  // Use the existing browser profile with saved cookies
  const browser = await chromium.launchPersistentContext(
    '/Users/sheikhown/.openclaw/workspace/.browser-profiles/rankray-wp',
    { headless: true }
  );
  
  const page = await browser.pages()[0];

  // Navigate to the page editor
  console.log('Navigating to page editor...');
  await page.goto('https://tonicphysio.com/wp-admin/post.php?post=1791&action=edit', { waitUntil: 'networkidle' });
  
  // Check if we're logged in
  const currentUrl = page.url();
  console.log('Current URL:', currentUrl);
  
  if (currentUrl.includes('wp-login')) {
    console.log('Not logged in. Attempting login...');
    await page.fill('#user_login', 'Dan');
    await page.fill('#user_pass', 'APpX GLFR oMuV oPor ZLxy HMTc');
    await page.click('#wp-submit');
    await page.waitForTimeout(5000);
    console.log('After login attempt. Current URL:', page.url());
  }
  
  // Wait for ACF fields to load
  try {
    await page.waitForSelector('.acf-field', { timeout: 10000 });
    console.log('ACF fields loaded');
  } catch (e) {
    console.log('ACF fields not found. Taking screenshot...');
    await page.screenshot({ path: '/Users/sheikhown/.openclaw/workspace/debug-page.png' });
    console.log('Screenshot saved to debug-page.png');
    await browser.close();
    return;
  }

  // Update benefits fields (why_choose_us_point_*)
  const benefits = [
    { field: 'why_choose_us_point_1', value: 'Reduced Pain and Inflammation: Targeted techniques to decrease swelling and manage pain levels effectively.' },
    { field: 'why_choose_us_point_2', value: 'Restored Range of Motion: Customized stretching and joint mobilization to break through stiffness.' },
    { field: 'why_choose_us_point_3', value: 'Increased Strength and Stability: Evidence-based loading programs to protect your joints from future injury.' },
    { field: 'why_choose_us_point_4', value: 'Personalized Recovery Timelines: Treatment plans tailored to your specific goals.' },
    { field: 'why_choose_us_point_5', value: 'Non-Invasive Alternative to Surgery: Effective management of orthopedic conditions, potentially avoiding surgery.' }
  ];

  for (const benefit of benefits) {
    const selector = `[data-name="${benefit.field}"] textarea, [data-name="${benefit.field}"] input`;
    const el = await page.$(selector);
    if (el) {
      await el.fill(benefit.value);
      console.log(`Updated ${benefit.field}`);
    } else {
      console.log(`Field not found: ${benefit.field}`);
    }
  }

  // Update treatment approach fields (solution_*)
  const solutions = [
    { field: 'solution_1', value: 'Step 1: Comprehensive Assessment – Your journey begins with a detailed evaluation. We analyze your medical history, current pain levels, and physical limitations through movement screening and orthopedic testing to create a precise diagnosis.' },
    { field: 'solution_2', value: 'Step 2: Targeted Intervention – Based on your assessment, we implement a blend of manual therapy, joint mobilization, and corrective exercises. This phase focuses on reducing acute pain and restoring the basic mechanics of the affected area.' },
    { field: 'solution_3', value: 'Step 3: Functional Restoration – As you progress, we shift the focus toward strength and endurance. We introduce functional movements that mimic your daily activities, ensuring your body can handle the stresses of your specific lifestyle.' },
    { field: 'solution_4', value: 'Step 4: Maintenance and Prevention – The final stage is about longevity. We provide you with a sustainable home exercise program and lifestyle adjustments to ensure your injury does not return.' }
  ];

  for (const solution of solutions) {
    const selector = `[data-name="${solution.field}"] textarea, [data-name="${solution.field}"] input`;
    const el = await page.$(selector);
    if (el) {
      await el.fill(solution.value);
      console.log(`Updated ${solution.field}`);
    } else {
      console.log(`Field not found: ${solution.field}`);
    }
  }

  // Click update button
  console.log('Saving page...');
  const updateBtn = await page.$('#publishing-action .button-primary');
  if (updateBtn) {
    await updateBtn.click();
    await page.waitForTimeout(5000);
    console.log('Page update submitted!');
  } else {
    console.log('Update button not found');
  }
  
  console.log('Final URL:', page.url());
  
  await browser.close();
})();
