# Features and integration status

**Sources of truth:** [ROADMAP.md](../core/ROADMAP.md) (vision + module status), [audits/FULL_SYSTEM_AUDIT.md](../audits/FULL_SYSTEM_AUDIT.md) (finance/CRM tab ↔ API audit), [RELEASE_GATE.md](./RELEASE_GATE.md) (pre-demo checklist).

This file summarizes **what exists in the UI**, **live vs partial API wiring**, and **demo-safe claims**.

---

## Sales / demo matrix

| Area | Buyer-facing summary | Status | Notes |
|------|----------------------|--------|-------|
| Auth & workspace | Sign-in, tenant isolation | **done** | `JWT_SECRET` required; roles should load for UX |
| Dashboard | Revenue, tasks, SEO alerts | **partial** | Not “full BI”; harden empty data |
| Leads | Pipeline, activity, reports | **partial** | Deal/company linkage UX |
| Clients | Companies, contacts | **partial** | Company-first; profile multi-fetch |
| SEO | 12 website-scoped areas (audit, keywords, WP, etc.) | **partial** | Needs website + providers; see [SEO_BLUEPRINT.md](../seo/SEO_BLUEPRINT.md) |
| Publishing (top-level) | Video | **partial** | Blog under SEO |
| Team | HRM tabs | **partial** | Per-tab errors (target) |
| Finance | Quotes, invoices, ledger, … | **partial** | Core tabs audited; full lifecycle regression |
| Projects / tasks | Delivery + time | **partial** | Confirm time tracking live |
| Assets | Domains, hosting, … | **partial** | Several types “coming soon” |
| Settings | Workspace config | **partial** | ≠ SEO Integrations |
| Outreach / Marketing / Analytics / Inbox | Campaigns, inbox, … | **partial** | **Verify live data** before promising |
| Client portal | — | **planned** | |
| Ahrefs/Semrush-class index metrics | Volume, KD, full link graph | **not available** until [SEO_RANK_INTELLIGENCE.md](./SEO_RANK_INTELLIGENCE.md) ships |
| Social posting (multi-platform) | — | **planned** | [SOCIAL_PUBLISHING_MVP.md](./SOCIAL_PUBLISHING_MVP.md) |

**Demo checklist:** (1) One website + one working provider or audit-only. (2) Company → contact → deal without claiming unaudited reports. (3) Finance screens only after confirming tab audit. (4) No Ahrefs/Semrush parity claims without rank-intel shipped and labeled.

---

## Engineering detail (by area)

### Auth and workspace

Live API: `/auth/login`, `/auth/register`, `/auth/me`; `rankray-hq-frontend/src/stores/authStore.ts`. Workspace: `/workspaces/current`.

### CRM

Leads/Clients → `/crm/*`. See audit table for per-tab status.

### Finance

Many sub-sections; invoices, receipts, payments, expenses, banks **audit-passed** in [FULL_SYSTEM_AUDIT.md](../audits/FULL_SYSTEM_AUDIT.md). Quote→payment path: validate end-to-end.

### SEO

Website-scoped; **not** legacy dashboard/on-page/off-page IA. Routes and components: [SEO_BLUEPRINT.md](../seo/SEO_BLUEPRINT.md). Optional **Redis** for automation queues.

### HRM, projects, publishing, assets, settings

As in [ROADMAP.md](../core/ROADMAP.md) module board.

### Frontend infrastructure

⌘K palette, responsive shell, Zustand (`authStore`, `crmStore`, `financeStore`, `hrmStore`, `taskStore`, `projectStore`, SEO website context).

### Backend integration summary

| Area | UI | Data | Notes |
|------|----|------|-------|
| Auth | Done | Live API | |
| CRM core | Done | Live API | Reports tab: see audit |
| Finance core | Done | Live API | Several tabs: see audit |
| SEO | Done | Live + providers | |
| HRM | Partial | Verify | |
| Projects / time | Partial | Mixed | |
