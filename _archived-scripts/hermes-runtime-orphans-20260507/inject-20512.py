#!/usr/bin/env python3
import urllib.request as u
import urllib.error
import json, base64, re

USER = "openclaw"
PASS = "6Zz9 5gJL 8uyA QH4g RQDH GV1j"
auth = base64.b64encode(f"{USER}:{PASS}".encode()).decode()

def get_post(pid):
    req = u.Request(f"https://rankray.com/wp-json/wp/v2/posts/{pid}?context=edit")
    req.add_header("Authorization", f"Basic {auth}")
    with u.urlopen(req, timeout=30) as r:
        return json.loads(r.read().decode())

def update_post(pid, body):
    data = json.dumps({"content": body, "status": "publish"}).encode()
    req = u.Request(f"https://rankray.com/wp-json/wp/v2/posts/{pid}", data=data, method="POST")
    req.add_header("Authorization", f"Basic {auth}")
    req.add_header("Content-Type", "application/json")
    try:
        with u.urlopen(req, timeout=60) as r:
            return json.loads(r.read().decode())
    except urllib.error.HTTPError as e:
        return {"error": e.read().decode()}

# Post 20512: Internal Linking
post = get_post(20512)
body = post["content"]["raw"]
links = [
    ("/digital-marketing-services/seo-audit-services/", "SEO audit services"),
    ("/digital-marketing-services/link-building/", "link building services"),
    ("/digital-marketing-services/content-marketing/", "content marketing services"),
    ("/digital-marketing-services/search-engine-optimization-seo/", "SEO services"),
    ("/technical-seo-guide/", "technical SEO fundamentals"),
    ("/content-cluster-strategy/", "content cluster strategy"),
    ("/schema-markup-guide/", "schema markup guide"),
    ("/site-architecture-seo/", "site architecture for SEO"),
    ("/ecommerce-seo/", "ecommerce SEO tips"),
    ("/ee-a-t-guide/", "E-E-A-T guide"),
]
svc = [l for l in links if "digital-marketing-services" in l[0]]
blog = [l for l in links if "digital-marketing-services" not in l[0]]

block = ""
if svc:
    anchors = [f'\u003ca href="{url}"\u003e{anchor}\u003c/a\u003e' for url, anchor in svc]
    block += f'\n\n\u003cp\u003eRank Ray provides specialized {", ".join(anchors)} designed to improve your search visibility. Our team implements proven strategies that drive measurable organic growth.\u003c/p\u003e'
if blog:
    block += '\n\n\u003ch2\u003eRelated Resources\u003c/h2\u003e\n\u003cul\u003e'
    for url, anchor in blog:
        block += f'\n\u003cli\u003e\u003ca href="{url}"\u003e{anchor}\u003c/a\u003e\u003c/li\u003e'
    block += '\n\u003c/ul\u003e'

slug = "internal-linking-strategy-seo-link-equity-distribution"
if "\u003ch2\u003eConclusion\u003c/h2\u003e" in body:
    body = body.replace("\u003ch2\u003eConclusion\u003c/h2\u003e", block + "\n\n\u003ch2\u003eConclusion\u003c/h2\u003e")
else:
    body = body + block

new_links = re.findall(r'href="(/[^"#]*)"', body)
new_links = [l for l in new_links if l != f"/{slug}/" and l != f"/{slug}"]
new_links = list(set(new_links))
print(f"[{post['id']}] {post['title']['raw'][:45]} | BEFORE: 0 | AFTER: {len(new_links)}")

result = update_post(20512, body)
print(f"  {'SUCCESS' if 'id' in result else 'ERROR: ' + result.get('error','unknown')[:60]}")
