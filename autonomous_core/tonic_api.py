"""
TonicPhysio Autonomous API Client
Robust, failure-resistant WordPress REST API handler.
"""
import requests, json, sys
from urllib.parse import urljoin

creds = {
    "url": "https://tonicphysio.com",
    "user": "Dan",
    "password": "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"
}

def get_auth():
    from requests.auth import HTTPBasicAuth
    return HTTPBasicAuth(creds["user"], creds["password"])

def get_post(post_id):
    endpoint = urljoin(creds["url"] + "/", f"wp-json/wp/v2/posts/{post_id}")
    r = requests.get(endpoint, auth=get_auth(), timeout=30)
    return r.json() if r.status_code == 200 else {"error": r.text, "status": r.status_code}

def update_post(post_id, data):
    endpoint = urljoin(creds["url"] + "/", f"wp-json/wp/v2/posts/{post_id}")
    headers = {"Content-Type": "application/json"}
    r = requests.post(endpoint, auth=get_auth(), headers=headers, json=data, timeout=30)
    return r.json() if r.status_code in [200, 201] else {"error": r.text, "status": r.status_code}

def update_yoast(post_id, focuskw, title, metadesc):
    endpoint = urljoin(creds["url"] + "/", f"wp-json/wp/v2/posts/{post_id}")
    headers = {"Content-Type": "application/json"}
    meta = {
        "meta": {
            "_yoast_wpseo_focuskw": focuskw,
            "_yoast_wpseo_title": title,
            "_yoast_wpseo_metadesc": metadesc
        }
    }
    r = requests.post(endpoint, auth=get_auth(), headers=headers, json=meta, timeout=30)
    return r.json() if r.status_code in [200, 201] else {"error": r.text, "status": r.status_code}

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python3 tonic_api.py <get|update|yoast> <post_id> [json_data]")
        sys.exit(1)
    
    cmd = sys.argv[1]
    post_id = sys.argv[2]
    
    if cmd == "get":
        print(json.dumps(get_post(post_id), indent=2))
    elif cmd == "update":
        data = json.loads(sys.argv[3])
        print(json.dumps(update_post(post_id, data), indent=2))
    elif cmd == "yoast":
        focuskw, title, metadesc = sys.argv[3], sys.argv[4], sys.argv[5]
        print(json.dumps(update_yoast(post_id, focuskw, title, metadesc), indent=2))
