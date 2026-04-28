const { chromium } = require('playwright');
const { config, ensureDirs } = require('./rankray-common');

(async () => {
  ensureDirs();
  const context = await chromium.launchPersistentContext(config.userDataDir, {
    headless: false,
    viewport: { width: 1440, height: 1000 },
    slowMo: 100
  });
  const page = context.pages()[0] || await context.newPage();
  
  try {
    console.log('Navigating to post edit page...');
    await page.goto('https://tonicphysio.com/wp-admin/post.php?post=12055&action=edit', { waitUntil: 'networkidle' });

    // 1. Set Yoast Focus Keyphrase
    console.log('Setting Yoast Focus Keyphrase...');
    const focusKeyphraseSelector = 'input[name="yoast_dp_focus_kw"]';
    await page.waitForSelector(focusKeyphraseSelector);
    await page.fill(focusKeyphraseSelector, 'Sports Physiotherapy in Milton');

    // 2. Set Meta Description
    console.log('Setting Meta Description...');
    // We need to click "Edit snippet" or find the snippet editor. 
    // Yoast often uses a separate UI for the snippet.
    // Let's try to find the snippet description field directly if available, or click the "Edit snippet" button.
    const editSnippetButton = 'button.yoast-snippet-editor-toggle'; // This is a guess, we might need to check the DOM
    
    // Alternative: Use the direct meta field if it exists in the admin page
    // Let's look for the description field in the Yoast SEO section.
    const descriptionSelector = 'textarea[id*="yoast-seo-description"]'; 
    
    // Since the exact ID can change, let's try to find a textarea within the Yoast SEO section
    const yoastSection = await page.locator('.yoast-seo-metabox');
    const descField = yoastSection.locator('textarea').first();
    
    if (await descField.isVisible()) {
        await descField.fill('Recover faster with expert sports physiotherapy in Milton. Specialised athletic rehabilitation for peak performance. Book at Tonic Physio.');
    } else {
        console.log('Description field not found directly, attempting a wider search...');
        await page.fill('textarea[name*="description"]', 'Recover faster with expert sports physiotherapy in Milton. Specialised athletic rehabilitation for peak performance. Book at Tonic Physio.');
    }

    await page.screenshot({ path: `${config.artifactsDir}/yoast-settings-applied.png`, fullPage: true });
    
    // 3. Save/Update the post
    console.log('Updating post...');
    const publishButton = 'input[id="publish"]'; // Standard WP publish button
    await page.click(publishButton);
    await page.waitForNavigation();
    
    console.log('Post updated successfully.');
    await page.screenshot({ path: `${config.artifactsDir}/post-updated.png`, fullPage: true });

  } catch (err) {
    console.error('Error during SEO polish:', err);
    await page.screenshot({ path: `${config.artifactsDir}/error-state.png`, fullPage: true });
  } finally {
    await context.close();
  }
})();
