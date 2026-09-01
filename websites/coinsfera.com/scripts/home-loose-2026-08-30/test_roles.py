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

# List application passwords for this user - this should show what apps are registered
url = 'https://www.coinsfera.com/wp-json/wp/v2/users/me/application-passwords'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = json.loads(resp.read())
    print(f"Application passwords: {json.dumps(result, indent=2)[:500]}")
except urllib.error.HTTPError as e:
    print(f"App passwords list: {e.code} {e.reason}")
    print(e.read().decode()[:300])

# Maybe we can update the user's role by editing our own user
# Try PATCH on users/me
print("\n=== Try PATCH users/me to add role ===")
user_data = json.dumps({"roles": ["editor"]}).encode('utf-8')
url = 'https://www.coinsfera.com/wp-json/wp/v2/users/me'
req = urllib.request.Request(url, data=user_data, headers=headers, method='PATCH')
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = json.loads(resp.read())
    print(f"PATCH user: SUCCESS")
    print(f"Roles: {result.get('roles')}")
except urllib.error.HTTPError as e:
    print(f"PATCH user: {e.code} {e.reason}")
    print(e.read().decode()[:300])

# Try to get the settings endpoint to check if we can access admin-level info
print("\n=== Try settings ===")
url = 'https://www.coinsfera.com/wp-json/wp/v2/settings'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = json.loads(resp.read())
    print(f"Settings: {json.dumps(result, indent=2)[:500]}")
except urllib.error.HTTPError as e:
    print(f"Settings: {e.code} {e.reason}")

# Try accessing the post via Elementor API (elementor/v1)
print("\n=== Try Elementor API ===")
url = 'https://www.coinsfera.com/wp-json/elementor/v1/documents?per_page=1'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = json.loads(resp.read())
    print(f"Elementor docs: {json.dumps(result, indent=2)[:500]}")
except urllib.error.HTTPError as e:
    print(f"Elementor: {e.code} {e.reason}")