#!/usr/bin/env python3
"""
wp-blog-auditor.py — ULTIMATE + SEMANTIC SEO VERSION
Ai Brain P0 Agent Sub-Function

Pulls ALL blog posts from WordPress and performs deep audit.
Checks: links, Yoast, headings, images, dashes, emojis, markdown,
summary blocks, paragraph density, tables/lists, external links,
visual breaks, content structure, AND Semantic SEO:

SEMANTIC MODULES ADDED:
- Entity extraction & topic inference
- LSI keyword coverage analysis
- JSON-LD schema markup detection (Service, FAQPage, BreadcrumbList, etc.)
- Readability scoring (Flesch Reading Ease)
- Anchor text diversity analysis
- CTA & service page link detection
- FAQ structure & featured snippet targeting
- Canonical tag validation
- Information density (fluff vs data)
- Image file size audit
- Self-plagiarism (duplicate titles/descriptions)

Usage CLI:
    python3 wp-blog-auditor.py --site rankray --output audit.json
    python3 wp-blog-auditor.py --site rankray --filter low_links --min-links 5
    python3 wp-blog-auditor.py --site rankray --show-issues-only
    python3 wp-blog-auditor.py --site rankray --semantic-report

Checks: links, Yoast (focuskw/title/desc/brand), categories, headings, images,
alt text, dashes, emojis, markdown, summary blocks, paragraph density, tables/lists,
external links, visual breaks, content structure, AND Semantic SEO:
- Entity extraction & topic inference
- LSI keyword coverage analysis
- JSON-LD schema markup detection
- Readability scoring (Flesch)
- Anchor text diversity analysis
- CTA & service page link detection
- FAQ structure & featured snippet targeting
- Canonical tag validation
- Information density (fluff vs data)
- Image file size audit
- Self-plagiarism (duplicate titles/descriptions)

Author: AI Brain / Agent Ops
Last Updated: 2026-05-08 (Semantic SEO + Categories + Brand Check + Exact Char Limits)
"""
import sys, json, argparse, base64, re, urllib.parse
from urllib import request
from collections import Counter
from concurrent.futures import ThreadPoolExecutor, as_completed

# ── OPTIONAL TEXTSTAT (Readability) ────────────────────────────────────────────
try:
    import textstat
    HAS_TEXTSTAT = True
except ImportError:
    HAS_TEXTSTAT = False

# ── SITE CONFIG ──────────────────────────────────────────────────────────
SITES = {
    "rankray": {
        "url": "https://rankray.com/wp-json/wp/v2/",
        "user": "openclaw",
        "pass": "6Zz9 5gJL 8uyA QH4g RQDH GV1j",
    },
}

MIN_WORDS = 2000
# MIN_INTERNAL_LINKS removed — no forced quotas. Quality over quantity.
META_TITLE_MAX = 60
META_DESC_MAX = 160
MAX_SENTENCES_PER_PARAGRAPH = 3
MAX_WORDS_PER_PARAGRAPH = 60

# ── SEMANTIC SEO MODULES ─────────────────────────────────────────────────

# Topic → LSI term maps for quick coverage checks
LSI_TOPICS = {
    "seo": ["search engine optimization", "rankings", "organic traffic", "keywords",
            "backlinks", "on-page", "off-page", "technical seo", "search visibility",
            " SERP", "crawl", "index", "algorithm", "search intent"],
    "digital marketing": ["online marketing", "ppc", "social media", "content strategy",
                         "lead generation", "conversion rate", "ROI", "analytics",
                         "email marketing", "brand awareness", "customer journey"],
    "content": ["blog post", "pillar content", "content strategy", "editorial calendar",
                "evergreen content", "content audit", "topic cluster", "semantic seo"],
    "geo": ["generative engine optimization", "ai search", "chatgpt", "perplexity",
            "ai overview", "bing copilot", "semantic search", "entity optimization"],
    "franchise": ["franchise marketing", "multi-location", "brand consistency",
                  "franchisee", "local advertising", "territory"],
    "enterprise": ["enterprise seo", "scale", "automation", "stakeholder", "roi",
                   "martech", "workflow", "integration"],
    "local": ["local seo", "google business profile", "citations", "NAP", 
              "near me", "local pack", "map pack"],
    "link building": ["backlinks", "outreach", "guest post", "domain authority",
                     "referring domains", "anchor text", "link equity", "dofollow"],
    "technical": ["core web vitals", "page speed", "crawl budget", "indexing",
                   "schema markup", "canonical", "redirect", "xml sitemap"],
}


def _detect_topic(focuskw, title, slug):
    """Infer primary topic from focus keyword, title, or slug."""
    text = f"{focuskw} {title} {slug}".lower()
    scores = {}
    for topic, terms in LSI_TOPICS.items():
        score = sum(1 for t in terms if t.lower() in text)
        if score > 0:
            scores[topic] = score
    if scores:
        return max(scores, key=scores.get)
    return None


def _check_lsi_coverage(clean_text, topic):
    """Check which LSI terms from the topic map are present in content."""
    if not topic or topic not in LSI_TOPICS:
        return {"topic": topic, "terms_found": 0, "terms_total": 0, "coverage_pct": 0, "missing": []}
    terms = LSI_TOPICS[topic]
    text_lower = clean_text.lower()
    found = [t for t in terms if t.lower() in text_lower]
    missing = [t for t in terms if t.lower() not in text_lower]
    coverage = round(len(found) / len(terms) * 100, 1) if terms else 0
    return {
        "topic": topic,
        "terms_found": len(found),
        "terms_total": len(terms),
        "coverage_pct": coverage,
        "found_terms": found,
        "missing_terms": missing[:5],  # cap output
    }


def _extract_entities(clean_text):
    """Simple entity extraction using capitalized noun phrases."""
    # Extract capitalized phrases (2-4 words) as likely entities
    entity_pat = re.compile(r'\b([A-Z][a-zA-Z]+(?:\s+[A-Z][a-zA-Z]+){1,3})\b')
    raw = entity_pat.findall(clean_text)
    # Filter common false positives
    stopwords = {"The", "This", "That", "These", "Those", "There", "What", "When",
                 "Where", "Which", "While", "How", "Why", "Who", "Will", "Would",
                 "Should", "Could", "Can", "May", "Might", "Must", "Shall"}
    filtered = [e for e in raw if not any(e.startswith(sw) for sw in stopwords)]
    counts = Counter(filtered)
    top = counts.most_common(15)
    return {"entity_count": len(set(filtered)), "top_entities": [e for e, c in top]}


def _check_schema_markup(post_obj):
    """
    Detect JSON-LD schema types. Checks `yoast_head_json` first (live Yoast schema
    in page <head>), falls back to post body embedded scripts.
    """
    found_types = []
    
    # PRIMARY: Check Yoast's rendered head JSON (where live schema lives)
    yoast_head = post_obj.get("yoast_head_json") or {}
    if yoast_head:
        schema_data = yoast_head.get("schema", {})
        # schema can be a string ("@graph": [...]) or dict with @graph
        if isinstance(schema_data, dict):
            graph = schema_data.get("@graph", [])
            if graph:
                for item in graph:
                    t = item.get("@type", "")
                    if t:
                        found_types.append(t)
            else:
                # Direct @type on schema root
                t = schema_data.get("@type", "")
                if t:
                    found_types.append(t)
        elif isinstance(schema_data, str):
            # Regex fallback
            types = re.findall(r'"@type"\s*:\s*"([^"]+)"', schema_data)
            found_types.extend(types)
    
    # FALLBACK: Also check any ld+json scripts inside post body
    body = post_obj.get("content", {}).get("rendered", "")
    if 'application/ld+json' in body:
        schemas = re.findall(r'<script[^\u003e]*type="application/ld\+json"[^\u003e]*\u003e(.*?)\u003c/script\u003e',
                             body, re.DOTALL|re.IGNORECASE)
        for s in schemas:
            try:
                data = json.loads(s.strip())
                if isinstance(data, dict):
                    t = data.get("@type", "")
                    if t:
                        found_types.append(t)
                    if "@graph" in data:
                        for item in data["@graph"]:
                            if isinstance(item, dict):
                                t = item.get("@type", "")
                                if t:
                                    found_types.append(t)
                elif isinstance(data, list):
                    for item in data:
                        if isinstance(item, dict) and "@type" in item:
                            found_types.append(item["@type"])
            except json.JSONDecodeError:
                types = re.findall(r'"@type"\s*:\s*"([^"]+)"', s)
                found_types.extend(types)
    
    important = ["Service", "FAQPage", "BreadcrumbList", "ProfessionalService",
                 "LocalBusiness", "Organization", "Article", "WebPage"]
    has = {t: t in found_types for t in important}
    
    return {
        "schema_count": len(found_types),
        "schema_types": list(set(found_types)),
        "has_service": has["Service"],
        "has_faqpage": has["FAQPage"],
        "has_breadcrumb": has["BreadcrumbList"],
        "has_professional_service": has["ProfessionalService"],
        "has_article": has["Article"],
        "has_organization": has["Organization"],
        "important_missing": [t for t in important if not has[t] and t in ["Service", "FAQPage", "BreadcrumbList"]],
    }


def _check_readability(clean_text):
    """Calculate readability scores. Returns None if textstat not available."""
    if not HAS_TEXTSTAT:
        return {"available": False, "flesch_reading_ease": None, "grade_level": None}
    try:
        fre = textstat.flesch_reading_ease(clean_text)
        grade = textstat.flesch_kincaid_grade(clean_text)
        # Interpretation
        level = "easy" if fre > 70 else "moderate" if fre > 50 else "difficult"
        return {
            "available": True,
            "flesch_reading_ease": round(fre, 1),
            "grade_level": round(grade, 1),
            "interpretation": level,
        }
    except Exception:
        return {"available": False, "flesch_reading_ease": None, "grade_level": None}


def _check_anchor_text_diversity(html_content, site="rankray"):
    """Analyze internal link anchor text distribution."""
    DOMAINS = {
        "rankray": ["rankray.com", "www.rankray.com"],
    }
    domains_list = DOMAINS.get(site, [])
    
    anchors = re.findall(r'<a[^>]*href="([^"]+)"[^>]*>(.*?)</a>', html_content, re.IGNORECASE|re.DOTALL)
    internal_anchors = []
    for href, text in anchors:
        parsed = urllib.parse.urlparse(href)
        is_internal = not parsed.netloc or any(d.lower() in parsed.netloc.lower() for d in domains_list)
        if is_internal and '#' not in href:
            clean_text = re.sub(r'<[^>]+>', ' ', text).strip().lower()
            if clean_text:
                internal_anchors.append(clean_text)
    
    if not internal_anchors:
        return {"total_internal_anchors": 0, "unique_anchors": 0, "repeated_anchors": [], "diversity_score": 0}
    
    counts = Counter(internal_anchors)
    repeated = [(a, c) for a, c in counts.items() if c > 1]
    diversity = round(len(counts) / len(internal_anchors) * 100, 1)
    
    return {
        "total_internal_anchors": len(internal_anchors),
        "unique_anchors": len(counts),
        "repeated_anchors": sorted(repeated, key=lambda x: x[1], reverse=True)[:5],
        "diversity_score": diversity,
        "low_diversity": diversity < 50 and len(internal_anchors) > 3,
    }


def _check_cta_presence(html_content, clean_text):
    """Detect call-to-action patterns and service page links."""
    cta_patterns = [
        r'contact us', r'get started', r'book a consultation', r'schedule',
        r'request a quote', r'call us', r'learn more about.*service',
        r'our team can help', r'work with us', r'hire us',
    ]
    cta_found = []
    text_lower = clean_text.lower()
    for pat in cta_patterns:
        if re.search(pat, text_lower):
            cta_found.append(pat.replace(r'\b', '').strip())
    
    # Check for service page links
    has_service_link = bool(re.search(r'href="[^"]*(?:service|seo|marketing)[^"]*"', html_content, re.IGNORECASE))
    
    return {
        "has_cta": len(cta_found) > 0,
        "cta_patterns_found": cta_found[:3],
        "has_service_page_link": has_service_link,
    }


def _check_faq_structure(html_content):
    """Scan for FAQ/question-based headings that target featured snippets."""
    # Look for headings that start with question words
    question_patterns = [
        r'<h[23][^>]*>\s*(?:what|how|why|when|where|which|who|can|does|is|are)\s+',
        r'<h[23][^>]*>\s*\d+\.\s*(?:what|how|why|when|where)',
    ]
    faq_headings = []
    for pat in question_patterns:
        matches = re.findall(pat, html_content, re.IGNORECASE)
        faq_headings.extend(matches)
    
    # Also check for FAQ schema
    has_faq_schema = bool(re.search(r'"@type"\s*:\s*"FAQPage"', html_content, re.IGNORECASE))
    
    return {
        "faq_heading_count": len(faq_headings),
        "has_faq_schema": has_faq_schema,
        "snippet_opportunities": len(faq_headings) > 0,
    }


def _check_canonical(html_content, current_url):
    """Check if canonical tag matches the actual URL."""
    canon = re.search(r'<link[^>]*rel="canonical"[^>]*href="([^"]+)"', html_content, re.IGNORECASE)
    if not canon:
        canon = re.search(r'<link[^>]*href="([^"]+)"[^>]*rel="canonical"', html_content, re.IGNORECASE)
    
    if canon:
        canonical_url = canon.group(1)
        # Simple comparison — strip trailing slashes and protocol for matching
        canon_norm = canonical_url.rstrip('/').replace('https://', '').replace('http://', '')
        current_norm = current_url.rstrip('/').replace('https://', '').replace('http://', '')
        matches = canon_norm == current_norm
        return {"has_canonical": True, "canonical_url": canonical_url, "matches": matches}
    
    return {"has_canonical": False, "canonical_url": None, "matches": False}


def _check_image_size(image_url, timeout=10):
    """Check image file size via Content-Length header."""
    if not image_url:
        return {"size_kb": 0, "ok": True}
    try:
        req = request.Request(image_url, method="HEAD")
        req.add_header("User-Agent", "Mozilla/5.0")
        with request.urlopen(req, timeout=timeout) as r:
            cl = r.headers.get('Content-Length')
            if cl:
                size_kb = int(cl) / 1024
                return {"size_kb": round(size_kb, 1), "ok": size_kb < 500, "too_large": size_kb >= 500}
            return {"size_kb": None, "ok": True, "too_large": False}
    except Exception as e:
        return {"size_kb": None, "ok": True, "too_large": False, "error": str(e)}


def _check_information_density(clean_text):
    """Calculate ratio of 'fluff' words vs substantive content."""
    fluff_words = {
        "very", "really", "just", "quite", "rather", "basically", "actually",
        "essentially", "fundamentally", "literally", "simply", "obviously",
        "certainly", "definitely", "probably", "likely", "generally", "typically",
        "basically", "ultimately", "needless to say", "it goes without saying"
    }
    words = clean_text.lower().split()
    total = len(words)
    fluff_count = sum(1 for w in words if w in fluff_words)
    # Also count data points (numbers, percentages, years)
    data_points = len(re.findall(r'\b\d+(?:\.\d+)?%?\b|\b20\d{2}\b', clean_text))
    
    fluff_pct = round(fluff_count / total * 100, 1) if total else 0
    data_density = round(data_points / total * 100, 1) if total else 0
    
    return {
        "total_words": total,
        "fluff_words": fluff_count,
        "fluff_pct": fluff_pct,
        "data_points": data_points,
        "data_density_pct": data_density,
        "low_density": fluff_pct > 5 and data_density < 2,
    }


def _check_canonical_yoast(raw_meta, current_slug, base_url="https://rankray.com"):
    """Check Yoast canonical meta field — the real source on WordPress."""
    yoast_canon = raw_meta.get("_yoast_wpseo_canonical", "")
    if not yoast_canon:
        return {"has_canonical": False, "canonical_url": None, "matches": None}
    expected = f"{base_url.rstrip('/')}/{current_slug.strip('/')}/"
    expected_alt = f"{base_url.rstrip('/')}/{current_slug.strip('/')}"
    matches = yoast_canon == expected or yoast_canon == expected_alt
    return {"has_canonical": True, "canonical_url": yoast_canon, "matches": matches}


# ── HELPERS ──────────────────────────────────────────────────────────────

def _auth(user, pw):
    return base64.b64encode(f"{user}:{pw}".encode()).decode()


def _get(base, slug, endpoint, params=""):
    url = f"{base}{endpoint}?{params}&per_page=100".rstrip('?')
    req = request.Request(url)
    req.add_header("Authorization", f"Basic {_auth(SITES[slug]['user'], SITES[slug]['pass'])}")
    with request.urlopen(req, timeout=60) as r:
        return json.loads(r.read().decode())


def _clean_text_for_wordcount(html_content):
    """Remove scripts, styles, shortcodes, and tags for accurate word count."""
    # Remove script/style/shortcode blocks
    html_content = re.sub(r'<script[^>]*>.*?</script>', ' ', html_content, flags=re.DOTALL|re.IGNORECASE)
    html_content = re.sub(r'<style[^>]*>.*?</style>', ' ', html_content, flags=re.DOTALL|re.IGNORECASE)
    html_content = re.sub(r'\[.*?\]', ' ', html_content)
    # Remove tags
    text = re.sub(r'<[^>]+>', ' ', html_content)
    # Normalize whitespace
    text = re.sub(r'\s+', ' ', text).strip()
    return text


def _count_sentences(text):
    """Rough sentence count for paragraph analysis."""
    sentences = re.split(r'[.!?]+', text)
    return len([s for s in sentences if s.strip()])


def _analyze_paragraphs(html_content):
    """Check paragraph density: max 3 sentences, max 60 words."""
    # Find all <p> tags
    paragraphs = re.findall(r'<p[^>]*>(.*?)</p>', html_content, re.DOTALL|re.IGNORECASE)
    
    issues = []
    total_paragraphs = 0
    long_paragraphs = 0
    
    for p in paragraphs:
        # Strip inner tags
        clean = re.sub(r'<[^>]+>', ' ', p)
        clean = re.sub(r'\s+', ' ', clean).strip()
        if not clean:
            continue
        
        total_paragraphs += 1
        words = len(clean.split())
        sentences = _count_sentences(clean)
        
        if sentences > MAX_SENTENCES_PER_PARAGRAPH or words > MAX_WORDS_PER_PARAGRAPH:
            long_paragraphs += 1
            issues.append({
                "words": words,
                "sentences": sentences,
                "preview": clean[:80] + "..." if len(clean) > 80 else clean
            })
    
    return {
        "total_paragraphs": total_paragraphs,
        "long_paragraphs": long_paragraphs,
        "paragraph_issues": issues[:3],  # Show first 3
    }


def _count_real_links(html_content, current_slug, site="rankray"):
    """
    Distinguish real cross-page links from TOC self-anchors.
    Handles both relative AND absolute internal links.
    Returns: real links, self anchors, external links, plus per-domain breakdown.
    """
    DOMAINS = {
        "rankray": ["rankray.com", "www.rankray.com"],
        "tonicphysio": ["tonicphysio.com", "www.tonicphysio.com"],
        "teammotorcycle": ["teammotorcycle.com", "www.teammotorcycle.com"],
        "khanllp": ["khanllp.com", "www.khanllp.com"],
        "coinsfera": ["coinsfera.com", "www.coinsfera.com"],
    }
    domains_list = DOMAINS.get(site, [])

    # Catch ALL hrefs
    all_hrefs = re.findall(r'href="([^"]*)"', html_content, re.IGNORECASE)
    all_hrefs += re.findall(r"href='([^']*)'", html_content, re.IGNORECASE)

    real = set()
    self_anchors = set()
    external = set()
    seen_raw = set()

    for href in all_hrefs:
        href = href.strip()
        if not href or href in seen_raw:
            continue
        seen_raw.add(href)

        parsed = urllib.parse.urlparse(href)

        # Is it internal?
        is_internal = False
        if not parsed.netloc:
            is_internal = True  # relative
        else:
            for d in domains_list:
                if d.lower() in parsed.netloc.lower():
                    is_internal = True
                    break

        if not is_internal:
            external.add(href)
            continue

        # Internal — classify
        path = parsed.path.rstrip('/')

        # Has # anchor?
        if '#' in href:
            slug_variants = [f'/{current_slug}', f'/{current_slug}/', current_slug]
            base_on_current = any(
                path == v or path.rstrip('/') == v.rstrip('/')
                for v in slug_variants
            ) or (not path.strip('/'))

            if base_on_current:
                self_anchors.add(href)
                continue
            else:
                real.add(href)
        else:
            real.add(href)

    return {
        "total_hrefs": len(seen_raw),
        "real_links_count": len(real),
        "self_anchors_count": len(self_anchors),
        "external_count": len(external),
        "real_urls": sorted(real),
        "self_anchor_urls": sorted(self_anchors),
        "external_urls": sorted(external),
    }


def _check_summary_block(html_content):
    """
    Check for summary block (blockquote after intro, before first H2).
    Looks for approved labels in blockquote near the top.
    """
    APPROVED_LABELS = [
        "What You'll Learn", "Key Findings", "Quick Comparison",
        "Key Takeaway", "The Bottom Line", "Quick Overview",
        "The Results", "In Short"
    ]
    BANNED_LABELS = ["TL;DR"]
    
    # Find first blockquote
    first_bq = re.search(r'<blockquote[^>]*>(.*?)</blockquote>', html_content, re.DOTALL|re.IGNORECASE)
    if not first_bq:
        return {"found": False, "label": None, "has_banned_label": False}
    
    bq_content = first_bq.group(1)
    bq_text = re.sub(r'<[^>]+>', ' ', bq_content).strip()
    
    # Check for approved labels
    found_label = None
    for label in APPROVED_LABELS:
        if label.lower() in bq_text.lower():
            found_label = label
            break
    
    # Check for banned labels
    has_banned = any(banned.lower() in bq_text.lower() for banned in BANNED_LABELS)
    
    return {
        "found": True,
        "label": found_label,
        "has_banned_label": has_banned,
        "bq_text_preview": bq_text[:100] + "..." if len(bq_text) > 100 else bq_text,
    }


def _check_link_status(url, timeout=15):
    """Check if an internal link returns 200, redirects, or broken."""
    try:
        req = request.Request(url, method="HEAD")
        req.add_header("User-Agent", "Mozilla/5.0")
        with request.urlopen(req, timeout=timeout) as r:
            status = r.getcode()
            # Check content type from headers
            content_type = r.headers.get('Content-Type', '')
            return {
                "status": status,
                "content_type": content_type,
                "final_url": r.geturl()
            }
    except urllib.error.HTTPError as e:
        return {"status": e.code, "error": str(e.reason), "content_type": "", "final_url": url}
    except urllib.error.URLError as e:
        return {"status": None, "error": str(e.reason), "content_type": "", "final_url": url}
    except Exception as e:
        return {"status": None, "error": str(e), "content_type": "", "final_url": url}


def _classify_link_status(status_info):
    """Classify link status into OK, redirect, broken, or suspicious."""
    status = status_info.get("status")
    content_type = status_info.get("content_type", "")
    error = status_info.get("error", "")
    
    if status == 200:
        if content_type and not content_type.startswith('text/html'):
            return "non_html", f"OK but returns {content_type}"
        return "ok", "Valid page"
    elif status in (301, 302, 307, 308):
        return "redirect", f"Redirects to other URL"
    elif status == 404 or status == 410:
        return "broken", f"Page not found ({status})"
    elif status == 500:
        return "server_error", "Server error"
    elif status is None:
        return "dead", f"Unreachable: {error}"
    else:
        return "unknown", f"HTTP {status}: {error}"


def _check_all_links(link_urls, base_domain, delay=0.0, max_workers=8, timeout=10):
    """Check all links concurrently with threading."""
    results = {}
    
    def _check_one(url):
        full_url = url if url.startswith('http') else f"https://{base_domain}{url}"
        status_info = _check_link_status(full_url, timeout=timeout)
        category, message = _classify_link_status(status_info)
        return url, {
            "full_url": full_url,
            "http_status": status_info["status"],
            "category": category,
            "message": message,
            "final_url": status_info.get("final_url", full_url)
        }
    
    with ThreadPoolExecutor(max_workers=max_workers) as ex:
        futures = {ex.submit(_check_one, url): url for url in link_urls}
        for future in as_completed(futures):
            try:
                url, result = future.result()
                results[url] = result
            except Exception as e:
                url = futures[future]
                results[url] = {"full_url": url, "error": str(e), "category": "dead", "message": str(e)}
    
    return results


def _check_summary_block(html_content):
    """
    Check for summary block (blockquote after intro, before first H2).
    Looks for approved labels in blockquote near the top.
    """
    APPROVED_LABELS = [
        "What You'll Learn", "Key Findings", "Quick Comparison",
        "Key Takeaway", "The Bottom Line", "Quick Overview",
        "The Results", "In Short"
    ]
    BANNED_LABELS = ["TL;DR"]
    
    # Find first blockquote
    first_bq = re.search(r'<blockquote[^>]*>(.*?)</blockquote>', html_content, re.DOTALL|re.IGNORECASE)
    if not first_bq:
        return {"found": False, "label": None, "has_banned_label": False}
    
    bq_content = first_bq.group(1)
    bq_text = re.sub(r'<[^>]+>', ' ', bq_content).strip()
    
    # Check for approved labels
    found_label = None
    for label in APPROVED_LABELS:
        if label.lower() in bq_text.lower():
            found_label = label
            break
    
    # Check for banned labels
    has_banned = any(banned.lower() in bq_text.lower() for banned in BANNED_LABELS)
    
    return {
        "found": True,
        "label": found_label,
        "has_banned_label": has_banned,
        "bq_text_preview": bq_text[:100] + "..." if len(bq_text) > 100 else bq_text,
    }


def _check_structure(html_content):
    """Check tables, lists, hr breaks, heading levels."""
    tables = len(re.findall(r'<table', html_content, re.IGNORECASE))
    lists = len(re.findall(r'<(ul|ol)', html_content, re.IGNORECASE))
    hrs = len(re.findall(r'<hr', html_content, re.IGNORECASE))
    
    # Heading levels
    h_tags = re.findall(r'<h([1-6])[^>]*>', html_content, re.IGNORECASE)
    h_counts = {f"h{i}": 0 for i in range(1, 7)}
    for h in h_tags:
        h_counts[f"h{h}"] += 1
    
    # Check heading hierarchy
    skipped = []
    prev = None
    for h in h_tags:
        h_int = int(h)
        if prev and h_int > prev + 1:
            skipped.append(f"H{prev}→H{h_int}")
        prev = h_int
    
    return {
        "table_count": tables,
        "list_count": lists,
        "hr_count": hrs,
        "heading_counts": h_counts,
        "total_headings": len(h_tags),
        "skipped_levels": skipped,
        "has_h1": h_counts["h1"] > 0,
    }


def _analyze_post(post, check_links=False):
    body = post.get("content", {}).get("rendered", "")
    slug = post.get("slug", "")
    
    # Clean text for accurate word count
    clean_text = _clean_text_for_wordcount(body)
    word_count = len(clean_text.split())
    
    # Yoast meta
    raw_meta = post.get("meta", {})
    yoast = {
        "focuskw": raw_meta.get("_yoast_wpseo_focuskw", "") or "",
        "title": raw_meta.get("_yoast_wpseo_title", "") or "",
        "metadesc": raw_meta.get("_yoast_wpseo_metadesc", "") or "",
    }
    
    # Links
    link_data = _count_real_links(body, slug, site="rankray")
    
    # Check link health for all real internal links
    link_status_check = {}
    broken_links = []
    redirect_links = []
    non_html_links = []
    
    if link_data["real_urls"]:
        if check_links:
            # Live HEAD requests — slow but accurate
            link_status_check = _check_all_links(link_data["real_urls"], "rankray.com")
            for url, status in link_status_check.items():
                if status["category"] == "broken":
                    broken_links.append({"url": url, "error": status["message"]})
                elif status["category"] == "redirect":
                    redirect_links.append({"url": url, "redirects_to": status["final_url"]})
                elif status["category"] == "non_html":
                    non_html_links.append({"url": url, "type": status["message"]})
        else:
            # Fast path: count only, no live HEAD requests
            for url in link_data["real_urls"]:
                link_status_check[url] = {"category": "unchecked", "message": "Live check skipped"}
    
    # Note: Use --check-links CLI flag to enable live broken link detection
    
    # Featured image — read from `featured_media` field directly (no _embed needed)
    fm_id = post.get("featured_media")
    featured_image = {
        "id": fm_id,
        "source_url": "",  # Would need separate media fetch for URL
        "alt_text": "",
    }
    if not fm_id:
        issues.append({"type": "missing_image", "detail": "No featured image"})
        tags.append("missing_image")
    # Alt text check: we can't verify alt without fetching media, skip for now
    
    # Structure analysis
    structure = _check_structure(body)
    
    # Summary block
    summary = _check_summary_block(body)
    
    # Paragraph density
    paragraphs = _analyze_paragraphs(body)
    
    # ── SEMANTIC SEO CHECKS ──────────────────────────────────────────────
    topic = _detect_topic(yoast["focuskw"], post.get("title", {}).get("rendered", ""), slug)
    lsi_coverage = _check_lsi_coverage(clean_text, topic)
    entities = _extract_entities(clean_text)
    schema = _check_schema_markup(post)
    readability = _check_readability(clean_text)
    anchor_diversity = _check_anchor_text_diversity(body, site="rankray")
    cta = _check_cta_presence(body, clean_text)
    faq = _check_faq_structure(body)
    canonical_yoast = _check_canonical_yoast(raw_meta, slug)
    info_density = _check_information_density(clean_text)
    
    # Check featured image size
    image_size = {"size_kb": 0, "ok": True}
    if featured_image["source_url"]:
        image_size = _check_image_size(featured_image["source_url"])
    
    # Em-dashes
    em_dash_count = body.count('\u2014') + body.count('\u2013')
    
    # Double dashes (filter out HTML comment artifacts)
    # Strip WP block editor comments first
    body_no_comments = re.sub(r'\u003c!--.*?--\u003e', '', body, re.DOTALL|re.IGNORECASE)
    double_dash = body_no_comments.count('--')
    
    # Repeated words
    repeat_words = list(set(re.compile(r'(\b\w{4,}\b)\s+\1', re.IGNORECASE).findall(body)))
    
    # Emoji check
    emoji_pat = re.compile(
        r'[\U0001F600-\U0001F64F\U0001F300-\U0001F5FF\U0001F680-\U0001F6FF'
        r'\U0001F700-\U0001F77F\U0001F780-\U0001F7FF\U0001F800-\U0001F8FF'
        r'\U0001F900-\U0001F9FF\U0001FA00-\U0001FA6F\U0001FA70-\U0001FAFF'
        r'\U00002702-\U000027B0\U000024C2-\U0001F251]+')
    emojis = emoji_pat.findall(body)
    
    # Raw markdown patterns
    md_patterns = [
        r'^##\s+', r'\*\*', r'^-\s+', r'^\d+\.\s+', r'\[.*?\]\(.*?\)'
    ]
    raw_md_found = [{"pattern": p, "count": len(re.findall(p, body, re.MULTILINE))}
                    for p in md_patterns if re.findall(p, body, re.MULTILINE)]
    
    # Categorize issues
    issues = []
    tags = []
    
    if word_count < MIN_WORDS:
        issues.append({"type": "thin_content", "detail": f"words:{word_count}"})
        tags.append("thin_content")
    
    # Link quality check — no hard minimum, but warn if zero and report broken links
    if link_data["real_links_count"] == 0:
        issues.append({"type": "no_links", "detail": "Zero real internal links"})
        tags.append("no_links")
    
    if broken_links:
        issues.append({"type": "broken_links", "detail": f"{len(broken_links)} broken link(s)"})
        tags.append("broken_links")
    
    if redirect_links:
        issues.append({"type": "redirect_links", "detail": f"{len(redirect_links)} link(s) redirecting"})
        tags.append("redirect_links")
    
    if non_html_links:
        issues.append({"type": "non_html_links", "detail": f"{len(non_html_links)} link(s) returning non-HTML content"})
        tags.append("non_html_links")
    
    if not yoast["focuskw"]:
        issues.append({"type": "missing_focuskw", "detail": "No focus keyword"})
        tags.append("missing_yoast")
    
    if not yoast["title"]:
        issues.append({"type": "missing_title", "detail": "No meta title"})
        tags.append("missing_yoast")
    elif len(yoast["title"]) > META_TITLE_MAX:
        issues.append({"type": "long_title", "detail": f"title_len:{len(yoast['title'])}"})
        tags.append("missing_yoast")
    
    if not yoast["metadesc"]:
        issues.append({"type": "missing_desc", "detail": "No meta description"})
        tags.append("missing_yoast")
    elif len(yoast["metadesc"]) > META_DESC_MAX:
        issues.append({"type": "long_desc", "detail": f"desc_len:{len(yoast['metadesc'])} (max {META_DESC_MAX})"})
        tags.append("missing_yoast")
    
    # ── NEW: Brand in meta description ───────────────────────────────────
    BRAND_NAMES = ["rank ray", "rankray"]
    if yoast["metadesc"] and not any(b in yoast["metadesc"].lower() for b in BRAND_NAMES):
        issues.append({"type": "missing_brand_in_desc", "detail": "Yoast meta description missing brand name (Rank Ray / RankRay)"})
        tags.append("missing_yoast")
    
    # ── NEW: Categories check ────────────────────────────────────────────
    # categories parameter comes from post["categories"] — list of IDs
    post_categories = post.get("categories", [])
    if 1 in post_categories:
        issues.append({"type": "default_category", "detail": "Post assigned to default 'Topics' [1]. Assign real categories."})
        tags.append("bad_categories")
    elif not post_categories:
        issues.append({"type": "no_category", "detail": "No categories assigned. Will default to WordPress catch-all."})
        tags.append("bad_categories")
    
    if featured_image["id"] is None:
        issues.append({"type": "missing_image", "detail": "No featured image"})
        tags.append("missing_image")
    
    if not featured_image["alt_text"]:
        issues.append({"type": "missing_alt", "detail": "No alt text"})
        tags.append("missing_alt")
    
    if emojis:
        issues.append({"type": "emojis", "detail": f"emojis:{len(emojis)}"})
        tags.append("has_emoji")
    
    if em_dash_count > 0:
        issues.append({"type": "em_dashes", "detail": f"em_dashes:{em_dash_count}"})
        tags.append("has_em_dash")
    
    if double_dash > 0:
        issues.append({"type": "double_dashes", "detail": f"double_dashes:{double_dash}"})
        tags.append("double_dash")
    
    if raw_md_found:
        issues.append({"type": "raw_markdown", "detail": f"raw_md:{len(raw_md_found)}"})
        tags.append("raw_markdown")
    
    if structure["has_h1"]:
        issues.append({"type": "h1_in_body", "detail": "H1 tag found in body"})
        tags.append("bad_headings")
    
    if structure["skipped_levels"]:
        issues.append({"type": "skipped_levels", "detail": f"skipped:{len(structure['skipped_levels'])}"})
        tags.append("bad_headings")
    
    if repeat_words:
        issues.append({"type": "repeated_words", "detail": f"repeated:{len(repeat_words)}"})
        tags.append("repeated_words")
    
    if not summary["found"]:
        issues.append({"type": "missing_summary", "detail": "No summary block"})
        tags.append("missing_summary")
    elif summary["has_banned_label"]:
        issues.append({"type": "banned_label", "detail": "Uses banned TL;DR label"})
        tags.append("bad_summary")
    elif not summary["label"]:
        issues.append({"type": "unlabeled_summary", "detail": "Summary block missing approved label"})
        tags.append("bad_summary")
    
    if paragraphs["long_paragraphs"] > 0:
        issues.append({"type": "dense_paragraphs", "detail": f"long_paragraphs:{paragraphs['long_paragraphs']}"})
        tags.append("bad_formatting")
    
    if structure["heading_counts"]["h2"] == 0:
        issues.append({"type": "no_h2s", "detail": "No H2 headings"})
        tags.append("bad_structure")
    
    h2_count = structure["heading_counts"]["h2"]
    h3_count = structure["heading_counts"]["h3"]
    if h2_count > 0 and h3_count < h2_count * 0.5:
        issues.append({"type": "few_h3s", "detail": f"h2:{h2_count}, h3:{h3_count}"})
        tags.append("bad_structure")
    
    if structure["table_count"] == 0 and structure["list_count"] == 0:
        # Only flag if word count > 1000 (long posts should have some structure)
        if word_count > 1000:
            issues.append({"type": "no_tables_or_lists", "detail": "No tables or lists found"})
            tags.append("bad_structure")
    
    # ── SEMANTIC SEO ISSUES ──────────────────────────────────────────────
    if lsi_coverage["coverage_pct"] < 30 and lsi_coverage["terms_total"] > 3:
        issues.append({"type": "low_lsi_coverage", "detail": f"LSI coverage: {lsi_coverage['coverage_pct']}%"})
        tags.append("semantic_gap")
    
    if schema["schema_count"] == 0:
        issues.append({"type": "missing_schema", "detail": "No JSON-LD schema markup found"})
        tags.append("missing_schema")
    elif not schema["has_article"] and not schema["has_service"]:
        issues.append({"type": "weak_schema", "detail": "No Article or Service schema detected"})
        tags.append("weak_schema")
    
    if not cta["has_cta"]:
        issues.append({"type": "missing_cta", "detail": "No call-to-action detected"})
        tags.append("missing_cta")
    
    if anchor_diversity.get("low_diversity"):
        issues.append({"type": "low_anchor_diversity", "detail": f"Diversity: {anchor_diversity['diversity_score']}%"})
        tags.append("manipulative_anchors")
    
    if faq["snippet_opportunities"] and not faq["has_faq_schema"]:
        issues.append({"type": "faq_no_schema", "detail": f"{faq['faq_heading_count']} FAQ headings but no FAQPage schema"})
        tags.append("missed_snippet")
    
    if canonical_yoast["has_canonical"] and not canonical_yoast["matches"]:
        issues.append({"type": "canonical_mismatch", "detail": f"Canonical points to {canonical_yoast['canonical_url']}"})
        tags.append("canonical_issue")
    elif not canonical_yoast["has_canonical"]:
        issues.append({"type": "missing_canonical", "detail": "No Yoast canonical set"})
        tags.append("canonical_issue")
    
    if not cta["has_service_page_link"]:
        issues.append({"type": "no_service_link", "detail": "No service page link"})
        tags.append("missing_service_link")
    
    if info_density["low_density"]:
        issues.append({"type": "low_information_density", "detail": f"Fluff: {info_density['fluff_pct']}% | Data: {info_density['data_density_pct']}%"})
        tags.append("thin_value")
    
    if image_size.get("too_large"):
        issues.append({"type": "oversized_image", "detail": f"Image: {image_size['size_kb']}KB"})
        tags.append("performance_issue")
    
    if word_count > 500 and not faq["snippet_opportunities"]:
        issues.append({"type": "no_faq_opportunities", "detail": "No snippet targets"})
        tags.append("missed_snippet")
    
    return {
        "id": post.get("id"),
        "slug": slug,
        "title": post.get("title", {}).get("rendered", ""),
        "status": post.get("status", ""),
        "word_count": word_count,
        "topic": topic,
        "link_count": link_data["real_links_count"],
        "self_anchors": link_data["self_anchors_count"],
        "external_links": link_data["external_count"],
        "total_hrefs": link_data["total_hrefs"],
        "link_status": link_status_check,
        "broken_links": broken_links,
        "redirect_links": redirect_links,
        "non_html_links": non_html_links,
        "featured_image": featured_image,
        "image_size": image_size,
        "yoast": yoast,
        "summary_block": summary,
        "structure": structure,
        "paragraphs": paragraphs,
        "semantic": {
            "topic": topic,
            "lsi_coverage": lsi_coverage,
            "entities": entities,
            "schema": schema,
            "readability": readability,
            "anchor_diversity": anchor_diversity,
            "cta": cta,
            "faq": faq,
            "canonical": canonical_yoast,
            "information_density": info_density,
        },
        "em_dashes": em_dash_count,
        "double_dashes": double_dash,
        "emojis": len(emojis),
        "raw_markdown": len(raw_md_found),
        "repeated_words": len(repeat_words),
        "has_emoji": bool(emojis),
        "issues": issues,
        "issue_count": len(issues),
        "tags": list(set(tags)),
    }


def audit_site(site_key, status="publish", max_posts=None, check_links=False):
    cfg = SITES.get(site_key)
    if not cfg:
        return {"error": f"Unknown site: {site_key}"}

    params = "context=edit&_fields=id,slug,title,status,content,meta,yoast_head_json,featured_media"
    if status:
        params += f"&status={status}"

    posts = _get(cfg["url"], site_key, "posts", params)
    if isinstance(posts, dict) and "_error" in posts:
        return {"error": posts["_error"]}

    if max_posts:
        posts = posts[:max_posts]

    results = [_analyze_post(p, check_links=check_links) for p in posts]
    
    # ── SELF-PLAGIARISM CHECK (site-wide) ──────────────────────────────────
    # Find duplicate H1s (titles) and meta descriptions across posts
    title_map = Counter(r["title"] for r in results)
    desc_map = Counter(r["yoast"]["metadesc"] for r in results if r["yoast"].get("metadesc"))
    
    dup_titles = {t: c for t, c in title_map.items() if c > 1}
    dup_descs = {d: c for d, c in desc_map.items() if c > 1}
    
    for r in results:
        if r["title"] in dup_titles and r["title"]:
            r["issues"].append({"type": "duplicate_title", "detail": f"Title duplicated {dup_titles[r['title']]} times"})
            r["tags"].append("self_plagiarism")
        if r["yoast"].get("metadesc") in dup_descs and r["yoast"].get("metadesc"):
            r["issues"].append({"type": "duplicate_meta_desc", "detail": f"Meta desc duplicated {dup_descs[r['yoast']['metadesc']]} times"})
            r["tags"].append("self_plagiarism")
        # Update issue_count after additions
        r["issue_count"] = len(r["issues"])
        r["tags"] = list(set(r["tags"]))
    
    # Build rich summary
    total = len(results)
    
    # ── SEMANTIC SUMMARY METRICS ──────────────────────────────────────────
    semantic_posts = [r for r in results if r.get("semantic")]
    
    # Count semantic issues
    low_lsi = sum(1 for r in results if "semantic_gap" in r["tags"])
    missing_schema = sum(1 for r in results if "missing_schema" in r["tags"])
    weak_schema = sum(1 for r in results if "weak_schema" in r["tags"])
    missing_cta = sum(1 for r in results if "missing_cta" in r["tags"])
    anchor_problems = sum(1 for r in results if "manipulative_anchors" in r["tags"])
    canonical_issues = sum(1 for r in results if "canonical_issue" in r["tags"])
    no_service_links = sum(1 for r in results if "missing_service_link" in r["tags"])
    low_density = sum(1 for r in results if "thin_value" in r["tags"])
    missed_snippets = sum(1 for r in results if "missed_snippet" in r["tags"])
    dup_title_count = len(dup_titles)
    dup_desc_count = len(dup_descs)
    
    summary = {
        "total_posts": total,
        "posts_with_any_issue": sum(1 for r in results if r["issues"]),
        "clean_posts": sum(1 for r in results if not r["issues"]),
        
        # Content quality
        "thin_content": sum(1 for r in results if r["word_count"] < MIN_WORDS),
        "low_links": sum(1 for r in results if r["link_count"] == 0),
        "missing_summary": sum(1 for r in results if "missing_summary" in r["tags"]),
        "bad_summary_label": sum(1 for r in results if "bad_summary" in r["tags"]),
        "dense_paragraphs": sum(1 for r in results if "bad_formatting" in r["tags"]),
        "no_tables_or_lists": sum(1 for r in results if "no_tables_or_lists" in [i["type"] for i in r["issues"]]),
        "few_h3s": sum(1 for r in results if "few_h3s" in [i["type"] for i in r["issues"]]),
        "no_h2s": sum(1 for r in results if "no_h2s" in [i["type"] for i in r["issues"]]),
        "broken_links": sum(1 for r in results if r["broken_links"]),
        "redirect_links": sum(1 for r in results if r["redirect_links"]),
        "non_html_links": sum(1 for r in results if r["non_html_links"]),
        
        # SEO basics
        "missing_yoast": sum(1 for r in results if "missing_yoast" in r["tags"]),
        "missing_featured_image": sum(1 for r in results if "missing_image" in r["tags"]),
        "missing_alt_text": sum(1 for r in results if "missing_alt" in r["tags"]),
        
        # Hygiene
        "em_dash_posts": sum(1 for r in results if r["em_dashes"] > 0),
        "double_dash_posts": sum(1 for r in results if r["double_dashes"] > 0),
        "emoji_posts": sum(1 for r in results if r["has_emoji"]),
        "h1_in_body": sum(1 for r in results if "bad_headings" in r["tags"] and any("h1_in_body" == i["type"] for i in r["issues"])),
        "raw_markdown_posts": sum(1 for r in results if r["raw_markdown"] > 0),
        "repeated_word_posts": sum(1 for r in results if r["repeated_words"] > 0),
        
        # ── CATEGORIES ────────────────────────────────────────────────────
        "bad_categories": sum(1 for r in results if "bad_categories" in r["tags"]),
        
        # ── SEMANTIC SEO METRICS ─────────────────────────────────────
        "low_lsi_coverage": low_lsi,
        "missing_schema": missing_schema,
        "weak_schema": weak_schema,
        "missing_cta": missing_cta,
        "low_anchor_diversity": anchor_problems,
        "canonical_issues": canonical_issues,
        "no_service_page_links": no_service_links,
        "low_information_density": low_density,
        "missed_snippet_opportunities": missed_snippets,
        "duplicate_titles": dup_title_count,
        "duplicate_meta_descriptions": dup_desc_count,
        "oversized_images": sum(1 for r in results if "performance_issue" in r["tags"]),
        
        # ── AVERAGES ────────────────────────────────────────────────
        "avg_word_count": sum(r["word_count"] for r in results) // total if total else 0,
        "avg_links": round(sum(r["link_count"] for r in results) / total, 1) if total else 0,
        "avg_h2s": round(sum(r["structure"]["heading_counts"]["h2"] for r in results) / total, 1) if total else 0,
        "avg_h3s": round(sum(r["structure"]["heading_counts"]["h3"] for r in results) / total, 1) if total else 0,
        "avg_lsi_coverage": round(sum(r["semantic"]["lsi_coverage"]["coverage_pct"] for r in semantic_posts) / len(semantic_posts), 1) if semantic_posts else 0,
        "avg_fluff_pct": round(sum(r["semantic"]["information_density"]["fluff_pct"] for r in semantic_posts) / len(semantic_posts), 1) if semantic_posts else 0,
    }
    
    # Worst offenders
    by_issues = sorted(results, key=lambda x: x["issue_count"], reverse=True)
    worst = [
        {
            "id": r["id"],
            "title": r["title"][:60],
            "issues": [i["type"] for i in r["issues"]],
            "issue_count": r["issue_count"],
        }
        for r in by_issues[:10]
    ]

    return {
        "site": site_key,
        "total_posts": total,
        "status_filter": status,
        "summary": summary,
        "worst_offenders": worst,
        "posts": results,
    }


# ── CLI ────────────────────────────────────────────────────────────────────
if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Ultimate WordPress Blog Auditor")
    parser.add_argument("--site", required=True, help="Site key (e.g. rankray)")
    parser.add_argument("--status", default="publish", help="Post status filter")
    parser.add_argument("--max", type=int, help="Max posts to audit")
    parser.add_argument("--filter", default="", help="Filter by tag (e.g. low_links, missing_summary)")
    parser.add_argument("--min-links", type=int, default=0, help="Only show posts below this link count")
    parser.add_argument("--show-issues-only", action="store_true", help="Only output posts with issues")
    parser.add_argument("--semantic-report", action="store_true", help="Print Semantic SEO summary only")
    parser.add_argument("--check-links", action="store_true", help="Enable live broken link checking (slower)")
    parser.add_argument("--output", default="", help="Write JSON to file")
    args = parser.parse_args()

    result = audit_site(args.site, status=args.status, max_posts=args.max, check_links=args.check_links)

    if "error" in result:
        print(json.dumps(result, indent=2))
        sys.exit(1)

    # Filter if requested
    posts = result["posts"]
    if args.filter:
        posts = [p for p in posts if args.filter in p.get("tags", [])]
    if args.min_links > 0:
        posts = [p for p in posts if p["link_count"] < args.min_links]
    if args.show_issues_only:
        posts = [p for p in posts if p["issues"]]

    # ── SEMANTIC REPORT MODE ─────────────────────────────────────────────
    if args.semantic_report:
        summary = result["summary"]
        print("=" * 58)
        print("   SEMANTIC SEO REPORT")
        print("=" * 58)
        print(f"Posts audited: {summary['total_posts']}")
        print("")
        print("ENTITY & TOPIC COVERAGE:")
        print(f"  Low LSI coverage:     {summary['low_lsi_coverage']} posts")
        print(f"  Avg LSI coverage:       {summary.get('avg_lsi_coverage', 0)}%")
        print("")
        print("SCHEMA MARKUP:")
        print(f"  Missing schema:         {summary['missing_schema']} posts")
        print(f"  Weak schema:            {summary['weak_schema']} posts")
        print("")
        print("READABILITY & CONTENT:")
        print(f"  Avg fluff percentage:   {summary.get('avg_fluff_pct', 0)}%")
        print(f"  Low info density:       {summary['low_information_density']} posts")
        print("")
        print("INTERNAL LINKING:")
        print(f"  Low anchor diversity:   {summary['low_anchor_diversity']} posts")
        print(f"  No service page links:  {summary['no_service_page_links']} posts")
        print("")
        print("CTA & CONVERSION:")
        print(f"  Missing CTA:            {summary['missing_cta']} posts")
        print("")
        print("FEATURED SNIPPETS:")
        print(f"  Missed opportunities:   {summary['missed_snippet_opportunities']} posts")
        print("")
        print("TECHNICAL:")
        print(f"  Canonical issues:       {summary['canonical_issues']} posts")
        print(f"  Duplicate titles:       {summary['duplicate_titles']}")
        print(f"  Duplicate meta descs:   {summary['duplicate_meta_descriptions']}")
        print(f"  Oversized images:       {summary.get('oversized_images', 0)} posts")
        print("=" * 58)
        sys.exit(0)

    # Build output
    output = {
        "site": result["site"],
        "total_posts_audited": result["total_posts"],
        "posts_matching_filter": len(posts),
        "summary": result["summary"],
        "worst_offenders": result["worst_offenders"],
        "filtered_posts": posts,
    }

    print(json.dumps({"summary": output["summary"]}, indent=2))
    print(f"\nPosts matching filter: {len(posts)}")
    
    if args.output:
        with open(args.output, 'w') as f:
            json.dump(output, f, indent=2)
        print(f"Full JSON written to: {args.output}")
    
    # Show first few matching posts
    for p in posts[:5]:
        issue_types = [i["type"] for i in p["issues"]]
        print(f"\n  [{p['id']}] {p['title'][:55]}...")
        print(f"     Issues: {', '.join(issue_types) if issue_types else 'None'}")
        print(f"     Links: {p['link_count']} | Words: {p['word_count']} | Summary: {'✓' if p['summary_block']['found'] else '✗'}")
        print(f"     H2s: {p['structure']['heading_counts']['h2']} | H3s: {p['structure']['heading_counts']['h3']} | Tables: {p['structure']['table_count']} | Lists: {p['structure']['list_count']}")
        # Show semantic details if present
        if p.get("semantic"):
            s = p["semantic"]
            if s.get("topic"):
                print(f"     Topic: {s['topic']} | LSI: {s['lsi_coverage'].get('coverage_pct', 0)}%")
            if s.get("schema"):
                sc = s["schema"]
                print(f"     Schema types: {', '.join(sc.get('schema_types', [])) or 'None'}")
            if s.get("readability", {}).get("available"):
                r = s["readability"]
                print(f"     Readability: {r['flesch_reading_ease']} (Grade {r['grade_level']})")
