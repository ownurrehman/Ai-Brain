const { request } = require('./node_modules/gaxios/build/src/index.js');
const fs = require('fs');
const jwt = require('jsonwebtoken');

const keyFile = process.env.GOOGLE_SERVICE_ACCOUNT_KEY || '~/.config/google-sheets/credentials.json';
const resolvedPath = keyFile.replace(/^~/, require('os').homedir());
const serviceAccount = JSON.parse(fs.readFileSync(resolvedPath, 'utf8'));

function createJWT() {
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

async function readSheet(accessToken, range) {
  const { data } = await request({
    url: `https://sheets.googleapis.com/v4/spreadsheets/11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4/values/${encodeURIComponent(range)}`,
    headers: { Authorization: `Bearer ${accessToken}` },
  });
  return data.values || [];
}

function isAIGenerated(row) {
  const business = (row[2] || '').toLowerCase();
  const industry = (row[8] || '').toLowerCase();
  const notes = (row[20] || '').toLowerCase();
  const solution = (row[11] || '').toLowerCase();
  const painPoints = (row[10] || '').toLowerCase();
  
  // Detect templated AI content patterns
  const aiPatterns = [
    /making waves in/i,
    /many agencies in this space struggle/i,
    /rank ray offers semantic seo/i,
    /rank ray specializes in/i,
    /competing in .+ market requires advanced technical seo/i,
    /discovered via web search for top agencies/i,
    /geo \(generative engine optimization\)/i,
    /geo optimization/i,
    /semantic seo and geo/i,
    /future-proof your rankings/i,
    /quick audit of your current seo footprint/i,
    /no pitch, just value/i,
  ];
  
  for (const pattern of aiPatterns) {
    if (pattern.test(notes) || pattern.test(solution) || pattern.test(painPoints)) {
      return true;
    }
  }
  
  // Remove SEO/digital marketing agencies (they're competitors, not leads)
  const competitorIndustries = [
    'seo',
    'digital marketing',
    'web design / seo',
    'web design',
    'ecommerce seo',
    'local seo',
    'seo & ppc',
    'web design / seo',
  ];
  
  if (competitorIndustries.some(ind => industry.includes(ind.toLowerCase()))) {
    return true;
  }
  
  // Remove if business name contains SEO/digital marketing terms
  const competitorNames = [
    'seo', 'marketing', 'digital', 'web design', 'web agency',
    'ppc', 'sem', 'search engine', 'optimization'
  ];
  
  if (competitorNames.some(name => business.includes(name))) {
    return true;
  }
  
  return false;
}

async function main() {
  const token = await getAccessToken();
  
  // Read all rows
  let allRows = [];
  let start = 2;
  let hasMore = true;
  
  while (hasMore && start < 1000) {
    const range = `Lead Pipeline!A${start}:Z${start + 99}`;
    const rows = await readSheet(token, range);
    if (!rows || rows.length === 0) {
      hasMore = false;
    } else {
      rows.forEach((row, i) => {
        if (row[0] || row[2]) {
          allRows.push({ idx: start + i, data: row });
        }
      });
      start += rows.length;
      if (rows.length < 100) hasMore = false;
    }
  }
  
  console.log(`Total rows: ${allRows.length}`);
  
  const aiRows = [];
  const realBusinessRows = [];
  
  allRows.forEach(row => {
    if (isAIGenerated(row.data)) {
      aiRows.push({
        idx: row.idx,
        leadId: row.data[0] || '',
        business: row.data[2] || '',
        industry: row.data[8] || '',
        reason: 'AI-generated / Competitor'
      });
    } else {
      realBusinessRows.push(row);
    }
  });
  
  console.log(`AI/Competitor rows to remove: ${aiRows.length}`);
  console.log(`Real business leads remaining: ${realBusinessRows.length}`);
  
  console.log('\n=== AI ROWS (first 30) ===');
  aiRows.slice(0, 30).forEach(r => {
    console.log(`Row ${r.idx}: ${r.leadId} | ${r.business} | ${r.industry} | ${r.reason}`);
  });
  
  console.log('\n=== REAL BUSINESS LEADS (first 30) ===');
  realBusinessRows.slice(0, 30).forEach(r => {
    console.log(`Row ${r.idx}: ${r.data[0]} | ${r.data[2]} | ${r.data[8]} | ${r.data[9]}`);
  });
  
  // Delete AI rows
  const sorted = aiRows.map(r => r.idx).sort((a, b) => b - a);
  
  console.log(`\nDeleting ${sorted.length} AI rows...`);
  
  const BATCH_SIZE = 100;
  let deleted = 0;
  
  for (let i = 0; i < sorted.length; i += BATCH_SIZE) {
    const batch = sorted.slice(i, i + BATCH_SIZE);
    
    const requests = batch.map(rowIdx => ({
      deleteDimension: {
        range: {
          sheetId: 0,
          dimension: 'ROWS',
          startIndex: rowIdx - 1,
          endIndex: rowIdx
        }
      }
    }));
    
    try {
      await request({
        url: 'https://sheets.googleapis.com/v4/spreadsheets/11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4:batchUpdate',
        method: 'POST',
        headers: { 
          Authorization: `Bearer ${token}`,
          'Content-Type': 'application/json'
        },
        data: { requests }
      });
      
      deleted += batch.length;
      console.log(`  ✓ Deleted ${batch.length} rows`);
    } catch (err) {
      console.error(`  ✗ Error:`, err.message);
    }
  }
  
  console.log(`\n✓ Total deleted: ${deleted} AI/competitor rows`);
  console.log(`✓ Lead Pipeline now has ${realBusinessRows.length} real business leads`);
}

main().catch(console.error);
