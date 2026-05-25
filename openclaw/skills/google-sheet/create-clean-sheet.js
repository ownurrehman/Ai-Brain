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

function isFakeBusiness(business, email, website) {
  if (!business) return true;
  // Numbered domains are definitely fake
  if (email && /\w+-\d+\.(ca|com|net|ae|ai|io|us)/.test(email)) return true;
  return false;
}

async function main() {
  const token = await getAccessToken();
  
  // Read headers first
  const headers = await readSheet(token, 'Lead Pipeline!A1:Z1');
  console.log('Headers read');
  
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
  
  // Filter real rows
  const realRows = allRows.filter(row => {
    const business = (row.data[2] || '').trim();
    const email = (row.data[4] || '').trim().toLowerCase();
    const website = (row.data[6] || '').trim();
    return !isFakeBusiness(business, email, website);
  });
  
  console.log(`Real rows: ${realRows.length}`);
  console.log(`Fake rows: ${allRows.length - realRows.length}`);
  
  // Prepare data for writing
  const cleanData = [headers[0]];
  realRows.forEach(row => {
    // Pad row to 26 columns to match header
    const padded = [...row.data];
    while (padded.length < 26) padded.push('');
    cleanData.push(padded);
  });
  
  console.log(`Prepared ${cleanData.length} rows for clean sheet`);
  
  // First, clear the existing clean sheet or create a new one
  // Check if "Clean Leads" sheet exists
  const { data: sheetInfo } = await request({
    url: 'https://sheets.googleapis.com/v4/spreadsheets/11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4',
    headers: { Authorization: `Bearer ${token}` },
  });
  
  const existingSheet = sheetInfo.sheets.find(s => s.properties.title === 'Clean Leads');
  let targetSheetId;
  
  if (existingSheet) {
    targetSheetId = existingSheet.properties.sheetId;
    console.log('Found existing Clean Leads sheet');
    
    // Clear existing data
    await request({
      url: 'https://sheets.googleapis.com/v4/spreadsheets/11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4:batchUpdate',
      method: 'POST',
      headers: { 
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      data: {
        requests: [{
          updateCells: {
            range: {
              sheetId: targetSheetId,
              startRowIndex: 0,
              startColumnIndex: 0,
            },
            fields: 'userEnteredValue'
          }
        }]
      }
    });
    console.log('Cleared existing Clean Leads sheet');
  } else {
    // Create new sheet
    const { data: newSheet } = await request({
      url: 'https://sheets.googleapis.com/v4/spreadsheets/11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4:batchUpdate',
      method: 'POST',
      headers: { 
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      data: {
        requests: [{
          addSheet: {
            properties: {
              title: 'Clean Leads',
              gridProperties: {
                rowCount: cleanData.length + 100,
                columnCount: 26
              }
            }
          }
        }]
      }
    });
    targetSheetId = newSheet.replies[0].addSheet.properties.sheetId;
    console.log('Created new Clean Leads sheet');
  }
  
  // Write data in batches of 500 rows
  const BATCH_SIZE = 500;
  for (let i = 0; i < cleanData.length; i += BATCH_SIZE) {
    const batch = cleanData.slice(i, i + BATCH_SIZE);
    const endRow = i + batch.length;
    
    console.log(`Writing rows ${i + 1} to ${endRow}...`);
    
    await request({
      url: `https://sheets.googleapis.com/v4/spreadsheets/11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4/values/Clean%20Leads!A${i + 1}:Z${endRow}?valueInputOption=RAW`,
      method: 'PUT',
      headers: { 
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      data: {
        values: batch
      }
    });
    
    console.log(`  ✓ Written ${batch.length} rows`);
  }
  
  console.log(`\n✓ Clean sheet created with ${cleanData.length} rows (${realRows.length} leads + 1 header)`);
  console.log('Sheet: Clean Leads in Rank Ray Lead Tracker');
}

main().catch(err => {
  console.error('Error:', err.message);
  if (err.response?.data) console.error('Details:', JSON.stringify(err.response.data, null, 2));
  process.exit(1);
});
