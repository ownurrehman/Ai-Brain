#!/usr/bin/env node
/**
 * GMB Lead Scraper v2 - Real browser automation using Playwright
 * No paid APIs. Scrapes Google Maps for businesses without websites.
 */

const { chromium } = require('playwright');
const { request } = require('./node_modules/gaxios/build/src/index.js');
const fs = require('fs');
const jwt = require('jsonwebtoken');

const SHEET_ID = '11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4';

// Country configs
const COUNTRY_CONFIGS = {
  'USA': {
    cities: ['New York NY', 'Los Angeles CA', 'Chicago IL', 'Houston TX', 'Phoenix AZ',
            'Philadelphia PA', 'San Antonio TX', 'San Diego CA', 'Dallas TX', 'San Jose CA',
            'Austin TX', 'Jacksonville FL', 'Fort Worth TX', 'Columbus OH', 'Charlotte NC'],
    industries: ['plumber', 'dentist', 'lawyer', 'physiotherapist', 'roofing contractor',
                'auto repair', 'salon', 'electrician', 'hvac', 'accountant',
                'landscaping', 'cleaning service', 'carpenter', 'painter', 'handyman',
                'chiropractor', 'pest control', 'pool service', 'moving company', 'locksmith']
  },
  'Canada': {
    cities: ['Toronto ON', 'Montreal QC', 'Vancouver BC', 'Calgary AB', 'Edmonton AB',
            'Ottawa ON', 'Winnipeg MB', 'Quebec City QC', 'Hamilton ON', 'Kitchener ON',
            'London ON', 'Victoria BC', 'Halifax NS', 'Oshawa ON', 'Windsor ON'],
    industries: ['plumber', 'dentist', 'lawyer', 'physiotherapist', 'roofing contractor',
                'auto repair', 'salon', 'electrician', 'hvac', 'accountant',
                'landscaping', 'cleaning service', 'carpenter', 'painter', 'handyman']
  },
  'UK': {
    cities: ['London', 'Manchester', 'Birmingham', 'Leeds', 'Glasgow',
            'Liverpool', 'Newcastle', 'Sheffield', 'Bristol', 'Cardiff',
            'Edinburgh', 'Belfast', 'Nottingham', 'Southampton', 'Leicester'],
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
 * Scrape Google Maps for businesses in a specific city/industry
 * Returns array of business objects with name, phone, address, hasWebsite
 */
async function scrapeGoogleMaps(browser, industry, city, country) {
  const searchQuery = `${industry} ${city}`;
  console.log(`  Scraping: "${searchQuery}"`);
  
  const context = await browser.newContext({
    userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    viewport: { width: 1920, height: 1080 },
    locale: 'en-US',
    timezoneId: 'America/New_York',
    geolocation: { latitude: 40.7128, longitude: -74.0060 },
    permissions: ['geolocation'],
  });
  
  const page = await context.newPage();
  
  try {
    // Navigate to Google Maps
    const mapsUrl = `https://www.google.com/maps/search/${encodeURIComponent(searchQuery)}`;
    await page.goto(mapsUrl, { waitUntil: 'networkidle', timeout: 30000 });
    
    // Wait for results to load
    await sleep(3000);
    
    // Check for CAPTCHA or block
    const blocked = await page.$('text=unusual traffic') || await page.$('text=verify you\'re not a robot');
    if (blocked) {
      console.log(`    ⚠️ CAPTCHA detected for "${searchQuery}"`);
      await context.close();
      return [];
    }
    
    // Extract business listings
    const businesses = await page.evaluate(() => {
      const results = [];
      
      // Try multiple selectors for business cards
      const cards = document.querySelectorAll('[data-result-index], .bfdHYd, [jsaction*="click:PLACE_LIST"]');
      
      cards.forEach(card => {
        // Business name
        const nameEl = card.querySelector('h3, .qBF1Pd, [role="heading"] span, .fontHeadlineSmall');
        const name = nameEl ? nameEl.textContent.trim() : '';
        
        // Skip if no name or it's a category/heading
        if (!name || name.length < 2 || name.includes('Results') || name.includes('Businesses')) return;
        
        // Phone
        const phoneEl = card.querySelector('[data-item-id*="phone"], [aria-label*="Phone"]');
        const phone = phoneEl ? phoneEl.getAttribute('aria-label')?.replace('Phone: ', '') || phoneEl.textContent : '';
        
        // Address
        const addressEl = card.querySelector('[data-item-id*="address"], .W4Efsd span, [aria-label*="Address"]');
        const address = addressEl ? addressEl.getAttribute('aria-label')?.replace('Address: ', '') || addressEl.textContent : '';
        
        // Check for website button/link
        const websiteEl = card.querySelector('[data-item-id*="website"], [aria-label*="Website"]');
        const hasWebsite = !!websiteEl;
        
        // Rating
        const ratingEl = card.querySelector('.MW4etd, .YDIN4c');
        const rating = ratingEl ? ratingEl.textContent.trim() : '';
        
        // Reviews count
        const reviewsEl = card.querySelector('.UY7F9, .ZkP5Je');
        const reviews = reviewsEl ? reviewsEl.textContent.replace(/[()]/g, '').trim() : '';
        
        if (name && name.length > 1) {
          results.push({
            name,
            phone: phone.replace(/\s+/g, ' ').trim(),
            address: address.replace(/\s+/g, ' ').trim(),
            hasWebsite,
            rating,
            reviews
          });
        }
      });
      
      return results;
    });
    
    await context.close();
    
    // Filter for businesses without websites (the actual leads)
    const noWebsiteBusinesses = businesses.filter(b => !b.hasWebsite && b.phone);
    
    console.log(`    Found ${businesses.length} businesses, ${noWebsiteBusinesses.length} without websites`);
    
    return noWebsiteBusinesses;
    
  } catch (err) {
    console.log(`    Error scraping "${searchQuery}": ${err.message}`);
    try { await context.close(); } catch (e) {}
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
  
  console.log(`=== GMB Lead Scraper v2 [${country}] - ${new Date().toISOString()} ===\n`);
  
  // Get Google Sheets token
  const token = await getAccessToken();
  
  // Get existing businesses to avoid duplicates
  const existingRows = await readSheet(token, 'Lead Pipeline!C2:C2000');
  const existingBusinesses = new Set();
  existingRows.forEach(row => {
    if (row[0]) existingBusinesses.add(row[0].trim().toLowerCase());
  });
  console.log(`Found ${existingBusinesses.size} existing businesses in sheet`);
  
  // Launch browser
  console.log('Launching browser...');
  const browser = await chromium.launch({
    headless: true,
  });
  
  const today = new Date().toISOString().split('T')[0];
  const newLeads = [];
  let attemptCount = 0;
  
  // Shuffle combinations
  const combos = [];
  for (const city of config.cities) {
    for (const industry of config.industries) {
      combos.push({ city, industry });
    }
  }
  
  for (let i = combos.length - 1; i > 0; i--) {
    const j = Math.floor(Math.random() * (i + 1));
    [combos[i], combos[j]] = [combos[j], combos[i]];
  }
  
  // Process up to 15 combos or until we have 5 real leads
  for (const { city, industry } of combos.slice(0, 15)) {
    attemptCount++;
    
    // Scrape Google Maps
    const businesses = await scrapeGoogleMaps(browser, industry, city, country);
    
    for (const biz of businesses) {
      // Skip duplicates
      if (existingBusinesses.has(biz.name.toLowerCase())) {
        console.log(`    Skipping duplicate: ${biz.name}`);
        continue;
      }
      
      // Mark as existing to avoid duplicates within this run
      existingBusinesses.add(biz.name.toLowerCase());
      
      const leadId = `RR-${country.substring(0,2).toUpperCase()}-${today.replace(/-/g,'')}-${String(newLeads.length + 1).padStart(3,'0')}`;
      
      const lead = [
        leadId,
        today,
        biz.name,
        biz.phone,
        '', // email
        biz.address,
        'NO WEBSITE',
        '', // website url
        industry,
        `${city}, ${country}`,
        `${biz.name} - ${industry} in ${city}. Phone: ${biz.phone}. No website found. Rating: ${biz.rating || 'N/A'} (${biz.reviews || '0'} reviews). Address: ${biz.address || 'N/A'}`,
        '', // notes
        'B',
        'New Lead',
        '', // contact date
        'No', // contacted
        'Pending', // status
        '', // proposal sent
        '', // proposal date
        `Found via Google Maps search: "${industry} ${city}". Has no website - prime candidate for web design + SEO services.`,
        '', '', '', '', '', '', '',
      ];
      
      newLeads.push(lead);
      console.log(`  ✓ Lead: ${biz.name} | ${industry} | ${city}`);
      
      // Stop at 5 leads
      if (newLeads.length >= 5) break;
    }
    
    if (newLeads.length >= 5) break;
    
    // Rate limiting between requests
    await sleep(2000 + Math.random() * 2000);
  }
  
  await browser.close();
  
  // Write to sheet
  if (newLeads.length > 0) {
    const startRow = await appendToSheet(token, newLeads);
    console.log(`\n✓ Appended ${newLeads.length} real ${country} leads starting at ${startRow}`);
  } else {
    console.log(`\n⚠ No new leads found after ${attemptCount} attempts`);
  }
  
  console.log('\nDone');
}

main().catch(err => {
  console.error('Fatal error:', err);
  process.exit(1);
});
