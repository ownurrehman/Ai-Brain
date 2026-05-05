import requests
import os

with open('content/canada-agentic-seo-guide.md', 'r', encoding='utf-8') as f:
    content = f.read()

lines = content.split('\n')
meta = {}
body_start = 0
if lines[0].startswith('---'):
    body_start = 0
    for i in range(1, len(lines)):
        if lines[i] == '---':
            body_start = i + 1
            break
        if ':' in lines[i]:
            key, val = lines[i].split(':', 1)
            meta[key.strip()] = val.strip()

body = '\n'.join(lines[body_start:])

USER = os.environ.get('RANKRAY_WP_USER')
# Use the REST API KEY which looks like a real application password
APP_PASS = os.environ.get('RANKRAY_WP_REST_API_KEY') 
URL = "https://rankray.com/wp-json/wp/v2/posts"

payload = {
    "title": meta.get('title', 'The Future of Growth: Agentic SEO'),
    "content": body,
    "status": "draft"
}

try:
    response = requests.post(URL, auth=(USER, APP_PASS), json=payload)
    response.raise_for_status()
    print(f"SUCCESS: Post created with ID {response.json().get('id')}")
except Exception as e:
    print(f"ERROR: {e}")
    if 'response' in locals():
        print(f"RESPONSE: {response.text}")
