#!/usr/bin/env python3
"""Fetch live Coinsfera pages and print WPML/Yoast SEO tags."""

from __future__ import annotations

import re
import time
import urllib.request
from urllib.parse import urlparse

UA = (
    "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) "
    "AppleWebKit/537.36 Chrome/120.0.0.0 Safari/537.36"
)

URLS = [
    "https://www.coinsfera.com/",
    "https://www.coinsfera.com/ru/",
    "https://www.coinsfera.com/tr/",
    "https://www.coinsfera.com/buy-bitcoin-in-istanbul/",
    "https://www.coinsfera.com/ru/buy-bitcoin-in-istanbul/",
    "https://www.coinsfera.com/tr/buy-bitcoin-in-istanbul/",
    "https://www.coinsfera.com/sell-bitcoin-in-istanbul/",
    "https://www.coinsfera.com/ru/sell-bitcoin-in-istanbul/",
    "https://www.coinsfera.com/tr/sell-bitcoin-in-istanbul/",
]


def fetch(url: str) -> tuple[str, dict[str, str], str]:
    import subprocess

    stamped = url + ("&" if "?" in url else "?") + "nocache=" + str(int(time.time()))
    result = subprocess.run(
        [
            "curl",
            "-sL",
            "-A",
            UA,
            "-H",
            "Cache-Control: no-cache",
            "--max-time",
            "25",
            "-w",
            "\n__META__%{http_code} %{url_effective}",
            stamped,
        ],
        capture_output=True,
        text=True,
        check=False,
    )
    body = result.stdout
    status = "000"
    final = url
    if "__META__" in body:
        html, meta = body.rsplit("__META__", 1)
        parts = meta.strip().split(" ", 1)
        status = parts[0]
        if len(parts) > 1:
            final = parts[1]
    else:
        html = body
    if status != "200":
        raise RuntimeError("HTTP %s for %s" % (status, url))
    return final, {}, html


def meta_content(html: str, key: str) -> list[str]:
    out: list[str] = []
    for tag in re.findall(r"<meta[^>]+>", html, re.I):
        if re.search(r'(?:property|name)=["\']%s["\']' % re.escape(key), tag, re.I):
            match = re.search(r'content=["\']([^"\']*)["\']', tag, re.I)
            if match:
                out.append(match.group(1))
    return out


def link_tags(html: str, rel: str) -> list[tuple[str, str]]:
    out: list[tuple[str, str]] = []
    for tag in re.findall(r"<link[^>]+>", html, re.I):
        if not re.search(r'rel=["\'][^"\']*%s[^"\']*["\']' % re.escape(rel), tag, re.I):
            continue
        href = re.search(r'href=["\']([^"\']+)["\']', tag, re.I)
        hreflang = re.search(r'hreflang=["\']([^"\']+)["\']', tag, re.I)
        out.append(
            (
                hreflang.group(1) if hreflang else "",
                href.group(1) if href else "",
            )
        )
    return out


def html_lang(html: str) -> str:
    match = re.search(r"<html[^>]*lang=[\"']([^\"']+)[\"']", html, re.I)
    return match.group(1) if match else ""


def title(html: str) -> str:
    match = re.search(r"<title>(.*?)</title>", html, re.I | re.S)
    if not match:
        return ""
    return re.sub(r"\s+", " ", match.group(1)).strip()


def visible_sample(html: str) -> dict[str, bool]:
    return {
        "tr_social": "Sosyal Medyada Bizi Takip Edin" in html,
        "ru_social": "Следите за нами в соцсетях" in html,
        "en_social": "Follow Us on Social Media" in html,
        "tr_loc": "İstanbul'da Coinsfera OTC ATM Konumu" in html,
        "ru_loc": "Расположение OTC ATM Coinsfera" in html,
        "en_loc": "Location of Coinsfera OTC ATM" in html,
        "tr_email": ">E-posta<" in html or "> E-posta <" in html,
        "ru_email": "Эл. почта" in html,
    }


def main() -> None:
    for url in URLS:
        print("\n" + "=" * 80)
        print("URL", url)
        try:
            final, headers, html = fetch(url)
        except Exception as exc:  # noqa: BLE001
            print(" ERR", exc)
            continue
        print(" final", final.split("?")[0])
        print(" html lang", html_lang(html))
        print(" title", title(html)[:140])
        print(" robots", meta_content(html, "robots")[:1])
        canonicals = [href for _, href in link_tags(html, "canonical")]
        print(" canonical", canonicals)
        hreflangs = [(hl, href) for hl, href in link_tags(html, "alternate") if hl]
        print(" hreflang count", len(hreflangs))
        for lang, href in hreflangs:
            print("   %-10s %s" % (lang, href))
        print(" og:locale", meta_content(html, "og:locale"))
        print(" og:locale:alternate", meta_content(html, "og:locale:alternate"))
        print(" og:url", meta_content(html, "og:url"))
        print(" og:title", [item[:100] for item in meta_content(html, "og:title")])
        print(" description", [item[:120] for item in meta_content(html, "description")])
        print(" twitter:title", [item[:100] for item in meta_content(html, "twitter:title")])
        smoke = {k: v for k, v in visible_sample(html).items() if v}
        print(" lang-smoke", smoke)

        page_path = urlparse(url).path
        issues: list[str] = []
        if canonicals:
            can = canonicals[0]
            if "nocache=" in can:
                issues.append("canonical has nocache query")
            if urlparse(can).netloc != "www.coinsfera.com":
                issues.append("canonical host is " + urlparse(can).netloc)
            if urlparse(can).scheme != "https":
                issues.append("canonical not https")
            # self-canonical: path should match requested path
            if urlparse(can).path.rstrip("/") != page_path.rstrip("/"):
                issues.append("canonical path differs: %s vs %s" % (urlparse(can).path, page_path))
        else:
            issues.append("missing canonical")

        langs = {hl: href for hl, href in hreflangs}
        for needed in ("en", "ru", "tr", "x-default"):
            if needed not in langs:
                issues.append("missing hreflang " + needed)
        if "x-default" in langs and "en" in langs and langs["x-default"] != langs["en"]:
            issues.append("x-default is not English: " + langs["x-default"])
        for hl, href in hreflangs:
            parsed = urlparse(href)
            if parsed.scheme != "https":
                issues.append("hreflang %s not https" % hl)
            if parsed.netloc != "www.coinsfera.com":
                issues.append("hreflang %s host %s" % (hl, parsed.netloc))
            if hl == "ru" and not parsed.path.startswith("/ru/"):
                if parsed.path not in ("/ru", "/ru/"):
                    issues.append("hreflang ru path not /ru/: " + parsed.path)
            if hl == "tr" and not parsed.path.startswith("/tr/"):
                if parsed.path not in ("/tr", "/tr/"):
                    issues.append("hreflang tr path not /tr/: " + parsed.path)
            if hl in ("en", "x-default") and parsed.path.startswith(("/ru/", "/tr/")):
                issues.append("hreflang %s points at translated path %s" % (hl, parsed.path))

        locale = meta_content(html, "og:locale")
        if "/ru/" in url and locale and not locale[0].lower().startswith("ru"):
            issues.append("og:locale should be ru: " + locale[0])
        if "/tr/" in url and locale and not locale[0].lower().startswith("tr"):
            issues.append("og:locale should be tr: " + locale[0])
        if "/ru/" not in url and "/tr/" not in url and locale and not locale[0].lower().startswith("en"):
            issues.append("og:locale should be en: " + locale[0])

        if "/ru/buy-bitcoin" in url:
            if smoke.get("tr_loc") or smoke.get("tr_social"):
                issues.append("RU buy-bitcoin still showing Turkish globals")
            if not smoke.get("ru_loc"):
                issues.append("RU buy-bitcoin missing Russian location heading")
        if "/tr/buy-bitcoin" in url:
            if smoke.get("ru_loc") or smoke.get("ru_social"):
                issues.append("TR buy-bitcoin still showing Russian globals")
            if not smoke.get("tr_loc"):
                issues.append("TR buy-bitcoin missing Turkish location heading")

        if issues:
            print(" ISSUES:")
            for item in issues:
                print("  -", item)
        else:
            print(" ISSUES: none")


if __name__ == "__main__":
    main()
