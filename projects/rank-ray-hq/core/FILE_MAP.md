# RankRay HQ FILE_MAP

Purpose: send agents to the correct files fast.

---

## Repo roots

- **Agent bootstrap:** `AGENTS.md` (repo root — not inside `docs/`)
- Frontend: `rankray-hq-frontend/`
- Backend: `rankray-hq-backend/`
- **Doc index:** [README.md](../README.md)

---

## Global Shell

- Frontend entry: `rankray-hq-frontend/src/main.tsx`
- App routing/module shell: `rankray-hq-frontend/src/App.tsx`
- Sidebar nav contract: `rankray-hq-frontend/src/components/layout/Sidebar.tsx`
- Header shell: `rankray-hq-frontend/src/components/layout/Header.tsx`
- Global UI state: `rankray-hq-frontend/src/stores/uiStore.ts`
- API client wrapper: `rankray-hq-frontend/src/lib/api.ts`
- Permissions: `rankray-hq-frontend/src/lib/permissions.ts`

Stores:

- Auth: `rankray-hq-frontend/src/stores/authStore.ts`
- CRM: `rankray-hq-frontend/src/stores/crmStore.ts`
- Finance: `rankray-hq-frontend/src/stores/financeStore.ts`
- HRM: `rankray-hq-frontend/src/stores/hrmStore.ts`
- Tasks: `rankray-hq-frontend/src/stores/taskStore.ts`
- Projects: `rankray-hq-frontend/src/stores/projectStore.ts`
- SEO website context: `rankray-hq-frontend/src/modules/seo/store/seoWebsiteContextStore.ts`

- Backend entry: `rankray-hq-backend/src/main.ts`
- Module wiring: `rankray-hq-backend/src/app.module.ts`
- Request context guardrails: `rankray-hq-backend/src/common/interceptors/context.interceptor.ts`

---

## Frontend Module Ownership

- Dashboard: `rankray-hq-frontend/src/modules/dashboard/`
- Tasks: `rankray-hq-frontend/src/modules/tasks/`
- Leads/Clients (CRM): `rankray-hq-frontend/src/modules/crm/`
- SEO: `rankray-hq-frontend/src/modules/seo/`
- Publishing (videos area): `rankray-hq-frontend/src/modules/publishing/`
- Outreach: `rankray-hq-frontend/src/modules/outreach/`
- Analytics (staged/demo): `rankray-hq-frontend/src/modules/analytics/`
- Marketing (staged/demo): `rankray-hq-frontend/src/modules/marketing/`
- Inbox (staged/demo): `rankray-hq-frontend/src/modules/inbox/`
- Team (HRM): `rankray-hq-frontend/src/modules/hrm/`
- Finance: `rankray-hq-frontend/src/modules/finance/`
- Assets: `rankray-hq-frontend/src/modules/assets/`
- Settings: `rankray-hq-frontend/src/modules/settings/`

---

## Backend Module Ownership

- Dashboard APIs: `rankray-hq-backend/src/dashboard/`
- CRM APIs: `rankray-hq-backend/src/crm/`
- HRM APIs: `rankray-hq-backend/src/hrm/`
- Tasks APIs: `rankray-hq-backend/src/tasks/`
- Projects APIs: `rankray-hq-backend/src/projects/`
- Finance APIs: `rankray-hq-backend/src/finance/`
- SEO APIs/services: `rankray-hq-backend/src/seo/`
- Publishing APIs/services: `rankray-hq-backend/src/seo/publishing/`
- Outreach APIs/services: `rankray-hq-backend/src/outreach/`
- Assets APIs: `rankray-hq-backend/src/assets/`
- Workspace/admin: `rankray-hq-backend/src/users/`, `rankray-hq-backend/src/invitations/`, `rankray-hq-backend/src/workspace/`, `rankray-hq-backend/src/audit/`, `rankray-hq-backend/src/system-logs/`

---

## High-Risk Files (Edit Carefully)

- `rankray-hq-frontend/src/App.tsx`
- `rankray-hq-frontend/src/components/layout/Sidebar.tsx`
- `rankray-hq-frontend/src/lib/api.ts`
- `rankray-hq-backend/src/app.module.ts`
- `rankray-hq-backend/src/main.ts`
- `rankray-hq-backend/src/seo/seo.service.ts`
- `rankray-hq-backend/src/finance/finance.service.ts`

---

## Module Boundary Rules

- Do not hand-roll network calls in module files; use `src/lib/api.ts`.
- Keep module internals inside their own module folder.
- Route ownership stays in `App.tsx`; nav labels/defaults stay in `Sidebar.tsx`.
- Backend endpoints/services must be added to the owning NestJS module folder.
- If module ownership changes, update this file in the same PR.
