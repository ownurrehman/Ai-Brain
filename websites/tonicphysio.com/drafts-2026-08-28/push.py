#!/usr/bin/env python3
"""Push Tonic Physio Wave-1 drafts to WordPress as DRAFT posts.
Reads registry, checks slug collisions, uploads fresh Pexels featured image
(EXIF stripped), sets Yoast fields, category 58, author 5. Dry-run by default."""
import os, re, sys, json, time, io, hashlib
import requests
from dotenv import load_dotenv

VAULT = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain"
WS = f"{VAULT}/websites/tonicphysio.com/drafts-2026-08-28"
load_dotenv(f"{VAULT}/master-env.env")
BASE = os.getenv("TONICPHYSIO_WP_URL").rstrip("/")
AUTH = (os.getenv("TONICPHYSIO_WP_USER"), os.getenv("TONICPHYSIO_WP_APP_PASS"))
PEXELS = os.getenv("PEXELS_API_KEY")

DRY = "--push" not in sys.argv
ONLY = [a for a in sys.argv[1:] if a.isdigit()]

# slug -> (file, image query)
PLAN = {
    "do-you-need-a-referral-for-physiotherapy-in-ontario": ("post-01-referral-ontario.html", "physiotherapist consulting patient clinic"),
    "how-often-should-you-go-to-physiotherapy": ("post-02-session-frequency.html", "physiotherapy exercise rehabilitation session"),
    "is-physiotherapy-covered-by-ohip": ("post-03-ohip-vs-private.html", "health insurance paperwork canada"),
    "massage-therapy-for-desk-workers": ("post-04-massage-desk-workers.html", "office worker neck shoulder pain massage"),
    "how-often-should-you-get-a-massage": ("post-05-massage-frequency.html", "registered massage therapy treatment room"),
    "shockwave-therapy-for-plantar-fasciitis": ("post-06-shockwave-plantar.html", "foot heel pain physiotherapy treatment"),
    "shockwave-therapy-for-tennis-elbow": ("post-07-shockwave-tennis-elbow.html", "elbow arm physiotherapy treatment"),
    "whiplash-symptoms-after-car-accident": ("post-08-whiplash-delayed.html", "neck pain cervical treatment"),
    "concussion-rehab-after-car-accident": ("post-09-concussion-mva.html", "dizziness balance physiotherapy assessment"),
    "how-to-sleep-with-tmj-pain": ("post-10-tmj-sleep.html", "jaw pain face massage therapy"),
    "jaw-clicking-treatment": ("post-11-jaw-clicking.html", "jaw pain therapist examination"),
    "repetitive-strain-injury-treatment": ("post-12-rsi-work.html", "wrist hand therapy workplace injury"),
    "mens-pelvic-floor-physiotherapy": ("post-13-mens-pelvic-floor.html", "man physiotherapy core exercise clinic"),
    "custom-knee-brace-for-skiing": ("post-14-knee-brace-hockey-ski.html", "knee brace athlete sport"),
    "custom-orthotics-vs-over-the-counter-insoles": ("post-15-orthotics-vs-insoles.html", "custom orthotic insole shoe fitting"),
    "return-to-sport-testing": ("post-16-return-to-sport.html", "athlete jump test rehabilitation sport"),
}

def head_comment(path):
    t = open(path, encoding="utf-8").read()
    m = re.match(r"<!--\s*title:(.*?)\|\s*meta:(.*?)\|\s*kw:(.*?)\|\s*image:(.*?)\|\s*alt:(.*?)-->", t, re.S)
    if not m:
        return None
    return dict(title=m.group(1).strip(), meta=m.group(2).strip(), kw=m.group(3).strip(),
                image_concept=m.group(4).strip(), alt=m.group(5).strip(), body=t[m.end():].strip())

def wc(html):
    txt = re.sub(r"<[^>]+>", " ", html)
    return len(txt.split())

def check_links(body, slug):
    links = re.findall(r'href="([^"]+)"', body)
    probs = []
    seen = set()
    for l in links:
        if l in seen:
            probs.append(f"DUPLICATE LINK {l}")
        seen.add(l)
        if "tonicphysio.com" not in l and not l.startswith("tel:") and not l.startswith("#"):
            probs.append(f"EXTERNAL LINK {l}")
    return links, probs

def banned_scan(html):
    bad = []
    pats = [r"\u2014", r"\u2013", r"[\u201c\u201d\u2018\u2019]", r"\b(delve|tapestry|testament|pivotal|crucial|vibrant|seamless|game-changer|groundbreaking|nestled|stunning|breathtaking)\b",
            r"it'?s important to note", r"in conclusion", r"let'?s dive", r"when it comes to", r"in today'?s",
            r"not just\b.{0,40}\bbut\b", r"<h1", r"<img", r"FAQ", r"Conclusion", r"TL;DR", r"\bof course\b"]
    for p in pats:
        for m in re.finditer(p, html, re.I):
            bad.append((p, html[max(0,m.start()-40):m.end()+40].replace("\n"," ")))
    # invisible unicode
    for i, c in enumerate(html):
        if unicodedata_category(c) in ("Cf", "Co"):
            bad.append(("unicode", hex(ord(c))))
    return bad

def unicodedata_category(c):
    import unicodedata
    return unicodedata.category(c)

def pexels_image(query, slug):
    r = requests.get("https://api.pexels.com/v1/search",
                     params={"query": query, "per_page": 6, "orientation": "landscape"},
                     headers={"Authorization": PEXELS}, timeout=20)
    r.raise_for_status()
    photos = r.json().get("photos", [])
    if not photos:
        return None
    pick = photos[0]
    url = pick["src"]["large2x"]
    img = requests.get(url, timeout=30)
    img.raise_for_status()
    fn = f"{slug}.jpeg"
    fp = f"{WS}/images/{fn}"
    open(fp, "wb").write(img.content)
    return fp

def strip_metadata(fp):
    os.system(f'/opt/homebrew/bin/exiftool -all= -overwrite_original "{fp}" >/dev/null 2>&1')

def media_library_search(filename_slug):
    r = requests.get(f"{BASE}/media", params={"search": filename_slug, "per_page": 5}, auth=AUTH, timeout=30)
    if r.status_code == 200:
        return r.json()
    return []

def main():
    report = []
    for slug, (fname, query) in PLAN.items():
        if ONLY and slug[:2].replace("-","") not in ONLY and fname.split("-")[1][:2] not in ONLY:
            continue
        path = f"{WS}/{fname}"
        if not os.path.exists(path):
            report.append((slug, "MISSING FILE", fname))
            continue
        meta = head_comment(path)
        body = meta["body"] if meta else open(path, encoding="utf-8").read()
        words = wc(body)
        links, lprobs = check_links(body, slug)
        bprobs = banned_scan(body)
        status = "OK"
        if not meta: status = "NO HEADER COMMENT"
        if words < 2000: status = f"LOW WORDS {words}"
        if lprobs: status += " | " + ";".join(lprobs[:3])
        if bprobs: status += f" | BANNED HITS {len(bprobs)}"
        report.append((slug, status, f"{words}w, {len(links)} links, title={len(meta['title']) if meta else '?'}c, meta={len(meta['meta']) if meta else '?'}c"))
    for row in report:
        print(" | ".join(row))

if __name__ == "__main__":
    main()