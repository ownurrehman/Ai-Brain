# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

---

## Mandatory read order before any code change

1. `docs/core/RULES.md` — non-negotiables (no fake data, honest states, doc-update rule)
2. `docs/core/AGENT_CONTRACT.md` — execution contract (queue isolation, contract safety, verification gates)
3. `docs/core/ROADMAP.md` — current product phase, module status board
4. `docs/core/ARCHITECTURE.md` — entities, navigation contract, module ownership
5. `docs/core/FILE_MAP.md` — where to edit fast + high-risk files
6. Then the matching `docs/modules/<module>/` for the touched domain

Legacy `.ai/*` paths and `SEO Engine Ai - wordpress old plugin/` are **reference only**, not source-of-truth.

---

## Repo shape

Monorepo with two apps wired by root `package.json` scripts:

- `rankray-hq-backend/` — NestJS 11 + Prisma 6 (SQLite at `prisma/dev.db`) + BullMQ (Redis optional)
- `rankray-hq-frontend/` — React 19 + Vite 7 + Tailwind 3 + Radix/shadcn + Zustand + TanStack Query

The product is a multi-module agency OS: Dashboard, Tasks, Leads, **SEO** (12 screens, website-scoped), Publishing (videos), Clients, Team (HRM), Finance, Projects, Assets, Settings, Automation. SEO and Automation are the two heavy modules.

---

## Common commands (run from repo root)

| Action | Command |
| :--- | :--- |
| Install everything | `npm run install:all` |
| Dev (both, with port cleanup) | `npm run dev:all:clean` |
| Dev (both, stable, kill on exit) | `npm run dev:all:stable` |
| Backend only | `npm run dev:backend` |
| Frontend only | `npm run dev:frontend` |
| Free ports 3000 / 5173 | `npm run e2e:clean-ports` |
| E2E (seeds e2e.db, runs Playwright) | `npm run test:e2e` |

Backend (run from `rankray-hq-backend/`):

| Action | Command |
| :--- | :--- |
| Build | `npm run build` (runs `prisma generate` then `nest build`) |
| Type-check (workspace) | `npx tsc -b` |
| Unit tests | `npm test` |
| Single test file | `npx jest path/to/file.spec.ts` |
| Single test by name | `npx jest -t "name"` |
| Lint (auto-fix) | `npm run lint` |
| Prisma generate | `npx prisma generate` |
| Apply migrations to dev.db | `npm run db:prepare` |
| Seed dev.db | `npm run db:seed` |
| Inspect data | `npx prisma studio` |
| Reset demo dataset | `npm run db:demo-reset` |

Frontend (run from `rankray-hq-frontend/`):

| Action | Command |
| :--- | :--- |
| Build (tsc + vite) | `npm run build` |
| Lint | `npm run lint` |
| Playwright E2E | `npm run test:e2e` |
| Headed Playwright | `npm run test:e2e:headed` |

Ports: backend `3000`, frontend `5173`. Frontend reads API at `VITE_API_URL` (default `http://127.0.0.1:3000/api`).

---

## Required environment

- `JWT_SECRET` — backend will not boot without it
- `DATABASE_URL` — root scripts inject `file:$PWD/rankray-hq-backend/prisma/dev.db`; only set manually when running prisma commands directly from the backend folder
- `NODE_OPTIONS=--max-old-space-size=4096` — already set in dev scripts; raise if `nest start --watch` OOMs
- `SEO_GSC_MODE=mock` is set in `dev:backend` so SEO works without Google OAuth credentials
- Redis is **optional**; BullMQ `ECONNREFUSED` warnings are non-fatal — sync paths fall back

Provider keys (Google GSC/GA, AI providers, etc.) live in backend env. See `docs/reference/keys.md` (sanitized).

---

## Architecture: things you must know before editing

### Backend module boundaries (NestJS)

- `src/automation/**` — automation orchestration (Content Lab runs, WordPress connect, bulk engine)
- `src/seo/**` — SEO intelligence (audits, keywords, performance, technical, content, etc.)
- `src/seo/publishing/**` — publishing records (top-level Publishing UI = videos; SEO blog/image flows live here)
- `src/finance/**`, `src/crm/**`, `src/hrm/**`, `src/projects/**`, `src/tasks/**`, `src/assets/**`
- `src/users/**`, `src/invitations/**`, `src/workspace/**`, `src/audit/**`, `src/system-logs/**` — admin/workspace
- `src/common/interceptors/context.interceptor.ts` — request context guardrails (workspace isolation, RBAC)
- `prisma/schema.prisma` — authoritative model definition

### **Queue isolation (critical — has caused production breakage)**

Two BullMQ queues, never crossed:

- `seo-automation-queue` → `AutomationModule` + `AutomationProcessor` → Content Lab + automation runs
- `seo-automation-intel-queue` → `SEOModule` + `SeoAutomationProcessor` → background SEO intelligence work

Renaming a queue requires updates in **all four places**: module registration, `@Processor(...)`, `@InjectQueue(...)`, and any monitoring UI showing queue names.

### Frontend conventions

- Routing/module shell: `src/App.tsx`
- Sidebar nav: `src/components/layout/Sidebar.tsx` (large modules → 2nd sidebar; small → dropdown; **no horizontal tab rows app-wide**)
- API wrapper: `src/lib/api.ts` — never hand-roll `fetch`/`axios` in modules
- Permissions: `src/lib/permissions.ts` — null role → all `can*` false; **do not hide primary Create buttons** behind frontend perms; backend enforces
- State: Zustand stores in `src/stores/` + per-module services in `src/modules/*/services/`
- SEO website context: `src/modules/seo/store/seoWebsiteContextStore.ts` — every SEO screen depends on a selected website

### High-risk files (small change → big blast radius)

- `rankray-hq-frontend/src/App.tsx`
- `rankray-hq-frontend/src/components/layout/Sidebar.tsx`
- `rankray-hq-frontend/src/lib/api.ts`
- `rankray-hq-backend/src/app.module.ts`
- `rankray-hq-backend/src/main.ts`
- `rankray-hq-backend/src/seo/seo.service.ts`
- `rankray-hq-backend/src/finance/finance.service.ts`
- `rankray-hq-backend/prisma/schema.prisma`

### Entity ownership (do not mix)

- **Company** is the business owner and roots Contacts, Websites, Projects, Finance, Assets
- **Contact** = client-side person; **Employee/User** = internal — never merged
- **Website (SeoWebsite)** belongs to Company; SEO is website-first
- Finance is company-owned; Assets are company-owned + optional website link

---

## Non-negotiables (the rules that have been broken before)

1. **No fake data, no fake KPIs, no fake success states.** Honest screen states only: `ready`, `needs_setup`, `sync_required`, `insufficient_data`. SEO is the strictest.
2. **Don't break working flows while refactoring adjacent ones.** Stabilize before redesigning. Preserve buttons/actions unless replaced with a working equivalent.
3. **Minimal diffs, scoped to the asked task.** No silent expansion into "while I'm here" cleanup. Log unrelated issues separately.
4. **Contract changes are additive when possible.** If a DTO changes, update DTO + frontend types + service wrapper + module doc in the same change.
5. **Migration safety on local SQLite.** Local DBs drift; reads for new optional fields must degrade gracefully, not crash all generation.
6. **Async flows must expose status:** queued / in-progress / final. No silent background work, no unbounded parallelism, jobs idempotent and bounded.
7. **One widget failing must not crash a page.** Each section fetches independently; use `.catch(() => null)` on non-critical parallel fetches; toasts only for user-initiated actions.
8. **Update docs in the same change** when behavior shifts:
   - module behavior → `docs/modules/<module>/`
   - structural ownership → `docs/core/FILE_MAP.md`
   - module status → `docs/core/ROADMAP.md`
   - real bug root cause + fix → `docs/operations/BUG_LEDGER.md`
9. **No new top-level navigation** without updating `ROADMAP.md`, `ARCHITECTURE.md`, `FILE_MAP.md` together.

---

## Verification gates (required before claiming "done")

- **Backend changes:** `npx tsc -b` (or `npm run build` in backend) + targeted API verification of changed endpoints
- **Frontend changes:** `npm run build` (or at minimum lint) in frontend + manually verify the changed UI flow
- **Async/automation changes:** trigger one real run end-to-end, observe runtime logs, verify final persisted state
- Report exactly what was verified. "Fixed" without verification evidence is forbidden.

---

## Doc map (quick)

- `docs/core/` — rules, contract, roadmap, architecture, file map
- `docs/modules/<module>/` — per-domain contracts (CRM, Finance, HRM, SEO, Settings, automation)
- `docs/modules/seo/` — SEO module: `README.md` (status), `blueprint.md` (12 screens + APIs + capability tiers), `user-journey.md` (UX flows)
- `docs/product/` — features matrix, release gate, GTM
- `docs/reference/` — env keys, API surface notes
- `docs/operations/` — `BUG_LEDGER.md`, QA checklist, `rebirth-tracker.md`
- `docs/audits/` — test/audit evidence

Repo root: `AGENTS.md` + this `CLAUDE.md` only — both bootstrap, not source-of-truth.
