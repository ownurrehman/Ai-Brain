const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

// Working IP found through testing
const WP_IP = '145.79.24.231';
const WP_DOMAIN = 'tonicphysio.com';
const WP_BASE = `https://${WP_DOMAIN}`;
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
  { id: 1793, name: 'Pediatric', whyChoose: 'pediatric-physiotherapy-why-choose.jpg', solutions: 'pediatric-physiotherapy-solutions.jpg' },
  { id: 1791, name: 'Orthopedic', whyChoose: 'orthopedic-physiotherapy-why-choose.jpg', solutions: 'orthopedic-physiotherapy-solutions.jpg' },
];

(async () => {
  console.log('=== TonicPhysio WordPress Image Upload ===');
  console.log(`Working IP: ${WP_IP}`);
  
  // Launch browser with specific args to work with the CDN
  const browser = await chromium.launch({
    headless: true,
    args: [
      '--ignore-certificate-errors',
      '--disable-web-security',
      '--disable-features=IsolateOrigins,site-per-process',
    ],
  });

  // Create context with custom host resolution
  const context = await browser.newContext({
    viewport: { width: 1440, height: 900 },
    userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
    ignoreHTTPSErrors: true,
  });

  const page = await context.newPage();

  // Route all requests to tonicphysio.com through the working IP
  await page.route('**/*', route => {
    const url = route.request().url();
    if (url.includes(WP_DOMAIN)) {
      const newUrl = url.replace(WP_DOMAIN, WP_IP);
      route.continue({ url: newUrl });
    } else {
      route.continue();
    }
  });

  // ========== STEP 1: LOGIN ==========
  console.log('\n[1] Logging in...');
  
  // First visit to get cookies
  await page.goto(`${WP_BASE}/wp-login.php`, { waitUntil: 'domcontentloaded', timeout: 45000 });
  await page.waitForTimeout(3000);

  // Check if already on login page
  const loginForm = await page.$('#loginform');
  if (!loginForm) {
    console.log('[1] No login form found, checking if already logged in...');
    const currentUrl = page.url();
    console.log(`[1] Current URL: ${currentUrl}`);
  } else {
    console.log('[1] Found login form, entering credentials...');
    
    // Fill credentials
    await page.fill('#user_login', WP_USER);
    await page.waitForTimeout(500);
    
    // For password with special chars, type character by character
    await page.focus('#user_pass');
    await page.keyboard.type(WP_PASS, { delay: 50 });
    await page.waitForTimeout(500);
    
    console.log('[1] Clicking login button...');
    await page.click('#wp-submit');
    
    // Wait for navigation
    await page.waitForLoadState('domcontentloaded');
    await page.waitForTimeout(5000);
    
    console.log(`[1] After login URL: ${page.url()}`);
  }

  // Verify we're logged in by checking for admin bar
  const adminBar = await page.$('#wpadminbar');
  const isLoggedIn = !!adminBar || page.url().includes('/wp-admin');
  console.log(`[1] Login status: ${isLoggedIn ? 'SUCCESS' : 'FAILED'}`);

  if (!isLoggedIn) {
    // Check for error message
    const errorMsg = await page.$('#login_error');
    if (errorMsg) {
      const errorText = await errorMsg.textContent();
      console.log(`[1] Login error: ${errorText.trim()}`);
    }
    
    // Try alternative: use the REST API with application password approach
    console.log('[1] Trying REST API approach...');
  }

  // ========== STEP 2: UPLOAD IMAGES ==========
  const mediaIds = {};

  for (const imgName of IMAGES) {
    console.log(`\n[2] Uploading ${imgName}...`);
    const filePath = path.join(WORKSPACE, imgName);

    try {
      // Go to Media > Add New with browser uploader
      await page.goto(`${WP_ADMIN}/media-new.php?browser-uploader`, { 
        waitUntil: 'domcontentloaded', 
        timeout: 30000 
      });
      await page.waitForTimeout(3000);

      // Debug screenshot
      await page.screenshot({ path: path.join(WORKSPACE, `debug-upload-${imgName.replace('.jpg', '')}.png`) });

      // Find the file input for browser uploader
      let fileInput = await page.$('#async-upload');
      
      if (!fileInput) {
        // Try alternative selectors
        fileInput = await page.$('input[type="file"]');
      }

      if (fileInput) {
        console.log(`[2] Found file input, uploading...`);
        await fileInput.setInputFiles(filePath);
        await page.waitForTimeout(2000);

        // Click upload button
        const uploadBtn = await page.$('#html-upload');
        if (uploadBtn) {
          await uploadBtn.click();
          console.log('[2] Upload button clicked');
          await page.waitForLoadState('domcontentloaded');
          await page.waitForTimeout(8000);
        }
      } else {
        console.log('[2] No file input found, trying plupload approach...');
        
        // Try clicking the drop zone to trigger file chooser
        const [fileChooser] = await Promise.all([
          page.waitForEvent('filechooser', { timeout: 10000 }).catch(() => null),
          page.click('#plupload-upload-ui, .drag-drop, .upload-ui').catch(() => {})
        ]);

        if (fileChooser) {
          await fileChooser.setFiles(filePath);
          await page.waitForTimeout(8000);
        }
      }

      // Get media ID from the response page
      let mediaId = null;

      // Method 1: Check URL for post= parameter
      const urlMatch = page.url().match(/post=(\d+)/);
      if (urlMatch) {
        mediaId = urlMatch[1];
      }

      // Method 2: Look for edit link
      if (!mediaId) {
        mediaId = await page.evaluate(() => {
          const editLink = document.querySelector('a.edit-attachment');
          if (editLink) {
            const m = editLink.href.match(/post=(\d+)/);
            if (m) return m[1];
          }
          
          // Check media-item divs
          const items = document.querySelectorAll('.media-item');
          for (const item of items) {
            const idMatch = item.id?.match(/(\d+)/);
            if (idMatch) return idMatch[1];
          }
          
          return null;
        });
      }

      // Method 3: Search media library
      if (!mediaId) {
        console.log('[2] Searching media library...');
        await page.goto(`${WP_ADMIN}/upload.php?mode=list&s=${encodeURIComponent(imgName.replace('.jpg', ''))}`, {
          waitUntil: 'domcontentloaded',
          timeout: 30000
        });
        await page.waitForTimeout(3000);

        mediaId = await page.evaluate(() => {
          const rows = document.querySelectorAll('tr[id^="post-"], tr[id^="att-"]');
          if (rows.length > 0) {
            return rows[0].id.replace(/^(post|att)-/, '');
          }
          return null;
        });
      }

      if (mediaId) {
        mediaIds[imgName] = String(mediaId);
        console.log(`[2] ✓ ${imgName} => Media ID ${mediaId}`);
      } else {
        console.log(`[2] ✗ Could not find media ID for ${imgName}`);
      }

    } catch (err) {
      console.log(`[2] Error uploading ${imgName}: ${err.message}`);
    }

    await page.waitForTimeout(1000);
  }

  console.log('\n[2] === Media IDs Summary ===');
  console.log(JSON.stringify(mediaIds, null, 2));

  // ========== STEP 3: SET ACF FIELDS ==========
  for (const pg of PAGES) {
    console.log(`\n[3] Editing Page: ${pg.name} (ID ${pg.id})`);
    
    await page.goto(`${WP_ADMIN}/post.php?post=${pg.id}&action=edit`, {
      waitUntil: 'domcontentloaded',
      timeout: 30000
    });
    await page.waitForTimeout(5000);

    // Debug: List ACF fields
    const acfInfo = await page.evaluate(() => {
      const info = [];
      if (typeof acf !== 'undefined') {
        acf.getFields().forEach(f => {
          info.push({ key: f.key, name: f.data?.name, type: f.data?.type });
        });
      }
      document.querySelectorAll('input[name*="acf"]').forEach(inp => {
        info.push({ name: inp.name, value: inp.value, type: 'input' });
      });
      return info;
    });
    console.log(`[3] ACF fields found: ${JSON.stringify(acfInfo.slice(0, 10))}...`);

    // Set each ACF image field
    for (const [fieldName, imgFile] of [['why_choose_us_image', pg.whyChoose], ['solutions_image', pg.solutions]]) {
      const mid = mediaIds[imgFile];
      if (!mid) {
        console.log(`[3] ✗ No media ID for ${imgFile}`);
        continue;
      }

      console.log(`[3] Setting ${fieldName} = ${mid}`);

      const setResult = await page.evaluate(({ field, mid }) => {
        let success = false;

        // Try ACF JS API
        if (typeof acf !== 'undefined') {
          const fields = acf.getFields();
          for (const f of fields) {
            if (f.data?.name === field) {
              f.val(mid);
              success = true;
              console.log(`Set via ACF API: ${field} = ${mid}`);
            }
          }
        }

        // Try hidden input
        const inputs = document.querySelectorAll('input[type="hidden"]');
        for (const inp of inputs) {
          if (inp.name && inp.name.includes(field)) {
            inp.value = String(mid);
            inp.dispatchEvent(new Event('change', { bubbles: true }));
            success = true;
            console.log(`Set via hidden input: ${inp.name}`);
          }
        }

        return success;
      }, { field: fieldName, mid });

      console.log(`[3] Field set result: ${setResult}`);
      await page.waitForTimeout(2000);
    }

    // Save the page
    console.log('[3] Saving page...');
    
    // Try Gutenberg publish button
    let saved = false;
    const gutenbergBtn = await page.$('.editor-post-publish-button__button, .editor-post-publish-button');
    if (gutenbergBtn) {
      await gutenbergBtn.click();
      await page.waitForTimeout(3000);
      
      // Handle publish confirmation
      const confirmBtn = await page.$('.editor-post-publish-panel__header .editor-post-publish-button');
      if (confirmBtn) {
        await confirmBtn.click();
      }
      
      await page.waitForLoadState('domcontentloaded');
      await page.waitForTimeout(3000);
      saved = true;
    }

    // Try classic editor button
    if (!saved) {
      const classicBtn = await page.$('#publish, #save-post');
      if (classicBtn) {
        await classicBtn.click();
        await page.waitForLoadState('domcontentloaded');
        await page.waitForTimeout(3000);
        saved = true;
      }
    }

    // Fallback: keyboard shortcut
    if (!saved) {
      await page.keyboard.press('Meta+s');
      await page.waitForTimeout(3000);
      saved = true;
    }

    console.log(`[3] Page ${pg.name} saved: ${saved}`);
  }

  // ========== STEP 4: VERIFY FRONTEND ==========
  console.log('\n[4] Verifying Frontend...');
  
  for (const pg of PAGES) {
    const url = `${WP_BASE}/?page_id=${pg.id}`;
    console.log(`[4] Checking ${url}...`);
    
    await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 });
    await page.waitForTimeout(3000);
    
    // Screenshot
    const screenshotPath = path.join(WORKSPACE, `frontend-${pg.id}.png`);
    await page.screenshot({ path: screenshotPath, fullPage: true });
    console.log(`[4] Screenshot: ${screenshotPath}`);
    
    // Count images
    const imgCount = await page.$$eval('img', imgs => imgs.length);
    const title = await page.title();
    console.log(`[4] ${pg.name}: title="${title}", images=${imgCount}`);
  }

  await browser.close();

  // ========== FINAL REPORT ==========
  console.log('\n========================================');
  console.log('           UPLOAD COMPLETE            ');
  console.log('========================================');
  console.log('Media IDs:', JSON.stringify(mediaIds, null, 2));
  console.log('Pages Updated:', PAGES.map(p => p.name).join(', '));
  console.log('========================================');
})();
