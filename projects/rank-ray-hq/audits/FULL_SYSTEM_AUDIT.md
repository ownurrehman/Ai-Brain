# Full system audit (frontend tab ↔ API)

Per-tab finance/CRM audit status. **SEO** route map: [../seo/SEO_BLUEPRINT.md](../seo/SEO_BLUEPRINT.md). **Product status:** [../core/ROADMAP.md](../core/ROADMAP.md). **Features / demos:** [../product/FEATURES.md](../product/FEATURES.md).

Paths below are under `rankray-hq-frontend/src/`.

## Module inventory

- **Dashboard**: `modules/dashboard/Dashboard.tsx`
- **CRM**: `modules/crm/CRM.tsx`
- **Projects**: `modules/projects/Projects.tsx`
- **Finance**: `modules/finance/Finance.tsx`
- **HRM**: `modules/hrm/HRM.tsx`
- **Outreach**: `modules/outreach/Outreach.tsx`
- **SEO**: `modules/seo/SEO.tsx`
- **Settings**: `modules/settings/Settings.tsx`

## Finance module

**Component**: `modules/finance/Finance.tsx` — tabs: `uiStore.activeFinanceTab`

| Tab ID | Component | API | Status |
|--------|-----------|-----|--------|
| `items` | `sections/Items.tsx` | `/finance/items` | Pending audit |
| `customers` | `sections/Customers.tsx` | `/finance/customers` | Pending audit |
| `quotes` | `sections/Quotes.tsx` | `/finance/quotes` | Pending audit |
| `retainer` | `sections/RetainerInvoices.tsx` | `/finance/retainers` | Pending audit |
| `invoices` | `sections/Invoices.tsx` | `/finance/invoices` | **Passed** |
| `receipts` | `sections/SalesReceipts.tsx` | `/finance/receipts` | **Passed** |
| `payments` | `sections/PaymentsReceived.tsx` | `/finance/payments` | **Passed** |
| `recurring` | `sections/RecurringInvoices.tsx` | `/finance/recurring` | Pending audit |
| `credits` | `sections/CreditNotes.tsx` | `/finance/credits` | Pending audit |
| `expenses` | `sections/Expenses.tsx` | `/finance/expenses` | **Passed** |
| `banks` | `sections/Banks.tsx` | `/finance/banks` | **Passed** |
| `reports` | `sections/Reports.tsx` | `/finance/reports` | Pending audit |
| `settings` | `sections/FinanceSettings.tsx` | `/finance/settings` | Pending audit |

## CRM module

**Component**: `modules/crm/CRM.tsx` — tabs: `uiStore.activeCrmTab`

| Tab ID | Component | API | Status |
|--------|-----------|-----|--------|
| `pipeline` | `sections/Pipeline.tsx` | `/crm/deals` | **Passed** |
| `companies` | `sections/Companies.tsx` | `/crm/companies` | **Passed** |
| `contacts` | `sections/Contacts.tsx` | `/crm/contacts` | **Passed** |
| `tasks` | `sections/Tasks.tsx` | `/crm/activities` | **Passed** |
| `activity` | `sections/Activity.tsx` | `/crm/activities` | **Passed** |
| `reports` | `sections/Reports.tsx` | `/crm/reports` | Pending audit |

## Projects

**Component**: `modules/projects/Projects.tsx`

| Tab | API | Status |
|-----|-----|--------|
| `projects` | `/projects` | **Partial** (real projects; client linkage verify) |
| `tasks` | `/projects/:id/tasks` | **Partial** |
| `time` | — | **Failed** / mock risk (`timeEntries`) — verify |

## HRM

**Component**: `modules/hrm/HRM.tsx` — **Partial**; see [ROADMAP.md](../core/ROADMAP.md) (shared error state).

## Outreach

**Component**: `modules/outreach/Outreach.tsx` — audit pending; possible mock/sample data.

## SEO module

**Component**: `modules/seo/SEO.tsx`  
**IA**: 12 screens under `/seo/*` — [../seo/SEO_BLUEPRINT.md](../seo/SEO_BLUEPRINT.md).  
**Backend**: `rankray-hq-backend/src/seo/*`. States: `ready` / `needs_setup` / `sync_required` / `insufficient_data`.  
**Status:** **Partial** — automation/queues need production verification.

## Dashboard

**Component**: `modules/dashboard/Dashboard.tsx` — audit pending; widget resilience per ROADMAP.

## Settings

**Component**: `modules/settings/Settings.tsx` — audit pending.
