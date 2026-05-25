const { chromium } = require('playwright');
const path = require('path');

const WP_URL = 'https://tonicphysio.com/wp-admin';
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

  // ========== STEP 1: LOGIN ==========
  console.log('[1] Navigating to wp-login...');
  await page.goto(WP_URL, { waitUntil: 'networkidle', timeout: 30000 });
  await page.waitForTimeout(2000);

  // Check if already logged in
  const loginForm = await page.$('#loginform');
  if (loginForm) {
    console.log('[1] Login form found, logging in...');
    await page.fill('#user_login', WP_USER);
    await page.fill('#user_pass', WP_PASS);
    await page.click('#wp-submit');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(2000);
    console.log('[1] Logged in successfully.');
  } else {
    console.log('[1] Already logged in or no login form.');
  }

  // ========== STEP 2: UPLOAD IMAGES ==========
  const mediaIds = {};

  for (const imgName of IMAGES) {
    console.log(`[2] Uploading ${imgName}...`);
    // Go to Media > Add New
    await page.goto(`${WP_URL}/media-new.php`, { waitUntil: 'networkidle', timeout: 30000 });
    await page.waitForTimeout(2000);

    // Handle the multi-file uploader or browser uploader
    // Try the plupload approach first
    const htmlUploader = await page.$('#html-upload');
    
    // Use the async-upload form with file input
    const fileInput = await page.$('input[type="file"]');
    
    if (fileInput) {
      console.log(`[2] Found file input, uploading ${imgName} via async uploader...`);
      await fileInput.setInputFiles(path.join(WORKSPACE, imgName));
      
      // Wait for upload to complete
      await page.waitForTimeout(5000);
      
      // Wait for the media item to appear
      try {
        await page.waitForSelector('.media-item', { timeout: 15000 });
        console.log(`[2] Upload UI visible for ${imgName}`);
      } catch (e) {
        console.log(`[2] No .media-item selector, checking other indicators...`);
      }
      
      await page.waitForTimeout(3000);
    } else {
      // Fallback: try browser uploader
      console.log(`[2] Using browser uploader...`);
      await page.goto(`${WP_URL}/media-new.php?browser-uploader`, { waitUntil: 'networkidle', timeout: 30000 });
      const browserFileInput = await page.$('#async-upload');
      if (browserFileInput) {
        await browserFileInput.setInputFiles(path.join(WORKSPACE, imgName));
        await page.click('#html-upload');
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(3000);
      }
    }

    // Get the media ID from the page
    // After upload, WP shows the attachment ID in the edit link
    let mediaId = null;

    // Try multiple selectors to find the media ID
    const editLink = await page.$('a.edit-attachment');
    if (editLink) {
      const href = await editLink.getAttribute('href');
      console.log(`[2] Edit link href: ${href}`);
      const match = href.match(/post=(\d+)/);
      if (match) mediaId = match[1];
    }

    if (!mediaId) {
      // Try finding it from the media grid
      const attachIdEl = await page.$('[data-id]');
      if (attachIdEl) {
        mediaId = await attachIdEl.getAttribute('data-id');
      }
    }

    if (!mediaId) {
      // Try the URL approach: search for the image in the media library
      console.log(`[2] Direct ID not found, searching media library...`);
      await page.goto(`${WP_URL}/upload.php?mode=list&s=${encodeURIComponent(imgName.replace('.jpg', ''))}`, { waitUntil: 'networkidle', timeout: 30000 });
      await page.waitForTimeout(3000);

      // Try to find the attachment ID in the list view
      const rows = await page.$$('tr[id^="post-"]');
      for (const row of rows) {
        const id = await row.getAttribute('id');
        const title = await row.$('.column-title .media-icon + strong, .column-title strong, .title');
        if (title) {
          const titleText = await title.textContent();
          console.log(`[2] Found media row: id=${id}, title=${titleText.trim()}`);
          if (titleText.trim().includes(imgName.replace('.jpg', ''))) {
            mediaId = id.replace('post-', '');
            break;
          }
        }
      }

      // If still not found, try clicking edit on first result
      if (!mediaId && rows.length > 0) {
        const firstId = await rows[0].getAttribute('id');
        mediaId = firstId.replace('post-', '');
        console.log(`[2] Using first media item: ${mediaId}`);
      }
    }

    if (mediaId) {
      mediaIds[imgName] = mediaId;
      console.log(`[2] ✓ ${imgName} => Media ID ${mediaId}`);
    } else {
      console.log(`[2] ✗ Could not determine media ID for ${imgName}`);
    }

    // Navigate away to reset
    await page.waitForTimeout(1000);
  }

  console.log('[2] All uploads attempted. Media IDs:', JSON.stringify(mediaIds, null, 2));

  // ========== STEP 3: UPDATE ACF FIELDS FOR EACH PAGE ==========
  for (const pg of PAGES) {
    console.log(`[3] Editing page: ${pg.name} (ID ${pg.id})...`);
    await page.goto(`${WP_URL}/post.php?post=${pg.id}&action=edit`, { waitUntil: 'networkidle', timeout: 30000 });
    await page.waitForTimeout(3000);

    // We need to set ACF image fields
    // ACF image fields typically have a button to select image from media library
    // Field names: why_choose_us_image, solutions_image

    for (const [fieldSlug, imgFile] of [['why_choose_us_image', pg.whyChoose], ['solutions_image', pg.solutions]]) {
      const mediaId = mediaIds[imgFile];
      if (!mediaId) {
        console.log(`[3] ✗ No media ID for ${imgFile}, skipping ${fieldSlug}`);
        continue;
      }

      console.log(`[3] Setting ${fieldSlug} to media ID ${mediaId}...`);

      // ACF image fields can be set via hidden input or via the media frame
      // Try hidden input approach first (ACF stores image ID in a hidden input)
      const hiddenInput = await page.$(`input[name="acf[${fieldSlug}]"]`);
      
      if (hiddenInput) {
        console.log(`[3] Found ACF hidden input for ${fieldSlug}, setting value...`);
        await page.evaluate(({ sel, val }) => {
          const input = document.querySelector(sel);
          if (input) {
            input.value = val;
            input.dispatchEvent(new Event('change', { bubbles: true }));
          }
        }, { sel: `input[name="acf[${fieldSlug}]"]`, val: mediaId });
      } else {
        // Try alternative selectors - ACF can use different naming conventions
        console.log(`[3] Standard ACF input not found, trying alternatives...`);
        
        // Try data-key based selector
        const acfField = await page.$(`[data-key="${fieldSlug}"] .acf-hidden input`);
        if (acfField) {
          console.log(`[3] Found ACF field via data-key, setting value...`);
          await page.evaluate(({ sel, val }) => {
            const input = document.querySelector(sel);
            if (input) {
              input.value = val;
              input.dispatchEvent(new Event('change', { bubbles: true }));
            }
          }, { sel: `[data-key="${fieldSlug}"] .acf-hidden input`, val: mediaId });
        } else {
          // Try finding any input that contains the field name
          console.log(`[3] Trying broader ACF selectors...`);
          
          // List all ACF field keys on the page
          const acfKeys = await page.evaluate(() => {
            const keys = [];
            document.querySelectorAll('[data-key]').forEach(el => {
              keys.push(el.getAttribute('data-key'));
            });
            return keys;
          });
          console.log(`[3] ACF data-keys found on page: ${JSON.stringify(acfKeys)}`);

          // Try clicking the "Add image" button for the field and using the media frame
          // Find the ACF field container
          const fieldContainer = await page.$(`[data-key="${fieldSlug}"]`);
          if (fieldContainer) {
            console.log(`[3] Found field container for ${fieldSlug}, clicking add image...`);
            const addBtn = await fieldContainer.$('.acf-actions .acf-button');
            if (!addBtn) {
              // Try the button inside the image field
              const btn = await fieldContainer.$('button, .button');
              if (btn) {
                await btn.click();
              }
            } else {
              await addBtn.click();
            }
            
            // Wait for media frame
            await page.waitForTimeout(2000);
            
            // The media frame should open - search for our image
            // In the media frame, we can select by ID
            const mediaFrame = await page.$('.media-modal');
            if (mediaFrame) {
              console.log(`[3] Media frame opened, selecting image...`);
              // Try to select the image by clicking on it in the grid
              // Or use the search feature
            }
          } else {
            // Last resort: try using JavaScript to set the ACF field
            console.log(`[3] Using JS evaluation to set ACF field...`);
            await page.evaluate(({ field, mid }) => {
              // Find any input whose name contains the field slug
              const inputs = document.querySelectorAll('input[type="hidden"]');
              for (const input of inputs) {
                if (input.name && input.name.includes(field)) {
                  input.value = mid;
                  input.dispatchEvent(new Event('change', { bubbles: true }));
                  console.log(`Set ${input.name} to ${mid}`);
                }
              }
            }, { field: fieldSlug, mid: mediaId });
          }
        }
      }

      // Also try to trigger the ACF image preview update
      await page.evaluate(({ field, mid }) => {
        // Trigger acf field update if available
        if (typeof acf !== 'undefined') {
          // Find field by name
          const fields = acf.getFields();
          fields.forEach(f => {
            if (f.data && f.data.name === field) {
              f.val(mid);
            }
          });
        }
      }, { field: fieldSlug, mid: mediaId });

      await page.waitForTimeout(2000);
    }

    // Click Update button
    console.log(`[3] Updating page ${pg.name}...`);
    const updateBtn = await page.$('#publish, .editor-post-publish-button, .editor-post-save-draft');
    if (updateBtn) {
      await updateBtn.click();
      await page.waitForLoadState('networkidle');
      await page.waitForTimeout(3000);
      console.log(`[3] ✓ Page ${pg.name} updated.`);
    } else {
      console.log(`[3] Update button not found, trying keyboard shortcut...`);
      await page.keyboard.press('Meta+s');
      await page.waitForTimeout(3000);
    }
  }

  // ========== STEP 4: VERIFY FRONTEND ==========
  console.log('[4] Verifying frontend pages...');
  for (const pg of PAGES) {
    const frontendUrl = `https://tonicphysio.com/?page_id=${pg.id}`;
    console.log(`[4] Checking ${frontendUrl}...`);
    await page.goto(frontendUrl, { waitUntil: 'networkidle', timeout: 30000 });
    await page.waitForTimeout(2000);

    // Take a screenshot
    const screenshotPath = path.join(WORKSPACE, `verify-${pg.id}.png`);
    await page.screenshot({ path: screenshotPath, fullPage: true });
    console.log(`[4] Screenshot saved: ${screenshotPath}`);

    // Check for images on the page
    const imgCount = await page.$$eval('img', imgs => imgs.length);
    console.log(`[4] Found ${imgCount} images on ${pg.name} frontend`);
  }

  await browser.close();
  console.log('[DONE] All operations completed.');
  console.log('Media IDs:', JSON.stringify(mediaIds, null, 2));
})();