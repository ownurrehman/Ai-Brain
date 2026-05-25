const { request } = require('./node_modules/gaxios/build/src/index.js');
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

// Search for businesses using Brave Search API
async function searchBusinesses(industry, city) {
  const apiKey = process.env.BRAVE_SEARCH_API_KEY;
  const query = `"${industry}" "${city}" phone number address business hours`;
  
  try {
    const { data } = await request({
      url: 'https://api.search.brave.com/res/v1/web/search',
      headers: {
        'X-Subscription-Token': apiKey,
        'Accept': 'application/json',
      },
      params: {
        q: query,
        count: 10,
        offset: 0,
      }
    });
    
    const results = [];
    if (data.web && data.web.results) {
      for (const result of data.web.results) {
        // Extract phone numbers from description
        const phoneMatch = result.description?.match(/\(?\d{3}\)?[\s.-]?\d{3}[\s.-]?\d{4}/);
        const phone = phoneMatch ? phoneMatch[0] : '';
        
        // Extract business name from title
        const title = result.title || '';
        const name = title.split('|')[0].split('-')[0].trim();
        
        // Check if it has a website
        const hasWebsite = result.url && !result.url.includes('yelp') && !result.url.includes('facebook');
        
        if (name.length > 2 && (phone || !hasWebsite)) {
          results.push({
            name,
            phone,
            address: result.description?.substring(0, 100) || '',
            url: hasWebsite ? result.url : '',
            hasWebsite: !!hasWebsite,
          });
        }
      }
    }
    
    return results;
  } catch (err) {
    console.log(`Search error: ${err.message}`);
    return [];
  }
}

// Find empty rows that need enrichment
async function findEmptyRows(token) {
  const rows = await readSheet(token, 'Lead Pipeline!A2:Z1000');
  const emptyRows = [];
  
  rows.forEach((row, idx) => {
    const rowNum = idx + 2;
    const leadId = row[0] || '';
    const business = row[2] || '';
    const industry = row[8] || '';
    const location = row[9] || '';
    
    // If business name is empty but we have industry + location
    if (!business && industry && location && leadId.startsWith('RR-')) {
      emptyRows.push({
        rowNum,
        leadId,
        industry,
        location,
        city: location.split(',')[0].trim(),
        country: location.split(',')[1]?.trim() || '',
      });
    }
  });
  
  return emptyRows;
}

async function main() {
  console.log(`=== Enriching Empty Lead Rows ===\n`);
  
  const token = await getAccessToken();
  
  // Find empty rows
  const emptyRows = await findEmptyRows(token);
  console.log(`Found ${emptyRows.length} empty rows needing enrichment`);
  
  if (emptyRows.length === 0) {
    console.log('No empty rows to enrich');
    return;
  }
  
  // Limit to 10 rows per run to stay within API limits
  const batch = emptyRows.slice(0, 10);
  console.log(`Processing ${batch.length} rows...\n`);
  
  let enriched = 0;
  let failed = 0;
  
  for (const row of batch) {
    console.log(`Row ${row.rowNum}: ${row.industry} | ${row.city}, ${row.country}`);
    
    // Search for businesses
    const businesses = await searchBusinesses(row.industry, row.city);
    
    if (businesses.length > 0) {
      // Pick the first result that doesn't have a website
      const noWebsiteBiz = businesses.find(b => !b.hasWebsite) || businesses[0];
      
      // Update the row
      const updateRange = `Lead Pipeline!C${row.rowNum}:H${row.rowNum}`;
      await updateSheet(token, updateRange, [[
        noWebsiteBiz.name,           // Business Name (C)
        'Contact form',              // Contact Name (D)
        '',                          // Email (E) - would need separate enrichment
        noWebsiteBiz.phone,          // Phone (F)
        noWebsiteBiz.hasWebsite ? noWebsiteBiz.url : 'NO WEBSITE', // Website (G)
        noWebsiteBiz.address,        // Address (H)
      ]]);
      
      console.log(`  ✓ Enriched: ${noWebsiteBiz.name} | ${noWebsiteBiz.phone} | ${noWebsiteBiz.hasWebsite ? 'Has website' : 'NO WEBSITE'}`);
      enriched++;
    } else {
      console.log(`  ✗ No results found`);
      failed++;
    }
    
    // Rate limit delay
    await new Promise(r => setTimeout(r, 1500));
  }
  
  console.log(`\n=== Results ===`);
  console.log(`Enriched: ${enriched}`);
  console.log(`Failed: ${failed}`);
  console.log(`Remaining empty: ${emptyRows.length - batch.length}`);
}

main().catch(err => {
  console.error('Fatal error:', err);
  process.exit(1);
});
