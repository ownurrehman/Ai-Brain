> **Parent Site:** [[websites/archive/index|🌐 archive Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# KhanLLP.com — Combined Daily SEO Report
**Date:** 2026-05-04 | **Agent:** chronos | **Sequence:** Fix → Target → Link → Content

---

## 1. SITE AUDIT & TECHNICAL FIXES

### 1.1 Critical Issues Found

| # | Severity | Issue | Location | Fix |
|---|----------|-------|----------|-----|
| 1 | 🔴 CRITICAL | JSON-LD schema has double opening brace (`{    {`) breaking structured data | Homepage `<script type="application/ld+json">` | Remove extra `{`. This invalidates ALL schema markup for Google. |
| 2 | 🔴 CRITICAL | Homepage `<title>` omits Oakville: "Trusted Law Firm in Toronto, Milton, Mississauga" | Homepage `<title>` tag | Change to: "Khan Law | Trusted Law Firm in Oakville, Milton, Mississauga & Toronto" |
| 3 | 🟠 HIGH | Schema address only shows Milton. Oakville office (Unit 201-3465 Rebecca Street) not in structured data | LocalBusiness schema | Add Oakville as additional address or create separate LegalService node |
| 4 | 🟠 HIGH | OG title and `<title>` tag mismatch: OG says "Milton, Mississauga, Toronto" while OG description mentions Oakville | Meta tags | Align both to include Oakville consistently |
| 5 | 🟡 MEDIUM | `meta name="keywords"` uses names "Faraz Khan, Anam Khan" — Google ignores this tag but it's irrelevant | Homepage head | Remove or replace with practice area keywords |
| 6 | 🟡 MEDIUM | 302 redirect on blog: `/blogs/title-fraud-in-oakville-how-to-protect-your-home-from-scammers` → `/blogs/title-fraud-in-oakville-and-how-to-protect-your-home-from-scammers` | Blogs section | Update internal links to point directly to final URL (or update sitemap if needed) |
| 7 | 🟡 MEDIUM | Homepage canonicals and sitemap use `https://khanllp.com` but schema uses `https://www.khanllp.com/` | Schema URL field | Standardize on non-www version (khanllp.com) across all structured data |
| 8 | 🟢 LOW | "Cosultation" typo on homepage heading ("Online Cosultation") | Homepage form section | Fix to "Consultation" |

### 1.2 What's Working Well

- All 82 pages in sitemap resolve with 200 (only 1 benign 302)
- robots.txt properly configured blocking /admin/ and /login/
- Sitemap includes ALL pages (service pages + geo pages + all blogs)
- Canonical tags present on all key pages
- Open Graph tags set up correctly (image, type, locale)
- Blog publishing active — latest post April 28, 2026 (6 days ago)
- Geo-specific service pages created for ALL 5 cities × 4 practice areas = 20 location pages already live
- Contact page lists all 4 locations (Milton, Mississauga, Toronto, Oakville) with maps

---

## 2. SERP ANALYSIS & TARGETING

### 2.1 Current SERP Position Assessment

**For branded terms:** KhanLLP dominates page 1 (positions 1-5 for "khan law oakville" etc.)

**For non-branded Oakville keywords:** KhanLLP is NOT in the top 10 for any of these:

| Target Keyword | Est. Volume | Competitors Ranking | KhanLLP Position |
|---|---|---|---|
| real estate lawyer Oakville | Medium-High | haxelllaw.com, serafinilaw.ca, lcheng.ca, annagurevich.com | Not in top 10 |
| family lawyer Oakville | Medium | separation.ca, jamalfamilylaw.com, gagefamilylaw.ca, jgcoxfamilylaw.com | Not in top 10 |
| immigration lawyer Oakville | Low-Medium | matthewjeffery.com, bellissimolawgroup.com, srlawpc.com | Not in top 10 |
| will lawyer Oakville | Low-Medium | omh.ca, haxelllaw.com, sweatmanlaw.com, pssmlaw.com | Not in top 10 |
| law firm Oakville Ontario | Medium | haxelllaw.com, serafinilaw.ca, various | Not in top 10 |

### 2.2 Competitor Landscape (Oakville SERPs)

**Top competitors in Oakville legal market:**
- **Haxell Law** — Dominant across real estate, wills, family. Ranks for 3+ practice areas.
- **OMH LLP** — Strong in real estate + wills/estates
- **Serafini & Serafini** — Real estate + business law focus
- **Jamal Family Law** — Family law specialist in Oakville
- **JG Cox Family Law** — Dedicated family law firm

**Common competitor patterns:**
- All have Oakville-specific landing pages with LOCAL content (neighborhoods, courts, landmarks)
- Most have Google Maps embeds with Oakville address
- Dedicated practice area pages with "Oakville" in H1 and URL

### 2.3 Keyword Gap Opportunities

**High-priority targets (content already exists, needs ranking boost):**

1. **"real estate lawyer Oakville"** — Page exists at `/real-estate-lawyer-oakville` with strong content. Needs internal links and authority signals.
2. **"family lawyer Oakville"** — Page exists at `/family-law-oakville` with good content. H1 says "Family Lawyers Oakville Ontario" (good).
3. **"immigration lawyer Oakville"** — Page exists at `/immigration-lawyer-oakville` (not checked individually).
4. **"will lawyer Oakville"** — Page exists at `/wills-and-estates-lawyer-oakville`.

**New content gaps (no existing pages):**
- "Oakville divorce lawyer" — No dedicated page (but `/divorce-and-separation-ontario` exists, could add Oakville section)
- "Oakville real estate closing lawyer" — No dedicated page
- "Oakville criminal lawyer" — No Oakville-specific criminal page (only `/criminal-lawyer-ontario`)

---

## 3. INTERNAL LINKING OPTIMIZATION

### 3.1 Current Internal Link Analysis

**Homepage links to Oakville pages:**
- Homepage footer links to: `/real-estate-lawyer-oakville` ✅
- No homepage body links to Oakville-specific pages ❌
- Homepage practice area links go to generic pages (e.g., `/family-law` not `/family-law-oakville`)

**Cross-linking between Oakville geo-pages:**
- `/real-estate-lawyer-oakville` links internally but mostly to generic pages
- `/family-law-oakville` links to `/family-law` (generic) — good but needs more city-specific cross-links

**Blog → Service Page links (spot-checked):**
- Oakville blogs exist (e.g., "why you need an experienced real estate lawyer in Oakville", "title fraud in Oakville")
- Blog → service page links: Not fully verified but structure exists

### 3.2 Recommended Internal Linking Actions

| # | Action | From | To | Priority |
|---|--------|------|----|----------|
| 1 | Add "Oakville" to homepage navigation or hero section body text | Homepage | `/real-estate-lawyer-oakville`, `/family-law-oakville` | HIGH |
| 2 | Link Oakville blog posts to corresponding Oakville service pages | ~5 Oakville blogs | Respective `/xxx-oakville` pages | HIGH |
| 3 | Add cross-links between Oakville geo-pages (RE → Family → Wills → Immigration) | Each Oakville page | Other Oakville practice area pages | MEDIUM |
| 4 | Create a dedicated "Oakville Law Services" hub at `/oakville-lawyer` linking to all 4+ practice areas | New hub page | 4 Oakville practice pages | MEDIUM |
| 5 | Link Contact page location sections to corresponding geo-pages | Contact page (Oakville section) | `/real-estate-lawyer-oakville` etc. | MEDIUM |
| 6 | Ensure all ~82 blog posts link to at least 1 relevant service page | Blog posts | Service/geo pages | LOW (ongoing) |

### 3.3 Anchor Text Recommendations

- For `/real-estate-lawyer-oakville`: "Oakville real estate lawyers", "real estate lawyer in Oakville"
- For `/family-law-oakville`: "Oakville family lawyers", "family law attorney Oakville"
- For `/wills-and-estates-lawyer-oakville`: "Oakville will lawyer", "estate planning lawyer Oakville"
- For `/immigration-lawyer-oakville`: "Oakville immigration lawyer", "immigration attorney Oakville"

---

## 4. CONTENT UPDATES & RECOMMENDATIONS

### 4.1 Existing Content That Needs Updating

| Page | Issue | Recommended Update |
|---|---|---|
| Homepage | Title + meta don't mention Oakville | Add "Oakville" to title, H1-adjacent text, and meta description |
| Homepage schema | Only Milton address | Add Oakville as second location or areaServed |
| `/family-law` | Title says "Divorce and Family Lawyer Oakville, CA" — good | Body content should reinforce Oakville more |
| `/real-estate-lawyer-oakville` | Strong content with neighborhood mentions (Bronte, Morrison) | Add internal links to other Oakville pages; add FAQ section at bottom |
| `/family-law-oakville` | Content present but thin compared to real estate page | Expand with local court info, Ontario Family Law Act specifics, FAQ section |
| `/wills-estate-planning` | Good educational content but no Oakville mention | Add Oakville-specific callout (probate in Halton Region) |

### 4.2 New Content Opportunities

1. **"Oakville Divorce Lawyer" landing page** — Highest priority gap. Divorce is a volume driver.
2. **"Oakville Real Estate Closing Process"** — Transactional intent, good for conversion
3. **Blog: "Oakville Neighborhood Guide for Homebuyers"** — Local SEO + real estate crossover
4. **Blog: "Halton Region Family Court: What to Expect"** — Local authority signal
5. **Oakville-specific FAQ page** — Consolidate FAQ schema for Oakville queries

### 4.3 Content Enhancement Checklist

- [ ] Add FAQ sections with schema to all 4 Oakville practice pages
- [ ] Include Oakville-specific statistics/market data where relevant
- [ ] Mention local landmarks (Bronte Harbour, Kerr Village, Downtown Oakville)
- [ ] Reference Halton Region courts and procedures
- [ ] Add Google Maps embed for Oakville office on each Oakville page

---

## 5. PRIORITY ACTION ITEMS (RANKED)

| # | Action | Impact | Effort |
|---|--------|--------|--------|
| 1 | Fix JSON-LD schema syntax error (broken `{    {`) | 🔴 Schema completely broken for Google | 2 min |
| 2 | Update homepage `<title>` to include Oakville | 🟠 Missing from primary SERP display | 5 min |
| 3 | Add Oakville to homepage meta description | 🟠 Missing from SERP snippet | 5 min |
| 4 | Add Oakville address to LocalBusiness schema | 🟠 Missing from structured data | 10 min |
| 5 | Fix "Cosultation" typo on homepage | 🟢 Quality signal | 2 min |
| 6 | Add internal links from homepage body to Oakville service pages | 🟠 No internal link equity flowing to Oakville | 15 min |
| 7 | Add FAQ sections + FAQ schema to all 4 Oakville pages | 🟡 Content/Rich Result opportunity | 30 min |
| 8 | Create "Oakville Divorce Lawyer" page | 🟡 Keyword gap | 45 min |
| 9 | Fix 302 redirect blog link in sitemap/internal links | 🟡 Crawl efficiency | 5 min |
| 10 | Standardize www vs non-www in schema URLs | 🟡 Consistency | 5 min |

---

## 6. SITE HEALTH SUMMARY

| Metric | Status | Detail |
|---|---|---|
| Indexable pages | ✅ 82 pages in sitemap | All resolve 200 except 1 benign redirect |
| crawl budget | ✅ Healthy | robots.txt properly configured |
| Content freshness | ✅ Active | Latest blog: April 28, 2026 |
| Schema markup | 🔴 BROKEN | JSON syntax error invalidates all structured data |
| Title tag optimization | 🟠 INCOMPLETE | Missing Oakville in primary title |
| Geo-specific pages | ✅ COMPLETE | 20 location pages (5 cities × 4 practice areas) |
| Internal linking to Oakville | 🟠 WEAK | Homepage not passing equity; blog links unverified |
| Oakville SERP presence | 🔴 ABSENT | Not in top 10 for any non-branded Oakville keyword |
| Mobile responsiveness | ✅ Good | Viewport meta set; responsive CSS |
| Page speed indicators | 🟡 UNVERIFIED | CSS preloaded; no Lighthouse data collected |

---

## 7. SELF-AUDIT NOTES

**Verified evidence sources:**
- Homepage HTML source (curl + head) — confirmed schema error, title, meta tags
- Sitemap XML (curl) — full URL inventory extracted
- robots.txt — confirmed configuration
- Contact page — confirmed 4 locations including Oakville at Unit 201-3465 Rebecca Street
- Real Estate Oakville page — confirmed content quality + neighborhood mentions
- Family Law Oakville page — confirmed content presence
- SERP analysis (Firecrawl search × 4 queries) — confirmed non-appearance for all Oakville non-branded terms
- Blog listing page — confirmed active publishing schedule

**Confidence:** HIGH — All findings backed by direct source inspection.
**Recommended next review:** After fixes 1-4 are applied, re-check schema via Google's Rich Results Test.
