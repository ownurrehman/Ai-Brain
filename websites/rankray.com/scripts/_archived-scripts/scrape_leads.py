import json, re, time, sys, urllib.request
from urllib.parse import urljoin, urlparse

def fetch(url, timeout=15):
    try:
        req = urllib.request.Request(url, headers={
            'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36'
        })
        with urllib.request.urlopen(req, timeout=timeout) as resp:
            return resp.read().decode('utf-8', errors='ignore')
    except Exception as e:
        return 'ERROR:' + str(e)

EMAIL_RE = re.compile(r'[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}', re.IGNORECASE)
PHONE_RE = re.compile(r'(?:tel:|phone[:\s]*)?(?:\+?\d{1,3}[-.\s]?)?\(?\d{2,4}\)?[-.\s]?\d{2,4}[-.\s]?\d{2,9}', re.IGNORECASE)

def extract_emails(text):
    found = set()
    for m in EMAIL_RE.findall(text):
        m = m.lower().strip('.')
        if 'example' in m or 'test' in m or 'domain' in m or '.png' in m or '.jpg' in m:
            continue
        if len(m) > 5 and '.' in m.split('@')[-1]:
            found.add(m)
    return sorted(found)

def extract_phones(text):
    found = set()
    for m in PHONE_RE.findall(text):
        digits = re.sub(r'\D', '', m)
        if len(digits) >= 7 and len(digits) <= 15:
            found.add(m.strip())
    return sorted(found)

def extract_links(html, base_url):
    links = []
    for m in re.finditer(r'href=["\']([^"\']+)["\']', html, re.IGNORECASE):
        href = m.group(1)
        full = urljoin(base_url, href)
        links.append((href.lower(), full.lower()))
    return links

def extract_contact_names(html):
    names = set()
    for tag in ['h2', 'h3', 'h4', 'strong', 'span']:
        for m in re.finditer(r'<'+tag+r'[^>]*>([^<]{3,40})</'+tag+r'>', html, re.IGNORECASE):
            text = m.group(1).strip()
            if re.match(r'^[A-Z][a-z]+\s+[A-Z][a-z]+$', text):
                if len(text) > 5 and len(text) < 40:
                    names.add(text)
    return sorted(names)

def scrape_lead(lead):
    url = lead['website']
    if not url.startswith('http'):
        url = 'https://' + url
    
    result = {
        'lead_id': lead['lead_id'],
        'business_name': lead['business_name'],
        'website': url,
        'found_email': [],
        'found_phone': [],
        'found_contact_name': [],
        'found_address': '',
        'email_source': '',
        'phone_source': '',
        'contact_source': '',
        'status': 'pending'
    }
    
    homepage = fetch(url)
    if homepage.startswith('ERROR:'):
        result['status'] = 'error: ' + homepage
        return result
    
    result['status'] = 'ok'
    result['found_email'] = extract_emails(homepage)
    result['found_phone'] = extract_phones(homepage)
    result['found_contact_name'] = extract_contact_names(homepage)
    
    if result['found_email']:
        result['email_source'] = url
    if result['found_phone']:
        result['phone_source'] = url
    if result['found_contact_name']:
        result['contact_source'] = url
    
    links = extract_links(homepage, url)
    pages_to_check = []
    for href, full in links:
        if any(k in href for k in ['contact', 'about', 'team', 'staff', 'people', 'our-team']):
            if full not in pages_to_check:
                pages_to_check.append(full)
    
    for page_url in pages_to_check[:3]:
        time.sleep(1)
        page_html = fetch(page_url)
        if page_html.startswith('ERROR:'):
            continue
        
        if not result['found_email']:
            emails = extract_emails(page_html)
            if emails:
                result['found_email'] = emails
                result['email_source'] = page_url
        
        if not result['found_phone']:
            phones = extract_phones(page_html)
            if phones:
                result['found_phone'] = phones
                result['phone_source'] = page_url
        
        if not result['found_contact_name']:
            names = extract_contact_names(page_html)
            if names:
                result['found_contact_name'] = names
                result['contact_source'] = page_url
    
    return result

with open('/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw/top50.json') as f:
    leads = json.load(f)

results = []
for i, lead in enumerate(leads):
    name = lead['business_name']
    website = lead['website']
    print('[' + str(i+1) + '/50] Scraping ' + name + ' (' + website + ')...', file=sys.stderr)
    try:
        result = scrape_lead(lead)
        results.append(result)
        print('  email=' + str(len(result['found_email'])) + ' phone=' + str(len(result['found_phone'])) + ' names=' + str(len(result['found_contact_name'])), file=sys.stderr)
    except Exception as e:
        print('  EXCEPTION: ' + str(e), file=sys.stderr)
        results.append({
            'lead_id': lead['lead_id'],
            'business_name': lead['business_name'],
            'website': lead['website'],
            'status': 'exception: ' + str(e)
        })
    time.sleep(2)

print(json.dumps(results, indent=2))
