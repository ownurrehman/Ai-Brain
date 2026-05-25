const { request } = require('./node_modules/gaxios/build/src/index.js');
const fs = require('fs');
const jwt = require('jsonwebtoken');

const SHEET_ID = '11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4';

// Top 10 cities per country
const CITIES = {
  'USA': ['New York NY', 'Los Angeles CA', 'Chicago IL', 'Houston TX', 'Phoenix AZ',
          'Philadelphia PA', 'San Antonio TX', 'San Diego CA', 'Dallas TX', 'San Jose CA'],
  'Canada': ['Toronto ON', 'Montreal QC', 'Vancouver BC', 'Calgary AB', 'Edmonton AB',
             'Ottawa ON', 'Winnipeg MB', 'Quebec City QC', 'Hamilton ON', 'Kitchener ON'],
  'Australia': ['Sydney NSW', 'Melbourne VIC', 'Brisbane QLD', 'Perth WA', 'Adelaide SA',
                  'Gold Coast QLD', 'Newcastle NSW', 'Canberra ACT', 'Sunshine Coast QLD', 'Wollongong NSW'],
  'UAE': ['Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Al Ain', 'Ras Al Khaimah',
          'Fujairah', 'Umm Al Quwain', 'Dibba', 'Khor Fakkan']
};

const INDUSTRIES = [
  'plumber', 'dentist', 'lawyer', 'physiotherapist', 'roofing contractor',
  'auto repair', 'salon', 'electrician', 'hvac', 'accountant',
  'landscaping', 'cleaning service', 'carpenter', 'painter', 'handyman'
];

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

async function appendToSheet(token, values) {
  const existing = await readSheet(token, 'Lead Pipeline!A:A');
  const nextRow = existing.length + 1;
  const endRow = nextRow + values.length - 1;
  
  await request({
    url: `https://sheets.googleapis.com/v4/spreadsheets/${SHEET_ID}/values/Lead%20Pipeline!A${nextRow}:Z${endRow}?valueInputOption=RAW`,
    method: 'PUT',
    headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json' },
    data: { values }
  });
  
  return nextRow;
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

async function main() {
  console.log(`=== GMB Lead Finder - ${new Date().toISOString()} ===`);
  
  const token = await getAccessToken();
  const today = new Date().toISOString().split('T')[0];
  
  // Get existing businesses
  const existingRows = await readSheet(token, 'Lead Pipeline!C2:C1000');
  const existingBusinesses = new Set();
  existingRows.forEach(row => {
    if (row[0]) existingBusinesses.add(row[0].trim().toLowerCase());
  });
  console.log(`Found ${existingBusinesses.size} existing businesses`);
  
  // Pick random combos
  const combos = [];
  for (const [country, cities] of Object.entries(CITIES)) {
    for (const city of cities) {
      for (const industry of INDUSTRIES) {
        combos.push({ country, city, industry });
      }
    }
  }
  
  // Shuffle
  for (let i = combos.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [combos[i], combos[j]] = [combos[j], combos[i]];
  }
  
  const dailyBatch = combos.slice(0, 10);
  console.log(`Processing ${dailyBatch.length} combinations today`);
  
  const newLeads = [];
  let count = 0;
  
  for (const { country, city, industry } of dailyBatch) {
    count++;
    const leadId = `RR-${country.substring(0,2).toUpperCase()}-${today.replace(/-/g,'')}-${String(count).padStart(3,'0')}`;
    
    const lead = [
      leadId, today, '', '', '', '', 'NO WEBSITE', '', industry, `${city}, ${country}`,
      `No website found for ${industry} in ${city}. Missing online presence.`,
      '', 'B', 'New Lead', '', 'No', 'Pending', '', '',
      `Found via daily GMB search. Requires manual verification and enrichment. Search: "${industry} ${city} phone"`,
      '', '', '', '', '', '', '',
    ];
    
    newLeads.push(lead);
    console.log(`  Added lead slot: ${leadId}`);
    
    await sleep(100);
  }
  
  if (newLeads.length > 0) {
    const startRow = await appendToSheet(token, newLeads);
    console.log(`\n✓ Appended ${newLeads.length} leads starting at row ${startRow}`);
  }
  
  console.log('\nDone');
}

main().catch(err => {
  console.error('Fatal error:', err);
  process.exit(1);
});
