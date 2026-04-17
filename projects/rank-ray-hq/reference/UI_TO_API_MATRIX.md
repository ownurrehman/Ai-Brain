# UI to API matrix

Maps `rankray-hq-frontend` sections to HTTP endpoints. **Audit status:** prefer [FULL_SYSTEM_AUDIT.md](../audits/FULL_SYSTEM_AUDIT.md) when they disagree. Paths below are under `rankray-hq-frontend/src/modules/`.

## CRM Module

### Frontend Sections (`src/modules/crm/sections/`)

| Section | Component | Action | HTTP | Endpoint | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Companies** | `Companies.tsx` | View List | GET | `/crm/companies` | Display all companies |
| | `AddCompanyModal.tsx` | Create | POST | `/crm/companies` | Create new company |
| | | Update | PATCH | `/crm/companies/:id` | Update company details |
| | | Delete | DELETE | `/crm/companies/:id` | Delete company |
| **Contacts** | `Contacts.tsx` | View List | GET | `/crm/contacts` | Display all contacts |
| | `AddContactModal.tsx` | Create | POST | `/crm/contacts` | Create new contact |
| | | Update | PATCH | `/crm/contacts/:id` | Update contact |
| | | Delete | DELETE | `/crm/contacts/:id` | Delete contact |
| **Pipeline (Deals)** | `Pipeline.tsx` | View List | GET | `/crm/deals` | Display pipeline stages |
| | `AddDealModal.tsx` | Create | POST | `/crm/deals` | Create new deal |
| | (Drag & Drop) | Update Stage | PATCH | `/crm/deals/:id` | Trigger transition logic |
| | `DealDetailModal` | Delete | DELETE | `/crm/deals/:id` | Delete deal |
| **Tasks** | `Tasks.tsx` | View List | GET | `/crm/activities` | **Filtered by type='task'** |
| | `AddActivityModal.tsx`| Create | POST | `/crm/activities` | Create task activity |
| **Activity** | `Activity.tsx` | View List | GET | `/crm/activities` | Display activity feed |
| | `AddActivityModal.tsx`| Create | POST | `/crm/activities` | Create any activity |

## Finance Module

### Frontend Sections (`src/modules/finance/sections/`)

| Section | Component | Action | HTTP | Endpoint | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Invoices** | `Invoices.tsx` | View List | GET | `/finance/invoices` | Display invoices |
| | `CreateInvoice.tsx` (Dialog) | Create | POST | `/finance/invoices` | Create invoice |
| | | Status Upd | PATCH | `/finance/invoices/:id` | Mark sent/paid |
| **Payments** | `PaymentsReceived.tsx` | View List | GET | `/finance/payments` | List payments |
| | | Create | POST | `/finance/payments` | Record payment against invoice |
| **Expenses** | `Expenses.tsx` | View List | GET | `/finance/expenses` | List expenses |
| | | Create | POST | `/finance/expenses` | Record expense |
| **Bank Accounts** | `Banks.tsx` | View List | GET | `/finance/bank-accounts` | List accounts |
| **Sales Receipts** | `SalesReceipts.tsx` | View List | **MISSING** | **MISSING** | Backend Not Implemented |
| **Credit Notes** | `CreditNotes.tsx` | View List | GET | `/finance/credit-notes` | List credit notes |

## Projects Module

### Frontend Sections (`src/modules/projects/`)

| Section | Component | Action | HTTP | Endpoint | Description |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Projects** | `Projects.tsx` | View List | GET | `/projects` | List projects |
| | `AddProjectModal` | Create | POST | `/projects` | Create project |
| **Tasks** | `Projects.tsx` (Tabs) | View List | GET | `/projects/:id/tasks` | List tasks |
| | `AddTaskModal` | Create | POST | `/projects/:id/tasks` | Add task to project |
| **Time Tracking** | `Projects.tsx` (Tabs) | View List | **Mock Data** | **N/A** | Uses `mockData.ts` |
| | `LogTimeModal` | Create | POST | `/projects/:id/time` | Log time entry |

### Notes

- **Reports**: Currently fetches aggregated data from `/crm/deals` and computes metrics client-side. Future: Add `/crm/reports` endpoint.
