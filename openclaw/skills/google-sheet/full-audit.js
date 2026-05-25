const { request } = require('./node_modules/gaxios/build/src/index.js');
const fs = require('fs');
const jwt = require('jsonwebtoken');

const keyFile = process.env.GOOGLE_SERVICE_ACCOUNT_KEY || '~/.config/google-sheets/credentials.json';
const resolvedPath = keyFile.replace(/^~/, require('os').homedir());
const serviceAccount = JSON.parse(fs.readFileSync(resolvedPath, 'utf8'));

function createJWT() {
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

async function readSheet(accessToken, range) {
  const { data } = await request({
    url: `https://sheets.googleapis.com/v4/spreadsheets/11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4/values/${encodeURIComponent(range)}`,
    headers: { Authorization: `Bearer ${accessToken}` },
  });
  return data.values || [];
}

// Patterns that indicate AI-generated or fake business names
const FAKE_PATTERNS = [
  // AI generated name patterns
  /^[A-Z][a-z]+\s+(?:Dental|Dentistry|Smile|Ortho|Implant|Family\s+Dental|Cosmetic\s+Dental)$/,
  /^[A-Z][a-z]+\s+(?:Law|Lawyers|Attorneys|Legal|Law\s+Firm|Legal\s+Group)$/,
  /^[A-Z][a-z]+\s+(?:Plumbing|HVAC|Heating|Electrical|Roofing|Renovation|Construction)$/,
  /^[A-Z][a-z]+\s+(?:Physio|Physiotherapy|Physical\s+Therapy|Sports\s+Med|Rehab)$/,
  /^[A-Z][a-z]+\s+(?:Home|Services|Pro|Solutions|Experts|Group)$/,
  // Generic city + industry combos
  /^[A-Z][a-z]+\s+(?:Dental|Dentist|Law|Legal|Plumbing|HVAC|Physio|Home\s+Pro)$/i,
  // Pattern: Name + generic descriptor that seems AI-made
  /^(?:Evergreen|Summit|Pinnacle|Horizon|Heritage|Legacy|Premier|Elite|Advanced|Metro|Capitol|Beacon|Harbor|Harbour|Riverside|Lakeside|Parkside|Westside|Eastside|North|South)\s/i,
];

function looksAIGenerated(name) {
  if (!name) return true;
  const lower = name.toLowerCase();
  
  // Check if it's a generic pattern lead
  const genericPatterns = [
    /dental center$/i,
    /dental clinic$/i,
    /family dental$/i,
    /law firm$/i,
    /law group$/i,
    /legal group$/i,
    /plumbing$/i,
    /hvac$/i,
    /home pro$/i,
    /renovation$/i,
    /construction$/i,
    /physio$/i,
    /physiotherapy$/i,
    /physical therapy$/i,
  ];
  
  // Count how many generic patterns match
  let genericMatches = 0;
  for (const p of genericPatterns) {
    if (p.test(lower)) genericMatches++;
  }
  
  // Check for suspicious email patterns that suggest placeholder/AI data
  return genericMatches > 0;
}

function hasRealContact(row) {
  const email = (row[4] || '').trim();
  const phone = (row[5] || '').trim();
  const website = (row[6] || '').trim();
  
  // Real email should not be generic info@ if there's no other contact
  const hasEmail = email && email.includes('@') && email.includes('.') && email.length > 8;
  const hasPhone = phone && phone.replace(/\D/g, '').length >= 7;
  const hasWebsite = website && website.includes('.') && website.length > 10;
  
  return { hasEmail, hasPhone, hasWebsite, hasAny: hasEmail || hasPhone || hasWebsite };
}

async function main() {
  const token = await getAccessToken();
  
  // Read the current sheet - check info first
  const info = await request({
    url: 'https://sheets.googleapis.com/v4/spreadsheets/11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4',
    headers: { Authorization: `Bearer ${token}` },
  });
  
  const sheetInfo = info.data.sheets.find(s => s.properties.title === 'Lead Pipeline');
  const rowCount = sheetInfo.properties.gridProperties.rowCount;
  console.log(`Sheet has ${rowCount} rows total\n`);
  
  // Sample multiple sections of the sheet
  const sections = [
    'Lead Pipeline!A2:Z50',   // Top section
    'Lead Pipeline!A500:Z550', // Middle
    'Lead Pipeline!A1000:Z1050',
    'Lead Pipeline!A1500:Z1550',
    'Lead Pipeline!A2000:Z2050',
  ];
  
  let allSampled = [];
  for (const range of sections) {
    const rows = await readSheet(token, range);
    const startRow = parseInt(range.match(/A(\d+)/)[1]);
    rows.forEach((row, i) => {
      if (row[0] || row[2]) { // Has lead ID or business name
        allSampled.push({ idx: startRow + i, data: row });
      }
    });
  }
  
  console.log(`Sampled ${allSampled.length} rows from across the sheet\n`);
  
  // Analyze patterns
  const issues = [];
  const suspiciousEmails = new Map();
  const suspiciousNames = new Map();
  const industryCounts = new Map();
  const locationCounts = new Map();
  
  allSampled.forEach(row => {
    const leadId = (row.data[0] || '').trim();
    const business = (row.data[2] || '').trim();
    const email = (row.data[4] || '').trim().toLowerCase();
    const industry = (row.data[8] || '').trim();
    const location = (row.data[9] || '').trim();
    const notes = (row.data[20] || '').toLowerCase();
    const status = (row.data[13] || '').trim();
    
    const contact = hasRealContact(row.data);
    
    // Collect stats
    if (industry) {
      industryCounts.set(industry, (industryCounts.get(industry) || 0) + 1);
    }
    if (location) {
      locationCounts.set(location, (locationCounts.get(location) || 0) + 1);
    }
    
    let reasons = [];
    
    // No contact info
    if (!contact.hasAny) {
      reasons.push('NO CONTACT INFO');
    }
    
    // Generic info@ only with no other contact
    if (email && email.startsWith('info@') && !contact.hasPhone && !contact.hasWebsite) {
      reasons.push('GENERIC EMAIL ONLY');
    }
    
    // Check for AI-looking patterns in notes
    if (notes.includes('rank ray fix') || notes.includes('rank ray:') || notes.includes('rank ray deploy')) {
      // This is actually good - it means it was enriched
    }
    
    // Check if business name looks AI-generated
    if (business && looksAIGenerated(business)) {
      reasons.push('AI-LOOKING NAME');
    }
    
    // Suspicious: same location repeated too many times with different business names
    // (handled below in aggregate)
    
    // Test entries
    if (leadId.toLowerCase().includes('test') || business.toLowerCase().includes('test')) {
      reasons.push('TEST DATA');
    }
    
    // No lead ID
    if (!leadId) {
      reasons.push('NO LEAD ID');
    }
    
    if (reasons.length > 0) {
      issues.push({
        idx: row.idx,
        leadId,
        business,
        email,
        reasons: reasons.join(', '),
        status,
        location,
        industry
      });
    }
    
    // Track suspicious emails
    if (email) {
      const domain = email.split('@')[1];
      if (domain) {
        suspiciousEmails.set(domain, (suspiciousEmails.get(domain) || 0) + 1);
      }
    }
  });
  
  console.log('=== ISSUES FOUND IN SAMPLE ===');
  issues.forEach(issue => {
    console.log(`Row ${issue.idx}: ${issue.leadId || 'NO ID'} | ${issue.business} | ${issue.email} | ${issue.reasons}`);
  });
  
  console.log('\n=== INDUSTRY DISTRIBUTION ===');
  const sortedIndustries = [...industryCounts.entries()].sort((a,b) => b[1]-a[1]);
  sortedIndustries.slice(0, 20).forEach(([ind, count]) => console.log(`${ind}: ${count}`));
  
  console.log('\n=== LOCATION DISTRIBUTION ===');
  const sortedLocations = [...locationCounts.entries()].sort((a,b) => b[1]-a[1]);
  sortedLocations.slice(0, 20).forEach(([loc, count]) => console.log(`${loc}: ${count}`));
  
  console.log('\n=== EMAIL DOMAINS (sample) ===');
  const sortedDomains = [...suspiciousEmails.entries()].sort((a,b) => b[1]-a[1]);
  sortedDomains.slice(0, 30).forEach(([domain, count]) => console.log(`${domain}: ${count}`));
  
  console.log(`\nTotal issues in sample: ${issues.length}/${allSampled.length}`);
  
  // Now read ALL data to do a proper full audit
  console.log('\n=== FULL AUDIT - READING ALL ROWS ===');
  let allRows = [];
  let start = 2;
  let hasMore = true;
  
  while (hasMore && start <= rowCount) {
    const range = `Lead Pipeline!A${start}:Z${Math.min(start + 99, rowCount)}`;
    const rows = await readSheet(token, range);
    if (!rows || rows.length === 0) {
      hasMore = false;
    } else {
      rows.forEach((row, i) => {
        if (row[0] || row[2]) {
          allRows.push({ idx: start + i, data: row });
        }
      });
      start += rows.length;
      if (rows.length < 100) hasMore = false;
    }
  }
  
  console.log(`Total rows with data: ${allRows.length}\n`);
  
  // Full analysis
  const fullIssues = [];
  const emailDomains = new Map();
  const allLocations = new Map();
  const allIndustries = new Map();
  
  allRows.forEach(row => {
    const leadId = (row.data[0] || '').trim();
    const business = (row.data[2] || '').trim();
    const name = (row.data[3] || '').trim();
    const email = (row.data[4] || '').trim().toLowerCase();
    const phone = (row.data[5] || '').trim();
    const website = (row.data[6] || '').trim();
    const industry = (row.data[8] || '').trim();
    const location = (row.data[9] || '').trim();
    const status = (row.data[13] || '').trim();
    const notes = (row.data[20] || '').toLowerCase();
    
    const contact = hasRealContact(row.data);
    
    if (industry) allIndustries.set(industry, (allIndustries.get(industry) || 0) + 1);
    if (location) allLocations.set(location, (allLocations.get(location) || 0) + 1);
    if (email) {
      const domain = email.split('@')[1];
      if (domain) emailDomains.set(domain, (emailDomains.get(domain) || 0) + 1);
    }
    
    let reasons = [];
    
    if (!contact.hasAny) reasons.push('NO CONTACT');
    if (email && email.startsWith('info@') && !contact.hasPhone && !contact.hasWebsite) reasons.push('INFO@ ONLY');
    if (business && looksAIGenerated(business)) reasons.push('AI-LOOKING');
    if (leadId.toLowerCase().includes('test') || business.toLowerCase().includes('test')) reasons.push('TEST');
    if (!leadId) reasons.push('NO ID');
    if (!business) reasons.push('NO BUSINESS');
    
    // Check if website is valid
    if (website && !website.match(/^https?:\/\//) && !website.includes('.')) reasons.push('BAD WEBSITE');
    
    // Check for obviously fake phone numbers
    if (phone) {
      const digitsOnly = phone.replace(/\D/g, '');
      if (digitsOnly.length > 0 && digitsOnly.length < 7) reasons.push('BAD PHONE');
    }
    
    if (reasons.length > 0) {
      fullIssues.push({
        idx: row.idx,
        leadId: leadId || 'NO-ID',
        business: business || 'NO-NAME',
        email: email || 'NO-EMAIL',
        reasons: reasons.join(', '),
        status,
        location: location || 'NO-LOC'
      });
    }
  });
  
  console.log(`=== FULL AUDIT RESULTS ===`);
  console.log(`Total rows analyzed: ${allRows.length}`);
  console.log(`Rows with issues: ${fullIssues.length}`);
  console.log(`Clean rows: ${allRows.length - fullIssues.length}`);
  
  console.log('\n=== TOP 50 PROBLEMATIC ROWS ===');
  fullIssues.slice(0, 50).forEach(i => {
    console.log(`Row ${i.idx}: ${i.leadId} | ${i.business} | ${i.email} | ${i.reasons}`);
  });
  
  if (fullIssues.length > 50) {
    console.log(`\n... and ${fullIssues.length - 50} more`);
  }
  
  console.log('\n=== LOCATION COUNTS (Top 20) ===');
  [...allLocations.entries()].sort((a,b) => b[1]-a[1]).slice(0, 20).forEach(([loc, count]) => console.log(`${loc}: ${count}`));
  
  console.log('\n=== INDUSTRY COUNTS (Top 20) ===');
  [...allIndustries.entries()].sort((a,b) => b[1]-a[1]).slice(0, 20).forEach(([ind, count]) => console.log(`${ind}: ${count}`));
  
  console.log('\n=== EMAIL DOMAINS (Top 30) ===');
  [...emailDomains.entries()].sort((a,b) => b[1]-a[1]).slice(0, 30).forEach(([dom, count]) => console.log(`${dom}: ${count}`));
  
  // Save results
  fs.writeFileSync('full-audit.json', JSON.stringify({
    totalRows: allRows.length,
    issueCount: fullIssues.length,
    cleanCount: allRows.length - fullIssues.length,
    issues: fullIssues,
    locations: [...allLocations.entries()].sort((a,b) => b[1]-a[1]),
    industries: [...allIndustries.entries()].sort((a,b) => b[1]-a[1]),
    domains: [...emailDomains.entries()].sort((a,b) => b[1]-a[1]),
  }, null, 2));
  
  console.log('\nSaved full audit to full-audit.json');
}

main().catch(console.error);
