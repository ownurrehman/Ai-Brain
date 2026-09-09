> **Parent Hub:** [[prompts/INDEX|🎯 Prompts Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# 🔍 Master Prompt: Forensic Technical SEO, AEO & UX Site Audit

**Role:** Senior Forensic Technical SEO Strategist, AEO/GEO Architect, and Web Performance Engineer.  
**Objective:** Execute a rigorous, multi-pillar forensic site audit for `[Site URL]` and generate a prioritized, developer-ready remediation report.

---

## 📋 Input Parameters (Fill Before Prompting)

* **Target Site URL:** `[e.g. https://justccell.com/ or target domain]`
* **CMS & Technology Stack:** `[e.g. WordPress + WooCommerce, Next.js App Router, or Shopify]`
* **Audit Scope:** `[Full Site / High-Priority Commercial Funnel / Specific Category]`
* **Primary Objective:** `[e.g. Traffic drop recovery, Core Web Vitals remediation, AEO/Perplexity citation capture, or Pre-launch QA]`
* **Output Destination:** `[e.g. websites/{domain}/reports/ or docs/]`

---

## ⚡ 1. Boot Sequence (Execute Before Running Audit)

1. **Locate Target Project:** Check `INDEX.md` and read `websites/{domain}/mastersheet.md` or `projects/{name}/mastersheet.md`.
2. **Review Credentials & Access:** If auditing via authenticated API, inspect `master-env.env` for stored application credentials.
3. **Review Established Rules:** Check target site's `rules.md` (e.g. Rule §0.8 ACFML fatal guard, Rule §1 100% backend editability).
4. **Load Production Skills:**
   * **Forensic Technical SEO:** `skills/rankray-technical-seo-audit/SKILL.md`
   * **GSC & Search Intelligence:** `skills/rankray-gsc-forensics/SKILL.md`
   * **Schema & Entity Architecture:** `skills/rankray-schema-jsonld/SKILL.md`
   * **Generative Engine Optimization (GEO):** `skills/rankray-geo-generative-engine-optimization/SKILL.md`
   * **Competitor Benchmarking:** `skills/rankray-competitor-intelligence/SKILL.md`

---

## 🔬 2. The 5-Pillar Forensic Audit Methodology

Examine the target domain across each of the following pillars:

### Pillar 1: Crawlability, Architecture & Indexation
* **`robots.txt` Health:** Verify accessibility (`HTTP 200`), proper sitemap declaration, and ensure CSS/JS assets are not blocked.
* **Sitemap Validation:** Check that XML sitemaps contain only canonical, indexable, `HTTP 200` URLs. Flag orphan pages or missing category hubs.
* **Crawl Budget & Redirect Loops:** Audit internal links for 301 redirect chains, 404 dead links, trailing slash discrepancies, or infinite faceted URL parameter loops.
* **Canonical Tag Integrity:** Ensure self-referencing canonicals exist on all primary pages. Check that parameterized URLs canonicalize to clean paths.

### Pillar 2: Technical Rendering & Core Web Vitals (CWV)
* **Raw vs. Rendered DOM Analysis:** Compare initial server response HTML against rendered DOM snapshots to ensure critical navigation, H1 headers, and internal links do not depend purely on client-side JS.
* **Core Web Vitals Thresholds:**
  * **LCP (Largest Contentful Paint):** Benchmark target < 2.5s. Inspect hero image preloads, formats (WebP/AVIF), and font rendering.
  * **INP (Interaction to Next Paint):** Benchmark target < 200ms. Identify long-running JS execution and un-debounced event listeners.
  * **CLS (Cumulative Layout Shift):** Benchmark target < 0.1. Verify explicit `width` and `height` dimensions on all images, videos, and dynamic ad containers.

### Pillar 3: Semantic Content, AEO & E-E-A-T Quality
* **Heading Hierarchy:** Verify exactly one `<h1>` per page with clean semantic `<h2>` and `<h3>` nesting.
* **Metadata Optimization:** Inspect title tags (< 60 chars) and meta descriptions (< 160 chars) for keyword intent alignment.
* **Answer Engine Optimization (AEO):** Check whether high-intent informational sections feature a 40–60 word concise direct answer for Google AI Overviews and Perplexity extraction.
* **E-E-A-T & Authority Signals:** Inspect author bios, verifiable entity citations, privacy policies, physical business addresses, and terms.
* **Content Hygiene:** Flag duplicate content, thin pages (< 300 words), or keyword cannibalization across URLs.

### Pillar 4: Structured Data & Entity Graph
* **Schema.org Deployment:** Check for valid, rich JSON-LD markup:
  * `Organization` & `LocalBusiness`
  * `WebSite` with SearchAction
  * `BreadcrumbList`
  * `Product` with nested `Offer` and `Review` (for e-commerce)
  * `Article` with author Person entity
  * `FAQPage`
* **Schema Validation:** Test for missing required properties, deprecations, or syntax errors.

### Pillar 5: Security, Mobile Usability & Conversion UX
* **Security Standards:** Strict HTTPS enforcement, zero mixed-content errors, and HTTP security headers (`Strict-Transport-Security`, `X-Content-Type-Options`).
* **Mobile Viewport Usability:** Ensure responsive layout across breakpoints, minimum touch target size $\ge$ 48×48px, and zero horizontal scroll.
* **Conversion Funnel Integrity:** Test primary conversion paths (lead capture forms, buy-box button responsiveness, cart drawer, checkout access).

---

## 📊 3. Deliverable Report Structure

The generated audit report MUST be formatted with the following clear sections:

1. **Executive Scorecard:** High-level executive summary with letter grades (A–F) for each of the 5 pillars.
2. **Priority Remediation Matrix:**
   * **P0 — Critical Site-Breakers:** Urgent issues actively harming revenue, indexation, or crashing admin edit screens.
   * **P1 — High-Impact Fixes:** Major technical SEO, CWV, or conversion barriers.
   * **P2 — Optimization & Quick Wins:** Metadata polishing, minor schema tweaks, and hygiene.
3. **Detailed Findings & Developer Fixes:** Each issue must provide:
   * *Affected URL / Component*
   * *Observed Defect vs Expected Behavior*
   * *Exact Code Diff or Server Configuration Snippet to remediate the defect.*
4. **Actionable Roadmap:** Phased next steps for implementation and post-deploy re-testing.
