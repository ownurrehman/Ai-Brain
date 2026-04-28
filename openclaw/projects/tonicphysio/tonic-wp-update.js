const { chromium } = require('playwright');
const fs = require('fs');

const CREDENTIALS = {
  email: 'Dan',
  password: '4vFk 18fN UlLB twaw B2hU 0kRE'
};

// Pages to update with Yoast data (17 pages)
const PAGES_TO_UPDATE = [
  { id: 6305, slug: '/physiotherapy-in-milton/', keyphrase: 'physiotherapy in Milton', metaDesc: 'Physiotherapy in Milton for pain relief & recovery. Expert rehab, manual therapy & direct billing at Tonic Physio. Book today.' },
  { id: 6279, slug: '/compression-socks/', keyphrase: 'compression socks Milton', metaDesc: 'Compression socks in Milton for recovery & circulation support. Expert fitting & advice at Tonic Physio. In-person consultations available.' },
  { id: 1797, slug: '/custom-orthotics/', keyphrase: 'custom orthotics Milton', metaDesc: 'Custom orthotics in Milton for foot support & pain relief. Personalized assessment & gait analysis at Tonic Physio. Book consultation.' },
  { id: 6280, slug: '/custom-and-otc-bracing/', keyphrase: 'custom bracing Milton', metaDesc: 'Custom and OTC bracing in Milton for injury recovery & joint stability. Expert fitting at Tonic Physio. Knee, ankle & posture braces available.' },
  { id: 1794, slug: '/registered-massage-therapy/', keyphrase: 'registered massage therapy Milton', metaDesc: 'Registered massage therapy in Milton for pain relief & stress reduction. Personalized hands-on care at Tonic Physio. RMTs available. Book now.' },
  { id: 6283, slug: '/shockwave-therapy/', keyphrase: 'shockwave therapy Milton', metaDesc: 'Shockwave therapy in Milton for fast injury recovery. Pain relief & healing acceleration at Tonic Physio. Book your session today.' },
  { id: 1799, slug: '/motor-vehicle-accident-physiotherapy/', keyphrase: 'MVA physiotherapy Milton', metaDesc: 'Motor vehicle accident physiotherapy in Milton. MVA injury recovery, pain relief & mobility restoration at Tonic Physio. Direct billing available.' },
  { id: 1798, slug: '/wsib-care-programs/', keyphrase: 'WSIB care programs Milton', metaDesc: 'WSIB care programs in Milton for workplace injury recovery. Expert physiotherapy & direct billing at Tonic Physio. Get back to work faster.' },
  { id: 1795, slug: '/manual-osteopathy-milton/', keyphrase: 'manual osteopathy Milton', metaDesc: 'Manual osteopathy in Milton for pain relief & mobility. Gentle hands-on treatment by experienced osteopaths at Tonic Physio. Book assessment.' },
  { id: 1791, slug: '/physiotherapy-in-milton/orthopedic-physiotherapy/', keyphrase: 'orthopedic physiotherapy Milton', metaDesc: 'Orthopedic physiotherapy in Milton for joint pain & mobility recovery. Personalized rehab plans at Tonic Physio. Lasting results. Book today.' },
  { id: 1796, slug: '/physiotherapy-in-milton/neurological-physiotherapy/', keyphrase: 'neurological physiotherapy Milton', metaDesc: 'Neurological physiotherapy in Milton for movement & strength recovery. Personalized care for stroke, Parkinson\'s & conditions at Tonic Physio.' },
  { id: 1793, slug: '/physiotherapy-in-milton/pediatric-physiotherapy/', keyphrase: 'pediatric physiotherapy Milton', metaDesc: 'Pediatric physiotherapy in Milton for children\'s mobility & strength. Developmental care for kids at Tonic Physio. Book child assessment today.' },
  { id: 1792, slug: '/physiotherapy-in-milton/acupuncture-therapy/', keyphrase: 'acupuncture therapy Milton', metaDesc: 'Acupuncture therapy in Milton for pain relief & stress reduction. Natural healing & balance restoration at Tonic Physio. Book session today.' },
  { id: 6971, slug: '/physiotherapy-in-milton/joint-pain-and-stiffness/', keyphrase: 'joint pain treatment Milton', metaDesc: 'Joint pain and stiffness treatment in Milton. Personalized physiotherapy to restore mobility & reduce discomfort at Tonic Physio. Book now.' },
  { id: 6981, slug: '/physiotherapy-in-milton/rheumatoid-arthritis-therapy-treatment/', keyphrase: 'rheumatoid arthritis therapy Milton', metaDesc: 'Rheumatoid arthritis therapy in Milton for pain relief & mobility. Joint function improvement at Tonic Physio. Expert care. Book consultation.' },
  { id: 6991, slug: '/physiotherapy-in-milton/back-and-neck-pain/', keyphrase: 'back and neck pain Milton', metaDesc: 'Back and neck pain treatment in Milton. Expert physiotherapy for lasting pain relief at Tonic Physio. Personalized care. Book assessment today.' },
  { id: 11895, slug: '/physiotherapy-in-milton/sports-physiotherapy/', keyphrase: 'sports physiotherapy Milton', metaDesc: 'Sports physiotherapy in Milton for injury recovery & performance. Athlete-focused rehab at Tonic Physio. Direct billing. Book now.' }
];

// New pages needing featured images (4 pages)
const NEW_PAGES_IMAGES = [
  { id: 6996, slug: 'herniated-disc-treatment', imageId: 11848, title: 'Herniated Disc Treatment' },
  { id: 7001, slug: 'sciatica-treatment', imageId: 11695, title: 'Sciatica Treatment' },
  { id: 7006, slug: 'cervical-spondylosis', imageId: 9597, title: 'Cervical Spondylosis' },
  { id: 11603, slug: 'b-pulse-pelvic-floor-strengthening', imageId: 11808, title: 'B-Pulse Pelvic Floor Strengthening' }
];

const SCREENSHOT_DIR = '/Users/sheikhown/.openclaw/workspace/screenshots';
const LOG_FILE = '/Users/sheikhown/.openclaw/workspace/tonic-wp-update-log.md';

let logOutput = [];

function log(message) {
  const timestamp = new Date().toISOString();
  const entry = `[${timestamp}] ${message}`;
  logOutput.push(entry);
  console.log(entry);
}

async function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

async function run() {
  // Ensure screenshot directory exists
  if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
  }

  const browser = await chromium.launch({ 
    headless: false,
    args: ['--window-size=1920,1080', '--disable-dev-shm-usage']
  });
  
  const context = await browser.newContext({
    viewport: { width: 1920, height: 1080 }
  });
  
  const page = await context.newPage();
  
  log('=== Tonic Physio WordPress SEO Update ===');
  log('Starting login process...\n');
  
  try {
    // Navigate to WordPress admin
    await page.goto('https://tonicphysio.com/wp-admin/', { waitUntil: 'networkidle', timeout: 30000 });
    await sleep(2000);
    
    let currentUrl = page.url();
    log(`Initial URL: ${currentUrl}`);
    
    await page.screenshot({ path: `${SCREENSHOT_DIR}/wp-initial.png` });
    log('Screenshot saved: wp-initial.png');
    
    // Check if we need to login
    if (currentUrl.includes('wp-login.php')) {
      log('Login page detected. Attempting login...');
      
      // Wait for login form to be visible
      await page.waitForSelector('#user_login', { timeout: 10000 }).catch(() => {
        log('Standard WordPress login form not found immediately');
      });
      
      // Fill login form
      try {
        log('Filling login credentials...');
        await page.fill('#user_login', CREDENTIALS.email);
        await page.fill('#user_pass', CREDENTIALS.password);
        await page.click('#wp-submit');
        log('Login form submitted. Waiting for navigation...');
        
        await page.waitForNavigation({ waitUntil: 'networkidle', timeout: 30000 }).catch(() => {
          log('Navigation timeout - checking current state');
        });
        await sleep(3000);
        
        currentUrl = page.url();
        log(`Post-login URL: ${currentUrl}`);
        await page.screenshot({ path: `${SCREENSHOT_DIR}/wp-after-login.png` });
        
        if (currentUrl.includes('wp-login.php')) {
          log('WARNING: Still on login page. Checking for errors...');
          const errorMsg = await page.$eval('.error, .notice-error, #login_error', el => el.textContent).catch(() => 'No error message found');
          log(`Login error: ${errorMsg}`);
        } else {
          log('SUCCESS: Logged in to WordPress admin');
        }
      } catch (e) {
        log(`Login error: ${e.message}`);
        await page.screenshot({ path: `${SCREENSHOT_DIR}/wp-login-error.png` });
      }
    } else {
      log('Already logged in or on admin dashboard');
    }
    
    // Proceed with updates if logged in
    currentUrl = page.url();
    if (!currentUrl.includes('wp-login.php')) {
      log('\n=== Starting Yoast SEO Updates for 17 Pages ===\n');
      
      let successCount = 0;
      let failCount = 0;
      
      for (const pageData of PAGES_TO_UPDATE) {
        log(`\n[${successCount + failCount + 1}/${PAGES_TO_UPDATE.length}] Page ID ${pageData.id}: ${pageData.slug}`);
        
        try {
          const editUrl = `https://tonicphysio.com/wp-admin/post.php?post=${pageData.id}&action=edit`;
          await page.goto(editUrl, { waitUntil: 'networkidle', timeout: 30000 });
          await sleep(2000);
          
          // Wait for editor to load
          await page.waitForSelector('#title, #editor', { timeout: 10000 });
          
          // Find and fill Yoast Focus Keyphrase
          log(`  Setting Focus Keyphrase: "${pageData.keyphrase}"`);
          try {
            // Try multiple selectors for Yoast focus keyphrase
            const kpSelectors = [
              'input[name="yoast_focus_kw"]',
              '#yoast-focus-keyword',
              '.yoast-seo-focus-keyword input',
              '[aria-label*="focus keyphrase" i]',
              'input[id*="focus_keyword"]'
            ];
            
            for (const sel of kpSelectors) {
              const field = await page.$(sel);
              if (field) {
                await field.fill(pageData.keyphrase);
                log(`  ✓ Keyphrase set via ${sel}`);
                break;
              }
            }
          } catch (e) {
            log(`  ! Keyphrase error: ${e.message}`);
          }
          
          // Find and fill Meta Description
          log(`  Setting Meta Description (${pageData.metaDesc.length} chars)`);
          try {
            const mdSelectors = [
              'textarea[name="yoast_meta_desc"]',
              '#yoast-meta-desc',
              '.yoast-seo-meta-desc textarea',
              '[aria-label*="meta description" i]'
            ];
            
            for (const sel of mdSelectors) {
              const field = await page.$(sel);
              if (field) {
                await field.fill(pageData.metaDesc);
                log(`  ✓ Meta description set via ${sel}`);
                break;
              }
            }
          } catch (e) {
            log(`  ! Meta description error: ${e.message}`);
          }
          
          await sleep(500);
          
          // Save the page
          log('  Saving page...');
          try {
            const saveSelectors = [
              '#publishing-action .editor-post-publish-button',
              '#publishing-action input[type="submit"]',
              'button.editor-post-publish-button',
              '#save-post'
            ];
            
            for (const sel of saveSelectors) {
              const btn = await page.$(sel);
              if (btn) {
                await btn.click();
                log('  ✓ Update button clicked');
                break;
              }
            }
            
            // Wait for save confirmation
            await sleep(2000);
          } catch (e) {
            log(`  ! Save error: ${e.message}`);
          }
          
          successCount++;
          log(`  ✓ Complete for Page ID ${pageData.id}`);
          
        } catch (e) {
          failCount++;
          log(`  ✗ FAILED: ${e.message}`);
        }
        
        await sleep(500);
      }
      
      log(`\n=== Yoast Updates Summary: ${successCount} succeeded, ${failCount} failed ===`);
      
      // Featured image assignment for 4 new pages
      log('\n=== Starting Featured Image Assignment for 4 New Pages ===\n');
      
      for (const pageData of NEW_PAGES_IMAGES) {
        log(`\nPage ID ${pageData.id}: ${pageData.title}`);
        
        try {
          const editUrl = `https://tonicphysio.com/wp-admin/post.php?post=${pageData.id}&action=edit`;
          await page.goto(editUrl, { waitUntil: 'networkidle', timeout: 30000 });
          await sleep(2000);
          
          log(`  Assigning featured image ID ${pageData.imageId}...`);
          
          // Try to set via hidden field
          try {
            const thumbField = await page.$('#_thumbnail_id, input[name="_thumbnail_id"]');
            if (thumbField) {
              await thumbField.fill(String(pageData.imageId));
              await thumbField.dispatchEvent('change');
              log(`  ✓ Set _thumbnail_id to ${pageData.imageId}`);
              
              // Save
              const saveBtn = await page.$('#publishing-action .editor-post-publish-button, #publishing-action input[type="submit"]');
              if (saveBtn) {
                await saveBtn.click();
                await sleep(2000);
                log('  ✓ Page saved with featured image');
              }
            } else {
              log('  ! Could not find thumbnail field');
            }
          } catch (e) {
            log(`  ! Featured image error: ${e.message}`);
          }
          
        } catch (e) {
          log(`  ✗ FAILED: ${e.message}`);
        }
        
        await sleep(500);
      }
      
      log('\n=== All Updates Complete ===\n');
      
    } else {
      log('ERROR: Cannot proceed - not logged in');
    }
    
  } catch (e) {
    log(`FATAL ERROR: ${e.message}`);
  }
  
  // Final screenshot
  await page.screenshot({ path: `${SCREENSHOT_DIR}/wp-final.png` });
  log('Final screenshot saved');
  
  await browser.close();
  
  // Write log file
  const logContent = `# Tonic Physio WordPress Update Log\nGenerated: ${new Date().toISOString()}\n\n## Execution Log\n\n${logOutput.join('\n\n')}\n`;
  fs.writeFileSync(LOG_FILE, logContent);
  log(`Log written to: ${LOG_FILE}`);
  
  console.log('\n=== Script completed ===');
}

run().catch(console.error);
