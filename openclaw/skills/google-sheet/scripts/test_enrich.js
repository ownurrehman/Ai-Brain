const fs = require('fs');
const { execSync } = require('child_process');

// Load first 10 leads
const leadsToEnrich = JSON.parse(fs.readFileSync('/tmp/leads_to_enrich.json', 'utf8'));
const testLeads = leadsToEnrich.slice(0, 10);

console.log('Testing enrichment on first 10 leads...\n');

function getDomain(website) {
  try {
    const url = new URL(website);
    return url.hostname.replace(/^www\./, '');
  } catch {
    return website.replace(/^https?:\/\//, '').replace(/^www\./, '').split('/')[0];
  }
}

for (const lead of testLeads) {
  console.log(`\n[${lead.rowNum}] ${lead.businessName} - ${lead.website}`);
  
  const domain = getDomain(lead.website);
  let foundEmail = null;
  let foundPhone = null;
  let source = null;
  
  // Try main page
  try {
    const html = execSync(
      `curl -s -L --max-time 10 -A "Mozilla/5.0" "${lead.website}" 2>/dev/null | head -c 30000`,
      { encoding: 'utf8', timeout: 12000 }
    );
    
    const emails = [...html.matchAll(/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/gi)];
    const validEmails = [...new Set(emails.map(m => m[0]))].filter(e => {
      const lower = e.toLowerCase();
      return !lower.includes('example') && !lower.includes('domain') &&
             !lower.includes('email@') && !lower.startsWith('noreply') &&
             !lower.startsWith('no-reply') && e.length > 6;
    });
    
    const phones = [...html.matchAll(/(?:\+?1[-.\s]?)?\(?[0-9]{3}\)?[-.\s]?[0-9]{3}[-.\s]?[0-9]{4}/g)];
    const validPhones = [...new Set(phones.map(m => m[0]))].filter(p => p.replace(/\D/g, '').length >= 10);
    
    if (validEmails.length > 0) {
      const domainEmails = validEmails.filter(e => e.toLowerCase().includes(domain.toLowerCase()));
      foundEmail = domainEmails.length > 0 ? domainEmails[0] : validEmails[0];
      source = 'website';
    }
    
    if (validPhones.length > 0) {
      foundPhone = validPhones[0];
    }
  } catch (e) {}
  
  // Try contact pages
  if (!foundEmail) {
    const contactPaths = ['/contact', '/contact-us', '/about'];
    for (const path of contactPaths) {
      try {
        const baseUrl = lead.website.replace(/\/$/, '');
        const html = execSync(
          `curl -s -L --max-time 8 -A "Mozilla/5.0" "${baseUrl}${path}" 2>/dev/null | head -c 25000`,
          { encoding: 'utf8', timeout: 10000 }
        );
        
        const emails = [...html.matchAll(/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/gi)];
        const validEmails = [...new Set(emails.map(m => m[0]))].filter(e => {
          const lower = e.toLowerCase();
          return !lower.includes('example') && !lower.includes('domain') &&
                 !lower.includes('email@') && !lower.startsWith('noreply') &&
                 !lower.startsWith('no-reply') && e.length > 6;
        });
        
        if (validEmails.length > 0) {
          foundEmail = validEmails[0];
          source = 'contact-page';
          break;
        }
      } catch (e) {}
    }
  }
  
  console.log(`  Result: Email=${foundEmail || 'N/A'}, Phone=${foundPhone || 'N/A'}, Source=${source || 'none'}`);
}
