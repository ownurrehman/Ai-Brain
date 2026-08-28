"""HTML helpers for Justccell Discover posts. Straight quotes, hyphens only."""

from __future__ import annotations


def p(text: str) -> str:
    return f"<p>{text.strip()}</p>"


def h2(text: str) -> str:
    return f"<h2>{text.strip()}</h2>"


def h3(text: str) -> str:
    return f"<h3>{text.strip()}</h3>"


def bq(label: str, text: str) -> str:
    return (
        f"<blockquote><p><strong>{label}</strong></p>"
        f"<p>{text.strip()}</p></blockquote>"
    )


def ul(items: list[str]) -> str:
    inner = "".join(f"<li>{i}</li>" for i in items)
    return f"<ul>{inner}</ul>"


def ol(items: list[str]) -> str:
    inner = "".join(f"<li>{i}</li>" for i in items)
    return f"<ol>{inner}</ol>"


def table(headers: list[str], rows: list[list[str]]) -> str:
    assert len(headers) <= 3
    th = "".join(f"<th>{h}</th>" for h in headers)
    body = []
    for row in rows:
        body.append("<tr>" + "".join(f"<td>{c}</td>" for c in row) + "</tr>")
    return (
        "<figure class=\"wp-block-table\"><table>"
        f"<thead><tr>{th}</tr></thead><tbody>{''.join(body)}</tbody>"
        "</table></figure>"
    )


def a(href: str, text: str) -> str:
    return f'<a href="{href}">{text}</a>'


def hr() -> str:
    return "<hr>"


def words(html: str) -> int:
    import re

    text = re.sub(r"<[^>]+>", " ", html)
    return len([w for w in text.split() if w])


BASE = "https://justccell.com"

U = {
    "all_in_ones": f"{BASE}/all-in-ones/",
    "cartridge": f"{BASE}/cartridge/",
    "pod": f"{BASE}/pod-system/",
    "battery": f"{BASE}/battery/",
    "contact": f"{BASE}/contact/",
    "about": f"{BASE}/about/",
    "tech": f"{BASE}/technology/",
    "safety": f"{BASE}/safety/",
    "research": f"{BASE}/research/",
    "manufacture": f"{BASE}/manufacture/",
    "choose": f"{BASE}/choose-hardware/",
    "oil": f"{BASE}/oil-types/",
    "thread": f"{BASE}/510-thread/",
    "three": f"{BASE}/justccell-3-0/",
    "pack": f"{BASE}/packaging/",
    "laser": f"{BASE}/laser-engraving/",
    "discover": f"{BASE}/discover/",
    "tank": f"{BASE}/all-in-ones/tank/",
    "rosin_bar": f"{BASE}/all-in-ones/rosin-bar/",
    "th2": f"{BASE}/cartridge/th2-evomax/",
    "ceramic": f"{BASE}/cartridge/ceramic-evomax/",
    "stylo": f"{BASE}/battery/stylo/",
    "luster": f"{BASE}/pod-system/luster-pro/",
    "g_oil": f"{BASE}/guides/how-to-choose-hardware-by-oil-type/",
    "g_charge": f"{BASE}/guides/how-to-charge-a-510-thread-battery/",
    "g_510": f"{BASE}/guides/what-is-a-510-thread-cartridge/",
    "g_fill": f"{BASE}/guides/how-to-fill-ceramic-cartridges-without-leaks/",
    "g_volt": f"{BASE}/guides/voltage-settings-for-distillate-live-resin-rosin/",
    "n_cer": f"{BASE}/news/ceramic-core-hardware-for-wholesale-buyers-2026/",
    "n_cr": f"{BASE}/news/child-resistant-hardware-and-packaging-for-licensed-brands/",
    "n_laser": f"{BASE}/news/laser-engraving-and-private-label-hardware/",
    "n_30": f"{BASE}/news/justccell-3-0-heating-for-live-extracts/",
    "n_uk": f"{BASE}/news/uk-and-europe-hardware-compliance-for-extract-brands/",
    "b_terp": f"{BASE}/blogs/what-are-terpenes-and-why-hardware-temperature-matters/",
    "b_vs": f"{BASE}/blogs/ceramic-vs-cotton-heating-for-cannabis-oil/",
    "b_leak": f"{BASE}/blogs/why-cheap-cartridges-leak-and-what-it-costs-brands/",
    "b_tray": f"{BASE}/blogs/how-to-build-a-first-sample-tray/",
    "b_med": f"{BASE}/blogs/medical-grade-materials-in-inhalation-hardware/",
}
