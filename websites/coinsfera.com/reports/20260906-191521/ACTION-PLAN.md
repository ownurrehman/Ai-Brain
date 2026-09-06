> **Parent Hub:** [[websites/coinsfera.com/INDEX|🌐 coinsfera.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# ACTION PLAN — https://coinsfera.com

Stamp: `20260906-191521` (Asia/Karachi). Priorities derived only from script evidence in this directory. No invented CWV/GSC. No Rank Ray work.

## P0 — Unblock crawl / audit access

1. **Fix HTTP 403 for legitimate bots and SEO tooling**  
   - Evidence: `redirect_checker.out.txt` (403), `parse_html.out.txt` (title/H1 `403 - Forbidden`, `meta_robots: noindex`), `robots_checker` / `sitemap_checker` / `social_meta` all 403.  
   - Action: Adjust WAF/CDN bot rules; allowlist Googlebot/Bingbot; verify with a clean 200 HTML fetch.  
   - Done when: `fetch_page.py` + `parse_html.py` no longer show Forbidden interstitial.

2. **Restore robots.txt 200**  
   - Evidence: `robots_checker.out.txt` status 403.  
   - Action: Publish robots.txt with sitemap directive and intentional AI-crawler rules.

3. **Restore sitemap XML 200**  
   - Evidence: `sitemap_checker.out.txt` — three candidate URLs all HTTP 403, 0 URLs inventoried.  
   - Action: Serve canonical sitemap; link from robots.txt; re-run `sitemap_checker.py`.

## P1 — Security headers on responses

4. **Add six missing security headers**  
   - Evidence: `security_headers.out.txt` score 25; missing HSTS, CSP, X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy.  
   - Action: Implement at CDN/origin per script recommendations; re-run `security_headers.py` aiming for substantially higher score.

## P2 — AI / social readiness (after 200 HTML)

5. **Publish llms.txt (and optionally llms-full.txt)**  
   - Evidence: `llms_txt_checker.out.txt` exists=false, status 403.  
   - Action: Add curated site description + key URLs once root is publicly readable.

6. **Validate Open Graph / Twitter Card on real HTML**  
   - Evidence: `social_meta.out.txt` error HTTP 403 (incomplete measurement).  
   - Action: Re-run `social_meta.py` after unblock; fill any missing tags.

7. **Add Organization (and page) JSON-LD; validate**  
   - Evidence: `validate_schema.py` exit 0 with no blocks; `parse_html` `schema: []` on fetched body.  
   - Action: Implement schema on real templates; re-run `validate_schema.py`.

## P3 — Measurement & tooling

8. **Re-run PageSpeed with API key or after cooldown**  
   - Evidence: `pagespeed.out.txt` rate-limit error; no metrics.  
   - Action: Set `PAGESPEED_API_KEY` / `--api-key`; capture mobile CWV — do not guess numbers.

9. **Patch audit_runner HTML path for null canonical**  
   - Evidence: `audit_runner.err.txt` TypeError on `canonical` None.  
   - Action: Fix `generate_report.py` null-safe slice; re-run `audit_runner.py` for HTML dashboard.

## Explicit non-actions this run

- Do **not** treat thin-content / title-length / entity “403” Wikidata matches as homepage SEO truth until unblock.  
- Do **not** invent Core Web Vitals or GSC data.  
- Do **not** implement Rank Ray.
