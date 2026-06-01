#!/usr/bin/env python3
"""
TonicPhysio Service Page Meta Audit
Checks all 65 service pages for SEO quality issues
"""
import json, base64, urllib.request, urllib.error

WP_URL = "https://tonicphysio.com"
USER = "Dan"
PASS = "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"
auth_str = base64.b64encode(f"{USER}:{PASS}".encode()).decode()

def api_get(path):
    req = urllib.request.Request(f"{WP_URL}/wp-json/wp/v2/{path}", headers={
        "Authorization": f"Basic {auth_str}",
        "Content-Type": "application/json"
    })
    with urllib.request.urlopen(req, timeout=30) as resp:
        return json.loads(resp.read().decode())

# Get all service pages (cat 325)
pages = api_get("pages?per_page=100")
service_pages = [p for p in pages if 325 in p.get("page_category", [])]

issues = {
    "desc_too_long": [],
    "desc_too_short": [],
    "missing_desc": [],
    "weak_title": [],
    "missing_kw": [],
    "missing_brand": [],
    "slug_no_milton": [],
    "good": []
}

for p in service_pages:
    pid = p["id"]
    title = p["title"]["rendered"]
    slug = p["slug"]
    meta = p.get("meta", {})
    acf = p.get("acf", {})
    if isinstance(acf, list):
        acf = {}
    
    yoast_title = meta.get("_yoast_wpseo_title", "") or ""
    yoast_desc = meta.get("_yoast_wpseo_metadesc", "") or ""
    yoast_kw = meta.get("_yoast_wpseo_focuskw", "") or ""
    h1 = acf.get("h1", "") or ""
    
    desc_len = len(yoast_desc)
    title_len = len(yoast_title)
    
    has_milton = "milton" in (yoast_title + yoast_kw + slug + h1).lower()
    has_brand = "tonic" in yoast_title.lower() or "tonic" in yoast_desc.lower()
    has_kw = bool(yoast_kw.strip())
    
    # Categorize
    problems = []
    
    if not yoast_desc.strip():
        problems.append("missing_desc")
    elif desc_len > 160:
        problems.append("desc_too_long")
    elif desc_len < 80:
        problems.append("desc_too_short")
    
    if not yoast_title.strip():
        problems.append("weak_title")
    elif title_len < 20:
        problems.append("weak_title")
    elif not has_milton:
        problems.append("weak_title")
    
    if not has_kw:
        problems.append("missing_kw")
    
    if not has_brand:
        problems.append("missing_brand")
    
    if not has_milton and "milton" not in slug.lower():
        problems.append("slug_no_milton")
    
    if not problems:
        issues["good"].append({
            "id": pid, "title": title, "slug": slug,
            "yoast_title": yoast_title, "desc": yoast_desc, "kw": yoast_kw
        })
    else:
        for prob in problems:
            issues[prob].append({
                "id": pid, "title": title, "slug": slug,
                "yoast_title": yoast_title, "desc": yoast_desc, "kw": yoast_kw,
                "h1": h1, "desc_len": desc_len, "title_len": title_len
            })

# Build report
report = "# TonicPhysio Service Page Meta Audit\n\n"
report += f"**Date:** 2026-05-12\n"
report += f"**Total Service Pages:** {len(service_pages)}\n\n"

for key, label in [
    ("missing_desc", "Missing Meta Descriptions"),
    ("desc_too_long", f"Meta Descriptions >160 chars"),
    ("desc_too_short", "Meta Descriptions <80 chars"),
    ("weak_title", "Weak/Short/Missing-Milton Titles"),
    ("missing_kw", "Missing Focus Keywords"),
    ("missing_brand", "Missing Brand Name (Tonic)"),
    ("slug_no_milton", "Slug + SEO Missing Milton"),
]:
    items = issues[key]
    report += f"## {label} ({len(items)})\n\n"
    if not items:
        report += "None found.\n\n"
        continue
    for item in items:
        report += f"- **{item['title']}** (ID: `{item['id']}`)\n"
        report += f"  - Slug: `{item['slug']}`\n"
        report += f"  - Title: `{item['yoast_title'][:60]}` ({item.get('title_len', len(item['yoast_title']))} chars)\n"
        report += f"  - Desc: `{item['desc'][:70]}` ({item.get('desc_len', len(item['desc']))} chars)\n"
        report += f"  - KW: `{item['kw']}`\n"
        if item.get('h1'):
            report += f"  - H1: `{item['h1'][:60]}`\n"
        report += "\n"

report += f"## Good Pages ({len(issues['good'])})\n\n"
for item in issues["good"]:
    report += f"- {item['title']} (ID: {item['id']}) — Title: {item['yoast_title'][:50]}...\n"

# Summary counts
report += "\n## Summary\n\n"
report += "| Issue | Count |\n"
report += "|-------|-------|\n"
for key, label in [
    ("missing_desc", "Missing meta descriptions"),
    ("desc_too_long", "Descriptions too long"),
    ("desc_too_short", "Descriptions too short"),
    ("weak_title", "Weak titles"),
    ("missing_kw", "Missing keywords"),
    ("missing_brand", "Missing brand"),
    ("slug_no_milton", "No Milton in slug+SEO"),
    ("good", "Good pages"),
]:
    report += f"| {label} | {len(issues[key])} |\n"

with open("/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/tonicphysio.com/meta-audit-report.md", "w") as f:
    f.write(report)

print(f"Audit complete: {len(service_pages)} service pages checked")
print(f"Good: {len(issues['good'])} | Issues: {len(service_pages) - len(issues['good'])}")
for key, label in [
    ("missing_desc", "Missing descriptions"),
    ("desc_too_long", "Descriptions too long"),
    ("desc_too_short", "Descriptions too short"),
    ("weak_title", "Weak titles"),
    ("missing_kw", "Missing keywords"),
    ("missing_brand", "Missing brand"),
    ("slug_no_milton", "No Milton in slug+SEO"),
]:
    print(f"  {label}: {len(issues[key])}")
