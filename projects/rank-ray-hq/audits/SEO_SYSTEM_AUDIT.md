# SEO System Audit

## SEO Configure Control Center

- **Date:** Sun Mar 01 21:58:00 PKT 2026
- **Status:** Implemented
- **Frontend:** `/seo/settings` now renders a unified Configure control center for Google Search Console, Google Analytics, and Sync Health.
- **Reused Endpoints:** `GET /api/seo/gsc/auth-url`, `POST /api/seo/gsc/oauth`, `GET /api/seo/gsc/properties`, `POST /api/seo/gsc/connect`, `POST /api/seo/sync`, `GET /api/seo/analytics/auth-url`, `POST /api/seo/analytics/oauth`, `GET /api/seo/analytics/properties`, `POST /api/seo/analytics/connect`
- **New Endpoints:** `GET /api/seo/config`, `POST /api/seo/gsc/test`, `POST /api/seo/ga/test`, `POST /api/seo/ga/sync-now`
- **Health Data:** Control center now surfaces provider connection state, selected property, last sync timestamps, backend reason codes, rows written, and the last 10 sync audit entries.

## SEO Site Crawl

- **Date:** Mon Mar 02 03:47:00 PKT 2026
- **Status:** Implemented
- **Frontend:** `/seo/site-crawl` renders a dedicated SEO Site Crawl section with `Issues`, `Orphans`, and `Summary` tabs, site input, run action, status badge, and client-side CSV export.
- **Endpoints:** `POST /api/seo/crawl/run`, `GET /api/seo/crawl/summary`, `GET /api/seo/crawl/issues`, `GET /api/seo/crawl/orphans`
- **Safety Policy:** Blocks `localhost`, `127.0.0.1`, `::1`, `169.254.0.0/16`, RFC1918/CGNAT/ULA ranges, `.local`, and private DNS resolutions; enforces per-host throttling, `12s` request timeout, `2 MB` HTML cap, and redirect-chain detection after more than 2 hops.
- **Normalization Policy:** Lowercases hostnames, removes default ports and fragments, trims trailing slashes except root, strips `utm_*` plus common click-id params, and sorts remaining query params to dedupe variant URLs.
- **Execution Semantics:** Overlap lock is per `workspaceId + normalized siteUrl`; run contract returns `{ ok, enqueued, reason?, message?, lockKey? }`; summary read model surfaces queued metadata immediately from enqueue audit logs when the worker has not created the latest crawl job row yet.

## SEO Cannibalization

- **Date:** Mon Mar 02 04:28:00 PKT 2026
- **Status:** Implemented
- **Frontend:** `/seo/cannibalization` renders `Clusters` and `Summary` tabs with site scope selection, run-analysis CTA, detail modal, empty-state CTAs, and client-side CSV export.
- **Endpoints:** `POST /api/seo/cannibalization/run`, `GET /api/seo/cannibalization/summary`, `GET /api/seo/cannibalization/clusters`
- **Analysis Policy:** Results are derived from existing `TrackedKeyword`, `KeywordSnapshot`, and optional `SeoPage` crawl data; intent clusters use normalized token overlap, severity blends cluster size + ranking overlap + variance + page overlap, and actions map to `MERGE_CONTENT`, `SPLIT_INTENT`, `CANONICALIZE`, `INTERNAL_LINKS`, `RETARGET_KEYWORD`, or `NO_ACTION`.
- **Execution Semantics:** Overlap lock is per `workspaceId + resolved siteUrl`; run contract returns `{ ok, enqueued, reason?, message?, lockKey? }`; empty-state reasons surface `NO_GSC_CONNECTION`, `NO_PROPERTY_SELECTED`, `NO_TRACKED_KEYWORDS`, or `NO_SNAPSHOT_DATA` without blanking the UI.

## SEO Content Planner

- **Date:** Mon Mar 02 22:40:00 PKT 2026
- **Status:** Implemented
- **Frontend:** `/seo/content` renders `Plan`, `Briefs`, `Generate`, and `History` tabs with cluster creation, brief drafting, provider/model selection, copy/export actions, and signal-aware empty states.
- **Endpoints:** `GET /api/seo/content/plan`, `POST /api/seo/content/clusters`, `POST /api/seo/content/briefs`, `GET /api/seo/content/briefs`, `POST /api/seo/content/generate`, `GET /api/seo/content/history`
- **Read Model Policy:** Reuses the existing `ContentPlan.strategy` JSON payload for workspace-scoped SEO clusters/briefs and the existing `AiGeneratedContent` table for generated output history; no duplicate content-engine schema was introduced.
- **Planning Policy:** Ranking is deterministic from existing `TrackedKeyword`, `KeywordSnapshot`, crawl issues/pages, opportunities, cannibalization clusters, and backlink gap/toxic-link signals; clusters sort by `priorityScore desc` then `primaryKeyword asc`.
- **Generation Policy:** AI is only called on explicit `POST /api/seo/content/generate`; provider/model overrides reuse the existing AI provider configuration and persist provider/model metadata into generation history.

## SEO Publishing Engine

- **Date:** Tue Mar 03 00:40:00 PKT 2026
- **Status:** Implemented
- **Frontend:** `/seo/publishing` renders `Connect`, `Publish`, and `History` tabs with WordPress credential setup, connection testing, generated/manual content publishing, and publish-status tracking.
- **Endpoints:** `POST /api/seo/wp/connect`, `GET /api/seo/wp/status`, `POST /api/seo/wp/test`, `POST /api/seo/wp/publish`, `GET /api/seo/wp/history`
- **Data Model:** Extends `WordPressConnection` with `wpAdminUrl`, `authType`, `lastTestAt`, and `lastError`; adds `SeoPublishHistory` for workspace-scoped title/post/url/status/error/timestamp persistence.
- **Security Policy:** Application passwords are encrypted with the existing `CryptoUtil`; credentials are never returned; SSRF checks block localhost/private hosts before any WordPress request.
- **Execution Policy:** `SEO_WP_MODE=mock` yields deterministic test/publish responses for non-production environments only; live mode uses WordPress REST `/wp-json/wp/v2/users/me` and `/posts` with explicit error mapping into publish history.

## SEO Automation Center

- **Date:** Tue Mar 03 10:55:00 PKT 2026
- **Status:** Implemented
- **Frontend:** `/seo/automation-center` renders the `Automation` SEO section with workspace job filters, queue summary cards, quick-run actions, job detail logs, and retry/cancel controls.
- **Endpoints:** `GET /api/seo/automation/jobs`, `GET /api/seo/automation/jobs/:id`, `POST /api/seo/automation/jobs/:id/retry`, `POST /api/seo/automation/jobs/:id/cancel`
- **Queue Coverage:** Reads from `seo-audit-queue`, `keyword-strategy-queue`, `seo-automation-queue`, and `content-generation-queue`; live queue state is merged with persisted SEO audit metadata so completed jobs remain visible after BullMQ removes them.
- **Safety Policy:** All responses are scoped by `workspaceId`; retry only applies to failed jobs in the same queue/workspace; cancel removes queued jobs or records `CANCEL_REQUESTED` for running jobs without bypassing existing overlap locks or crawl/publishing safeguards.

## SEO Authority Builder

- **Date:** Tue Mar 03 12:05:00 PKT 2026
- **Status:** Implemented
- **Frontend:** `/seo/authority-builder` renders a deterministic authority-planning surface with plan generation, target-page ranking, anchor strategy, outreach targets, content-needs review, outreach-draft queue hooks, and client-side CSV export.
- **Endpoints:** `POST /api/seo/authority/run`, `GET /api/seo/authority/plan`, `GET /api/seo/authority/history`, `POST /api/seo/authority/plan/:id/outreach-draft`
- **Data Model:** Adds `SeoAuthorityPlan` with workspace-scoped `inputsJson` and `planJson` storage so plans and history survive reloads without exploding the schema.
- **Planning Policy:** The plan is deterministic for the same workspace inputs; ranking prioritizes opportunity-backed target pages, backlink gap domains, content-plan readiness, toxic-link risk, and crawl severity, then breaks ties alphabetically.
- **Safety Policy:** `workspaceId` scopes all reads/writes, `/authority/run` uses an idempotency lock to prevent overlap, and outreach drafts enqueue into the existing SEO automation queue without bypassing existing lock/tier rules.

## SEO Performance Command Center

- **Date:** Wed Mar 04 02:05:00 PKT 2026
- **Status:** Implemented
- **Frontend:** `/seo/performance` renders the Performance Command Center with overall score, 30-day trend, risk badge, module contribution cards, and blocker diagnostics.
- **Endpoints:** `GET /api/seo/performance/summary`, `GET /api/seo/performance/trends`
- **Aggregation Policy:** Deterministic scoring only; it reuses existing `TrackedKeyword` + `KeywordSnapshot`, backlink intelligence authority/toxicity signals, crawl jobs/issues/pages, cannibalization summaries, recent `SeoAuthorityPlan` records, publishing cadence from `SeoPublishHistory`, and analytics conversions when available.
- **Scoring Policy:** Health and momentum are pure weighted math with no AI or randomness; ties and daily trend windows use UTC-normalized dates so the 30-day series is stable across environments.

## PROJECT BEST PRACTICES (AUTO-GENERATED REFERENCE)

# 📘 Project Best Practices

## 1. Project Purpose
RankRay HQ is a full-stack TypeScript application providing an agency “HQ” suite: CRM, Finance, HRM, Outreach, SEO (Search Console + Analytics integrations, authority builder, site crawl, automation), and more.  
- Backend: NestJS 11 with Prisma (SQLite by default), JWT auth, role- and feature-tier guards, Google OAuth integrations (GSC/GA), background sync and audit logging.  
- Frontend: React 19 with Vite and Tailwind, feature-flagged modules, and Playwright E2E tests.

## 2. Project Structure
- Root
  - rankray-hq-backend/ (NestJS API)
    - src/
      - auth/, common/, audit/, prisma/ (PrismaService), seo/, crm/, finance/, hrm/, outreach/, projects/, users/, workspace/, system-logs/, tasks/, etc.
      - main.ts (Nest bootstrap), app.module.ts (composition), app.controller/service
      - seo/: controllers (seo.controller.ts, analytics.controller.ts), services/ (gsc.service.ts, sync.service.ts, analytics-sync.service.ts, etc.), dto/ (validation DTOs), ai/, content/, publishing/, utils/
    - prisma/
      - schema.prisma (SQLite provider), seed and demo scripts
    - scripts/ (ad-hoc operational scripts; not required at runtime)
    - nest-cli.json, tsconfig*.json, package.json, .env (local), logs/
  - rankray-hq-frontend/ (React + Vite)
    - src/modules/ (feature modules like seo/, projects/, tasks/, crm/…), components/, stores/
    - SEO module at src/modules/seo/SEO.tsx with tabs and feature gates
  - verify_*.sh scripts for quick environment/regression checks
  - test-api.js, test-results/
- Entry points and configuration
  - Backend: Nest factory in src/main.ts; environment via @nestjs/config, .env variables; Prisma via PrismaService.
  - Frontend: Vite dev server; API base set via VITE_API_URL for dev.
- Separation of concerns
  - Backend uses NestJS DI: Controllers (routing), Services (business logic), PrismaService (data access), DTOs (validation).
  - Frontend uses modular feature directories and state stores; SEO.tsx orchestrates tabs/routing and gated features.

## 3. Test Strategy
- Backend
  - Jest configured in rankray-hq-backend/package.json (transform with ts-jest, moduleNameMapper to strip .js extension at runtime).
  - Tests co-located under src with *.spec.ts suffix; use Nest testing utilities + mocks.
  - Coverage directory at backend/coverage; run with npm run test / test:watch / test:cov.
- Frontend
  - Playwright E2E tests (scripts test:e2e, test:e2e:headed, test:e2e:isolated).
  - No explicit unit-test setup shown; E2E focus on key user flows.
- Mocking guidelines
  - Prefer mocking external integrations (Google APIs) at service boundary.
  - Use PrismaService mocks to stub DB reads/writes in unit tests.
- Unit vs. Integration
  - Unit: individual services, DTO validation, guards/interceptors behavior.
  - Integration: controller-service-prisma flows with a test DB (e2e.db via scripts), OAuth exchanges using mocks, sync orchestration flows (mock mode).
  - E2E: critical frontend-to-backend flows (auth, SEO tabs, keyword management, GSC/GA setup flows) using Playwright with a running backend.

## 4. Code Style
- TypeScript everywhere; leverage strong typing and explicit return types for public functions.
- NestJS patterns
  - Use DTOs (class-validator/class-transformer) for all request bodies/queries.
  - Throw Nest exceptions (BadRequestException, UnauthorizedException, ConflictException, etc.) for predictable error semantics.
  - Inject dependencies via constructor (PrismaService, ConfigService, AuditLogService, etc.).
- Import style
  - For Jest + ESM module interop, maintain “.js” in relative imports within TS (supported by moduleNameMapper in Jest config).
- Error handling
  - Wrap external API calls (Google APIs) with try/catch; log via Logger/AuditLogService; return typed error responses.
  - Avoid leaking sensitive values in logs (mask client IDs/secrets).
- Comments/Docs
  - Keep service methods self-descriptive; short comments for non-obvious logic (e.g., OAuth refresh rules).
- Async behavior
  - Always await Prisma/Google API calls; avoid unhandled promises.
  - Use concurrency limits for batch work (e.g., sync service WORKSPACE_CONCURRENCY).

## 5. Common Patterns
- Dependency Injection: All services injected by Nest (constructor DI), enhancing testability and modular design.
- Data Access: Direct PrismaService usage (no custom repository abstraction), consistent workspace scoping in queries.
- Security & Feature Gating:
  - JwtAuthGuard and RolesGuard across controllers.
  - FeatureTierGuard with @RequireFeature('feature.key') for subscription-tier-based access.
- Idempotency: IdempotencyInterceptor used on endpoints where duplicate requests are possible.
- Auditing/Observability:
  - AuditLogService for structured actions (GSC_CONNECTED, SYNC_COMPLETED, etc.).
  - SystemLog model for generic observability across FE/BE.
- OAuth2 Integrations:
  - Google OAuth (GSC/GA) with offline access + prompt=consent.
  - Refresh-token encryption at rest (CryptoUtil), periodic access token refresh with DB persistence.
- Sync Orchestration:
  - SEOSyncService processes workspaces in batches with controlled concurrency, robust error semantics (NO_GSC_CONNECTION, NO_PROPERTY_SELECTED, TOKEN_REFRESH_FAILED).
  - Mock mode toggled via env (SEO_GSC_MODE=mock) to simulate syncs during development.

## 6. Do’s and Don’ts
- ✅ Do
  - Define required env vars in backend .env:
    - SERVER_PUBLIC_URL, GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, DATABASE_URL
    - Optional: SEO_GSC_MODE (mock|real), SEO_MOCK_WINDOW_DAYS
  - Keep workspaceId filtering in all Prisma reads/writes to enforce multi-tenant isolation.
  - Apply guards consistently: JwtAuthGuard + RolesGuard; add @Roles for mutating/admin endpoints; use @RequireFeature for tier-gated features.
  - Validate inputs with DTOs and class-validator decorators.
  - Record audit logs for security-sensitive or business-critical operations.
  - Mask sensitive identifiers (client IDs, tokens) in logs.
  - In SEO.tsx, keep the SEO_TAB_PATHS and seoTabFromPath in sync with router paths; ensure pushState and popstate are handled.
  - Set VITE_API_URL (e.g., http://127.0.0.1:3000/api) during frontend dev to avoid 404/CORS issues.
- ❌ Don’t
  - Don’t log unmasked tokens, secrets, or PII.
  - Don’t bypass guards or features without explicit design.
  - Don’t write Prisma queries without scoping to workspaceId when applicable.
  - Don’t introduce long-running operations in controllers; move to services/queues where necessary.
  - Don’t compile operational scripts into production builds unless they are maintained and aligned with current DI signatures.

## 7. Tools & Dependencies
- Backend
  - NestJS (@nestjs/*): controllers, DI, guards, scheduling
  - Prisma (@prisma/client, prisma): DB access + migrations/seeding
  - googleapis: OAuth + Search Console/Analytics APIs
  - bullmq: queues (future/optional usage)
  - class-validator/transformer: request validation
  - passport, passport-jwt: auth
  - helmet, csurf, cookie-parser: security middleware
- Frontend
  - React 19 + Vite 7, TailwindCSS + Radix UI
  - @tanstack/react-query for data fetching
  - Playwright for E2E tests
- Setup (dev)
  - Backend: npm install, prisma migrate/seed, npm run start:dev
  - Frontend: npm install, set VITE_API_URL, npm run dev
  - Verify scripts: verify_reachability.sh (requires backend running; add Authorization header if testing auth-protected endpoints), verify_seo_module.sh, verify_seo_position_tracking.sh

## 8. Other Notes
- Build pipeline caveat:
  - Current backend “scripts/” TypeScript utilities may fail to compile after service constructor changes. Two safe options:
    1) Exclude scripts from tsconfig.build.json (recommended for production builds), or
    2) Update scripts to bootstrap Nest testing modules and provide all required DI dependencies.
- SEO.tsx (frontend)
  - Manages SEO feature tabs with window.history pushState + popstate. Keep SEO_TAB_PATHS and seoTabFromPath in sync with router-level URLs to prevent broken deep links.
  - OAuth handling: looks for code/state in query params; posts to /seo/gsc/oauth or /seo/analytics/oauth accordingly, then cleans URL.
- API prefix:
  - Ensure client calls use /api prefix if server is configured with global API prefix (commonly set in main.ts). Confirm VITE_API_URL includes /api in dev.
- Auth in local verification:
  - Many endpoints are guard-protected; 401/403 is expected without a token/role/tier. Modify verification scripts or use a tool that injects a bearer token if you want to test functional success responses.
- Sensitive configuration:
  - SERVER_PUBLIC_URL must match the host used by the OAuth redirect (e.g., http://127.0.0.1:3000). Authorized redirect URI in Google Cloud must be exactly /api/seo/gsc/oauth and /api/seo/analytics/oauth for respective flows.