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

// Use firecrawl for web scraping
const { execSync } = require('child_process');
const axios = require('axios');

// Fallback: Use simple curl/web fetch
function searchWithCurl(industry, city) {
  const query = encodeURIComponent(`${industry} ${city} business phone`);
  
  try {
    // Use curl to get Google search results
    const result = execSync(`curl -s "https://html.duckduckgo.com/html/?q=${query}" | grep -oE 'class="result__title"[^>]*>[^<]+' | head -5`, {
      encoding: 'utf8',
      timeout: 15000,
    });
    
    const businesses = [];
    const lines = result.split('\n').filter(l => l.trim().length > 0);
    
    for (const line of lines) {
      // Extract business name from result title
      const clean = line.replace(/class="result__title"[^>]*>/, '').trim();
      if (clean.length > 2 && clean.length < 60 && !clean.includes('...')) {
        businesses.push({
          name: clean,
          phone: '',
          snippet: '',
        });
      }
    }
    
    return businesses;
  } catch (e) {
    console.log(`Search error: ${e.message}`);
    return [];
  }
}

// Find empty rows
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
  console.log(`=== Enriching Empty Lead Rows ===\n`);
  
  const token = await getAccessToken();
  const emptyRows = await findEmptyRows(token);
  
  console.log(`Found ${emptyRows.length} empty rows needing enrichment`);
  
  if (emptyRows.length === 0) {
    console.log('No empty rows to enrich');
    return;
  }
  
  // Process 5 rows per run
  const batch = emptyRows.slice(0, 5);
  console.log(`Processing ${batch.length} rows...\n`);
  
  let enriched = 0;
  
  for (const row of batch) {
    console.log(`Row ${row.rowNum}: ${row.industry} | ${row.city}`);
    
    const businesses = searchWithOpenClaw(row.industry, row.city);
    
    if (businesses.length > 0) {
      const biz = businesses[0]; // Take first result
      
      const updateRange = `Lead Pipeline!C${row.rowNum}:H${row.rowNum}`;
      await updateSheet(token, updateRange, [[
        biz.name,
        'Contact form',
        '',
        biz.phone,
        'NO WEBSITE',
        biz.snippet,
      ]]);
      
      console.log(`  ✓ Enriched: ${biz.name} | ${biz.phone || 'no phone'}`);
      enriched++;
    } else {
      console.log(`  ✗ No results found`);
    }
    
    // Rate limit
    await new Promise(r => setTimeout(r, 3000));
  }
  
  console.log(`\n=== Results ===`);
  console.log(`Enriched: ${enriched}/${batch.length}`);
  console.log(`Remaining: ${emptyRows.length - batch.length}`);
}

main().catch(err => {
  console.error('Fatal error:', err);
  process.exit(1);
});
