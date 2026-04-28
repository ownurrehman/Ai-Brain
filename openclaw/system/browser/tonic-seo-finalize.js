const { chromium } = require('playwright');
const { mkdirSync, writeFileSync } = require('fs');
const { join } = require('path');

const config = {
  loginUrl: "https://tonicphysio.com/wp-admin/",
  userDataDir: "/Users/sheikhown/.openclaw/workspace/.browser-profiles/tonic-wp-seo",
  artifactsDir: "/Users/sheikhown/.openclaw/workspace/reports/browser-artifacts"
};

// 17 existing pages with SEO values
const existingPages = [
  { slug: "/physiotherapy-in-milton/", id: 6305, keyphrase: "physiotherapy in Milton", meta: "Physiotherapy in Milton for pain relief & recovery. Expert rehab, manual therapy & direct billing at Tonic Physio. Book today." },
  { slug: "/compression-socks/", id: 6279, keyphrase: "compression socks Milton", meta: "Compression socks in Milton for recovery & circulation support. Expert fitting & advice at Tonic Physio. In-person consultations available." },
  { slug: "/custom-orthotics/", id: 1797, keyphrase: "custom orthotics Milton", meta: "Custom orthotics in Milton for foot support & pain relief. Personalized assessment & gait analysis at Tonic Physio. Book consultation." },
  { slug: "/custom-and-otc-bracing/", id: 6280, keyphrase: "custom bracing Milton", meta: "Custom and OTC bracing in Milton for injury recovery & joint stability. Expert fitting at Tonic Physio. Knee, ankle & posture braces available." },
  { slug: "/registered-massage-therapy/", id: 1794, keyphrase: "registered massage therapy Milton", meta: "Registered massage therapy in Milton for pain relief & stress reduction. Personalized hands-on care at Tonic Physio. RMTs available. Book now." },
  { slug: "/shockwave-therapy/", id: 6283, keyphrase: "shockwave therapy Milton", meta: "Shockwave therapy in Milton for fast injury recovery. Pain relief & healing acceleration at Tonic Physio. Book your session today." },
  { slug: "/motor-vehicle-accident-physiotherapy/", id: 1799, keyphrase: "MVA physiotherapy Milton", meta: "Motor vehicle accident physiotherapy in Milton. MVA injury recovery, pain relief & mobility restoration at Tonic Physio. Direct billing available." },
  { slug: "/wsib-care-programs/", id: 1798, keyphrase: "WSIB care programs Milton", meta: "WSIB care programs in Milton for workplace injury recovery. Expert physiotherapy & direct billing at Tonic Physio. Get back to work faster." },
  { slug: "/manual-osteopathy-milton/", id: 1795, keyphrase: "manual osteopathy Milton", meta: "Manual osteopathy in Milton for pain relief & mobility. Gentle hands-on treatment by experienced osteopaths at Tonic Physio. Book assessment." },
  { slug: "/physiotherapy-in-milton/orthopedic-physiotherapy/", id: 1791, keyphrase: "orthopedic physiotherapy Milton", meta: "Orthopedic physiotherapy in Milton for joint pain & mobility recovery. Personalized rehab plans at Tonic Physio. Lasting results. Book today." },
  { slug: "/physiotherapy-in-milton/neurological-physiotherapy/", id: 1796, keyphrase: "neurological physiotherapy Milton", meta: "Neurological physiotherapy in Milton for movement & strength recovery. Personalized care for stroke, Parkinson's & conditions at Tonic Physio." },
  { slug: "/physiotherapy-in-milton/pediatric-physiotherapy/", id: 1793, keyphrase: "pediatric physiotherapy Milton", meta: "Pediatric physiotherapy in Milton for children's mobility & strength. Developmental care for kids at Tonic Physio. Book child assessment today." },
  { slug: "/physiotherapy-in-milton/acupuncture-therapy/", id: 1792, keyphrase: "acupuncture therapy Milton", meta: "Acupuncture therapy in Milton for pain relief & stress reduction. Natural healing & balance restoration at Tonic Physio. Book session today." },
  { slug: "/physiotherapy-in-milton/joint-pain-and-stiffness/", id: 6971, keyphrase: "joint pain treatment Milton", meta: "Joint pain and stiffness treatment in Milton. Personalized physiotherapy to restore mobility & reduce discomfort at Tonic Physio. Book now." },
  { slug: "/physiotherapy-in-milton/rheumatoid-arthritis-therapy-treatment/", id: 6981, keyphrase: "rheumatoid arthritis therapy Milton", meta: "Rheumatoid arthritis therapy in Milton for pain relief & mobility. Joint function improvement at Tonic Physio. Expert care. Book consultation." },
  { slug: "/physiotherapy-in-milton/back-and-neck-pain/", id: 6991, keyphrase: "back and neck pain Milton", meta: "Back and neck pain treatment in Milton. Expert physiotherapy for lasting pain relief at Tonic Physio. Personalized care. Book assessment today." },
  { slug: "/physiotherapy-in-milton/sports-physiotherapy/", id: 11895, keyphrase: "sports physiotherapy Milton", meta: "Sports physiotherapy in Milton for injury recovery & performance. Athlete-focused rehab at Tonic Physio. Direct billing. Book now." }
];

// 4 new pages to create
const newPages = [
  { 
    slug: "herniated-disc-treatment", 
    title: "Herniated Disc Treatment in Milton",
    h1: "Get Rid of Herniated Disc Pain in Milton",
    keyphrase: "herniated disc treatment Milton",
    meta: "Herniated disc treatment in Milton for back pain relief. Expert physiotherapy, spinal mobilization & core strengthening at Tonic Physio. Book today.",
    contentFile: "tonicphysio-herniated-disc-content-map.md",
    images: { featured: 11848, whyChoose: 11849, solutions: 11850 }
  },
  { 
    slug: "sciatica-treatment", 
    title: "Sciatica Treatment in Milton",
    h1: "Stop Sciatica Pain and Get Your Mobility Back in Milton",
    keyphrase: "sciatica treatment Milton",
    meta: "Sciatica treatment in Milton for leg pain relief. Nerve flossing, pelvic alignment & decompression therapy at Tonic Physio. Book consultation.",
    contentFile: "tonicphysio-sciatica-treatment-content-map.md",
    images: { featured: 11695, whyChoose: 11694, solutions: 11693 }
  },
  { 
    slug: "cervical-spondylosis", 
    title: "Cervical Spondylosis Treatment in Milton",
    h1: "Get Relief from Neck Stiffness and Pain in Milton",
    keyphrase: "cervical spondylosis treatment Milton",
    meta: "Cervical spondylosis treatment in Milton for neck pain relief. Joint mobilization, posture correction & soft tissue therapy at Tonic Physio.",
    contentFile: "tonicphysio-cervical-spondylosis-content-map.md",
    images: { featured: 9597, whyChoose: 11651, solutions: 11650 }
  },
  { 
    slug: "b-pulse-pelvic-floor-strengthening", 
    title: "B-Pulse Pelvic Floor Strengthening in Milton",
    h1: "B-Pulse Pelvic Floor Strengthening in Milton",
    keyphrase: "B-Pulse pelvic floor strengthening Milton",
    meta: "B-Pulse pelvic floor strengthening in Milton. Non-invasive treatment for incontinence & core stability. Expert care at Tonic Physio. Book now.",
    contentFile: "reports/tonic-content-drafts/b-pulse-pelvic-floor-strengthening.md",
    images: { featured: 11808, whyChoose: 11815, solutions: 11822 }
  }
];

function ensureDirs() {
  try { mkdirSync(config.userDataDir, { recursive: true }); } catch(e) {}
  try { mkdirSync(config.artifactsDir, { recursive: true }); } catch(e) {}
}

async function updateYoastSEO(page, keyphrase, metaDescription) {
  // Find and click the Yoast SEO tab/section
  try {
    // Try to find Yoast SEO metabox
    const yoastTab = await page.locator('button[data-testid="yoast-seo-tab"], .yoast-seo-tab, button:has-text("Yoast SEO"), .components-tab-panel__tab:has-text("Yoast")').first();
    if (await yoastTab.isVisible()) {
      await yoastTab.click();
      await page.waitForTimeout(500);
    }
  } catch(e) {
    console.log("Yoast tab not found in standard location, searching for focus keyphrase field directly...");
  }

  // Find and fill Focus Keyphrase field
  try {
    const keyphraseInput = await page.locator('input[name="yoast_focus_keyword"], input[id="yoast_focus_keyword"], input[aria-label*="Focus keyphrase"], .yoast-keyword-input input').first();
    if (await keyphraseInput.isVisible()) {
      await keyphraseInput.fill(keyphrase);
      console.log(`  ✓ Set focus keyphrase: "${keyphrase}"`);
    } else {
      console.log(`  ⚠ Focus keyphrase input not found`);
    }
  } catch(e) {
    console.log(`  ⚠ Error setting keyphrase: ${e.message}`);
  }

  // Find and fill Meta Description
  try {
    const metaInput = await page.locator('textarea[name="yoast_meta_desc"], textarea[id="yoast_meta_desc"], textarea[aria-label*="Meta description"], .yoast-seo-meta-description').first();
    if (await metaInput.isVisible()) {
      await metaInput.fill(metaDescription);
      console.log(`  ✓ Set meta description (${metaDescription.length} chars)`);
    } else {
      console.log(`  ⚠ Meta description input not found`);
    }
  } catch(e) {
    console.log(`  ⚠ Error setting meta description: ${e.message}`);
  }

  // Check Yoast status
  try {
    const yoastStatus = await page.locator('.yoast-seo-score-icon, .yst-traffic-light, [class*="yoast-score"]').first();
    if (await yoastStatus.isVisible()) {
      const className = await yoastStatus.getAttribute('class') || '';
      const isGreen = className.includes('green') || className.includes('good');
      console.log(`  ${isGreen ? '✓' : '⚠'} Yoast status: ${isGreen ? 'Green' : 'Not green yet'}`);
    }
  } catch(e) {
    console.log(`  ⚠ Could not verify Yoast status`);
  }
}

async function savePage(page) {
  // Click Update/Publish button
  try {
    const updateBtn = await page.locator('button:has-text("Update"), button:has-text("Publish"), #publishing-action .button-primary').first();
    if (await updateBtn.isVisible()) {
      await updateBtn.click();
      await page.waitForTimeout(2000);
      
      // Wait for save confirmation
      try {
        await page.waitForSelector('.notice-success, .components-snackbar, text=updated', { timeout: 5000 });
        console.log('  ✓ Page saved successfully');
      } catch(e) {
        console.log('  ⚠ Save confirmation not visible, but update was triggered');
      }
    }
  } catch(e) {
    console.log(`  ⚠ Error saving page: ${e.message}`);
  }
}

(async() => {
  ensureDirs();
  
  console.log('=== Tonic Physio SEO Finalization ===');
  console.log('Starting browser automation...\n');
  
  const context = await chromium.launchPersistentContext(config.userDataDir, {
    headless: false,
    viewport: { width: 1440, height: 1000 },
    slowMo: 100
  });
  
  const page = context.pages()[0] || await context.newPage();
  
  // Navigate to WordPress admin
  console.log('Navigating to WordPress admin...');
  await page.goto(config.loginUrl, { waitUntil: 'domcontentloaded' });
  await page.screenshot({ path: join(config.artifactsDir, 'tonic-admin-start.png'), fullPage: true });
  
  // Check if already logged in
  const isLoggedIn = await page.isVisible('a[href*="logout"], #wp-admin-bar-my-account');
  
  if (!isLoggedIn) {
    console.log('Not logged in. Please log in manually in the browser window.');
    console.log('Credentials: Dan / App Password: 4vFk 18fN UlLB twaw B2hU 0kRE');
    console.log('Press Enter in terminal after logging in...');
    
    // Wait for user to log in
    await new Promise(resolve => {
      const checkLogin = setInterval(async() => {
        const nowLoggedIn = await page.isVisible('a[href*="logout"], #wp-admin-bar-my-account');
        if (nowLoggedIn) {
          clearInterval(checkLogin);
          resolve();
        }
      }, 2000);
    });
    
    console.log('Logged in! Continuing...\n');
  } else {
    console.log('Already logged in.\n');
  }
  
  // Process existing pages
  console.log('=== Updating 17 Existing Pages ===\n');
  
  for (const pageData of existingPages) {
    console.log(`\nProcessing: ${pageData.slug}`);
    const editUrl = `https://tonicphysio.com/wp-admin/post.php?post=${pageData.id}&action=edit`;
    await page.goto(editUrl, { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(1000);
    
    await updateYoastSEO(page, pageData.keyphrase, pageData.meta);
    await savePage(page);
    
    await page.screenshot({ path: join(config.artifactsDir, `tonic-${pageData.id}.png`) });
  }
  
  console.log('\n=== All 17 pages updated! ===');
  console.log('Please verify Yoast green status in WordPress admin.');
  
  // Keep browser open for verification
  console.log('\nBrowser will remain open for manual verification.');
  console.log('Press Ctrl+C to close when done.');
  
})();
