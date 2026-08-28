---
name: rankray-seo-technical-mastery
description: "Master playbook for RankRay forensic technical SEO audits, Google Search Console forensics, Core Web Vitals, programmatic location pages, and Schema JSON-LD engineering."
---

# 🔍 RankRay Technical SEO & Forensic Audit Mastery

> **The Single Source of Truth for Technical Architecture, Crawl Forensics, GSC Analysis, Schema, and Programmatic SEO.**

---

## 🛠️ 1. Forensic Technical Audit Framework

### 🚦 The 5 Core Diagnostic Pillars:
1. **Crawlability & Indexation:**
   - Detect non-indexed URLs, orphaned pages, soft 404s, and faceted navigation crawl traps.
   - Audit `robots.txt` and `sitemap.xml` for bloated URL parameters.
2. **JavaScript Rendering & DOM Hydration:**
   - Verify that Googlebot renders critical text, links, and schema without client-side execution delays.
3. **Core Web Vitals (CWV):**
   - Largest Contentful Paint (LCP) < 2.5s.
   - Interaction to Next Paint (INP) < 200ms.
   - Cumulative Layout Shift (CLS) < 0.1.
4. **Information Architecture & Canonicalization:**
   - Resolve self-referencing canonical discrepancies, trailing slash redirection chains, and HTTP/HTTPS mixed content.
5. **Toxic Link & Algorithmic Penalty Forensics:**
   - Detect negative SEO spikes, spam backlink networks, and unlinked brand mentions.

---

## 📊 2. Google Search Console (GSC) & CTR Forensics

- **Page 2 Money Keyword Striking Distance:**
  - Query GSC for URLs ranking #8–18 with high impressions (>500/month) and low CTR (<3%).
  - Action: Update `<h1>`, meta title, introductory paragraph, and build 2 contextual internal links from high-authority pages.
- **Cannibalization Resolution:**
  - Detect multiple URLs competing for the same primary search query in GSC.
  - Action: Consolidate content into the primary URL and 301-redirect or re-canonicalize the secondary page.

---

## 🗺️ 3. Programmatic SEO & Multi-Location Clusters

- **Geographic URL Structure:** `domain.com/[service]-[city]/` (e.g. `/physiotherapy-milton/`).
- **Dynamic Variable Injection:**
  - `{{city_name}}`, `{{region}}`, `{{postal_code}}`, `{{driving_directions}}`, `{{landmarks}}`.
- **Unique Value Proof:** Every programmatic page must contain distinct customer reviews, local map embeds, and local case studies to avoid thin/doorway page penalties.

---

## 📐 4. Schema JSON-LD Engineering

Always inject comprehensive, valid JSON-LD schemas into page headers:
- `Organization` & `WebSite` sitewide.
- `LocalBusiness` / `MedicalBusiness` / `FinancialService` on location and service pages.
- `FAQPage` schema on guides and transactional landing pages.
- `BreadcrumbList` schema for SERP snippet enhancement.
