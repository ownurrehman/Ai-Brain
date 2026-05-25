const https = require('https');
const fs = require('fs');
const path = require('path');

const WP_USER = 'Dan';
const WP_PASS = '4vFk 18fN UlLB twaw B2hU 0kRE'; // Application password
const BASE_URL = 'tonicphysio.com';
const AUTH = 'Basic ' + Buffer.from(`${WP_USER}:${WP_PASS}`).toString('base64');
const workspace = '/Users/sheikhown/.openclaw/workspace';

console.log('=== Testing REST API with Application Password ===\n');
console.log('Auth header (first 30 chars):', AUTH.substring(0, 30) + '...');

function apiRequest(endpoint, method = 'GET', body = null, isImage = false, imagePath = null) {
  return new Promise((resolve, reject) => {
    const options = {
      hostname: BASE_URL,
      path: `/wp-json/wp/v2${endpoint}`,
      method: method,
      headers: {
        'Authorization': AUTH,
        'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
        'Accept': 'application/json'
      }
    };

    if (isImage && imagePath) {
      const imageData = fs.readFileSync(imagePath);
      options.headers['Content-Type'] = 'image/jpeg';
      options.headers['Content-Disposition'] = `attachment; filename="${path.basename(imagePath)}"`;
      options.headers['Content-Length'] = imageData.length;
    } else if (body) {
      const jsonData = JSON.stringify(body);
      options.headers['Content-Type'] = 'application/json';
      options.headers['Content-Length'] = Buffer.byteLength(jsonData);
    }

    console.log(`\n${method} ${options.path}`);
    console.log('Headers:', JSON.stringify({
      Authorization: AUTH.substring(0, 20) + '...',
      'Content-Type': options.headers['Content-Type']
    }, null, 2));

    const req = https.request(options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        console.log(`Response ${res.statusCode}:`);
        try {
          const json = JSON.parse(data);
          console.log(JSON.stringify(json, null, 2).substring(0, 500));
          if (res.statusCode >= 200 && res.statusCode < 300) {
            resolve(json);
          } else {
            reject(new Error(`HTTP ${res.statusCode}`));
          }
        } catch (e) {
          console.log('Raw response:', data.substring(0, 200));
          resolve({ raw: data, status: res.statusCode });
        }
      });
    });

    req.on('error', (e) => {
      console.error('Request error:', e.message);
      reject(e);
    });

    if (isImage && imagePath) {
      console.log('Sending image:', imagePath);
      req.write(fs.readFileSync(imagePath));
    } else if (body) {
      req.write(JSON.stringify(body));
    }
    req.end();
  });
}

(async () => {
  try {
    // Test 1: Check current user
    console.log('\n--- Test 1: Get current user ---');
    try {
      const user = await apiRequest('/users/me');
      console.log('✓ User:', user.name || user.slug);
    } catch (e) {
      console.log('✗ User check failed:', e.message);
    }

    // Test 2: Get page 1793
    console.log('\n--- Test 2: Get page 1793 ---');
    try {
      const page = await apiRequest('/pages/1793');
      console.log('✓ Page title:', page.title?.rendered);
      console.log('ACF why_choose_us_image:', page.acf?.why_choose_us_image || '(empty)');
      console.log('ACF solutions_image:', page.acf?.solutions_image || '(empty)');
    } catch (e) {
      console.log('✗ Page get failed:', e.message);
    }

    // Test 3: Try to upload an image
    console.log('\n--- Test 3: Upload test image ---');
    const testImage = path.join(workspace, 'pediatric-physiotherapy-why-choose.jpg');
    if (fs.existsSync(testImage)) {
      console.log('Image exists:', testImage);
      console.log('Image size:', fs.statSync(testImage).size, 'bytes');
      
      try {
        const media = await apiRequest('/media', 'POST', null, true, testImage);
        console.log('✓ Media uploaded, ID:', media.id);
        console.log('Source URL:', media.source_url);
      } catch (e) {
        console.log('✗ Upload failed:', e.message);
      }
    } else {
      console.log('✗ Image not found:', testImage);
    }

    // Test 4: Try to update page with ACF
    console.log('\n--- Test 4: Update page with ACF (test value) ---');
    try {
      const update = await apiRequest('/pages/1793', 'POST', {
        acf: {
          why_choose_us_image: 9032, // Use existing media ID
          solutions_image: 9032
        }
      });
      console.log('✓ Page updated');
      console.log('New ACF values:', JSON.stringify(update.acf, null, 2).substring(0, 300));
    } catch (e) {
      console.log('✗ Update failed:', e.message);
    }

  } catch (error) {
    console.error('\n❌ Script error:', error.message);
  }
})();
