import json, re, time, sys
from urllib.request import urlopen, Request
from urllib.parse import urljoin

TOKEN = 'ya29.a0AQvPyIO5Gbt2uHDhwB85jFuS3vqy85WxfuhNRvavpI45Col7x1Oxmrym-R4EiV4MQcnCLi83E20rNv28pdc4y5nB0AvTX5Fb5mYzT1JcGHCMQzbWTxym7Q5eclhsKvz8qL0MqdOm_1b2SWOeRXDMQm0HsgAkt7sVgzyFJyu4iiYbIzgELqi5-bQcafx-YeDoKx_L2Y-PaCgYKAQMSARMSFQHGX2MikOaX-3Pi02cCsQtvrMvHNA0207'

def fetch(url, timeout=12):
    try:
        req = Request(url, headers={'User-Agent': 'Mozilla/5.0'})
        with urlopen(req, timeout=timeout) as resp:
            return resp.read().decode('utf-8', errors='ignore')
    except Exception as e:
        return ''

EMAIL_RE = re.compile(r'[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}', re.IGNORECASE)
PHONE_RE = re.compile(r'(?:tel[:/])?(?:\+?\d{1,3}[-.\s]?)?\(?\d{2,4}\)?[-.\s]?\d{2,4}[-.\s]?\d{2,9}')

def clean_email(e):
    e = e.lower().strip('.')
    bad = ['example','test','domain','sentry','wixpress','mailchimp','@gmail.com','@yahoo.com','@hotmail.com','@outlook.com']
    if any(b in e for b in bad):
        return None
    if len(e) < 6 or e.count('@') != 1:
        return None
    return e

def clean_phone(p):
    digits = re.sub(r'\D','',p)
    if len(digits) < 7 or len(digits) > 15:
        return None
    return p.strip()

def extract(html):
    emails = [e for e in set(EMAIL_RE.findall(html)) if clean_email(e)]
    phones = [p for p in set(PHONE_RE.findall(html)) if clean_phone(p)]
    return sorted(set(emails)), sorted(set(phones))

def scrape(lead):
    url = lead['website']
    if not url.startswith('http'):
        url = 'https://' + url
    res = {'lead_id': lead['lead_id'], 'business_name': lead['business_name'], 'website': url, 'found_email':'','found_phone':'','found_contact_name':'','email_source':'','phone_source':'','contact_source':'','status':''}
    html = fetch(url)
    if not html:
        res['status'] = 'unreachable'
        return res
    emails, phones = extract(html)
    res['status'] = 'ok'
    if emails:
        res['found_email'] = emails[0]
        res['email_source'] = url
    if phones:
        res['found_phone'] = phones[0]
        res['phone_source'] = url
    # quick contact page check
    for pat in ['/contact','/about','/team']:
        if pat in html.lower():
            contact_url = urljoin(url, pat)
            chtml = fetch(contact_url)
            if chtml:
                ce, cp = extract(chtml)
                if ce and not res['found_email']:
                    res['found_email'] = ce[0]
                    res['email_source'] = contact_url
                if cp and not res['found_phone']:
                    res['found_phone'] = cp[0]
                    res['phone_source'] = contact_url
            break  # only check one contact page
    return res

with open('/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw/top50.json') as f:
    leads = json.load(f)

results = []
for i, lead in enumerate(leads):
    print('[' + str(i+1) + '/50] ' + lead['business_name'] + ' ...', file=sys.stderr)
    try:
        r = scrape(lead)
        results.append(r)
        print('  email=' + str(r['found_email'] or '-') + ' phone=' + str(r['found_phone'] or '-'), file=sys.stderr)
    except Exception as e:
        results.append({'lead_id': lead['lead_id'], 'business_name': lead['business_name'], 'website': lead['website'], 'status': 'error: ' + str(e)})
    time.sleep(1.5)

print(json.dumps(results, indent=2))
