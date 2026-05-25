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

// Check if email looks like a real business email
function isSuspiciousEmail(email) {
  if (!email || email.length < 5) return true;
  
  // Suspicious patterns
  const suspicious = [
    /info@(?:test|fake|example|sample|demo)/i,
    /test@/i,
    /fake@/i,
    /example@/i,
    /@gmail\.com$/i,  // Most business leads shouldn't use gmail
    /@yahoo\.com$/i,
    /@hotmail\.com$/i,
    /@outlook\.com$/i,
    /noreply@/i,
    /admin@localhost/i,
    /^[0-9]+@/i,
  ];
  
  for (const pattern of suspicious) {
    if (pattern.test(email)) return true;
  }
  
  return false;
}

// Check if business name looks fake/test
function isSuspiciousBusiness(name) {
  if (!name) return true;
  const lower = name.toLowerCase();
  
  const suspicious = [
    /rotation \d/i,
    /batch \d/i,
    /test lead/i,
    /fake/i,
    /^2026-/i,
    /^run \d/i,
    /duplicate check/i,
    /group \d/i,
    /^row \d+/i,
    /^sample/i,
    /^demo/i,
    /^test/i,
    /^placeholder/i,
    /dubai\/uae group/i,
    /usa batch/i,
    /canada batch/i,
  ];
  
  for (const pattern of suspicious) {
    if (pattern.test(lower)) return true;
  }
  
  // Check for date-like business names (2026-05-06 etc)
  if (/^\d{4}-\d{2}-\d{2}/.test(name)) return true;
  
  return false;
}

// Check if it's a real lead (has actual contact info)
function isRealLead(row) {
  const leadId = (row[0] || '').trim();
  const business = (row[2] || '').trim();
  const name = (row[3] || '').trim();
  const email = (row[4] || '').trim().toLowerCase();
  const phone = (row[5] || '').trim();
  const website = (row[6] || '').trim();
  const status = (row[13] || '').trim().toLowerCase();
  const notes = (row[20] || '').toLowerCase();
  
  // Must have at least business name + one contact method
  if (!business || business.length < 2) return false;
  
  // Check if business name is suspicious
  if (isSuspiciousBusiness(business)) return false;
  
  // Check if email is suspicious
  if (email && isSuspiciousEmail(email)) {
    // If email is suspicious, check if we have other real contact info
    const hasRealPhone = phone && phone.length > 6 && !phone.match(/^\d{1,5}$/);
    const hasRealWebsite = website && website.length > 10 && website.includes('.');
    
    if (!hasRealPhone && !hasRealWebsite) return false;
  }
  
  // Must have at least email, phone, or website
  const hasEmail = email && email.includes('@') && email.includes('.');
  const hasPhone = phone && phone.length > 6;
  const hasWebsite = website && website.includes('.') && website.length > 8;
  
  if (!hasEmail && !hasPhone && !hasWebsite) return false;
  
  // Check for "test" in notes or status
  if (notes.includes('test') && !notes.includes('testimonial')) return false;
  if (status.includes('test')) return false;
  
  // Lead ID check - should look like a real lead ID
  // BUT: if we have real business + contact info, we can be lenient on lead ID
  const hasStrongContact = hasEmail || (phone && phone.length > 6) || (website && website.includes('.'));
  
  if (!leadId || leadId.length < 3) {
    // No lead ID, but has strong contact info -> still real
    if (!hasStrongContact) return false;
  }
  
  if (leadId.toLowerCase().includes('test')) return false;
  
  return true;
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
  
  console.log(`Total rows: ${allRows.length}\n`);
  
  const realLeads = [];
  const fakeLeads = [];
  const duplicateRealLeads = [];
  const emailMap = new Map();
  
  allRows.forEach(row => {
    const leadId = row.data[0] || '';
    const business = row.data[2] || '';
    const email = (row.data[4] || '').trim().toLowerCase();
    
    if (!isRealLead(row.data)) {
      fakeLeads.push({
        idx: row.idx,
        leadId,
        business,
        email,
        reason: getFakeReason(row.data)
      });
    } else {
      // Track duplicates among REAL leads
      if (email && email.length > 3) {
        if (emailMap.has(email)) {
          emailMap.get(email).push(row);
        } else {
          emailMap.set(email, [row]);
        }
      }
      realLeads.push(row);
    }
  });
  
  // Find duplicates among real leads
  emailMap.forEach((rows, email) => {
    if (rows.length > 1) {
      duplicateRealLeads.push({
        email,
        count: rows.length,
        rows: rows.map(r => ({ idx: r.idx, leadId: r.data[0], business: r.data[2] }))
      });
    }
  });
  
  console.log(`=== REAL LEADS: ${realLeads.length} ===`);
  console.log(`=== FAKE/JUNK LEADS: ${fakeLeads.length} ===`);
  console.log(`=== DUPLICATE REAL LEADS: ${duplicateRealLeads.length} unique emails with duplicates\n`);
  
  console.log('=== FAKE LEAD EXAMPLES (first 50) ===');
  fakeLeads.slice(0, 50).forEach(f => {
    console.log(`Row ${f.idx}: ${f.leadId} | ${f.business} | ${f.email} | ${f.reason}`);
  });
  
  if (fakeLeads.length > 50) {
    console.log(`... and ${fakeLeads.length - 50} more fake leads`);
  }
  
  console.log('\n=== DUPLICATE REAL LEADS (first 30) ===');
  duplicateRealLeads.slice(0, 30).forEach(d => {
    console.log(`\n${d.email} (${d.count} duplicates):`);
    d.rows.forEach(r => console.log(`  Row ${r.idx}: ${r.leadId} | ${r.business}`));
  });
  
  // Calculate rows to delete
  const toDelete = new Set();
  fakeLeads.forEach(f => toDelete.add(f.idx));
  
  // Mark duplicates for deletion (keep first occurrence)
  duplicateRealLeads.forEach(dup => {
    dup.rows.slice(1).forEach(r => toDelete.add(r.idx));
  });
  
  console.log(`\n=== TOTAL ROWS TO DELETE: ${toDelete.size} ===`);
  console.log(`Remaining real leads: ${allRows.length - toDelete.size}`);
  
  const deleteList = Array.from(toDelete).sort((a,b) => a-b);
  console.log('\nFirst 50 rows to delete:', deleteList.slice(0, 50).join(', '));
  
  fs.writeFileSync('deep-clean-results.json', JSON.stringify({
    totalRows: allRows.length,
    realLeads: realLeads.length,
    fakeLeads: fakeLeads.map(f => f.idx),
    duplicateEmails: duplicateRealLeads.map(d => d.rows.slice(1).map(r => r.idx)).flat(),
    allToDelete: deleteList,
    remaining: allRows.length - toDelete.size,
    fakeDetails: fakeLeads
  }, null, 2));
  
  console.log('\nSaved to deep-clean-results.json');
}

function getFakeReason(row) {
  const leadId = (row[0] || '').trim();
  const business = (row[2] || '').trim();
  const name = (row[3] || '').trim();
  const email = (row[4] || '').trim();
  const phone = (row[5] || '').trim();
  const website = (row[6] || '').trim();
  const notes = (row[20] || '').toLowerCase();
  
  if (!business || business.length < 2) return 'No business name';
  if (isSuspiciousBusiness(business)) return `Suspicious business name: "${business}"`;
  if (!email && !phone && !website) return 'No contact info at all';
  
  const hasEmail = email && email.includes('@') && email.includes('.');
  const hasPhone = phone && phone.length > 6;
  const hasWebsite = website && website.includes('.') && website.length > 8;
  
  if (!hasEmail && !hasPhone && !hasWebsite) return 'Invalid contact info format';
  
  if (email && isSuspiciousEmail(email)) {
    const hasRealPhone = phone && phone.length > 6 && !phone.match(/^\d{1,5}$/);
    const hasRealWebsite = website && website.length > 10 && website.includes('.');
    if (!hasRealPhone && !hasRealWebsite) return `Suspicious email only: ${email}`;
  }
  
  if (notes.includes('test') && !notes.includes('testimonial')) return 'Test data in notes';
  if (leadId.toLowerCase().includes('test')) return 'Test lead ID';
  
  return 'Unknown issue';
}

main().catch(console.error);
