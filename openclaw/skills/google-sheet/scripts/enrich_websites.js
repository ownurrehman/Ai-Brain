const { google } = require('googleapis');
const fs = require('fs');
const { execSync } = require('child_process');

const SPREADSHEET_ID = '11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4';
const TOKEN_PATH = '/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/token.json';
const BATCH_SIZE = 50;

// Read OAuth token
const tokenData = JSON.parse(fs.readFileSync(TOKEN_PATH, 'utf8'));

const oauth2Client = new google.auth.OAuth2(
  tokenData.client_id,
  tokenData.client_secret
);
oauth2Client.setCredentials({
  access_token: tokenData.token,
  refresh_token: tokenData.refresh_token,
  token_uri: tokenData.token_uri
});

const sheets = google.sheets({ version: 'v4', auth: oauth2Client });

// Load leads to enrich
const leadsToEnrich = JSON.parse(fs.readFileSync('/tmp/leads_to_enrich.json', 'utf8'));

console.log(`Starting enrichment of ${leadsToEnrich.length} leads...`);
console.log(`Processing in batches of ${BATCH_SIZE}`);

// Track statistics
let totalEnriched = 0;
let totalFailed = 0;
let totalSkipped = 0;

// Process leads in batches
async function processBatch(batch, batchNum) {
  const results = [];
  
  for (const lead of batch) {
    try {
      console.log(`\n[${lead.rowNum}] Scraping: ${lead.website}`);
      
      // Use web_fetch-like approach - scrape website for contact info
      const scrapedData = await scrapeWebsite(lead.website);
      
      if (scrapedData) {
        results.push({
          rowNum: lead.rowNum,
          ...scrapedData
        });
        totalEnriched++;
        console.log(`  ✓ Found: Email=${scrapedData.email || 'N/A'}, Phone=${scrapedData.phone || 'N/A'}, Contact=${scrapedData.contactName || 'N/A'}`);
      } else {
        totalSkipped++;
        console.log(`  ✗ No contact info found`);
      }
      
      // Small delay to be respectful
      await sleep(1000);
      
    } catch (error) {
      totalFailed++;
      console.error(`  ✗ Error scraping ${lead.website}: ${error.message}`);
    }
  }
  
  return results;
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

// Scrape website for contact info using curl
async function scrapeWebsite(url) {
  try {
    // Try to fetch the website content
    const result = execSync(`curl -s -L --max-time 15 -A "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36" "${url}" 2>/dev/null | head -c 50000`, { encoding: 'utf8', timeout: 20000 });
    
    const html = result.toLowerCase();
    
    // Extract email patterns
    const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/g;
    const emails = result.match(emailRegex) || [];
    
    // Filter out common false positives
    const validEmails = emails.filter(e => 
      !e.includes('example.com') && 
      !e.includes('domain.com') &&
      !e.includes('email@') &&
      !e.includes('@gmail.com') === false || e.includes('@yahoo.com') === false || e.includes('@outlook.com') === false ||
      e.length > 5
    );
    
    // Extract phone patterns (US/International)
    const phoneRegex = /\+?[\d\s\-\(\)]{10,20}/g;
    const phones = result.match(phoneRegex) || [];
    
    // Clean up phone numbers
    const validPhones = phones.map(p => p.replace(/\s+/g, ' ').trim()).filter(p => p.length >= 10);
    
    // Try to find contact page for more info
    let contactPageHtml = '';
    if (html.includes('contact') || html.includes('about')) {
      // Try contact page
      const contactUrls = [
        url.replace(/\/$/, '') + '/contact',
        url.replace(/\/$/, '') + '/contact-us',
        url.replace(/\/$/, '') + '/about',
        url.replace(/\/$/, '') + '/about-us',
        url.replace(/\/$/, '') + '/team'
      ];
      
      for (const contactUrl of contactUrls) {
        try {
          const contactResult = execSync(`curl -s -L --max-time 10 -A "Mozilla/5.0" "${contactUrl}" 2>/dev/null | head -c 30000`, { encoding: 'utf8', timeout: 15000 });
          
          const contactEmails = contactResult.match(emailRegex) || [];
          const contactPhones = contactResult.match(phoneRegex) || [];
          
          validEmails.push(...contactEmails.filter(e => 
            !e.includes('example.com') && !e.includes('domain.com')
          ));
          validPhones.push(...contactPhones.map(p => p.replace(/\s+/g, ' ').trim()).filter(p => p.length >= 10));
          
          contactPageHtml = contactResult;
          break;
        } catch (e) {
          // Continue to next URL
        }
      }
    }
    
    // Deduplicate
    const uniqueEmails = [...new Set(validEmails)].slice(0, 3);
    const uniquePhones = [...new Set(validPhones)].slice(0, 3);
    
    // Get best email (prefer info@, contact@, sales@, hello@ over personal emails)
    let bestEmail = '';
    const preferredPrefixes = ['info', 'contact', 'sales', 'hello', 'support', 'admin', 'team', 'office'];
    
    for (const prefix of preferredPrefixes) {
      const found = uniqueEmails.find(e => e.toLowerCase().startsWith(prefix + '@'));
      if (found) {
        bestEmail = found;
        break;
      }
    }
    
    if (!bestEmail && uniqueEmails.length > 0) {
      bestEmail = uniqueEmails[0];
    }
    
    // Get best phone
    let bestPhone = uniquePhones.length > 0 ? uniquePhones[0] : '';
    
    return {
      email: bestEmail,
      phone: bestPhone,
      contactName: '' // Would need more sophisticated parsing for names
    };
    
  } catch (error) {
    console.error(`Error scraping ${url}: ${error.message}`);
    return null;
  }
}

// Update Google Sheet with enriched data
async function updateSheet(results) {
  if (results.length === 0) return;
  
  const requests = [];
  
  for (const result of results) {
    const updates = [];
    
    if (result.email) {
      updates.push({ range: `Lead Pipeline!E${result.rowNum}`, values: [[result.email]] });
    }
    if (result.phone) {
      updates.push({ range: `Lead Pipeline!F${result.rowNum}`, values: [[result.phone]] });
    }
    if (result.contactName) {
      updates.push({ range: `Lead Pipeline!D${result.rowNum}`, values: [[result.contactName]] });
    }
    
    // Mark as enriched
    updates.push({ range: `Lead Pipeline!S${result.rowNum}`, values: [['Enriched 2026-05-08']] });
    
    requests.push(...updates);
  }
  
  // Batch update (Google Sheets API allows batch updates)
  try {
    const response = await sheets.spreadsheets.values.batchUpdate({
      spreadsheetId: SPREADSHEET_ID,
      requestBody: {
        valueInputOption: 'USER_ENTERED',
        data: requests
      }
    });
    
    console.log(`Updated ${response.data.totalUpdatedCells} cells`);
  } catch (error) {
    console.error('Error updating sheet:', error.message);
  }
}

// Main processing loop
async function main() {
  const totalBatches = Math.ceil(leadsToEnrich.length / BATCH_SIZE);
  
  for (let i = 0; i < totalBatches; i++) {
    const start = i * BATCH_SIZE;
    const end = Math.min(start + BATCH_SIZE, leadsToEnrich.length);
    const batch = leadsToEnrich.slice(start, end);
    
    console.log(`\n=== BATCH ${i + 1}/${totalBatches} (Leads ${start + 1}-${end}) ===`);
    
    const results = await processBatch(batch, i + 1);
    
    console.log(`\nUpdating sheet with ${results.length} enriched leads...`);
    await updateSheet(results);
    
    console.log(`\n--- BATCH ${i + 1} COMPLETE ---`);
    console.log(`Enriched this batch: ${results.length}`);
    console.log(`Running totals - Enriched: ${totalEnriched}, Failed: ${totalFailed}, Skipped: ${totalSkipped}`);
    
    // Progress report
    const progress = ((end / leadsToEnrich.length) * 100).toFixed(1);
    console.log(`Overall progress: ${progress}%`);
    
    // Save progress
    fs.writeFileSync('/tmp/enrichment_progress.json', JSON.stringify({
      batch: i + 1,
      totalBatches,
      processed: end,
      total: leadsToEnrich.length,
      totalEnriched,
      totalFailed,
      totalSkipped
    }, null, 2));
    
    // Delay between batches
    if (i < totalBatches - 1) {
      console.log('Waiting 5 seconds before next batch...');
      await sleep(5000);
    }
  }
  
  console.log('\n=== ENRICHMENT COMPLETE ===');
  console.log(`Total leads processed: ${leadsToEnrich.length}`);
  console.log(`Successfully enriched: ${totalEnriched}`);
  console.log(`Failed to scrape: ${totalFailed}`);
  console.log(`Skipped (no info found): ${totalSkipped}`);
}

main().catch(error => {
  console.error('Fatal error:', error);
  process.exit(1);
});
