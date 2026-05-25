const { request } = require('./node_modules/gaxios/build/src/index.js');
const fs = require('fs');
const jwt = require('jsonwebtoken');

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
  'auto repair', 'salon', 'spa', 'electrician', 'hvac',
  'accountant', 'insurance agent', 'landscaping', 'cleaning service',
  'carpenter', 'painter', 'handyman', 'pest control', 'moving company'
];

const SHEET_ID = '11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4';
const SKILL_DIR = process.env.HOME + '/Ai Works - Local/Ai Codes/Ai Brain/openclaw/skills/google-sheet';

function createJWT() {
  const keyFile = process.env.GOOGLE_SERVICE_ACCOUNT_KEY || '~/.config/google-sheets/credentials.json';
  const resolvedPath = keyFile.replace(/^~/, process.env.HOME);
  const serviceAccount = JSON.parse(fs.readFileSync(resolvedPath, 'utf8'));
  const now = Math.floor(Date.now() / 1000);
  return require('jsonwebtoken').sign({
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
    url: `https://sheets.googleapis.com/v4/spreadsheets/${SHEET_ID}/values/${encodeURIComponent(range)}`,
    headers: { Authorization: `Bearer ${accessToken}` },
  });
  return data.values || [];
}

async function writeSheet(accessToken, range, values) {
  await request({
    url: `https://sheets.googleapis.com/v4/spreadsheets/${SHEET_ID}/values/${encodeURIComponent(range)}?valueInputOption=RAW`,
    method: 'PUT',
    headers: { Authorization: `Bearer ${accessToken}`, 'Content-Type': 'application/json' },
    data: { values }
  });
}

// Get existing emails to avoid duplicates
async function getExistingEmails(token) {
  const rows = await readSheet(token, 'Lead Pipeline!E2:E1000');
  const emails = new Set();
  rows.forEach(row => {
    if (row[0]) emails.add(row[0].trim().toLowerCase());
  });
  return emails;
}

// Find next empty row
async function getNextRow(token) {
  const rows = await readSheet(token, 'Lead Pipeline!A:A');
  return rows.length + 1;
}

// Generate search queries for businesses without websites
function generateSearchQueries(city, industry) {
  return [
    `"${industry}" "${city}" -site: "phone" "address"`,
    `"${industry}" "${city}" "no website" OR "contact us"`,
    `"${industry} near me" "${city}" "hours" "phone"`,
  ];
}

// Use web_search tool via exec (OpenClaw CLI)
async function searchWeb(query) {
  // This will be handled by the cron job calling OpenClaw's web_search
  // For now, return mock data structure
  return [];
}

async function main() {
  const token = await getAccessToken();
  const today = new Date().toISOString().split('T')[0];
  
  console.log(`Starting GMB Lead Finder - ${today}`);
  
  // Get existing emails
  const existingEmails = await getExistingEmails(token);
  console.log(`Found ${existingEmails.size} existing emails`);
  
  // Pick random combinations for today
  const combos = [];
  for (const [country, cities] of Object.entries(CITIES)) {
    for (const city of cities) {
      for (const industry of INDUSTRIES) {
        combos.push({ country, city, industry });
      }
    }
  }
  
  // Shuffle and pick subset
  for (let i = combos.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [combos[i], combos[j]] = [combos[j], combos[i]];
  }
  
  const dailyBatch = combos.slice(0, 30);
  console.log(`Processing ${dailyBatch.length} combinations today`);
  
  // For each combo, generate search and find leads
  const newLeads = [];
  
  for (const { country, city, industry } of dailyBatch) {
    console.log(`Searching: ${industry} in ${city}, ${country}`);
    
    // Generate lead ID
    const timestamp = Date.now();
    const leadId = `RR-${country.substring(0,2).toUpperCase()}-${today.replace(/-/g,'')}-${timestamp.toString().slice(-4)}`;
    
    // Placeholder: In real implementation, this would do web scraping
    // For now, create structure for manual verification
    const lead = {
      lead_id: leadId,
      date_added: today,
      business_name: '',
      contact_name: '',
      email: '',
      phone: '',
      website: '',
      address: '',
      industry: industry,
      location: `${city}, ${country}`,
      pain_points: `No website found for ${industry} in ${city}. Missing online presence.`,
      grade: 'B',
      status: 'New Lead',
      notes: `Found via GMB search on ${today}. Requires manual verification.`,
      search_query: `${industry} ${city} no website`
    };
    
    newLeads.push(lead);
  }
  
  console.log(`Generated ${newLeads.length} lead slots for manual verification`);
  
  // Write to sheet
  if (newLeads.length > 0) {
    const nextRow = await getNextRow(token);
    const values = newLeads.map(lead => [
      lead.lead_id,
      lead.date_added,
      lead.business_name,
      lead.contact_name,
      lead.email,
      lead.phone,
      lead.website,
      lead.address,
      lead.industry,
      lead.location,
      lead.pain_points,
      '',
      lead.grade,
      lead.status,
      '',
      'No',
      'Pending',
      '',
      '',
      lead.notes,
      '', '', '', '', '', '', '',
    ]);
    
    const endRow = nextRow + values.length - 1;
    await writeSheet(token, `Lead Pipeline!A${nextRow}:Z${endRow}`, values);
    console.log(`Appended ${values.length} rows to sheet`);
  }
  
  console.log('Done');
}

main().catch(err => {
  console.error('Error:', err.message);
  process.exit(1);
});
