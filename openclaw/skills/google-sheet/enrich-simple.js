const { request } = require('./node_modules/gaxios/build/src/index.js');
const { execSync } = require('child_process');
const fs = require('fs');
const jwt = require('jsonwebtoken');

const SHEET_ID = '11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4';

function createJWT() {
  const keyFile = process.env.GOOGLE_SERVICE_ACCOUNT_KEY || '~/.config/google-sheets/credentials.json';
  const resolvedPath = keyFile.replace(/^~/, process.env.HOME);
  const serviceAccount = JSON.parse(fs.readFileSync(resolvedPath, 'utf8'));
  const now = Math.floor(Date.now() / 1000);
  return jwt.sign({
    iss: serviceAccount.client_email,
    scope: 'https://www.googleapis.com/auth/spreadsheets',
    aud: 'https://oauth2.googleapis.com/token',
    iat: now,
    exp: now + 3600,
  }, serviceAccount.private_key, { algorithm: 'RS256' });
}

async function getAccessToken() {
  const token = createJWT();
  const { data } = await request({
    url: 'https://oauth2.googleapis.com/token',
    method: 'POST',
    data: new URLSearchParams({
      grant_type: 'urn:ietf:params:oauth:grant-type:jwt-bearer',
      assertion: token,
    }).toString(),
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
  });
  return data.access_token;
}

async function readSheet(token, range) {
  const { data } = await request({
    url: `https://sheets.googleapis.com/v4/spreadsheets/${SHEET_ID}/values/${encodeURIComponent(range)}`,
    headers: { Authorization: `Bearer ${token}` },
  });
  return data.values || [];
}

async function updateSheet(token, range, values) {
  await request({
    url: `https://sheets.googleapis.com/v4/spreadsheets/${SHEET_ID}/values/${encodeURIComponent(range)}?valueInputOption=RAW`,
    method: 'PUT',
    headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json' },
    data: { values }
  });
}

// Simple search using curl + DuckDuckGo
function searchBusiness(industry, city) {
  const query = encodeURIComponent(`${industry} ${city} phone`);
  
  try {
    // Use DuckDuckGo HTML search
    const cmd = `curl -s -L "https://html.duckduckgo.com/html/?q=${query}" -H "User-Agent: Mozilla/5.0" | sed 's/<[^\u003e]*>//g' | grep -oE '[A-Z][a-zA-Z\s]+(?:Plumbing|Dental|Law|Physio|Roofing|Salon|HVAC|Electrician|Auto|Repair|Cleaning|Landscaping|Carpenter|Painter|Handyman|Accountant|LLC|Inc|Ltd)' | head -5 | sort -u`;
    
    const result = execSync(cmd, {
      encoding: 'utf8',
      timeout: 20000,
      shell: '/bin/bash',
    });
    
    const names = result.split('\n').filter(n => n.trim().length > 2 && n.length < 50);
    
    // Also extract phone numbers
    const phoneCmd = `curl -s -L "https://html.duckduckgo.com/html/?q=${query}" -H "User-Agent: Mozilla/5.0" | sed 's/<[^\u003e]*>//g' | grep -oE '\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}' | head -3`;
    
    const phoneResult = execSync(phoneCmd, {
      encoding: 'utf8',
      timeout: 20000,
      shell: '/bin/bash',
    });
    
    const phones = phoneResult.split('\n').filter(p => p.trim().length > 0);
    
    if (names.length > 0) {
      return {
        name: names[0].trim(),
        phone: phones[0] || '',
        address: `${city}`,
      };
    }
    
    return null;
  } catch (e) {
    console.log(`  Search error: ${e.message}`);
    return null;
  }
}

async function findEmptyRows(token) {
  const rows = await readSheet(token, 'Lead Pipeline!A2:Z1000');
  const emptyRows = [];
  
  rows.forEach((row, idx) => {
    const rowNum = idx + 2;
    const leadId = row[0] || '';
    const business = row[2] || '';
    const industry = row[8] || '';
    const location = row[9] || '';
    
    if (!business && industry && location && leadId.startsWith('RR-')) {
      emptyRows.push({
        rowNum,
        leadId,
        industry,
        location,
        city: location.split(',')[0].trim(),
      });
    }
  });
  
  return emptyRows;
}

async function main() {
  console.log(`=== Simple Lead Enrichment ===\n`);
  
  const token = await getAccessToken();
  const emptyRows = await findEmptyRows(token);
  
  console.log(`Found ${emptyRows.length} empty rows`);
  
  if (emptyRows.length === 0) {
    console.log('No empty rows');
    return;
  }
  
  const batch = emptyRows.slice(0, 5);
  console.log(`Processing ${batch.length} rows...\n`);
  
  let enriched = 0;
  
  for (const row of batch) {
    console.log(`Row ${row.rowNum}: ${row.industry} | ${row.city}`);
    
    const biz = searchBusiness(row.industry, row.city);
    
    if (biz) {
      await updateSheet(token, `Lead Pipeline!C${row.rowNum}:H${row.rowNum}`, [[
        biz.name,
        'Contact form',
        '',
        biz.phone,
        'NO WEBSITE',
        biz.address,
      ]]);
      
      console.log(`  ✓ Enriched: ${biz.name} | ${biz.phone || 'no phone'}`);
      enriched++;
    } else {
      console.log(`  ✗ No results`);
    }
    
    await new Promise(r => setTimeout(r, 3000));
  }
  
  console.log(`\n=== Results: ${enriched}/${batch.length} enriched ===`);
  console.log(`Remaining: ${emptyRows.length - batch.length}`);
}

main().catch(err => {
  console.error('Fatal error:', err);
  process.exit(1);
});
