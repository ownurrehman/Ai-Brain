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

# List all available yoast routes
url = 'https://www.coinsfera.com/wp-json/yoast/v1'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = json.loads(resp.read())
    routes = result.get('routes', {})
    for route in routes:
        methods = routes[route].get('methods', [])
        print(f"{route}: {methods}")
except urllib.error.HTTPError as e:
    print(f"Yoast routes: {e.code} {e.reason}")

# List all wp/v2 routes
print("\n=== WP v2 routes ===")
url = 'https://www.coinsfera.com/wp-json/wp/v2'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = json.loads(resp.read())
    routes = result.get('routes', {})
    for route in sorted(routes.keys()):
        methods = routes[route].get('methods', [])
        if 'POST' in methods or 'PUT' in methods or 'PATCH' in methods:
            print(f"{route}: {methods}")
except urllib.error.HTTPError as e:
    print(f"WP v2: {e.code} {e.reason}")