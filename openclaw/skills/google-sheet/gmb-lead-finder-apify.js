const { request } = require('./node_modules/gaxios/build/src/index.js');
const fs = require('fs');
const jwt = require('jsonwebtoken');

const SHEET_ID = '11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4';
const APIFY_API_KEY = process.env.APIFY_API_KEY || '';

// Country-specific config
const COUNTRY = process.argv[2] || 'USA';

const CITIES = {
  'USA': ['New York NY', 'Los Angeles CA', 'Chicago IL', 'Houston TX', 'Phoenix AZ',
          'Philadelphia PA', 'San Antonio TX', 'San Diego CA', 'Dallas TX', 'San Jose CA'],
  'Canada': ['Toronto ON', 'Montreal QC', 'Vancouver BC', 'Calgary AB', 'Edmonton AB',
             'Ottawa ON', 'Winnipeg MB', 'Quebec City QC', 'Hamilton ON', 'Kitchener ON'],
  'Australia': ['Sydney NSW', 'Melbourne VIC', 'Brisbane QLD', 'Perth WA', 'Adelaide SA',
                'Gold Coast QLD', 'Newcastle NSW', 'Canberra ACT', 'Sunshine Coast QLD', 'Wollongong NSW'],
  'UAE': ['Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Al Ain', 'Ras Al Khaimah',
          'Fujairah', 'Umm Al Quwain', 'Dibba', 'Khor Fakkan'],
  'UK': ['London', 'Manchester', 'Birmingham', 'Leeds', 'Glasgow',
         'Liverpool', 'Newcastle', 'Sheffield', 'Bristol', 'Cardiff']
};

const INDUSTRIES = [
  'plumber', 'dentist', 'lawyer', 'physiotherapist', 'roofing contractor',
  'auto repair', 'salon', 'electrician', 'hvac', 'accountant',
  'landscaping', 'cleaning service', 'carpenter', 'painter', 'handyman',
  'restaurant', 'cafe', 'barber', 'mechanic', 'realtor',
  'insurance agent', 'mortgage broker', 'financial advisor', ' chiropractor',
  'massage therapist', 'pet groomer', 'towing service', 'locksmith', 'appliance repair'
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
  const { data } = await request({
    url: `https://sheets.googleapis.com/v4/spreadsheets/${SHEET_ID}/values/Lead%20Pipeline!A1:Z1:append?valueInputOption=RAW&insertDataOption=INSERT_ROWS`,
    method: 'POST',
    headers: { Authorization: `Bearer ${token}`, 'Content-Type': 'application/json' },
    data: { values }
  });
  return data.updates?.updatedRange || 'unknown';
}

async function runApifyScraper(searchString, location) {
  if (!APIFY_API_KEY) {
    console.error('No APIFY_API_KEY found. Set it in master-env.env');
    return [];
  }

  try {
    // Use compass Google Maps scraper (actor ID: nwua9Gu5YrADL7ZDj)
    const startRes = await request({
      url: 'https://api.apify.com/v2/acts/nwua9Gu5YrADL7ZDj/runs',
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${APIFY_API_KEY}`,
        'Content-Type': 'application/json',
      },
      data: {
        searchStringsArray: [`${searchString} in ${location}`],
        maxCrawledPlaces: 20,
      },
    });

    const runId = startRes.data.id;
    console.log(`  Apify run started: ${runId} for "${searchString} in ${location}"`);

    // Wait for completion (max 90s)
    let status = 'RUNNING';
    let attempts = 0;
    while (status === 'RUNNING' && attempts < 45) {
      await sleep(2000);
      const statusRes = await request({
        url: `https://api.apify.com/v2/actor-runs/${runId}`,
        headers: { 'Authorization': `Bearer ${APIFY_API_KEY}` },
      });
      status = statusRes.data.status;
      attempts++;
    }

    if (status !== 'SUCCEEDED') {
      console.error(`  Apify run failed with status: ${status}`);
      return [];
    }

    // Get results
    const datasetRes = await request({
      url: `https://api.apify.com/v2/actor-runs/${runId}/dataset/items`,
      headers: { 'Authorization': `Bearer ${APIFY_API_KEY}` },
    });

    return datasetRes.data || [];
  } catch (err) {
    console.error(`  Apify error: ${err.message}`);
    return [];
  }
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

async function main() {
  console.log(`=== GMB Lead Finder (REAL) [${COUNTRY}] - ${new Date().toISOString()} ===`);

  if (!APIFY_API_KEY) {
    console.error('ERROR: APIFY_API_KEY not set. Add to master-env.env');
    process.exit(1);
  }

  const token = await getAccessToken();
  const today = new Date().toISOString().split('T')[0];

  // Get existing businesses to avoid duplicates
  const existingRows = await readSheet(token, 'Lead Pipeline!C2:C5000');
  const existingBusinesses = new Set();
  existingRows.forEach(row => {
    if (row[0]) existingBusinesses.add(row[0].trim().toLowerCase());
  });
  console.log(`Found ${existingBusinesses.size} existing businesses in sheet`);

  // Pick random city+industry combos
  const cities = CITIES[COUNTRY];
  if (!cities) {
    console.error(`Unknown country: ${COUNTRY}`);
    process.exit(1);
  }

  const combos = [];
  for (const city of cities) {
    for (const industry of INDUSTRIES) {
      combos.push({ city, industry });
    }
  }

  // Shuffle and pick 15 combos (to get more leads)
  for (let i = combos.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [combos[i], combos[j]] = [combos[j], combos[i]];
  }

  const dailyBatch = combos.slice(0, 15);
  console.log(`Processing ${dailyBatch.length} combinations for ${COUNTRY}`);

  const newLeads = [];
  let count = 0;
  let totalScraped = 0;
  let noWebsiteCount = 0;

  for (const { city, industry } of dailyBatch) {
    count++;
    console.log(`\n[${count}/${dailyBatch.length}] Searching: ${industry} in ${city}`);

    // Run Apify scraper
    const results = await runApifyScraper(industry, `${city}, ${COUNTRY}`);
    totalScraped += results.length;

    for (const place of results) {
      const name = place.title || '';
      const phone = place.phone || '';
      const address = place.address || `${place.street || ''}, ${place.city || ''}, ${place.state || ''} ${place.postalCode || ''}`;
      const website = place.website || '';
      const rating = place.totalScore || '';
      const reviews = place.reviewsCount || '';
      const category = place.categoryName || (place.categories && place.categories[0]) || industry;

      // Skip if already exists
      if (name && existingBusinesses.has(name.trim().toLowerCase())) {
        console.log(`    SKIP (duplicate): ${name}`);
        continue;
      }

      // Grade based on website presence
      let grade = 'B';
      let notes = '';
      
      if (!website || website.length < 4) {
        grade = 'A';
        notes = `NO WEBSITE. Rating: ${rating}/5, Reviews: ${reviews}. Needs web presence + SEO.`;
        noWebsiteCount++;
        console.log(`    ADDED (A-grade, no website): ${name} | ${phone || 'no phone'}`);
      } else {
        console.log(`    SKIP (has website): ${name} - ${website.substring(0, 40)}`);
        continue;
      }

      const leadId = `RR-${COUNTRY.substring(0,2).toUpperCase()}-${today.replace(/-/g,'')}-${String(noWebsiteCount).padStart(3,'0')}`;

      const lead = [
        leadId,
        today,
        name,
        '', // email - will be enriched later
        phone,
        address,
        website || 'NO WEBSITE',
        '',
        category,
        `${city}, ${COUNTRY}`,
        notes,
        '',
        grade,
        'New Lead',
        '',
        'No',
        'Pending',
        '',
        '',
        `Apify Google Maps: ${industry} in ${city}. Place ID: ${place.placeId || 'N/A'}`,
        '', '', '', '', '', '', '',
      ];

      newLeads.push(lead);
      existingBusinesses.add(name.trim().toLowerCase());
    }

    // Rate limit between requests
    await sleep(3000);
  }

  // Save to Google Sheet
  if (newLeads.length > 0) {
    const startRow = await appendToSheet(token, newLeads);
    console.log(`\n✓ SUCCESS: Appended ${newLeads.length} real leads to sheet`);
    console.log(`  Total scraped: ${totalScraped}`);
    console.log(`  No website (kept): ${noWebsiteCount}`);
    console.log(`  Duplicates skipped: ${totalScraped - noWebsiteCount - newLeads.length}`);
    console.log(`  Sheet range: ${startRow}`);
  } else {
    console.log('\n⚠ No new leads found without websites');
    console.log(`  Total scraped: ${totalScraped}`);
  }

  console.log('\nDone');
}

main().catch(err => {
  console.error('Fatal error:', err);
  process.exit(1);
});
