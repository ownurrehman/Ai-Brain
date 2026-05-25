// Simple analysis using gaxios directly
const { request } = require('./node_modules/gaxios/build/src/index.js');
const fs = require('fs');

const keyFile = process.env.GOOGLE_SERVICE_ACCOUNT_KEY || '~/.config/google-sheets/credentials.json';
const resolvedPath = keyFile.replace(/^~/, require('os').homedir());
const serviceAccount = JSON.parse(fs.readFileSync(resolvedPath, 'utf8'));

const jwt = require('jsonwebtoken');

function createJWT() {
  const now = Math.floor(Date.now() / 1000);
  const token = jwt.sign({
    iss: serviceAccount.client_email,
    scope: 'https://www.googleapis.com/auth/spreadsheets',
    aud: 'https://oauth2.googleapis.com/token',
    iat: now,
    exp: now + 3600,
  }, serviceAccount.private_key, { algorithm: 'RS256' });
  return token;
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
  
  // Read headers
  const headers = await readSheet(token, 'Lead Pipeline!A1:Z1');
  const headerRow = headers[0];
  console.log('Headers:', headerRow.join(' | '));
  
  // Read all data in batches
  let allRows = [];
  const batchSize = 100;
  let start = 2;
  let hasMore = true;
  
  while (hasMore && start < 3500) {
    const end = start + batchSize - 1;
    const range = `Lead Pipeline!A${start}:Z${end}`;
    const rows = await readSheet(token, range);
    if (!rows || rows.length === 0) {
      hasMore = false;
    } else {
      rows.forEach((row, i) => {
        allRows.push({ idx: start + i, data: row });
      });
      start += rows.length;
      if (rows.length < batchSize) hasMore = false;
    }
  }
  
  console.log(`\nTotal rows read: ${allRows.length}`);
  
  const emailCol = 4; // Column E
  const nameCol = 3;  // Column D
  const bizCol = 2;   // Column C
  const statusCol = 13; // Column N
  
  const emailMap = new Map();
  const nameMap = new Map();
  const bizMap = new Map();
  const junkRows = [];
  const emptyRows = [];
  
  allRows.forEach(row => {
    const email = (row.data[emailCol] || '').trim().toLowerCase();
    const name = (row.data[nameCol] || '').trim().toLowerCase();
    const business = (row.data[bizCol] || '').trim().toLowerCase();
    const status = (row.data[statusCol] || '').trim();
    const leadId = row.data[0] || '';
    
    // Check for completely empty rows
    const hasAnyData = row.data.some(cell => (cell || '').trim().length > 0);
    if (!hasAnyData) {
      emptyRows.push(row.idx);
      return;
    }
    
    // Check for junk: has business name but no email/name/phone/website
    const hasEmail = email.length > 0;
    const hasName = name.length > 0 && name !== 'contact form';
    const hasBusiness = business.length > 0;
    const hasPhone = (row.data[5] || '').trim().length > 0;
    const hasWebsite = (row.data[6] || '').trim().length > 0;
    
    if (!hasEmail && !hasName && !hasPhone && !hasWebsite && hasBusiness) {
      junkRows.push({ 
        idx: row.idx, 
        leadId, 
        business: row.data[bizCol], 
        reason: 'Only business name, no contact info',
        status
      });
    }
    
    if (email && email !== 'info@' && email.length > 3) {
      if (emailMap.has(email)) emailMap.get(email).push(row);
      else emailMap.set(email, [row]);
    }
    
    if (name && name.length > 1 && name !== 'contact form') {
      if (nameMap.has(name)) nameMap.get(name).push(row);
      else nameMap.set(name, [row]);
    }
    
    if (business && business.length > 1) {
      if (bizMap.has(business)) bizMap.get(business).push(row);
      else bizMap.set(business, [row]);
    }
  });
  
  console.log('\n=== EMPTY ROWS ===');
  console.log(`Total empty rows: ${emptyRows.length}`);
  if (emptyRows.length > 0) console.log('Rows:', emptyRows.slice(0, 30).join(', '));
  
  console.log('\n=== JUNK ROWS (no contact info) ===');
  console.log(`Total junk rows: ${junkRows.length}`);
  junkRows.slice(0, 30).forEach(r => {
    console.log(`Row ${r.idx}: ${r.leadId} | ${r.business} | Status: ${r.status} | ${r.reason}`);
  });
  
  console.log('\n=== DUPLICATE EMAILS ===');
  let dupEmailCount = 0;
  const dupEmails = [];
  emailMap.forEach((vals, key) => {
    if (vals.length > 1) {
      dupEmailCount += vals.length;
      dupEmails.push({ email: key, count: vals.length, rows: vals.map(v => ({ idx: v.idx, leadId: v.data[0], biz: v.data[2] })) });
    }
  });
  dupEmails.slice(0, 30).forEach(d => {
    console.log(`\n${d.email} (${d.count} duplicates):`);
    d.rows.forEach(r => console.log(`  Row ${r.idx}: ${r.leadId} | ${r.biz}`));
  });
  console.log(`\nTotal duplicate email entries: ${dupEmailCount}`);
  
  console.log('\n=== DUPLICATE BUSINESS NAMES ===');
  let dupBizCount = 0;
  const dupBiz = [];
  bizMap.forEach((vals, key) => {
    if (vals.length > 1 && key.length > 2) {
      dupBizCount += vals.length;
      dupBiz.push({ biz: key, count: vals.length, rows: vals.map(v => ({ idx: v.idx, leadId: v.data[0], email: v.data[4] })) });
    }
  });
  dupBiz.slice(0, 30).forEach(d => {
    console.log(`\n${d.biz} (${d.count} duplicates):`);
    d.rows.forEach(r => console.log(`  Row ${r.idx}: ${r.leadId} | ${r.email || 'no email'}`));
  });
  console.log(`\nTotal duplicate business entries: ${dupBizCount}`);
  
  // Build delete list
  const toDelete = new Set();
  emptyRows.forEach(r => toDelete.add(r));
  junkRows.forEach(r => toDelete.add(r.idx));
  
  // For duplicates, keep first, delete rest
  emailMap.forEach(vals => {
    if (vals.length > 1) {
      vals.slice(1).forEach(v => toDelete.add(v.idx));
    }
  });
  
  bizMap.forEach(vals => {
    if (vals.length > 1) {
      vals.slice(1).forEach(v => toDelete.add(v.idx));
    }
  });
  
  console.log(`\n=== TOTAL ROWS TO DELETE: ${toDelete.size} ===`);
  const deleteList = Array.from(toDelete).sort((a,b) => a-b);
  console.log('First 100 rows to delete:', deleteList.slice(0, 100).join(', '));
  if (deleteList.length > 100) console.log(`... and ${deleteList.length - 100} more`);
  
  // Save to file
  fs.writeFileSync('rows-to-delete.json', JSON.stringify({
    empty: emptyRows,
    junk: junkRows.map(r => r.idx),
    duplicates: Array.from(toDelete).filter(r => !emptyRows.includes(r) && !junkRows.some(j => j.idx === r)),
    all: deleteList,
  }, null, 2));
  console.log('\nSaved to rows-to-delete.json');
}

main().catch(err => {
  console.error(err);
  process.exit(1);
});
