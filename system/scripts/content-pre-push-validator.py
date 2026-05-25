#!/usr/bin/env python3
"""
content-pre-push-validator.py
Ai Brain P0 Agent Sub-Function

Reads WordPress post content (HTML string) and runs all 14 pre-push checks.
CRITICAL: Table of Contents anchors (#section-name) on the SAME post are NOT
real internal links. They are excluded from the count.

NEW (2026-05-08):
- Checks categories assigned (FAIL if default "Topics" [1])
- Checks brand in meta description
- Precise char count warnings (tells you how many chars to trim)

Returns structured JSON. Does NOT auto-fix.

Usage:
    python3 content-pre-push-validator.py "<html>" [slug] [site] [yoast_title] [yoast_desc] [yoast_focuskw] [categories]

    OR import:
        from content_pre_push_validator import validate_content
        result = validate_content(html_str, slug="my-post", site="rankray", yoast_title="...", categories=[450, 449])

Author: AI Brain / Agent Ops
Last Updated: 2026-05-08 (categories + brand check + precise char count added)
"""
import re, sys, json

# ── Config ──────────────────────────────────────────────────────────────
MIN_WORDS = 2000
# MIN_INTERNAL_LINKS removed — no forced quotas. Quality over quantity.
LINK_QUALITY_GUIDELINE = 3  # Warn if fewer than this, but never block
META_TITLE_MAX = 60
META_DESC_MAX = 160
EM_DASH_CHARS = ['\u2014', '\u2013']
FILLER_PATTERNS = [
    r"In today['′]s digital landscape",
    r"It is important to note",
    r"In the world of",
    r"It goes without saying",
    r"As we all know",
    r"At the end of the day",
    r"Needless to say",
    r"In this blog post",
    r"In this article",
    r"In today's competitive market",
    r"In the ever-evolving world of",
]
RAW_MARKDOWN_PATTERNS = [
    r'^##\s+',
    r'^\*\*(.+?)\*\*',
    r'^-\s+',
    r'^\d+\.\s+',
    r'\[.+?\]\(.+?\)',
]


# ── Core ────────────────────────────────────────────────────────────────
def _strip_tags(html_content):
    clean = re.sub(r'<[^\u003e]+>', ' ', html_content)
    clean = re.sub(r'\s+', ' ', clean).strip()
    return clean


def _count_links(html_content, current_slug=None):
    """
    Extract links and separate real internal links from TOC anchors.
    Handles BOTH relative (/path) AND absolute (https://site.com/path) URLs.
    Returns: {
        total_href_count: N,
        real_internal_links: N,   # excludes self-page #anchors
        self_anchors: N,          # #anchors on current page
        all_hrefs: [...]
    }
    """
    from urllib.parse import urlparse
    # Match ALL href values: relative, absolute, with or without hash
    all_hrefs_found = re.findall(r'<a[^>]*href="([^"]*)"[^>]*>', html_content)

    self_anchors = []
    real_internal = []
    all_hrefs = []

    for href in all_hrefs_found:
        href = href.strip()
        all_hrefs.append(href)
        
        # Parse the URL
        parsed = urlparse(href)
        
        # Skip external links (not rankray.com)
        if parsed.netloc and 'rankray.com' not in parsed.netloc:
            continue
            
        # Get the path (remove domain if present)
        path = parsed.path if parsed.path else href
        if not path.startswith('/'):
            path = '/' + path
            
        path_clean = path.rstrip('/')
        
        # Check for hash anchor
        if parsed.fragment:
            # If this anchor is on the current post page, it's a TOC anchor
            if current_slug and path_clean.rstrip('/').endswith(current_slug):
                self_anchors.append(href)
            elif not path_clean or path_clean == '/':
                self_anchors.append(href)
            else:
                real_internal.append(href)
        else:
            real_internal.append(href)

    return {
        "total_href_count": len(set(all_hrefs)),
        "real_internal_links": len(set(real_internal)),
        "self_anchors": len(set(self_anchors)),
        "real_internal_urls": list(set(real_internal)),
        "self_anchor_urls": list(set(self_anchors)),
    }


def validate_content(html_content: str, slug: str = None, site="rankray", yoast_title="", yoast_desc="", yoast_focuskw="", status="draft", categories=None):
    text = _strip_tags(html_content)
    lines = html_content.split('\n')
    words = text.split()
    word_count = len(words)

    issues = []
    warnings = []
    info = []

    # ── 1. No <h1> in body ──────────────────────────────────────────────
    h1_tags = re.findall(r'<h1[^\u003e]*>(.*?)</h1>', html_content, re.IGNORECASE | re.DOTALL)
    if h1_tags:
        issues.append({"check": "no_h1_in_body", "status": "FAIL",
            "message": f"Found {len(h1_tags)} <h1>(s). WordPress title is the only H1.",
            "details": [t[:80].strip() for t in h1_tags][:3]})
    else:
        info.append({"check": "no_h1_in_body", "status": "PASS"})

    # ── 2. Word count >= MIN ───────────────────────────────────────────
    if word_count < MIN_WORDS:
        issues.append({"check": "word_count", "status": "FAIL",
            "message": f"Word count: {word_count}. Required: >={MIN_WORDS}",
            "details": {"current": word_count, "required": MIN_WORDS}})
    else:
        info.append({"check": "word_count", "status": "PASS", "value": word_count})

    # ── 3. Real internal links — quality check, no quantity quota ─────────
    link_data = _count_links(html_content, current_slug=slug)
    real_count = link_data["real_internal_links"]

    if real_count == 0:
        warnings.append({"check": "internal_links", "status": "WARN",
            "message": "Zero real internal links. Consider adding 2-4 contextually relevant links to related content on this site.",
            "details": {"real_links_found": real_count, "self_anchors_excluded": link_data["self_anchors"]}})
    elif real_count < LINK_QUALITY_GUIDELINE:
        warnings.append({"check": "internal_links", "status": "WARN",
            "message": f"Only {real_count} real internal link(s). For topical authority, add links where this topic naturally connects to existing content.",
            "details": {"real_links_found": real_count, "self_anchors_excluded": link_data["self_anchors"],
                        "real_urls_found": link_data["real_internal_urls"]}})
    else:
        info.append({"check": "internal_links", "status": "PASS", "value": real_count,
            "excluded_self_anchors": link_data["self_anchors"],
            "urls": link_data["real_internal_urls"]})

    # Check 3b: Duplicate internal URLs
    seen = {}
    dupes = []
    for url in link_data["real_internal_urls"]:
        if url in seen:
            dupes.append(url)
        seen[url] = True
    if dupes:
        warnings.append({"check": "duplicate_internal_links", "status": "WARN",
            "message": f"{len(set(dupes))} duplicate internal link(s) found. Remove duplicates.",
            "details": list(set(dupes))})

    # ── 4. Yoast focus keyword ──────────────────────────────────────────
    if not yoast_focuskw or not yoast_focuskw.strip():
        issues.append({"check": "yoast_focuskw", "status": "FAIL", "message": "Yoast focus keyword not set."})
    else:
        info.append({"check": "yoast_focuskw", "status": "PASS", "value": yoast_focuskw})

    # ── 5. Meta title < 60 ─────────────────────────────────────────────
    if not yoast_title:
        issues.append({"check": "yoast_title", "status": "FAIL", "message": "Yoast meta title not set."})
    elif len(yoast_title) > META_TITLE_MAX:
        issues.append({"check": "yoast_title", "status": "FAIL",
            "message": f"Meta title: {len(yoast_title)} chars. Max: {META_TITLE_MAX}. Trim {len(yoast_title) - META_TITLE_MAX} chars.",
            "details": {"current": len(yoast_title), "max": META_TITLE_MAX, "value": yoast_title}})
    else:
        info.append({"check": "yoast_title", "status": "PASS", "value": yoast_title, "length": len(yoast_title)})

    # ── 6. Meta desc < 160 ─────────────────────────────────────────────
    if not yoast_desc:
        issues.append({"check": "yoast_metadesc", "status": "FAIL", "message": "Yoast meta description not set."})
    elif len(yoast_desc) > META_DESC_MAX:
        issues.append({"check": "yoast_metadesc", "status": "FAIL",
            "message": f"Meta desc: {len(yoast_desc)} chars. Max: {META_DESC_MAX}. Trim {len(yoast_desc) - META_DESC_MAX} chars.",
            "details": {"current": len(yoast_desc), "max": META_DESC_MAX, "value": yoast_desc}})
    else:
        info.append({"check": "yoast_metadesc", "status": "PASS", "value": yoast_desc, "length": len(yoast_desc)})

    # ── 6b. Brand in meta desc ──────────────────────────────────────────
    BRAND_NAMES = ["rank ray", "rankray"]
    if yoast_desc and not any(b in yoast_desc.lower() for b in BRAND_NAMES):
        warnings.append({"check": "brand_in_metadesc", "status": "WARN",
            "message": "Yoast meta description missing brand name (Rank Ray / RankRay).",
            "details": {"current_desc": yoast_desc}})
    elif yoast_desc:
        info.append({"check": "brand_in_metadesc", "status": "PASS"})

    # ── 6c. Categories assigned ──────────────────────────────────────────
    # categories parameter = list of category IDs. Default WordPress "Topics" = [1]
    if categories and 1 in categories:
        issues.append({"check": "categories", "status": "FAIL",
            "message": "Categories contain default 'Topics' [1]. Assign real categories from live API.",
            "details": {"current": categories, "fix": "Fetch live categories, remove [1], assign relevant"}})
    elif not categories or len(categories) == 0:
        warnings.append({"check": "categories", "status": "WARN",
            "message": "No categories assigned. Post will default to WordPress catch-all.",
            "details": {"fix": "Fetch live categories, assign 1-2 relevant IDs"}})
    else:
        info.append({"check": "categories", "status": "PASS", "value": categories})

    # ── 7. Em-dashes ──────────────────────────────────────────────────
    em_count = sum(html_content.count(ch) for ch in EM_DASH_CHARS)
    if em_count > 0:
        locs = [{"line": i, "context": ln.strip()[:120]} for i, ln in enumerate(lines, 1)
                for ch in EM_DASH_CHARS if ch in ln]
        if site == "tonicphysio":
            issues.append({"check": "em_dashes", "status": "FAIL",
                "message": f"{em_count} em/en dash(es). TonicPhysio: strictly prohibited.",
                "details": locs[:5]})
        else:
            warnings.append({"check": "em_dashes", "status": "WARN",
                "message": f"{em_count} em/en dash(es). Consider replacing.",
                "details": locs[:5]})
    else:
        info.append({"check": "em_dashes", "status": "PASS"})

    # ── 8. No repeated words ───────────────────────────────────────────
    pat = re.compile(r'(\b\w{4,}\b)\s+\1', re.IGNORECASE)
    repeats = list(set(pat.findall(html_content)))
    if repeats:
        issues.append({"check": "repeated_words", "status": "FAIL",
            "message": f"{len(repeats)} repeated word pattern(s).",
            "details": repeats[:5]})
    else:
        info.append({"check": "repeated_words", "status": "PASS"})

    # ── 9. No raw markdown ─────────────────────────────────────────────
    rm = []
    for p in RAW_MARKDOWN_PATTERNS:
        m = re.findall(p, html_content, re.MULTILINE)
        if m:
            rm.append({"pattern": p, "count": len(m), "example": str(m[0])[:80]})
    if rm:
        issues.append({"check": "markdown_converted", "status": "FAIL",
            "message": f"Raw markdown found: {len(rm)} pattern type(s). Convert to HTML.",
            "details": rm})
    else:
        info.append({"check": "markdown_converted", "status": "PASS"})

    # ── 10. Heading hierarchy ─────────────────────────────────────────────
    headings = re.findall(r'<h([2-6])[^\u003e]*>', html_content)
    if headings:
        first = int(headings[0])
        skipped = []
        prev = first
        for h in headings:
            h = int(h)
            if h > prev + 1:
                skipped.append(f"H{prev}→H{h}")
            prev = h
        if skipped:
            issues.append({"check": "heading_hierarchy", "status": "FAIL",
                "message": "Heading hierarchy broken (missing levels).",
                "details": skipped})
        elif first != 2:
            warnings.append({"check": "heading_hierarchy", "status": "WARN",
                "message": f"First heading is H{first}. Body should start with <H2> or <p>."})
        else:
            info.append({"check": "heading_hierarchy", "status": "PASS"})
    else:
        warnings.append({"check": "heading_hierarchy", "status": "WARN",
            "message": "No headings found. Ensure H2 sections exist."})

    # ── 11. Filler intros ─────────────────────────────────────────────────
    filler = [p for p in FILLER_PATTERNS if re.search(p, text, re.IGNORECASE)]
    if filler:
        warnings.append({"check": "filler_intros", "status": "WARN",
            "message": f"{len(filler)} filler intro(s) found.",
            "details": filler})

    # ── 12. Emoji detection ──────────────────────────────────────────────
    emoji_p = re.compile(
        r'[\U0001F600-\U0001F64F\U0001F300-\U0001F5FF\U0001F680-\U0001F6FF'
        r'\U0001F700-\U0001F77F\U0001F780-\U0001F7FF\U0001F800-\U0001F8FF'
        r'\U0001F900-\U0001F9FF\U0001FA00-\U0001FA6F\U0001FA70-\U0001FAFF'
        r'\U00002702-\U000027B0\U000024C2-\U0001F251]+')
    emojis = emoji_p.findall(html_content)
    if emojis:
        issues.append({"check": "no_emojis", "status": "FAIL",
            "message": f"{len(emojis)} emoji(s). User prohibits emojis.",
            "details": emojis[:5]})
    else:
        info.append({"check": "no_emojis", "status": "PASS"})

    # ── 13. Double dashes ───────────────────────────────────────────────
    dd = html_content.count('--')
    if dd > 0:
        warnings.append({"check": "double_dashes", "status": "WARN",
            "message": f"{dd} double dash sequence(s). Use proper punctuation."})

    # ── Summary ─────────────────────────────────────────────────────────
    can_push = (len(issues) == 0)

    return {
        "can_push": can_push,
        "total_checks": len(info) + len(warnings) + len(issues),
        "passed": len(info),
        "warnings": len(warnings),
        "issues": len(issues),
        "summary": {
            "word_count": word_count,
            "real_internal_links": real_count,
            "self_anchors_excluded": link_data["self_anchors"],
            "yoast_focuskw_set": bool(yoast_focuskw.strip()),
            "yoast_title_length": len(yoast_title) if yoast_title else 0,
            "yoast_desc_length": len(yoast_desc) if yoast_desc else 0,
            "em_dashes": em_count,
            "raw_markdown_found": bool(rm),
            "repeated_words": len(repeats) if repeats else 0,
            "filler_intros": len(filler),
            "emojis": len(emojis) if emojis else 0,
            "status_check": status,
        },
        "issues": issues,
        "warnings": warnings,
        "info": info,
    }


# ── CLI ─────────────────────────────────────────────────────────────────
if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage:", sys.argv[0], "'\u003chtml-string\u003e' [slug] [site] [yoast_title] [yoast_desc] [yoast_focuskw]")
        sys.exit(1)

    html_s = sys.argv[1]
    slug = sys.argv[2] if len(sys.argv) > 2 else None
    site = sys.argv[3] if len(sys.argv) > 3 else "rankray"
    yt = sys.argv[4] if len(sys.argv) > 4 else ""
    yd = sys.argv[5] if len(sys.argv) > 5 else ""
    yf = sys.argv[6] if len(sys.argv) > 6 else ""
    cats_raw = sys.argv[7] if len(sys.argv) > 7 else ""
    cats = [int(x) for x in cats_raw.split(",") if x.strip().isdigit()] if cats_raw else []

    r = validate_content(html_s, slug=slug, site=site, yoast_title=yt, yoast_desc=yd, yoast_focuskw=yf, categories=cats)
    print(json.dumps(r, indent=2))
