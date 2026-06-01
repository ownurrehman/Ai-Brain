#!/usr/bin/env python3
"""
TonicPhysio Post Registry Builder
Fetches all pages via REST API and creates post-registry.md
"""
import json, base64, urllib.request, urllib.error, datetime

WP_URL = "https://tonicphysio.com"
USER = "Dan"
PASS = "TP#Admin@2026"
PER_PAGE = 100

auth_str = base64.b64encode(f"{USER}:{PASS}".encode()).decode()

all_pages = []
page = 1
while True:
    url = f"{WP_URL}/wp-json/wp/v2/pages?per_page={PER_PAGE}&page={page}"
    req = urllib.request.Request(url, headers={
        "Authorization": f"Basic {auth_str}",
        "Content-Type": "application/json"
    })
    try:
        with urllib.request.urlopen(req, timeout=30) as resp:
            data = json.loads(resp.read().decode())
            if not data:
                break
            all_pages.extend(data)
            if len(data) < PER_PAGE:
                break
            page += 1
    except urllib.error.HTTPError as e:
        print(f"Error page {page}: {e.code}")
        break
    except Exception as e:
        print(f"Error: {e}")
        break

# Sort by parent then title
all_pages.sort(key=lambda p: (p.get("parent", 0), p.get("title", {}).get("rendered", "")))

registry = []
for p in all_pages:
    yoast = p.get("meta", {})
    raw_acf = p.get("acf", {})
    acf = raw_acf if isinstance(raw_acf, dict) else {}
    registry.append({
        "id": p["id"],
        "title": p["title"]["rendered"],
        "slug": p["slug"],
        "status": p["status"],
        "parent": p.get("parent"),
        "template": p.get("template", ""),
        "yoast_title": yoast.get("_yoast_wpseo_title", ""),
        "yoast_desc": yoast.get("_yoast_wpseo_metadesc", ""),
        "yoast_kw": yoast.get("_yoast_wpseo_focuskw", ""),
        "h1": acf.get("h1", ""),
        "page_category": p.get("page_category", []),
    })

now = datetime.datetime.now().strftime('%Y-%m-%d %H:%M')

output = f"""# TonicPhysio Post Registry

**Generated:** {now}
**Total Pages:** {len(registry)}

## All Pages

| ID | Title | Slug | Status | Parent | Yoast Title | Yoast Desc | Yoast KW | H1 | Category |
|----|-------|------|--------|--------|-------------|------------|----------|----|----------|
"""

for r in registry:
    cat = ",".join(map(str, r["page_category"]))
    output += f"| {r['id']} | {r['title'][:40]} | {r['slug'][:35]} | {r['status']} | {r['parent'] or '-'} | {r['yoast_title'][:40] if r['yoast_title'] else '-'} | {r['yoast_desc'][:40] if r['yoast_desc'] else '-'} | {r['yoast_kw'][:20] if r['yoast_kw'] else '-'} | {r['h1'][:40] if r['h1'] else '-'} | {cat} |\n"

output += "\n## Service Pages (Category 325)\n\n"
service_count = 0
for r in registry:
    if 325 in r.get("page_category", []):
        output += f"- **{r['title']}** — ID: `{r['id']}` — Slug: `{r['slug']}` — Status: {r['status']}\n"
        service_count += 1

output += f"\n**Total Service Pages:** {service_count}\n\n"

output += "## Pages Missing 'Milton' in SEO Fields (Published)\n\n"
missing_milton = []
for r in registry:
    if r["status"] != "publish":
        continue
    combined = f"{r['yoast_title']} {r['yoast_kw']} {r['h1']} {r['slug']}".lower()
    if "milton" not in combined:
        missing_milton.append(r)
        output += f"- **{r['title']}** — ID: `{r['id']}` — Yoast: `{r['yoast_title']}` | H1: `{r['h1'][:50]}` | Slug: `{r['slug']}`\n"

output += f"\n**Total Missing Milton:** {len(missing_milton)}\n\n"

output += "## Non-Service Published Pages\n\n"
for r in registry:
    if 325 not in r.get("page_category", []) and r["status"] == "publish":
        output += f"- **{r['title']}** — ID: `{r['id']}` — Slug: `{r['slug']}`\n"

with open("/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/tonicphysio.com/post-registry.md", "w") as f:
    f.write(output)

print(f"Registry built: {len(registry)} pages")
print(f"Service pages (cat 325): {service_count}")
print(f"Pages missing Milton: {len(missing_milton)}")
