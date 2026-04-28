# Coinsfera Schema Audit — 2026-04-21

## Verdict
**Schema IS present** on homepage (Yoast SEO JSON-LD). Initial audit error was due to incorrect detection method.

## Schema Types Found (Homepage)

| Schema Type | Status | Details |
|-------------|--------|---------|
| LocalBusiness/FinancialService | ✅ Present | Full NAP, phone, address, geo coordinates |
| WebPage | ✅ Present | Yoast standard |
| WebSite | ✅ Present | With SearchAction |
| BreadcrumbList | ✅ Present | Single item (Home) |
| AggregateRating | ✅ Present | 4.9/5 from 998 reviews |
| Review | ✅ Present | 5 recent reviews included |
| OpeningHoursSpecification | ✅ Present | Mon-Fri 09:00-18:00, Sat 09:00-15:00 |
| GeoCoordinates | ✅ Present | 41.0238, 28.9772 |
| Service | ❌ Missing | Not on homepage or service pages |
| FAQPage | ❌ Missing | FAQ content exists but not marked up |
| Product | ❌ Missing | No Product schema for crypto services |

## Detection Error (Root Cause Analysis)

**What Went Wrong:**
- Initial check used `grep -i -E '(schema|json-ld|microdata)'` which missed minified JSON-LD
- Didn't extract `<script type="application/ld+json">` blocks properly
- Only checked homepage, didn't verify service pages

**Correct Method (Now Documented):**
```bash
curl -sL <URL> | grep -o '<script[^>]*type="application/ld+json"[^>]*>[^<]*</script>'
```

## Service Page Audit — COMPLETED

### `/services/usdt/` — ❌ CRITICAL ISSUE
**Status:** Redirects to Russian blog post instead of service page
**Redirect Target:** `/ru/блоги/usdt-против-usdc-что-выбрать/` (USDT vs USDC blog article)
**Schema Found:** Article schema (blog post), NOT Service schema
**Impact:** Users seeking USDT service land on unrelated blog content

**Schema Present (on blog post):**
- Article (Russian language)
- WebPage
- BreadcrumbList
- WebSite (Russian version)
- **Missing:** LocalBusiness, Service, FinancialService

### `/sell-bitcoin-in-istanbul/` — ✅ GOOD
**Schema Types Present:**
- WebPage ✅
- WebSite ✅
- BreadcrumbList ✅
- LocalBusiness/FinancialService ✅ (inherited from site-wide)
- **Service** ✅ (page-specific)
  - Name: "Sell Bitcoin in Istanbul"
  - ServiceType: "Cryptocurrency Exchange"
  - AreaServed: Istanbul
  - Offers: Free (price: 0 USD)

### `/buy-bitcoin-in-istanbul/` — ✅ GOOD
**Schema Types Present:**
- WebPage ✅
- WebSite ✅
- BreadcrumbList ✅
- LocalBusiness/FinancialService ✅ (inherited from site-wide)
- **Service** ✅ (page-specific)
  - Name: "Buy Bitcoin in Istanbul"
  - ServiceType: "Cryptocurrency Exchange"
  - AreaServed: Istanbul
  - Offers: Free (price: 0 USD)

## Competitor Schema Comparison — PENDING

**Top Ranking Competitors (via OpenSERP logs):**
OpenSERP is running but experiencing timeout issues on concurrent requests. Manual SERP check shows:
1. coinsfera.com (ranks #1 for own brand)
2. f6s.com/company/coinsfera-crypto-exchange-in-istanbul
3. cryptonvg.com/exchange/coinsfera-istanbul-necatibey
4. bitcoinwide.com/coinsfera-...

**Competitor Schema Check:**
- cryptonvg.com: No JSON-LD schema detected
- bitcoinwide.com: No JSON-LD schema detected

**Advantage:** Coinsfera has superior schema coverage vs. competitors.

## Competitor Schema Comparison (Pending)

**Top Ranking Competitors (via OpenSERP):**
1. f6s.com/company/coinsfera-crypto-exchange-in-istanbul
2. cryptonvg.com/exchange/coinsfera-istanbul-necatibey
3. bitcoinwide.com/coinsfera-...

**Action:** Fetch competitor schema markup and identify gaps.

## Fix Priorities

| Priority | Issue | Impact | Effort |
|----------|-------|--------|--------|
| P0 | `/services/usdt/` redirects to Russian blog instead of service page | Critical UX + SEO loss | Medium |
| P1 | Add FAQPage schema to homepage FAQ section | FAQ rich results | Low |
| P2 | Add Product schema for USDT/BTC exchange services | Product rich results | Medium |
| P3 | Ensure all service pages have Service schema (some already do) | Rich snippets consistency | Low |

## Service Schema Coverage Summary

| Page | Service Schema | LocalBusiness | Status |
|------|---------------|---------------|--------|
| Homepage | ❌ | ✅ | Partial |
| /sell-bitcoin-in-istanbul/ | ✅ | ✅ | Complete |
| /buy-bitcoin-in-istanbul/ | ✅ | ✅ | Complete |
| /services/usdt/ | ❌ (redirects) | ❌ | Broken |

**Gap:** Homepage lacks Service schema despite being the main service entry point.

## Tools Used

- **OpenSERP**: `http://127.0.0.1:7070` (rotating proxies, 10 entries)
- **Location**: `/tmp/openserp/`
- **Fetcher Script**: `/workspace/semantic-engine/scripts/openserp-fetcher.py`

## Next Actions

1. ✅ Update MEMORY.md with schema audit lessons
2. ✅ Update technical-seo-checker skill with correct detection method
3. ✅ Audit service pages for schema gaps (completed)
4. ⏳ Fix `/services/usdt/` redirect issue (P0)
5. ⏳ Add FAQPage schema to homepage FAQ section
6. ⏳ Add Product schema for crypto exchange services
7. ⏳ Competitor schema comparison (preliminary: Coinsfera ahead)

---

**Auditor:** Ranki (main)
**Date:** 2026-04-21
**Status:** Partial (homepage complete, service pages pending)
