---
date: '2026-05-16'
status: complete
tags:
  - rankray-plugins
  - ai-visibility
  - module-overhaul
  - v0.4.19
---

> **Parent Site:** [[websites/rankray.com/index|🌐 rankray.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# AI Visibility Audit Module — Overhaul (v0.4.19)

## Status: ✅ Complete — NLP Engine Deployed

### What Changed
The entire scoring backend was replaced. The old approach scraped DuckDuckGo for AI-platform mentions (ChatGPT, Gemini, etc.) — fragile and inaccurate. 

**New engine** uses **4-dimension NLP text analysis** (each 25% weight):

| Dimension | What it Measures | Signal |
|---|---|---|
| Perplexity & Burstiness | Sentence length variance (CV) | Uniform rhythm = AI |
| Semantic Repetition & Fluff | AI-typical filler phrases ("delve", "testament to", "navigate the complexities") | Tiered weighting (3x/2x/1x) |
| LSI & Keyword Integration | Keyword distribution + density across text quarters | Clustering = robotic |
| Predictive Token Overlap | Common LLM bigram/trigram sequences + sentence starter diversity | High overlap = AI |

### Architecture
- `modules/ai-visibility-audit/class-nlp-engine.php` — standalone `AIVisibilityNLP` class
- `modules/ai-visibility-audit/ai-visibility-audit.php` — WordPress hooks, UI, AJAX, report rendering
- `modules/ai-visibility-audit/assets/ai-visibility-audit.js` — dual-mode input (paste text / enter URL)

### UI
- **Dual input**: toggle between "Paste Text" and "Enter URL" modes
- **Word counter** on textarea
- **Report card**: overall score (0-100%), confidence badge, 4 dimension breakdowns, flagged AI phrases, actionable fixes (max 3)
- URL mode extracts main content via `<article>`, `<main>`, `.entry-content` selectors

### Scoring Calibration
- **False positive prevention**: varied academic writing won't auto-flag; needs multiple dimension agreement
- **Confidence rating**: High (500+ words, <25pt spread), Medium, Low
- **Color coding**: Green (<25) → Blue (25-44) → Yellow (45-69) → Red (70+)

### Build
- Version: `0.4.19`
- ZIP: `builds/rankray-plugins.zip` (145KB, `rankray-plugins/` top-level)
