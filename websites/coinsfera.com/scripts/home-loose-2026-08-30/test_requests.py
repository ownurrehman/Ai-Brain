import requests
import json
import urllib3

# Disable SSL warnings
urllib3.disable_warnings()

username = 'SheikhOpen'
password = 'T92W7D1oaUYtCUICnCmXC0mb'

# Test with requests library - it handles auth differently
session = requests.Session()
session.verify = False
session.auth = (username, password)

# Test users/me
resp = session.get('https://www.coinsfera.com/wp-json/wp/v2/users/me')
print(f"users/me: {resp.status_code}")
if resp.status_code == 200:
    user = resp.json()
    print(f"  User: {user.get('name')} ID: {user.get('id')} Roles: {user.get('roles')}")
else:
    print(f"  {resp.text[:200]}")

# Test POST
resp = session.post(
    'https://www.coinsfera.com/wp-json/wp/v2/posts/4435',
    json={"date": "2025-07-26T07:21:12"},
    headers={'Content-Type': 'application/json'}
)
print(f"\nPOST: {resp.status_code}")
print(f"  {resp.text[:200]}")

# Also try with explicit header
print("\n=== Try explicit Authorization header ===")
import base64
credentials = base64.b64encode(f'{username}:{password}'.encode()).decode()
resp = requests.get(
    'https://www.coinsfera.com/wp-json/wp/v2/users/me',
    headers={'Authorization': f'Basic {credentials}'},
    verify=False
)
print(f"Explicit header: {resp.status_code}")
print(f"  {resp.text[:200]}")

# Try with application password containing spaces
credentials_spaces = base64.b64encode(f'{username}:T92W 7D1o aUYt CUIC nCmX C0mb'.encode()).decode()
resp = requests.get(
    'https://www.coinsfera.com/wp-json/wp/v2/users/me',
    headers={'Authorization': f'Basic {credentials_spaces}'},
    verify=False
)
print(f"\nWith spaces: {resp.status_code}")
print(f"  {resp.text[:200]}")