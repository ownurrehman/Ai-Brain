import urllib.request
import ssl
import json
import base64

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

# Try different password formats
passwords = [
    'T92W 7D1o aUYt CUIC nCmX C0mb',  # with spaces
    'T92W7D1oaUYtCUICnCmXC0mb',  # no spaces
    'T92W7D1o aUYt CUIC nCmX C0mb',  # partial spaces
]

headers_base = {
    'Content-Type': 'application/json'
}

for i, pw in enumerate(passwords):
    credentials = base64.b64encode(f'SheikhOpen:{pw}'.encode()).decode()
    headers = dict(headers_base)
    headers['Authorization'] = f'Basic {credentials}'
    
    # Test with users/me endpoint
    url = 'https://www.coinsfera.com/wp-json/wp/v2/users/me'
    req = urllib.request.Request(url, headers=headers)
    try:
        resp = urllib.request.urlopen(req, context=ctx)
        user = json.loads(resp.read())
        print(f"Password {i}: SUCCESS - User: {user.get('name')} ID: {user.get('id')}")
        
        # Now try POST
        post_data = json.dumps({"date": "2025-07-26T07:21:12"}).encode('utf-8')
        url = f'https://www.coinsfera.com/wp-json/wp/v2/posts/4435'
        req = urllib.request.Request(url, data=post_data, headers=headers, method='POST')
        try:
            resp = urllib.request.urlopen(req, context=ctx)
            result = json.loads(resp.read())
            print(f"  POST: SUCCESS - Post ID: {result['id']}")
        except urllib.error.HTTPError as e:
            print(f"  POST: {e.code} {e.reason}")
            print(f"  {e.read().decode()[:200]}")
    except urllib.error.HTTPError as e:
        print(f"Password {i}: {e.code} {e.reason}")
        print(f"  {e.read().decode()[:200]}")