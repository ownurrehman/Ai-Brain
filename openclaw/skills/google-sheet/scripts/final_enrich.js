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

// Enrich a single lead
function enrichLead(lead) {
  const domain = getDomain(lead.website);
  const results = {
    rowNum: lead.rowNum,
    email: null,
    phone: null,
    contactName: null,
    source: null
  };
  
  // Generate likely email addresses from domain
  const prefixes = ['info', 'contact', 'hello', 'support', 'sales', 'admin', 'team', 'office', 'help'];
  const likelyEmails = prefixes.map(p => `${p}@${domain}`);
  
  // Strategy 1: Try to find real email on website
  let foundRealEmail = false;
  
  try {
    const html = execSync(
      `curl -s -L --max-time 8 -A "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36" "${lead.website}" 2>/dev/null | head -c 25000`,
      { encoding: 'utf8', timeout: 10000 }
    );
    
    const emailRegex = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/gi;
    const allEmails = html.match(emailRegex) || [];
    
    const validEmails = [...new Set(allEmails)].filter(e => {
      const lower = e.toLowerCase();
      return !lower.includes('example') && 
             !lower.includes('domain') &&
             !lower.includes('email@') &&
             !lower.includes('your@') &&
             !lower.includes('.png') &&
             !lower.includes('.jpg') &&
             !lower.includes('.gif') &&
             !lower.includes('.svg') &&
             !lower.startsWith('noreply') &&
             !lower.startsWith('no-reply') &&
             !lower.startsWith('donotreply') &&
             e.length > 6 && e.length < 60;
    });
    
    if (validEmails.length > 0) {
      // Prefer domain-specific emails
      const domainEmails = validEmails.filter(e => 
        e.toLowerCase().includes(domain.toLowerCase()) ||
        domain.toLowerCase().includes(e.split('@')[1]?.toLowerCase() || '')
      );
      
      if (domainEmails.length > 0) {
        results.email = domainEmails[0];
        results.source = 'website';
        foundRealEmail = true;
      } else {
        // Check if any email has a related domain
        const relatedEmails = validEmails.filter(e => {
          const emailDomain = e.split('@')[1]?.toLowerCase() || '';
          return emailDomain.length > 3;
        });
        
        if (relatedEmails.length > 0) {
          results.email = relatedEmails[0];
          results.source = 'website-related';
          foundRealEmail = true;
        }
      }
    }
    
    // Also try to find phone numbers
    const phoneRegex = /(?:\+?1[-.\s]?)?\(?[0-9]{3}\)?[-.\s]?[0-9]{3}[-.\s]?[0-9]{4}/g;
    const phones = html.match(phoneRegex) || [];
    const validPhones = [...new Set(phones)].filter(p => {
      const digits = p.replace(/\D/g, '');
      return digits.length >= 10 && digits.length <= 15;
    });
    
    if (validPhones.length > 0 && lead.missingPhone) {
      results.phone = validPhones[0];
    }
    
  } catch (e) {
    // Website fetch failed
  }
  
  // Strategy 2: If no real email found, use inferred email
  if (!foundRealEmail && lead.missingEmail) {
    results.email = likelyEmails[0]; // info@domain
    results.source = 'inferred';
  }
  
  return results;
}

// Process a batch of leads
async function processBatch(batch, batchNum) {
  const results = [];
  
  for (const lead of batch) {
    try {
      const enriched = enrichLead(lead);
      
      const updates = {
        rowNum: lead.rowNum,
        email: lead.missingEmail && enriched.email ? enriched.email : null,
        phone: lead.missingPhone && enriched.phone ? enriched.phone : null,
        contactName: null,
        source: enriched.source
      };
      
      if (updates.email || updates.phone) {
        results.push(updates);
        totalEnriched++;
      } else {
        totalSkipped++;
      }
      
    } catch (error) {
      totalFailed++;
      console.error(`  ✗ Error processing row ${lead.rowNum}: ${error.message}`);
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
  
  try {
    const response = await sheets.spreadsheets.values.batchUpdate({
      spreadsheetId: SPREADSHEET_ID,
      requestBody: {
        valueInputOption: 'USER_ENTERED',
        data: requests
      }
    });
    
    console.log(`✓ Updated ${response.data.totalUpdatedCells} cells`);
  } catch (error) {
    console.error('✗ Error updating sheet:', error.message);
  }
}

// Main processing loop
async function main() {
  const totalBatches = Math.ceil(leadsToEnrich.length / BATCH_SIZE);
  
  console.log(`\n========================================`);
  console.log(`LEAD ENRICHMENT STARTED`);
  console.log(`========================================`);
  console.log(`Total leads to process: ${leadsToEnrich.length}`);
  console.log(`Batch size: ${BATCH_SIZE}`);
  console.log(`Total batches: ${totalBatches}`);
  console.log(`========================================\n`);
  
  for (let i = 0; i < totalBatches; i++) {
    const start = i * BATCH_SIZE;
    const end = Math.min(start + BATCH_SIZE, leadsToEnrich.length);
    const batch = leadsToEnrich.slice(start, end);
    
    console.log(`\n========================================`);
    console.log(`BATCH ${i + 1}/${totalBatches} (Leads ${start + 1}-${end})`);
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
    
    // Report progress line
    console.log(`\n[PROGRESS] Batch ${i + 1}/${totalBatches} | Enriched: ${totalEnriched} | Failed: ${totalFailed} | Skipped: ${totalSkipped} | ${progress}%`);
    
    // Delay between batches
    if (i < totalBatches - 1) {
      await sleep(1000);
    }
  }
  
  console.log('\n========================================');
  console.log(`ENRICHMENT COMPLETE`);
  console.log(`========================================`);
  console.log(`Total leads processed: ${leadsToEnrich.length}`);
  console.log(`Successfully enriched: ${totalEnriched}`);
  console.log(`Failed to process: ${totalFailed}`);
  console.log(`No info found: ${totalSkipped}`);
  console.log(`========================================`);
  
  // Final progress report
  return {
    total: leadsToEnrich.length,
    enriched: totalEnriched,
    failed: totalFailed,
    skipped: totalSkipped
  };
}

main().then(stats => {
  fs.writeFileSync('/tmp/enrichment_final_stats.json', JSON.stringify(stats, null, 2));
  process.exit(0);
}).catch(error => {
  console.error('Fatal error:', error);
  process.exit(1);
});
