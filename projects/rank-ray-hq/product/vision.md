# 🚀 RankRay HQ: Product Vision & Architecture

## 🏛️ THE VISION
**Objective**: Build a high-performance, agency-grade desktop command center mirroring professional tools like SEMrush/Ahrefs. The system is designed for massive scale, starting with a powerful SEO engine.

## 🏗️ Core Architecture
- **Monorepo Structure**: SaaS application with a NestJS backend and a React 19 / Vite 7 frontend.
- **Data Layer**: Prisma ORM with SQLite (Initial) / PostgreSQL (Scale).
- **The "Spine" Contract**: A "Core Shell + Registry" architecture where the frontend dynamically builds navigation and tools based on backend plugin manifests.
- **Dynamic Plugin Shell**: Plugins inherit from a library of **Elite Templates** (Grid, Chart, TABBED_DRILLDOWN) to ensure zero-touch scaling.

## 🎯 Primary Modules

### 🩺 1. Health Center (Technical Audit)
- **Crawl Engine**: Comprehensive technical crawl for errors (404s, broken links, meta-tags).
- **Intelligence**: Automated fixes pushed directly to WordPress sites via the Helper Plugin.

### 📍 2. Position Radar (Daily Tracking)
- **Tracking**: Daily keyword monitoring for specific business locations.
- **Efficiency**: Open-source/Local crawler focus to eliminate paid API dependency.

### 🤖 3. Automations Workspace
- **Bulk Builder**: AI generation of hundreds of SEO-optimized landing pages.
- **Drip-Feed Law**: Gradual publishing (1–5 pages/day) to maintain organic trust signals.
- **WP Sync**: Deep "Read/Write" of WordPress ACF fields via the RankRay Handshake.

## 🔄 THE AGENTIC MASTER WORKFLOW
1. **BUILD (Claude)**: Primary Feature Coder for UI/UX (Elite Light Mode) and Product Logic.
2. **AUDIT & STABILIZE (Antigravity)**: DevOps & Security lead. Audits code, hardens logic, and manages the `dev` branch.
3. **VERIFY (User)**: Validation of functionality on a local `dev` branch.
4. **SHIP (Main)**: Merge to `main` after successful UAT.

---
*Derived from VISION_SEO.md and mastersheet.md — Last Updated: 2026-04-16*
