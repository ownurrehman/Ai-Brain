const fs = require('fs');

// Read the lead data
const leadData = JSON.parse(fs.readFileSync('/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw/lead_gen_run3_dubai_uae.json', 'utf8'));

// Read existing sheet data to check for duplicates
const { google } = require('googleapis');
const path = require('path');

const CREDENTIALS_PATH = path.join(process.env.HOME || process.env.USERPROFILE, '.config/google-sheets/credentials.json');
const SPREADSHEET_ID = '11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4';
const SHEET_NAME = 'Lead Pipeline';

async function checkDuplicatesAndAppend() {
  try {
    const credentials = JSON.parse(fs.readFileSync(CREDENTIALS_PATH, 'utf8'));
    const auth = new google.auth.GoogleAuth({
      credentials,
      scopes: ['https://www.googleapis.com/auth/spreadsheets']
    });

    const sheets = google.sheets({ version: 'v4', auth });

    // Get existing data
    const response = await sheets.spreadsheets.values.get({
      spreadsheetId: SPREADSHEET_ID,
      range: `${SHEET_NAME}!C2:G1000`
    });

    const existingRows = response.data.values || [];
    const existingNames = existingRows.map(row => (row[0] || '').toLowerCase().trim());
    const existingWebsites = existingRows.map(row => (row[4] || '').toLowerCase().trim());

    console.log('Existing business names (first 20):', existingNames.slice(0, 20));
    console.log('Total existing rows:', existingRows.length);

    // Filter out duplicates
    const newLeads = leadData.leads.filter(lead => {
      const nameMatch = existingNames.includes(lead.business_name.toLowerCase().trim());
      const websiteMatch = existingWebsites.includes(lead.website.toLowerCase().trim());
      return !nameMatch && !websiteMatch;
    });

    console.log('New leads to add:', newLeads.length);
    console.log('Duplicates found:', leadData.leads.length - newLeads.length);

    if (newLeads.length === 0) {
      console.log('No new leads to add - all are duplicates.');
      return;
    }

    // Prepare data for append
    const rowsToAppend = newLeads.map(lead => [
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
      lead.our_solution,
      lead.lead_grade,
      lead.status,
      lead.email_draft,
      lead.email_sent,
      lead.follow_up_status,
      lead.follow_up_date,
      lead.last_touchpoint,
      lead.notes
    ]);

    // Find the next empty row
    const currentData = await sheets.spreadsheets.values.get({
      spreadsheetId: SPREADSHEET_ID,
      range: `${SHEET_NAME}!A:A`
    });

    const nextRow = (currentData.data.values || []).length + 1;
    console.log('Next available row:', nextRow);

    // Append the data
    const appendResponse = await sheets.spreadsheets.values.append({
      spreadsheetId: SPREADSHEET_ID,
      range: `${SHEET_NAME}!A${nextRow}`,
      valueInputOption: 'USER_ENTERED',
      insertDataOption: 'INSERT_ROWS',
      resource: {
        values: rowsToAppend
      }
    });

    console.log(`Successfully appended ${rowsToAppend.length} rows.`);
    console.log('Updated range:', appendResponse.data.updates?.updatedRange);

    // Save a summary
    const summary = {
      run: leadData.run,
      region: leadData.region,
      totalDiscovered: leadData.leads.length,
      duplicatesSkipped: leadData.leads.length - newLeads.length,
      leadsAdded: newLeads.length,
      leadsAddedNames: newLeads.map(l => l.business_name),
      timestamp: new Date().toISOString()
    };

    fs.writeFileSync('/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw/lead_gen_run3_summary.json', JSON.stringify(summary, null, 2));
    console.log('Summary saved to lead_gen_run3_summary.json');

  } catch (error) {
    console.error('Error:', error.message);
    if (error.response) {
      console.error('API Error:', error.response.data);
    }
  }
}

checkDuplicatesAndAppend();
