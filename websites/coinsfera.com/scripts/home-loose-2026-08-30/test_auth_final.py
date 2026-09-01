import urllib.request
import ssl
import json
import base64

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

# The nonce we found
nonce = '9af486ae52'

# Try using the nonce with the X-WP-Nonce header (no cookies - this won't work for REST API)
# But let's check if the site has a special REST setup

# Let me try a completely different approach: check if the application password
# is being correctly recognized by looking at a user-specific endpoint

# When auth is working, GET /wp/v2/users/me should return the user
# When NOT working, it returns 404 rest_user_invalid_id (because "me" can't be resolved)

username = 'SheikhOpen'

# Try different password interpretations
# Original: T92W 7D1o aUYt CUIC nCmX C0mb
# Maybe the spaces indicate different groupings?
# WordPress app passwords are 24 chars without spaces
# Let's count: T92W7D1oaUYtCUICnCmXC0mb = 24 chars - that's correct for WP app passwords

# But what if the password is actually different? What if the spaces are part of it?
# "T92W 7D1o aUYt CUIC nCmX C0mb" with spaces = 29 chars

# The real question: is the auth being recognized?
# Let's test by making a request and checking if the response differs for authed vs unauthed

# Get post WITHOUT auth
url = 'https://www.coinsfera.com/wp-json/wp/v2/posts/4435'
req = urllib.request.Request(url, headers={'Content-Type': 'application/json'})
try:
    resp = urllib.request.urlopen(req, context=ctx)
    post_noauth = json.loads(resp.read())
    self_noauth = post_noauth.get('_links', {}).get('self', [{}])[0].get('targetHints', {}).get('allow', [])
    print(f"Without auth - self allow: {self_noauth}")
except urllib.error.HTTPError as e:
    print(f"Without auth: {e.code}")

# Get post WITH auth (no spaces)
password = 'T92W7D1oaUYtCUICnCmXC0mb'
credentials = base64.b64encode(f'{username}:{password}'.encode()).decode()
headers = {'Authorization': f'Basic {credentials}', 'Content-Type': 'application/json'}
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    post_auth = json.loads(resp.read())
    self_auth = post_auth.get('_links', {}).get('self', [{}])[0].get('targetHints', {}).get('allow', [])
    print(f"With auth (no spaces) - self allow: {self_auth}")
except urllib.error.HTTPError as e:
    print(f"With auth (no spaces): {e.code}")

# Get post WITH auth (spaces)
password_spaces = 'T92W 7D1o aUYt CUIC nCmX C0mb'
credentials_spaces = base64.b64encode(f'{username}:{password_spaces}'.encode()).decode()
headers_spaces = {'Authorization': f'Basic {credentials_spaces}', 'Content-Type': 'application/json'}
req = urllib.request.Request(url, headers=headers_spaces)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    post_auth2 = json.loads(resp.read())
    self_auth2 = post_auth2.get('_links', {}).get('self', [{}])[0].get('targetHints', {}).get('allow', [])
    print(f"With auth (spaces) - self allow: {self_auth2}")
except urllib.error.HTTPError as e:
    print(f"With auth (spaces): {e.code}")

# Check if the meta field is accessible (it shouldn't be without auth, and should show yoast meta with auth)
print(f"\nWithout auth meta keys: {list(post_noauth.get('meta', {}).keys())}")
print(f"With auth (no spaces) meta keys: {list(post_auth.get('meta', {}).keys())}")
print(f"With auth (spaces) meta keys: {list(post_auth2.get('meta', {}).keys())}")

# The key difference: if auth is working, we should see different _links or meta
# Let's also compare the users list - an authed user should see more info
url = 'https://www.coinsfera.com/wp-json/wp/v2/users?per_page=10'
req = urllib.request.Request(url, headers={'Content-Type': 'application/json'})
try:
    resp = urllib.request.urlopen(req, context=ctx)
    users_noauth = json.loads(resp.read())
    print(f"\nWithout auth users count: {len(users_noauth)}")
    for u in users_noauth:
        print(f"  {u['id']}: {u['name']}")
except urllib.error.HTTPError as e:
    print(f"Without auth users: {e.code}")

req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    users_auth = json.loads(resp.read())
    print(f"\nWith auth users count: {len(users_auth)}")
    for u in users_auth:
        print(f"  {u['id']}: {u['name']}")
except urllib.error.HTTPError as e:
    print(f"With auth users: {e.code}")