const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

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

const PAGES = [
  { id: 1793, name: 'Pediatric Physiotherapy', whyChoose: 'pediatric-physiotherapy-why-choose.jpg', solutions: 'pediatric-physiotherapy-solutions.jpg' },
  { id: 1791, name: 'Orthopedic Physiotherapy', whyChoose: 'orthopedic-physiotherapy-why-choose.jpg', solutions: 'orthopedic-physiotherapy-solutions.jpg' },
];

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  const page = await context.newPage();

  // Collect console logs
  page.on('console', msg => {
    if (msg.type() === 'log' || msg.type() === 'error') {
      console.log(`[BROWSER ${msg.type()}] ${msg.text()}`);
    }
  });

  // ========== STEP 1: LOGIN ==========
  console.log('[1] Navigating to wp-login...');
  await page.goto(WP_ADMIN, { waitUntil: 'networkidle', timeout: 30000 });
  await page.waitForTimeout(2000);

  const loginForm = await page.$('#loginform');
  if (loginForm) {
    console.log('[1] Logging in...');
    await page.fill('#user_login', WP_USER);
    await page.fill('#user_pass', WP_PASS);
    await page.click('#wp-submit');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(3000);
    console.log('[1] Logged in. Current URL:', page.url());
  }

  // ========== STEP 2: UPLOAD IMAGES VIA WP REST API (using admin cookies) ==========
  // First get the nonce from the admin page
  console.log('[2] Getting WP REST nonce...');
  await page.goto(`${WP_ADMIN}/index.php`, { waitUntil: 'networkidle', timeout: 30000 });
  await page.waitForTimeout(2000);

  const wpNonce = await page.evaluate(() => {
    // Try multiple ways to get the REST nonce
    if (typeof wpApiSettings !== 'undefined' && wpApiSettings.nonce) return wpApiSettings.nonce;
    // Try from wp-admin heartbeat
    if (typeof wp !== 'undefined' && wp.heartbeat && wp.heartbeat.settings) return wp.heartbeat.settings.nonce;
    // Try meta tag
    const meta = document.querySelector('meta[name="wp-api-nonce"]');
    if (meta) return meta.content;
    return null;
  });

  console.log('[2] WP nonce:', wpNonce);

  // Upload via REST API
  const mediaIds = {};

  for (const imgName of IMAGES) {
    const filePath = path.join(WORKSPACE, imgName);
    console.log(`[2] Uploading ${imgName} via REST API...`);

    try {
      // Use the page context to make the API call with proper cookies
      const result = await page.evaluate(async ({ filename, filepath, nonce, baseUrl }) => {
        // We need to read the file - but we can't from inside evaluate
        // Instead, use fetch with FormData from the page context
        return { note: 'need to use route approach' };
      }, { filename: imgName, filepath: filePath, nonce: wpNonce, baseUrl: WP_BASE });

      // Alternative: Use the WP admin async-upload.php endpoint with proper multipart
      const uploadResult = await page.goto(`${WP_ADMIN}/media-new.php`, { waitUntil: 'networkidle', timeout: 30000 });
      await page.waitForTimeout(2000);

      // Check what upload mechanisms are available
      const pageContent = await page.content();

      // Look for the plupload container and its file input
      const fileInputInfo = await page.evaluate(() => {
        const inputs = document.querySelectorAll('input[type="file"]');
        const info = [];
        inputs.forEach(inp => {
          info.push({
            id: inp.id,
            name: inp.name,
            className: inp.className,
            parent: inp.parentElement ? inp.parentElement.className : '',
            accept: inp.accept,
          });
        });
        // Also check for the plupload container
        const plupload = document.querySelector('#plupload-upload-ui');
        const htmlUpload = document.querySelector('#html-upload-ui');
        return {
          inputs: info,
          hasPlupload: !!plupload,
          hasHtmlUpload: !!htmlUpload,
        };
      });

      console.log(`[2] File inputs on media-new: ${JSON.stringify(fileInputInfo)}`);

      // Try to find the hidden file input that plupload creates
      // Plupload creates a hidden input that we can use
      if (fileInputInfo.inputs.length > 0) {
        const fileInput = await page.$(`#${fileInputInfo.inputs[0].id}`);
        if (fileInput) {
          console.log(`[2] Setting files on input #${fileInputInfo.inputs[0].id}...`);
          await fileInput.setInputFiles(filePath);
          await page.waitForTimeout(2000);

          // Look for and click the upload button
          const uploadBtn = await page.$('#html-upload, .upload-button, .start-upload');
          if (uploadBtn) {
            await uploadBtn.click();
            console.log(`[2] Clicked upload button`);
          }

          await page.waitForTimeout(5000);
        }
      }

      // If plupload with no visible input, we need to use the approach of triggering file selection
      // The key is that plupload uses a transparent overlay on the drop zone
      // We can use page.setInputFiles on the dynamically created input

      // Let's try a completely different approach: use the async-upload.php endpoint directly
      console.log(`[2] Trying direct POST to async-upload.php...`);

      // First get _wpnonce from the media page
      const wpUploadNonce = await page.evaluate(() => {
        const nonceInput = document.querySelector('#_wpnonce');
        if (nonceInput) return nonceInput.value;
        // Try async-upload nonce
        const asyncNonce = document.querySelector('input[name="async-upload-nonce"]');
        if (asyncNonce) return asyncNonce.value;
        return null;
      });

      console.log(`[2] Upload nonce: ${wpUploadNonce}`);

      // Actually, let's use the browser's native file chooser approach
      // Go back to media-new page and handle the file chooser dialog
      await page.goto(`${WP_ADMIN}/media-new.php`, { waitUntil: 'networkidle', timeout: 30000 });
      await page.waitForTimeout(2000);

      // Switch to browser uploader if available (more reliable for automation)
      const browserUploaderLink = await page.$('a.browser-upload-link, a.switch-to-browser-uploader');
      if (browserUploaderLink) {
        console.log('[2] Switching to browser uploader...');
        await browserUploaderLink.click();
        await page.waitForTimeout(2000);
      }

      // Check for the async-upload file input
      const asyncUploadInput = await page.$('#async-upload');
      if (asyncUploadInput) {
        console.log(`[2] Found #async-upload input, uploading ${imgName}...`);
        await asyncUploadInput.setInputFiles(filePath);
        await page.waitForTimeout(1000);

        // Click the upload button
        const htmlUploadBtn = await page.$('#html-upload');
        if (htmlUploadBtn) {
          await htmlUploadBtn.click();
          console.log('[2] Clicked HTML upload button');
          await page.waitForLoadState('networkidle');
          await page.waitForTimeout(5000);
        }
      } else {
        // Use Plupload approach: listen for file chooser and click the drop zone
        console.log('[2] Trying Plupload drop zone click approach...');

        // Plupload creates an invisible file input overlay
        // We need to find it
        const pluploadInput = await page.$('.plupload input[type="file"], .moxie-shim input[type="file"], input[type="file"][multiple]');

        if (pluploadInput) {
          console.log('[2] Found plupload file input');
          await pluploadInput.setInputFiles(filePath);
          await page.waitForTimeout(8000); // Wait for upload processing
        } else {
          // The plupload file input might be deeply hidden
          // Use filechooser event approach
          const [fileChooser] = await Promise.all([
            page.waitForEvent('filechooser', { timeout: 5000 }).catch(() => null),
            page.click('#plupload-upload-ui, .drag-drop').catch(() => console.log('[2] Could not click drop zone'))
          ]);

          if (fileChooser) {
            console.log('[2] File chooser opened, setting files...');
            await fileChooser.setFiles(filePath);
            await page.waitForTimeout(8000);
          }
        }
      }

      // Now try to find the media ID
      await page.waitForTimeout(3000);

      // Check the current page for success indicators
      const pageText = await page.textContent('body');
      console.log(`[2] Page contains edit link: ${pageText.includes('edit-attachment')}`);

      // Try to get media ID from the upload response
      let mediaId = null;

      // Method 1: Check URL for post= parameter
      const currentUrl = page.url();
      const postMatch = currentUrl.match(/post=(\d+)/);
      if (postMatch) mediaId = postMatch[1];

      // Method 2: Check page content for attachment ID
      if (!mediaId) {
        mediaId = await page.evaluate(() => {
          // Look for the media ID in various places
          const editLink = document.querySelector('a.edit-attachment');
          if (editLink) {
            const href = editLink.getAttribute('href');
            const m = href.match(/post=(\d+)/);
            if (m) return m[1];
          }

          // Look in the media-item div attributes
          const mediaItem = document.querySelector('.media-item');
          if (mediaItem) {
            const id = mediaItem.getAttribute('id');
            if (id) {
              const m = id.match(/media-item-(\d+)/);
              if (m) return m[1];
            }
          }

          return null;
        });
      }

      // Method 3: Search the media library
      if (!mediaId) {
        console.log(`[2] Searching media library for ${imgName}...`);
        await page.goto(`${WP_ADMIN}/upload.php?mode=list`, { waitUntil: 'networkidle', timeout: 30000 });
        await page.waitForTimeout(3000);

        // Search for the image
        const searchInput = await page.$('#media-search-input, .search-box input[name="s"]');
        if (searchInput) {
          await searchInput.fill(imgName.replace('.jpg', ''));
          await page.keyboard.press('Enter');
          await page.waitForLoadState('networkidle');
          await page.waitForTimeout(2000);
        }

        // Find the media item in the list
        const rows = await page.$$('tr[id^="post-"], tr[id^="att-"]');
        console.log(`[2] Found ${rows.length} media rows`);
        for (const row of rows) {
          const rowId = await row.getAttribute('id');
          console.log(`[2] Row: ${rowId}`);
        }

        if (rows.length > 0) {
          mediaId = (await rows[0].getAttribute('id')).replace(/^(post|att)-/, '');
          console.log(`[2] Using first row media ID: ${mediaId}`);
        }
      }

      if (mediaId) {
        mediaIds[imgName] = mediaId;
        console.log(`[2] ✓ ${imgName} => Media ID ${mediaId}`);
      } else {
        console.log(`[2] ✗ Could not find media ID for ${imgName}`);
      }

    } catch (err) {
      console.log(`[2] Error uploading ${imgName}: ${err.message}`);
    }

    await page.waitForTimeout(1000);
  }

  console.log('[2] Media IDs collected:', JSON.stringify(mediaIds));

  // ========== STEP 3: USE WP REST API TO SET ACF FIELDS ==========
  // If we couldn't get media IDs from the browser, try the REST API
  if (Object.keys(mediaIds).length === 0) {
    console.log('[2] No media IDs from browser approach, trying REST API...');

    // Use WP REST API to upload files
    for (const imgName of IMAGES) {
      try {
        console.log(`[2] Uploading ${imgName} via REST API...`);

        // Navigate to a page to get fresh cookies and nonce
        await page.goto(`${WP_ADMIN}/index.php`, { waitUntil: 'networkidle', timeout: 30000 });
        await page.waitForTimeout(2000);

        const nonce = await page.evaluate(() => {
          if (typeof wpApiSettings !== 'undefined') return wpApiSettings.nonce;
          return null;
        });

        if (!nonce) {
          console.log('[2] No REST nonce available');
          continue;
        }

        // Use page.evaluate to upload via REST API
        const result = await page.evaluate(async ({ filename, nonce, baseUrl }) => {
          // We can't read local files from evaluate, need different approach
          return { error: 'cannot read local files from evaluate' };
        }, { filename: imgName, nonce, baseUrl: WP_BASE });

      } catch (err) {
        console.log(`[2] REST API upload error for ${imgName}: ${err.message}`);
      }
    }
  }

  // ========== STEP 4: SET ACF FIELDS ==========
  for (const pg of PAGES) {
    console.log(`[3] Editing page: ${pg.name} (ID ${pg.id})...`);
    await page.goto(`${WP_ADMIN}/post.php?post=${pg.id}&action=edit`, { waitUntil: 'networkidle', timeout: 30000 });
    await page.waitForTimeout(5000);

    // List all ACF fields on the page
    const acfInfo = await page.evaluate(() => {
      const fields = [];
      if (typeof acf !== 'undefined') {
        acf.getFields().forEach(f => {
          fields.push({
            key: f.key,
            name: f.data ? f.data.name : null,
            type: f.data ? f.data.type : null,
            val: f.val ? f.val() : null,
          });
        });
      }
      // Also check for ACF hidden inputs
      document.querySelectorAll('input[name*="acf["]').forEach(inp => {
        fields.push({ inputName: inp.name, inputValue: inp.value, type: 'hidden_input' });
      });
      return fields;
    });

    console.log(`[3] ACF fields found: ${JSON.stringify(acfInfo, null, 2)}`);

    for (const [fieldSlug, imgFile] of [['why_choose_us_image', pg.whyChoose], ['solutions_image', pg.solutions]]) {
      const mediaId = mediaIds[imgFile];
      if (!mediaId) {
        console.log(`[3] ✗ No media ID for ${imgFile}, skipping ${fieldSlug}`);
        continue;
      }

      console.log(`[3] Setting ${fieldSlug} = ${mediaId}...`);

      // Try ACF JS API
      const setResult = await page.evaluate(({ field, mid }) => {
        if (typeof acf !== 'undefined') {
          const fields = acf.getFields();
          for (const f of fields) {
            if (f.data && (f.data.name === field || f.data.name === field.replace(/_/g, '-'))) {
              f.val(mid);
              return { method: 'acf_api', fieldKey: f.key, success: true };
            }
          }
        }

        // Try hidden input approach
        const inputs = document.querySelectorAll(`input[name*="${field}"]`);
        for (const inp of inputs) {
          inp.value = mid;
          inp.dispatchEvent(new Event('change', { bubbles: true }));
          return { method: 'hidden_input', name: inp.name, success: true };
        }

        return { method: 'none', success: false };
      }, { field: fieldSlug, mid: mediaId });

      console.log(`[3] Set result: ${JSON.stringify(setResult)}`);
      await page.waitForTimeout(2000);
    }

    // Update the page - try multiple approaches
    console.log(`[3] Updating page ${pg.name}...`);

    // Gutenberg editor
    const gutenbergBtn = await page.$('.editor-post-publish-button, .editor-post-save-draft');
    if (gutenbergBtn) {
      console.log('[3] Using Gutenberg publish button...');
      await gutenbergBtn.click();
      await page.waitForTimeout(5000);

      // Handle the publish dialog if it appears
      const confirmBtn = await page.$('.editor-post-publish-panel__header button');
      if (confirmBtn) {
        await confirmBtn.click();
      }

      await page.waitForLoadState('networkidle');
      await page.waitForTimeout(3000);
    } else {
      // Classic editor
      const publishBtn = await page.$('#publish, #save-post');
      if (publishBtn) {
        console.log('[3] Using classic publish button...');
        await publishBtn.click();
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(3000);
      }
    }

    console.log(`[3] ✓ Page ${pg.name} update attempted.`);
  }

  // ========== STEP 5: VERIFY FRONTEND ==========
  console.log('[4] Verifying frontend...');
  const results = {};

  for (const pg of PAGES) {
    // Try both pretty permalink and query string
    const urls = [
      `${WP_BASE}/?page_id=${pg.id}`,
      `${WP_BASE}/?p=${pg.id}`,
    ];

    for (const url of urls) {
      try {
        console.log(`[4] Checking ${url}...`);
        await page.goto(url, { waitUntil: 'networkidle', timeout: 30000 });
        await page.waitForTimeout(2000);

        const screenshotPath = path.join(WORKSPACE, `verify-${pg.id}.png`);
        await page.screenshot({ path: screenshotPath, fullPage: true });

        const imgCount = await page.$$eval('img', imgs => imgs.length);
        const title = await page.title();
        console.log(`[4] Page title: ${title}, Images: ${imgCount}`);

        results[pg.id] = { url, images: imgCount, title };
        break; // Found a working URL
      } catch (e) {
        console.log(`[4] Error checking ${url}: ${e.message}`);
      }
    }
  }

  await browser.close();

  console.log('\n========== FINAL REPORT ==========');
  console.log('Media IDs:', JSON.stringify(mediaIds, null, 2));
  console.log('Page results:', JSON.stringify(results, null, 2));
  console.log('====================================');
})();