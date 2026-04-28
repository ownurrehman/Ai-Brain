#!/usr/bin/env python3
"""
Rank Ray Media Index — Duplicate Detection & Management v2.0
Enigma Unified  •  SHA-256 + perceptual hash  •  CLI tool
"""
import argparse, hashlib, json, os, subprocess, sys
from pathlib import Path

INDEX_PATH = Path("/Users/sheikhown/.openclaw/workspace/data/rankray-media-index.json")
INDEX_PATH.parent.mkdir(parents=True, exist_ok=True)


def sha256(path: Path) -> str:
    h = hashlib.sha256()
    with open(path, "rb") as f:
        for chunk in iter(lambda: f.read(8192), b""):
            h.update(chunk)
    return h.hexdigest()


def perceptual_hash(path: Path) -> str | None:
    """8x8 grayscale average-hash via ImageMagick."""
    try:
        cmd = [
            "magick", str(path),
            "-resize", "8x8!", "-colorspace", "Gray",
            "-format", "%[fx:int(255*u)]", "info:"
        ]
        out = subprocess.check_output(cmd, text=True, timeout=30)
        vals = [int(x) for x in out.strip().split(",") if x.strip()]
        avg = sum(vals) / len(vals)
        bits = "".join("1" if v >= avg else "0" for v in vals)
        return hex(int(bits, 2))[2:].zfill(16)
    except Exception:
        return None


def hamming(a: str, b: str) -> int:
    return bin(int(a, 16) ^ int(b, 16)).count("1")


def load_index() -> dict:
    if INDEX_PATH.exists():
        return json.loads(INDEX_PATH.read_text())
    return {"version": 2, "updated": None, "items": []}


def save_index(data: dict) -> None:
    data["updated"] = datetime.now().isoformat()
    INDEX_PATH.write_text(json.dumps(data, indent=2))


def find_duplicate(path: Path, idx: dict, threshold: int = 5) -> dict | None:
    """Return closest existing item if perceptual hash distance ≤ threshold."""
    ph = perceptual_hash(path)
    if not ph:
        return None
    sh = sha256(path)
    for item in idx["items"]:
        if item["sha256"] == sh:
            return item  # exact match
        if item.get("phash") and hamming(ph, item["phash"]) <= threshold:
            return item  # perceptual match
    return None


def cmd_check(args) -> int:
    path = Path(args.file)
    if not path.exists():
        print(f"❌ File not found: {path}")
        return 1
    idx = load_index()
    dup = find_duplicate(path, idx)
    if dup:
        print(f"⚠️  Duplicate detected!")
        print(f"   Existing: {dup.get('slug', 'N/A')}  media_id={dup.get('media_id', 'N/A')}")
        print(f"   URL: {dup.get('url', 'N/A')}")
        return 2
    print("✅ No duplicate found")
    return 0


def cmd_add(args) -> int:
    path = Path(args.file)
    if not path.exists():
        print(f"❌ File not found: {path}")
        return 1
    idx = load_index()
    dup = find_duplicate(path, idx)
    if dup:
        print(f"⚠️  Duplicate of existing item '{dup.get('slug')}' — skipping")
        return 2
    item = {
        "sha256": sha256(path),
        "phash": perceptual_hash(path),
        "slug": args.slug,
        "media_id": args.media_id,
        "url": args.url,
        "alt": args.alt,
        "page": args.page or "",
        "filename": path.name,
    }
    idx["items"].append(item)
    save_index(idx)
    print(f"✅ Added: {path.name} → {args.url}")
    return 0


def cmd_list(_) -> int:
    idx = load_index()
    print(f"Media Index v{idx['version']}  •  {len(idx['items'])} items  •  Last update: {idx.get('updated', 'never')}")
    for i in idx["items"]:
        print(f"  [{i['media_id']}] {i['slug']}  {i['filename']}")
    return 0


def main():
    ap = argparse.ArgumentParser(description="Rank Ray Media Index Manager")
    sub = ap.add_subparsers(dest="cmd", required=True)

    chk = sub.add_parser("check", help="Check if an image is a duplicate")
    chk.add_argument("file")
    chk.add_argument("--slug", default="")

    add = sub.add_parser("add", help="Add an image to the index")
    add.add_argument("file")
    add.add_argument("--slug", required=True)
    add.add_argument("--media-id", required=True)
    add.add_argument("--url", required=True)
    add.add_argument("--alt", required=True)
    add.add_argument("--page", default="")

    lst = sub.add_parser("list", help="List all indexed images")

    args = ap.parse_args()
    fn = {"check": cmd_check, "add": cmd_add, "list": cmd_list}[args.cmd]
    sys.exit(fn(args))


if __name__ == "__main__":
    from datetime import datetime
    main()
