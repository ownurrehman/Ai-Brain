#!/usr/bin/env python3
"""Verify coinsfera SEO batch from the origin with a browser UA."""
from __future__ import annotations

import re
import urllib.request

UA = (
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) "
    "AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36"
)


def fetch(url: str, meta: bool = False) -> tuple[int, str, str]:
    req = urllib.request.Request(url, headers={"User-Agent": UA}, method="GET")
    try:
        with urllib.request.urlopen(req, timeout=25) as res:
            body = res.read().decode("utf-8", "replace")
            loc = res.geturl()
            return res.status, loc, body
    except urllib.error.HTTPError as e:
        loc = e.headers.get("Location", "")
        body = e.read().decode("utf-8", "replace") if e.fp else ""
        return e.code, loc, body


def robots(html: str) -> str:
    m = re.search(r'<meta\s+name=["\']robots["\']\s+content=["\']([^"\']+)', html, re.I)
    return m.group(1) if m else "(missing)"


def title(html: str) -> str:
    m = re.search(r"<title>([^<]+)</title>", html, re.I)
    return m.group(1).strip() if m else "(missing)"


checks = []

# 1 recent news
st, loc, html = fetch(
    "https://www.coinsfera.com/news/uk-crypto-gets-green-light-as-lawmakers-classify-them-as-property/"
)
r = robots(html)
print(f"NEW_NEWS {st} robots={r} title={title(html)[:80]}")
checks.append(("recent news indexable", "noindex" not in r.lower()))

# 2 old news
st, loc, html = fetch(
    "https://www.coinsfera.com/news/just-eat-will-add-bitcoin-to-payment-options-in-france/"
)
r = robots(html)
print(f"OLD_NEWS {st} robots={r}")
checks.append(("old news noindex", "noindex" in r.lower()))

# 3 RU redirect
req = urllib.request.Request(
    "https://www.coinsfera.com/ru/news/%d0%ba%d0%b0%d0%ba-%d0%bf%d0%be%d0%bb%d1%8c%d0%b7%d0%be%d0%b2%d0%b0%d1%82%d1%8c%d1%81%d1%8f-trust-wallet/",
    headers={"User-Agent": UA},
    method="GET",
)
try:
    opener = urllib.request.build_opener(urllib.request.HTTPRedirectHandler)
    # don't follow — use raw
except Exception:
    pass

class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl):
        return None

opener = urllib.request.build_opener(NoRedirect)
try:
    res = opener.open(req, timeout=25)
    print(f"RU_REDIR followed {res.status} {res.geturl()}")
    checks.append(("ru news redirect", False))
except urllib.error.HTTPError as e:
    loc = e.headers.get("Location", "")
    print(f"RU_REDIR {e.code} -> {loc}")
    checks.append(("ru news redirect 301 to novosti", e.code in (301, 302, 308) and "novosti" in loc))

# 4 homepage
st, loc, html = fetch("https://www.coinsfera.com/")
head = html.split("</head>", 1)[0]
foot = html[html.lower().find("</head>"):]
print(f"HOME {st} title={title(html)[:90]}")
print("  cdnjs_fa", "cdnjs.cloudflare.com/ajax/libs/font-awesome" in html)
print("  gtm", "GTM-P7ZNP7K" in head)
print("  yandex_verify", "yandex-verification" in head)
print("  yandex_tag_in_head", "mc.yandex.ru" in head)
print("  ahrefs_in_head", "analytics.ahrefs.com" in head)
print("  trustpilot_in_head", "widget.trustpilot.com" in head)
print("  delayed_loader", "mc.yandex.ru/metrika/tag.js" in html and "setTimeout" in html)
lazy = len(re.findall(r'<img[^>]+loading=["\']lazy["\']', html, re.I))
imgs = len(re.findall(r"<img\b", html, re.I))
print(f"  imgs={imgs} lazy={lazy}")
checks.append(("no cdnjs FA", "cdnjs.cloudflare.com/ajax/libs/font-awesome" not in html))
checks.append(("GTM in head", "GTM-P7ZNP7K" in head))
checks.append(("yandex tag not in head", "mc.yandex.ru" not in head))
checks.append(("ahrefs not in head", "analytics.ahrefs.com" not in head))
checks.append(("some lazy images", lazy >= 10))

# 5 RU litecoin title
st, loc, html = fetch("https://www.coinsfera.com/ru/buy-litecoin-in-istanbul/")
t = title(html)
print(f"RU_LTC {st} title={t}")
checks.append(("RU litecoin Russian title", "Купить Litecoin" in t and "Buy Litecoin" not in t))

# 6 RU home title
st, loc, html = fetch("https://www.coinsfera.com/ru/")
print(f"RU_HOME {st} title={title(html)}")

print("\n==== RESULTS ====")
ok = True
for name, passed in checks:
    print(("OK  " if passed else "FAIL") + " " + name)
    ok = ok and passed
raise SystemExit(0 if ok else 1)
