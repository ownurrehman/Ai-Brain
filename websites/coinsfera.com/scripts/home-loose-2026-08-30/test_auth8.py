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

headers = {
    'Authorization': f'Basic {credentials}',
    'Content-Type': 'application/json'
}

# Get the current user's capabilities - try to access user 8 with context=edit
url = 'https://www.coinsfera.com/wp-json/wp/v2/users/8?context=edit'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    user = json.loads(resp.read())
    print(f"User 8: {user.get('name')}")
    print(f"Roles: {user.get('roles')}")
    print(f"Capabilities: {user.get('capabilities')}")
except urllib.error.HTTPError as e:
    print(f"User 8 context=edit: {e.code} {e.reason}")
    print(e.read().decode()[:300])

# Get user 8 without context
url = 'https://www.coinsfera.com/wp-json/wp/v2/users/8'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    user = json.loads(resp.read())
    print(f"\nUser 8 (public): {user.get('name')}")
    print(f"Roles: {user.get('roles', 'N/A')}")
except urllib.error.HTTPError as e:
    print(f"User 8: {e.code} {e.reason}")

# Check wp-abilities for user 8
url = 'https://www.coinsfera.com/wp-json/wp-abilities/v1/users/8?capabilities=true'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = json.loads(resp.read())
    print(f"\nAbilities: {json.dumps(result, indent=2)[:500]}")
except urllib.error.HTTPError as e:
    print(f"\nAbilities: {e.code} {e.reason}")

# Try to see if the SG security plugin is blocking - try the SG security endpoint
url = 'https://www.coinsfera.com/wp-json/sg-security/v1/'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = json.loads(resp.read())
    print(f"\nSG Security: {json.dumps(result, indent=2)[:500]}")
except urllib.error.HTTPError as e:
    print(f"\nSG Security: {e.code} {e.reason}")

# Try to POST to a different endpoint - maybe taxonomies
# Try listing categories with context=edit to verify edit access
url = 'https://www.coinsfera.com/wp-json/wp/v2/categories?context=edit&per_page=1'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = json.loads(resp.read())
    print(f"\nCategories with context=edit: SUCCESS ({len(result)} categories)")
except urllib.error.HTTPError as e:
    print(f"\nCategories context=edit: {e.code} {e.reason}")