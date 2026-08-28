---
name: rankray-gsc-forensics
description: "Google Search Console API extraction, query striking distance optimization (positions 8-18), CTR boosts, and keyword cannibalization detection."
---

# 📊 RankRay GSC Data Forensics & CTR Optimization

> **Actionable workflows for mining Google Search Console performance data.**

---

## 🎯 1. Striking-Distance Keyword Optimization
- **Definition:** Queries ranking in positions **8.0 to 18.0** with high impressions (>500/month) and below-average CTR (<3%).
- **Execution Plan:**
  1. Pull query and landing URL from GSC Search Analytics API.
  2. Inspect target page: check if the exact query entity exists in the `<h1>`, first 100 words, or an `<h2>`.
  3. Add a dedicated `<h2>` or table summarizing the query's answer.
  4. Inject 2 internal links from top-ranking pages on the site using the striking-distance keyword as anchor text.

---

## ⚔️ 2. Keyword Cannibalization Detection
- **Symptom:** Multiple URLs on the domain splitting clicks and impressions for the same target query.
- **Remediation Options:**
  - **Consolidation:** 301-redirect the weaker URL into the primary pillar URL and merge the unique content.
  - **Differentiation:** Re-optimize the secondary URL for an adjacent long-tail modifier and remove overlapping headers.
  - **Canonicalization:** Point the canonical tag of the weaker page to the primary page if both pages must remain live for UX reasons.
