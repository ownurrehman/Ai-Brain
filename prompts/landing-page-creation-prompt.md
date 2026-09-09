> **Parent Hub:** [[prompts/INDEX|🎯 Prompts Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# 🎯 Master Prompt: High-Conversion CRO Landing Page Creation

**Role:** Principal Landing Page Architect, CRO Copywriter, and Conversion UX Specialist.  
**Objective:** Design, write, and construct a high-converting, SEO/AEO-optimized landing page for `[Product/Service Name]` targeting `[Primary Keyword]`.

---

## 📋 Input Parameters (Fill Before Prompting)

* **Target Project / Site:** `[e.g. websites/rankray.com/ or websites/justccell.com/]`
* **Product / Service Name:** `[Product/Service Name]`
* **Primary Keyword:** `[Target Search Keyword]`
* **Ideal Customer Profile (ICP):** `[Specific target audience, e.g. B2B SaaS Founders / Wholesale Buyers]`
* **Core Value Proposition:** `[Unique mechanism & quantifiable primary outcome]`
* **Primary Conversion Goal:** `[e.g. Book Demo / Request Wholesale Quote / Start Free Trial]`

---

## ⚡ 1. Boot Sequence (Execute Before Generating Copy/Code)

1. **Locate Target Project:** Find the site or project in `INDEX.md` and read its `mastersheet.md` and `rules.md` (if applicable).
2. **Review Credentials & Environment:** Inspect `master-env.env` for environment credentials and target CMS endpoints.
3. **Load Copywriting & Quality Standards:** Read `rules/content/content-rules.md` and `rules/content/semantic-seo-writer.md`.
4. **Load Production Skills:**
   * **CRO Copywriting:** `skills/rankray-cro-copywriting/SKILL.md` (Above-the-fold value hook formula)
   * **SEO & Content Depth:** `skills/rankray-seo-content-writing/SKILL.md`
   * **Structured Data:** `skills/rankray-schema-jsonld/SKILL.md` (Service, WebPage, FAQPage schemas)
   * **Engineering & CMS:** `skills/rankray-wordpress-engineering/SKILL.md` (for WordPress/ACF) or `skills/rankray-react-nextjs-engineering/SKILL.md` (for Next.js/React)

---

## 🏗️ 2. High-Converting Page Architecture & Blueprint

Structure the landing page with the following required narrative arc:

### 1. Above-the-Fold (ATF) Hero Section
* **Eyebrow / Badge:** Target category identifier + credibility tag (e.g. *Enterprise Vaporization Hardware*).
* **H1 Headline:** Benefit-driven, high-impact headline answering "What is it?" and "What is the measurable outcome?".
* **Subheadline (H2):** Explicit ICP callout with objection handling.
* **Primary CTA:** High-contrast, benefit-oriented button label (e.g. *[Get Wholesale Quote]* or *[Start 14-Day Free Trial]*).
* **Social Proof Strip:** Trust metrics, client logos, or rating badges immediately visible without scrolling.

### 2. AEO Quick-Answer Extraction Box
* Positioned directly below the hero section.
* 40–60 word definitive summary answering the core query for Google AI Overviews and Perplexity extraction.

### 3. The "Old Way vs. Modern Way" Comparison Matrix
* Direct comparison table illustrating customer pain points vs your superior mechanism.
* Clear visual demarcation of why alternative solutions fail.

### 4. Core Features & Benefit Cards
* 3 to 4 distinct feature cards using the **Feature $\rightarrow$ Function $\rightarrow$ Business Payoff** framework.
* Clean iconography and scannable bullet points.

### 5. Social Proof & Authority Signals
* Real customer metrics, testimonials with headshots/company names, or compliance/industry certificates.
* Zero generic placeholders; use verified entities.

### 6. Low-Friction Lead Capture / Conversion Form
* Minimum required input fields (name, business email, inquiry focus).
* Micro-copy addressing privacy and turnaround time (e.g. *"Response within 2 hours. No spam guarantee."*).

### 7. High-Intent FAQ Accordion
* 5 to 7 real customer objections answered transparently.
* Formatted in clean schema-ready HTML markup.

### 8. Final Urgency & Closing CTA
* Sticky footer or bottom banner summarizing the main value proposition with a repeat primary CTA button.

---

## 🚨 3. Technical & Copywriting Non-Negotiables

* **Zero Em-Dash Rule:** Never use em-dashes (`—`) or double-hyphens (`--`). Use commas, colons, or clean parentheticals.
* **Zero Emojis:** Keep professional copy clean and enterprise-ready.
* **100% Backend Editability (WordPress):** Never hardcode text, headings, or button labels in PHP templates. Map to native fields or ACF groups.
* **Structured Data:** Generate valid JSON-LD schema (`WebPage`, `Service` or `Product`, and `FAQPage`).
* **SEO Metadata:** Meta title (< 60 chars) and meta description (< 160 chars) targeting the primary keyword.
* **Delivery Protocol:** Push code or page content as **DRAFT** (`status: draft`). Deliver live preview URL and wait for approval.
