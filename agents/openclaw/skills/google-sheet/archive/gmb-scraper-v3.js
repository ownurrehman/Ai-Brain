#!/usr/bin/env node
/**
 * GMB Lead Scraper v3 - Uses OpenClaw browser tool for stealth scraping
 * No paid APIs. Delegates to browser automation via OpenClaw's managed browser.
 */

const { request } = require('./node_modules/gaxios/build/src/index.js');
const fs = require('fs');
const jwt = require('jsonwebtoken');

const SHEET_ID = '11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4';

const COUNTRY_CONFIGS = {
  'USA': {
    cities: ['New York NY', 'Los Angeles CA', 'Chicago IL', 'Houston TX', 'Phoenix AZ',
            'Philadelphia PA', 'San Antonio TX', 'San Diego CA', 'Dallas TX', 'San Jose CA'],
    industries: ['plumber', 'dentist', 'lawyer', 'physiotherapist', 'roofing contractor',
                'auto repair', 'salon', 'electrician', 'hvac', 'accountant',
                'landscaping', 'cleaning service', 'carpenter', 'painter', 'handyman']
  },
  'Canada': {
    cities: ['Toronto ON', 'Montreal QC', 'Vancouver BC', 'Calgary AB', 'Edmonton AB',
            'Ottawa ON', 'Winnipeg MB', 'Quebec City QC', 'Hamilton ON', 'Kitchener ON'],
    industries: ['plumber', 'dentist', 'lawyer', 'physiotherapist', 'roofing contractor',
                'auto repair', 'salon', 'electrician', 'hvac', 'accountant',
                'landscaping', 'cleaning service', 'carpenter', 'painter', 'handyman']
  },
  'UK': {
    cities: ['London', 'Manchester', 'Birmingham', 'Leeds', 'Glasgow',
            'Liverpool', 'Newcastle', 'Sheffield', 'Bristol', 'Cardiff'],
    industries: ['plumber', 'dentist', 'solicitor', 'physiotherapist', 'roofing contractor',
                'auto repair', 'hair salon', 'electrician', 'gas engineer', 'accountant',
                'landscaping', 'cleaning service', 'carpenter', 'painter', 'handyman']
  },
  'Australia': {
    cities: ['Sydney NSW', 'Melbourne VIC', 'Brisbane QLD', 'Perth WA', 'Adelaide SA',
            'Gold Coast QLD', 'Newcastle NSW', 'Canberra ACT', 'Sunshine Coast QLD', 'Wollongong NSW'],
    industries: ['plumber', 'dentist', 'lawyer', 'physiotherapist', 'roofing contractor',
                'auto repair', 'salon', 'electrician', 'hvac', 'accountant',
                'landscaping', 'cleaning service', 'carpenter', 'painter', 'handyman']
  },
  'UAE': {
    cities: ['Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Al Ain',
            'Ras Al Khaimah', 'Fujairah', 'Umm Al Quwain', 'Dibba', 'Khor Fakkan'],
    industries: ['plumber', 'dentist', 'lawyer', 'physiotherapist', 'maintenance company',
                'auto repair', 'salon', 'electrician', 'hvac', 'accountant',
                'landscaping', 'cleaning service', 'carpenter', 'painter', 'handyman']
  }
};

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

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

/**
 * Use serper skill for Google Maps search via OpenClaw
 * This is a skill-based approach that doesn't require paid APIs
 */
async function searchGMB(industry, city, country) {
  const query = `${industry} ${city}`;
  console.log(`  Searching: "${query}"`);
  
  // Use web_search via OpenClaw's built-in capability
  // We'll search for business listings and extract from results
  try {
    // For now, use a lightweight approach: search and parse
    // In a full implementation, this would use the browser tool
    console.log(`    [Simulated search for ${query}]`);
    return [];
  } catch (err) {
    console.log(`    Error: ${err.message}`);
    return [];
  }
}

async function main() {
  const country = process.argv[2] || 'USA';
  const config = COUNTRY_CONFIGS[country];
  
  if (!config) {
    console.error(`Unknown country: ${country}`);
    console.error(`Available: ${Object.keys(COUNTRY_CONFIGS).join(', ')}`);
    process.exit(1);
  }
  
  console.log(`=== GMB Lead Scraper v3 [${country}] - ${new Date().toISOString()} ===\n`);
  
  const token = await getAccessToken();
  
  // Get existing businesses
  const existingRows = await readSheet(token, 'Lead Pipeline!C2:C2000');
  const existingBusinesses = new Set();
  existingRows.forEach(row => {
    if (row[0]) existingBusinesses.add(row[0].trim().toLowerCase());
  });
  console.log(`Found ${existingBusinesses.size} existing businesses in sheet`);
  
  const today = new Date().toISOString().split('T')[0];
  const newLeads = [];
  
  // For v3, we acknowledge the limitation: Google Maps scraping is hard without proper tooling
  // The real fix is to use OpenClaw's browser tool in the cron payload, not in a Node script
  
  console.log('\n⚠️  NOTE: v3 requires OpenClaw browser tool integration.');
  console.log('This script should be called via cron with browser snapshots.');
  console.log('For now, this is a placeholder that documents the pipeline.\n');
  
  if (newLeads.length > 0) {
    const startRow = await appendToSheet(token, newLeads);
    console.log(`Appended ${newLeads.length} leads`);
  }
  
  console.log('Done');
}

main().catch(err => {
  console.error('Fatal error:', err);
  process.exit(1);
});
