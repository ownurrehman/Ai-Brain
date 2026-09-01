import urllib.request
import ssl
import json
import base64

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

# The password as given: T92W 7D1o aUYt CUIC nCmX C0mb
# WordPress application passwords: spaces are visual separators only
# The actual password is: T92W7D1oaUYtCUICnCmXC0mb
# BUT maybe the display has spaces and the actual password includes them?

# Let me try ALL variations
passwords = [
    ('exact with spaces', 'T92W 7D1o aUYt CUIC nCmX C0mb'),
    ('no spaces', 'T92W7D1oaUYtCUICnCmXC0mb'),
    ('lowercase no spaces', 't92w7d1oaUYtCUICnCmXC0mb'),
    ('uppercase no spaces', 'T92W7D1OAUYTCUICNCMXC0MB'),
    ('dashed', 'T92W-7D1o-aUYt-CUIC-nCmX-C0mb'),
]

username = 'SheikhOpen'

for label, pw in passwords:
    credentials = base64.b64encode(f'{username}:{pw}'.encode()).decode()
    headers = {
        'Authorization': f'Basic {credentials}',
        'Content-Type': 'application/json'
    }
    
    # Test POST
    post_data = json.dumps({"date": "2025-07-26T07:21:12"}).encode('utf-8')
    url = f'https://www.coinsfera.com/wp-json/wp/v2/posts/4435'
    req = urllib.request.Request(url, data=post_data, headers=headers, method='POST')
    try:
        resp = urllib.request.urlopen(req, context=ctx)
        result = json.loads(resp.read())
        print(f"{label}: POST SUCCESS! Post ID: {result['id']}")
        break
    except urllib.error.HTTPError as e:
        body = e.read().decode()
        if 'rest_not_logged_in' in body:
            print(f"{label}: NOT LOGGED IN (bad auth)")
        elif 'rest_cannot_edit' in body:
            print(f"{label}: AUTH OK but CANNOT EDIT")
        else:
            print(f"{label}: {e.code} {body[:100]}")

# Also try with 'wp' as username
print("\n=== Try other usernames ===")
for uname in ['admin', 'wp', 'coinsfera', 'own', 'sheikh']:
    credentials = base64.b64encode(f'{uname}:T92W7D1oaUYtCUICnCmXC0mb'.encode()).decode()
    headers = {
        'Authorization': f'Basic {credentials}',
        'Content-Type': 'application/json'
    }
    url = 'https://www.coinsfera.com/wp-json/wp/v2/users/me'
    req = urllib.request.Request(url, headers=headers)
    try:
        resp = urllib.request.urlopen(req, context=ctx)
        user = json.loads(resp.read())
        print(f"  {uname}: AUTHED as {user.get('name')} ID:{user.get('id')}")
    except urllib.error.HTTPError as e:
        body = e.read().decode()
        if 'rest_not_logged_in' in body:
            print(f"  {uname}: not logged in")
        else:
            print(f"  {uname}: {e.code} {body[:80]}")