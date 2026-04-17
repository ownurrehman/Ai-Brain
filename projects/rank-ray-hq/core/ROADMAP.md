# RankRay HQ Roadmap

Single product + delivery doc: **vision**, **model**, **navigation**, **module status**, and **next steps**. (Former `MASTERPLAN.md` content is merged here.)

---

## What this product is

RankRay HQ is **one operating system** for an SEO agency or in-house growth team—not a generic CRM bolted to a generic SEO dashboard.

**Scope:** lead management, client/company management, website operations, SEO execution, publishing (video + SEO-side blog), finance, assets, internal team ops.

**Core value:** **companies, websites, SEO work, finance, tasks, and assets stay linked** in one workspace.

**Audience:** agency owner, account manager, SEO operator, content/publishing operator, finance operator, internal team lead.

**Vision**

- One workspace where business records, websites, tasks, SEO, publishing, finance, and infrastructure stay connected.
- **No fake intelligence:** metrics from real data or honest setup / missing-data states.
- **Insights → action:** create task, sync, crawl, generate content, publish, assign work.

---

## Current product phase

- Phase: **Core stabilization + SEO module operational**
- Mode: **Fix-first, polish second, no broad rewrites**
- Priority: (1) working CRUD, (2) data integrity and honest states, (3) navigation clarity, (4) UX polish, (5) controlled expansion (automation, publishing pipeline)

**Status legend:** done | partial | broken | planned

---

## Navigation contract (implemented)

**Sidebar (top → bottom):** Dashboard → Tasks → Leads (Pipeline, Activity, Reports) → **SEO** (2nd sidebar, 12 screens — see [SEO_BLUEPRINT.md](../seo/SEO_BLUEPRINT.md)) → Publishing (2nd sidebar, videos) → Clients (Companies, Contacts) → Team (Employees, Attendance, Leave, Payroll, Performance) → Finance (2nd sidebar) → Projects → Assets → **Settings** (bottom, above profile).

**Patterns:** Large modules (SEO, Finance, Publishing) use a **2nd sidebar**; small modules use **main-sidebar dropdowns**. **No horizontal tab rows** app-wide.

**Rules:** Leads ≠ Clients (no Companies/Contacts under Leads). SEO provider UI = **Integrations**, not global Settings. **Top-level Publishing = video**; **blog/image** lives under **SEO** routes.

**SEO:** Always **website-scoped**—no meaningful analysis without a selected website. Screen list and routes: [SEO_BLUEPRINT.md](../seo/SEO_BLUEPRINT.md).

---

## Core product model

- **Company** — primary business owner (client, internal, or vendor). Owns/links contacts, websites, projects, finance, assets.
- **Contact** — client-side person (never merged with employees).
- **Employee** — internal only.
- **Website (SeoWebsite)** — belongs to company; root context for SEO.
- **Project** — delivery container; company-linked; optional websites/tasks/finance context.
- **Task** — `entityType` + `entityId` + assignee.
- **Finance records** — company-owned; project-linkable; preserve history (no destructive billing deletes).
- **Asset** — company-owned, optional website link; archive-based removal; track expiry/renewal.
- **Publishing records** — workspace-owned; SEO-originated records carry website context.

---

## Workflow sketches

- **Lead → client:** lead → pipeline → won → company/contact → delivery.
- **Delivery:** company → website → SEO per site → tasks/projects → content/publish → finance → assets.
- **SEO:** select website → connect providers → sync/crawl → insights → task or content → publish → measure.
- **Finance:** quote → invoice → payment → receipts/history preserved.
- **Assets:** create → link company/website → track renewal → archive when retired.

---

## Definition of done (product)

Ship-quality when: end-to-end flows on **real persisted data**; company-first ownership; contacts ≠ employees; websites company-linked; SEO website-scoped; unified tasks; stable finance; reviewable publishing; assets + expiry; unfinished areas **labeled honestly**; builds pass; critical paths regression-checked.

**Non-negotiable truths:** company-first; contacts vs employees; websites under companies; SEO website-first; finance company-owned; linked assets; **no fake KPIs or success states**.

---

## Module status board

### Module: Dashboard

Purpose: mission control for revenue, tasks, SEO alerts, expiries, and operational blockers  
Owner Entity: Workspace  
Current Status: **partial**  
Known Problems: some widgets error when dependent data is empty; noisy toasts silenced but widget reliability still uneven  
Next Work: make each widget independently resilient; honest empty states instead of errors

### Module: Leads

Purpose: pre-client CRM pipeline and deal tracking  
Owner Entity: Lead / Deal  
Current Status: **partial**  
Known Problems: deal creation requires companyId in DB; company field is text-with-autocomplete in UI  
Recent Fixes: separated from Clients in sidebar  
Next Work: validate deal CRUD end-to-end; consider optional deal.companyId migration

### Module: Clients

Purpose: real business relationships and company records  
Owner Entity: Company  
Current Status: **partial**  
Known Problems: company profile loads multiple sub-resources that may fail independently  
Recent Fixes: create buttons visible; separated from Leads  
Next Work: strengthen company profile error resilience

### Module: Team (HRM)

Purpose: internal workforce records and team operations  
Owner Entity: Employee/User  
Current Status: **partial**  
Known Problems: shared error state across all HRM tabs  
Next Work: separate error state per tab; employee ↔ user accounts for login

### Module: Finance

Purpose: invoice/payment/receipt/expense workflows  
Owner Entity: Invoice  
Current Status: **partial**  
Recent Fixes: ledger balance; quote-to-invoice; expense default category; create buttons visible  
Known Problems: modals may not populate service/company selects when data not loaded  
Next Work: full quote→invoice→payment lifecycle regression

### Module: SEO

Purpose: website-scoped SEO command system  
Owner Entity: SeoWebsite  
Current Status: **partial**  
Done Means: 12-screen architecture stable, honest, workflow-first ([SEO_BLUEPRINT.md](../seo/SEO_BLUEPRINT.md))  
Recent Fixes: graceful errors on screens; sync fallback when Redis unavailable; Crawlee site audit  
Known Problems: `needs_setup` when providers missing (correct); automation needs hardening  
Next Work: provider flows; automation reliability  
Module contract: [modules/SEO_MODULE.md](../modules/SEO_MODULE.md)

### Module: Publishing (top-level)

Purpose: video generation and operations  
Current Status: **partial**  
Next Work: queue reliability and error handling

### Module: Outreach

Current Status: **partial** — sequence orchestration not fully wired

### Module: Analytics

Current Status: **partial** — demo-style metrics risk in UI

### Module: Marketing

Current Status: **partial** — some static placeholders

### Module: Inbox

Current Status: **partial** — sample conversation detail risk

### Module: Projects

Current Status: **partial** — validate CRUD and task linking; time tab verify live data

### Module: Assets

Current Status: **partial** — Domain/Hosting creatable; other types “coming soon”

### Module: Settings

Current Status: **partial** — provider/integration clarity

### Module: Auth + workspace isolation

Current Status: **done**

### Module: SEO automation

Current Status: **partial** — BullMQ/Redis; not all routes production-verified

### Module: Client portal

Current Status: **planned**

### Module: Intelligence layer

Current Status: **planned**

---

## Known issues (cross-module)

- Dashboard widgets should fail independently with graceful empty states.
- HRM: one shared `error` state affects all tabs.
- Finance modals: selects if store loads late.
- SEO: GSC/GA OAuth requires Google Cloud setup.
- Role: backend returns uppercase; frontend normalizes; null role broke permission UI—create buttons ungated, backend enforces.

---

## Immediate next steps

1. Validate CRUD: company → contact → deal → invoice → payment.  
2. Validate SEO: website → provider → audit → keywords → results.  
3. Dashboard widget resilience.  
4. HRM per-tab errors.  
5. Stabilize automation (Redis).

---

## Blockers

- Paid index APIs for volume/KD/toxicity/broad competitor data (see [SEO_RANK_INTELLIGENCE.md](../product/SEO_RANK_INTELLIGENCE.md)).  
- Redis for full BullMQ automation (fallback covers audit/crawl paths only).  
- Google Cloud OAuth for GSC/GA in production.
