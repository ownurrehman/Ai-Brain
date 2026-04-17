# Release gate — practical criteria

Derived from [ROADMAP.md](../core/ROADMAP.md) **Immediate next steps** and **Known issues**. Use this as a **pre-release or pre-demo** checklist. Check boxes when verified in a clean environment (seeded DB, env vars set per [../../AGENTS.md](../../AGENTS.md)).

## 1. Core business flow (CRM → Finance)

- [ ] Create **company** (Clients).
- [ ] Create **contact** linked to company.
- [ ] Create or progress **deal** (Leads); confirm behavior when company is text-autocomplete vs persisted company id.
- [ ] Create **quote**, convert to **invoice** (if applicable to your demo path).
- [ ] Record **payment**; ledger balances look correct.

## 2. SEO flow (website-first)

- [ ] Add or select **SEO website** under a company.
- [ ] Complete **Integrations** (GSC/GA or mock mode per env) where demo requires it.
- [ ] Run **site audit** (technical); issues list loads without red crash states.
- [ ] Add or view **keywords** / rankings per connected provider.

## 3. Dashboard

- [ ] Each **widget** fails independently with empty data (no full-page error); honest empty states.

## 4. Team (HRM)

- [ ] **Error state is per tab** — failure in one tab does not blanket-error others (target state per ROADMAP).

## 5. Automation

- [ ] With **Redis**: queue jobs process as expected for critical paths.
- [ ] Without **Redis**: acceptable degradation documented (e.g. sync fallback for audit/crawl only); no silent data corruption.

## 6. Engineering hygiene (informational)

- Backend: `npm test --prefix rankray-hq-backend` — track regressions against baseline.
- Frontend E2E: `npm run test:e2e` from repo root when DB seed and ports are available.
- Lint: large pre-existing debt; gate on **new** critical issues only unless doing a dedicated cleanup sprint.

### Automated test snapshot (2026-04-04)

Run from repo root: `npm test --prefix rankray-hq-backend`.

| Result | Value |
|--------|--------|
| Suites | 16 passed, **4 failed**, 20 total |
| Tests | **91 passed**, 8 failed, 99 total |

**Failing suites (fix before treating tests as release hard gate):**

- `dashboard.controller.spec.ts` / `dashboard.service.spec.ts` — `DashboardService` missing `AssetsService` mock in test module.
- `sync.service.spec.ts` — expectations for `mock` mode / production guard out of sync with current sync behavior (`real` / `NO_REAL_PROVIDER`).
- `seo.sync-status.spec.ts` — `lastSync.mode` expectation vs implementation.

Re-run after fixes and update this table.

## Sign-off

| Role | Name | Date | Notes |
|------|------|------|--------|
| Product | | | |
| Engineering | | | |
