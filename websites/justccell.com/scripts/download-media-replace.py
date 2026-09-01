#!/usr/bin/env python3
"""Download justccell.com media from inventory CSV with SEO filenames."""

from __future__ import annotations

import csv
import json
import sys
from collections import Counter
from pathlib import Path
from urllib.error import URLError
from urllib.parse import quote, urlsplit, urlunsplit
from urllib.request import Request, urlopen

ROOT = Path(__file__).resolve().parents[1]
CSV_PATH = ROOT / "csvs" / "justccell-media-inventory.csv"
OUT_DIR = ROOT / "media-replace-ready"
IN_USE_DIR = OUT_DIR / "in-use"
UNUSED_DIR = OUT_DIR / "unused"
MANIFEST_PATH = OUT_DIR / "manifest.json"
TIMEOUT = 60


def encode_url(url: str) -> str:
    parts = urlsplit(url)
    path = quote(parts.path, safe="/%")
    return urlunsplit((parts.scheme, parts.netloc, path, parts.query, parts.fragment))


def unique_name(base: str, used: Counter[str]) -> str:
    used[base] += 1
    if used[base] == 1:
        return base
    stem = Path(base).stem
    ext = Path(base).suffix
    return f"{stem}-{used[base]:02d}{ext}"


def download(url: str, dest: Path) -> None:
    dest.parent.mkdir(parents=True, exist_ok=True)
    req = Request(encode_url(url), headers={"User-Agent": "JustccellMediaReplace/1.0"})
    with urlopen(req, timeout=TIMEOUT) as resp:
        data = resp.read()
    dest.write_bytes(data)


def main() -> int:
    only_in_use = "--in-use-only" in sys.argv

    if not CSV_PATH.is_file():
        print(f"Missing CSV: {CSV_PATH}", file=sys.stderr)
        return 1

    rows: list[dict[str, str]] = []
    with CSV_PATH.open(newline="", encoding="utf-8") as fh:
        rows = list(csv.DictReader(fh))

    if only_in_use:
        rows = [r for r in rows if (r.get("in_use") or "").strip().lower() == "yes"]

    used_names: Counter[str] = Counter()
    manifest: list[dict[str, str]] = []
    ok = 0
    failed: list[str] = []

    for row in rows:
        url = (row.get("url") or "").strip()
        suggested = (row.get("suggested_filename") or "").strip()
        if not url or not suggested:
            continue

        in_use = (row.get("in_use") or "").strip().lower() == "yes"
        folder = IN_USE_DIR if in_use else UNUSED_DIR
        filename = unique_name(suggested, used_names)
        dest = folder / filename

        try:
            if not dest.is_file():
                download(url, dest)
            ok += 1
            manifest.append(
                {
                    "id": row.get("id", ""),
                    "local_file": str(dest.relative_to(ROOT)),
                    "suggested_filename": filename,
                    "suggested_alt": row.get("suggested_alt", ""),
                    "usage": row.get("usage", ""),
                    "old_filename": row.get("current_filename", ""),
                    "url": url,
                    "in_use": row.get("in_use", ""),
                }
            )
            print(f"OK  {filename}")
        except (URLError, TimeoutError, OSError) as exc:
            failed.append(f"{row.get('id')}: {url} -> {exc}")
            print(f"FAIL {suggested}: {exc}", file=sys.stderr)

    MANIFEST_PATH.write_text(json.dumps(manifest, indent=2), encoding="utf-8")

    print()
    print(f"Downloaded: {ok}")
    print(f"Failed: {len(failed)}")
    print(f"In-use folder: {IN_USE_DIR}")
    print(f"Unused folder: {UNUSED_DIR}")
    print(f"Manifest: {MANIFEST_PATH}")

    if failed:
        (OUT_DIR / "failed.txt").write_text("\n".join(failed), encoding="utf-8")
        return 2
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
