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

async function main() {
  const token = await getAccessToken();
  const rows = await readSheet(token, 'Lead Pipeline!A2:Z1000');
  
  let fakeCount = 0;
  let emptyCount = 0;
  let realCount = 0;
  const fakeRows = [];
  
  rows.forEach((row, idx) => {
    const rowNum = idx + 2;
    const leadId = row[0] || '';
    const businessName = row[2] || '';
    const phone = row[3] || '';
    const websiteStatus = row[6] || '';
    const industry = row[8] || '';
    const location = row[9] || '';
    const notes = row[10] || '';
    
    // Check if row is completely empty (already cleared)
    const isEmpty = !leadId && !businessName && !phone && !industry && !location;
    
    // Check if fake: NO WEBSITE with no real data
    const isFake = !isEmpty && (
      (websiteStatus === 'NO WEBSITE' && !phone && !businessName) ||
      (notes.includes('Requires manual verification') && !phone) ||
      (notes.includes('No website found for') && !businessName) ||
      (!businessName && !phone && leadId.startsWith('RR-'))
    );
    
    if (isEmpty) {
      emptyCount++;
    } else if (isFake) {
      fakeCount++;
      fakeRows.push({
        rowNum,
        leadId,
        businessName: businessName || '(empty)',
        phone: phone || '(empty)',
        reason: !businessName ? 'no business name' : !phone ? 'no phone' : 'placeholder notes'
      });
    } else {
      realCount++;
    }
  });
  
  console.log(JSON.stringify({
    totalRows: rows.length,
    emptyRows: emptyCount,
    fakeRows: fakeCount,
    realRows: realCount,
    sampleFake: fakeRows.slice(0, 20),
    sampleReal: rows.filter((r) => {
      const businessName = r[2] || '';
      const phone = r[3] || '';
      return businessName && phone;
    }).slice(0, 5).map(r => ({
      name: r[2],
      phone: r[3],
      location: r[9],
      industry: r[8]
    }))
  }, null, 2));
}

main().catch(err => {
  console.error('Error:', err.message);
  process.exit(1);
});
