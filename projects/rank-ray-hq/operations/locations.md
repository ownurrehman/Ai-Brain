# 🗺️ RankRay HQ — Master Locations Map

This document serves as the authoritative guide for finding critical files and data sources within the RankRay HQ platform.

---

## 🔑 Core Configuration & Security

| Item | Location | Purpose |
| :--- | :--- | :--- |
| **Global Env** | `rankray-hq-backend/.env` | Database URLs, JWT secrets, Master passwords, API keys. |
| **Frontend Env** | `rankray-hq-frontend/.env` | API base URL and feature toggles. |
| **Prisma Schema** | `rankray-hq-backend/prisma/schema.prisma` | The database blueprint (Tables, Relationships, Enums). |
| **Migration History**| `rankray-hq-backend/prisma/migrations/` | Record of all database changes over time. |

---

## 💾 Data Persistence (The "Master DB")

| Item | Location | Notes |
| :--- | :--- | :--- |
| **Active Database** | `rankray-hq-backend/prisma/dev.db` | **The single source of truth.** Contains all manual data (Cients, Leads, HRM, Finance). |
| **Seed Script** | `rankray-hq-backend/prisma/seed.ts` | Script used to initialize the platform with admin users and demo data. |
| **Crawl Storage** | `rankray-hq-backend/storage/` | Local storage for SEO audit reports, request queues, and video renders. |

---

## 🏗️ Architecture & Entry Points

| Item | Location | Purpose |
| :--- | :--- | :--- |
| **Backend Entry** | `rankray-hq-backend/src/main.ts` | Entry point for the NestJS server. |
| **Frontend Entry** | `rankray-hq-frontend/src/main.tsx` | Entry point for the React/Vite application. |
| **App Routing** | `rankray-hq-frontend/src/App.tsx` | Global layout and route protection (Login vs Dashboard). |
| **Sidebar Navigation**| `rankray-hq-frontend/src/components/layout/Sidebar.tsx` | Controls the left navigation menu and module visibility. |
| **CRM Sidebar**      | `rankray-hq-frontend/src/modules/crm/components/CrmSidebar.tsx` | Lean navigation for Leads and Clients. |
| **HRM Sidebar**      | `rankray-hq-frontend/src/modules/hrm/components/HrmSidebar.tsx` | Lean navigation for Team and Payroll. |
| **Assets Sidebar**   | `rankray-hq-frontend/src/modules/assets/components/AssetsSidebar.tsx` | Lean navigation for Domains and Websites. |

---

## 📦 Module Directory (Where the work happens)

| Module | Backend Path (Logic) | Frontend Path (UI) |
| :--- | :--- | :--- |
| **CRM (Leads)** | `rankray-hq-backend/src/crm/` | `rankray-hq-frontend/src/modules/crm/` |
| **SEO Engine** | `rankray-hq-backend/src/seo/` | `rankray-hq-frontend/src/modules/seo/` |
| **Automation** | `rankray-hq-backend/src/automation/` | `rankray-hq-frontend/src/modules/automation/` |
| **Invoices/Finance**| `rankray-hq-backend/src/finance/` | `rankray-hq-frontend/src/modules/finance/` |
| **HRM (Team)**| `rankray-hq-backend/src/hrm/` | `rankray-hq-frontend/src/modules/hrm/` |
| **Assets** | `rankray-hq-backend/src/assets/` | `rankray-hq-frontend/src/modules/assets/` |
| **Dashboard** | `rankray-hq-backend/src/dashboard/` | `rankray-hq-frontend/src/modules/dashboard/` |

---

## 🛡️ Security & Stability Guards

- **.gitignore**: Configured to ignore `*.db` files to prevent Git from overwriting your local data.
- **Isolations**: Database is strictly local first; sensitive keys stay in the `.env` which is ignored by version control.
