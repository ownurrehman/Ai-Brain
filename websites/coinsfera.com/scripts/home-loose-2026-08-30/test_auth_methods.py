import urllib.request
import ssl
import json
import base64

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

username = 'SheikhOpen'
password = 'T92W7D1oaUYtCUICnCmXC0mb'
credentials = base64.b64encode(f'{username}:{password}'.encode()).decode()

# The nonce we found earlier might be stale, let's get a fresh one
import re

# Get a fresh nonce
req = urllib.request.Request('https://www.coinsfera.com/')
resp = urllib.request.urlopen(req, context=ctx)
html = resp.read().decode('utf-8', errors='ignore')
nonce_match = re.search(r'"nonce":"([a-f0-9]+)"', html)
nonce = nonce_match.group(1) if nonce_match else None
print(f"Fresh nonce: {nonce}")

# Try the SG Optimizer approach - sometimes SG requires the auth header to be passed differently
# Try using REDIRECT_HTTP_AUTHORIZATION
# Try passing auth via query param
url = f'https://www.coinsfera.com/wp-json/wp/v2/users/me?_authorization={credentials}'
req = urllib.request.Request(url, headers={'Content-Type': 'application/json'})
try:
    resp = urllib.request.urlopen(req, context=ctx)
    user = json.loads(resp.read())
    print(f"Query param auth: SUCCESS - {user.get('name')}")
except urllib.error.HTTPError as e:
    print(f"Query param auth: {e.code}")

# Try using a custom header
print("\n=== Try custom header approaches ===")
custom_headers = [
    ('X-Authorization', f'Basic {credentials}'),
    ('X-Auth', f'Basic {credentials}'),
    ('PHP_AUTH_USER', username),
]

for header_name, header_val in custom_headers:
    headers = {'Content-Type': 'application/json', header_name: header_val}
    url = 'https://www.coinsfera.com/wp-json/wp/v2/users/me'
    req = urllib.request.Request(url, headers=headers)
    try:
        resp = urllib.request.urlopen(req, context=ctx)
        user = json.loads(resp.read())
        print(f"{header_name}: SUCCESS - {user.get('name')}")
    except urllib.error.HTTPError as e:
        body = e.read().decode()
        if 'rest_not_logged_in' in body:
            print(f"{header_name}: not logged in")
        else:
            print(f"{header_name}: {e.code} {body[:80]}")

# Try using the HTTP Basic auth in URL (user:pass@host)
print("\n=== Try auth in URL ===")
url_auth = f'https://{username}:{password}@www.coinsfera.com/wp-json/wp/v2/users/me'
req = urllib.request.Request(url_auth, headers={'Content-Type': 'application/json'})
try:
    resp = urllib.request.urlopen(req, context=ctx)
    user = json.loads(resp.read())
    print(f"URL auth: SUCCESS - {user.get('name')}")
except urllib.error.HTTPError as e:
    body = e.read().decode()
    print(f"URL auth: {e.code} {body[:80]}")
except ValueError as e:
    print(f"URL auth error: {e}")

# Try using .htaccess-style approach - sometimes the issue is that nginx strips auth
# and you need to use the HTTP_AUTHORIZATION header or REDIRECT_HTTP_AUTHORIZATION
print("\n=== Try HTTP_AUTHORIZATION header ===")
headers = {
    'Content-Type': 'application/json',
    'HTTP_AUTHORIZATION': f'Basic {credentials}',
    'REDIRECT_HTTP_AUTHORIZATION': f'Basic {credentials}',
    'Authorization': f'Basic {credentials}',
}
url = 'https://www.coinsfera.com/wp-json/wp/v2/users/me'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    user = json.loads(resp.read())
    print(f"HTTP_AUTHORIZATION: SUCCESS - {user.get('name')}")
except urllib.error.HTTPError as e:
    body = e.read().decode()
    if 'rest_not_logged_in' in body:
        print(f"HTTP_AUTHORIZATION: not logged in")
    else:
        print(f"HTTP_AUTHORIZATION: {e.code} {body[:80]}")