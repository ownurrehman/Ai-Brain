#!/usr/bin/env python3
import ssl
import json
import urllib.request
import urllib.error
import base64
import mimetypes
import os

# Config
WP_URL = "https://backlinkcrypto.com/wp-json/wp/v2"
AUTH = base64.b64encode(b"openclaw:VtFT sb2q LeHr hybr 6450 Bqmc").decode("utf-8")
CATEGORY_ID = 33
AUTHOR_ID = 2
SLUG = "crypto-seo-services-hiring-agency"
TITLE = "Crypto SEO Services: What to Look for When Hiring an Agency for Blockchain Projects"
DATE = "2026-07-12T10:00:00"
AIOSEO_TITLE = "Crypto SEO Services: Hire the Right Agency | Backlink Crypto"
AIOSEO_DESC = "What to look for when hiring crypto SEO services. How to evaluate blockchain SEO agencies and avoid bad hires. Backlink Crypto"

# SSL context
ctx = ssl.create_default_context()
ctx.check_hostname = False
ctx.verify_mode = ssl.CERT_NONE

# Read content
with open("blog12_content.html", "r") as f:
    content = f.read()

def wp_request(url, method="GET", data=None, headers=None):
    """Make a WordPress REST API request."""
    req_headers = {
        "Authorization": f"Basic {AUTH}",
    }
    if headers:
        req_headers.update(headers)
    req = urllib.request.Request(url, method=method)
    for k, v in req_headers.items():
        req.add_header(k, v)
    if data:
        req.data = data
    try:
        with urllib.request.urlopen(req, context=ctx) as resp:
            body = resp.read().decode("utf-8")
            return json.loads(body) if body else {}
    except urllib.error.HTTPError as e:
        print(f"HTTP Error {e.code}: {e.read().decode('utf-8')}")
        raise
    except Exception as e:
        print(f"Error: {e}")
        raise

# Step 1: Create the post
print("=== Step 1: Creating post ===")
post_data = json.dumps({
    "title": TITLE,
    "slug": SLUG,
    "content": content,
    "status": "publish",
    "date": DATE,
    "categories": [CATEGORY_ID],
    "author": AUTHOR_ID,
}).encode("utf-8")

post = wp_request(
    f"{WP_URL}/posts",
    method="POST",
    data=post_data,
    headers={"Content-Type": "application/json"}
)
post_id = post["id"]
print(f"Post created with ID: {post_id}")

# Step 2: Set AIOSEO meta
print("\n=== Step 2: Setting AIOSEO meta ===")
meta_data = json.dumps({
    "meta": {
        "aioseo_title": AIOSEO_TITLE,
        "aioseo_description": AIOSEO_DESC,
    }
}).encode("utf-8")

meta_result = wp_request(
    f"{WP_URL}/posts/{post_id}",
    method="POST",
    data=meta_data,
    headers={"Content-Type": "application/json"}
)
print(f"AIOSEO meta set: title={meta_result.get('meta', {}).get('aioseo_title', 'N/A')}")

# Step 3: Upload featured image
print("\n=== Step 3: Uploading featured image ===")
image_path = "blog12_featured.jpg"
with open(image_path, "rb") as f:
    image_data = f.read()

boundary = "----WebKitFormBoundary7MA4YWxkTrZu0gW"
body_parts = []
body_parts.append(f"--{boundary}".encode())
body_parts.append(f'Content-Disposition: form-data; name="file"; filename="{os.path.basename(image_path)}"'.encode())
body_parts.append(f"Content-Type: {mimetypes.guess_type(image_path)[0]}".encode())
body_parts.append(b"")
body_parts.append(image_data)
body_parts.append(f"--{boundary}--".encode())
body_parts.append(b"")
body_data = b"\r\n".join(body_parts)

media = wp_request(
    f"{WP_URL}/media",
    method="POST",
    data=body_data,
    headers={
        "Content-Type": f"multipart/form-data; boundary={boundary}",
        "Content-Disposition": f'attachment; filename="{os.path.basename(image_path)}"',
    }
)
media_id = media["id"]
print(f"Media uploaded with ID: {media_id}")

# Step 4: Set featured image on post
print("\n=== Step 4: Setting featured image ===")
featured_data = json.dumps({"featured_media": media_id}).encode("utf-8")
featured_result = wp_request(
    f"{WP_URL}/posts/{post_id}",
    method="POST",
    data=featured_data,
    headers={"Content-Type": "application/json"}
)
print(f"Featured media set: {featured_result.get('featured_media', 'N/A')}")

# Step 5: Verify
print("\n=== Step 5: Verification ===")
verify = wp_request(f"{WP_URL}/posts/{post_id}")
import re
verify_content = re.sub(r'<[^>]+>', ' ', verify["content"]["rendered"])
word_count = len(verify_content.split())
print(f"Post URL: {verify['link']}")
print(f"Word count: {word_count}")
print(f"Status: {verify['status']}")
print(f"Slug: {verify['slug']}")
print(f"Featured media: {verify['featured_media']}")
print(f"Categories: {verify['categories']}")

if word_count < 2000:
    print("WARNING: Word count is below 2000!")
else:
    print("Word count check: PASSED")

print(f"\nDone! Post published at: {verify['link']}")