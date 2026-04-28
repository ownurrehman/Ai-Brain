#!/usr/bin/env python3
"""
Upload and update images for Tonic Physio service pages via WordPress REST API.
- Orthopedic Physiotherapy (ID: 1791)
- Pediatric Physiotherapy (ID: 1793)
"""

import requests
from requests.auth import HTTPBasicAuth
import json
import base64
import mimetypes

# WordPress credentials
USER = "Dan"
APP_PASSWORD = "jS9z Oyu6 wLKH sy1A qeLM Ikwz"
BASE_URL = "https://tonicphysio.com/wp-json/wp/v2"

# Image files
IMAGES = {
    "orthopedic": {
        "path": "/tmp/orthopedic-physiotherapy.jpg",
        "filename": "orthopedic-physiotherapy.jpg",
        "alt_text": "Orthopedic Physiotherapy Milton - Expert Joint Pain Treatment",
        "title": "Orthopedic Physiotherapy",
        "page_id": 1791,
        "page_url": "https://tonicphysio.com/orthopedic-physiotherapy/"
    },
    "pediatric": {
        "path": "/tmp/pediatric-physiotherapy.jpg",
        "filename": "pediatric-physiotherapy.jpg",
        "alt_text": "Pediatric Physiotherapy Milton - Children's Mobility & Development Care",
        "title": "Pediatric Physiotherapy",
        "page_id": 1793,
        "page_url": "https://tonicphysio.com/pediatric-physiotherapy/"
    }
}

def upload_media(image_data):
    """Upload image to WordPress Media Library via REST API"""
    url = f"{BASE_URL}/media"
    
    with open(image_data["path"], "rb") as f:
        file_content = f.read()
    
    # Get MIME type
    mime_type = mimetypes.guess_type(image_data["path"])[0] or "image/jpeg"
    
    headers = {
        "Content-Type": mime_type,
        "Content-Disposition": f'attachment; filename="{image_data["filename"]}"'
    }
    
    response = requests.post(
        url,
        auth=HTTPBasicAuth(USER, APP_PASSWORD),
        headers=headers,
        data=file_content,
        timeout=30
    )
    
    if response.status_code == 201:
        media_data = response.json()
        print(f"✓ Uploaded: {image_data['filename']} (ID: {media_data['id']})")
        return media_data["id"]
    else:
        print(f"✗ Upload failed: {response.status_code} - {response.text}")
        return None

def update_page_featured_image(page_id, media_id, title):
    """Set featured image for a page via REST API"""
    url = f"{BASE_URL}/pages/{page_id}"
    
    payload = {
        "featured_media": media_id
    }
    
    response = requests.post(
        url,
        auth=HTTPBasicAuth(USER, APP_PASSWORD),
        json=payload,
        timeout=15
    )
    
    if response.status_code == 200:
        print(f"✓ Featured image set for page ID {page_id}")
        return True
    else:
        print(f"✗ Failed to update page {page_id}: {response.status_code} - {response.text}")
        return False

def update_page_acf_images(page_id, media_id, title):
    """Update ACF image fields for the page"""
    url = f"{BASE_URL}/pages/{page_id}"
    
    # Update both solutions_image and why_choose_us_image with the new image
    payload = {
        "acf": {
            "solutions_image": media_id,
            "why_choose_us_image": media_id
        }
    }
    
    response = requests.post(
        url,
        auth=HTTPBasicAuth(USER, APP_PASSWORD),
        json=payload,
        timeout=15
    )
    
    if response.status_code == 200:
        print(f"✓ ACF images updated for page ID {page_id}")
        return True
    else:
        print(f"✗ Failed to update ACF for page {page_id}: {response.status_code}")
        return False

def get_page_info(page_id):
    """Get current page data"""
    url = f"{BASE_URL}/pages/{page_id}"
    response = requests.get(url, auth=HTTPBasicAuth(USER, APP_PASSWORD), timeout=10)
    if response.status_code == 200:
        return response.json()
    return None

def main():
    print("=== Tonic Physio Image Update ===\n")
    
    results = []
    
    for key, img_data in IMAGES.items():
        print(f"\n--- {img_data['title']} ---")
        print(f"Page ID: {img_data['page_id']}")
        print(f"URL: {img_data['page_url']}")
        
        # Check current page state
        page_info = get_page_info(img_data["page_id"])
        if page_info:
            current_featured = page_info.get("featured_media", 0)
            print(f"Current featured media ID: {current_featured}")
        
        # Upload image
        media_id = upload_media(img_data)
        if not media_id:
            results.append({
                "service": img_data["title"],
                "status": "FAILED",
                "error": "Upload failed"
            })
            continue
        
        # Update featured image
        featured_success = update_page_featured_image(img_data["page_id"], media_id, img_data["title"])
        
        # Update ACF fields
        acf_success = update_page_acf_images(img_data["page_id"], media_id, img_data["title"])
        
        # Verify update
        page_info = get_page_info(img_data["page_id"])
        if page_info:
            new_featured = page_info.get("featured_media", 0)
            acf = page_info.get("acf", {})
            print(f"New featured media ID: {new_featured}")
            print(f"ACF solutions_image: {acf.get('solutions_image', 'N/A')}")
            print(f"ACF why_choose_us_image: {acf.get('why_choose_us_image', 'N/A')}")
        
        results.append({
            "service": img_data["title"],
            "status": "SUCCESS" if (featured_success and acf_success) else "PARTIAL",
            "media_id": media_id,
            "page_id": img_data["page_id"],
            "url": img_data["page_url"]
        })
    
    print("\n=== Summary ===")
    for result in results:
        status_icon = "✓" if result["status"] == "SUCCESS" else "⚠" if result["status"] == "PARTIAL" else "✗"
        print(f"{status_icon} {result['service']}: {result['status']}")
        if result.get("media_id"):
            print(f"  Media ID: {result['media_id']}")
            print(f"  Page URL: {result['url']}")
    
    return results

if __name__ == "__main__":
    main()
