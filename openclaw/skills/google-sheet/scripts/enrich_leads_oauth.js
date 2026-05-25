const { google } = require('googleapis');
const fs = require('fs');

const SPREADSHEET_ID = '11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4';
const TOKEN_PATH = '/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/token.json';

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

async function getAllLeads() {
  try {
    const response = await sheets.spreadsheets.values.get({
      spreadsheetId: SPREADSHEET_ID,
      range: 'Lead Pipeline!A2:T3500',
    });
    
    const rows = response.data.values || [];
    const leadsToEnrich = [];
    const alreadyEnriched = [];
    const noWebsite = [];
    const completeLeads = [];
    
    rows.forEach((row, index) => {
      const rowNum = index + 2;
      const leadId = row[0] || '';
      const dateAdded = row[1] || '';
      const businessName = row[2] || '';
      const contactName = row[3] || '';
      const email = row[4] || '';
      const phone = row[5] || '';
      const website = row[6] || '';
      const lastTouchpoint = row[18] || '';
      
      // Skip if no website or website is "-"
      if (!website || website === '-') {
        noWebsite.push({ rowNum, leadId, businessName });
        return;
      }
      
      // Skip if already enriched today
      if (lastTouchpoint === 'Enriched 2026-05-08') {
        alreadyEnriched.push({ rowNum, leadId, businessName });
        return;
      }
      
      // Check if missing email OR phone OR contact name
      const missingEmail = !email || email === '-' || email === '';
      const missingPhone = !phone || phone === '-' || phone === '';
      const missingContact = !contactName || contactName === '-' || contactName === '' || contactName === 'Contact form';
      
      if (missingEmail || missingPhone || missingContact) {
        leadsToEnrich.push({
          rowNum,
          leadId,
          businessName,
          website,
          missingEmail,
          missingPhone,
          missingContact,
          email,
          phone,
          contactName,
          lastTouchpoint
        });
      } else {
        completeLeads.push({ rowNum, leadId, businessName });
      }
    });
    
    console.log(`Total leads: ${rows.length}`);
    console.log(`Leads to enrich: ${leadsToEnrich.length}`);
    console.log(`Already enriched: ${alreadyEnriched.length}`);
    console.log(`No website: ${noWebsite.length}`);
    console.log(`Complete leads: ${completeLeads.length}`);
    
    // Output as JSON for further processing
    fs.writeFileSync('/tmp/leads_to_enrich.json', JSON.stringify(leadsToEnrich, null, 2));
    fs.writeFileSync('/tmp/already_enriched.json', JSON.stringify(alreadyEnriched, null, 2));
    fs.writeFileSync('/tmp/no_website.json', JSON.stringify(noWebsite, null, 2));
    fs.writeFileSync('/tmp/complete_leads.json', JSON.stringify(completeLeads, null, 2));
    
  } catch (error) {
    console.error('Error:', error.message);
    if (error.response) {
      console.error('Response:', error.response.data);
    }
    process.exit(1);
  }
}

getAllLeads();
