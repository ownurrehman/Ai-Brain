#!/usr/bin/env python3
"""
Update Yoast SEO fields on 5 Tonic Physio WordPress pages using REST API.
"""

import requests
import base64
import json

# WordPress REST API credentials
WP_BASE_URL = "https://tonicphysio.com"
WP_REST_USER = "rankrayagency@gmail.com"
WP_REST_PASS = "RR#Tonic@2026"

# Create credentials string for Basic Auth
credentials = f"{WP_REST_USER}:{WP_REST_PASS}"
token = base64.b64encode(credentials.encode()).decode()

headers = {
    "Authorization": f"Basic {token}",
    "Content-Type": "application/json"
}

# Page data
PAGES = [
    {
        "id": 11603,
        "name": "B-Pulse Pelvic Floor",
        "seo_title": "B-Pulse Pelvic Floor Strengthening Milton | Tonic Physio",
        "meta_description": "B-Pulse pelvic floor strengthening in Milton at Tonic Physio. Expert treatment for postpartum recovery, incontinence & pelvic pain. Book consultation."
    },
    {
        "id": 6971,
        "name": "Joint Pain",
        "seo_title": "Joint Pain Treatment Milton | Tonic Physio",
        "meta_description": "Relieve joint pain and stiffness in Milton at Tonic Physio. Expert physiotherapy for arthritis, injury & chronic pain. Book your appointment."
    },
    {
        "id": 1791,
        "name": "Orthopedic Physiotherapy",
        "seo_title": "Orthopedic Physiotherapy Milton | Tonic Physio",
        "meta_description": "Expert orthopedic physiotherapy in Milton at Tonic Physio. Joint & muscle rehab, post-surgery recovery & pain relief. Book assessment today."
    },
    {
        "id": 1793,
        "name": "Pediatric Physiotherapy",
        "seo_title": "Pediatric Physiotherapy Milton | Tonic Physio",
        "meta_description": "Pediatric physiotherapy in Milton at Tonic Physio. Expert care for children with developmental delays, injuries & mobility issues. Book now."
    },
    {
        "id": 6587,
        "name": "Hot Stone Massage",
        "seo_title": "Hot Stone Massage Milton | Tonic Physio",
        "meta_description": "Hot stone massage in Milton at Tonic Physio. Therapeutic heat therapy for muscle tension, stress relief & relaxation. Book your session."
    }
]

def update_page(page_data):
    """Update Yoast SEO fields for a single page via REST API."""
    print(f"\n=== Updating Page {page_data['id']}: {page_data['name']} ===")
    
    url = f"{WP_BASE_URL}/wp-json/wp/v2/pages/{page_data['id']}"
    
    # Yoast SEO fields in WordPress REST API
    payload = {
        "yoast_head": page_data['seo_title'],
        "yoast_head_json": {
            "title": page_data['seo_title'],
            "description": page_data['meta_description']
        },
        "yoast_seo_focuskw": page_data['seo_title'].split('|')[0].strip(),
        "_yoast_wpseo_focuskw": page_data['seo_title'].split('|')[0].strip(),
        "_yoast_wpseo_metadesc": page_data['meta_description'],
        "_yoast_wpseo_title": page_data['seo_title']
    }
    
    print(f"SEO Title: {page_data['seo_title']} ({len(page_data['seo_title'])} chars)")
    print(f"Meta Description: {page_data['meta_description']} ({len(page_data['meta_description'])} chars)")
    
    try:
        response = requests.post(url, headers=headers, json=payload)
        
        if response.status_code in [200, 201]:
            print(f"✓ Page {page_data['id']} updated successfully")
            return {
                "id": page_data['id'],
                "name": page_data['name'],
                "success": True,
                "status_code": response.status_code,
                "title_length": len(page_data['seo_title']),
                "desc_length": len(page_data['meta_description'])
            }
        else:
            print(f"⚠ Page {page_data['id']} update failed: {response.status_code}")
            print(f"Response: {response.text[:200]}")
            return {
                "id": page_data['id'],
                "name": page_data['name'],
                "success": False,
                "status_code": response.status_code,
                "error": response.text[:200]
            }
    except Exception as e:
        print(f"⚠ Error updating page {page_data['id']}: {e}")
        return {
            "id": page_data['id'],
            "name": page_data['name'],
            "success": False,
            "error": str(e)
        }

def verify_page(page_data):
    """Verify Yoast SEO fields on a page."""
    print(f"\n=== Verifying Page {page_data['id']}: {page_data['name']} ===")
    
    url = f"{WP_BASE_URL}/wp-json/wp/v2/pages/{page_data['id']}"
    
    try:
        response = requests.get(url, headers=headers)
        
        if response.status_code == 200:
            data = response.json()
            
            # Check Yoast fields
            yoast_title = data.get('_yoast_wpseo_title', '')
            yoast_desc = data.get('_yoast_wpseo_metadesc', '')
            
            title_match = page_data['seo_title'] == yoast_title
            desc_match = page_data['meta_description'] == yoast_desc
            
            print(f"Yoast Title: {yoast_title[:50]}... ({len(yoast_title)} chars)")
            print(f"Yoast Description: {yoast_desc[:50]}... ({len(yoast_desc)} chars)")
            print(f"Title match: {title_match}")
            print(f"Description match: {desc_match}")
            
            return {
                "id": page_data['id'],
                "name": page_data['name'],
                "verified": title_match and desc_match,
                "title_length": len(yoast_title),
                "desc_length": len(yoast_desc),
                "tonic_in_desc": "Tonic" in yoast_desc
            }
        else:
            print(f"⚠ Failed to fetch page {page_data['id']}: {response.status_code}")
            return {
                "id": page_data['id'],
                "name": page_data['name'],
                "verified": False,
                "error": f"Status {response.status_code}"
            }
    except Exception as e:
        print(f"⚠ Error verifying page {page_data['id']}: {e}")
        return {
            "id": page_data['id'],
            "name": page_data['name'],
            "verified": False,
            "error": str(e)
        }

def main():
    print("Starting Yoast SEO update via REST API...")
    print(f"Base URL: {WP_BASE_URL}")
    print(f"User: {WP_REST_USER}")
    
    # Test connection first
    print("\n=== Testing Connection ===")
    test_url = f"{WP_BASE_URL}/wp-json/wp/v2/pages/{PAGES[0]['id']}"
    test_response = requests.get(test_url, headers=headers)
    
    if test_response.status_code == 200:
        print("✓ Connection successful")
    else:
        print(f"⚠ Connection failed: {test_response.status_code}")
        print(f"Response: {test_response.text[:200]}")
        return
    
    # Update all pages
    print("\n=== Updating Pages ===")
    update_results = []
    for page_data in PAGES:
        result = update_page(page_data)
        update_results.append(result)
    
    # Verify all pages
    print("\n=== Verifying Updates ===")
    verify_results = []
    for page_data in PAGES:
        result = verify_page(page_data)
        verify_results.append(result)
    
    # Summary
    print("\n" + "="*60)
    print("FINAL SUMMARY")
    print("="*60)
    
    for v in verify_results:
        status = "✓" if v['verified'] else "⚠"
        print(f"{status} Page {v['id']} ({v['name']})")
        if 'title_length' in v:
            print(f"   Title: {v['title_length']} chars")
            print(f"   Description: {v['desc_length']} chars")
            print(f"   'Tonic' in description: {v['tonic_in_desc']}")
        if 'error' in v:
            print(f"   Error: {v['error']}")

if __name__ == "__main__":
    main()
