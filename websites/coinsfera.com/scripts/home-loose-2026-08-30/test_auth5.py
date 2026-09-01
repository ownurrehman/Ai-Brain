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

# List all users to see who has edit capability
url = 'https://www.coinsfera.com/wp-json/wp/v2/users?per_page=100'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    users = json.loads(resp.read())
    print(f"Total users found: {len(users)}")
    for u in users:
        print(f"  ID: {u['id']} Name: {u['name']} Roles: {u.get('roles', 'N/A')} URL: {u.get('url', '')}")
except urllib.error.HTTPError as e:
    print(f"Users list: {e.code} {e.reason}")
    print(e.read().decode()[:300])

# Check the post author - post 4435 is authored by user 8
# The user might need to be the post author or have edit capabilities
# Let's check if there are other application password formats

# Also try the REST API root to check available endpoints
url = 'https://www.coinsfera.com/wp-json/'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    root = json.loads(resp.read())
    print(f"\nSite name: {root.get('name', 'N/A')}")
    print(f"Authentication: {json.dumps(root.get('authentication', {}), indent=2)}")
    print(f"Namespaces: {root.get('namespaces', [])}")
except urllib.error.HTTPError as e:
    print(f"Root: {e.code} {e.reason}")