import requests
from requests.auth import HTTPBasicAuth
import json

USER = "Dan"
PASS = "jS9z Oyu6 wLKH sy1A qeLM Ikwz"
BASE_URL = "https://tonicphysio.com/wp-json/wp/v2"

# Map based on reports/tonicphysio-image-mapping.md
FIXES = [
    {
        "id": 1791, 
        "title": "Orthopedic Physiotherapy",
        "why_choose_us_image": 11462, 
        "solutions_image": 11460
    },
    {
        "id": 1793, 
        "title": "Pediatric Physiotherapy",
        "why_choose_us_image": 11836, 
        "solutions_image": 11837
    }
]

def fix_page(page_data):
    url = f"{BASE_URL}/pages/{page_data['id']}"
    
    # We update only the ACF fields to avoid overwriting other content
    payload = {
        "acf": {
            "why_choose_us_image": page_data["why_choose_us_image"],
            "solutions_image": page_data["solutions_image"]
        }
    }
    
    try:
        response = requests.post(
            url, 
            auth=HTTPBasicAuth(USER, PASS), 
            json=payload, 
            timeout=10
        )
        if response.status_code == 200:
            return {"title": page_data["title"], "status": "SUCCESS"}
        else:
            return {"title": page_data["title"], "status": "FAILED", "code": response.status_code, "body": response.text}
    except Exception as e:
        return {"title": page_data["title"], "status": "EXCEPTION", "error": str(e)}

results = []
for item in FIXES:
    results.append(fix_page(item))

print(json.dumps(results, indent=2))
