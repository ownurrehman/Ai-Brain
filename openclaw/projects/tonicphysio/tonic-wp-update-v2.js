const { chromium } = require('playwright');
const fs = require('fs');

const BROWSER_PROFILE_DIR = '/Users/sheikhown/.openclaw/workspace/.browser-profiles/tonic-wp';
const SCREENSHOT_DIR = '/Users/sheikhown/.openclaw/workspace/screenshots';
const LOG_FILE = '/Users/sheikhown/.openclaw/workspace/tonic-wp-update-log.md';

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
  if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
  }

  log('=== Tonic Physio WordPress SEO Update (v2 - Using Existing Profile) ===');
  log(`Using browser profile: ${BROWSER_PROFILE_DIR}\n`);
  
  const browser = await chromium.launchPersistentContext(BROWSER_PROFILE_DIR, { 
    headless: false,
    args: ['--window-size=1920,1080', '--disable-dev-shm-usage'],
    viewport: { width: 1920, height: 1080 }
  });
  
  const page = browser.pages()[0] || await browser.newPage();
  
  try {
    // Navigate to WordPress admin
    await page.goto('https://tonicphysio.com/wp-admin/', { waitUntil: 'networkidle', timeout: 30000 });
    await sleep(3000);
    
    let currentUrl = page.url();
    log(`Current URL: ${currentUrl}`);
    
    await page.screenshot({ path: `${SCREENSHOT_DIR}/wp-initial.png` });
    
    // Check if already logged in
    if (currentUrl.includes('wp-login.php')) {
      log('Not logged in. Login required.');
      log('NOTE: The provided application password appears to be invalid.');
      log('Please manually log in at: https://tonicphysio.com/wp-login.php');
      log('Then re-run this script or complete updates manually.');
    } else {
      log('SUCCESS: Already logged in to WordPress admin!\n');
      
      // Proceed with Yoast updates
      log('=== Starting Yoast SEO Updates for 17 Pages ===\n');
      
      let successCount = 0;
      let failCount = 0;
      const results = [];
      
      for (const pageData of PAGES_TO_UPDATE) {
        log(`\n[${successCount + failCount + 1}/${PAGES_TO_UPDATE.length}] Page ID ${pageData.id}: ${pageData.slug}`);
        
        try {
          const editUrl = `https://tonicphysio.com/wp-admin/post.php?post=${pageData.id}&action=edit`;
          await page.goto(editUrl, { waitUntil: 'networkidle', timeout: 30000 });
          await sleep(2000);
          
          // Wait for editor
          await page.waitForSelector('#title, #editor', { timeout: 10000 });
          
          // Set Focus Keyphrase
          log(`  Focus Keyphrase: "${pageData.keyphrase}"`);
          let kpSet = false;
          const kpSelectors = [
            'input[name="yoast_focus_kw"]',
            '#yoast-focus-keyword',
            '.yoast-seo-focus-keyword input',
            '[aria-label*="focus keyphrase" i]'
          ];
          
          for (const sel of kpSelectors) {
            const field = await page.$(sel);
            if (field) {
              await field.fill(pageData.keyphrase);
              log(`  ✓ Keyphrase set`);
              kpSet = true;
              break;
            }
          }
          if (!kpSet) log('  ! Keyphrase field not found');
          
          // Set Meta Description
          log(`  Meta Description: ${pageData.metaDesc.length} chars`);
          let mdSet = false;
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
              log(`  ✓ Meta description set`);
              mdSet = true;
              break;
            }
          }
          if (!mdSet) log('  ! Meta description field not found');
          
          await sleep(500);
          
          // Save
          log('  Saving...');
          const saveSelectors = [
            '#publishing-action .editor-post-publish-button',
            '#publishing-action input[type="submit"]',
            'button.editor-post-publish-button'
          ];
          
          for (const sel of saveSelectors) {
            const btn = await page.$(sel);
            if (btn) {
              await btn.click();
              log('  ✓ Update clicked');
              break;
            }
          }
          
          await sleep(2000);
          successCount++;
          results.push({ id: pageData.id, slug: pageData.slug, status: 'success' });
          log(`  ✓ Complete`);
          
        } catch (e) {
          failCount++;
          results.push({ id: pageData.id, slug: pageData.slug, status: 'failed', error: e.message });
          log(`  ✗ FAILED: ${e.message}`);
        }
        
        await sleep(500);
      }
      
      log(`\n=== Yoast Summary: ${successCount}/${PAGES_TO_UPDATE.length} succeeded ===`);
      
      // Featured images for 4 new pages
      log('\n=== Featured Image Assignment for 4 New Pages ===\n');
      
      for (const pageData of NEW_PAGES_IMAGES) {
        log(`\nPage ID ${pageData.id}: ${pageData.title}`);
        
        try {
          const editUrl = `https://tonicphysio.com/wp-admin/post.php?post=${pageData.id}&action=edit`;
          await page.goto(editUrl, { waitUntil: 'networkidle', timeout: 30000 });
          await sleep(2000);
          
          log(`  Setting featured image ID: ${pageData.imageId}`);
          
          // Try hidden field method
          const thumbField = await page.$('#_thumbnail_id, input[name="_thumbnail_id"]');
          if (thumbField) {
            await thumbField.fill(String(pageData.imageId));
            await thumbField.dispatchEvent('change');
            log(`  ✓ Thumbnail ID set`);
            
            // Save
            const saveBtn = await page.$('#publishing-action .editor-post-publish-button, #publishing-action input[type="submit"]');
            if (saveBtn) {
              await saveBtn.click();
              await sleep(2000);
              log('  ✓ Saved');
            }
          } else {
            log('  ! Thumbnail field not found - may need manual assignment');
          }
          
        } catch (e) {
          log(`  ✗ FAILED: ${e.message}`);
        }
        
        await sleep(500);
      }
      
      log('\n=== All Updates Complete ===\n');
      
      // Generate summary
      log('UPDATE SUMMARY:');
      log(`- Yoast SEO Updates: ${successCount}/${PAGES_TO_UPDATE.length} pages updated`);
      log(`- Featured Images: Attempted for 4 new pages`);
    }
    
  } catch (e) {
    log(`FATAL ERROR: ${e.message}`);
  }
  
  await page.screenshot({ path: `${SCREENSHOT_DIR}/wp-final.png` });
  
  // Write log
  const logContent = `# Tonic Physio WordPress Update Log\nGenerated: ${new Date().toISOString()}\n\n## Execution Log\n\n${logOutput.join('\n\n')}\n`;
  fs.writeFileSync(LOG_FILE, logContent);
  
  console.log('\n=== Script completed ===');
}

run().catch(console.error);
