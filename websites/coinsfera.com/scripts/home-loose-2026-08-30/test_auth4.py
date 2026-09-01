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

# Check if we can read our own user info through a different endpoint
# Try /wp-json/wp/v2/users without context
url = 'https://www.coinsfera.com/wp-json/wp/v2/users?per_page=1'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    users = json.loads(resp.read())
    print(f"Users list: {len(users)} users")
    for u in users:
        print(f"  ID: {u['id']} Name: {u['name']}")
except urllib.error.HTTPError as e:
    print(f"Users list: {e.code} {e.reason}")

# Try to read post types and capabilities
url = 'https://www.coinsfera.com/wp-json/wp/v2/types/post'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    types = json.loads(resp.read())
    print(f"\nPost type supports: {types.get('supports', [])}")
    print(f"REST base: {types.get('rest_base', 'N/A')}")
except urllib.error.HTTPError as e:
    print(f"Types: {e.code} {e.reason}")

# Try using the application password with the wp-admin XMLRPC interface instead
print("\n=== Try XMLRPC ===")
xmlrpc_data = '''<?xml version="1.0"?>
<methodCall>
<methodName>wp.getPost</methodName>
<params>
<param><value><int>1</int></value></param>
<param><value><string>SheikhOpen</string></value></param>
<param><value><string>T92W7D1oaUYtCUICnCmXC0mb</string></value></param>
<param><value><int>4435</int></value></param>
</params>
</methodCall>'''.encode('utf-8')

url = 'https://www.coinsfera.com/xmlrpc.php'
req = urllib.request.Request(url, data=xmlrpc_data, headers={'Content-Type': 'text/xml'})
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = resp.read().decode()
    print(f"XMLRPC response (first 500 chars): {result[:500]}")
except urllib.error.HTTPError as e:
    print(f"XMLRPC: {e.code} {e.reason}")
    print(e.read().decode()[:500])