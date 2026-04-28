import requests
from requests.auth import HTTPBasicAuth
import json

USER = "Dan"
PASS = "jS9z Oyu6 wLKH sy1A qeLM Ikwz"
BASE_URL = "https://tonicphysio.com/wp-json/wp/v2"

IMAGE_IDS = [11462, 11460, 11836, 11837]

def get_image_info(img_id):
    url = f"{BASE_URL}/media/{img_id}"
    try:
        response = requests.get(url, auth=HTTPBasicAuth(USER, PASS), timeout=10)
        if response.status_code == 200:
            data = response.json()
            return {
                "id": img_id,
                "title": data.get("title", {}).get("rendered", "No Title"),
                "alt": data.get("alt_text", "No Alt"),
                "url": data.get("source_url", "No URL")
            }
        return {"id": img_id, "error": response.status_code}
    except Exception as e:
        return {"id": img_id, "error": str(e)}

results = [get_image_info(iid) for iid in IMAGE_IDS]
print(json.dumps(results, indent=2))
