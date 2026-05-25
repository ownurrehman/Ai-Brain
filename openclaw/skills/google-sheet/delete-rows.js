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

async function deleteRows(accessToken, rowNumbers) {
  // Sort descending so we delete from bottom to top
  const sorted = [...rowNumbers].sort((a, b) => b - a);
  
  // Group consecutive rows for batch deletion
  const requests = [];
  let i = 0;
  
  while (i < sorted.length) {
    let startIdx = sorted[i];
    let endIdx = sorted[i];
    let count = 1;
    
    // Find consecutive range
    while (i + 1 < sorted.length && sorted[i + 1] === endIdx - 1) {
      endIdx = sorted[i + 1];
      count++;
      i++;
    }
    
    // Google Sheets uses 0-based indexing for API
    // But our row numbers are 1-based (as shown in the sheet)
    // DeleteDimension uses startIndex (0-based, inclusive) and endIndex (0-based, exclusive)
    requests.push({
      deleteDimension: {
        range: {
          sheetId: 0, // Lead Pipeline tab
          dimension: 'ROWS',
          startIndex: endIdx - 1, // Convert to 0-based
          endIndex: startIdx // endIndex is exclusive
        }
      }
    });
    
    i++;
  }
  
  console.log(`Deleting ${sorted.length} rows in ${requests.length} batches...`);
  
  // Send in batches of 100 requests at a time
  const BATCH_SIZE = 100;
  let deleted = 0;
  
  for (let b = 0; b < requests.length; b += BATCH_SIZE) {
    const batch = requests.slice(b, b + BATCH_SIZE);
    console.log(`Batch ${Math.floor(b/BATCH_SIZE) + 1}/${Math.ceil(requests.length/BATCH_SIZE)}: ${batch.length} operations`);
    
    const { data } = await request({
      url: 'https://sheets.googleapis.com/v4/spreadsheets/11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4:batchUpdate',
      method: 'POST',
      headers: { 
        Authorization: `Bearer ${accessToken}`,
        'Content-Type': 'application/json'
      },
      data: { requests: batch }
    });
    
    deleted += batch.length;
    console.log(`  ✓ Deleted batch ${Math.floor(b/BATCH_SIZE) + 1}`);
  }
  
  return deleted;
}

async function main() {
  const results = JSON.parse(fs.readFileSync('deep-clean-results.json', 'utf8'));
  const rowsToDelete = results.allToDelete;
  
  console.log(`Preparing to delete ${rowsToDelete.length} rows...`);
  console.log('Rows:', rowsToDelete.slice(0, 50).join(', '));
  if (rowsToDelete.length > 50) {
    console.log(`... and ${rowsToDelete.length - 50} more`);
  }
  
  console.log('\nStarting deletion...');
  
  const token = await getAccessToken();
  const deleted = await deleteRows(token, rowsToDelete);
  
  console.log(`\n✓ Deleted ${deleted} row groups`);
  console.log(`Remaining real leads: ${results.remaining}`);
}

main().catch(err => {
  console.error('Error:', err.message);
  if (err.response?.data) console.error('Details:', err.response.data);
  process.exit(1);
});
