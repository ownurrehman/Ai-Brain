# Authority Module Codebase Index — Rank Ray HQ

> [!NOTE]
> This code-index maps **all physical file paths** recursively by logical product modules across both frontend (`rankray-hq-frontend`) and backend (`rankray-hq-backend`) workspaces.
> Automatically generated on **2026-05-22 16:53:45** during synchronization.

## 📊 Synchronization State
- **Current Git Branch:** `dev-cursor`
- **Latest Commit:** `d8fd0cb5b - chore: clean up duplicate nested repository reference from git tracking (Own-ur-Rehman Sheikh)`
- **Visualization Engine:** integrated with [Graphify](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/graphify-out/GRAPH_REPORT.md) (AST index of 5,928 nodes)
- **Total Tracked Module Files:** 482 (Frontend: 236 · Backend: 246)

## 🗺️ Product Module Map
| Logical Module | Description | Prisma Models | Files (FE/BE) |
| :--- | :--- | :--- | :--- |
| [**Dashboard**](#dashboard) | System command center featuring real-time operational widgets, cashflow deltas, task progression summaries, and multi-module quick links. | *None* | **2** FE / **5** BE |
| [**Tasks**](#tasks) | Kanban board and list views of tasks, interactive assignees stack rendering, task status transitions, and detail drawers. | `Task`, `TaskComment`, `TaskAssignment` | **3** FE / **5** BE |
| [**CRM (Leads & Clients)**](#crm-leads--clients) | Sales pipeline and client relationship management, company directories, contact records, and website-to-company linkages. | `Lead`, `Company`, `Contact`, `Deal` | **21** FE / **11** BE |
| [**SEO Suite**](#seo-suite) | Central SEO command suite containing keyword lookup tools, competitor analysis, rank tracking metrics, backlink auditing, and technical audit crawls. | `SeoWebsite`, `TrackedKeyword`, `KeywordSnapshot`, `SiteAuditRun`, `SiteAuditIssue`, `BacklinkOpportunity` | **91** FE / **103** BE |
| [**Publishing & CMS Connection**](#publishing--cms-connection) | Content distribution system mapping blog and image publishing runs to external content management platforms like WordPress. | `PublishHistory`, `WordPressConnection` | **6** FE / **0** BE |
| [**Outreach & Sourcing**](#outreach--sourcing) | Cold outreach campaign management, prospective client lead scraping, status tracking, and automated drafting workflows. | `OutreachCampaign`, `OutreachProspect` | **1** FE / **4** BE |
| [**HRM (Team & Employees)**](#hrm-team--employees) | Internal team profiles, assignee avatars mapping, role classifications, permissions (RBAC), and employee directory controls. | `Employee`, `Department` | **5** FE / **7** BE |
| [**Finance & Billing**](#finance--billing) | Double-entry bookkeeping, Stripe/PayPal webhooks, public pricing catalogs, invoices, and expense logs. | `Invoice`, `Payment`, `Expense`, `BankAccount`, `RecurringTransaction`, `Plan`, `Subscription`, `BillingEvent` | **30** FE / **19** BE |
| [**Assets**](#assets) | Repository for uploaded files, images, PDFs, and client branding documents associated with specific campaigns or domains. | `Asset`, `AssetLink` | **4** FE / **5** BE |
| [**Automation & Lab**](#automation--lab) | Recurring background pipelines, queues (BullMQ), WordPress connector stubs, and scheduler tasks. | `SeoAutomationConfig`, `AutomationRun`, `AutomationAlert` | **19** FE / **9** BE |
| [**AI Agents & Orchestration**](#ai-agents--orchestration) | Execution workspace for autonomous agent profiles (Dark, Enigma, Chronos, Nemo) and agent run tracking logs. | `Agent`, `AgentRun`, `AgentMessage` | **11** FE / **15** BE |
| [**Superadmin & Controls**](#superadmin--controls) | Top-level administration panel for workspace settings, subscription plans, server logs, and permission overrides. | `SystemLog`, `Entitlement` | **21** FE / **4** BE |
| [**Settings**](#settings) | General settings, workspace preferences, API keys, notifications, and integration configs. | `WorkspaceConfig` | **9** FE / **0** BE |
| [**System Core & Shared**](#system-core--shared) | Shared layouts, common utils, root routers, database schemas, configuration files. | *Root schema* | 13 FE / 59 BE |

---

<a name="dashboard"></a>
## Dashboard
**Description:** System command center featuring real-time operational widgets, cashflow deltas, task progression summaries, and multi-module quick links.

### 🖥️ Frontend Scope (2 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-frontend/src/modules/dashboard/Dashboard.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/dashboard/Dashboard.tsx) | `12.9 KB` |
| [rankray-hq-frontend/src/modules/dashboard/components/DashboardWidgets.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/dashboard/components/DashboardWidgets.tsx) | `7.2 KB` |

### ⚙️ Backend Scope (5 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-backend/src/dashboard/dashboard.controller.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/dashboard/dashboard.controller.spec.ts) | `1.2 KB` |
| [rankray-hq-backend/src/dashboard/dashboard.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/dashboard/dashboard.controller.ts) | `799 B` |
| [rankray-hq-backend/src/dashboard/dashboard.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/dashboard/dashboard.module.ts) | `359 B` |
| [rankray-hq-backend/src/dashboard/dashboard.service.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/dashboard/dashboard.service.spec.ts) | `689 B` |
| [rankray-hq-backend/src/dashboard/dashboard.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/dashboard/dashboard.service.ts) | `6.3 KB` |

---

<a name="tasks"></a>
## Tasks
**Description:** Kanban board and list views of tasks, interactive assignees stack rendering, task status transitions, and detail drawers.

**Relational Data Models (Prisma):** `Task`, `TaskComment`, `TaskAssignment`

### 🖥️ Frontend Scope (3 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-frontend/src/modules/tasks/Tasks.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/tasks/Tasks.tsx) | `38.9 KB` |
| [rankray-hq-frontend/src/modules/tasks/components/AssigneePicker.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/tasks/components/AssigneePicker.tsx) | `12.7 KB` |
| [rankray-hq-frontend/src/modules/tasks/components/TasksSidebar.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/tasks/components/TasksSidebar.tsx) | `828 B` |

### ⚙️ Backend Scope (5 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-backend/src/tasks/dto/tasks.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/tasks/dto/tasks.dto.ts) | `2.7 KB` |
| [rankray-hq-backend/src/tasks/tasks.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/tasks/tasks.controller.ts) | `1.4 KB` |
| [rankray-hq-backend/src/tasks/tasks.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/tasks/tasks.module.ts) | `275 B` |
| [rankray-hq-backend/src/tasks/tasks.service.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/tasks/tasks.service.spec.ts) | `4.9 KB` |
| [rankray-hq-backend/src/tasks/tasks.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/tasks/tasks.service.ts) | `12.6 KB` |

---

<a name="crm-leads--clients"></a>
## CRM (Leads & Clients)
**Description:** Sales pipeline and client relationship management, company directories, contact records, and website-to-company linkages.

**Relational Data Models (Prisma):** `Lead`, `Company`, `Contact`, `Deal`

### 🖥️ Frontend Scope (21 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-frontend/src/modules/crm/CRM.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/CRM.tsx) | `5.0 KB` |
| [rankray-hq-frontend/src/modules/crm/components/AddActivityModal.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/components/AddActivityModal.tsx) | `3.5 KB` |
| [rankray-hq-frontend/src/modules/crm/components/AddCompanyModal.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/components/AddCompanyModal.tsx) | `3.2 KB` |
| [rankray-hq-frontend/src/modules/crm/components/AddContactModal.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/components/AddContactModal.tsx) | `3.6 KB` |
| [rankray-hq-frontend/src/modules/crm/components/AddDealModal.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/components/AddDealModal.tsx) | `7.6 KB` |
| [rankray-hq-frontend/src/modules/crm/components/CRMStats.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/components/CRMStats.tsx) | `1.6 KB` |
| [rankray-hq-frontend/src/modules/crm/components/CompanyCard.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/components/CompanyCard.tsx) | `5.2 KB` |
| [rankray-hq-frontend/src/modules/crm/components/CompanyModal.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/components/CompanyModal.tsx) | `4.8 KB` |
| [rankray-hq-frontend/src/modules/crm/components/ContactModal.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/components/ContactModal.tsx) | `4.4 KB` |
| [rankray-hq-frontend/src/modules/crm/components/CrmSidebar.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/components/CrmSidebar.tsx) | `1.1 KB` |
| [rankray-hq-frontend/src/modules/crm/components/DealModal.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/components/DealModal.tsx) | `5.4 KB` |
| [rankray-hq-frontend/src/modules/crm/components/StatCard.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/components/StatCard.tsx) | `1.1 KB` |
| [rankray-hq-frontend/src/modules/crm/constants.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/constants.ts) | `1000 B` |
| [rankray-hq-frontend/src/modules/crm/sections/Activity.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/sections/Activity.tsx) | `2.0 KB` |
| [rankray-hq-frontend/src/modules/crm/sections/Companies.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/sections/Companies.tsx) | `8.9 KB` |
| [rankray-hq-frontend/src/modules/crm/sections/CompanyProfile.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/sections/CompanyProfile.tsx) | `45.4 KB` |
| [rankray-hq-frontend/src/modules/crm/sections/Contacts.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/sections/Contacts.tsx) | `7.6 KB` |
| [rankray-hq-frontend/src/modules/crm/sections/Pipeline.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/sections/Pipeline.tsx) | `10.3 KB` |
| [rankray-hq-frontend/src/modules/crm/sections/Reports.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/sections/Reports.tsx) | `4.2 KB` |
| [rankray-hq-frontend/src/modules/crm/sections/Tasks.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/sections/Tasks.tsx) | `4.6 KB` |
| [rankray-hq-frontend/src/modules/crm/services/crm.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/crm/services/crm.service.ts) | `3.4 KB` |

### ⚙️ Backend Scope (11 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-backend/src/crm/company-contacts.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/crm/company-contacts.controller.ts) | `2.9 KB` |
| [rankray-hq-backend/src/crm/contacts.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/crm/contacts.controller.ts) | `976 B` |
| [rankray-hq-backend/src/crm/crm.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/crm/crm.controller.ts) | `4.9 KB` |
| [rankray-hq-backend/src/crm/crm.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/crm/crm.module.ts) | `820 B` |
| [rankray-hq-backend/src/crm/dto/create-activity.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/crm/dto/create-activity.dto.ts) | `550 B` |
| [rankray-hq-backend/src/crm/dto/crm.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/crm/dto/crm.dto.ts) | `2.6 KB` |
| [rankray-hq-backend/src/crm/dto/update-activity.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/crm/dto/update-activity.dto.ts) | `531 B` |
| [rankray-hq-backend/src/crm/services/activity.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/crm/services/activity.service.ts) | `3.6 KB` |
| [rankray-hq-backend/src/crm/services/company.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/crm/services/company.service.ts) | `25.2 KB` |
| [rankray-hq-backend/src/crm/services/contact.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/crm/services/contact.service.ts) | `3.6 KB` |
| [rankray-hq-backend/src/crm/services/deal.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/crm/services/deal.service.ts) | `6.8 KB` |

---

<a name="seo-suite"></a>
## SEO Suite
**Description:** Central SEO command suite containing keyword lookup tools, competitor analysis, rank tracking metrics, backlink auditing, and technical audit crawls.

**Relational Data Models (Prisma):** `SeoWebsite`, `TrackedKeyword`, `KeywordSnapshot`, `SiteAuditRun`, `SiteAuditIssue`, `BacklinkOpportunity`

### 🖥️ Frontend Scope (91 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-frontend/src/modules/seo/SEO.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/SEO.tsx) | `4.8 KB` |
| [rankray-hq-frontend/src/modules/seo/content/briefs/BriefsPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/content/briefs/BriefsPage.tsx) | `7.2 KB` |
| [rankray-hq-frontend/src/modules/seo/content/briefs/components/BriefDetailDrawer.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/content/briefs/components/BriefDetailDrawer.tsx) | `7.1 KB` |
| [rankray-hq-frontend/src/modules/seo/content/briefs/components/BriefsTable.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/content/briefs/components/BriefsTable.tsx) | `4.8 KB` |
| [rankray-hq-frontend/src/modules/seo/content/briefs/components/StatusBadge.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/content/briefs/components/StatusBadge.tsx) | `1.2 KB` |
| [rankray-hq-frontend/src/modules/seo/content/briefs/hooks/useBriefs.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/content/briefs/hooks/useBriefs.ts) | `1.3 KB` |
| [rankray-hq-frontend/src/modules/seo/content/briefs/services/briefsService.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/content/briefs/services/briefsService.ts) | `8.3 KB` |
| [rankray-hq-frontend/src/modules/seo/content/briefs/services/briefsTypes.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/content/briefs/services/briefsTypes.ts) | `1.8 KB` |
| [rankray-hq-frontend/src/modules/seo/content/gaps/ContentGapsPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/content/gaps/ContentGapsPage.tsx) | `9.4 KB` |
| [rankray-hq-frontend/src/modules/seo/content/gaps/components/ContentGapsTable.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/content/gaps/components/ContentGapsTable.tsx) | `5.6 KB` |
| [rankray-hq-frontend/src/modules/seo/content/gaps/components/DomainStack.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/content/gaps/components/DomainStack.tsx) | `1.6 KB` |
| [rankray-hq-frontend/src/modules/seo/content/gaps/components/KeywordGapsTable.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/content/gaps/components/KeywordGapsTable.tsx) | `6.7 KB` |
| [rankray-hq-frontend/src/modules/seo/content/gaps/hooks/useContentGaps.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/content/gaps/hooks/useContentGaps.ts) | `1.7 KB` |
| [rankray-hq-frontend/src/modules/seo/content/gaps/services/gapsService.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/content/gaps/services/gapsService.ts) | `12.0 KB` |
| [rankray-hq-frontend/src/modules/seo/content/gaps/services/gapsTypes.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/content/gaps/services/gapsTypes.ts) | `2.5 KB` |
| [rankray-hq-frontend/src/modules/seo/dashboard/DashboardPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/dashboard/DashboardPage.tsx) | `4.4 KB` |
| [rankray-hq-frontend/src/modules/seo/dashboard/components/ModuleStatCard.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/dashboard/components/ModuleStatCard.tsx) | `6.3 KB` |
| [rankray-hq-frontend/src/modules/seo/dashboard/hooks/useDashboardCards.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/dashboard/hooks/useDashboardCards.ts) | `2.4 KB` |
| [rankray-hq-frontend/src/modules/seo/dashboard/hooks/useIntelligenceFeed.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/dashboard/hooks/useIntelligenceFeed.ts) | `1.9 KB` |
| [rankray-hq-frontend/src/modules/seo/dashboard/services/dashboardService.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/dashboard/services/dashboardService.ts) | `10.8 KB` |
| [rankray-hq-frontend/src/modules/seo/dashboard/services/dashboardTypes.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/dashboard/services/dashboardTypes.ts) | `1.5 KB` |
| [rankray-hq-frontend/src/modules/seo/layout/SEOShell.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/layout/SEOShell.tsx) | `2.2 KB` |
| [rankray-hq-frontend/src/modules/seo/layout/seoNav.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/layout/seoNav.ts) | `4.2 KB` |
| [rankray-hq-frontend/src/modules/seo/pages/PlaceholderPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/pages/PlaceholderPage.tsx) | `1.0 KB` |
| [rankray-hq-frontend/src/modules/seo/research/clusters/ClustersPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/clusters/ClustersPage.tsx) | `9.7 KB` |
| [rankray-hq-frontend/src/modules/seo/research/clusters/components/ClusterCard.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/clusters/components/ClusterCard.tsx) | `5.1 KB` |
| [rankray-hq-frontend/src/modules/seo/research/clusters/components/ClusterFilters.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/clusters/components/ClusterFilters.tsx) | `2.9 KB` |
| [rankray-hq-frontend/src/modules/seo/research/clusters/components/ClusterKeywordsTable.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/clusters/components/ClusterKeywordsTable.tsx) | `4.6 KB` |
| [rankray-hq-frontend/src/modules/seo/research/clusters/hooks/useClusters.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/clusters/hooks/useClusters.ts) | `1.7 KB` |
| [rankray-hq-frontend/src/modules/seo/research/clusters/services/clustersService.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/clusters/services/clustersService.ts) | `10.8 KB` |
| [rankray-hq-frontend/src/modules/seo/research/clusters/services/clustersTypes.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/clusters/services/clustersTypes.ts) | `2.9 KB` |
| [rankray-hq-frontend/src/modules/seo/research/keywords/KeywordExplorerPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/keywords/KeywordExplorerPage.tsx) | `7.8 KB` |
| [rankray-hq-frontend/src/modules/seo/research/keywords/components/CompetitorLookupWidget.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/keywords/components/CompetitorLookupWidget.tsx) | `5.8 KB` |
| [rankray-hq-frontend/src/modules/seo/research/keywords/components/IntentBadge.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/keywords/components/IntentBadge.tsx) | `1.4 KB` |
| [rankray-hq-frontend/src/modules/seo/research/keywords/components/KeywordExplorerTable.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/keywords/components/KeywordExplorerTable.tsx) | `5.6 KB` |
| [rankray-hq-frontend/src/modules/seo/research/keywords/components/KeywordFilters.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/keywords/components/KeywordFilters.tsx) | `5.4 KB` |
| [rankray-hq-frontend/src/modules/seo/research/keywords/components/KeywordLookupWidget.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/keywords/components/KeywordLookupWidget.tsx) | `9.3 KB` |
| [rankray-hq-frontend/src/modules/seo/research/keywords/hooks/useKeywordExplorer.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/keywords/hooks/useKeywordExplorer.ts) | `1.7 KB` |
| [rankray-hq-frontend/src/modules/seo/research/keywords/hooks/useKeywordFilters.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/keywords/hooks/useKeywordFilters.ts) | `5.6 KB` |
| [rankray-hq-frontend/src/modules/seo/research/keywords/services/keywordExplorerService.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/keywords/services/keywordExplorerService.ts) | `8.1 KB` |
| [rankray-hq-frontend/src/modules/seo/research/keywords/services/keywordExplorerTypes.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/keywords/services/keywordExplorerTypes.ts) | `2.9 KB` |
| [rankray-hq-frontend/src/modules/seo/research/serp/SERPMonitorPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/serp/SERPMonitorPage.tsx) | `8.7 KB` |
| [rankray-hq-frontend/src/modules/seo/research/serp/components/ChangesTable.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/serp/components/ChangesTable.tsx) | `6.3 KB` |
| [rankray-hq-frontend/src/modules/seo/research/serp/components/CompetitorsTable.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/serp/components/CompetitorsTable.tsx) | `4.3 KB` |
| [rankray-hq-frontend/src/modules/seo/research/serp/components/FeaturesGrid.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/serp/components/FeaturesGrid.tsx) | `2.9 KB` |
| [rankray-hq-frontend/src/modules/seo/research/serp/hooks/useSerpMonitor.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/serp/hooks/useSerpMonitor.ts) | `1.9 KB` |
| [rankray-hq-frontend/src/modules/seo/research/serp/services/serpService.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/serp/services/serpService.ts) | `13.4 KB` |
| [rankray-hq-frontend/src/modules/seo/research/serp/services/serpTypes.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/research/serp/services/serpTypes.ts) | `3.7 KB` |
| [rankray-hq-frontend/src/modules/seo/settings/SettingsPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/settings/SettingsPage.tsx) | `9.0 KB` |
| [rankray-hq-frontend/src/modules/seo/settings/components/AddWebsiteDialog.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/settings/components/AddWebsiteDialog.tsx) | `4.6 KB` |
| [rankray-hq-frontend/src/modules/seo/settings/components/EditWebsiteDialog.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/settings/components/EditWebsiteDialog.tsx) | `4.3 KB` |
| [rankray-hq-frontend/src/modules/seo/settings/components/ImportFromGscWizard.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/settings/components/ImportFromGscWizard.tsx) | `11.5 KB` |
| [rankray-hq-frontend/src/modules/seo/settings/components/IntegrationStatusPills.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/settings/components/IntegrationStatusPills.tsx) | `3.3 KB` |
| [rankray-hq-frontend/src/modules/seo/settings/components/WebsitesTable.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/settings/components/WebsitesTable.tsx) | `4.8 KB` |
| [rankray-hq-frontend/src/modules/seo/settings/components/WorkspaceProvidersCard.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/settings/components/WorkspaceProvidersCard.tsx) | `7.3 KB` |
| [rankray-hq-frontend/src/modules/seo/settings/hooks/useSeoSettings.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/settings/hooks/useSeoSettings.ts) | `3.3 KB` |
| [rankray-hq-frontend/src/modules/seo/settings/hooks/useWorkspaceProviders.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/settings/hooks/useWorkspaceProviders.ts) | `1.8 KB` |
| [rankray-hq-frontend/src/modules/seo/settings/services/importService.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/settings/services/importService.ts) | `3.0 KB` |
| [rankray-hq-frontend/src/modules/seo/settings/services/providersService.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/settings/services/providersService.ts) | `3.3 KB` |
| [rankray-hq-frontend/src/modules/seo/settings/services/settingsService.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/settings/services/settingsService.ts) | `6.1 KB` |
| [rankray-hq-frontend/src/modules/seo/settings/services/settingsTypes.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/settings/services/settingsTypes.ts) | `2.3 KB` |
| [rankray-hq-frontend/src/modules/seo/settings/setupGuidance.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/settings/setupGuidance.ts) | `2.8 KB` |
| [rankray-hq-frontend/src/modules/seo/site/audit/SiteAuditPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/audit/SiteAuditPage.tsx) | `7.3 KB` |
| [rankray-hq-frontend/src/modules/seo/site/audit/components/AuditCategoryTrack.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/audit/components/AuditCategoryTrack.tsx) | `1.5 KB` |
| [rankray-hq-frontend/src/modules/seo/site/audit/components/HealthOverview.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/audit/components/HealthOverview.tsx) | `1.8 KB` |
| [rankray-hq-frontend/src/modules/seo/site/audit/components/IssueGroupList.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/audit/components/IssueGroupList.tsx) | `2.2 KB` |
| [rankray-hq-frontend/src/modules/seo/site/audit/components/IssueInspector.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/audit/components/IssueInspector.tsx) | `3.7 KB` |
| [rankray-hq-frontend/src/modules/seo/site/audit/components/IssueRow.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/audit/components/IssueRow.tsx) | `1.5 KB` |
| [rankray-hq-frontend/src/modules/seo/site/audit/hooks/useIssuePages.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/audit/hooks/useIssuePages.ts) | `1.7 KB` |
| [rankray-hq-frontend/src/modules/seo/site/audit/hooks/useSiteAudit.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/audit/hooks/useSiteAudit.ts) | `1.9 KB` |
| [rankray-hq-frontend/src/modules/seo/site/audit/services/auditService.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/audit/services/auditService.ts) | `7.8 KB` |
| [rankray-hq-frontend/src/modules/seo/site/audit/services/auditTypes.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/audit/services/auditTypes.ts) | `1.8 KB` |
| [rankray-hq-frontend/src/modules/seo/site/backlinks/BacklinksPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/backlinks/BacklinksPage.tsx) | `10.4 KB` |
| [rankray-hq-frontend/src/modules/seo/site/backlinks/components/AnchorDistribution.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/backlinks/components/AnchorDistribution.tsx) | `2.1 KB` |
| [rankray-hq-frontend/src/modules/seo/site/backlinks/components/BacklinkFilters.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/backlinks/components/BacklinkFilters.tsx) | `3.1 KB` |
| [rankray-hq-frontend/src/modules/seo/site/backlinks/components/BacklinkTable.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/backlinks/components/BacklinkTable.tsx) | `5.6 KB` |
| [rankray-hq-frontend/src/modules/seo/site/backlinks/components/BacklinksLookupWidget.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/backlinks/components/BacklinksLookupWidget.tsx) | `5.0 KB` |
| [rankray-hq-frontend/src/modules/seo/site/backlinks/components/RDChart.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/backlinks/components/RDChart.tsx) | `3.4 KB` |
| [rankray-hq-frontend/src/modules/seo/site/backlinks/hooks/useBacklinks.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/backlinks/hooks/useBacklinks.ts) | `1.8 KB` |
| [rankray-hq-frontend/src/modules/seo/site/backlinks/services/backlinksService.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/backlinks/services/backlinksService.ts) | `11.2 KB` |
| [rankray-hq-frontend/src/modules/seo/site/backlinks/services/backlinksTypes.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/backlinks/services/backlinksTypes.ts) | `3.5 KB` |
| [rankray-hq-frontend/src/modules/seo/site/ranks/RankTrackerPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/ranks/RankTrackerPage.tsx) | `8.1 KB` |
| [rankray-hq-frontend/src/modules/seo/site/ranks/components/AddKeywordDialog.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/ranks/components/AddKeywordDialog.tsx) | `2.9 KB` |
| [rankray-hq-frontend/src/modules/seo/site/ranks/components/KeywordTable.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/ranks/components/KeywordTable.tsx) | `5.8 KB` |
| [rankray-hq-frontend/src/modules/seo/site/ranks/components/PropertyCard.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/ranks/components/PropertyCard.tsx) | `2.5 KB` |
| [rankray-hq-frontend/src/modules/seo/site/ranks/components/PropertyGrid.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/ranks/components/PropertyGrid.tsx) | `1.1 KB` |
| [rankray-hq-frontend/src/modules/seo/site/ranks/components/SerpStripCell.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/ranks/components/SerpStripCell.tsx) | `1.6 KB` |
| [rankray-hq-frontend/src/modules/seo/site/ranks/hooks/useRankTracker.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/ranks/hooks/useRankTracker.ts) | `1.7 KB` |
| [rankray-hq-frontend/src/modules/seo/site/ranks/services/rankTrackerService.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/ranks/services/rankTrackerService.ts) | `11.0 KB` |
| [rankray-hq-frontend/src/modules/seo/site/ranks/services/rankTrackerTypes.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/site/ranks/services/rankTrackerTypes.ts) | `2.3 KB` |
| [rankray-hq-frontend/src/modules/seo/store/websiteContext.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/seo/store/websiteContext.ts) | `1.9 KB` |

### ⚙️ Backend Scope (103 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-backend/src/seo/ai/ai.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/ai/ai.controller.ts) | `3.5 KB` |
| [rankray-hq-backend/src/seo/ai/ai.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/ai/ai.module.ts) | `567 B` |
| [rankray-hq-backend/src/seo/ai/ai.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/ai/ai.service.ts) | `30.9 KB` |
| [rankray-hq-backend/src/seo/ai/interfaces/ai-provider.interface.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/ai/interfaces/ai-provider.interface.ts) | `363 B` |
| [rankray-hq-backend/src/seo/ai/providers/anthropic.provider.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/ai/providers/anthropic.provider.ts) | `1.7 KB` |
| [rankray-hq-backend/src/seo/ai/providers/gemini.provider.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/ai/providers/gemini.provider.ts) | `1.7 KB` |
| [rankray-hq-backend/src/seo/ai/providers/ollama-cloud.provider.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/ai/providers/ollama-cloud.provider.ts) | `3.1 KB` |
| [rankray-hq-backend/src/seo/ai/providers/openai.provider.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/ai/providers/openai.provider.ts) | `1.7 KB` |
| [rankray-hq-backend/src/seo/analytics.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/analytics.controller.ts) | `1.8 KB` |
| [rankray-hq-backend/src/seo/backlinks.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/backlinks.controller.ts) | `3.6 KB` |
| [rankray-hq-backend/src/seo/content/content.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/content/content.controller.ts) | `2.8 KB` |
| [rankray-hq-backend/src/seo/content/content.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/content/content.module.ts) | `834 B` |
| [rankray-hq-backend/src/seo/content/content.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/content/content.service.ts) | `3.8 KB` |
| [rankray-hq-backend/src/seo/content/dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/content/dto.ts) | `1.4 KB` |
| [rankray-hq-backend/src/seo/content/services/content-strategy.processor.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/content/services/content-strategy.processor.ts) | `3.7 KB` |
| [rankray-hq-backend/src/seo/data-sources/data-source.interface.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/data-sources/data-source.interface.ts) | `2.4 KB` |
| [rankray-hq-backend/src/seo/data-sources/data-sources.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/data-sources/data-sources.module.ts) | `617 B` |
| [rankray-hq-backend/src/seo/data-sources/gsc/gsc-data-source.adapter.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/data-sources/gsc/gsc-data-source.adapter.ts) | `2.8 KB` |
| [rankray-hq-backend/src/seo/data-sources/keyword-providers/ahrefs.provider.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/data-sources/keyword-providers/ahrefs.provider.ts) | `4.2 KB` |
| [rankray-hq-backend/src/seo/data-sources/keyword-providers/dataforseo.provider.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/data-sources/keyword-providers/dataforseo.provider.ts) | `4.4 KB` |
| [rankray-hq-backend/src/seo/data-sources/keyword-providers/keyword-provider.interface.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/data-sources/keyword-providers/keyword-provider.interface.ts) | `4.4 KB` |
| [rankray-hq-backend/src/seo/data-sources/keyword-providers/semrush.provider.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/data-sources/keyword-providers/semrush.provider.ts) | `11.9 KB` |
| [rankray-hq-backend/src/seo/data-sources/keyword-providers/seo-data-provider.registry.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/data-sources/keyword-providers/seo-data-provider.registry.ts) | `6.8 KB` |
| [rankray-hq-backend/src/seo/dto/backlinks.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/dto/backlinks.dto.ts) | `1.6 KB` |
| [rankray-hq-backend/src/seo/dto/opportunity.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/dto/opportunity.dto.ts) | `1.6 KB` |
| [rankray-hq-backend/src/seo/dto/seo.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/dto/seo.dto.ts) | `3.3 KB` |
| [rankray-hq-backend/src/seo/dto/sync-status.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/dto/sync-status.dto.ts) | `580 B` |
| [rankray-hq-backend/src/seo/keywords-overview.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/keywords-overview.controller.ts) | `3.9 KB` |
| [rankray-hq-backend/src/seo/mentions.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/mentions.controller.ts) | `2.1 KB` |
| [rankray-hq-backend/src/seo/plugins/gsc-intelligence/gsc-intelligence-payload.type.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/plugins/gsc-intelligence/gsc-intelligence-payload.type.ts) | `715 B` |
| [rankray-hq-backend/src/seo/plugins/gsc-intelligence/gsc-intelligence.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/plugins/gsc-intelligence/gsc-intelligence.module.ts) | `943 B` |
| [rankray-hq-backend/src/seo/plugins/gsc-intelligence/gsc-intelligence.plugin.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/plugins/gsc-intelligence/gsc-intelligence.plugin.ts) | `9.7 KB` |
| [rankray-hq-backend/src/seo/plugins/position-tracking/position-tracking-payload.type.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/plugins/position-tracking/position-tracking-payload.type.ts) | `2.0 KB` |
| [rankray-hq-backend/src/seo/plugins/position-tracking/position-tracking.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/plugins/position-tracking/position-tracking.controller.ts) | `7.0 KB` |
| [rankray-hq-backend/src/seo/plugins/position-tracking/position-tracking.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/plugins/position-tracking/position-tracking.module.ts) | `1004 B` |
| [rankray-hq-backend/src/seo/plugins/position-tracking/position-tracking.plugin.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/plugins/position-tracking/position-tracking.plugin.ts) | `12.7 KB` |
| [rankray-hq-backend/src/seo/plugins/position-tracking/serp-scraper.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/plugins/position-tracking/serp-scraper.service.ts) | `7.3 KB` |
| [rankray-hq-backend/src/seo/plugins/site-audit/site-audit-payload.type.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/plugins/site-audit/site-audit-payload.type.ts) | `1.2 KB` |
| [rankray-hq-backend/src/seo/plugins/site-audit/site-audit.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/plugins/site-audit/site-audit.module.ts) | `955 B` |
| [rankray-hq-backend/src/seo/plugins/site-audit/site-audit.plugin.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/plugins/site-audit/site-audit.plugin.ts) | `9.3 KB` |
| [rankray-hq-backend/src/seo/publishing/dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/publishing/dto.ts) | `1.7 KB` |
| [rankray-hq-backend/src/seo/publishing/image-gen.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/publishing/image-gen.service.ts) | `4.2 KB` |
| [rankray-hq-backend/src/seo/publishing/publishing.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/publishing/publishing.controller.ts) | `2.8 KB` |
| [rankray-hq-backend/src/seo/publishing/publishing.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/publishing/publishing.module.ts) | `698 B` |
| [rankray-hq-backend/src/seo/publishing/publishing.service.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/publishing/publishing.service.spec.ts) | `6.7 KB` |
| [rankray-hq-backend/src/seo/publishing/publishing.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/publishing/publishing.service.ts) | `39.7 KB` |
| [rankray-hq-backend/src/seo/publishing/videos/remotion/remotion.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/publishing/videos/remotion/remotion.service.ts) | `3.0 KB` |
| [rankray-hq-backend/src/seo/publishing/videos/remotion/template.registry.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/publishing/videos/remotion/template.registry.ts) | `1.3 KB` |
| [rankray-hq-backend/src/seo/publishing/videos/remotion/templates/SeoPromoTemplate.entry.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/publishing/videos/remotion/templates/SeoPromoTemplate.entry.tsx) | `124 B` |
| [rankray-hq-backend/src/seo/publishing/videos/remotion/templates/SeoPromoTemplate.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/publishing/videos/remotion/templates/SeoPromoTemplate.tsx) | `6.6 KB` |
| [rankray-hq-backend/src/seo/publishing/videos/remotion/templates/SimpleTemplate.entry.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/publishing/videos/remotion/templates/SimpleTemplate.entry.tsx) | `122 B` |
| [rankray-hq-backend/src/seo/publishing/videos/remotion/templates/SimpleTemplate.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/publishing/videos/remotion/templates/SimpleTemplate.tsx) | `1.2 KB` |
| [rankray-hq-backend/src/seo/publishing/videos/videos.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/publishing/videos/videos.controller.ts) | `1.8 KB` |
| [rankray-hq-backend/src/seo/publishing/videos/videos.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/publishing/videos/videos.dto.ts) | `1001 B` |
| [rankray-hq-backend/src/seo/publishing/videos/videos.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/publishing/videos/videos.module.ts) | `536 B` |
| [rankray-hq-backend/src/seo/publishing/videos/videos.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/publishing/videos/videos.service.ts) | `3.3 KB` |
| [rankray-hq-backend/src/seo/reports.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/reports.controller.ts) | `2.8 KB` |
| [rankray-hq-backend/src/seo/seo.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/seo.controller.ts) | `80.9 KB` |
| [rankray-hq-backend/src/seo/seo.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/seo.module.ts) | `5.8 KB` |
| [rankray-hq-backend/src/seo/seo.opportunities.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/seo.opportunities.spec.ts) | `7.7 KB` |
| [rankray-hq-backend/src/seo/seo.service.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/seo.service.spec.ts) | `89.9 KB` |
| [rankray-hq-backend/src/seo/seo.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/seo.service.ts) | `381.8 KB` |
| [rankray-hq-backend/src/seo/seo.sync-status.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/seo.sync-status.spec.ts) | `2.2 KB` |
| [rankray-hq-backend/src/seo/services/analytics-sync.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/analytics-sync.service.ts) | `15.6 KB` |
| [rankray-hq-backend/src/seo/services/analytics.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/analytics.service.ts) | `11.6 KB` |
| [rankray-hq-backend/src/seo/services/authority-builder.service.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/authority-builder.service.spec.ts) | `7.5 KB` |
| [rankray-hq-backend/src/seo/services/authority-builder.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/authority-builder.service.ts) | `29.5 KB` |
| [rankray-hq-backend/src/seo/services/backlink-intelligence.service.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/backlink-intelligence.service.spec.ts) | `10.1 KB` |
| [rankray-hq-backend/src/seo/services/backlink-intelligence.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/backlink-intelligence.service.ts) | `58.4 KB` |
| [rankray-hq-backend/src/seo/services/backlinks.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/backlinks.service.ts) | `7.4 KB` |
| [rankray-hq-backend/src/seo/services/cannibalization.service.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/cannibalization.service.spec.ts) | `5.4 KB` |
| [rankray-hq-backend/src/seo/services/cannibalization.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/cannibalization.service.ts) | `30.2 KB` |
| [rankray-hq-backend/src/seo/services/content-planner.service.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/content-planner.service.spec.ts) | `7.1 KB` |
| [rankray-hq-backend/src/seo/services/content-planner.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/content-planner.service.ts) | `30.9 KB` |
| [rankray-hq-backend/src/seo/services/crawler.service.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/crawler.service.spec.ts) | `7.7 KB` |
| [rankray-hq-backend/src/seo/services/crawler.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/crawler.service.ts) | `36.4 KB` |
| [rankray-hq-backend/src/seo/services/firehose.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/firehose.service.ts) | `8.2 KB` |
| [rankray-hq-backend/src/seo/services/gsc.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/gsc.module.ts) | `402 B` |
| [rankray-hq-backend/src/seo/services/gsc.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/gsc.service.ts) | `11.0 KB` |
| [rankray-hq-backend/src/seo/services/keyword-strategy.processor.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/keyword-strategy.processor.ts) | `7.7 KB` |
| [rankray-hq-backend/src/seo/services/next-action.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/next-action.service.ts) | `8.9 KB` |
| [rankray-hq-backend/src/seo/services/report-generator.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/report-generator.service.ts) | `7.5 KB` |
| [rankray-hq-backend/src/seo/services/report.scheduler.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/report.scheduler.ts) | `1.8 KB` |
| [rankray-hq-backend/src/seo/services/seo-audit.processor.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/seo-audit.processor.ts) | `7.0 KB` |
| [rankray-hq-backend/src/seo/services/seo-automation.processor.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/seo-automation.processor.ts) | `11.8 KB` |
| [rankray-hq-backend/src/seo/services/seo-performance.service.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/seo-performance.service.spec.ts) | `5.1 KB` |
| [rankray-hq-backend/src/seo/services/seo-performance.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/seo-performance.service.ts) | `26.8 KB` |
| [rankray-hq-backend/src/seo/services/serpbear.service.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/serpbear.service.spec.ts) | `3.5 KB` |
| [rankray-hq-backend/src/seo/services/serpbear.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/serpbear.service.ts) | `20.1 KB` |
| [rankray-hq-backend/src/seo/services/site-audit-rules.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/site-audit-rules.service.ts) | `9.7 KB` |
| [rankray-hq-backend/src/seo/services/site-audit.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/site-audit.service.ts) | `21.7 KB` |
| [rankray-hq-backend/src/seo/services/sync.service.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/sync.service.spec.ts) | `8.1 KB` |
| [rankray-hq-backend/src/seo/services/sync.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/services/sync.service.ts) | `45.5 KB` |
| [rankray-hq-backend/src/seo/spine/interfaces/seo-job.interface.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/spine/interfaces/seo-job.interface.ts) | `1.1 KB` |
| [rankray-hq-backend/src/seo/spine/interfaces/seo-nav.interface.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/spine/interfaces/seo-nav.interface.ts) | `893 B` |
| [rankray-hq-backend/src/seo/spine/interfaces/seo-plugin.interface.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/spine/interfaces/seo-plugin.interface.ts) | `2.5 KB` |
| [rankray-hq-backend/src/seo/spine/seo-plugin.registry.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/spine/seo-plugin.registry.ts) | `3.2 KB` |
| [rankray-hq-backend/src/seo/spine/seo-spine.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/spine/seo-spine.controller.ts) | `3.9 KB` |
| [rankray-hq-backend/src/seo/spine/spine.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/spine/spine.module.ts) | `798 B` |
| [rankray-hq-backend/src/seo/utils/robots.util.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/utils/robots.util.ts) | `1.2 KB` |
| [rankray-hq-backend/src/seo/utils/ssrf.util.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/utils/ssrf.util.ts) | `2.1 KB` |
| [rankray-hq-backend/src/seo/utils/url.util.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/utils/url.util.ts) | `1.5 KB` |
| [rankray-hq-backend/src/seo/website-control.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/seo/website-control.controller.ts) | `2.4 KB` |

---

<a name="publishing--cms-connection"></a>
## Publishing & CMS Connection
**Description:** Content distribution system mapping blog and image publishing runs to external content management platforms like WordPress.

**Relational Data Models (Prisma):** `PublishHistory`, `WordPressConnection`

### 🖥️ Frontend Scope (6 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-frontend/src/modules/publishing/Publishing.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/publishing/Publishing.tsx) | `2.3 KB` |
| [rankray-hq-frontend/src/modules/publishing/components/PublishingSidebar.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/publishing/components/PublishingSidebar.tsx) | `703 B` |
| [rankray-hq-frontend/src/modules/publishing/sections/BlogsSubModule.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/publishing/sections/BlogsSubModule.tsx) | `6.6 KB` |
| [rankray-hq-frontend/src/modules/publishing/sections/CreateVideoDialog.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/publishing/sections/CreateVideoDialog.tsx) | `7.7 KB` |
| [rankray-hq-frontend/src/modules/publishing/sections/ImagesSubModule.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/publishing/sections/ImagesSubModule.tsx) | `8.0 KB` |
| [rankray-hq-frontend/src/modules/publishing/sections/VideosSubModule.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/publishing/sections/VideosSubModule.tsx) | `9.9 KB` |

### ⚙️ Backend Scope (0 files)
*No dedicated backend module files in this scope.*

---

<a name="outreach--sourcing"></a>
## Outreach & Sourcing
**Description:** Cold outreach campaign management, prospective client lead scraping, status tracking, and automated drafting workflows.

**Relational Data Models (Prisma):** `OutreachCampaign`, `OutreachProspect`

### 🖥️ Frontend Scope (1 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-frontend/src/modules/outreach/Outreach.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/outreach/Outreach.tsx) | `29.7 KB` |

### ⚙️ Backend Scope (4 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-backend/src/outreach/dto/outreach.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/outreach/dto/outreach.dto.ts) | `1006 B` |
| [rankray-hq-backend/src/outreach/outreach.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/outreach/outreach.controller.ts) | `2.2 KB` |
| [rankray-hq-backend/src/outreach/outreach.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/outreach/outreach.module.ts) | `382 B` |
| [rankray-hq-backend/src/outreach/outreach.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/outreach/outreach.service.ts) | `2.2 KB` |

---

<a name="hrm-team--employees"></a>
## HRM (Team & Employees)
**Description:** Internal team profiles, assignee avatars mapping, role classifications, permissions (RBAC), and employee directory controls.

**Relational Data Models (Prisma):** `Employee`, `Department`

### 🖥️ Frontend Scope (5 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-frontend/src/modules/hrm/HRM.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/hrm/HRM.tsx) | `33.9 KB` |
| [rankray-hq-frontend/src/modules/hrm/components/EmployeeDashboard.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/hrm/components/EmployeeDashboard.tsx) | `10.9 KB` |
| [rankray-hq-frontend/src/modules/hrm/components/EmployeeModal.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/hrm/components/EmployeeModal.tsx) | `5.6 KB` |
| [rankray-hq-frontend/src/modules/hrm/components/HrmSidebar.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/hrm/components/HrmSidebar.tsx) | `982 B` |
| [rankray-hq-frontend/src/modules/hrm/components/PayrollModal.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/hrm/components/PayrollModal.tsx) | `1.9 KB` |

### ⚙️ Backend Scope (7 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-backend/src/hrm/dto/hrm-extension.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/hrm/dto/hrm-extension.dto.ts) | `1.2 KB` |
| [rankray-hq-backend/src/hrm/dto/hrm.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/hrm/dto/hrm.dto.ts) | `992 B` |
| [rankray-hq-backend/src/hrm/dto/workforce.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/hrm/dto/workforce.dto.ts) | `1.0 KB` |
| [rankray-hq-backend/src/hrm/employees.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/hrm/employees.controller.ts) | `934 B` |
| [rankray-hq-backend/src/hrm/hrm.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/hrm/hrm.controller.ts) | `4.6 KB` |
| [rankray-hq-backend/src/hrm/hrm.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/hrm/hrm.module.ts) | `425 B` |
| [rankray-hq-backend/src/hrm/hrm.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/hrm/hrm.service.ts) | `14.9 KB` |

---

<a name="finance--billing"></a>
## Finance & Billing
**Description:** Double-entry bookkeeping, Stripe/PayPal webhooks, public pricing catalogs, invoices, and expense logs.

**Relational Data Models (Prisma):** `Invoice`, `Payment`, `Expense`, `BankAccount`, `RecurringTransaction`, `Plan`, `Subscription`, `BillingEvent`

### 🖥️ Frontend Scope (30 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-frontend/src/modules/billing/checkout/CheckoutCancelPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/billing/checkout/CheckoutCancelPage.tsx) | `934 B` |
| [rankray-hq-frontend/src/modules/billing/checkout/CheckoutSuccessPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/billing/checkout/CheckoutSuccessPage.tsx) | `1.5 KB` |
| [rankray-hq-frontend/src/modules/billing/pricing/PricingPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/billing/pricing/PricingPage.tsx) | `6.6 KB` |
| [rankray-hq-frontend/src/modules/billing/pricing/components/BillingCycleToggle.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/billing/pricing/components/BillingCycleToggle.tsx) | `1.2 KB` |
| [rankray-hq-frontend/src/modules/billing/pricing/components/PricingCard.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/billing/pricing/components/PricingCard.tsx) | `9.4 KB` |
| [rankray-hq-frontend/src/modules/billing/pricing/hooks/usePublicPlans.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/billing/pricing/hooks/usePublicPlans.ts) | `1.1 KB` |
| [rankray-hq-frontend/src/modules/billing/pricing/services/pricingService.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/billing/pricing/services/pricingService.ts) | `1.5 KB` |
| [rankray-hq-frontend/src/modules/billing/pricing/services/pricingTypes.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/billing/pricing/services/pricingTypes.ts) | `1.6 KB` |
| [rankray-hq-frontend/src/modules/billing/settings/BillingSection.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/billing/settings/BillingSection.tsx) | `7.5 KB` |
| [rankray-hq-frontend/src/modules/finance/Finance.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/Finance.tsx) | `12.2 KB` |
| [rankray-hq-frontend/src/modules/finance/components/ColumnVisibilityMenu.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/components/ColumnVisibilityMenu.tsx) | `1.8 KB` |
| [rankray-hq-frontend/src/modules/finance/components/CurrencyConverter.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/components/CurrencyConverter.tsx) | `3.5 KB` |
| [rankray-hq-frontend/src/modules/finance/components/FinanceDropZone.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/components/FinanceDropZone.tsx) | `7.6 KB` |
| [rankray-hq-frontend/src/modules/finance/components/FinanceSidebar.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/components/FinanceSidebar.tsx) | `2.0 KB` |
| [rankray-hq-frontend/src/modules/finance/components/financeSectionLayout.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/components/financeSectionLayout.ts) | `1.7 KB` |
| [rankray-hq-frontend/src/modules/finance/hooks/useFinanceTablePrefs.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/hooks/useFinanceTablePrefs.ts) | `2.4 KB` |
| [rankray-hq-frontend/src/modules/finance/sections/Banks.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/sections/Banks.tsx) | `15.4 KB` |
| [rankray-hq-frontend/src/modules/finance/sections/CreditNotes.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/sections/CreditNotes.tsx) | `11.8 KB` |
| [rankray-hq-frontend/src/modules/finance/sections/Customers.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/sections/Customers.tsx) | `13.5 KB` |
| [rankray-hq-frontend/src/modules/finance/sections/Expenses.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/sections/Expenses.tsx) | `14.6 KB` |
| [rankray-hq-frontend/src/modules/finance/sections/FinanceSettings.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/sections/FinanceSettings.tsx) | `19.9 KB` |
| [rankray-hq-frontend/src/modules/finance/sections/Invoices.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/sections/Invoices.tsx) | `30.4 KB` |
| [rankray-hq-frontend/src/modules/finance/sections/Items.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/sections/Items.tsx) | `14.9 KB` |
| [rankray-hq-frontend/src/modules/finance/sections/PaymentsReceived.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/sections/PaymentsReceived.tsx) | `16.2 KB` |
| [rankray-hq-frontend/src/modules/finance/sections/Quotes.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/sections/Quotes.tsx) | `18.5 KB` |
| [rankray-hq-frontend/src/modules/finance/sections/RecurringInvoices.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/sections/RecurringInvoices.tsx) | `13.1 KB` |
| [rankray-hq-frontend/src/modules/finance/sections/Reports.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/sections/Reports.tsx) | `10.1 KB` |
| [rankray-hq-frontend/src/modules/finance/sections/RetainerInvoices.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/sections/RetainerInvoices.tsx) | `14.2 KB` |
| [rankray-hq-frontend/src/modules/finance/sections/SalesReceipts.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/sections/SalesReceipts.tsx) | `16.4 KB` |
| [rankray-hq-frontend/src/modules/finance/services/finance.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/finance/services/finance.service.ts) | `4.5 KB` |

### ⚙️ Backend Scope (19 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-backend/src/billing/billing.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/billing/billing.controller.ts) | `14.4 KB` |
| [rankray-hq-backend/src/billing/billing.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/billing/billing.module.ts) | `565 B` |
| [rankray-hq-backend/src/billing/crypto.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/billing/crypto.service.ts) | `13.3 KB` |
| [rankray-hq-backend/src/billing/dto/billing.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/billing/dto/billing.dto.ts) | `1.5 KB` |
| [rankray-hq-backend/src/billing/entitlement.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/billing/entitlement.service.ts) | `7.6 KB` |
| [rankray-hq-backend/src/billing/paypal.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/billing/paypal.service.ts) | `29.8 KB` |
| [rankray-hq-backend/src/billing/stripe.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/billing/stripe.service.ts) | `23.2 KB` |
| [rankray-hq-backend/src/finance/dto/expense.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/finance/dto/expense.dto.ts) | `858 B` |
| [rankray-hq-backend/src/finance/dto/finance.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/finance/dto/finance.dto.ts) | `8.7 KB` |
| [rankray-hq-backend/src/finance/finance.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/finance/finance.controller.ts) | `14.7 KB` |
| [rankray-hq-backend/src/finance/finance.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/finance/finance.module.ts) | `1.2 KB` |
| [rankray-hq-backend/src/finance/finance.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/finance/finance.service.ts) | `22.4 KB` |
| [rankray-hq-backend/src/finance/services/expense.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/finance/services/expense.service.ts) | `3.3 KB` |
| [rankray-hq-backend/src/finance/services/invoice.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/finance/services/invoice.service.ts) | `8.9 KB` |
| [rankray-hq-backend/src/finance/services/ledger.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/finance/services/ledger.service.ts) | `1.7 KB` |
| [rankray-hq-backend/src/finance/services/payment.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/finance/services/payment.service.ts) | `9.8 KB` |
| [rankray-hq-backend/src/finance/services/pdf.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/finance/services/pdf.service.ts) | `11.9 KB` |
| [rankray-hq-backend/src/finance/services/receipt-ai.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/finance/services/receipt-ai.service.ts) | `8.9 KB` |
| [rankray-hq-backend/src/finance/services/sales-receipt.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/finance/services/sales-receipt.service.ts) | `3.3 KB` |

---

<a name="assets"></a>
## Assets
**Description:** Repository for uploaded files, images, PDFs, and client branding documents associated with specific campaigns or domains.

**Relational Data Models (Prisma):** `Asset`, `AssetLink`

### 🖥️ Frontend Scope (4 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-frontend/src/modules/assets/Assets.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/assets/Assets.tsx) | `9.7 KB` |
| [rankray-hq-frontend/src/modules/assets/components/AssetList.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/assets/components/AssetList.tsx) | `6.1 KB` |
| [rankray-hq-frontend/src/modules/assets/components/AssetsSidebar.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/assets/components/AssetsSidebar.tsx) | `1.3 KB` |
| [rankray-hq-frontend/src/modules/assets/constants.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/assets/constants.ts) | `1.0 KB` |

### ⚙️ Backend Scope (5 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-backend/src/assets/assets.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/assets/assets.controller.ts) | `2.9 KB` |
| [rankray-hq-backend/src/assets/assets.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/assets/assets.module.ts) | `366 B` |
| [rankray-hq-backend/src/assets/assets.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/assets/assets.service.ts) | `28.7 KB` |
| [rankray-hq-backend/src/assets/dto/assets.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/assets/dto/assets.dto.ts) | `2.9 KB` |
| [rankray-hq-backend/src/assets/dto/maintenance.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/assets/dto/maintenance.dto.ts) | `818 B` |

---

<a name="automation--lab"></a>
## Automation & Lab
**Description:** Recurring background pipelines, queues (BullMQ), WordPress connector stubs, and scheduler tasks.

**Relational Data Models (Prisma):** `SeoAutomationConfig`, `AutomationRun`, `AutomationAlert`

### 🖥️ Frontend Scope (19 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-frontend/src/modules/automation/Automation.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/Automation.tsx) | `2.7 KB` |
| [rankray-hq-frontend/src/modules/automation/components/AutomationSidebar.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/components/AutomationSidebar.tsx) | `9.8 KB` |
| [rankray-hq-frontend/src/modules/automation/components/AutomationWizard.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/components/AutomationWizard.tsx) | `13.6 KB` |
| [rankray-hq-frontend/src/modules/automation/core/constants.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/core/constants.ts) | `805 B` |
| [rankray-hq-frontend/src/modules/automation/features/bulk-pages/AutomationSettings.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/features/bulk-pages/AutomationSettings.tsx) | `18.8 KB` |
| [rankray-hq-frontend/src/modules/automation/features/bulk-pages/BulkGeneratorPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/features/bulk-pages/BulkGeneratorPage.tsx) | `14.6 KB` |
| [rankray-hq-frontend/src/modules/automation/features/content-automation/ContentAutomation.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/features/content-automation/ContentAutomation.tsx) | `10.4 KB` |
| [rankray-hq-frontend/src/modules/automation/features/content-automation/components/GapAnalysis.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/features/content-automation/components/GapAnalysis.tsx) | `6.3 KB` |
| [rankray-hq-frontend/src/modules/automation/features/content-automation/components/IdeaQueue.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/features/content-automation/components/IdeaQueue.tsx) | `8.8 KB` |
| [rankray-hq-frontend/src/modules/automation/features/content-automation/components/MappingMatrix.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/features/content-automation/components/MappingMatrix.tsx) | `5.7 KB` |
| [rankray-hq-frontend/src/modules/automation/features/content-automation/components/SourceBadge.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/features/content-automation/components/SourceBadge.tsx) | `475 B` |
| [rankray-hq-frontend/src/modules/automation/features/content-automation/components/WpConnectForm.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/features/content-automation/components/WpConnectForm.tsx) | `5.6 KB` |
| [rankray-hq-frontend/src/modules/automation/features/dashboard/AutomationDashboard.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/features/dashboard/AutomationDashboard.tsx) | `11.6 KB` |
| [rankray-hq-frontend/src/modules/automation/features/settings/AutomationSettings.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/features/settings/AutomationSettings.tsx) | `10.7 KB` |
| [rankray-hq-frontend/src/modules/automation/services/automationService.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/services/automationService.ts) | `7.1 KB` |
| [rankray-hq-frontend/src/modules/automation/services/pipeline.api.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/services/pipeline.api.ts) | `2.1 KB` |
| [rankray-hq-frontend/src/modules/automation/services/wordpress.api.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/services/wordpress.api.ts) | `1.3 KB` |
| [rankray-hq-frontend/src/modules/automation/store/connectionsStore.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/store/connectionsStore.ts) | `2.3 KB` |
| [rankray-hq-frontend/src/modules/automation/types/automation-types.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/automation/types/automation-types.ts) | `4.3 KB` |

### ⚙️ Backend Scope (9 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-backend/src/automation/automation.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/automation/automation.controller.ts) | `4.5 KB` |
| [rankray-hq-backend/src/automation/automation.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/automation/automation.module.ts) | `1.2 KB` |
| [rankray-hq-backend/src/automation/controllers/wordpress.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/automation/controllers/wordpress.controller.ts) | `843 B` |
| [rankray-hq-backend/src/automation/dto/automation.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/automation/dto/automation.dto.ts) | `4.2 KB` |
| [rankray-hq-backend/src/automation/processors/automation.processor.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/automation/processors/automation.processor.ts) | `26.4 KB` |
| [rankray-hq-backend/src/automation/services/automation.service.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/automation/services/automation.service.spec.ts) | `2.9 KB` |
| [rankray-hq-backend/src/automation/services/automation.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/automation/services/automation.service.ts) | `36.0 KB` |
| [rankray-hq-backend/src/automation/services/wordpress.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/automation/services/wordpress.service.ts) | `5.9 KB` |
| [rankray-hq-backend/src/automation/services/wp-introspector.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/automation/services/wp-introspector.service.ts) | `14.0 KB` |

---

<a name="ai-agents--orchestration"></a>
## AI Agents & Orchestration
**Description:** Execution workspace for autonomous agent profiles (Dark, Enigma, Chronos, Nemo) and agent run tracking logs.

**Relational Data Models (Prisma):** `Agent`, `AgentRun`, `AgentMessage`

### 🖥️ Frontend Scope (11 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-frontend/src/modules/agents/Agents.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/agents/Agents.tsx) | `6.5 KB` |
| [rankray-hq-frontend/src/modules/agents/components/AgentChat.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/agents/components/AgentChat.tsx) | `7.2 KB` |
| [rankray-hq-frontend/src/modules/agents/components/AgentExecutionPanel.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/agents/components/AgentExecutionPanel.tsx) | `7.2 KB` |
| [rankray-hq-frontend/src/modules/agents/components/AgentHealthScore.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/agents/components/AgentHealthScore.tsx) | `4.8 KB` |
| [rankray-hq-frontend/src/modules/agents/components/AgentRoleCard.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/agents/components/AgentRoleCard.tsx) | `3.5 KB` |
| [rankray-hq-frontend/src/modules/agents/components/AgentsSidebar.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/agents/components/AgentsSidebar.tsx) | `1.7 KB` |
| [rankray-hq-frontend/src/modules/agents/components/DiffView.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/agents/components/DiffView.tsx) | `5.1 KB` |
| [rankray-hq-frontend/src/modules/agents/components/PreviewPanel.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/agents/components/PreviewPanel.tsx) | `5.9 KB` |
| [rankray-hq-frontend/src/modules/agents/components/TemplateMarketplace.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/agents/components/TemplateMarketplace.tsx) | `12.2 KB` |
| [rankray-hq-frontend/src/modules/agents/pages/AgentsPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/agents/pages/AgentsPage.tsx) | `25.8 KB` |
| [rankray-hq-frontend/src/modules/agents/services/agentService.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/agents/services/agentService.ts) | `2.8 KB` |

### ⚙️ Backend Scope (15 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-backend/src/agents/agent-definition.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/agents/agent-definition.service.ts) | `7.5 KB` |
| [rankray-hq-backend/src/agents/agent-roles.config.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/agents/agent-roles.config.ts) | `11.4 KB` |
| [rankray-hq-backend/src/agents/agent-runner.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/agents/agent-runner.service.ts) | `14.1 KB` |
| [rankray-hq-backend/src/agents/agent-tool.registry.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/agents/agent-tool.registry.ts) | `2.0 KB` |
| [rankray-hq-backend/src/agents/agents.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/agents/agents.controller.ts) | `4.8 KB` |
| [rankray-hq-backend/src/agents/agents.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/agents/agents.module.ts) | `1.2 KB` |
| [rankray-hq-backend/src/agents/dto/agents.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/agents/dto/agents.dto.ts) | `1.1 KB` |
| [rankray-hq-backend/src/agents/tools/business-tools.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/agents/tools/business-tools.ts) | `4.3 KB` |
| [rankray-hq-backend/src/agents/tools/seo-tools.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/agents/tools/seo-tools.ts) | `4.9 KB` |
| [rankray-hq-backend/src/agents/tools/tool.types.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/agents/tools/tool.types.ts) | `977 B` |
| [rankray-hq-backend/src/openclaw/billing-webhook.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/openclaw/billing-webhook.controller.ts) | `2.5 KB` |
| [rankray-hq-backend/src/openclaw/openclaw-profile.manager.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/openclaw/openclaw-profile.manager.ts) | `6.1 KB` |
| [rankray-hq-backend/src/openclaw/openclaw-provisioning.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/openclaw/openclaw-provisioning.service.ts) | `5.1 KB` |
| [rankray-hq-backend/src/openclaw/openclaw.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/openclaw/openclaw.module.ts) | `939 B` |
| [rankray-hq-backend/src/openclaw/openclaw.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/openclaw/openclaw.service.ts) | `5.0 KB` |

---

<a name="superadmin--controls"></a>
## Superadmin & Controls
**Description:** Top-level administration panel for workspace settings, subscription plans, server logs, and permission overrides.

**Relational Data Models (Prisma):** `SystemLog`, `Entitlement`

### 🖥️ Frontend Scope (21 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-frontend/src/modules/admin/AdminPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/AdminPage.tsx) | `2.4 KB` |
| [rankray-hq-frontend/src/modules/admin/components/AdminPageState.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/components/AdminPageState.tsx) | `1.4 KB` |
| [rankray-hq-frontend/src/modules/admin/components/AdminShell.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/components/AdminShell.tsx) | `1.0 KB` |
| [rankray-hq-frontend/src/modules/admin/components/AdminTable.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/components/AdminTable.tsx) | `2.1 KB` |
| [rankray-hq-frontend/src/modules/admin/components/EditPlanDialog.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/components/EditPlanDialog.tsx) | `6.6 KB` |
| [rankray-hq-frontend/src/modules/admin/components/GrantSubscriptionDialog.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/components/GrantSubscriptionDialog.tsx) | `6.7 KB` |
| [rankray-hq-frontend/src/modules/admin/components/StatusPill.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/components/StatusPill.tsx) | `1.8 KB` |
| [rankray-hq-frontend/src/modules/admin/components/WorkspaceDetailDrawer.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/components/WorkspaceDetailDrawer.tsx) | `10.8 KB` |
| [rankray-hq-frontend/src/modules/admin/components/adminFormat.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/components/adminFormat.ts) | `1.5 KB` |
| [rankray-hq-frontend/src/modules/admin/components/adminNav.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/components/adminNav.ts) | `3.4 KB` |
| [rankray-hq-frontend/src/modules/admin/hooks/useAdmin.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/hooks/useAdmin.ts) | `2.1 KB` |
| [rankray-hq-frontend/src/modules/admin/pages/AgentsPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/pages/AgentsPage.tsx) | `20.5 KB` |
| [rankray-hq-frontend/src/modules/admin/pages/EventsPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/pages/EventsPage.tsx) | `2.2 KB` |
| [rankray-hq-frontend/src/modules/admin/pages/OverviewPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/pages/OverviewPage.tsx) | `4.4 KB` |
| [rankray-hq-frontend/src/modules/admin/pages/PlansPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/pages/PlansPage.tsx) | `8.9 KB` |
| [rankray-hq-frontend/src/modules/admin/pages/SeoProvidersPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/pages/SeoProvidersPage.tsx) | `8.7 KB` |
| [rankray-hq-frontend/src/modules/admin/pages/SubscriptionsPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/pages/SubscriptionsPage.tsx) | `5.0 KB` |
| [rankray-hq-frontend/src/modules/admin/pages/UsersPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/pages/UsersPage.tsx) | `2.8 KB` |
| [rankray-hq-frontend/src/modules/admin/pages/WorkspacesPage.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/pages/WorkspacesPage.tsx) | `3.8 KB` |
| [rankray-hq-frontend/src/modules/admin/services/adminService.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/services/adminService.ts) | `9.9 KB` |
| [rankray-hq-frontend/src/modules/admin/services/adminTypes.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/admin/services/adminTypes.ts) | `4.6 KB` |

### ⚙️ Backend Scope (4 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-backend/src/admin/admin.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/admin/admin.controller.ts) | `10.9 KB` |
| [rankray-hq-backend/src/admin/admin.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/admin/admin.module.ts) | `543 B` |
| [rankray-hq-backend/src/admin/admin.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/admin/admin.service.ts) | `25.3 KB` |
| [rankray-hq-backend/src/admin/dto/admin.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/admin/dto/admin.dto.ts) | `3.8 KB` |

---

<a name="settings"></a>
## Settings
**Description:** General settings, workspace preferences, API keys, notifications, and integration configs.

**Relational Data Models (Prisma):** `WorkspaceConfig`

### 🖥️ Frontend Scope (9 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-frontend/src/modules/settings/InviteUserDialog.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/settings/InviteUserDialog.tsx) | `3.2 KB` |
| [rankray-hq-frontend/src/modules/settings/Settings.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/settings/Settings.tsx) | `46.0 KB` |
| [rankray-hq-frontend/src/modules/settings/Settings.tsx.backup](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/settings/Settings.tsx.backup) | `49.4 KB` |
| [rankray-hq-frontend/src/modules/settings/SettingsModule.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/settings/SettingsModule.tsx) | `9.5 KB` |
| [rankray-hq-frontend/src/modules/settings/components/SettingsSidebar.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/settings/components/SettingsSidebar.tsx) | `3.0 KB` |
| [rankray-hq-frontend/src/modules/settings/sections/AISettings.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/settings/sections/AISettings.tsx) | `5.4 KB` |
| [rankray-hq-frontend/src/modules/settings/sections/AuditLogs.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/settings/sections/AuditLogs.tsx) | `7.4 KB` |
| [rankray-hq-frontend/src/modules/settings/sections/Diagnostics.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/settings/sections/Diagnostics.tsx) | `19.9 KB` |
| [rankray-hq-frontend/src/modules/settings/sections/TrashBin.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/settings/sections/TrashBin.tsx) | `5.0 KB` |

### ⚙️ Backend Scope (0 files)
*No dedicated backend module files in this scope.*

---

<a name="system-core--shared"></a>
## System Core & Shared
**Description:** App-wide routing definitions, shared layouts, API wrapper systems, common middleware, and SQLite database migration scripts.

### 🖥️ Frontend Shared Core (13 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-frontend/src/modules/analytics/AnalyticsModule.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/analytics/AnalyticsModule.tsx) | `8.6 KB` |
| [rankray-hq-frontend/src/modules/auth/Login.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/auth/Login.tsx) | `7.2 KB` |
| [rankray-hq-frontend/src/modules/inbox/InboxModule.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/inbox/InboxModule.tsx) | `11.9 KB` |
| [rankray-hq-frontend/src/modules/marketing/MarketingModule.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/marketing/MarketingModule.tsx) | `9.8 KB` |
| [rankray-hq-frontend/src/modules/marketing/sections/MarketingAudiences.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/marketing/sections/MarketingAudiences.tsx) | `5.7 KB` |
| [rankray-hq-frontend/src/modules/marketing/sections/MarketingCampaigns.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/marketing/sections/MarketingCampaigns.tsx) | `8.9 KB` |
| [rankray-hq-frontend/src/modules/marketing/sections/MarketingOverview.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/marketing/sections/MarketingOverview.tsx) | `2.5 KB` |
| [rankray-hq-frontend/src/modules/marketing/sections/MarketingPerformance.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/marketing/sections/MarketingPerformance.tsx) | `5.9 KB` |
| [rankray-hq-frontend/src/modules/marketing/sections/MarketingTemplates.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/marketing/sections/MarketingTemplates.tsx) | `4.9 KB` |
| [rankray-hq-frontend/src/modules/marketing/sections/index.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/marketing/sections/index.ts) | `191 B` |
| [rankray-hq-frontend/src/modules/projects/Projects.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/projects/Projects.tsx) | `43.6 KB` |
| [rankray-hq-frontend/src/modules/projects/components/ProjectStatsStrip.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/projects/components/ProjectStatsStrip.tsx) | `1.8 KB` |
| [rankray-hq-frontend/src/modules/projects/components/ProjectTable.tsx](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend/src/modules/projects/components/ProjectTable.tsx) | `7.7 KB` |

### ⚙️ Backend Shared Core (59 files)
| File Path | Size |
| :--- | :--- |
| [rankray-hq-backend/src/app.controller.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/app.controller.spec.ts) | `617 B` |
| [rankray-hq-backend/src/app.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/app.controller.ts) | `378 B` |
| [rankray-hq-backend/src/app.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/app.module.ts) | `4.1 KB` |
| [rankray-hq-backend/src/app.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/app.service.ts) | `142 B` |
| [rankray-hq-backend/src/audit/audit.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/audit/audit.controller.ts) | `1.6 KB` |
| [rankray-hq-backend/src/audit/audit.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/audit/audit.module.ts) | `385 B` |
| [rankray-hq-backend/src/audit/audit.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/audit/audit.service.ts) | `1.3 KB` |
| [rankray-hq-backend/src/auth/auth.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/auth/auth.controller.ts) | `1.4 KB` |
| [rankray-hq-backend/src/auth/auth.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/auth/auth.module.ts) | `892 B` |
| [rankray-hq-backend/src/auth/auth.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/auth/auth.service.ts) | `5.9 KB` |
| [rankray-hq-backend/src/auth/dto/auth.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/auth/dto/auth.dto.ts) | `685 B` |
| [rankray-hq-backend/src/auth/jwt-auth.guard.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/auth/jwt-auth.guard.ts) | `160 B` |
| [rankray-hq-backend/src/auth/jwt.strategy.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/auth/jwt.strategy.ts) | `853 B` |
| [rankray-hq-backend/src/auth/roles.decorator.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/auth/roles.decorator.ts) | `157 B` |
| [rankray-hq-backend/src/auth/roles.guard.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/auth/roles.guard.ts) | `792 B` |
| [rankray-hq-backend/src/common/context/request-context.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/common/context/request-context.ts) | `287 B` |
| [rankray-hq-backend/src/common/decorators/feature.decorator.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/common/decorators/feature.decorator.ts) | `237 B` |
| [rankray-hq-backend/src/common/features.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/common/features.ts) | `4.7 KB` |
| [rankray-hq-backend/src/common/filters/http-exception.filter.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/common/filters/http-exception.filter.ts) | `3.7 KB` |
| [rankray-hq-backend/src/common/guards/feature-tier.guard.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/common/guards/feature-tier.guard.ts) | `2.2 KB` |
| [rankray-hq-backend/src/common/interceptors/context.interceptor.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/common/interceptors/context.interceptor.ts) | `1.0 KB` |
| [rankray-hq-backend/src/common/interceptors/idempotency.interceptor.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/common/interceptors/idempotency.interceptor.ts) | `4.9 KB` |
| [rankray-hq-backend/src/common/interceptors/request-logger.interceptor.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/common/interceptors/request-logger.interceptor.ts) | `2.8 KB` |
| [rankray-hq-backend/src/common/middleware/correlation-id.middleware.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/common/middleware/correlation-id.middleware.ts) | `503 B` |
| [rankray-hq-backend/src/common/middleware/request-logger.middleware.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/common/middleware/request-logger.middleware.ts) | `2.0 KB` |
| [rankray-hq-backend/src/common/pdf-branding/pdf-branding.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/common/pdf-branding/pdf-branding.module.ts) | `218 B` |
| [rankray-hq-backend/src/common/pdf-branding/pdf-branding.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/common/pdf-branding/pdf-branding.service.ts) | `4.8 KB` |
| [rankray-hq-backend/src/common/utils/crypto.util.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/common/utils/crypto.util.ts) | `2.3 KB` |
| [rankray-hq-backend/src/invitations/invitations.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/invitations/invitations.controller.ts) | `1.5 KB` |
| [rankray-hq-backend/src/invitations/invitations.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/invitations/invitations.module.ts) | `406 B` |
| [rankray-hq-backend/src/invitations/invitations.security.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/invitations/invitations.security.spec.ts) | `4.5 KB` |
| [rankray-hq-backend/src/invitations/invitations.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/invitations/invitations.service.ts) | `9.8 KB` |
| [rankray-hq-backend/src/mail/mail.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/mail/mail.module.ts) | `200 B` |
| [rankray-hq-backend/src/mail/mail.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/mail/mail.service.ts) | `1.1 KB` |
| [rankray-hq-backend/src/main.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/main.ts) | `4.6 KB` |
| [rankray-hq-backend/src/premium/premium.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/premium/premium.controller.ts) | `792 B` |
| [rankray-hq-backend/src/premium/premium.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/premium/premium.module.ts) | `179 B` |
| [rankray-hq-backend/src/prisma/prisma.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/prisma/prisma.module.ts) | `347 B` |
| [rankray-hq-backend/src/prisma/prisma.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/prisma/prisma.service.ts) | `1.9 KB` |
| [rankray-hq-backend/src/projects/dto/projects.dto.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/projects/dto/projects.dto.ts) | `1.2 KB` |
| [rankray-hq-backend/src/projects/projects.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/projects/projects.controller.ts) | `2.0 KB` |
| [rankray-hq-backend/src/projects/projects.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/projects/projects.module.ts) | `448 B` |
| [rankray-hq-backend/src/projects/projects.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/projects/projects.service.ts) | `8.9 KB` |
| [rankray-hq-backend/src/system-logs/system-diagnostics.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/system-logs/system-diagnostics.controller.ts) | `744 B` |
| [rankray-hq-backend/src/system-logs/system-logs.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/system-logs/system-logs.controller.ts) | `1.2 KB` |
| [rankray-hq-backend/src/system-logs/system-logs.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/system-logs/system-logs.module.ts) | `526 B` |
| [rankray-hq-backend/src/system-logs/system-logs.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/system-logs/system-logs.service.ts) | `6.7 KB` |
| [rankray-hq-backend/src/trash/trash.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/trash/trash.controller.ts) | `1.1 KB` |
| [rankray-hq-backend/src/trash/trash.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/trash/trash.module.ts) | `275 B` |
| [rankray-hq-backend/src/trash/trash.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/trash/trash.service.ts) | `20.7 KB` |
| [rankray-hq-backend/src/users/users.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/users/users.controller.ts) | `2.8 KB` |
| [rankray-hq-backend/src/users/users.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/users/users.module.ts) | `358 B` |
| [rankray-hq-backend/src/users/users.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/users/users.service.ts) | `4.2 KB` |
| [rankray-hq-backend/src/workspace/workspace.controller.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/workspace/workspace.controller.spec.ts) | `944 B` |
| [rankray-hq-backend/src/workspace/workspace.controller.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/workspace/workspace.controller.ts) | `2.6 KB` |
| [rankray-hq-backend/src/workspace/workspace.module.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/workspace/workspace.module.ts) | `456 B` |
| [rankray-hq-backend/src/workspace/workspace.security.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/workspace/workspace.security.spec.ts) | `2.1 KB` |
| [rankray-hq-backend/src/workspace/workspace.service.spec.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/workspace/workspace.service.spec.ts) | `749 B` |
| [rankray-hq-backend/src/workspace/workspace.service.ts](file:///Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend/src/workspace/workspace.service.ts) | `582 B` |

---

## 🔄 Maintaining the Index
> [!TIP]
> This index and the corresponding code graph stay synchronized dynamically using `graphify` and custom validation utilities.
> To refresh both the AST knowledge graph and this module index after making file changes, run:
```bash
graphify update .
python3 scripts/generate_module_index.py
```
> This ensures all AI agents (Gemini, Claude Code, Cursor) remain perfectly aligned and fully insulated from file-map confusion.