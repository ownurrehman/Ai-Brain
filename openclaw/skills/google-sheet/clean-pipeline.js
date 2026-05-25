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

function isFake(business, email) {
  if (!business) return true;
  if (email && /\w+-\d+\.(ca|com|net|ae|ai|io|us)/.test(email)) return true;
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
  
  console.log(`Total rows: ${allRows.length}`);
  
  // Find fake rows to delete
  const fakeRows = [];
  allRows.forEach(row => {
    const business = (row.data[2] || '').trim();
    const email = (row.data[4] || '').trim().toLowerCase();
    if (isFake(business, email)) {
      fakeRows.push(row.idx);
    }
  });
  
  console.log(`Fake rows to delete: ${fakeRows.length}`);
  console.log(`Real rows remaining: ${allRows.length - fakeRows.length}`);
  
  // Sort descending for deletion
  const sorted = fakeRows.sort((a, b) => b - a);
  
  // Delete in batches (max 100 per batch)
  const BATCH_SIZE = 100;
  let deleted = 0;
  
  for (let i = 0; i < sorted.length; i += BATCH_SIZE) {
    const batch = sorted.slice(i, i + BATCH_SIZE);
    console.log(`\nDeleting batch ${Math.floor(i/BATCH_SIZE) + 1}: rows ${batch[0]} to ${batch[batch.length-1]}`);
    
    const requests = batch.map(rowIdx => ({
      deleteDimension: {
        range: {
          sheetId: 0, // Lead Pipeline
          dimension: 'ROWS',
          startIndex: rowIdx - 1, // 0-based
          endIndex: rowIdx // exclusive
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
      if (err.response?.data) console.error('Details:', JSON.stringify(err.response.data, null, 2));
    }
  }
  
  console.log(`\n✓ Total deleted: ${deleted} fake rows`);
  console.log(`✓ Lead Pipeline now has ${allRows.length - deleted} real leads`);
}

main().catch(err => {
  console.error('Error:', err.message);
  if (err.response?.data) console.error('Details:', JSON.stringify(err.response.data, null, 2));
  process.exit(1);
});
