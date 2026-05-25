# Technical SEO Audit Lessons (2026-04-21) — Schema Detection Error

### What Went Wrong
**Incident:** Coinsfera schema audit incorrectly reported "no schema present" when homepage had full Yoast SEO JSON-LD schema.

**Root Causes:**
1. Used basic `grep` patterns that missed minified JSON-LD in `<head>` section
2. Didn't extract `<script type="application/ld+json">` blocks properly
3. Only checked homepage, didn't verify service pages
4. Didn't use OpenSERP (existing free tool with proxy rotation) for SERP analysis

### Correct Schema Audit Workflow

**Step 1: Use OpenSERP for SERP Landscape**
```bash
# OpenSERP is running at /tmp/openserp/ on port 7070
curl -s "http://127.0.0.1:7070/google/search?text=<query>&limit=10"
# Returns: organic rankings, competitor URLs, SERP features
```

**Step 2: Fetch Full Raw HTML**
```bash
curl -sL <URL> > /tmp/page-source.html
```

**Step 3: Extract JSON-LD Properly**
```bash
# Correct pattern for Yoast/minified JSON-LD
curl -sL <URL> | grep -o '<script[^>]*type="application/ld+json"[^>]*>[^<]*</script>'
# Or use Python/Node JSON parser for complex cases
```

**Step 4: Validate Schema Types Present**
Check for:
- LocalBusiness/Organization (NAP consistency)
- Service/Product (on service pages, not just homepage)
- FAQPage (for FAQ sections)
- Article/BlogPosting (for blogs)
- BreadcrumbList (navigation)
- AggregateRating + Review (if reviews displayed)

**Step 5: Cross-Page Verification**
- Homepage schema ≠ all pages have schema
- Service pages often missing Service schema
- Blog posts often missing Article schema
- Check at least: homepage, 2 service pages, 1 blog post

**Step 6: Competitor Comparison**
- Use OpenSERP to get top 5 ranking competitors
- Fetch their schema markup
- Identify gaps: what schema types do competitors have that we don't?

### Available Tools (Free, Already Configured)

**OpenSERP** — Local SERP scraper with rotating proxies
- Location: `/tmp/openserp/`
- Endpoint: `http://127.0.0.1:7070`
- Proxy pool: 10 rotating proxies (config.yaml + proxies.txt)
- Fetcher script: `/workspace/semantic-engine/scripts/openserp-fetcher.py`
- Supports: Google, DuckDuckGo, Bing
- Caching: 24hr TTL built-in

**Serper Skill** — Google search with full page content extraction
- Location: `/workspace/skills/serper/`
- Script: `scripts/search.py`
- Mode: `default` (5 results) or `current` (news)
- Includes: Full page content via trafilatura (3s timeout)

### Audit Checklist (Mandatory)
- [ ] Use OpenSERP or Serper for SERP analysis (not manual search)
- [ ] Fetch full raw HTML before checking schema
- [ ] Extract JSON-LD with proper regex (handle minified/multiline)
- [ ] Check homepage + at least 3 internal pages
- [ ] Validate schema types against Google Rich Results Test specs
- [ ] Compare vs. top 3 competitors' schema coverage
- [ ] Document exact schema types found (not just "has schema")
- [ ] Save audit output to `memory/audits/technical-seo/`

### Memory Update Rule
After any technical SEO audit, append findings to:
- `memory/audits/technical-seo/<date>-<domain>-schema-audit.md`
- Include: schema types found, missing types, competitor gaps, fix priorities
