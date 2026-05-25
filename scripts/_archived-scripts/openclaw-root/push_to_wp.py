#!/usr/bin/env python3
"""Push Batch 1 location page rewrites to Rank Ray WordPress via REST API + ACF."""

import json
import urllib.request
import base64
import re
import socket
import time

IP = "104.21.41.115"
BASE = "https://rankray.com"
USER = "openclaw"
APP_PASS = "6Zz9 5gJL 8uyA QH4g RQDH GV1j"

# Patch DNS resolution
original_getaddrinfo = socket.getaddrinfo
def patched_getaddrinfo(host, port, *args, **kwargs):
    if host == 'rankray.com':
        return [(socket.AF_INET, socket.SOCK_STREAM, 6, '', (IP, port))]
    return original_getaddrinfo(host, port, *args, **kwargs)
socket.getaddrinfo = patched_getaddrinfo


def api_request(method, path, data=None):
    url = BASE + path
    credentials = base64.b64encode(f"{USER}:{APP_PASS}".encode()).decode()
    headers = {
        "Authorization": f"Basic {credentials}",
        "Content-Type": "application/json",
        "Accept": "application/json",
        "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36",
    }
    body = json.dumps(data).encode() if data else None
    req = urllib.request.Request(url, data=body, headers=headers, method=method)
    try:
        with urllib.request.urlopen(req, timeout=60) as resp:
            return resp.status, json.loads(resp.read().decode())
    except urllib.error.HTTPError as e:
        return e.code, e.read().decode()
    except Exception as e:
        return 0, str(e)


def parse_rewrite_file(filepath):
    """Parse rewrite markdown file into dict of acf_field_name -> value."""
    fields = {}
    with open(filepath, 'r') as f:
        content = f.read()
    pattern = r'### (acf_\w+)\n(.*?)(?=\n### |$)'
    matches = re.findall(pattern, content, re.DOTALL)
    for field_name, value in matches:
        fields[field_name] = value.strip()
    return fields


pages = [
    {"id": 19253, "slug": "seo-agency-new-york", "file": "/Users/sheikhown/.openclaw/workspace/batch1/seo-agency-new-york.md"},
    {"id": 19254, "slug": "seo-agency-los-angeles", "file": "/Users/sheikhown/.openclaw/workspace/batch1/seo-agency-los-angeles.md"},
    {"id": 18020, "slug": "seo-agency-dubai", "file": "/Users/sheikhown/.openclaw/workspace/batch1/seo-agency-dubai.md"},
]

results = []

for page in pages:
    print(f"\n{'='*60}")
    print(f"Pushing: {page['slug']} (ID: {page['id']})")

    fields = parse_rewrite_file(page['file'])
    field_count = len(fields)
    print(f"  Parsed {field_count} ACF fields")

    if not fields:
        print("  ERROR: No fields parsed!")
        results.append({"slug": page['slug'], "status": "error", "detail": "no fields"})
        continue

    payload = {"acf": fields}
    status, response = api_request("POST", f"/wp-json/wp/v2/location-page/{page['id']}", payload)

    if status in [200, 201]:
        print(f"  SUCCESS: {field_count} ACF fields updated")
        results.append({"slug": page['slug'], "status": "success", "fields": field_count})
    else:
        resp_str = str(response)[:500]
        print(f"  FAILED: HTTP {status}")
        print(f"  Response: {resp_str}")
        results.append({"slug": page['slug'], "status": "failed", "code": status, "detail": resp_str})

    time.sleep(2)

print(f"\n\n{'='*60}")
print("FINAL RESULTS:")
for r in results:
    icon = "OK" if r['status'] == 'success' else "FAIL"
    detail = r.get('detail', str(r.get('fields', '')) + ' fields')
    print(f"  [{icon}] {r['slug']}: {detail}")