#!/usr/bin/env python3
import subprocess, re, os
UA = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36"

def get(url):
    p = subprocess.run(
        ["curl", "-sS", "-A", UA, "-D", "-", "-o", "/tmp/b.html", "-L", "--max-time", "20", url],
        capture_output=True, text=True,
    )
    body = open("/tmp/b.html", "rb").read()
    html = body.decode("utf-8", "replace")
    codes = [l for l in p.stdout.splitlines() if l.startswith("HTTP/")]
    robots = re.search(r'name=["\']robots["\'][^>]*content=["\']([^"\']+)', html, re.I)
    canon = re.search(r'rel=["\']canonical["\'][^>]*href=["\']([^"\']+)', html, re.I)
    title = re.search(r"<title>(.*?)</title>", html, re.I | re.S)
    t = re.sub(r"<[^>]+>", "", title.group(1)).strip()[:90] if title else None
    return " | ".join(codes), robots.group(1) if robots else None, canon.group(1) if canon else None, t, len(body)

urls = [
    "https://www.coinsfera.com/category/news/",
    "https://www.coinsfera.com/author/hidayat/",
    "https://www.coinsfera.com/author/sheikhown/",
    "https://www.coinsfera.com/category/blog/",
]
for url in urls:
    codes, robots, canon, title, n = get(url)
    print("---", url)
    print(" ", codes)
    print("  robots=", robots)
    print("  canon=", canon)
    print("  title=", title, "bytes", n)

subprocess.run(["curl", "-sS", "-A", UA, "-o", "/tmp/home.html", "--max-time", "20", "https://www.coinsfera.com/"], check=False)
html = open("/tmp/home.html", encoding="utf-8", errors="replace").read()
assets = re.findall(r"https://www\.coinsfera\.com/wp-content/uploads/siteground-optimizer-assets/[^\"']+", html)
assets = list(dict.fromkeys(assets))
print("\n==== SG asset sizes ====")
for u in assets:
    p = subprocess.run(
        ["curl", "-sS", "-A", UA, "-o", "/dev/null", "-w", "%{http_code} %{size_download}", "--max-time", "20", u],
        capture_output=True, text=True,
    )
    print(p.stdout, u.split("/")[-1][:90])
