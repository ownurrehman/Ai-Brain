#!/usr/bin/env python3
"""Upload images using WordPress REST API key"""

import requests
import os
from pathlib import Path
import json

WP_URL = "https://rankray.com"
REST_API_KEY = "6Zz9 5gJL 8uyA QH4g RQDH GV1j"  # From .env

# Try different auth methods
print("🔐 Testing REST API authentication methods...")
print("=" * 70)

# Method 1: API key in header
print("\n1. Testing API key in Authorization header...")
headers = {"Authorization": f"Bearer {REST_API_KEY}"}
try:
    resp = requests.get(f"{WP_URL}/wp-json/wp/v2/users/me", headers=headers, timeout=10)
    print(f"   Status: {resp.status_code}")
    if resp.status_code == 200:
        user = resp.json()
        print(f"   ✅ Success! User: {user.get('name', 'N/A')}")
    else:
        print(f"   ❌ Failed: {resp.text[:150]}")
except Exception as e:
    print(f"   ❌ Error: {e}")

# Method 2: API key as query param
print("\n2. Testing API key as query parameter...")
try:
    resp = requests.get(f"{WP_URL}/wp-json/wp/v2/users/me?rest_api_key={REST_API_KEY}", timeout=10)
    print(f"   Status: {resp.status_code}")
    if resp.status_code == 200:
        print(f"   ✅ Success!")
    else:
        print(f"   ❌ Failed: {resp.text[:150]}")
except Exception as e:
    print(f"   ❌ Error: {e}")

# Method 3: Application password with correct username
print("\n3. Testing application password (openclaw user)...")
from requests.auth import HTTPBasicAuth
try:
    resp = requests.get(
        f"{WP_URL}/wp-json/wp/v2/users/me",
        auth=HTTPBasicAuth("openclaw", "OpenClaw#Admin@2026"),
        timeout=10
    )
    print(f"   Status: {resp.status_code}")
    if resp.status_code == 200:
        user = resp.json()
        print(f"   ✅ Success! User: {user.get('name', 'N/A')}")
        
        # Try upload
        print("\n4. Testing media upload...")
        test_file = "/Users/sheikhown/.openclaw/workspace/semantic-engine/images/downloads/semantic-seo-services-rank-ray.jpg"
        with open(test_file, 'rb') as f:
            content = f.read()
        
        upload_headers = {
            "Content-Disposition": 'attachment; filename="test.jpg"',
            "Content-Type": "image/jpeg",
        }
        
        resp = requests.post(
            f"{WP_URL}/wp-json/wp/v2/media",
            data=content,
            headers=upload_headers,
            auth=HTTPBasicAuth("openclaw", "OpenClaw#Admin@2026"),
            timeout=60
        )
        
        print(f"   Upload status: {resp.status_code}")
        if resp.status_code == 201:
            media = resp.json()
            print(f"   ✅ Upload successful!")
            print(f"   Media ID: {media.get('id')}")
            print(f"   URL: {media.get('source_url')}")
        else:
            print(f"   ❌ Upload failed: {resp.text[:200]}")
            
    else:
        print(f"   ❌ Failed: {resp.text[:150]}")
except Exception as e:
    print(f"   ❌ Error: {e}")

print("\n" + "=" * 70)
