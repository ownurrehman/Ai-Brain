const { chromium } = require('playwright');
const path = require('path');

// Use the working IP directly
const WP_IP = '145.79.24.231';
const WP_BASE = `https://${WP_IP}`;
const WP_USER = 'Dan';
const WP_PASS = 'RR#Tonic@2026';
const WP_DOMAIN = 'tonicphysio.com';
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
  console.log('=== TonicPhysio WordPress Upload (Direct IP) ===');
  
  const browser = await chromium.launch({
    headless: true,
    args: ['--ignore-certificate-errors', '--disable-web-security'],
  });

  const context = await browser.newContext({
    viewport: { width: 1440, height: 900 },
    userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
    ignoreHTTPSErrors: true,
    // Add extra headers to appear more like a real browser
    extraHTTPHeaders: {
      'Accept-Language': 'en-US,en;q=0.9',
      'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
    },
  });

  const page = await context.newPage();

  // ========== LOGIN ==========
  console.log('\n[1] Logging in...');
  
  // Navigate to login page using IP
  await page.goto(`${WP_BASE}/wp-login.php`, { waitUntil: 'domcontentloaded', timeout: 45000 });
  await page.waitForTimeout(3000);

  // Take screenshot to debug
  await page.screenshot({ path: path.join(WORKSPACE, 'debug-login.png') });

  // Check for login form
  const loginForm = await page.$('#loginform');
  if (!loginForm) {
    console.log('[1] No login form found');
    const url = page.url();
    console.log(`[1] Current URL: ${url}`);
    
    // Check if already logged in
    const adminBar = await page.$('#wpadminbar');
    if (adminBar) {
      console.log('[1] Already logged in!');
    }
  } else {
    console.log('[1] Entering credentials...');
    
    // Fill username
    await page.fill('#user_login', WP_USER);
    await page.waitForTimeout(500);
    
    // Fill password carefully
    await page.click('#user_pass');
    await page.waitForTimeout(200);
    await page.keyboard.type(WP_PASS, { delay: 100 });
    await page.waitForTimeout(500);
    
    console.log('[1] Submitting login...');
    await page.click('#wp-submit');
    
    // Wait for navigation
    try {
      await page.waitForNavigation({ waitUntil: 'domcontentloaded', timeout: 30000 });
    } catch (e) {
      console.log('[1] Navigation timeout, checking current state...');
    }
    await page.waitForTimeout(5000);
    
    // Debug screenshot
    await page.screenshot({ path: path.join(WORKSPACE, 'debug-after-login.png') });
    
    console.log(`[1] After login URL: ${page.url()}`);
  }

  // Check login status
  const adminBar = await page.$('#wpadminbar');
  const isLoggedIn = !!adminBar || page.url().includes('/wp-admin');
  console.log(`[1] Login status: ${isLoggedIn ? 'SUCCESS' : 'FAILED'}`);

  if (!isLoggedIn) {
    // Check for error
    const errorMsg = await page.$('#login_error');
    if (errorMsg) {
      const text = await errorMsg.textContent();
      console.log(`[1] Login error: ${text.trim()}`);
    }
  }

  // ========== UPLOAD IMAGES ==========
  const mediaIds = {};

  if (isLoggedIn) {
    for (const imgName of IMAGES) {
      console.log(`\n[2] Uploading ${imgName}...`);
      const filePath = path.join(WORKSPACE, imgName);

      try {
        // Go to Media > Add New with browser uploader
        await page.goto(`${WP_BASE}/wp-admin/media-new.php?browser-uploader`, {
          waitUntil: 'domcontentloaded',
          timeout: 30000
        });
        await page.waitForTimeout(3000);

        // Find file input
        const fileInput = await page.$('#async-upload');
        if (fileInput) {
          console.log('[2] Found file input');
          await fileInput.setInputFiles(filePath);
          await page.waitForTimeout(2000);

          // Click upload
          const uploadBtn = await page.$('#html-upload');
          if (uploadBtn) {
            await uploadBtn.click();
            console.log('[2] Upload clicked');
            await page.waitForLoadState('domcontentloaded');
            await page.waitForTimeout(8000);
          }
        } else {
          console.log('[2] No file input found');
          // Try plupload
          const [fileChooser] = await Promise.all([
            page.waitForEvent('filechooser', { timeout: 10000 }).catch(() => null),
            page.click('#plupload-upload-ui').catch(() => {})
          ]);
          if (fileChooser) {
            await fileChooser.setFiles(filePath);
            await page.waitForTimeout(8000);
          }
        }

        // Get media ID
        let mediaId = await page.evaluate(() => {
          const editLink = document.querySelector('a.edit-attachment');
          if (editLink) {
            const m = editLink.href.match(/post=(\d+)/);
            if (m) return m[1];
          }
          const items = document.querySelectorAll('.media-item');
          for (const item of items) {
            const m = item.id?.match(/(\d+)/);
            if (m) return m[1];
          }
          return null;
        });

        // Search in media library if not found
        if (!mediaId) {
          console.log('[2] Searching media library...');
          await page.goto(`${WP_BASE}/wp-admin/upload.php?mode=list&s=${encodeURIComponent(imgName.replace('.jpg', ''))}`, {
            waitUntil: 'domcontentloaded',
            timeout: 30000
          });
          await page.waitForTimeout(3000);

          mediaId = await page.evaluate(() => {
            const rows = document.querySelectorAll('tr[id^="post-"]');
            if (rows.length > 0) {
              return rows[0].id.replace('post-', '');
            }
            return null;
          });
        }

        if (mediaId) {
          mediaIds[imgName] = String(mediaId);
          console.log(`[2] ✓ ${imgName} => Media ID ${mediaId}`);
        } else {
          console.log(`[2] ✗ No media ID for ${imgName}`);
        }

      } catch (err) {
        console.log(`[2] Error: ${err.message}`);
      }

      await page.waitForTimeout(1000);
    }
  }

  console.log('\n[2] === Media IDs ===');
  console.log(JSON.stringify(mediaIds, null, 2));

  // Save media IDs
  const fs = require('fs');
  fs.writeFileSync('/tmp/wp-media-ids.json', JSON.stringify(mediaIds, null, 2));

  // ========== SET ACF FIELDS ==========
  if (isLoggedIn && Object.keys(mediaIds).length > 0) {
    for (const pg of PAGES) {
      console.log(`\n[3] Editing Page: ${pg.name} (ID ${pg.id})`);
      
      await page.goto(`${WP_BASE}/wp-admin/post.php?post=${pg.id}&action=edit`, {
        waitUntil: 'domcontentloaded',
        timeout: 30000
      });
      await page.waitForTimeout(5000);

      // Set ACF fields
      for (const [fieldName, imgFile] of [['why_choose_us_image', pg.whyChoose], ['solutions_image', pg.solutions]]) {
        const mid = mediaIds[imgFile];
        if (!mid) continue;

        console.log(`[3] Setting ${fieldName} = ${mid}`);
        
        await page.evaluate(({ field, mid }) => {
          // Try ACF API
          if (typeof acf !== 'undefined') {
            const fields = acf.getFields();
            for (const f of fields) {
              if (f.data?.name === field) {
                f.val(mid);
              }
            }
          }
          // Try hidden input
          document.querySelectorAll('input[type="hidden"]').forEach(inp => {
            if (inp.name && inp.name.includes(field)) {
              inp.value = String(mid);
              inp.dispatchEvent(new Event('change', { bubbles: true }));
            }
          });
        }, { field: fieldName, mid });

        await page.waitForTimeout(2000);
      }

      // Save page
      console.log('[3] Saving...');
      const saveBtn = await page.$('.editor-post-publish-button__button, #publish, #save-post');
      if (saveBtn) {
        await saveBtn.click();
        await page.waitForTimeout(5000);
      } else {
        await page.keyboard.press('Meta+s');
        await page.waitForTimeout(3000);
      }
      console.log(`[3] Page ${pg.name} saved`);
    }
  }

  // ========== VERIFY ==========
  console.log('\n[4] Verifying...');
  for (const pg of PAGES) {
    const url = `${WP_BASE}/?page_id=${pg.id}`;
    console.log(`[4] Checking ${url}...`);
    
    try {
      await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 });
      await page.waitForTimeout(3000);
      
      await page.screenshot({ path: path.join(WORKSPACE, `frontend-${pg.id}.png`), fullPage: true });
      const imgCount = await page.$$eval('img', imgs => imgs.length);
      console.log(`[4] ${pg.name}: ${imgCount} images`);
    } catch (e) {
      console.log(`[4] Error: ${e.message}`);
    }
  }

  await browser.close();

  console.log('\n=== COMPLETE ===');
  console.log('Media IDs:', JSON.stringify(mediaIds));
})();
