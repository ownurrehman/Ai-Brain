const https = require('https');

// Credentials from service monitor (worked for 4 priority pages)
const auth = Buffer.from('rankrayagency@gmail.com:4isf Zcbd pvGI O1fp lQKB Jz2M').toString('base64');

// Remaining 17 pages needing SEO metadata (from service monitor)
const pages = [
  { id: 6305, keyphrase: 'physiotherapy in Milton', meta: 'Physiotherapy in Milton for pain relief & recovery. Expert rehab, manual therapy & direct billing at Tonic Physio. Book today.' },
  { id: 6279, keyphrase: 'compression socks Milton', meta: 'Compression socks in Milton for recovery & circulation support. Expert fitting & advice at Tonic Physio. In-person consultations available.' },
  { id: 1797, keyphrase: 'custom orthotics Milton', meta: 'Custom orthotics in Milton for foot support & pain relief. Personalized assessment & gait analysis at Tonic Physio. Book consultation.' },
  { id: 6280, keyphrase: 'custom bracing Milton', meta: 'Custom and OTC bracing in Milton for injury recovery & joint stability. Expert fitting at Tonic Physio. Knee, ankle & posture braces available.' },
  { id: 1794, keyphrase: 'registered massage therapy Milton', meta: 'Registered massage therapy in Milton for pain relief & stress reduction. Personalized hands-on care at Tonic Physio. RMTs available. Book now.' },
  { id: 6283, keyphrase: 'shockwave therapy Milton', meta: 'Shockwave therapy in Milton for fast injury recovery. Pain relief & healing acceleration at Tonic Physio. Book your session today.' },
  { id: 1799, keyphrase: 'MVA physiotherapy Milton', meta: 'Motor vehicle accident physiotherapy in Milton. MVA injury recovery, pain relief & mobility restoration at Tonic Physio. Direct billing available.' },
  { id: 1798, keyphrase: 'WSIB care programs Milton', meta: 'WSIB care programs in Milton for workplace injury recovery. Expert physiotherapy & direct billing at Tonic Physio. Get back to work faster.' },
  { id: 1795, keyphrase: 'manual osteopathy Milton', meta: 'Manual osteopathy in Milton for pain relief & mobility. Gentle hands-on treatment by experienced osteopaths at Tonic Physio. Book assessment.' },
  { id: 1791, keyphrase: 'orthopedic physiotherapy Milton', meta: 'Orthopedic physiotherapy in Milton for joint pain & mobility recovery. Personalized rehab plans at Tonic Physio. Lasting results. Book today.' },
  { id: 1796, keyphrase: 'neurological physiotherapy Milton', meta: 'Neurological physiotherapy in Milton for movement & strength recovery. Personalized care for stroke, Parkinsons & conditions at Tonic Physio.' },
  { id: 1793, keyphrase: 'pediatric physiotherapy Milton', meta: 'Pediatric physiotherapy in Milton for childrens mobility & strength. Developmental care for kids at Tonic Physio. Book child assessment today.' },
  { id: 1792, keyphrase: 'acupuncture therapy Milton', meta: 'Acupuncture therapy in Milton for pain relief & stress reduction. Natural healing & balance restoration at Tonic Physio. Book session today.' },
  { id: 6971, keyphrase: 'joint pain treatment Milton', meta: 'Joint pain and stiffness treatment in Milton. Personalized physiotherapy to restore mobility & reduce discomfort at Tonic Physio. Book now.' },
  { id: 6981, keyphrase: 'rheumatoid arthritis therapy Milton', meta: 'Rheumatoid arthritis therapy in Milton for pain relief & mobility. Joint function improvement at Tonic Physio. Expert care. Book consultation.' },
  { id: 6991, keyphrase: 'back and neck pain Milton', meta: 'Back and neck pain treatment in Milton. Expert physiotherapy for lasting pain relief at Tonic Physio. Personalized care. Book assessment today.' },
  { id: 11895, keyphrase: 'sports physiotherapy Milton', meta: 'Sports physiotherapy in Milton for injury recovery & performance. Athlete-focused rehab at Tonic Physio. Direct billing. Book now.' }
];

function updatePage(page) {
  return new Promise((resolve, reject) => {
    const data = JSON.stringify({
      yoast_focus_kwphrase: page.keyphrase,
      yoast_metadesc: page.meta
    });

    const options = {
      hostname: 'tonicphysio.com',
      port: 443,
      path: `/wp-json/wp/v2/pages/${page.id}`,
      method: 'POST',
      headers: {
        'Authorization': `Basic ${auth}`,
        'Content-Type': 'application/json',
        'Content-Length': data.length
      }
    };

    const req = https.request(options, (res) => {
      let body = '';
      res.on('data', chunk => body += chunk);
      res.on('end', () => {
        if (res.statusCode === 200) {
          resolve({ id: page.id, status: 'success', slug: page.slug });
        } else {
          resolve({ id: page.id, status: 'failed', code: res.statusCode, body: body.substring(0, 200) });
        }
      });
    });

    req.on('error', reject);
    req.write(data);
    req.end();
  });
}

async function batchUpdate() {
  console.log(`Starting batch update for ${pages.length} pages...\n`);
  const results = [];
  
  for (const page of pages) {
    try {
      const result = await updatePage(page);
      results.push(result);
      console.log(`Page ${result.id}: ${result.status === 'success' ? '✅' : '❌'} ${result.status === 'failed' ? `(HTTP ${result.code})` : ''}`);
    } catch (err) {
      results.push({ id: page.id, status: 'error', message: err.message });
      console.log(`Page ${page.id}: ❌ ERROR - ${err.message}`);
    }
  }

  const success = results.filter(r => r.status === 'success').length;
  console.log(`\n=== Batch Complete: ${success}/${pages.length} successful ===`);
  process.exit(success === pages.length ? 0 : 1);
}

batchUpdate();
