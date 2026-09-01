import urllib.request
import ssl
import json
import base64

ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

username = 'SheikhOpen'
password = 'T92W7D1oaUYtCUICnCmXC0mb'  # no spaces
credentials = base64.b64encode(f'{username}:{password}'.encode()).decode()

headers = {
    'Authorization': f'Basic {credentials}',
    'Content-Type': 'application/json'
}

# Fetch post without context=edit
url = f'https://www.coinsfera.com/wp-json/wp/v2/posts/4435'
req = urllib.request.Request(url, headers=headers)
try:
    resp = urllib.request.urlopen(req, context=ctx)
    post = json.loads(resp.read())
    print(f"ID: {post['id']}")
    print(f"Title: {post['title']['rendered']}")
    print(f"Slug: {post['slug']}")
    print(f"Status: {post['status']}")
    print(f"Date: {post['date']}")
    print(f"Featured Media: {post.get('featured_media', 0)}")
    print(f"Word count (rendered): {len(post['content']['rendered'].split())}")
    print(f"\n=== RENDERED CONTENT ===")
    print(post['content']['rendered'])
    print(f"\n=== META ===")
    for k, v in post.get('meta', {}).items():
        if 'yoast' in k.lower():
            print(f"{k}: {v}")
    print(f"\n=== LINKS ===")
    print(json.dumps(post.get('_links', {}), indent=2)[:500])
except urllib.error.HTTPError as e:
    print(f"Post fetch HTTP Error: {e.code} {e.reason}")
    print(e.read().decode()[:500])