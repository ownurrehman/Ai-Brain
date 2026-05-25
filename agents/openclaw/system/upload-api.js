#!/usr/bin/env node
const https = require('https');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'tonicphysio.com';
const WP_USER = 'Dan';
const WP_PASS = '4vFk 18fN UlLB twaw B2hU 0kRE'; // Application password
const AUTH = 'Basic ' + Buffer.from(`${WP_USER}:${WP_PASS}`).toString('base64');
const workspace = '/Users/sheikhown/.openclaw/workspace';

function apiRequest(endpoint, method = 'GET', body = null, isImage = false) {
  return new Promise((resolve, reject) => {
    const options = {
      hostname: BASE_URL,
      path: `/wp-json/wp/v2${endpoint}`,
      method: method,
      headers: {
        'Authorization': AUTH,
        'User-Agent': 'OpenClaw/1.0'
      }
    };

    if (isImage && body) {
      const imageData = fs.readFileSync(body);
      options.headers['Content-Type'] = 'image/jpeg';
      options.headers['Content-Disposition'] = `attachment; filename="${path.basename(body)}"`;
      options.headers['Content-Length'] = imageData.length;
    } else if (body) {
      const jsonData = JSON.stringify(body);
      options.headers['Content-Type'] = 'application/json';
      options.headers['Content-Length'] = Buffer.byteLength(jsonData);
    }

    const req = https.request(options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        try {
          const json = JSON.parse(data);
          if (res.statusCode >= 200 && res.statusCode < 300) {
            resolve(json);
          } else {
            console.error(`API Error ${res.statusCode}:`, json);
            reject(new Error(`HTTP ${res.statusCode}`));
          }
        } catch (e) {
          resolve({ raw: data, status: res.statusCode });
        }
      });
    });

    req.on('error', reject);

    if (isImage && body) {
      req.write(fs.readFileSync(body));
    } else if (body) {
      req.write(JSON.stringify(body));
    }
    req.end();
  });
}

(async () => {
  console.log('=== TonicPhysio Image Upload via REST API ===\n');

  try {
    // Test connection
    console.log('1. Testing API connection...');
    const user = await apiRequest('/users/me');
    console.log(`✓ Connected as: ${user.name}`);

    // Upload Pediatric images
    console.log('\n2. Uploading pediatric-physiotherapy-why-choose.jpg...');
    const pedWhy = await apiRequest('/media', 'POST', path.join(workspace, 'pediatric-physiotherapy-why-choose.jpg'), true);
    console.log(`✓ Uploaded, ID: ${pedWhy.id}, URL: ${pedWhy.source_url}`);

    console.log('\n3. Uploading pediatric-physiotherapy-solutions.jpg...');
    const pedSol = await apiRequest('/media', 'POST', path.join(workspace, 'pediatric-physiotherapy-solutions.jpg'), true);
    console.log(`✓ Uploaded, ID: ${pedSol.id}, URL: ${pedSol.source_url}`);

    // Update Pediatric page
    console.log('\n4. Updating Pediatric Physiotherapy page (ID: 1793)...');
    const pedUpdate = await apiRequest('/pages/1793', 'POST', {
      acf: {
        why_choose_us_image: pedWhy.id,
        solutions_image: pedSol.id
      }
    });
    console.log(`✓ Page updated`);

    // Upload Orthopedic images
    console.log('\n5. Uploading orthopedic-physiotherapy-why-choose.jpg...');
    const orthoWhy = await apiRequest('/media', 'POST', path.join(workspace, 'orthopedic-physiotherapy-why-choose.jpg'), true);
    console.log(`✓ Uploaded, ID: ${orthoWhy.id}, URL: ${orthoWhy.source_url}`);

    console.log('\n6. Uploading orthopedic-physiotherapy-solutions.jpg...');
    const orthoSol = await apiRequest('/media', 'POST', path.join(workspace, 'orthopedic-physiotherapy-solutions.jpg'), true);
    console.log(`✓ Uploaded, ID: ${orthoSol.id}, URL: ${orthoSol.source_url}`);

    // Update Orthopedic page
    console.log('\n7. Updating Orthopedic Physiotherapy page (ID: 1791)...');
    const orthoUpdate = await apiRequest('/pages/1791', 'POST', {
      acf: {
        why_choose_us_image: orthoWhy.id,
        solutions_image: orthoSol.id
      }
    });
    console.log(`✓ Page updated`);

    console.log('\n✅ ALL UPLOADS COMPLETED SUCCESSFULLY!\n');
    console.log('Pages updated:');
    console.log('  Pediatric:  https://tonicphysio.com/physiotherapy-in-milton/pediatric-physiotherapy/');
    console.log('  Orthopedic: https://tonicphysio.com/physiotherapy-in-milton/orthopedic-physiotherapy/');
    
    console.log('\nImages uploaded:');
    console.log(`  Pediatric why_choose: ${pedWhy.source_url}`);
    console.log(`  Pediatric solutions:  ${pedSol.source_url}`);
    console.log(`  Orthopedic why_choose: ${orthoWhy.source_url}`);
    console.log(`  Orthopedic solutions:  ${orthoSol.source_url}`);

  } catch (error) {
    console.error('❌ Error:', error.message);
    process.exit(1);
  }
})();
