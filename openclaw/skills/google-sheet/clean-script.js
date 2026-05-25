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
  return data.values;
}

async function main() {
  const token = await getAccessToken();
  
  let allRows = [];
  let start = 2;
  let hasMore = true;
  
  while (hasMore && start < 3500) {
    const range = `Lead Pipeline!A${start}:Z${start + 99}`;
    const rows = await readSheet(token, range);
    if (!rows || rows.length === 0) {
      hasMore = false;
    } else {
      rows.forEach((row, i) => allRows.push({ idx: start + i, data: row }));
      start += rows.length;
      if (rows.length < 100) hasMore = false;
    }
  }
  
  console.log(`Total rows read: ${allRows.length}`);
  
  const emailCol = 4, nameCol = 3, bizCol = 2;
  const emailMap = new Map(), bizMap = new Map();
  const junkRows = [], emptyRows = [];
  
  allRows.forEach(row => {
    const email = (row.data[emailCol] || '').trim().toLowerCase();
    const name = (row.data[nameCol] || '').trim().toLowerCase();
    const business = (row.data[bizCol] || '').trim().toLowerCase();
    const leadId = row.data[0] || '';
    
    const hasAnyData = row.data.some(cell => (cell || '').trim().length > 0);
    if (!hasAnyData) {
      emptyRows.push(row.idx);
      return;
    }
    
    const hasEmail = email.length > 0;
    const hasName = name.length > 0 && name !== 'contact form';
    const hasBusiness = business.length > 0;
    const hasPhone = (row.data[5] || '').trim().length > 0;
    const hasWebsite = (row.data[6] || '').trim().length > 0;
    
    if (!hasEmail && !hasName && !hasPhone && !hasWebsite && hasBusiness) {
      junkRows.push({ idx: row.idx, leadId, business: row.data[bizCol], reason: 'Only business name, no contact info' });
    }
    
    if (email && email.length > 3) {
      if (emailMap.has(email)) emailMap.get(email).push(row);
      else emailMap.set(email, [row]);
    }
    
    if (business && business.length > 1) {
      if (bizMap.has(business)) bizMap.get(business).push(row);
      else bizMap.set(business, [row]);
    }
  });
  
  console.log(`\nEmpty rows: ${emptyRows.length}`);
  console.log(`Junk rows: ${junkRows.length}`);
  
  console.log('\n=== Duplicate Emails ===');
  let dupEmailCount = 0;
  emailMap.forEach((vals, key) => {
    if (vals.length > 1) {
      dupEmailCount += vals.length;
      console.log(`${key}: ${vals.length} duplicates`);
    }
  });
  console.log(`Total duplicate email entries: ${dupEmailCount}`);
  
  console.log('\n=== Duplicate Business ===');
  let dupBizCount = 0;
  bizMap.forEach((vals, key) => {
    if (vals.length > 1 && key.length > 2) {
      dupBizCount += vals.length;
      console.log(`${key}: ${vals.length} duplicates`);
    }
  });
  console.log(`Total duplicate business entries: ${dupBizCount}`);
  
  const toDelete = new Set();
  emptyRows.forEach(r => toDelete.add(r));
  junkRows.forEach(r => toDelete.add(r.idx));
  
  emailMap.forEach(vals => {
    if (vals.length > 1) vals.slice(1).forEach(v => toDelete.add(v.idx));
  });
  
  bizMap.forEach(vals => {
    if (vals.length > 1) vals.slice(1).forEach(v => toDelete.add(v.idx));
  });
  
  console.log(`\n=== TOTAL ROWS TO DELETE: ${toDelete.size} ===`);
  
  fs.writeFileSync('rows-to-delete.json', JSON.stringify({
    empty: emptyRows,
    junk: junkRows.map(r => r.idx),
    all: Array.from(toDelete).sort((a,b) => a-b),
  }, null, 2));
}

main().catch(console.error);
