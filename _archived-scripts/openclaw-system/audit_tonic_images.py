import requests
from requests.auth import HTTPBasicAuth
import json

# Credentials from memory
USER = "Dan"
PASS = "jS9z Oyu6 wLKH sy1A qeLM Ikwz"
BASE_URL = "https://tonicphysio.com/wp-json/wp/v2"

# Page IDs from memory/2026-04-20-tonic-final.md
PAGE_IDS = [
    11603, 6996, 6279, 6280, 1797, 6283, 1792, 6991, 7006, 6986, 6971, 1796, 1791,
    6976, 1793, 6981, 7001, 11895,
    6577, 6587, 6589, 6585, 6583, 6581, 6579, 8153
]

def audit_page(page_id):
    url = f"{BASE_URL}/pages/{page_id}"
    params = {"context": "edit"}
    try:
        response = requests.get(url, auth=HTTPBasicAuth(USER, PASS), params=params, timeout=10)
        if response.status_code != 200:
            return {"id": page_id, "status": "API_ERROR", "code": response.status_code}
        
        data = response.json()
        title = data.get("title", {}).get("rendered", f"ID {page_id}")
        featured_image = data.get("featured_media")
        acf = data.get("acf", {})
        
        # We are looking for image fields in ACF. 
        # Based on previous logs, there are usually 3 images: Featured, WhyChooseUs, Solutions.
        # I will check all fields that look like image IDs or contain 'image' in the key.
        missing = []
        if not featured_image:
            missing.append("Featured Image")
        
        # Scan ACF for anything that looks like a media ID or an image field
        # Since I don't have the exact ACF keys for this site, I'll list all ACF keys that are empty/null
        # but specifically alert if expected image patterns are missing.
        
        # Let's identify which fields are supposed to be images.
        # Common patterns in this project: 'image_why_choose_us', 'image_solutions', etc.
        # For now, I'll report the Featured Image and any ACF field that is null.
        
        acf_images = {k: v for k, v in acf.items() if "image" in k.lower()}
        for k, v in acf_images.items():
            if not v:
                missing.append(f"ACF Image Field: {k}")
        
        return {
            "id": page_id,
            "title": title,
            "featured_image": featured_image,
            "acf_images": acf_images,
            "missing": missing,
            "status": "OK" if not missing else "MISSING_IMAGES"
        }
    except Exception as e:
        return {"id": page_id, "status": "EXCEPTION", "error": str(e)}

results = []
for pid in PAGE_IDS:
    results.append(audit_page(pid))

print(json.dumps(results, indent=2))
