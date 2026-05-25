#!/usr/bin/env python3
import json, base64
from urllib.request import Request, urlopen

WP_URL = "https://tonicphysio.com/wp-json/wp/v2"
WP_USER = "Dan"
WP_APP_PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"
HEADERS = {
    "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36",
    "Accept": "application/json",
    "Referer": "https://tonicphysio.com/",
}

POSTS = [
    (13030, "2026-05-20T09:00:00"),
    (13032, "2026-05-21T10:00:00"),
    (13033, "2026-05-22T11:00:00"),
    (13034, "2026-05-23T12:00:00"),
    (13039, "2026-05-24T08:00:00"),
    (13040, "2026-05-24T14:00:00"),
]

for pid, date in POSTS:
    print(f"Setting post {pid} date to {date}...")
    try:
        url = f"{WP_URL}/posts/{pid}"
        data = json.dumps({"date": date}).encode()
        req = Request(url, data=data, method="POST")
        creds = base64.b64encode(f"{WP_USER}:{WP_APP_PASS}".encode()).decode()
        req.add_header("Authorization", f"Basic {creds}")
        req.add_header("Content-Type", "application/json")
        for k, v in HEADERS.items():
            req.add_header(k, v)
        with urlopen(req, timeout=30) as resp:
            d = json.loads(resp.read().decode())
            print(f"  Post {d['id']}: date={d['date']} status={d['status']}")
    except Exception as e:
        print(f"  Error: {e}")
