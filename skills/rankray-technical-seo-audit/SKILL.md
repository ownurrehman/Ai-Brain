---
name: rankray-technical-seo-audit
description: "Forensic technical SEO audit playbook covering crawlability, indexation bloat, JS rendering, Core Web Vitals, and log file analysis."
---

# 🔍 RankRay Forensic Technical SEO Audit

> **Step-by-step diagnostic framework for comprehensive technical site audits.**

---

## 🚦 1. The 5-Pillar Audit Checklist

### Pillar 1: Crawl & Architecture
- [ ] Verify `robots.txt` is accessible (`HTTP 200`) and not blocking CSS/JS assets.
- [ ] Inspect XML Sitemaps for orphan URLs, redirected URLs, or non-canonical URLs.
- [ ] Identify crawl budget waste caused by infinite parameter loops, faceted navigation, or internal 301 chains.

### Pillar 2: Indexation & Canonicals
- [ ] Verify self-referencing canonical tags on all indexable primary pages.
- [ ] Check for `noindex` tag leaks in staging-to-production deployments.
- [ ] Audit trailing slash consistency (redirect `/url` to `/url/` or vice versa sitewide).

### Pillar 3: JavaScript Rendering & Hydration
- [ ] Compare Raw HTML (server response) vs Rendered HTML (DOM snapshot).
- [ ] Ensure critical navigation menus, `<h1>` headers, and internal links are present in the initial server HTML.

### Pillar 4: Performance & Core Web Vitals (CWV)
- [ ] **LCP (Largest Contentful Paint):** Target < 2.5s (optimize hero image format, preconnect fonts).
- [ ] **INP (Interaction to Next Paint):** Target < 200ms (minimize long JS tasks, debounce listeners).
- [ ] **CLS (Cumulative Layout Shift):** Target < 0.1 (explicit image `width`/`height` attributes).

### Pillar 5: Security & HTTP Status
- [ ] Strict HTTPS enforcement across all subdomains.
- [ ] Zero mixed-content warnings.
- [ ] Clean HTTP header security (`Strict-Transport-Security`, `X-Content-Type-Options`).
