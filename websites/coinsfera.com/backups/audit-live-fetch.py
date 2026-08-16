#!/usr/bin/env python3
import subprocess, re

UA = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36"

def curl(url):
    p = subprocess.run(
        ["curl", "-sS", "-A", UA, "-D", "-", "-o", "/tmp/body.html", "-L", "--max-time", "25",
         "-H", "Cache-Control: no-cache", url],
        capture_output=True, text=True
    )
    try:
        body = open("/tmp/body.html", "rb").read()
    except Exception:
        body = b""
    return p.stdout, body

def hdr(h, name):
    m = re.search(rf"(?im)^{re.escape(name)}:\s*(.+)$", h)
    return m.group(1).strip() if m else ""

def status_line(h):
    lines = [l for l in h.splitlines() if l.startswith("HTTP/")]
    return " | ".join(lines) if lines else "?"

pages = [
    "https://www.coinsfera.com/",
    "https://www.coinsfera.com/ru/",
    "https://www.coinsfera.com/tr/",
    "https://www.coinsfera.com/buy-bitcoin-in-istanbul/",
    "https://www.coinsfera.com/ru/buy-bitcoin-in-istanbul/",
    "https://www.coinsfera.com/tr/buy-bitcoin-in-istanbul/",
    "https://www.coinsfera.com/sell-tether-in-istanbul/",
    "https://www.coinsfera.com/privacy-policy/",
    "https://www.coinsfera.com/blog/",
    "https://coinsfera.com/",
    "https://www.coinsfera.com/services/usdt/",
]

print("==== URL HEADERS / HEAD TAGS ====")
for url in pages:
    h, b = curl(url)
    html = b.decode("utf-8", "replace")
    title = re.search(r"<title>(.*?)</title>", html, re.I | re.S)
    desc = re.search(r'name=["\']description["\'][^>]*content=["\']([^"\']*)["\']', html, re.I)
    robots = re.search(r'name=["\']robots["\'][^>]*content=["\']([^"\']*)["\']', html, re.I)
    canon = re.search(r'rel=["\']canonical["\'][^>]*href=["\']([^"\']+)["\']', html, re.I)
    hrefl = re.findall(r'hreflang=["\']([^"\']+)["\'][^>]*href=["\']([^"\']+)["\']', html, re.I)
    if not hrefl:
        hrefl = re.findall(r'href=["\']([^"\']+)["\'][^>]*hreflang=["\']([^"\']+)["\']', html, re.I)
        hrefl = [(b2, a) for a, b2 in hrefl]
    lang = re.search(r'<html[^>]*lang=["\']([^"\']+)["\']', html, re.I)
    og = re.search(r'property=["\']og:locale["\'][^>]*content=["\']([^"\']+)["\']', html, re.I)
    h1s = re.findall(r"<h1[^>]*>(.*?)</h1>", html, re.I | re.S)
    h1s = [re.sub(r"\s+", " ", re.sub(r"<[^>]+>", "", x)).strip() for x in h1s]
    h1s = [x for x in h1s if x]
    imgs = re.findall(r"<img[^>]*>", html, re.I)
    missing_alt = sum(1 for i in imgs if not re.search(r"\balt=", i, re.I))
    empty_alt = sum(1 for i in imgs if re.search(r'alt=["\']\s*["\']', i, re.I))
    scripts = len(re.findall(r"<script", html, re.I))
    css = len(re.findall(r'rel=["\']stylesheet["\']', html, re.I))
    ld = len(re.findall(r'application/ld\+json', html, re.I))
    gtm = "GTM-" in html or "googletagmanager" in html
    print("---", url)
    print(" ", status_line(h))
    print("  enc=", hdr(h, "content-encoding"), "hsts=", bool(hdr(h, "strict-transport-security")),
          "xfo=", hdr(h, "x-frame-options") or "-", "xcto=", hdr(h, "x-content-type-options") or "-",
          "csp=", bool(hdr(h, "content-security-policy")))
    print("  cache=", hdr(h, "x-cache") or hdr(h, "x-sg-cache") or hdr(h, "x-proxy-cache") or "-")
    print("  html_lang=", lang.group(1) if lang else None, "og:locale=", og.group(1) if og else None)
    t = re.sub(r"<[^>]+>", "", title.group(1)).strip() if title else ""
    print("  title_len=", len(t), "title=", t[:100])
    print("  desc_len=", len(desc.group(1)) if desc else 0, "robots=", robots.group(1) if robots else None)
    print("  canon=", canon.group(1) if canon else None)
    print("  hreflang=", hrefl)
    print("  h1_count=", len(h1s), "h1=", h1s[:2])
    print("  imgs=", len(imgs), "no_alt=", missing_alt, "empty_alt=", empty_alt,
          "scripts=", scripts, "css=", css, "ld+json=", ld, "gtm=", gtm, "html_kb=", round(len(b) / 1024, 1))

print("\n==== ROBOTS ====")
h, b = curl("https://www.coinsfera.com/robots.txt")
print(status_line(h))
print(b.decode("utf-8", "replace")[:1800])

print("\n==== SITEMAP INDEX ====")
h, b = curl("https://www.coinsfera.com/sitemap_index.xml")
print(status_line(h))
txt = b.decode("utf-8", "replace")
locs = re.findall(r"<loc>(.*?)</loc>", txt)
print("indexes", locs)
for loc in locs:
    h2, b2 = curl(loc)
    t2 = b2.decode("utf-8", "replace")
    urls = re.findall(r"<url>", t2)
    last = re.findall(r"<lastmod>(.*?)</lastmod>", t2)[:2]
    hreflangs = len(re.findall(r"hreflang=", t2))
    print(loc, status_line(h2), "urls", len(urls), "hreflang_tags", hreflangs, "lastmod", last)
