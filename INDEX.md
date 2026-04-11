Summary: This file routes AI and humans to the smallest useful part of `ai-brain` for a given task.

# Routing Index

Use this file before loading anything else. Read only the entries relevant to the current job.

| Path | Purpose | Use when | Skip when |
| :--- | :--- | :--- | :--- |
| **`../AGENTS.md`** | **Universal agent workflow** (7 steps) for any repo under **AI Codes** — Mastersheet → AGENTS → skills → Antigravity → `docs/` → work → update + report. | Cross-repo or product work (RankRay-HQ, WP plugins, etc.). | Task is only editing files inside **Ai-Brain** with no other repo. |
| `README.MD` | Defines the system and its philosophy. | You need the overall intent of `ai-brain`. | You already know the operating model. |
| `INDEX.md` | Main routing file for selective loading. | Starting any new task in this brain. | You chose the exact file already. |
| **`SKILLS.md`** | **One-page map:** [Rank Ray](https://www.rankray.com) **service lines** (`digital-marketing`, `seo-services`, `web-development`, `app-development`, `saas-development`, `ai-automation`, `crypto-trading`) + all other `skills/` + **`antigravity-awesome-skills/`** + precedence. | Quick orientation or client-service routing. | You only need one known `SKILL.md`. |
| **`skills/README.md`** | **Two-layer skills:** first-party `skills/` = Rank Ray **control** `SKILL.md` + links to catalog depth; **`skills/_CATALOG_MAP.md`** = folder → Antigravity ids. | Any task that might need a playbook not in first-party `skills/`. | You already know the exact `SKILL.md` path. |
| **`ANTIGRAVITY.md`** | **Submodule** ops: clone, pull upstream `main`, Cursor installer. | Updating or browsing the big catalog. | You are only using first-party `skills/` / `core/`. |
| `antigravity-awesome-skills/CATALOG.md` | Index of **1,300+** upstream `SKILL.md` ids (MIT). [Repo](https://github.com/sickn33/antigravity-awesome-skills). | Need a community skill not defined under `skills/`. | Task is covered by an `INDEX.md` row already. |
| `core/engineering-standards.md` | **Unified engineering & verification rules.** | Starting any coding task or bug fix. | Task is purely strategic/research. |
| `core/strategic-research.md` | **Fact-gathering & evidence evaluation.** | Comparing options or building briefs. | Task is purely coding-focused. |
| `core/communication-standards.md` | **Prose, protocol, and the Learning Loop.** | Drafting reports or updating Brain/Mastersheet. | Task is purely tactical/local implementation. |
| `skills/rankray-seo-ui/SKILL.md` | **RankRay product & SEO UI patterns.** | Working on RankRay-HQ or SEO modules. | Task is for a different product line. |
| `skills/seo/SKILL.md` | SEO research & search intent strategy. | Analyzing keywords or SERP intent. | Task is pure writing/implementation. |
| `skills/content-writing/SKILL.md` | Structured, search-aware asset creation. | Drafting or improving blog/web copy. | Task is pure research or publishing. |
| `skills/wordpress-publisher/SKILL.md` | Prep and publishing routines for WP. | Entering content into WordPress/Gutenberg. | Task ends before the CMS stage. |
| `skills/saas-go-to-market/SKILL.md` | SaaS ICP, positioning, and GTM cycles. | Defining a new SaaS product direction. | Task is tactical ads or local code. |
| `skills/paid-acquisition/SKILL.md` | Paid campaign structure and optimization. | Running Google/Meta/LinkedIn ads. | Task is organic or product-only. |
| `skills/conversion-rate-optimization/SKILL.md` | Landing page and funnel experiments. | Trying to improve signup/activation lift. | Task is broad awareness or coding. |
| `skills/saas-app-foundation/SKILL.md` | SaaS architecture and delivery baseline. | Designing core app tenancy or boundaries. | Task is a small local UI component fix. |
| `skills/saas-auth-billing/SKILL.md` | SaaS identity and subscription lifecycle. | Implementing auth or Stripe events. | Task is unrelated to user sessions. |
| `skills/saas-growth-analytics/SKILL.md` | Funnel metrics and event instrumentation. | Working on Mixpanel or PostHog capture. | Task is purely visual or ad-only. |
| `patterns/architecture-patterns.md` | Reusable system-structure decisions. | Need structural guidance for features. | Task is content or publishing work. |
| `patterns/backend-patterns.md` | Services, APIs, and job patterns. | Implementing logic on the server side. | Task is frontend-only or non-code. |
| `patterns/ui-ux-patterns.md` | Interface and interaction consistency. | Building user-facing screens or widgets. | Task has no interface components. |
| `memory/lessons-learned.md` | Ledger of failure patterns to avoid. | High-risk tasks where history matters. | Task is trivial and low risk. |
| `memory/past-wins.md` | Ledger of proven tactical approaches. | Need a fast path based on prior success. | Novel constraints make wins irrelevant. |
| `maintenance/brain-update-process.md` | Rules for keeping the brain current. | Pruning, merging, or adding brain files. | You are only consuming guidance. |
