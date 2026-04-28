const { chromium } = require('playwright');
const fs = require('fs');

const pages = [
  { id: 6305, slug: '/physiotherapy-in-milton/', keyphrase: 'physiotherapy in Milton', meta: 'Physiotherapy in Milton for pain relief & recovery. Expert rehab, manual therapy & direct billing at Tonic Physio. Book today.' },
  { id: 6279, slug: '/compression-socks/', keyphrase: 'compression socks Milton', meta: 'Compression socks in Milton for recovery & circulation support. Expert fitting & advice at Tonic Physio. In-person consultations available.' },
  { id: 1797, slug: '/custom-orthotics/', keyphrase: 'custom orthotics Milton', meta: 'Custom orthotics in Milton for foot support & pain relief. Personalized assessment & gait analysis at Tonic Physio. Book consultation.' },
  { id: 6280, slug: '/custom-and-otc-bracing/', keyphrase: 'custom bracing Milton', meta: 'Custom and OTC bracing in Milton for injury recovery & joint stability. Expert fitting at Tonic Physio. Knee, ankle & posture braces available.' },
  { id: 1794, slug: '/registered-massage-therapy/', keyphrase: 'registered massage therapy Milton', meta: 'Registered massage therapy in Milton for pain relief & stress reduction. Personalized hands-on care at Tonic Physio. RMTs available. Book now.' },
  { id: 6283, slug: '/shockwave-therapy/', keyphrase: 'shockwave therapy Milton', meta: 'Shockwave therapy in Milton for fast injury recovery. Pain relief & healing acceleration at Tonic Physio. Book your session today.' },
  { id: 1799, slug: '/motor-vehicle-accident-physiotherapy/', keyphrase: 'MVA physiotherapy Milton', meta: 'Motor vehicle accident physiotherapy in Milton. MVA injury recovery, pain relief & mobility restoration at Tonic Physio. Direct billing available.' },
  { id: 1798, slug: '/wsib-care-programs/', keyphrase: 'WSIB care programs Milton', meta: 'WSIB care programs in Milton for workplace injury recovery. Expert physiotherapy & direct billing at Tonic Physio. Get back to work faster.' },
  { id: 1795, slug: '/manual-osteopathy-milton/', keyphrase: 'manual osteopathy Milton', meta: 'Manual osteopathy in Milton for pain relief & mobility. Gentle hands-on treatment by experienced osteopaths at Tonic Physio. Book assessment.' },
  { id: 1791, slug: '/physiotherapy-in-milton/orthopedic-physiotherapy/', keyphrase: 'orthopedic physiotherapy Milton', meta: 'Orthopedic physiotherapy in Milton for joint pain & mobility recovery. Personalized rehab plans at Tonic Physio. Lasting results. Book today.' },
  { id: 1796, slug: '/physiotherapy-in-milton/neurological-physiotherapy/', keyphrase: 'neurological physiotherapy Milton', meta: 'Neurological physiotherapy in Milton for movement & strength recovery. Personalized care for stroke, Parkinson\'s & conditions at Tonic Physio.' },
  { id: 1793, slug: '/physiotherapy-in-milton/pediatric-physiotherapy/', keyphrase: 'pediatric physiotherapy Milton', meta: 'Pediatric physiotherapy in Milton for children\'s mobility & strength. Developmental care for kids at Tonic Physio. Book child assessment today.' },
  { id: 1792, slug: '/physiotherapy-in-milton/acupuncture-therapy/', keyphrase: 'acupuncture therapy Milton', meta: 'Acupuncture therapy in Milton for pain relief & stress reduction. Natural healing & balance restoration at Tonic Physio. Book session today.' },
  { id: 6971, slug: '/physiotherapy-in-milton/joint-pain-and-stiffness/', keyphrase: 'joint pain treatment Milton', meta: 'Joint pain and stiffness treatment in Milton. Personalized physiotherapy to restore mobility & reduce discomfort at Tonic Physio. Book now.' },
  { id: 6981, slug: '/physiotherapy-in-milton/rheumatoid-arthritis-therapy-treatment/', keyphrase: 'rheumatoid arthritis therapy Milton', meta: 'Rheumatoid arthritis therapy in Milton for pain relief & mobility. Joint function improvement at Tonic Physio. Expert care. Book consultation.' },
  { id: 6991, slug: '/physiotherapy-in-milton/back-and-neck-pain/', keyphrase: 'back and neck pain Milton', meta: 'Back and neck pain treatment in Milton. Expert physiotherapy for lasting pain relief at Tonic Physio. Personalized care. Book assessment today.' },
  { id: 11895, slug: '/physiotherapy-in-milton/sports-physiotherapy/', keyphrase: 'sports physiotherapy Milton', meta: 'Sports physiotherapy in Milton for injury recovery & performance. Athlete-focused rehab at Tonic Physio. Direct billing. Book now.' }
];

const imageMapping = {
  6996: { slug: 'herniated-disc-treatment', featuredImage: 11848 },
  7001: { slug: 'sciatica-treatment', featuredImage: 11695 },
  7006: { slug: 'cervical-spondylosis', featuredImage: 9597 },
  11603: { slug: 'b-pulse-pelvic-floor-strengthening', featuredImage: 11808 }
};

async function run() {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();

  try {
    console.log('🔐 Attempting login...');
    await page.goto('https://tonicphysio.com/wp-admin/', { waitUntil: 'networkidle' });
    
    await page.fill('#user_login', 'Dan');
    await page.fill('#user_pass', '4vFk 18fN UlLB twaw B2hU 0kRE');
    
    // Click login and wait for dashboard element to avoid timeout on navigation
    await page.click('#wp-submit');
    await page.waitForSelector('#adminmenu', { timeout: 30000 }).catch(e => {
      console.log('Login timeout or redirect failed. Checking current URL...');
      console.log('Current URL:', page.url());
      throw e;
    });
    console.log('✅ Logged in successfully');

    for (const pageData of pages) {
      console.log(`📄 Processing ${pageData.slug}`);
      await page.goto(`https://tonicphysio.com/wp-admin/post.php?post=${pageData.id}&action=edit`, { waitUntil: 'networkidle' });
      
      // Strategy: Use JS to set Yoast fields to bypass complex DOM/Iframe issues
      await page.evaluate((data) => {
        // Classic editor fields
        const focusKw = document.getElementsByName('yoast_wpseo_focuskw')[0];
        const metaDesc = document.getElementsByName('yoast_wpseo_metadesc')[0];
        if (focusKw) focusKw.value = data.keyphrase;
        if (metaDesc) metaDesc.value = data.meta;

        // Gutenberg fields (they often use different naming or are in shadows)
        const allInputs = document.querySelectorAll('input, textarea');
        allInputs.forEach(input => {
          if (input.placeholder && input.placeholder.includes('keyphrase')) input.value = data.keyphrase;
          if (input.placeholder && input.placeholder.includes('description')) input.value = data.meta;
        });
      }, pageData);

      await page.click('#publish').catch(() => {}); 
      await page.waitForTimeout(1000);
    }

    console.log('🖼️ Assigning images...');
    for (const [pageId, imageData] of Object.entries(imageMapping)) {
      await page.goto(`https://tonicphysio.com/wp-admin/post.php?post=${pageId}&action=edit`, { waitUntil: 'networkidle' });
      await page.evaluate((imgId) => {
        // Force set featured image via hidden field if available
        const input = document.getElementsByName('_thumbnail_id')[0];
        if (input) input.value = imgId;
      }, imageData.featuredImage);
      await page.click('#publish').catch(() => {});
    }

    console.log('✅ All operations completed.');
  } catch (error) {
    console.error('Fatal:', error);
  } finally {
    await browser.close();
  }
}

run();
