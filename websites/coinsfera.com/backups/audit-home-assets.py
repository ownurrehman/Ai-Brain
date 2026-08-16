#!/usr/bin/env python3
import subprocess, re
UA = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36"
p = subprocess.run(
    ["curl", "-sS", "-A", UA, "-o", "/tmp/home.html", "-w",
     "ttfb=%{time_starttransfer} total=%{time_total} size=%{size_download} code=%{http_code}",
     "--max-time", "25", "https://www.coinsfera.com/"],
    capture_output=True, text=True,
)
print(p.stdout)
html = open("/tmp/home.html", encoding="utf-8", errors="replace").read()
print("\n==== third party counts ====")
needles = [
    "googletagmanager", "GTM-", "google-analytics", "yandex", "mc.yandex", "ahrefs",
    "trustpilot", "facebook.net", "hotjar", "maps.googleapis", "fonts.googleapis",
    "font-awesome", "kit.fontawesome", "elementor", "chaty", "siteground-optimizer",
    "lottie", "wp-reviews",
]
low = html.lower()
for s in needles:
    print(s, low.count(s.lower()))
print("\n==== script srcs ====")
for s in re.findall(r"<script[^>]+src=[\"']([^\"']+)", html, re.I):
    print(" ", s[:180])
print("inline_scripts", len(re.findall(r"<script(?![^>]+src=)", html, re.I)))
print("\n==== css ====")
for s in re.findall(r"href=[\"']([^\"']+\.css[^\"']*)", html, re.I):
    print(" ", s[:180])
print("\n==== preload ====")
for s in re.findall(r"<link[^>]+rel=[\"']preload[\"'][^>]*>", html, re.I):
    print(" ", re.sub(r"\s+", " ", s)[:220])
print("\n==== ld+json @types ====")
for m in re.findall(r"<script[^>]*ld\+json[^>]*>(.*?)</script>", html, re.I | re.S):
    types = re.findall(r"\"@type\"\s*:\s*\"([^\"]+)\"", m)
    print(" ", types[:15])
imgs = re.findall(r"<img[^>]*>", html, re.I)
lazy = sum(1 for i in imgs if re.search(r"loading=[\"']lazy", i, re.I) or "lazyload" in i.lower())
print("\nimgs", len(imgs), "lazyish", lazy, "fetchpriority", "fetchpriority" in low)
if imgs:
    print("first_img", re.sub(r"\s+", " ", imgs[0])[:280])
