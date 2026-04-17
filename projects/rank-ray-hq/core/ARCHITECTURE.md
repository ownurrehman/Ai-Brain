# RankRay HQ Architecture

## Core Entities

- Workspace
- User
- Employee
- Company
- Contact
- SeoWebsite
- Project
- Task
- Asset
- Invoice
- Payment
- Receipt
- Expense
- GeneratedContent
- PublishJob
- PublishRecord

---

## Entity Relationships (Authoritative)

### Workspace

Workspace is the tenant boundary and contains all records.

### Company

Company is the primary business owner entity.

Company owns or links:

- contacts
- websites
- projects
- finance records
- assets

### Contact

Contact belongs to company and represents a client-side person.

### Employee

Employee/User is internal only and must not be mixed with contacts.

### SeoWebsite

Website belongs to company and is the root object for SEO workspace context.

### Project

Project belongs to company and links delivery work, tasks, and optional finance context.

### Task

Task links to one target entity using `entityType + entityId`, and one assignee.

### Finance Records

Invoices, payments, receipts, and expenses are company-owned and may be project-linked.

### Asset

Asset is company-owned and may be website-linked.

### Publishing Records

Publishing records are workspace-owned; SEO-origin records should carry website context.

---

## Route and Navigation Truth

### Top-Level Navigation (Implemented)

Main sidebar items (top to bottom):

- Dashboard
- Tasks
- Leads (dropdown: Pipeline, Activity, Reports)
- SEO (2nd sidebar: 12 screens)
- Publishing (2nd sidebar)
- Clients (dropdown: Companies, Contacts)
- Team (dropdown: Employees, Attendance, Leave Requests, Payroll, Performance)
- Finance (2nd sidebar)
- Projects (dropdown: Projects, Project Tasks, Time Logs)
- Assets (dropdown: Domains, Hosting, SSL, APIs, Servers, Custom Assets)
- Settings (bottom of sidebar, above user profile card)

### Navigation Pattern

- Large modules (SEO, Finance, Publishing): 2nd sidebar inside module.
- Small modules (Leads, Clients, Team, Projects, Assets): expandable dropdown in main sidebar.
- No horizontal tab menus anywhere.

### Leads vs Clients

Leads and Clients are separate sidebar entries that both route to `CRM.tsx`:

- Leads active: `/crm/pipeline`, `/crm/activity`, `/crm/reports`
- Clients active: `/crm/companies`, `/crm/contacts`

Companies and Contacts do NOT appear under Leads.

### SEO Subroutes

SEO is website-first and uses:

- `/seo/overview`
- `/seo/performance`
- `/seo/rankings`
- `/seo/keywords`
- `/seo/technical`
- `/seo/content`
- `/seo/mentions` (limited — requires mention provider)
- `/seo/backlinks` (limited — observed data only)
- `/seo/competitors` (limited — tracked overlap only)
- `/seo/publishing`
- `/seo/automation` (partial — needs hardening)
- `/seo/settings` (labeled "Integrations" in UI)

No SEO analysis screen should load meaningfully without selected website context.

See [SEO_USER_JOURNEY.md](../seo/SEO_USER_JOURNEY.md) and [SEO_BLUEPRINT.md](../seo/SEO_BLUEPRINT.md) for full SEO architecture.

### Publishing Subareas

- Videos

Publishing architecture contract:

- Top-level Publishing is video-focused.
- Blog/image generation belongs to SEO workflows.

### Assets Subareas

- Domains
- Hosting
- SSL (coming soon)
- APIs (coming soon)
- Servers (coming soon)
- Custom Assets (coming soon)

---

## Frontend Module Ownership

### App Shell

- `rankray-hq-frontend/src/App.tsx` -> top-level route ownership
- `rankray-hq-frontend/src/components/layout/Sidebar.tsx` -> main navigation
- `rankray-hq-frontend/src/components/layout/Header.tsx` -> page title / top shell logic

### Dashboard

- `rankray-hq-frontend/src/modules/dashboard/*`

### Leads / Clients / Contacts

- `rankray-hq-frontend/src/modules/crm/*`

### Team

- `rankray-hq-frontend/src/modules/hrm/*`

### Projects (Backend)

- `rankray-hq-frontend/src/modules/projects/*`

### Tasks (Backend)

- `rankray-hq-frontend/src/modules/tasks/*`

### Finance (Backend)

- `rankray-hq-frontend/src/modules/finance/*`

### SEO (Backend)

- `rankray-hq-frontend/src/modules/seo/*`

### Publishing (Backend)

- `rankray-hq-frontend/src/modules/publishing/*`

### Assets (Backend)

- `rankray-hq-frontend/src/modules/assets/*`

### Settings

- `rankray-hq-frontend/src/modules/settings/*`

---

## Backend Module Ownership (NestJS)

### CRM / Clients

- `rankray-hq-backend/src/crm/*`

### Team / Employees

- `rankray-hq-backend/src/hrm/*`

### Projects

- `rankray-hq-backend/src/projects/*`

### Tasks

- `rankray-hq-backend/src/tasks/*`

### Finance

- `rankray-hq-backend/src/finance/*`

### SEO

- `rankray-hq-backend/src/seo/*`

### Publishing

- `rankray-hq-backend/src/seo/publishing/*`

### Assets

- `rankray-hq-backend/src/assets/*`

### Settings / Admin / Workspace

- `rankray-hq-backend/src/users/*`
- `rankray-hq-backend/src/invitations/*`
- `rankray-hq-backend/src/workspace/*`
- `rankray-hq-backend/src/audit/*`
- `rankray-hq-backend/src/system-logs/*`

---

## Frontend implementation patterns

(`rankray-hq-frontend/`)

- **Modules:** `src/modules/*`; **App.tsx** owns top-level routing / active module.
- **State:** Zustand — `uiStore` (shell, module tabs), domain stores (`crmStore`, `financeStore`, …). Prefer **services** in `src/modules/*/services/` consumed by stores.
- **UI:** Tailwind + Radix/shadcn; client hydration guards for charts (`isHydrated` pattern).
- **Mobile:** Collapsible sidebar / drawer; `Header.tsx` menu; use `min-w-0` on flex rows with wide tables.

---

## Backend conventions (NestJS)

(`rankray-hq-backend/`)

- **ORM/schema:** Prisma — **`prisma/schema.prisma`** is authoritative for models.
- **Layers:** Controllers = HTTP + guards only; services = logic + persistence; DTOs validated with `class-validator`.
- **CRM:** `CrmController` + `DealService`, `CompanyService`, `ContactService`, `ActivityService`.
- **Finance:** `FinanceController` + `InvoiceService`, `PaymentService`, `ExpenseService`, `SalesReceiptService`, `LedgerService`, etc.
- **Module health:** see [ROADMAP.md](./ROADMAP.md) (this folder).

---

## SEO submodule (summary)

Website-scoped; **12 screens**, state contract, capability tiers, and route map: **[SEO_BLUEPRINT.md](../seo/SEO_BLUEPRINT.md)**. Journeys: [SEO_USER_JOURNEY.md](../seo/SEO_USER_JOURNEY.md). Product/status detail: [modules/SEO_MODULE.md](../modules/SEO_MODULE.md).

---

## Finance Architecture Notes

- Company-first ownership is non-negotiable.
- Service is the catalog concept, not Item.
- Finance actions must not regress when UI is refactored.
- History is preserved even when company becomes inactive or archived.

---

## Assets Architecture Notes

- Assets are linked records, not orphan records.
- Archive-based removal only.
- Current implemented types are narrower than target types.
- Expiry intelligence must drive dashboard/alert usefulness.

---

## Publishing Architecture Notes

- Publishing is provider-driven.
- UI should expose only flows that backend/provider can actually execute.
- Blogs / Videos / Images share the same high-level publishing area, but backend capabilities may differ.
- Remotion-based videos and WordPress publishing must remain honest about readiness and failure states.

---

## Architectural Non-Negotiables

- Company-first ownership across CRM, websites, finance, and assets
- Contacts must never be merged with employees
- SEO is website-first
- Setup/config should not dominate operator workspaces
- No fake metrics, fake readiness, or fake publish/sync states
