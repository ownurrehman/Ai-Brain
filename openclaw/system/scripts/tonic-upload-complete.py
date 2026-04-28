#!/usr/bin/env python3
"""
Complete image upload for TonicPhysio - Pediatric + Orthopedic pages
Uses WordPress REST API with proper authentication
"""

import requests
import base64
import os
from pathlib import Path
import time

# Config
WP_URL = "https://tonicphysio.com"
WP_ADMIN_EMAIL = "Dan"
WP_ADMIN_PASSWORD = "RR#Tonic@2026"
WP_APP_PASSWORD = "jS9z Oyu6 wLKH sy1A qeLM Ikwz"  # openclaw user app password
WP_REST_USER = "Dan"
WP_REST_API_KEY = "4vFk 18fN UlLB twaw B2hU 0kRE"  # REST API key (NOT app password)
WORKSPACE = Path("/Users/sheikhown/.openclaw/workspace")

# Create session with retries
session = requests.Session()
session.headers.update({
    'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
})
adapter = requests.adapters.HTTPAdapter(
    max_retries=requests.adapters.Retry(
        total=3,
        backoff_factor=2,
        status_forcelist=[429, 500, 502, 503, 504]
    )
)
session.mount('https://', adapter)

# Pages to update
PAGES = {
    "pediatric": {
        "id": 1793,
        "slug": "pediatric-physiotherapy",
        "url": "/physiotherapy-in-milton/pediatric-physiotherapy/"
    },
    "orthopedic": {
        "id": 1791,
        "slug": "orthopedic-physiotherapy", 
        "url": "/physiotherapy-in-milton/orthopedic-physiotherapy/"
    }
}

def get_auth_basic(email, password):
    """Basic auth header"""
    credentials = f"{email}:{password}"
    token = base64.b64encode(credentials.encode()).decode()
    return {"Authorization": f"Basic {token}"}

def test_auth():
    """Test if REST API key works for REST API"""
    url = f"{WP_URL}/wp-json/wp/v2/users/me?context=edit"
    
    # Try REST API key first (CORRECT METHOD)
    try:
        response = session.get(url, headers=get_auth_basic(WP_REST_USER, WP_REST_API_KEY), timeout=30)
        
        if response.status_code == 200:
            user = response.json()
            print(f"✅ Authenticated as: {user.get('name', 'Unknown')} ({user.get('slug', 'unknown')})")
            return True
        else:
            print(f"⚠️ REST API key failed: {response.status_code}")
            print(response.text)
    except Exception as e:
        print(f"⚠️ REST API key connection error: {e}")
    
    # Fallback to app password
    try:
        response = session.get(url, headers=get_auth_basic("openclaw", WP_APP_PASSWORD), timeout=30)
        
        if response.status_code == 200:
            user = response.json()
            print(f"✅ Authenticated as: {user.get('name', 'Unknown')} ({user.get('slug', 'unknown')})")
            return True
        else:
            print(f"⚠️ App password failed: {response.status_code}")
    except Exception as e:
        print(f"⚠️ App password connection error: {e}")
    
    # Last resort: admin password
    try:
        response = session.get(url, headers=get_auth_basic(WP_ADMIN_EMAIL, WP_ADMIN_PASSWORD), timeout=30)
        
        if response.status_code == 200:
            user = response.json()
            print(f"✅ Authenticated as: {user.get('name', 'Unknown')} ({user.get('slug', 'unknown')})")
            return True
        else:
            print(f"❌ Admin password failed: {response.status_code}")
            print(response.text)
            return False
    except Exception as e:
        print(f"❌ Connection error: {e}")
        return False

def upload_image(filepath, title, alt_text):
    """Upload image to WordPress media library"""
    url = f"{WP_URL}/wp-json/wp/v2/media?context=edit"
    
    if not filepath.exists():
        print(f"❌ File not found: {filepath}")
        return None
    
    with open(filepath, 'rb') as f:
        file_data = f.read()
    
    filename = filepath.name
    
    print(f"⬆️ Uploading: {filename}...")
    
    try:
        response = session.post(
            url,
            headers=get_auth_basic(WP_REST_USER, WP_REST_API_KEY),
            files={'file': (filename, file_data, 'image/jpeg')},
            data={
                'title': title,
                'alt_text': alt_text,
                'caption': title,
                'description': title
            },
            timeout=60
        )
        
        if response.status_code == 201:
            data = response.json()
            media_id = data['id']
            print(f"   ✅ Media ID: {media_id}")
            print(f"   URL: {data.get('source_url', 'N/A')}")
            return media_id
        else:
            print(f"   ❌ Failed: {response.status_code}")
            try:
                error = response.json()
                print(f"   Error: {error.get('message', 'Unknown')}")
            except:
                print(f"   Response: {response.text[:200]}")
            return None
    except Exception as e:
        print(f"   ❌ Connection error: {e}")
        return None

def update_page_acf(page_id, page_name, why_image_id, solutions_image_id):
    """Update ACF image fields on a page"""
    url = f"{WP_URL}/wp-json/wp/v2/pages/{page_id}"
    
    print(f"\n🔗 Updating {page_name} (ID: {page_id})...")
    
    try:
        # Get current page first
        response = session.get(url, headers=get_auth_basic(WP_REST_USER, WP_REST_API_KEY), timeout=30)
        if response.status_code != 200:
            print(f"   ❌ Cannot fetch page: {response.status_code}")
            return False
        
        # Update ACF fields
        update_data = {
            "acf": {
                "why_choose_us_image": why_image_id,
                "solutions_image": solutions_image_id
            },
            "status": "publish"  # Ensure it stays published
        }
        
        response = session.post(
            url,
            headers=get_auth_basic(WP_REST_USER, WP_REST_API_KEY),
            json=update_data,
            timeout=30
        )
        
        if response.status_code == 200:
            print(f"   ✅ ACF fields updated successfully")
            return True
        else:
            print(f"   ❌ Failed: {response.status_code}")
            try:
                error = response.json()
                print(f"   Error: {error.get('message', 'Unknown')}")
            except:
                print(f"   Response: {response.text[:200]}")
            return False
    except Exception as e:
        print(f"   ❌ Connection error: {e}")
        return False

def verify_page(page_url, page_name):
    """Verify page loads and has images"""
    print(f"\n👁️ Verifying {page_name}...")
    full_url = f"{WP_URL}{page_url}"
    
    try:
        response = session.get(full_url, timeout=30)
        if response.status_code == 200:
            content = response.text
            # Check if ACF image sections are present
            if "why_choose_us_image" in content or "solutions_image" in content:
                print(f"   ✅ Page loads and contains ACF image references")
            else:
                print(f"   ⚠️ Page loads but ACF images may not be visible")
            print(f"   URL: {full_url}")
            return True
        else:
            print(f"   ❌ Page failed to load: {response.status_code}")
            return False
    except Exception as e:
        print(f"   ❌ Connection error: {e}")
        return False

def main():
    print("="*70)
    print("TONIC PHYSIO - IMAGE UPLOAD & ACF CONNECTION")
    print("="*70)
    
    # Test authentication first
    print("\n🔐 Testing authentication...")
    if not test_auth():
        print("\n❌ Authentication failed. Cannot proceed.")
        return
    
    # Define images to upload
    images = [
        {
            'path': WORKSPACE / "pediatric-physiotherapy-why-choose.jpg",
            'title': 'Pediatric Physiotherapy Milton - Why Choose Us',
            'alt': 'Pediatric Physiotherapy Milton - Child Development Treatment Center',
            'page': 'pediatric',
            'field': 'why'
        },
        {
            'path': WORKSPACE / "pediatric-physiotherapy-solutions.jpg",
            'title': 'Pediatric Physiotherapy Milton - Treatment Solutions',
            'alt': 'Pediatric Physiotherapy Milton - Children Treatment Solutions',
            'page': 'pediatric',
            'field': 'solutions'
        },
        {
            'path': WORKSPACE / "orthopedic-physiotherapy-why-choose.jpg",
            'title': 'Orthopedic Physiotherapy Milton - Why Choose Us',
            'alt': 'Orthopedic Physiotherapy Milton - Joint and Bone Treatment Specialist',
            'page': 'orthopedic',
            'field': 'why'
        },
        {
            'path': WORKSPACE / "orthopedic-physiotherapy-solutions.jpg",
            'title': 'Orthopedic Physiotherapy Milton - Treatment Solutions',
            'alt': 'Orthopedic Physiotherapy Milton - Joint Pain Treatment Solutions',
            'page': 'orthopedic',
            'field': 'solutions'
        }
    ]
    
    # Upload all images
    print("\n📤 UPLOADING IMAGES")
    print("-"*70)
    
    media_ids = {
        'pediatric': {},
        'orthopedic': {}
    }
    
    for img in images:
        media_id = upload_image(img['path'], img['title'], img['alt'])
        if media_id:
            media_ids[img['page']][img['field']] = media_id
    
    # Check if all uploads succeeded
    print("\n📋 UPLOAD SUMMARY")
    print("-"*70)
    for page_key, page_data in PAGES.items():
        why_id = media_ids[page_key].get('why', 'FAILED')
        sol_id = media_ids[page_key].get('solutions', 'FAILED')
        print(f"{page_key.capitalize():15} Why: {why_id:10} Solutions: {sol_id}")
    
    # Verify all images uploaded before proceeding
    all_uploaded = all(
        media_ids[page].get('why') and media_ids[page].get('solutions')
        for page in ['pediatric', 'orthopedic']
    )
    
    if not all_uploaded:
        print("\n❌ Not all images uploaded successfully. Stopping.")
        return
    
    # Update ACF fields
    print("\n🔗 CONNECTING IMAGES TO ACF FIELDS")
    print("-"*70)
    
    results = {}
    for page_key, page_data in PAGES.items():
        success = update_page_acf(
            page_data['id'],
            page_key.capitalize(),
            media_ids[page_key]['why'],
            media_ids[page_key]['solutions']
        )
        results[page_key] = success
    
    # Verify pages
    print("\n✅ VERIFYING PAGES")
    print("-"*70)
    
    for page_key, page_data in PAGES.items():
        verify_page(page_data['url'], page_key.capitalize())
    
    # Final report
    print("\n" + "="*70)
    print("FINAL REPORT")
    print("="*70)
    
    all_success = all(results.values())
    
    for page_key, page_data in PAGES.items():
        status = "✅ COMPLETE" if results[page_key] else "❌ FAILED"
        print(f"\n{page_key.capitalize()} Physiotherapy:")
        print(f"  URL: {WP_URL}{page_data['url']}")
        print(f"  Status: {status}")
        print(f"  Why Choose Image ID: {media_ids[page_key].get('why', 'N/A')}")
        print(f"  Solutions Image ID: {media_ids[page_key].get('solutions', 'N/A')}")
    
    print("\n" + "="*70)
    if all_success:
        print("✅ ALL PAGES UPDATED SUCCESSFULLY")
    else:
        print("⚠️ SOME PAGES FAILED - CHECK ERRORS ABOVE")
    print("="*70)

if __name__ == '__main__':
    main()
