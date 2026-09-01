import urllib.request
import ssl
import json
import base64

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

# Try the password EXACTLY as given with spaces
username = 'SheikhOpen'
password = 'T92W 7D1o aUYt CUIC nCmX C0mb'
credentials = base64.b64encode(f'{username}:{password}'.encode()).decode()

headers = {
    'Authorization': f'Basic {credentials}',
    'Content-Type': 'application/json'
}

# Try the users/me endpoint
url = 'https://www.coinsfera.com/wp-json/wp/v2/users/me'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    user = json.loads(resp.read())
    print(f"users/me SUCCESS: {user.get('name')} ID: {user.get('id')}")
    print(f"Roles: {user.get('roles', 'N/A')}")
except urllib.error.HTTPError as e:
    print(f"users/me with spaces: {e.code} {e.reason}")

# Try POST
print("\n=== POST test with spaces ===")
post_data = json.dumps({"date": "2025-07-26T07:21:12"}).encode('utf-8')
url = f'https://www.coinsfera.com/wp-json/wp/v2/posts/4435'
req = urllib.request.Request(url, data=post_data, headers=headers, method='POST')
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = json.loads(resp.read())
    print(f"POST SUCCESS: {result['id']}")
except urllib.error.HTTPError as e:
    print(f"POST with spaces: {e.code} {e.reason}")
    body = e.read().decode()
    print(f"  {body[:300]}")

# Try the no-spaces password for GET on users/me (different endpoint)
password2 = 'T92W7D1oaUYtCUICnCmXC0mb'
credentials2 = base64.b64encode(f'{username}:{password2}'.encode()).decode()
headers2 = {
    'Authorization': f'Basic {credentials2}',
    'Content-Type': 'application/json'
}

# Try POST with no-spaces
print("\n=== POST test no-spaces ===")
req = urllib.request.Request(url, data=post_data, headers=headers2, method='POST')
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = json.loads(resp.read())
    print(f"POST no-spaces SUCCESS: {result['id']}")
except urllib.error.HTTPError as e:
    print(f"POST no-spaces: {e.code} {e.reason}")
    body = e.read().decode()
    print(f"  {body[:300]}")

# Maybe the user just can't edit post 4435 specifically - try listing all posts they can access
print("\n=== Try creating a test post ===")
post_data2 = json.dumps({"title": "TEST", "content": "test", "status": "draft"}).encode('utf-8')
url2 = f'https://www.coinsfera.com/wp-json/wp/v2/posts'
req = urllib.request.Request(url2, data=post_data2, headers=headers2, method='POST')
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = json.loads(resp.read())
    print(f"CREATE SUCCESS: {result['id']}")
except urllib.error.HTTPError as e:
    print(f"CREATE: {e.code} {e.reason}")
    body = e.read().decode()
    print(f"  {body[:300]}")