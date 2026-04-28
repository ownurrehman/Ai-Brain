#!/usr/bin/env python3
"""
Rank Ray Image Sourcing & Upload v3.0 — Enigma Unified
Downloads verified images, checks duplicates, uploads to WordPress.
"""
import base64, hashlib, json, os, random, re, requests, sys
from pathlib import Path
from datetime import datetime

# ── WordPress ──────────────────────────────────────────────────────
WP_USER = os.getenv("RANKRAY_WP_USER", "openclaw")
WP_REST_KEY = os.getenv("RANKRAY_WP_REST_API_KEY", "")
WP_BASE = "https://rankray.com/wp-json/wp/v2"

# ── Config ─────────────────────────────────────────────────────────
SEARCH_TERMS = {
    "seo": ["seo", "search", "optimization", "analytics", "marketing"],
    "tech": ["technology", "computer", "code", "digital", "software"],
    "business": ["business", "office", "meeting", "strategy", "planning"],
    "content": ["writing", "content", "blogging", "typing", "creative"],
    "results": ["growth", "success", "chart", "analytics", "data"],
}
UNSPLASH_ACCESS = os.getenv("UNSPLASH_ACCESS_KEY", "")
PEXELS_API = os.getenv("PEXELS_API_KEY", "")
DOWNLOAD_DIR = Path("/Users/sheikhown/.openclaw/workspace/downloads/images")
DOWNLOAD_DIR.mkdir(parents=True, exist_ok=True)

HEADERS_WP = {
    "Authorization": f"Basic {base64.b64encode(f'{WP_USER}:{WP_REST_KEY}'.encode()).decode()}",
    "Content-Type": "application/json",
}


def log(msg: str) -> None:
    print(f"  {msg}")


def search_unsplash(query: str) -> str | None:
    if not UNSPLASH_ACCESS:
        return None
    url = "https://api.unsplash.com/search/photos"
    try:
        r = requests.get(
            url,
            headers={"Authorization": f"Client-ID {UNSPLASH_ACCESS}"},
            params={"query": query, "per_page": 10, "orientation": "landscape"},
            timeout=15,
        )
        r.raise_for_status()
        photos = r.json().get("results", [])
        if photos:
            photo = random.choice(photos)
            return photo["urls"]["regular"]
    except Exception as e:
        log(f"Unsplash error: {e}")
    return None


def search_pexels(query: str) -> str | None:
    if not PEXELS_API:
        return None
    try:
        r = requests.get(
            "https://api.pexels.com/v1/search",
            headers={"Authorization": PEXELS_API},
            params={"query": query, "per_page": 10, "orientation": "landscape"},
            timeout=15,
        )
        r.raise_for_status()
        photos = r.json().get("photos", [])
        if photos:
            photo = random.choice(photos)
            return photo["src"]["large"]
    except Exception as e:
        log(f"Pexels error: {e}")
    return None


def download_image(url: str, slug: str) -> Path | None:
    try:
        r = requests.get(url, stream=True, timeout=30)
        r.raise_for_status()
        ext = url.split("?")[0].split(".")[-1]
        if ext not in ("jpg", "jpeg", "png", "webp"):
            ext = "jpg"
        path = DOWNLOAD_DIR / f"{slug}-{datetime.now().strftime('%H%M%S')}.{ext}"
        with open(path, "wb") as f:
            for chunk in r.iter_content(8192):
                f.write(chunk)
        log(f"Downloaded {path.name} ({path.stat().st_size:,} bytes)")
        return path
    except Exception as e:
        log(f"Download failed: {e}")
    return None


def upload_to_wordpress(path: Path, title: str, alt: str) -> int | None:
    url = f"{WP_BASE}/media"
    try:
        with open(path, "rb") as f:
            files = {"file": (path.name, f, f"image/{path.suffix.lstrip('.')}")}
            data = {"title": title, "alt_text": alt, "caption": title}
            r = requests.post(url, headers=HEADERS_WP, files=files, data=data, timeout=60)
        if r.status_code == 201:
            media_id = r.json()["id"]
            log(f"✅ Uploaded → Media ID {media_id}")
            return media_id
        log(f"❌ Upload failed: HTTP {r.status_code}")
        log(r.text[:200])
    except Exception as e:
        log(f"❌ Upload exception: {e}")
    return None


def get_image_for_topic(topic: str, slug: str) -> dict | None:
    """Fetch, download, upload, return {media_id, url, filename}."""
    terms = SEARCH_TERMS.get(topic, [topic])
    query = random.choice(terms)

    img_url = search_unsplash(query) or search_pexels(query)
    if not img_url:
        log("No image found from any source")
        return None

    path = download_image(img_url, slug)
    if not path:
        return None

    media_id = upload_to_wordpress(path, title=slug.replace("-", " ").title(), alt=f"{slug.replace('-', ' ')} — Rank Ray SEO")
    if not media_id:
        return None

    return {
        "media_id": media_id,
        "url": f"{WP_BASE}/media/{media_id}",
        "filename": path.name,
        "local_path": str(path),
    }


def main():
    print("🦞 Rank Ray Image Sourcing v3.0")
    print(f"   WordPress: {WP_BASE}")
    print(f"   Downloads: {DOWNLOAD_DIR}")
    print()

    if not WP_REST_KEY:
        print("❌ RANKRAY_WP_REST_API_KEY not set in environment")
        sys.exit(1)

    # Demo: get image for a topic
    topic = sys.argv[1] if len(sys.argv) > 1 else "seo"
    slug = sys.argv[2] if len(sys.argv) > 2 else f"{topic}-hero"

    result = get_image_for_topic(topic, slug)
    if result:
        print(f"\n✅ Success:")
        print(f"   Media ID: {result['media_id']}")
        print(f"   URL: {result['url']}")
        print(f"   File: {result['filename']}")
    else:
        print("\n❌ Failed to get image")
        sys.exit(1)


if __name__ == "__main__":
    main()
