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

# Try GET on users/me with no-spaces password directly (no loop)
url = 'https://www.coinsfera.com/wp-json/wp/v2/users/me'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    user = json.loads(resp.read())
    print(f"users/me SUCCESS: {user.get('name')} ID: {user.get('id')}")
    print(f"Roles: {user.get('roles', 'N/A')}")
except urllib.error.HTTPError as e:
    print(f"users/me: {e.code} {e.reason}")
    body = e.read().decode()
    print(f"  {body[:300]}")

# Try GET /wp-json/wp/v2/posts/4435?context=edit
url = f'https://www.coinsfera.com/wp-json/wp/v2/posts/4435?context=edit'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    post = json.loads(resp.read())
    print(f"\ncontext=edit SUCCESS: {post['id']}")
    print(f"Raw content length: {len(post['content']['raw'])}")
except urllib.error.HTTPError as e:
    print(f"\ncontext=edit: {e.code} {e.reason}")
    body = e.read().decode()
    print(f"  {body[:300]}")

# Try POST with minimal change
print("\n=== POST test ===")
post_data = json.dumps({"date": "2025-07-26T07:21:12"}).encode('utf-8')
url = f'https://www.coinsfera.com/wp-json/wp/v2/posts/4435'
req = urllib.request.Request(url, data=post_data, headers=headers, method='POST')
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = json.loads(resp.read())
    print(f"POST SUCCESS: {result['id']}")
except urllib.error.HTTPError as e:
    print(f"POST: {e.code} {e.reason}")
    body = e.read().decode()
    print(f"  {body[:300]}")

# Try with X-HTTP-Method-Override
print("\n=== POST via GET with method override ===")
req2 = urllib.request.Request(url, data=post_data, headers={**headers, 'X-HTTP-Method-Override': 'PUT'}, method='POST')
try:
    resp2 = urllib.request.urlopen(req2, context=ctx)
    result2 = json.loads(resp2.read())
    print(f"Override POST SUCCESS: {result2['id']}")
except urllib.error.HTTPError as e:
    print(f"Override POST: {e.code} {e.reason}")
    body = e.read().decode()
    print(f"  {body[:300]}")