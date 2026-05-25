#!/usr/bin/env python3
"""
media-dedup-checker.py
Ai Brain P0 Agent Sub-Function

Query WordPress media library to detect if a proposed image or similar
image already exists. Prevents agents from reusing old media.

Usage as CLI:
    python3 media-dedup-checker.py --site rankray --query "AI automation"
    python3 media-dedup-checker.py --site rankray --filename "ai-automation.webp"
    python3 media-dedup-checker.py --site rankray --alt "AI Automation"

Usage as module:
    from media_dedup_checker import check_media
    result = check_media(site="rankray", query="AI automation")

Returns:
{
  "clear": false,
  "conflicts": [
    {"id": 15906, "alt": "", "source_url": "...", "filename": "..."}
  ],
  "message": "CONFLICT: 3 matching media items found. Choose a NEW image."
}

Author: AI Brain / Agent Ops
"""
import sys, json, argparse, base64
from urllib import request

# ---------------------------------------------------------------------------
# Site credentials map (agents can also inject these via env/env file)
# ---------------------------------------------------------------------------
SITES = {
    "rankray": {
        "url": "https://rankray.com/wp-json/wp/v2/",
        "user": "openclaw",
        "pass": "6Zz9 5gJL 8uyA QH4g RQDH GV1j",
    },
}

def _basic_auth(user, pw):
    return base64.b64encode(f"{user}:{pw}".encode()).decode()


def _fetch_media(base_url, slug, query=None, fields="id,source_url,alt_text,title.slug,date,media_details.file"):
    """Fetch media items matching query or all recent items."""
    base_url = base_url.rstrip('/')
    url = f"{base_url}/media?slug={slug}&per_page=50"
    if query:
        # WP search param
        url += f"&search={request.quote(query.encode('utf-8'))}"
    url += f"&fields={fields}"
    req = request.Request(url)
    req.add_header("Authorization", f"Basic {_basic_auth(SITES[slug]['user'], SITES[slug]['pass'])}")

    try:
        with request.urlopen(req, timeout=30) as r:
            data = json.loads(r.read().decode())
        return data
    except Exception as e:
        return {"_error": str(e)}


def check_media(site, query="", filename="", alt_check=""):
    cfg = SITES.get(site)
    if not cfg:
        return {"error": f"Unknown site: {site}. Configured: {list(SITES.keys())}"}

    data = _fetch_media(cfg["url"], site, query=query or None)
    if isinstance(data, dict) and "_error" in data:
        return {"error": data["_error"]}

    conflicts = []
    search_terms = []
    if query:
        search_terms.extend(query.lower().split())
    if filename:
        base_fn = filename.lower().replace('.jpg', '').replace('.jpeg', '').replace('.png', '').replace('.webp', '').replace('.gif', '')
        search_terms.append(base_fn)
    if alt_check:
        search_terms.extend(alt_check.lower().split())

    for item in data:
        item_id = item.get("id", 0)
        item_url = item.get("source_url", "")
        item_alt = item.get("alt_text", "")
        title_obj = item.get("title", {})
        item_slug = title_obj.get("raw", "") if isinstance(title_obj, dict) else str(title_obj)
        media_details = item.get("media_details", {})
        file_name = media_details.get("file", "")

        # Score matching
        score = 0
        for term in search_terms:
            if term in item_url.lower():
                score += 3
            if term in item_alt.lower():
                score += 3
            if term in item_slug.lower():
                score += 2
            if term in file_name.lower():
                score += 3

        if score >= 2:
            conflicts.append({
                "id": item_id,
                "score": score,
                "source_url": item_url,
                "alt_text": item_alt,
                "slug": item_slug,
                "filename": file_name,
            })

    conflicts.sort(key=lambda x: x["score"], reverse=True)

    return {
        "clear": len(conflicts) == 0,
        "conflicts_count": len(conflicts),
        "conflicts": conflicts,
        "search_terms": search_terms,
        "message": (
            "CLEAR: No matching media found. Safe to upload NEW image."
            if not conflicts
            else f"CONFLICT: {len(conflicts)} existing media item(s) match. Choose a NEW image from Pexels/Unsplash/Pixabay."
        )
    }


def add_site(site_key, api_url, username, app_password):
    """Add a new site to the registry dynamically."""
    SITES[site_key] = {"url": api_url, "user": username, "pass": app_password}


# ── CLI ─────────────────────────────────────────────────────────────────
if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Check WP media library for conflicts")
    parser.add_argument("--site", required=True, help="Site key (e.g. rankray)")
    parser.add_argument("--query", default="", help="Search query term")
    parser.add_argument("--filename", default="", help="Proposed filename")
    parser.add_argument("--alt", default="", help="Proposed alt text")
    args = parser.parse_args()

    result = check_media(args.site, query=args.query, filename=args.filename, alt_check=args.alt)
    print(json.dumps(result, indent=2))
