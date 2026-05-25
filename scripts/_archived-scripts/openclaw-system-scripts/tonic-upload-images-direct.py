#!/usr/bin/env python3
"""Upload images to TonicPhysio WordPress and connect to ACF fields"""

import requests
import base64
import os

WP_URL = "https://tonicphysio.com"
WP_USER = "openclaw"
WP_APP_PASSWORD = "jS9z Oyu6 wLKH sy1A qeLM Ikwz"
WORKSPACE = "/Users/sheikhown/.openclaw/workspace"

def get_auth():
    credentials = f"{WP_USER}:{WP_APP_PASSWORD}"
    token = base64.b64encode(credentials.encode()).decode()
    return {"Authorization": f"Basic {token}"}

def upload_image(filepath, title, alt_text):
    """Upload image to WordPress media library"""
    url = f"{WP_URL}/wp-json/wp/v2/media"
    
    with open(filepath, 'rb') as f:
        file_data = f.read()
    
    filename = os.path.basename(filepath)
    
    response = requests.post(
        url,
        headers=get_auth(),
        files={'file': (filename, file_data, 'image/jpeg')},
        data={
            'title': title,
            'alt_text': alt_text,
            'caption': title
        }
    )
    
    if response.status_code == 201:
        data = response.json()
        print(f"✅ Uploaded: {filename} → Media ID: {data['id']}")
        return data['id']
    else:
        print(f"❌ Failed to upload {filename}: {response.status_code}")
        print(response.text)
        return None

def update_acf_fields(page_id, page_name, why_image_id, solutions_image_id):
    """Update ACF image fields on a page"""
    url = f"{WP_URL}/wp-json/wp/v2/pages/{page_id}"
    
    # First get current page to check ACF structure
    response = requests.get(url, headers=get_auth())
    if response.status_code != 200:
        print(f"❌ Could not fetch page {page_id}: {response.status_code}")
        return False
    
    update_data = {
        "acf": {
            "why_choose_us_image": why_image_id,
            "solutions_image": solutions_image_id
        }
    }
    
    response = requests.post(url, headers=get_auth(), json=update_data)
    
    if response.status_code == 200:
        print(f"✅ Updated ACF fields for {page_name} (ID: {page_id})")
        return True
    else:
        print(f"❌ Failed to update {page_name}: {response.status_code}")
        print(response.text)
        return False

def main():
    print("🔧 Uploading images to TonicPhysio.com...\n")
    
    # Upload 4 images
    images = [
        {
            'path': f"{WORKSPACE}/pediatric-physiotherapy-why-choose.jpg",
            'title': 'Pediatric Physiotherapy Milton - Why Choose Us',
            'alt': 'Pediatric Physiotherapy Milton - Child Development Treatment',
            'page': 'pediatric',
            'field': 'why'
        },
        {
            'path': f"{WORKSPACE}/pediatric-physiotherapy-solutions.jpg",
            'title': 'Pediatric Physiotherapy Milton - Solutions',
            'alt': 'Pediatric Physiotherapy Milton - Treatment Solutions for Children',
            'page': 'pediatric',
            'field': 'solutions'
        },
        {
            'path': f"{WORKSPACE}/orthopedic-physiotherapy-why-choose.jpg",
            'title': 'Orthopedic Physiotherapy Milton - Why Choose Us',
            'alt': 'Orthopedic Physiotherapy Milton - Joint and Bone Treatment',
            'page': 'orthopedic',
            'field': 'why'
        },
        {
            'path': f"{WORKSPACE}/orthopedic-physiotherapy-solutions.jpg",
            'title': 'Orthopedic Physiotherapy Milton - Solutions',
            'alt': 'Orthopedic Physiotherapy Milton - Treatment Solutions for Joint Pain',
            'page': 'orthopedic',
            'field': 'solutions'
        }
    ]
    
    media_ids = {'pediatric': {}, 'orthopedic': {}}
    
    for img in images:
        media_id = upload_image(img['path'], img['title'], img['alt'])
        if media_id:
            media_ids[img['page']][img['field']] = media_id
    
    print("\n📋 Media IDs:")
    print(f"  Pediatric Why Choose: {media_ids['pediatric'].get('why', 'FAILED')}")
    print(f"  Pediatric Solutions: {media_ids['pediatric'].get('solutions', 'FAILED')}")
    print(f"  Orthopedic Why Choose: {media_ids['orthopedic'].get('why', 'FAILED')}")
    print(f"  Orthopedic Solutions: {media_ids['orthopedic'].get('solutions', 'FAILED')}")
    
    # Update ACF fields
    print("\n🔗 Connecting images to ACF fields...")
    
    pediatric_page_id = 1793
    orthopedic_page_id = None
    
    # Find orthopedic page ID
    print("\n🔍 Finding Orthopedic Physiotherapy page ID...")
    search_url = f"{WP_URL}/wp-json/wp/v2/pages?slug=orthopedic-physiotherapy"
    response = requests.get(search_url, headers=get_auth())
    if response.status_code == 200:
        pages = response.json()
        if pages:
            orthopedic_page_id = pages[0]['id']
            print(f"✅ Found Orthopedic page ID: {orthopedic_page_id}")
        else:
            print("❌ Could not find Orthopedic page")
            return
    else:
        print(f"❌ Search failed: {response.status_code}")
        return
    
    # Update both pages
    pediatric_success = update_acf_fields(
        pediatric_page_id,
        "Pediatric Physiotherapy",
        media_ids['pediatric'].get('why'),
        media_ids['pediatric'].get('solutions')
    )
    
    orthopedic_success = update_acf_fields(
        orthopedic_page_id,
        "Orthopedic Physiotherapy",
        media_ids['orthopedic'].get('why'),
        media_ids['orthopedic'].get('solutions')
    )
    
    print("\n" + "="*60)
    print("FINAL RESULTS:")
    print("="*60)
    print(f"\n1. Pediatric Physiotherapy:")
    print(f"   URL: https://tonicphysio.com/physiotherapy-in-milton/pediatric-physiotherapy/")
    print(f"   Status: {'✅ COMPLETE' if pediatric_success else '❌ FAILED'}")
    
    print(f"\n2. Orthopedic Physiotherapy:")
    print(f"   URL: https://tonicphysio.com/physiotherapy-in-milton/orthopedic-physiotherapy/")
    print(f"   Status: {'✅ COMPLETE' if orthopedic_success else '❌ FAILED'}")
    
    if pediatric_success and orthopedic_success:
        print("\n✅ All images uploaded and connected successfully!")
    else:
        print("\n⚠️ Some updates failed - check errors above")

if __name__ == '__main__':
    main()
