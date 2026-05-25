#!/usr/bin/env python3
"""
Ai Brain — SEO Content Validator (Unified)
Single script for ALL content pre-push + audit tasks.
No auto-fixes. Detection + context only. Returns structured JSON.

Usage:
  python3 content-validator.py audit-content \
    --site https://rankray.com \
    --html-file article.html \
    --topic "GEO Strategy 2027"

  python3 content-validator.py audit-site \
    --site https://rankray.com \
    --output report.json

  python3 content-validator.py check-topic \
    --site https://rankray.com \
    --topic "Enterprise Digital Marketing"

  python3 content-validator.py check-media \
    --site https://rankray.com \
    --proposed-topic "AI Automation"

  python3 content-validator.py check-links \
    --site https://rankray.com \
    --html-file article.html
"""

import argparse, json, re, sys, os, urllib.request as u
from urllib.parse import urljoin, urlparse
from html.parser import HTMLParser

# ── CONFIG ─────────────────────────────────────────────────────────

RULES_PATH = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/content/content-rules.md"
BANNED_DOMAINS = ["discoveredlabs.com", "arxiv.org"]
META_KEY_FOCUS = "_yoast_wpseo_focuskw"
META_KEY_TITLE = "_yoast_wpseo_title"
META_KEY_DESC = "_yoast_wpseo_metadesc"

# ── WP API HELPERS ─────────────────────────────────────────────────

def wp_get_all_posts(site, per_page=100, user=None, app_pass=None):
    """Fetch all published posts with embedded meta and media."""
    posts = []
    page = 1
    auth_header = None
    if user and app_pass:
        import base64
        auth_header = f"Basic {base64.b64encode(f'{user}:{app_pass}'.encode()).decode()}"

    while True:
        url = f"{site}/wp-json/wp/v2/posts?per_page={per_page}&page={page}&_embed=wp:featuredmedia&status=publish"
        req = u.Request(url)
        req.add_header("User-Agent", "AiBrain-ContentValidator/1.0")
        if auth_header:
            req.add_header("Authorization", auth_header)
        try:
            with u.urlopen(req, timeout=30) as r:
                batch = json.loads(r.read().decode())
                if not batch:
                    break
                posts.extend(batch)
                if len(batch) < per_page:
                    break
                page += 1
        except Exception as e:
            break
    return posts

def wp_get_all_media(site, per_page=100, user=None, app_pass=None):
    """Fetch all media library items."""
    media = []
    page = 1
    auth_header = None
    if user and app_pass:
        import base64
        auth_header = f"Basic {base64.b64encode(f'{user}:{app_pass}'.encode()).decode()}"

    while True:
        url = f"{site}/wp-json/wp/v2/media?per_page={per_page}&page={page}"
        req = u.Request(url)
        req.add_header("User-Agent", "AiBrain-ContentValidator/1.0")
        if auth_header:
            req.add_header("Authorization", auth_header)
        try:
            with u.urlopen(req, timeout=30) as r:
                batch = json.loads(r.read().decode())
                if not batch:
                    break
                media.extend(batch)
                if len(batch) < per_page:
                    break
                page += 1
        except Exception as e:
            break
    return media

def fetch_url_status(url):
    """HEAD request to check URL status."""
    try:
        req = u.Request(url, method="HEAD")
        req.add_header("User-Agent", "AiBrain-ContentValidator/1.0")
        with u.urlopen(req, timeout=15) as r:
            return r.status
    except Exception as e:
        return f"ERROR: {e}"

# ── HTML ANALYSIS ──────────────────────────────────────────────────

class HTMLAnalyzer(HTMLParser):
    def __init__(self):
        super().__init__()
        self.h1_count = 0
        self.h2_count = 0
        self.h3_count = 0
        self.p_count = 0
        self.img_count = 0
        self.internal_links = []
        self.external_links = []
        self.raw_text = []
        self.in_body = False
        self.current_tag = None

    def handle_starttag(self, tag, attrs):
        self.current_tag = tag
        if tag == "h1":
            self.h1_count += 1
        elif tag == "h2":
            self.h2_count += 1
        elif tag == "h3":
            self.h3_count += 1
        elif tag == "p":
            self.p_count += 1
        elif tag == "img":
            self.img_count += 1
        elif tag == "a":
            attrs_dict = dict(attrs)
            href = attrs_dict.get("href", "")
            if href:
                self._classify_link(href)

    def handle_data(self, data):
        self.raw_text.append(data)

    def _classify_link(self, href):
        if href.startswith("http"):
            self.external_links.append(href)
        elif href.startswith("/") or href.startswith("./") or href.startswith("../"):
            self.internal_links.append(href)
        elif not href.startswith("#") and not href.startswith("mailto:"):
            self.internal_links.append(href)

    def get_text(self):
        return " ".join(self.raw_text)

# ── CHECK FUNCTIONS ────────────────────────────────────────────────

def check_no_h1(analyzer):
    return {
        "check": "no_h1_in_body",
        "status": "PASS" if analyzer.h1_count == 0 else "FAIL",
        "h1_count": analyzer.h1_count,
        "message": "No H1 tags found" if analyzer.h1_count == 0 else f"Found {analyzer.h1_count} H1 tag(s) in body — WordPress title is the only H1"
    }

def check_word_count(analyzer, min_words=2000):
    text = analyzer.get_text()
    words = len(text.split())
    return {
        "check": "word_count",
        "status": "PASS" if words >= min_words else "FAIL",
        "word_count": words,
        "minimum": min_words,
        "message": f"{words} words (min: {min_words})" if words >= min_words else f"Only {words} words — minimum is {min_words}"
    }

def check_internal_links(analyzer, html_content, site_domain, min_links=10):
    # Also extract from raw markdown in case HTML is not fully parsed
    all_links = set(analyzer.internal_links)
    # Find markdown-style links [text](url)
    md_links = re.findall(r'\[([^\]]+)\]\(([^)]+)\)', html_content)
    for _, href in md_links:
        if not href.startswith("http") and not href.startswith("#"):
            all_links.add(href)
    # Find raw hrefs
    raw_hrefs = re.findall(r'href=["\']([^"\']+)["\']', html_content)
    for href in raw_hrefs:
        if not href.startswith("http") and not href.startswith("#") and not href.startswith("mailto:"):
            all_links.add(href)

    return {
        "check": "internal_links",
        "status": "PASS" if len(all_links) >= min_links else "FAIL",
        "link_count": len(all_links),
        "minimum": min_links,
        "links": sorted(list(all_links))[:20],
        "message": f"{len(all_links)} internal links (min: {min_links})"
    }

def check_heading_hierarchy(analyzer):
    issues = []
    if analyzer.h2_count == 0:
        issues.append("No H2 sections found")
    if analyzer.h2_count < 6:
        issues.append(f"Only {analyzer.h2_count} H2 sections (recommend 6-10)")
    return {
        "check": "heading_hierarchy",
        "status": "PASS" if not issues else "WARN",
        "h2_count": analyzer.h2_count,
        "h3_count": analyzer.h3_count,
        "issues": issues,
        "message": "Good heading structure" if not issues else "; ".join(issues)
    }

def check_em_dashes(text):
    em_count = text.count('\u2014')
    locations = []
    if em_count > 0:
        for i, line in enumerate(text.split('\n'), 1):
            if '\u2014' in line:
                locations.append({"line": i, "context": line.strip()[:100]})
    return {
        "check": "no_em_dashes",
        "status": "PASS" if em_count == 0 else "FAIL",
        "em_dash_count": em_count,
        "locations": locations[:5],
        "message": "No em-dashes found" if em_count == 0 else f"{em_count} em-dash(es) found"
    }

def check_repeated_words(text):
    words = re.findall(r'\b[a-zA-Z]+\b', text.lower())
    repeated = []
    for i in range(len(words) - 1):
        if words[i] == words[i+1] and len(words[i]) > 3:
            repeated.append({"word": words[i], "position": i})
    return {
        "check": "no_repeated_words",
        "status": "PASS" if len(repeated) == 0 else "FAIL",
        "repeated_count": len(repeated),
        "examples": repeated[:5],
        "message": "No repeated words" if not repeated else f"{len(repeated)} repeated word instances"
    }

def check_markdown_remains(html_content):
    md_patterns = [
        (r'^#{1,6} ', "Markdown heading"),
        (r'\*\*', "Bold markdown"),
        (r'^\* ', "Bullet list"),
        (r'^\d+\. ', "Numbered list"),
        (r'`[^`]+`', "Inline code"),
    ]
    findings = []
    lines = html_content.split('\n')
    for i, line in enumerate(lines, 1):
        for pattern, name in md_patterns:
            if re.search(pattern, line):
                findings.append({"line": i, "type": name, "context": line.strip()[:80]})
    return {
        "check": "no_raw_markdown",
        "status": "PASS" if not findings else "FAIL",
        "findings": findings[:10],
        "message": "No raw markdown" if not findings else f"{len(findings)} raw markdown elements found"
    }

def check_yoast_fields(meta):
    checks = []
    focus = meta.get(META_KEY_FOCUS, "")
    title = meta.get(META_KEY_TITLE, "")
    desc = meta.get(META_KEY_DESC, "")

    checks.append({
        "field": "focus_keyword",
        "status": "PASS" if focus else "FAIL",
        "value": focus,
        "message": "Focus keyword set" if focus else "Focus keyword MISSING"
    })
    checks.append({
        "field": "meta_title",
        "status": "PASS" if title and len(title) < 60 else "FAIL",
        "value": title,
        "length": len(title) if title else 0,
        "message": f"Title: {len(title)} chars" if title and len(title) < 60 else f"Title MISSING or too long ({len(title)} chars)"
    })
    checks.append({
        "field": "meta_description",
        "status": "PASS" if desc and len(desc) < 160 else "FAIL",
        "value": desc,
        "length": len(desc) if desc else 0,
        "message": f"Description: {len(desc)} chars" if desc and len(desc) < 160 else f"Description MISSING or too long ({len(desc)} chars)"
    })
    all_pass = all(c["status"] == "PASS" for c in checks)
    return {
        "check": "yoast_fields",
        "status": "PASS" if all_pass else "FAIL",
        "fields": checks,
        "message": "All Yoast fields set correctly" if all_pass else "Yoast fields incomplete"
    }

def check_external_links(analyzer):
    violations = []
    for link in analyzer.external_links:
        domain = urlparse(link).netloc.lower()
        for banned in BANNED_DOMAINS:
            if banned in domain:
                violations.append({"link": link, "reason": f"Banned competitor domain: {banned}"})
    return {
        "check": "external_links",
        "status": "PASS" if not violations else "FAIL",
        "external_count": len(analyzer.external_links),
        "violations": violations,
        "message": f"{len(analyzer.external_links)} external links, no violations" if not violations else f"{len(violations)} banned competitor link(s)"
    }

def check_duplicate_topic(topic, existing_posts):
    topic_lower = topic.lower()
    topic_words = set(topic_lower.split())
    matches = []
    for post in existing_posts:
        title = post.get("title", {}).get("rendered", "").lower()
        meta = post.get("meta", {})
        focus = (meta.get(META_KEY_FOCUS, "") or "").lower()
        content = (post.get("content", {}).get("rendered", "") or "").lower()

        title_words = set(title.split())
        focus_words = set(focus.split())

        # Check exact match or high overlap
        overlap_title = len(topic_words & title_words) / max(len(topic_words), 1)
        overlap_focus = len(topic_words & focus_words) / max(len(topic_words), 1)

        if topic_lower in title or topic_lower in focus or overlap_title > 0.6 or overlap_focus > 0.6:
            matches.append({
                "id": post["id"],
                "title": post["title"]["rendered"],
                "url": post["link"],
                "focus_keyword": focus,
                "similarity": max(overlap_title, overlap_focus)
            })

    matches.sort(key=lambda x: x["similarity"], reverse=True)
    return {
        "check": "topic_duplication",
        "status": "CLEAR" if not matches else "CONFLICT",
        "matches_found": len(matches),
        "matches": matches[:5],
        "message": "Topic is unique" if not matches else f"{len(matches)} similar post(s) found"
    }

def check_media_conflict(proposed_topic, existing_media):
    """Check if media library already has images matching proposed topic."""
    topic_words = set(proposed_topic.lower().replace("-", " ").replace("_", " ").split())
    conflicts = []
    for item in existing_media:
        filename = (item.get("source_url", "") or "").lower().split("/")[-1]
        alt = (item.get("alt_text", "") or "").lower()
        title = (item.get("title", {}).get("rendered", "") or "").lower()

        filename_words = set(filename.replace("-", " ").replace("_", " ").replace(".", " ").split())
        alt_words = set(alt.split())
        title_words = set(title.split())

        combined = filename_words | alt_words | title_words
        overlap = len(topic_words & combined)
        if overlap >= max(2, len(topic_words) * 0.5):
            conflicts.append({
                "id": item["id"],
                "filename": filename,
                "alt_text": item.get("alt_text", ""),
                "url": item.get("source_url", "")
            })

    return {
        "check": "media_deduplication",
        "status": "CLEAR" if not conflicts else "CONFLICT",
        "conflicts_found": len(conflicts),
        "conflicts": conflicts[:5],
        "message": "No media conflicts" if not conflicts else f"{len(conflicts)} existing media item(s) match this topic"
    }

def check_link_validity(analyzer, site_domain):
    """Verify internal links actually resolve."""
    broken = []
    checked = 0
    for href in set(analyzer.internal_links):
        if href.startswith("#"):
            continue
        full_url = urljoin(site_domain, href)
        status = fetch_url_status(full_url)
        checked += 1
        if isinstance(status, str) or status >= 400:
            broken.append({"url": full_url, "status": status})
    return {
        "check": "link_validity",
        "status": "PASS" if not broken else "FAIL",
        "checked": checked,
        "broken_count": len(broken),
        "broken": broken[:10],
        "message": f"{checked} links checked, all valid" if not broken else f"{len(broken)} broken link(s)"
    }

# ── MAIN COMMANDS ──────────────────────────────────────────────────

def cmd_audit_content(args):
    html = args.html_content or ""
    if args.html_file:
        with open(args.html_file, 'r') as f:
            html = f.read()

    analyzer = HTMLAnalyzer()
    analyzer.feed(html)
    text = analyzer.get_text()

    report = {
        "command": "audit-content",
        "site": args.site,
        "checks": [
            check_no_h1(analyzer),
            check_word_count(analyzer),
            check_internal_links(analyzer, html, args.site),
            check_heading_hierarchy(analyzer),
            check_em_dashes(text),
            check_repeated_words(text),
            check_markdown_remains(html),
            check_external_links(analyzer),
        ],
        "overall_status": "PASS"
    }

    # Determine overall status
    for c in report["checks"]:
        if c["status"] == "FAIL":
            report["overall_status"] = "FAIL"
            break
        elif c["status"] == "WARN" and report["overall_status"] == "PASS":
            report["overall_status"] = "WARN"

    if args.site and args.topic:
        posts = wp_get_all_posts(args.site)
        report["topic_check"] = check_duplicate_topic(args.topic, posts)
        report["media_check"] = check_media_conflict(args.topic, wp_get_all_media(args.site))
        if report["topic_check"]["status"] == "CONFLICT":
            report["overall_status"] = "FAIL"

    print(json.dumps(report, indent=2))
    return report

def cmd_audit_site(args):
    posts = wp_get_all_posts(args.site)
    media = wp_get_all_media(args.site)
    results = []

    for post in posts:
        content = post.get("content", {}).get("rendered", "")
        analyzer = HTMLAnalyzer()
        analyzer.feed(content)
        text = analyzer.get_text()
        meta = post.get("meta", {})

        result = {
            "id": post["id"],
            "title": post["title"]["rendered"],
            "url": post["link"],
            "word_count": len(text.split()),
            "h1_count": analyzer.h1_count,
            "h2_count": analyzer.h2_count,
            "internal_links": len(analyzer.internal_links),
            "external_links": len(analyzer.external_links),
            "em_dashes": text.count('\u2014'),
            "yoast": check_yoast_fields(meta),
            "featured_media": post.get("featured_media", 0),
        }

        # Check if featured image has alt text
        if post.get("_embedded", {}).get("wp:featuredmedia"):
            fm = post["_embedded"]["wp:featuredmedia"][0]
            result["featured_image_alt"] = fm.get("alt_text", "") or "MISSING"
            result["featured_image_url"] = fm.get("source_url", "")
        else:
            result["featured_image_alt"] = "NO IMAGE"
            result["featured_image_url"] = ""

        results.append(result)

    report = {
        "command": "audit-site",
        "site": args.site,
        "posts_audited": len(results),
        "posts": results
    }

    if args.output:
        with open(args.output, 'w') as f:
            json.dump(report, f, indent=2)
        print(f"Report saved to {args.output}")
    else:
        print(json.dumps(report, indent=2))
    return report

def cmd_check_topic(args):
    posts = wp_get_all_posts(args.site)
    result = check_duplicate_topic(args.topic, posts)
    print(json.dumps(result, indent=2))
    return result

def cmd_check_media(args):
    media = wp_get_all_media(args.site)
    result = check_media_conflict(args.proposed_topic, media)
    print(json.dumps(result, indent=2))
    return result

def cmd_check_links(args):
    html = args.html_content or ""
    if args.html_file:
        with open(args.html_file, 'r') as f:
            html = f.read()
    analyzer = HTMLAnalyzer()
    analyzer.feed(html)
    result = check_link_validity(analyzer, args.site)
    print(json.dumps(result, indent=2))
    return result

# ── CLI ────────────────────────────────────────────────────────────

def main():
    parser = argparse.ArgumentParser(description="SEO Content Validator")
    sub = parser.add_subparsers(dest="command", required=True)

    # audit-content
    ac = sub.add_parser("audit-content", help="Validate new article before push")
    ac.add_argument("--site", required=True, help="Site base URL")
    ac.add_argument("--html-file", help="Path to HTML file")
    ac.add_argument("--html-content", help="Raw HTML string")
    ac.add_argument("--topic", help="Topic to check for duplication + media conflict")

    # audit-site
    asite = sub.add_parser("audit-site", help="Audit all published posts")
    asite.add_argument("--site", required=True, help="Site base URL")
    asite.add_argument("--output", help="Output JSON file path")

    # check-topic
    ct = sub.add_parser("check-topic", help="Check if topic already covered")
    ct.add_argument("--site", required=True, help="Site base URL")
    ct.add_argument("--topic", required=True, help="Topic to check")

    # check-media
    cm = sub.add_parser("check-media", help="Check media library for conflicts")
    cm.add_argument("--site", required=True, help="Site base URL")
    cm.add_argument("--proposed-topic", required=True, help="Proposed image topic")

    # check-links
    cl = sub.add_parser("check-links", help="Validate link resolution")
    cl.add_argument("--site", required=True, help="Site base URL")
    cl.add_argument("--html-file", help="Path to HTML file")
    cl.add_argument("--html-content", help="Raw HTML string")

    args = parser.parse_args()

    if args.command == "audit-content":
        cmd_audit_content(args)
    elif args.command == "audit-site":
        cmd_audit_site(args)
    elif args.command == "check-topic":
        cmd_check_topic(args)
    elif args.command == "check-media":
        cmd_check_media(args)
    elif args.command == "check-links":
        cmd_check_links(args)

if __name__ == "__main__":
    main()
