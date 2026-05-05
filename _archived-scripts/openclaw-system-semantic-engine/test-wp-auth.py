#!/usr/bin/env python3
"""Test WordPress REST API authentication"""

import requests
from requests.auth import HTTPBasicAuth

WP_URL = "https://rankray.com"
WP_USER = "OpenClaw"
WP_APP_PASS = "OpenClaw#Admin@2026"

print("🔐 Testing WordPress REST API authentication...")
print("=" * 70)

# Test 1: Basic auth with app password
print("\n1. Testing basic auth (user + app password)...")
try:
    resp = requests.get(f"{WP_URL}/wp-json/wp/v2/users/me", auth=HTTPBasicAuth(WP_USER, WP_APP_PASS), timeout=10)
    print(f"   Status: {resp.status_code}")
    if resp.status_code == 200:
        print(f"   ✅ Auth successful!")
        user_data = resp.json()
        print(f"   User: {user_data.get('name', 'N/A')} ({user_data.get('username', 'N/A')})")
    else:
        print(f"   ❌ Auth failed: {resp.text[:200]}")
except Exception as e:
    print(f"   ❌ Error: {e}")

# Test 2: Check if media endpoint is accessible
print("\n2. Testing media endpoint (public)...")
try:
    resp = requests.get(f"{WP_URL}/wp-json/wp/v2/media?per_page=5", timeout=10)
    print(f"   Status: {resp.status_code}")
    if resp.status_code == 200:
        media = resp.json()
        print(f"   ✅ Public access OK - {len(media)} items visible")
    else:
        print(f"   ❌ Error: {resp.text[:200]}")
except Exception as e:
    print(f"   ❌ Error: {e}")

# Test 3: Try uploading with proper headers
print("\n3. Testing media upload...")
test_file = "/Users/sheikhown/.openclaw/workspace/semantic-engine/images/downloads/semantic-seo-services-rank-ray.jpg"
try:
    with open(test_file, 'rb') as f:
        file_content = f.read()
    
    upload_url = f"{WP_URL}/wp-json/wp/v2/media"
    headers = {
        "Content-Disposition": 'attachment; filename="test-upload.jpg"',
        "Content-Type": "image/jpeg",
    }
    
    resp = requests.post(upload_url, data=file_content, headers=headers, auth=HTTPBasicAuth(WP_USER, WP_APP_PASS), timeout=60)
    print(f"   Status: {resp.status_code}")
    
    if resp.status_code == 201:
        media_data = resp.json()
        print(f"   ✅ Upload successful!")
        print(f"   Media ID: {media_data.get('id')}")
        print(f"   URL: {media_data.get('source_url')}")
    else:
        print(f"   ❌ Upload failed: {resp.status_code}")
        print(f"   Response: {resp.text[:300]}")
        
except Exception as e:
    print(f"   ❌ Error: {e}")

print("\n" + "=" * 70)
