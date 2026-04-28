#!/usr/bin/env python3
"""Update ACF image fields for Pediatric Physiotherapy page"""

import requests
import base64
import os
import json

WP_URL = "https://tonicphysio.com"
WP_USER = "Dan"
WP_APP_PASSWORD = "4vFk 18fN UlLB twaw B2hU 0kRE"
PAGE_ID = 1793  # Pediatric Physiotherapy page

# Existing images in media library
IMAGES = {
    "why_choose_us_image": 5419,  # paediatric-physiotherapy-points
    "solutions_image": 5456,  # paediatric-physiotherapy-3
}

def get_auth():
    credentials = f"{WP_USER}:{WP_APP_PASSWORD}"
    token = base64.b64encode(credentials.encode()).decode()
    return {"Authorization": f"Basic {token}"}

def update_acf_fields(page_id, acf_data):
    """Update ACF fields for a page"""
    url = f"{WP_URL}/wp-json/wp/v2/pages/{page_id}"
    
    # Get current page data first
    response = requests.get(url, headers=get_auth())
    if response.status_code != 200:
        print(f"Error fetching page: {response.text}")
        return False
    
    current_data = response.json()
    
    # Update ACF fields
    update_data = {"acf": {}}
    for field_name, media_id in IMAGES.items():
        if field_name in current_data.get("acf", {}):
            update_data["acf"][field_name] = media_id
            print(f"Setting {field_name} to media ID {media_id}")
    
    if not update_data["acf"]:
        print("No ACF fields to update")
        return True
    
    # Make the update request
    response = requests.post(url, headers=get_auth(), json=update_data)
    
    if response.status_code == 200:
        print("✅ ACF image fields updated successfully")
        return True
    else:
        print(f"❌ Error updating ACF fields: {response.status_code}")
        print(response.text)
        return False

def verify_update(page_id):
    """Verify the update was successful"""
    url = f"{WP_URL}/wp-json/wp/v2/pages/{page_id}"
    response = requests.get(url, headers=get_auth())
    
    if response.status_code == 200:
        data = response.json()
        acf = data.get("acf", {})
        print("\n📋 Current ACF image fields:")
        print(f"  why_choose_us_image: {acf.get('why_choose_us_image', 'empty')}")
        print(f"  solutions_image: {acf.get('solutions_image', 'empty')}")
        return True
    return False

if __name__ == "__main__":
    print("🔧 Updating Pediatric Physiotherapy ACF image fields...")
    print(f"Page ID: {PAGE_ID}")
    print(f"Images to connect: {IMAGES}")
    print()
    
    success = update_acf_fields(PAGE_ID, IMAGES)
    
    if success:
        verify_update(PAGE_ID)
        print(f"\n✅ Done! Page URL: {WP_URL}/physiotherapy-in-milton/pediatric-physiotherapy/")
    else:
        print("\n❌ Update failed")
