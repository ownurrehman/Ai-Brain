> **Parent Hub:** [[websites/coinsfera.com/INDEX|🌐 coinsfera.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# FULL AUDIT REPORT — https://coinsfera.com

| Field | Value |
|-------|--------|
| Audit stamp | `20260906-191521` (Asia/Karachi) |
| Target | https://coinsfera.com |
| Skill | Ai Brain Agentic SEO (`SKILL_DIR` …/.agents/skills/seo) |
| Runner | `/opt/homebrew/bin/python3.12` on Own's Mac |
| Evidence basis | Bundled skill scripts only (see `RUN-LOG.md`) |
| audit_runner overall score (JSON) | **57** (`audit-results.json`) — treat with caution: many category scores were computed against a **403** response body |

**Confidence legend**

- **Confirmed** — Directly observed in script stdout/JSON for this run.
- **Likely** — Strongly implied by Confirmed evidence; may still be WAF/CDN behavior rather than intentional SEO config.
- **Hypothesis** — Contaminated by 403 interstitial HTML or incomplete APIs; do not treat as true on-page SEO of the public homepage until a non-blocked fetch succeeds.
- **Unknown** — Not measured (API/env limitation).

---

## Executive summary

Automated skill scripts largely **could not retrieve the real homepage**. `fetch_page.py` saved HTML whose parsed title/H1 is **`403 - Forbidden`** with `meta robots=noindex` (`parse_html.out.txt`). Concurrent checks against `/robots.txt`, sitemaps, social meta, and redirects also report **HTTP 403**. PageSpeed Insights returned **no CWV metrics** due to Google API rate limiting. Therefore this report prioritizes **access/crawlability and security-header evidence**, and labels classic on-page findings as **Hypothesis** unless they describe the 403 page itself.

No Google Search Console data and no Core Web Vitals numbers are claimed. No Rank Ray implementation was performed.

---

## Finding 1 — Site returns HTTP 403 to skill fetchers (blocking)

| | |
|--|--|
| **Severity** | Critical |
| **Confidence** | Confirmed |
| **Evidence** | `redirect_checker.out.txt`: chain step status **403**, `total_hops: 0`. `parse_html.out.txt`: `"title": "403 - Forbidden"`, `"h1": ["403 - Forbidden"]`, `"meta_robots": "noindex"`, `word_count: 13`. `fetch_page.out.txt`: Saved `page.html`. `robots_checker.out.txt`: `"status": 403`, `"error": "HTTP 403"`. `social_meta.out.txt`: `"error": "HTTP 403"`. `sitemap_checker.out.txt`: sitemap.xml / sitemap_index.xml / sitemap-index.xml all **status 403**. `audit-results.json` → `broken_links.error`: `"Failed to fetch page: HTTP 403"`. |
| **Impact** | Search engines / AI crawlers using similar bot signatures may be blocked the same way; SEO audits and indexing signals become unreliable. The saved document is an error interstitial, not marketing content. |
| **Fix** | Allowlist reputable crawlers (Googlebot, Bingbot, etc.) and optionally SEO audit User-Agents at WAF/CDN; verify with live Googlebot fetch / URL Inspection after change. Re-run this skill suite once HTML title is no longer `403 - Forbidden`. |

---

## Finding 2 — robots.txt unreachable (403)

| | |
|--|--|
| **Severity** | Critical |
| **Confidence** | Confirmed (for this client) |
| **Evidence** | `robots_checker.out.txt`: url `https://coinsfera.com/robots.txt`, status **403**, empty `user_agents` / `sitemaps` / `ai_crawler_status`, error `"HTTP 403"`. |
| **Impact** | Cannot verify crawl allowances, sitemap declarations, or AI-crawler policy from robots.txt during this run. |
| **Fix** | Ensure `robots.txt` returns **200** to bots and auditors; declare sitemap URL(s); document AI crawler policy intentionally. |

---

## Finding 3 — XML sitemaps return HTTP 403

| | |
|--|--|
| **Severity** | High |
| **Confidence** | Confirmed (for checked URLs) |
| **Evidence** | `sitemap_checker.out.txt`: issues — `"Sitemap returned HTTP 403"` for `…/sitemap.xml`, `…/sitemap_index.xml`, `…/sitemap-index.xml`; `summary.urls: 0`, `summary.issues: 3`. |
| **Impact** | Sitemap discovery/validation failed; index coverage cannot be assessed from this run. |
| **Fix** | Serve a valid XML sitemap at a stable URL with **200**, reference it from robots.txt, and re-run `sitemap_checker.py`. |

---

## Finding 4 — Six security headers missing (score 25)

| | |
|--|--|
| **Severity** | High |
| **Confidence** | Confirmed (headers on response observed by script; note response was also 403) |
| **Evidence** | `security_headers.out.txt`: `"score": 25`, `"https": true`, `headers_present: {}`, `headers_missing` lists HSTS, CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy; issue: `"6 security headers missing — poor security posture"`. Matches `audit-results.json` security section. |
| **Impact** | Weaker browser security posture (clickjacking, MIME sniffing, XSS mitigation gaps, no HSTS). |
| **Fix** | At CDN/origin, add the headers recommended in script output (HSTS max-age=31536000; includeSubDomains; CSP; X-Frame-Options: SAMEORIGIN; X-Content-Type-Options: nosniff; Referrer-Policy: strict-origin-when-cross-origin; Permissions-Policy for camera/microphone/geolocation). |

---

## Finding 5 — No llms.txt / llms-full.txt available to checker

| | |
|--|--|
| **Severity** | Medium |
| **Confidence** | Likely (exists=false with status 403 — could be block or true absence) |
| **Evidence** | `llms_txt_checker.out.txt`: `"exists": false`, `"full_exists": false`, `"status": 403`, `"full_status": 403`, quality score 0. |
| **Impact** | No curated AI-readable site map for assistants; cannot confirm whether files exist behind the block. |
| **Fix** | After unblocking, add `/llms.txt` (and optionally `/llms-full.txt`) with site summary and key URLs; re-run checker expecting 200. |

---

## Finding 6 — Social meta check failed with HTTP 403

| | |
|--|--|
| **Severity** | Medium |
| **Confidence** | Confirmed failure to measure; **Hypothesis** for real OG/Twitter completeness |
| **Evidence** | `social_meta.out.txt`: score 0, empty og/twitter tags, `"error": "HTTP 403"`. |
| **Impact** | Share previews cannot be validated in this run. |
| **Fix** | Unblock fetchers; ensure og:title, og:description, og:image, og:url, twitter:card present on real HTML; re-run `social_meta.py`. |

---

## Finding 7 — Fetched document has no JSON-LD schema

| | |
|--|--|
| **Severity** | Medium (for interstitial); Unknown for real homepage |
| **Confidence** | Confirmed for `page.html`; **Hypothesis** for production homepage |
| **Evidence** | `validate_schema.py` exit **0**, empty stdout (no JSON-LD blocks → not an error per script). `parse_html.out.txt`: `"schema": []`. `audit-results.json` article/entity: no Organization/Person JSON-LD on fetched body. |
| **Impact** | Error page has no structured data. Real-site schema remains unverified. |
| **Fix** | After 200 HTML fetch, add Organization (and page-appropriate types) JSON-LD; re-run `validate_schema.py` + schema sub-checks. |

---

## Finding 8 — Redirect checker: no chain, terminal 403

| | |
|--|--|
| **Severity** | Info / context |
| **Confidence** | Confirmed |
| **Evidence** | `redirect_checker.out.txt`: `total_hops: 0`, `has_loop: false`, `has_mixed_protocol: false`, final status **403**. |
| **Impact** | No redirect-loop or mixed HTTP/HTTPS hop detected; primary problem is **403**, not redirect hygiene. |
| **Fix** | Resolve 403 first; then re-check www/apex and http→https separately if needed. |

---

## Finding 9 — audit_runner HTML generation crashed (tooling)

| | |
|--|--|
| **Severity** | Tooling / Medium for automation |
| **Confidence** | Confirmed |
| **Evidence** | Exit code **1**. `audit_runner.err.txt`: `TypeError: 'NoneType' object is not subscriptable` at `generate_html` when slicing `canonical` that is `None` (`parse_html` / onpage: `"canonical": null`). JSON still written. |
| **Impact** | Default HTML/markdown artifacts from audit_runner were not produced; this manual report substitutes. |
| **Fix** | Patch `generate_report.py` to coerce `None` canonical before `[:80]`; re-run audit_runner after site returns 200. |

---

## Contaminated / do-not-trust as homepage SEO

The following appear in `audit-results.json` but describe the **403 interstitial** (or mis-parse of it). Confidence: **Hypothesis** / contaminated — listed for transparency, **not** as confirmed site content issues:

- Title `"403 - Forbidden"`, thin word counts (1–13), missing meta description, noindex robots meta on the error page (`parse_html.out.txt`, article/readability sections).
- Entity checker treating entity_name `"403"` and matching Wikidata/Wikipedia for the natural number 403 (`audit-results.json` entity section) — **false positive**.
- Internal/link_profile “dead end” / 0 internal links — expected on an error page with no links.
- Category scores such as social 0, robots 0, pagespeed 0, entity 0 inside overall **57** — skewed by blocks and API limits.

---

## Environment Limitations

1. **HTTP 403 / bot protection** — Primary limitation. Most URL-based scripts received 403; `page.html` is a Forbidden interstitial with `noindex`, not the live marketing homepage.
2. **PageSpeed Insights rate limit** — `pagespeed.out.txt`: `"error": "Rate limited by Google API. Wait a few minutes or add an API key."`; `performance_score: null`, `metrics: {}`, `field_data_available: false`. **No LCP/INP/CLS invented.**
3. **Google Knowledge Graph** — `audit-results.json` entity.google_kg: `"checked": false`.
4. **audit_runner report writer bug** — HTML (and thus its bundled markdown paths) failed after JSON write (`audit_runner.err.txt`).
5. **No GSC / Rank Ray** — Not run; no Search Console or Rank Ray metrics cited.
6. **llms.txt / robots / sitemap “missing”** — Status 403 means absence vs block cannot be fully separated without an allowlisted fetch.

---

## Category scores (from audit_runner JSON only)

Source: `audit-results.json` → `scores` (interpret with Environment Limitations above):

| Category | Score |
|----------|------:|
| overall | 57 |
| security | 25 |
| social | 0 |
| robots | 0 |
| article | 50 |
| broken_links | 100 |
| internal_links | 100 |
| redirects | 100 |
| llms_txt | 0 |
| pagespeed | 0 |
| onpage | 75 |
| readability | 100 |
| entity | 0 |
| link_profile | 52 |
| hreflang | 0 |
| duplicate_content | 100 |

Note: Several “100” scores (e.g. broken_links with fetch error, redirects with terminal 403) are **not** evidence of a healthy public homepage.

---

## Next verification gate

Re-run the same stamp workflow when `parse_html.py` reports a real commercial title (not `403 - Forbidden`) and `robots_checker.py` returns status **200**. Only then promote on-page, schema, social, sitemap URL inventory, and CWV findings to Confirmed.
