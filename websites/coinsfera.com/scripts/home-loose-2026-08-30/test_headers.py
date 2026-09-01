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

# First check what the server actually sees - try to read response headers
url = 'https://www.coinsfera.com/wp-json/wp/v2/posts/4435'
headers = {
    'Authorization': f'Basic {credentials}',
    'Content-Type': 'application/json'
}

req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    # Check all headers
    print("Response headers:")
    for h, v in resp.headers.items():
        print(f"  {h}: {v}")
    post = json.loads(resp.read())
    # Check the _links for the current user's permissions
    links = post.get('_links', {})
    self_link = links.get('self', [{}])[0]
    print(f"\nSelf link allow: {self_link.get('targetHints', {}).get('allow', 'N/A')}")
    # Check if there's a 'wp:action-edit' link
    for key, val in links.items():
        if 'action' in key or 'edit' in key:
            print(f"Link {key}: {val}")
except urllib.error.HTTPError as e:
    print(f"Error: {e.code}")
    for h, v in e.headers.items():
        print(f"  {h}: {v}")

# Try passing auth as URL parameter (some SG setups strip Authorization headers)
print("\n=== Try auth via URL param ===")
url_auth = f'https://www.coinsfera.com/wp-json/wp/v2/posts/4435?_method=POST&context=edit'
# This won't work for auth but let's try a different approach

# Try with Bearer token instead of Basic
print("\n=== Try Bearer token ===")
headers_bearer = {
    'Authorization': f'Bearer {credentials}',
    'Content-Type': 'application/json'
}
req = urllib.request.Request(url, headers=headers_bearer)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    post = json.loads(resp.read())
    self_link = post.get('_links', {}).get('self', [{}])[0]
    print(f"Bearer auth self link allow: {self_link.get('targetHints', {}).get('allow', 'N/A')}")
except urllib.error.HTTPError as e:
    print(f"Bearer: {e.code} {e.reason}")

# Check if the site uses a custom auth - try with X-WP-Nonce
# First need to get a nonce from the page
print("\n=== Get nonce from page ===")
req = urllib.request.Request('https://www.coinsfera.com/')
try:
    resp = urllib.request.urlopen(req, context=ctx)
    html = resp.read().decode('utf-8', errors='ignore')
    import re
    nonce_match = re.search(r'var wpApiSettings.*?nonce["\s:=]+([a-f0-9]+)', html)
    if nonce_match:
        print(f"Found nonce: {nonce_match.group(1)}")
    else:
        # Try other patterns
        nonce_match2 = re.search(r'"nonce":"([a-f0-9]+)"', html)
        if nonce_match2:
            print(f"Found nonce: {nonce_match2.group(1)}")
        else:
            print("No nonce found in page")
            # Check if there's a wp-json URL in the HTML
            api_match = re.search(r'rest_api.*?url["\s:=]+(https?://[^"]+)', html)
            if api_match:
                print(f"API URL: {api_match.group(1)}")
except urllib.error.HTTPError as e:
    print(f"Page fetch: {e.code} {e.reason}")