const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();

  try {
    console.log('Navigating to login page...');
    await page.goto('https://www.rankray.com/wp-login.php');
    
    console.log('Filling credentials...');
    await page.fill('#username', 'openclaw');
    await page.fill('#password', 'OpenClaw');
    await page.click('#wp-submit');
    await page.waitForNavigation();
    
    console.log('Navigating to post editor...');
    await page.goto('https://rankray.com/wp-admin/post.php?post=19919&action=edit');
    
    // Extract content
    // Depending on whether it's Gutenberg or Classic, the selector differs.
    // Try Gutenberg first.
    let content = '';
    const editorSelector = '.block-editor-rich-text__editable';
    try {
      await page.waitForSelector(editorSelector, { timeout: 5000 });
      content = await page.innerText(editorSelector);
    } catch (e) {
      console.log('Gutenberg editor not found, trying Classic editor...');
      const classicSelector = '#content';
      await page.waitForSelector(classicSelector, { timeout: 5000 });
      content = await page.inputValue(classicSelector);
    }
    
    console.log('Extracted content length:', content.length);
    console.log('CONTENT_START');
    console.log(content);
    console.log('CONTENT_END');
    
  } catch (error) {
    console.error('Error during execution:', error);
  } finally {
    await browser.close();
  }
})();
