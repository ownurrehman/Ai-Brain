> **Parent Hub:** [[memory/procedural/INDEX|🛠️ Procedural Memory Hub]] · [[skills/rankray-technical-seo-audit/SKILL|Technical SEO Audit]] · [[INDEX|🧠 Master Ai Brain Hub]]

# 🔍 SOP: 5-Pillar Forensic Technical SEO Audit

> **Rapid audit execution protocol for evaluating prospect or new client websites.**

---

## 🏛️ The 5 Forensic Pillars

### 1. Crawlability & Indexation Architecture
- Verify `robots.txt`, XML sitemaps, canonical tags, and HTTP header response codes.
- Identify noindex leaks, duplicate staging environments, or redirect loops.

### 2. JavaScript Rendering & DOM Inspection
- Compare raw HTML vs. rendered DOM using `chrome-devtools-mcp` to ensure essential text, navigation, and schemas render properly for bots.

### 3. Core Web Vitals & Performance (CWV)
- Benchmark LCP ($< 2.5s$), INP ($< 200ms$), and CLS ($< 0.1$).
- Pinpoint unoptimized hero images, render-blocking JS bundles, or layout shifts.

### 4. Semantic Entity & Schema Density
- Validate JSON-LD schemas (`Organization`, `LocalBusiness`, `MedicalClinic`, `Product`, `FAQPage`) using Google Rich Results standards.

### 5. Content Quality & Thin Page Forensics
- Detect cannibalized keyword URLs, word counts $< 1,000$ on primary topics, and missing internal link anchor equity.

---

## 📊 Deliverable Generation
Generate structured executive markdown report in `reports/` summarizing: (1) Urgent Blockers, (2) Striking Distance Wins, and (3) 90-Day Implementation Roadmap.
