# Rank Ray Week 1 Enigma — 2026-09-07

**Status:** DONE (REST-only)  
**Cold email:** CLOSED (no outreach copy)  
**justccell:** not used  
**Auth:** `/workspace/week1-wp.env` (RANKRAY_WP_USER + RANKRAY_WP_APP_PASS; base https://rankray.com)  
**Completed (PKT):** 2026-09-07 05:27 PKT

## Scout evidence (GSC 2026-08-08 → 2026-09-04)

Cited Scout money-page gaps (impressions with ~0 clicks / weak position):

| URL | Clicks | Impr. | Avg pos | Action this pass |
|---|---:|---:|---:|---|
| https://rankray.com/seo-agency-dubai/ | 0 | 682 | 26.4 | Polished title/H1/intro/Yoast + listicle links |
| https://rankray.com/digital-marketing-services/local-seo/ | 0 | 174 | 44.1 | Polished title/H1/excerpt/Yoast + listicle links |
| https://rankray.com/digital-marketing-agency-sydney/ | 0 | 1020 | 50.8 | Polished title/H1/intro/Yoast + listicle links |

Listicle still dominates clicks (**5,214 clicks** on best-200 profile-creation post). Ottawa / NY / core SEO noted weak by Scout — **not required this pass**. No new listicles.

---

## 1) Listicle (source of internal links)

| Field | Before | After |
|---|---|---|
| URL | https://rankray.com/blog/best-200-profile-creation-backlinks/ | same |
| WP ID | 5410 | 5410 |
| Slug | `best-200-profile-creation-backlinks` | same |
| Title | TOP 200 SITES FOR FREE PROFILE BACKLINKS | unchanged (not a money page) |
| Modified (UTC) | 2026-07-27T10:23:16 | 2026-09-07T05:26:37 |

### Contextual money links added/fixed (FROM listicle → money pages)

Natural anchors (spammy relative stubs cleaned earlier in-session):

1. **Local SEO** → https://rankray.com/digital-marketing-services/local-seo/  
   - Facebook profile paragraph (map-pack / nearby-search context)  
   - Conclusion growth-plan sentence  
2. **SEO agency in Dubai** → https://rankray.com/seo-agency-dubai/  
   - Conclusion + related geo anchors live  
3. **digital marketing agency in Sydney** → https://rankray.com/digital-marketing-agency-sydney/  
   - Conclusion + related geo anchors live  

Also retained/cleaned supporting service links (SEO services, technical SEO, content marketing/writing) — not additional listicles.

---

## 2) Money page polish (proof-led, Rank Ray US English, results-first)

### A) Dubai — `location-page` ID 18020

| Field | Before | After |
|---|---|---|
| URL | https://rankray.com/seo-agency-dubai/ | same |
| WP title | SEO Agency in Dubai | SEO Agency Dubai — Bilingual Rankings That Drive Revenue |
| ACF `location_h1` (live H1) | Dubai SEO Agency for Businesses Targeting the Middle East's Most Competitive Markets | SEO Agency Dubai for Operators Who Need Leads From English and Arabic Search |
| Yoast title | SEO Agency Dubai: Rank Higher \| Rank Ray | SEO Agency Dubai \| Bilingual SEO Tied to Leads \| Rank Ray |
| Yoast metadesc | Hire the top SEO agency Dubai… Contact us today. (vanity “top”) | SEO agency Dubai for bilingual English/Arabic growth: map DIFC–Marina demand, fix mobile-first tech, and earn regionally relevant links that drive qualified leads. |
| Excerpt | SEO Agency in Dubai for local SEO, technical SEO, and lead generation. Rank Ray. | Rank Ray is the SEO agency Dubai operators hire when English and Arabic search both need to produce pipeline… |
| Live verify | — | Title/meta/H1 match REST (2026-09-07) |

Body: ACF-driven template (empty `content.raw`; Elementor data empty). H1/intro updated via ACF REST.

### B) Local SEO — page ID 12502

| Field | Before | After |
|---|---|---|
| URL | https://rankray.com/digital-marketing-services/local-seo/ | same |
| WP title | Local SEO | Local SEO Services That Drive Calls, Bookings, and Foot Traffic |
| Live H1 | Local SEO (thin) | Local SEO Services That Drive Calls, Bookings, and Foot Traffic |
| Yoast title | Local Search Engine Optimization Agency \| Local SEO Services | Local SEO Services \| Map Pack Growth Tied to Revenue \| Rank Ray |
| Yoast metadesc | Rank Ray offers top-notch local SEO services… (fluff, missing commas) | Rank Ray Local SEO wins map-pack and local search visibility that drives calls, bookings, and foot traffic—GBP, location pages, citations, and reviews built for operators. |
| Excerpt | (empty / thin related-services stub) | Operators-focused excerpt: nearby demand → calls/bookings/foot traffic |
| `content.raw` stub | Thin “Related SEO Services” blur | Proof-led intro stub + related SEO/Franchise links |

**Alpha blocker:** Page is Elementor (`_elementor_edit_mode=builder`). Full visual body/widgets are Elementor-owned. REST updated title, excerpt, content stub, and Yoast. If theme/Elementor later overrides H1 again, Alpha should align the Elementor heading widget to the new H1 copy.

### C) Sydney — `location-page` ID 17991

| Field | Before | After |
|---|---|---|
| URL | https://rankray.com/digital-marketing-agency-sydney/ | same |
| WP title | Digital Marketing Agency Sydney | Digital Marketing Agency Sydney — Pipeline From Local and APAC Demand |
| ACF `location_h1` (live H1) | Top-Rated Digital Marketing Agency in Sydney (vanity) | Digital Marketing Agency Sydney for Brands That Need Pipeline, Not Vanity Metrics |
| Yoast title | Digital Marketing Agency Sydney: Rank Higher \| Rank Ray | Digital Marketing Agency Sydney \| Revenue-Led Growth \| Rank Ray |
| Yoast metadesc | Hire the top digital marketing agency Sydney… Contact us today. | Digital marketing agency Sydney for finance, tech, property, and tourism brands: local SEO, multilingual content, and paid systems that create pipeline. |
| Excerpt | Template placeholder with `%customfield(location)%` / “#1” vanity | Pipeline-focused excerpt (local + APAC demand; revenue not traffic theater) |
| Live verify | — | Title/meta/H1 match REST (2026-09-07) |

---

## 3) Method notes

- **REST only** (Basic auth app password). No browser CMS edits. No secrets printed.
- SEO plugin on Rank Ray: **Yoast** (not Rank Math) — `_yoast_wpseo_*` fields.
- Dubai/Sydney are CPT **`location-page`** (`/wp-json/wp/v2/location-page/{id}`), not `pages`.
- No new listicles. No cold-email / outreach copy.

## 4) DONE checklist

- [x] Found live listicle `best-200-profile-creation-backlinks` (ID 5410)
- [x] Added contextual internal links FROM listicle TO Dubai + Local SEO + Sydney money pages
- [x] Polished three Scout-gap money pages (title/H1/intro/meta)
- [x] REST updates only; Elementor Local SEO body noted for Alpha
- [x] Reports written (box + Mac path)
- [x] Cold email CLOSED

## URLs (quick)

**Listicle:** https://rankray.com/blog/best-200-profile-creation-backlinks/  
**Dubai:** https://rankray.com/seo-agency-dubai/  
**Local SEO:** https://rankray.com/digital-marketing-services/local-seo/  
**Sydney:** https://rankray.com/digital-marketing-agency-sydney/
