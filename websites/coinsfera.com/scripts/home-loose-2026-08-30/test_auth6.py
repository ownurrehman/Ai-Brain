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

# Check user abilities
url = 'https://www.coinsfera.com/wp-json/wp-abilities/v1/users/8'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = json.loads(resp.read())
    print(f"Abilities: {json.dumps(result, indent=2)[:500]}")
except urllib.error.HTTPError as e:
    print(f"Abilities: {e.code} {e.reason}")

# Try yoast API for meta
url = 'https://www.coinsfera.com/wp-json/yoast/v1/get_data?object_type=post&object_id=4435'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = json.loads(resp.read())
    print(f"\nYoast data: {json.dumps(result, indent=2)[:500]}")
except urllib.error.HTTPError as e:
    print(f"\nYoast data: {e.code} {e.reason}")
    print(e.read().decode()[:300])

# Try the old XMLRPC approach with system.listMethods
print("\n=== XMLRPC with metaWeblog ===")
xmlrpc_data = '''<?xml version="1.0"?>
<methodCall>
<methodName>metaWeblog.getPost</methodName>
<params>
<param><value><string>4435</string></value></param>
<param><value><string>SheikhOpen</string></value></param>
<param><value><string>T92W7D1oaUYtCUICnCmXC0mb</string></value></param>
</params>
</methodCall>'''.encode('utf-8')

url = 'https://www.coinsfera.com/xmlrpc.php'
req = urllib.request.Request(url, data=xmlrpc_data, headers={'Content-Type': 'text/xml', 'User-Agent': 'Python'})
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = resp.read().decode()
    print(f"metaWeblog response (first 800 chars): {result[:800]}")
except urllib.error.HTTPError as e:
    print(f"metaWeblog: {e.code} {e.reason}")
    print(e.read().decode()[:500])