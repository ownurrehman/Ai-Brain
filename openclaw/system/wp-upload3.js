const { chromium } = require('playwright');
const path = require('path');

const WP_BASE = 'https://tonicphysio.com';
const WP_ADMIN = `${WP_BASE}/wp-admin`;
const WP_USER = 'Dan';
const WP_PASS = 'RR#Tonic@2026';
const WORKSPACE = '/Users/sheikhown/.openclaw/workspace';

const IMAGES = [
  'pediatric-physiotherapy-why-choose.jpg',
  'pediatric-physiotherapy-solutions.jpg',
  'orthopedic-physiotherapy-why-choose.jpg',
  'orthopedic-physiotherapy-solutions.jpg',
];

(async () => {
  const browser = await chromium.launch({
    headless: true,
    args: ['--ignore-certificate-errors', '--disable-web-security'],
  });
  const context = await browser.newContext({
    viewport: { width: 1440, height: 900 },
    userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
    ignoreHTTPSErrors: true,
  });
  const page = await context.newPage();

  // ========== LOGIN ==========
  console.log('[1] Navigating to wp-admin...');
  try {
    await page.goto(WP_ADMIN, { waitUntil: 'domcontentloaded', timeout: 45000 });
  } catch (e) {
    console.log('[1] First load error, retrying...');
    await page.waitForTimeout(5000);
    await page.goto(WP_ADMIN, { waitUntil: 'domcontentloaded', timeout: 45000 });
  }
  await page.waitForTimeout(3000);

  const loginForm = await page.$('#loginform');
  if (loginForm) {
    console.log('[1] Logging in...');
    await page.fill('#user_login', WP_USER);
    await page.fill('#user_pass', WP_PASS);
    await page.click('#wp-submit');
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(4000);
    console.log('[1] After login URL:', page.url());
  } else {
    console.log('[1] No login form, might already be logged in. URL:', page.url());
  }

  // Verify we're in admin
  const adminBar = await page.$('#wpadminbar, #adminmenu, .wp-admin');
  console.log('[1] Admin bar detected:', !!adminBar);

  if (!adminBar) {
    // Maybe redirected to login
    const currentUrl = page.url();
    console.log('[1] Current URL:', currentUrl);
    if (currentUrl.includes('wp-login')) {
      console.log('[1] Still on login, retrying...');
      await page.fill('#user_login', WP_USER);
      await page.fill('#user_pass', WP_PASS);
      await page.click('#wp-submit');
      await page.waitForLoadState('domcontentloaded');
      await page.waitForTimeout(4000);
    }
  }

  // ========== UPLOAD IMAGES ==========
  const mediaIds = {};

  for (const imgName of IMAGES) {
    console.log(`\n[2] === Uploading ${imgName} ===`);
    const filePath = path.join(WORKSPACE, imgName);

    try {
      // Go to Media > Add New (browser uploader for reliability)
      await page.goto(`${WP_ADMIN}/media-new.php?browser-uploader`, { waitUntil: 'domcontentloaded', timeout: 30000 });
      await page.waitForTimeout(3000);

      // Take a debug screenshot
      await page.screenshot({ path: path.join(WORKSPACE, `debug-media-${imgName}.png`) });

      // Find the file input
      const fileInput = await page.$('#async-upload');
      if (fileInput) {
        console.log(`[2] Found #async-upload input`);
        await fileInput.setInputFiles(filePath);
        await page.waitForTimeout(1000);

        // Click upload
        const uploadBtn = await page.$('#html-upload');
        if (uploadBtn) {
          console.log(`[2] Clicking HTML upload button...`);
          await uploadBtn.click();
          await page.waitForLoadState('domcontentloaded');
          await page.waitForTimeout(6000);
          console.log(`[2] After upload URL: ${page.url()}`);
        }
      } else {
        console.log(`[2] #async-upload not found, trying plupload...`);

        // Try the Plupload approach - wait for file chooser event
        const [fileChooser] = await Promise.all([
          page.waitForEvent('filechooser', { timeout: 10000 }).catch(() => null),
          page.click('#plupload-upload-ui, .drag-drop-area').catch(e => console.log(`[2] Click failed: ${e.message}`)),
        ]);

        if (fileChooser) {
          console.log(`[2] File chooser opened!`);
          await fileChooser.setFiles(filePath);
          await page.waitForTimeout(8000);
        } else {
          console.log(`[2] No file chooser event. Looking for hidden inputs...`);
          const hiddenInput = await page.$('input[type="file"][style*="opacity: 0"], input[type="file"][class*="plupload"]');
          if (hiddenInput) {
            await hiddenInput.setInputFiles(filePath);
            await page.waitForTimeout(8000);
          }
        }
      }

      // Try to get media ID
      await page.waitForTimeout(3000);
      let mediaId = null;

      // Method: Parse the page for attachment ID
      mediaId = await page.evaluate(() => {
        // Check for edit link
        const editLink = document.querySelector('a.edit-attachment');
        if (editLink) {
          const m = editLink.href.match(/post=(\d+)/);
          if (m) return m[1];
        }
        // Check for media-item with ID
        const items = document.querySelectorAll('.media-item');
        for (const item of items) {
          const id = item.id;
          const m = id.match(/(\d+)/);
          if (m) return m[1];
        }
        // Check for any link with item_id
        const links = document.querySelectorAll('a[href*="post="]');
        for (const link of links) {
          const m = link.href.match(/post=(\d+)/);
          if (m) return m[1];
        }
        return null;
      });

      if (!mediaId) {
        // Search in media library
        console.log(`[2] Media ID not found on upload page, searching library...`);
        await page.goto(`${WP_ADMIN}/upload.php?mode=list&s=${encodeURIComponent(imgName.replace('.jpg', ''))}`, { waitUntil: 'domcontentloaded', timeout: 30000 });
        await page.waitForTimeout(3000);

        mediaId = await page.evaluate(() => {
          const rows = document.querySelectorAll('tr[id^="post-"], tr[id^="att-"]');
          for (const row of rows) {
            const id = row.id.replace(/^(post|att)-/, '');
            return id; // Return first match
          }
          return null;
        });
      }

      if (mediaId) {
        mediaIds[imgName] = String(mediaId);
        console.log(`[2] ✓ ${imgName} => Media ID ${mediaId}`);
      } else {
        console.log(`[2] ✗ No media ID found for ${imgName}`);
      }

    } catch (err) {
      console.log(`[2] Error: ${err.message}`);
    }
  }

  console.log('\n[2] === Media IDs Summary ===');
  console.log(JSON.stringify(mediaIds, null, 2));

  // ========== SET ACF FIELDS ==========
  for (const pg of [
    { id: 1793, name: 'Pediatric', whyChoose: 'pediatric-physiotherapy-why-choose.jpg', solutions: 'pediatric-physiotherapy-solutions.jpg' },
    { id: 1791, name: 'Orthopedic', whyChoose: 'orthopedic-physiotherapy-why-choose.jpg', solutions: 'orthopedic-physiotherapy-solutions.jpg' },
  ]) {
    console.log(`\n[3] === Editing Page ${pg.name} (ID ${pg.id}) ===`);
    await page.goto(`${WP_ADMIN}/post.php?post=${pg.id}&action=edit`, { waitUntil: 'domcontentloaded', timeout: 30000 });
    await page.waitForTimeout(5000);

    // Discover ACF fields
    const acfFields = await page.evaluate(() => {
      const result = [];
      // ACF JS API
      if (typeof acf !== 'undefined') {
        acf.getFields().forEach(f => {
          result.push({ key: f.key, name: f.data?.name, type: f.data?.type, val: f.val() });
        });
      }
      // Hidden inputs
      document.querySelectorAll('input[name*="acf"]').forEach(inp => {
        result.push({ name: inp.name, value: inp.value, type: 'input' });
      });
      return result;
    });
    console.log(`[3] ACF fields: ${JSON.stringify(acfFields)}`);

    // Set ACF image fields
    for (const [fieldName, imgFile] of [['why_choose_us_image', pg.whyChoose], ['solutions_image', pg.solutions]]) {
      const mid = mediaIds[imgFile];
      if (!mid) {
        console.log(`[3] ✗ No media ID for ${imgFile}`);
        continue;
      }
      console.log(`[3] Setting ${fieldName} = ${mid}`);

      const setResult = await page.evaluate(({ field, mid }) => {
        let set = false;
        // ACF JS API
        if (typeof acf !== 'undefined') {
          acf.getFields().forEach(f => {
            if (f.data?.name === field) {
              f.val(mid);
              set = true;
            }
          });
        }
        // Hidden input fallback
        document.querySelectorAll('input[type="hidden"]').forEach(inp => {
          if (inp.name && inp.name.includes(field)) {
            inp.value = String(mid);
            inp.dispatchEvent(new Event('change', { bubbles: true }));
            set = true;
          }
        });
        return set;
      }, { field: fieldName, mid });
      console.log(`[3] Field set: ${setResult}`);
      await page.waitForTimeout(2000);
    }

    // Save/Update page
    console.log(`[3] Saving page...`);
    // Try Gutenberg
    let saved = false;
    const publishBtn = await page.$('.editor-post-publish-button__button');
    if (publishBtn) {
      await publishBtn.click();
      await page.waitForTimeout(3000);
      // Confirm publish if dialog
      const confirm = await page.$('.editor-post-publish-panel__header .editor-post-publish-button');
      if (confirm) await confirm.click();
      await page.waitForLoadState('domcontentloaded');
      await page.waitForTimeout(3000);
      saved = true;
    }

    if (!saved) {
      const classicBtn = await page.$('#publish, #save-post');
      if (classicBtn) {
        await classicBtn.click();
        await page.waitForLoadState('domcontentloaded');
        await page.waitForTimeout(3000);
        saved = true;
      }
    }

    console.log(`[3] Page ${pg.name} saved: ${saved}`);
  }

  // ========== VERIFY FRONTEND ==========
  console.log('\n[4] === Verifying Frontend ===');
  for (const pg of [{ id: 1793, name: 'Pediatric' }, { id: 1791, name: 'Orthopedic' }]) {
    const url = `${WP_BASE}/?page_id=${pg.id}`;
    console.log(`[4] Checking ${url}...`);
    await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 });
    await page.waitForTimeout(2000);
    await page.screenshot({ path: path.join(WORKSPACE, `frontend-${pg.id}.png`), fullPage: true });
    const imgCount = await page.$$eval('img', imgs => imgs.length);
    const title = await page.title();
    console.log(`[4] ${pg.name}: title="${title}", images=${imgCount}`);
  }

  await browser.close();
  console.log('\n========== COMPLETE ==========');
  console.log('Media IDs:', JSON.stringify(mediaIds));
  console.log('================================');
})();