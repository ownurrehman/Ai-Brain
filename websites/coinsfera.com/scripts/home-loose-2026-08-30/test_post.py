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

# Fetch media
url = f'https://www.coinsfera.com/wp-json/wp/v2/media/4436'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    media = json.loads(resp.read())
    print(f"Media ID: {media['id']}")
    print(f"Source URL: {media.get('source_url', 'N/A')}")
    print(f"Alt text: {media.get('alt_text', 'N/A')}")
    print(f"Title: {media.get('title', {}).get('rendered', 'N/A')}")
except urllib.error.HTTPError as e:
    print(f"Media fetch HTTP Error: {e.code} {e.reason}")
    print(e.read().decode()[:500])

# Test if we can POST (try updating date only to current date)
print("\n=== Testing POST capability ===")
post_data = json.dumps({"date": "2025-07-26T07:21:12"}).encode('utf-8')
url = f'https://www.coinsfera.com/wp-json/wp/v2/posts/4435'
req = urllib.request.Request(url, data=post_data, headers=headers, method='POST')
try:
    resp = urllib.request.urlopen(req, context=ctx)
    result = json.loads(resp.read())
    print(f"POST successful! Post ID: {result['id']}")
    print(f"Date: {result['date']}")
except urllib.error.HTTPError as e:
    print(f"POST HTTP Error: {e.code} {e.reason}")
    print(e.read().decode()[:500])