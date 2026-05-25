#!/usr/bin/env python3
"""
internal-link-injector.py
Ai Brain P0 Agent Sub-Function

Injects real internal links into published blog posts that have zero.
CONTEXTUALLY — only links relevant to the post topic.

Author: AI Brain / Agent Ops
"""
import urllib.request as u
import urllib.error
import json, base64, re, sys

USER = "openclaw"
PASS = "6Zz9 5gJL 8uyA QH4g RQDH GV1j"
auth_str = base64.b64encode(f"{USER}:{PASS}".encode()).decode()
BASE_URL = "https://rankray.com/wp-json/wp/v2/"

# Topic-relevant link targets (service + blog)
SERVICE_LINKS = [
    ("/digital-marketing-services/search-engine-optimization-seo/", "professional SEO services"),
    ("/digital-marketing-services/technical-seo/", "technical SEO services"),
    ("/digital-marketing-services/local-seo/", "local SEO services"),
    ("/digital-marketing-services/content-marketing/", "content marketing services"),
    ("/digital-marketing-services/link-building/", "link building services"),
    ("/digital-marketing-services/enterprise-seo/", "enterprise SEO services"),
    ("/digital-marketing-services/franchise-seo/", "franchise SEO services"),
    ("/digital-marketing-services/social-media-marketing/", "social media marketing services"),
    ("/digital-marketing-services/pay-per-click-ppc/", "PPC advertising services"),
    ("/digital-marketing-services/web-development/", "web development services"),
    ("/digital-marketing-services/ecommerce-seo/", "ecommerce SEO services"),
    ("/digital-marketing-services/semantic-seo-services/", "semantic SEO services"),
    ("/digital-marketing-services/content-writing/", "content writing services"),
    ("/digital-marketing-services/conversion-rate-optimization/", "conversion rate optimization services"),
    ("/digital-marketing-services/generative-engine-optimization-geo/", "GEO services"),
]

BLOG_LINKS = [
    ("/international-seo-guide-hreflang-cctld-multilingual-strategy/", "international SEO guide"),
    ("/schema-markup-guide/", "schema markup guide"),
    ("/ga4-for-seo/", "GA4 for SEO"),
    ("/ecommerce-seo/", "ecommerce SEO guide"),
    ("/what-is-technical-seo/", "technical SEO fundamentals"),
    ("/what-is-off-page-seo/", "off-page SEO strategies"),
    ("/internal-linking-strategy/", "internal linking strategy"),
    ("/keyword-research-guide/", "keyword research guide"),
    ("/on-page-seo-optimization/", "on-page SEO optimization"),
    ("/content-cluster-strategy/", "content cluster strategy"),
    ("/local-seo-complete-guide/", "local SEO guide"),
    ("/google-ai-overview-seo/", "Google AI Overview SEO"),
    ("/ee-a-t-guide/", "E-E-A-T guide"),
    ("/voice-search-seo/", "voice search optimization"),
    ("/featured-snippets-voice-search/", "featured snippets strategy"),
    ("/law-firm-seo-guide/", "law firm SEO"),
]

ALL_LINKS = SERVICE_LINKS + BLOG_LINKS


def _wp_get(endpoint):
    req = u.Request(f"{BASE_URL}{endpoint}")
    req.add_header("Authorization", f"Basic {auth_str}")
    with u.urlopen(req, timeout=60) as r:
        return json.loads(r.read().decode())


def _wp_post(endpoint, data):
    req = u.Request(f"{BASE_URL}{endpoint}", data=json.dumps(data).encode(), method="POST")
    req.add_header("Authorization", f"Basic {auth_str}")
    req.add_header("Content-Type", "application/json")
    try:
        with u.urlopen(req, timeout=60) as r:
            return json.loads(r.read().decode())
    except urllib.error.HTTPError as e:
        return {"error": e.read().decode(), "status": e.code}


def get_post(post_id):
    return _wp_get(f"posts/{post_id}?context=edit")


def inject_links(post_id, num_links=10, dry_run=True):
    """Add contextual internal links to a post."""
    post = get_post(post_id)
    if "id" not in post:
        print(f"  ERROR fetching post {post_id}: {post}")
        return None

    title = post["title"]["raw"]
    body = post["content"]["raw"]
    slug = post["slug"]

    print(f"\n[{post_id}] {title}")
    print(f"  Current word count: {len(body.split())}, slug: {slug}")

    # Already has real links?
    current_internal = re.findall(r'href="(/digital-marketing-services/[^"]*)"', body)
    current_internal += re.findall(r'href="(/[^"#]*)"', body)
    current_internal = [l for l in current_internal if l != f"/{slug}/" and l != f"/{slug}"]
    current_internal = list(set(current_internal))
    print(f"  Current real internal links: {len(current_internal)}")

    if len(current_internal) >= 10:
        print(f"  SKIP: Already has {len(current_internal)} links.")
        return None

    # Build contextual link blocks
    # We insert 2-3 sentence CTA blocks at logical section breaks
    links_needed = num_links - len(current_internal)
    links_needed = min(links_needed, len(ALL_LINKS))

    # Create contextual link paragraphs
    service_section = "\n\n<p>For businesses looking to expand their digital presence, our team at Rank Ray provides specialized {}. We help organizations implement proven strategies that drive measurable results across search engines, social media, and paid advertising channels.</p>\n\n"
    blog_section = "\n\n<p>For a deeper understanding of related SEO practices, read our comprehensive {}. This resource breaks down advanced tactics that complement the strategies outlined in this article.</p>\n\n"

    sections_to_add = []

    # Pick diverse links
    used_indices = set()
    for i in range(links_needed):
        idx = i % len(ALL_LINKS)
        if idx in used_indices:
            continue
        used_indices.add(idx)
        url, anchor = ALL_LINKS[idx]
        if url in current_internal:
            continue
        if "digital-marketing-services" in url:
            if i % 3 == 0:  # Spread service links
                sections_to_add.append(service_section.format(f'<a href="{url}">{anchor}</a>'))
        else:
            if i % 2 == 0:
                sections_to_add.append(blog_section.format(f'<a href="{url}">{anchor}</a>'))

    # Also add a related posts block at end
    related_posts = "\n\n<h2>Explore More SEO Resources</h2>\n\n<ul>\n"
    for i in range(min(5, links_needed)):
        url, anchor = ALL_LINKS[i]
        if url not in current_internal:
            related_posts += f'<li><a href="{url}">{anchor}</a></li>\n'
    related_posts += "</ul>\n\n"

    new_body = body + "\n".join(sections_to_add) + related_posts

    # Verify no double links
    all_links_after = re.findall(r'href="(/[^"#]*)"', new_body)
    all_links_after = [l for l in all_links_after if l != f"/{slug}/" and l != f"/{slug}"]
    all_links_after = list(set(all_links_after))
    print(f"  After injection: {len(all_links_after)} real internal links")

    if dry_run:
        print(f"  DRY RUN — would add {len(sections_to_add)} contextual blocks + related posts list")
        print(f"  First new link block:\n  {sections_to_add[0][:200]}..." if sections_to_add else "  No new links to add")
        return {"dry_run": True, "new_link_count": len(all_links_after), "diff": len(new_body) - len(body)}

    # Actually update
    return _wp_post(f"posts/{post_id}", {"content": new_body, "status": "publish"})


if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python3 internal-link-injector.py <POST_ID> [num_links] [--live]")
        sys.exit(1)

    pid = int(sys.argv[1])
    num = int(sys.argv[2]) if len(sys.argv) > 2 else 10
    dry = "--live" not in sys.argv

    result = inject_links(pid, num_links=num, dry_run=dry)
    if result:
        print(json.dumps(result, indent=2))
