#!/usr/bin/env python3
"""Download Pexels product photos, crop 1600x900, strip EXIF."""
from __future__ import annotations

import json
import os
import urllib.request
from io import BytesIO
from pathlib import Path

from PIL import Image

ROOT = Path(__file__).resolve().parent
OUT = ROOT / "images-v2"
OUT.mkdir(exist_ok=True)

KEY = os.environ["PEXELS_API_KEY"]
HEADERS = {"Authorization": KEY, "User-Agent": "justccell-editorial/2.0"}

# Post slug -> (pexels_id, filename, alt with focus keyword)
MAP = [
    (
        "how-to-choose-hardware-by-oil-type",
        18552890,
        "justccell-v2-choose-hardware-oil-type.jpg",
        "choose vape hardware by oil type - nicotine and vape liquid bottles arranged as fill-spec stand-ins",
    ),
    (
        "how-to-charge-a-510-thread-battery",
        19901858,
        "justccell-v2-charge-510-battery.jpg",
        "how to charge a 510 battery - vape mods and 18650 cells on a white bench",
    ),
    (
        "what-is-a-510-thread-cartridge",
        9419514,
        "justccell-v2-what-is-510-thread.jpg",
        "what is a 510 thread cartridge - sleek vape pen hardware on a wooden surface",
    ),
    (
        "how-to-fill-ceramic-cartridges-without-leaks",
        8923166,
        "justccell-v2-fill-ceramic-cartridge.jpg",
        "fill ceramic cartridges without leaks - measured syringe fill on a lab bench",
    ),
    (
        "voltage-settings-for-distillate-live-resin-rosin",
        19901864,
        "justccell-v2-voltage-distillate-live-resin.jpg",
        "voltage settings for live resin - three vape devices on a white product background",
    ),
    (
        "ceramic-core-hardware-for-wholesale-buyers-2026",
        5495435,
        "justccell-v2-ceramic-core-wholesale-2026.jpg",
        "ceramic core vape hardware - CBD vape pens in a product still life",
    ),
    (
        "child-resistant-hardware-and-packaging-for-licensed-brands",
        17604901,
        "justccell-v2-child-resistant-hardware.jpg",
        "child-resistant hardware and packaging - labelled CBD oil bottles in branded packaging",
    ),
    (
        "laser-engraving-and-private-label-hardware",
        7254419,
        "justccell-v2-laser-engraving-private-label.jpg",
        "laser engraving private-label hardware - CNC laser head etching metal",
    ),
    (
        "justccell-3-0-heating-for-live-extracts",
        36788832,
        "justccell-v2-justccell-3-0-heating.jpg",
        "Justccell 3.0 heating - glowing coiled heating element close-up",
    ),
    (
        "uk-and-europe-hardware-compliance-for-extract-brands",
        27066158,
        "justccell-v2-uk-europe-hardware-compliance.jpg",
        "UK and Europe hardware notes - vape shop window and neon storefront",
    ),
    (
        "what-are-terpenes-and-why-hardware-temperature-matters",
        30682036,
        "justccell-v2-terpenes-hardware-temperature.jpg",
        "what are terpenes - frosty cannabis bud trichomes that drive hardware temperature",
    ),
    (
        "ceramic-vs-cotton-heating-for-cannabis-oil",
        3727687,
        "justccell-v2-ceramic-vs-cotton-heating.jpg",
        "ceramic vs cotton heating - vape coil being primed with e-liquid",
    ),
    (
        "why-cheap-cartridges-leak-and-what-it-costs-brands",
        8139076,
        "justccell-v2-why-cheap-cartridges-leak.jpg",
        "why cheap cartridges leak - cannabis concentrate jar and smoking accessories on a bench",
    ),
    (
        "how-to-build-a-first-sample-tray",
        13870353,
        "justccell-v2-first-sample-tray-extract-brands.jpg",
        "how to build a first sample tray - row of vape liquid bottles as a labelled sample set",
    ),
    (
        "medical-grade-materials-in-inhalation-hardware",
        29702938,
        "justccell-v2-medical-grade-inhalation-materials.jpg",
        "medical-grade materials in inhalation hardware - hand holding a medical inhaler device",
    ),
]


def fetch_src(pid: int) -> str:
    req = urllib.request.Request(f"https://api.pexels.com/v1/photos/{pid}", headers=HEADERS)
    with urllib.request.urlopen(req, timeout=30) as r:
        photo = json.loads(r.read().decode())
    return photo["src"]["original"]


def crop_1600(img: Image.Image) -> Image.Image:
    img = img.convert("RGB")
    w, h = img.size
    target = 16 / 9
    if w / h > target:
        nw = int(h * target)
        left = (w - nw) // 2
        img = img.crop((left, 0, left + nw, h))
    else:
        nh = int(w / target)
        top = (h - nh) // 2
        img = img.crop((0, top, w, top + nh))
    return img.resize((1600, 900), Image.Resampling.LANCZOS)


def main() -> None:
    manifest = []
    for slug, pid, filename, alt in MAP:
        src = fetch_src(pid)
        req = urllib.request.Request(src, headers={"User-Agent": "justccell-editorial/2.0"})
        with urllib.request.urlopen(req, timeout=90) as r:
            raw = r.read()
        img = Image.open(BytesIO(raw))
        out = crop_1600(img)
        dest = OUT / filename
        out.save(dest, "JPEG", quality=86, optimize=True, progressive=True)
        manifest.append(
            {
                "slug": slug,
                "file": filename,
                "pexels": pid,
                "alt": alt,
                "bytes": dest.stat().st_size,
            }
        )
        print("OK", filename, dest.stat().st_size)
    (OUT / "map.json").write_text(json.dumps(manifest, indent=2) + "\n")
    print("wrote", len(manifest), "images")


if __name__ == "__main__":
    main()
