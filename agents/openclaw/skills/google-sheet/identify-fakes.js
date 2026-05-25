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

function isFakeEmail(email) {
  if (!email) return false;
  // Pattern: domain with number suffix like torontolawsite-13.ca
  return /\w+-\d+\.(ca|com|net|ae|ai|io|us)/.test(email);
}

function isFakeBusiness(business, email, website) {
  if (!business) return true;
  
  // Check for generic AI patterns that don't exist in real world
  const genericAIPatterns = [
    /home pro$/i,
    /law group$/i,
    /legal group$/i,
    /family dental$/i,
    /family law$/i,
    /dental center$/i,
    /dental clinic$/i,
  ];
  
  // If domain is numbered, it's definitely fake regardless of name
  if (isFakeEmail(email)) return true;
  
  // Check for location-specific fake patterns in business name
  const fakeLocations = ['San Diego', 'Boston', 'Denver', 'Austin', 'Chicago', 'Houston', 
    'Dallas', 'Atlanta', 'Miami', 'Seattle', 'Vancouver', 'Calgary', 'Montreal', 'Ottawa',
    'Toronto', 'Edmonton', 'NYC', 'New York', 'Los Angeles', 'Phoenix', 'Portland',
    'Nashville', 'Tampa', 'Charlotte', 'Orlando', 'Mirdif', 'JLT', 'Al Quoz'];
  
  const lowerBiz = business.toLowerCase();
  const hasFakeLocation = fakeLocations.some(loc => lowerBiz.includes(loc.toLowerCase()));
  
  // If it has a fake location in name + generic industry + no real website, it's fake
  if (hasFakeLocation && genericAIPatterns.some(p => p.test(business))) {
    // Check if website looks real
    if (!website || website.length < 10 || !website.match(/^https?:\/\//)) {
      return true;
    }
  }
  
  return false;
}

async function main() {
  const token = await getAccessToken();
  
  // Read all rows
  let allRows = [];
  let start = 2;
  let hasMore = true;
  
  while (hasMore && start < 3000) {
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
  
  console.log(`Total rows with data: ${allRows.length}\n`);
  
  const fakeRows = [];
  const realRows = [];
  
  allRows.forEach(row => {
    const leadId = (row.data[0] || '').trim();
    const business = (row.data[2] || '').trim();
    const email = (row.data[4] || '').trim().toLowerCase();
    const website = (row.data[6] || '').trim();
    const phone = (row.data[5] || '').trim();
    
    const isFake = isFakeBusiness(business, email, website);
    
    if (isFake) {
      fakeRows.push({
        idx: row.idx,
        leadId,
        business,
        email,
        website: website || 'NO-WEBSITE',
        phone: phone || 'NO-PHONE',
        reason: isFakeEmail(email) ? 'NUMBERED DOMAIN' : 'GENERIC AI NAME'
      });
    } else {
      realRows.push(row);
    }
  });
  
  console.log(`=== FAKE ROWS: ${fakeRows.length} ===`);
  console.log(`=== REAL ROWS: ${realRows.length} ===\n`);
  
  console.log('=== FIRST 50 FAKE ROWS ===');
  fakeRows.slice(0, 50).forEach(f => {
    console.log(`Row ${f.idx}: ${f.leadId} | ${f.business} | ${f.email} | ${f.website} | ${f.reason}`);
  });
  
  if (fakeRows.length > 50) {
    console.log(`\n... and ${fakeRows.length - 50} more fake rows`);
  }
  
  // Show sample of real rows for comparison
  console.log('\n=== SAMPLE REAL ROWS ===');
  realRows.slice(0, 20).forEach(r => {
    const leadId = (r.data[0] || '').trim();
    const business = (r.data[2] || '').trim();
    const email = (r.data[4] || '').trim();
    console.log(`Row ${r.idx}: ${leadId} | ${business} | ${email}`);
  });
  
  // Save results
  fs.writeFileSync('fake-rows.json', JSON.stringify({
    totalRows: allRows.length,
    fakeCount: fakeRows.length,
    realCount: realRows.length,
    fakeRows: fakeRows.map(f => f.idx),
    fakeDetails: fakeRows,
  }, null, 2));
  
  console.log(`\nSaved to fake-rows.json`);
}

main().catch(console.error);
