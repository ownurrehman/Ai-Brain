#!/usr/bin/env python3
"""Batch link injector for remaining 13 zero-link posts."""
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

def count_real_links(body, slug):
    all_h = re.findall(r'href="(/[^"#]*)"', body)
    all_h += re.findall(r'href="https?://(?:www\.)?rankray\.com(/[^"]*)"', body)
    unique = []
    seen = set()
    for l in all_h:
        l = l.rstrip('/')
        if l == f'/{slug}' or l == f'/{slug}/' or l == slug:
            continue
        if l not in seen:
            unique.append(l)
            seen.add(l)
    return unique

POSTS = [
    (20470, "international-seo-hreflang-guide", [
        ("/cctld-vs-subdirectory-subdomain-international-seo/", "ccTLD vs subdirectory vs subdomain"),
        ("/digital-marketing-services/local-seo/", "local SEO services"),
        ("/digital-marketing-services/enterprise-seo/", "enterprise SEO services"),
        ("/digital-marketing-services/technical-seo/", "technical SEO services"),
        ("/schema-markup-guide/", "schema markup implementation"),
        ("/content-cluster-strategy/", "content cluster strategy"),
        ("/ee-a-t-guide/", "E-E-A-T guide"),
        ("/google-ai-overview-seo/", "Google AI Overview SEO"),
        ("/ecommerce-seo/", "ecommerce SEO guide"),
        ("/digital-marketing-services/search-engine-optimization-seo/", "SEO services"),
    ]),
    (20513, "fix-orphan-pages-recover-seo-value", [
        ("/digital-marketing-services/technical-seo/", "technical SEO services"),
        ("/digital-marketing-services/seo-audit-services/", "SEO audit services"),
        ("/digital-marketing-services/link-building/", "link building services"),
        ("/site-architecture-seo/", "site architecture SEO"),
        ("/what-is-technical-seo/", "technical SEO fundamentals"),
        ("/internal-linking-strategy/", "internal linking strategy"),
        ("/cctld-vs-subdirectory-subdomain-international-seo/", "international SEO structure"),
        ("/content-cluster-strategy/", "content cluster strategy"),
        ("/ecommerce-seo/", "ecommerce SEO guide"),
        ("/schema-markup-guide/", "schema markup guide"),
    ]),
    (20502, "looker-studio-seo-dashboard-performance-reports", [
        ("/ga4-for-seo/", "GA4 for SEO"),
        ("/digital-marketing-services/search-engine-optimization-seo/", "SEO services"),
        ("/digital-marketing-services/enterprise-seo/", "enterprise SEO services"),
        ("/digital-marketing-services/local-seo/", "local SEO services"),
        ("/schema-markup-guide/", "schema markup guide"),
        ("/content-cluster-strategy/", "content cluster strategy"),
        ("/technical-seo-guide/", "technical SEO fundamentals"),
        ("/google-ai-overview-seo/", "Google AI Overview SEO"),
        ("/ee-a-t-guide/", "E-E-A-T guide"),
        ("/ecommerce-seo/", "ecommerce SEO"),
    ]),
    (20517, "product-page-seo-optimization-guide", [
        ("/ecommerce-seo/", "ecommerce SEO guide"),
        ("/category-page-seo/", "category page SEO"),
        ("/digital-marketing-services/ecommerce-seo/", "ecommerce SEO services"),
        ("/digital-marketing-services/search-engine-optimization-seo/", "SEO services"),
        ("/schema-markup-guide/", "schema markup for products"),
        ("/technical-seo-guide/", "technical SEO fundamentals"),
        ("/content-cluster-strategy/", "content cluster strategy"),
        ("/ee-a-t-guide/", "E-E-A-T guide"),
        ("/local-seo-complete-guide/", "local SEO guide"),
        ("/digital-marketing-services/content-marketing/", "content marketing services"),
    ]),
    (20501, "ga4-seo-tracking", [
        ("/looker-studio-seo-dashboard/", "Looker Studio SEO dashboard"),
        ("/digital-marketing-services/search-engine-optimization-seo/", "SEO services"),
        ("/digital-marketing-services/enterprise-seo/", "enterprise SEO services"),
        ("/content-cluster-strategy/", "content cluster strategy"),
        ("/technical-seo-guide/", "technical SEO fundamentals"),
        ("/schema-markup-guide/", "schema markup guide"),
        ("/google-ai-overview-seo/", "Google AI Overview SEO"),
        ("/ee-a-t-guide/", "E-E-A-T guide"),
        ("/ecommerce-seo/", "ecommerce SEO"),
        ("/digital-marketing-services/local-seo/", "local SEO services"),
    ]),
    (20474, "schema-markup-guide", [
        ("/digital-marketing-services/technical-seo/", "technical SEO services"),
        ("/digital-marketing-services/semantic-seo-services/", "semantic SEO services"),
        ("/digital-marketing-services/search-engine-optimization-seo/", "SEO services"),
        ("/technical-seo-guide/", "technical SEO fundamentals"),
        ("/ecommerce-seo/", "ecommerce SEO"),
        ("/content-cluster-strategy/", "content cluster strategy"),
        ("/google-ai-overview-seo/", "Google AI Overview SEO"),
        ("/ee-a-t-guide/", "E-E-A-T guide"),
        ("/local-seo-complete-guide/", "local SEO guide"),
        ("/digital-marketing-services/content-writing/", "content writing services"),
    ]),
    (20471, "ecommerce-seo-guide", [
        ("/product-page-seo/", "product page SEO"),
        ("/category-page-seo/", "category page SEO"),
        ("/digital-marketing-services/ecommerce-seo/", "ecommerce SEO services"),
        ("/digital-marketing-services/search-engine-optimization-seo/", "SEO services"),
        ("/schema-markup-guide/", "schema markup guide"),
        ("/technical-seo-guide/", "technical SEO fundamentals"),
        ("/content-cluster-strategy/", "content cluster strategy"),
        ("/digital-marketing-services/pay-per-click-ppc/", "PPC advertising"),
        ("/digital-marketing-services/content-marketing/", "content marketing services"),
        ("/digital-marketing-services/conversion-rate-optimization/", "CRO services"),
    ]),
    (20518, "category-page-seo-ecommerce", [
        ("/ecommerce-seo/", "ecommerce SEO guide"),
        ("/product-page-seo/", "product page SEO"),
        ("/digital-marketing-services/ecommerce-seo/", "ecommerce SEO services"),
        ("/digital-marketing-services/search-engine-optimization-seo/", "SEO services"),
        ("/schema-markup-guide/", "schema markup guide"),
        ("/content-cluster-strategy/", "content cluster strategy"),
        ("/technical-seo-guide/", "technical SEO fundamentals"),
        ("/internal-linking-strategy/", "internal linking strategy"),
        ("/ee-a-t-guide/", "E-E-A-T guide"),
        ("/digital-marketing-services/content-marketing/", "content marketing services"),
    ]),
    (20514, "site-architecture-seo", [
        ("/digital-marketing-services/technical-seo/", "technical SEO services"),
        ("/digital-marketing-services/seo-audit-services/", "SEO audit services"),
        ("/digital-marketing-services/search-engine-optimization-seo/", "SEO services"),
        ("/what-is-technical-seo/", "technical SEO fundamentals"),
        ("/internal-linking-strategy/", "internal linking strategy"),
        ("/content-cluster-strategy/", "content cluster strategy"),
        ("/cctld-vs-subdirectory-subdomain-international-seo/", "international SEO domain structure"),
        ("/schema-markup-guide/", "schema markup guide"),
        ("/ecommerce-seo/", "ecommerce SEO"),
        ("/digital-marketing-services/enterprise-seo/", "enterprise SEO services"),
    ]),
]

for pid, slug, links in POSTS:
    post = get_post(pid)
    body = post["content"]["raw"]
    title = post["title"]["raw"]

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

    if "\u003ch2\u003eConclusion\u003c/h2\u003e" in body:
        body = body.replace("\u003ch2\u003eConclusion\u003c/h2\u003e", block + "\n\n\u003ch2\u003eConclusion\u003c/h2\u003e")
    elif "\u003ch2\u003eFrequently Asked Questions\u003c/h2\u003e" in body:
        body = body.replace("\u003ch2\u003eFrequently Asked Questions\u003c/h2\u003e", block + "\n\n\u003ch2\u003eFrequently Asked Questions\u003c/h2\u003e")
    else:
        body = body + block

    before = count_real_links(post["content"]["raw"], slug)
    after = count_real_links(body, slug)
    print(f"[{pid}] {title[:45]:45s} | BEFORE: {len(before):2d} | AFTER: {len(after):2d}")

    result = update_post(pid, body)
    if "id" in result:
        print(f"  SUCCESS")
    else:
        print(f"  ERROR: {result.get('error', 'unknown')[:80]}")

print("\n=== Done processing 9 new posts + 2 already done ===")
print("Remaining: 4 older thin posts (5424, 5417, 5427, 5384)")
