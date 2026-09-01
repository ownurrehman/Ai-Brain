import ssl
import json
import urllib.request
import urllib.error
import base64

# WordPress credentials
WP_USER = 'openclaw'
WP_APP_PASS = 'VtFT sb2q LeHr hybr 6450 Bqmc'
WP_URL = 'https://backlinkcrypto.com/wp-json/wp/v2'
AUTH = base64.b64encode(f'{WP_USER}:{WP_APP_PASS}'.encode()).decode()

# SSL context
ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

# Read blog content
with open('/Users/sheikhown/blog13_content.html', 'r') as f:
    content = f.read()

# Step 1: Upload featured image
print("Uploading featured image...")
boundary = '----WebKitFormBoundary7MA4YWxkTrZu0gW'
with open('/Users/sheikhown/featured_image.jpg', 'rb') as f:
    image_data = f.read()

body = (
    f'--{boundary}\r\n'
    f'Content-Disposition: form-data; name="file"; filename="featured-keyword-research.jpg"\r\n'
    f'Content-Type: image/jpeg\r\n\r\n'
).encode() + image_data + f'\r\n--{boundary}\r\n'.encode()

# Add title field
body += (
    f'Content-Disposition: form-data; name="title"\r\n\r\n'
    f'Cryptocurrency Keyword Research Featured Image\r\n'
    f'--{boundary}--\r\n'
).encode()

req = urllib.request.Request(
    f'{WP_URL}/media',
    data=body,
    headers={
        'Authorization': f'Basic {AUTH}',
        'Content-Type': f'multipart/form-data; boundary={boundary}',
    }
)
resp = urllib.request.urlopen(req, context=ctx)
media_data = json.loads(resp.read())
media_id = media_data['id']
print(f"Featured image uploaded. Media ID: {media_id}")

# Step 2: Create the post
print("Creating blog post...")
post_data = {
    'title': 'Cryptocurrency Keyword Research: Finding Terms That Convert for Blockchain Sites',
    'slug': 'cryptocurrency-keyword-research-blockchain',
    'content': content,
    'status': 'publish',
    'date': '2026-07-13T10:00:00',
    'categories': [33],
    'author': 2,
    'featured_media': media_id,
    'meta': {
        'aioseo_title': 'Crypto Keyword Research for Blockchain Sites | Backlink Crypto',
        'aioseo_description': 'Cryptocurrency keyword research guide. Find crypto terms that convert and build keyword clusters for blockchain SEO. Backlink Crypto',
    }
}

req2 = urllib.request.Request(
    f'{WP_URL}/posts',
    data=json.dumps(post_data).encode(),
    headers={
        'Authorization': f'Basic {AUTH}',
        'Content-Type': 'application/json',
    }
)
resp2 = urllib.request.urlopen(req2, context=ctx)
post_data_resp = json.loads(resp2.read())
post_id = post_data_resp['id']
post_link = post_data_resp['link']
print(f"Post published! ID: {post_id}, URL: {post_link}")

# Verify word count
import re
text = re.sub(r'<[^>]+>', ' ', content)
words = text.split()
print(f"Word count: {len(words)}")

print("\nDone!")