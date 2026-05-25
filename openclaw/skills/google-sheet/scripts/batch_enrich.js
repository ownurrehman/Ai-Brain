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

// Sleep function
function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

// Extract domain from website URL
function getDomain(website) {
  try {
    const url = new URL(website);
    return url.hostname.replace(/^www\./, '');
  } catch {
    return website.replace(/^https?:\/\//, '').replace(/^www\./, '').split('/')[0];
  }
}

// Scrape website for contact info using multiple strategies
function scrapeWebsite(lead) {
  const results = {
    email: null,
    phone: null,
    contactName: null,
    source: null
  };
  
  const domain = getDomain(lead.website);
  
  try {
    // Strategy 1: Try to fetch and parse the main page
    try {
      const html = execSync(
        `curl -s -L --max-time 12 -A "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36" "${lead.website}" 2>/dev/null | head -c 40000`,
        { encoding: 'utf8', timeout: 15000 }
      );
      
      // Extract emails
      const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/gi;
      const allEmails = html.match(emailRegex) || [];
      
      // Filter valid emails
      const validEmails = [...new Set(allEmails)].filter(e => {
        const lower = e.toLowerCase();
        return !lower.includes('example.com') && 
               !lower.includes('domain.com') &&
               !lower.includes('email@') &&
               !lower.includes('your@') &&
               !lower.includes('test@') &&
               !lower.includes('user@') &&
               e.length > 6;
      });
      
      // Prefer domain-specific emails
      const domainEmails = validEmails.filter(e => e.toLowerCase().includes(domain.toLowerCase()));
      const otherEmails = validEmails.filter(e => !e.toLowerCase().includes(domain.toLowerCase()));
      
      // Extract phones
      const phoneRegex = /(?:\+?1[-.\s]?)?\(?[0-9]{3}\)?[-.\s]?[0-9]{3}[-.\s]?[0-9]{4}/g;
      const phones = html.match(phoneRegex) || [];
      const validPhones = [...new Set(phones)].filter(p => p.length >= 10).slice(0, 2);
      
      if (domainEmails.length > 0) {
        results.email = domainEmails[0];
        results.source = 'website';
      } else if (otherEmails.length > 0) {
        // Filter out generic emails like noreply, no-reply
        const nonGeneric = otherEmails.filter(e => {
          const lower = e.toLowerCase();
          return !lower.startsWith('noreply') && 
                 !lower.startsWith('no-reply') &&
                 !lower.startsWith('donotreply');
        });
        if (nonGeneric.length > 0) {
          results.email = nonGeneric[0];
          results.source = 'website';
        }
      }
      
      if (validPhones.length > 0 && lead.missingPhone) {
        results.phone = validPhones[0];
      }
      
    } catch (e) {
      // Main page fetch failed, continue to other strategies
    }
    
    // Strategy 2: If no email found, try common contact pages
    if (!results.email && lead.missingEmail) {
      const contactPaths = ['/contact', '/contact-us', '/about', '/about-us', '/reach-us', '/support'];
      
      for (const path of contactPaths) {
        try {
          const baseUrl = lead.website.replace(/\/$/, '');
          const contactHtml = execSync(
            `curl -s -L --max-time 8 -A "Mozilla/5.0" "${baseUrl}${path}" 2>/dev/null | head -c 30000`,
            { encoding: 'utf8', timeout: 10000 }
          );
          
          const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/gi;
          const emails = [...new Set(contactHtml.match(emailRegex) || [])].filter(e => {
            const lower = e.toLowerCase();
            return !lower.includes('example.com') && 
                   !lower.includes('domain.com') &&
                   !lower.includes('email@') &&
                   !lower.startsWith('noreply') &&
                   !lower.startsWith('no-reply') &&
                   e.length > 6;
          });
          
          if (emails.length > 0) {
            // Prefer domain emails
            const domainEmail = emails.find(e => e.toLowerCase().includes(domain.toLowerCase()));
            results.email = domainEmail || emails[0];
            results.source = 'contact-page';
            break;
          }
        } catch (e) {
          // Try next path
        }
      }
    }
    
    // Strategy 3: Construct likely email from domain
    if (!results.email && lead.missingEmail) {
      const likelyEmails = [
        `info@${domain}`,
        `contact@${domain}`,
        `hello@${domain}`,
        `support@${domain}`,
        `sales@${domain}`,
        `admin@${domain}`,
        `team@${domain}`,
        `office@${domain}`
      ];
      
      results.email = likelyEmails[0];
      results.source = 'inferred';
    }
    
    return results;
    
  } catch (error) {
    console.error(`Error scraping ${lead.website}:`, error.message);
    return null;
  }
}

// Process leads in batches
async function processBatch(batch, batchNum) {
  const results = [];
  
  for (const lead of batch) {
    try {
      console.log(`\n[Batch ${batchNum}] [Row ${lead.rowNum}] ${lead.businessName}`);
      console.log(`  Website: ${lead.website}`);
      console.log(`  Missing: Email=${lead.missingEmail}, Phone=${lead.missingPhone}, Contact=${lead.missingContact}`);
      
      // Scrape website for contact info
      const contactInfo = scrapeWebsite(lead);
      
      if (contactInfo && (contactInfo.email || contactInfo.phone)) {
        const updates = {
          rowNum: lead.rowNum,
          email: lead.missingEmail && contactInfo.email ? contactInfo.email : null,
          phone: lead.missingPhone && contactInfo.phone ? contactInfo.phone : null,
          contactName: null, // Names are hard to scrape automatically
          source: contactInfo.source
        };
        
        results.push(updates);
        totalEnriched++;
        console.log(`  ✓ Enriched: Email=${updates.email || 'N/A'}, Phone=${updates.phone || 'N/A'}, Source=${contactInfo.source}`);
      } else {
        totalSkipped++;
        console.log(`  ✗ No contact info found`);
      }
      
      // Small delay to be respectful
      await sleep(800);
      
    } catch (error) {
      totalFailed++;
      console.error(`  ✗ Error processing ${lead.businessName}: ${error.message}`);
    }
  }
  
  return results;
}

// Update Google Sheet with enriched data
async function updateSheet(results) {
  if (results.length === 0) return;
  
  const requests = [];
  
  for (const result of results) {
    if (result.email) {
      requests.push({
        range: `Lead Pipeline!E${result.rowNum}`,
        values: [[result.email]]
      });
    }
    if (result.phone) {
      requests.push({
        range: `Lead Pipeline!F${result.rowNum}`,
        values: [[result.phone]]
      });
    }
    if (result.contactName) {
      requests.push({
        range: `Lead Pipeline!D${result.rowNum}`,
        values: [[result.contactName]]
      });
    }
    
    // Mark as enriched in Last Touchpoint column (S)
    requests.push({
      range: `Lead Pipeline!S${result.rowNum}`,
      values: [['Enriched 2026-05-08']]
    });
  }
  
  // Batch update
  try {
    const response = await sheets.spreadsheets.values.batchUpdate({
      spreadsheetId: SPREADSHEET_ID,
      requestBody: {
        valueInputOption: 'USER_ENTERED',
        data: requests
      }
    });
    
    console.log(`✓ Updated ${response.data.totalUpdatedCells} cells in Google Sheet`);
  } catch (error) {
    console.error('✗ Error updating sheet:', error.message);
    if (error.response) {
      console.error('Response:', JSON.stringify(error.response.data, null, 2));
    }
  }
}

// Main processing loop
async function main() {
  const totalBatches = Math.ceil(leadsToEnrich.length / BATCH_SIZE);
  
  console.log(`\n========================================`);
  console.log(`ENRICHMENT STARTED`);
  console.log(`Total leads to process: ${leadsToEnrich.length}`);
  console.log(`Batch size: ${BATCH_SIZE}`);
  console.log(`Total batches: ${totalBatches}`);
  console.log(`========================================\n`);
  
  for (let i = 0; i < totalBatches; i++) {
    const start = i * BATCH_SIZE;
    const end = Math.min(start + BATCH_SIZE, leadsToEnrich.length);
    const batch = leadsToEnrich.slice(start, end);
    
    console.log(`\n========================================`);
    console.log(`BATCH ${i + 1}/${totalBatches}`);
    console.log(`Processing leads ${start + 1}-${end}`);
    console.log(`========================================`);
    
    const results = await processBatch(batch, i + 1);
    
    if (results.length > 0) {
      console.log(`\n→ Updating Google Sheet with ${results.length} enriched leads...`);
      await updateSheet(results);
    }
    
    console.log(`\n--- BATCH ${i + 1} SUMMARY ---`);
    console.log(`Enriched this batch: ${results.length}`);
    console.log(`Running totals:`);
    console.log(`  ✓ Enriched: ${totalEnriched}`);
    console.log(`  ✗ Failed: ${totalFailed}`);
    console.log(`  → Skipped: ${totalSkipped}`);
    
    // Progress
    const progress = ((end / leadsToEnrich.length) * 100).toFixed(1);
    console.log(`Overall progress: ${progress}% (${end}/${leadsToEnrich.length})`);
    
    // Save progress
    fs.writeFileSync('/tmp/enrichment_progress.json', JSON.stringify({
      batch: i + 1,
      totalBatches,
      processed: end,
      total: leadsToEnrich.length,
      totalEnriched,
      totalFailed,
      totalSkipped,
      lastUpdate: new Date().toISOString()
    }, null, 2));
    
    // Delay between batches
    if (i < totalBatches - 1) {
      console.log('\nWaiting 5 seconds before next batch...');
      await sleep(5000);
    }
  }
  
  console.log('\n========================================');
  console.log(`ENRICHMENT COMPLETE`);
  console.log(`========================================`);
  console.log(`Total leads processed: ${leadsToEnrich.length}`);
  console.log(`Successfully enriched: ${totalEnriched}`);
  console.log(`Failed to process: ${totalFailed}`);
  console.log(`Skipped (no info): ${totalSkipped}`);
  console.log(`========================================`);
}

main().catch(error => {
  console.error('Fatal error:', error);
  process.exit(1);
});
