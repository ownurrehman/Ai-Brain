import json, re

with open('/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw/enrichment_results.json') as f:
    results = json.load(f)

def clean_email(e):
    if not e:
        return ''
    e = e.strip().lower()
    # Remove false positives
    bad = ['example','test','domain','sentry','wixpress','mailchimp','gmail.com','yahoo.com','hotmail.com','outlook.com','@2x','.png','.jpg','.avif','.webp','noreferrer','@mailto']
    if any(b in e for b in bad):
        return ''
    if e.count('@') != 1:
        return ''
    local, domain = e.split('@')
    if len(local) < 2 or len(domain) < 4 or '.' not in domain:
        return ''
    # Remove URL-encoded spaces
    e = e.replace('%20', '')
    return e

def clean_phone(p):
    if not p:
        return ''
    p = p.strip()
    digits = re.sub(r'\D','',p)
    if len(digits) < 7 or len(digits) > 15:
        return ''
    # Reject placeholder/fake numbers
    if digits in ['0000000000','1234567890','9999999999','0123456789','10241024','255255255']:
        return ''
    if set(digits) <= {'0','1'} and len(set(digits)) <= 2:
        return ''
    # Reject CSS-like numbers (all same digit repeated)
    if len(set(digits)) == 1:
        return ''
    return p

# Create clean report
cleaned = []
for r in results:
    email = clean_email(r.get('found_email',''))
    phone = clean_phone(r.get('found_phone',''))
    # If status is unreachable, don't show fake data
    if r.get('status') == 'unreachable':
        email = ''
        phone = ''
    cleaned.append({
        'Lead ID': r['lead_id'],
        'Business Name': r['business_name'],
        'Website': r['website'],
        'Found Email': email,
        'Found Phone': phone,
        'Found Contact Name': r.get('found_contact_name',''),
        'Email Source': r.get('email_source','') if email else '',
        'Phone Source': r.get('phone_source','') if phone else '',
        'Status': r.get('status','')
    })

# Summary
total = len(cleaned)
has_email = sum(1 for r in cleaned if r['Found Email'])
has_phone = sum(1 for r in cleaned if r['Found Phone'])
has_both = sum(1 for r in cleaned if r['Found Email'] and r['Found Phone'])
unreachable = sum(1 for r in cleaned if r['Status'] == 'unreachable')

print('=== LEAD ENRICHMENT REPORT ===')
print('Total leads scraped:', total)
print('Emails found:', has_email)
print('Phones found:', has_phone)
print('Both email + phone:', has_both)
print('Unreachable sites:', unreachable)
print()

# Markdown table
print('| Lead ID | Business | Email | Phone | Status |')
print('|---|---|---|---|---|')
for r in cleaned:
    e = r['Found Email'] or '-'
    p = r['Found Phone'] or '-'
    s = r['Status']
    print(f'| {r["Lead ID"]} | {r["Business Name"]} | {e} | {p} | {s} |')

# Save JSON
with open('/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw/enrichment_final.json','w') as f:
    json.dump(cleaned, f, indent=2)
