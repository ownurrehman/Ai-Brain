# RankRay HQ Rules (Non-Negotiables)

## How Agents Must Use Docs

Use docs in this order for any meaningful task (all under `docs/core/`):

1. `RULES.md`
2. `ROADMAP.md` (vision + product model + module status + next steps)
3. `ARCHITECTURE.md`
4. `FILE_MAP.md`

Then open `docs/modules/`, `docs/seo/`, or `docs/product/` only when the task needs them — see [README.md](../README.md).

Meaning:

- `ROADMAP.md` = product vision and **current state** truth
- `ARCHITECTURE.md` = where/how the system is built
- `FILE_MAP.md` = where to edit fast
- `RULES.md` = guardrails that cannot be bypassed

---

## Product Integrity Rules

- No fake data.
- No fake success states.
- No fake KPI surfaces.
- No misleading “working” UI when provider/data is missing.
- Unfinished modules must be labeled honestly.
- Do not claim completion when a flow is partial or broken.

---

## Entity Ownership Rules

- Company is the owner business entity.
- Contacts are client-side people.
- Team/employees are internal-only people.
- Websites belong to company.
- SEO is website-first.
- Finance is company-owned.
- Assets are linked records, company-owned, optionally website-linked.

---

## Delivery Rules for Agents

- Keep scope tight to the asked task.
- Prefer minimal diffs over broad rewrites.
- Stabilize broken flows before redesigning them.
- Do not break working flows while redesigning adjacent areas.
- If a module is in transition, preserve backward compatibility until replacement is complete.
- Do not remove buttons, flows, or actions unless replacing them with a working equivalent.

---

## Navigation and IA Rules

- Do not change top-level navigation casually.
- Current navigation pattern:
  - Large modules (SEO, Finance, Publishing) → 2nd sidebar inside module.
  - Small modules (Leads, Clients, Team, Projects, Assets) → expandable dropdown in main sidebar.
  - No horizontal tab menus anywhere in the app.
- Leads and Clients are separate sidebar entries:
  - Leads = Pipeline, Activity, Reports.
  - Clients = Companies, Contacts.
  - Do not mix them or duplicate entries.
- Settings is always at the bottom of the sidebar, above the user profile card.
- SEO provider connections are labeled "Integrations" (not "Settings") to avoid confusion with global Settings.
- Top-level Publishing stays video-focused; blog/image workflows remain SEO-owned.
- If navigation changes, update:
  - `ROADMAP.md`
  - `ARCHITECTURE.md`
  - `FILE_MAP.md`
- Do not create duplicate entry points for the same workflow without explicit need.

---

## UI Pattern Rules

- Create/Add buttons must always be visible. Do not hide behind frontend permission checks. Backend enforces access.
- Error states must be helpful, not alarming. Show what to do next (connect provider, run audit, add data) instead of red crash cards.
- API failures in one widget must not crash the entire page. Each section fetches independently.
- Use `.catch(() => null)` for non-critical parallel fetches so one failure doesn't block everything.
- Toast notifications for expected empty states are banned. Only toast for user-initiated action results.

---

## Engineering Safety Rules

- Preserve workspace isolation and RBAC constraints.
- Do not bypass tier/internal access gates.
- Queue/automation jobs must be idempotent and bounded.
- Avoid unbounded parallelism and hidden background behavior.
- Do not leave stray files, temp files, or editor junk in the repo.

---

## SEO-Specific Rules

- SEO analysis requires website context.
- SEO screens must use honest screen states:
  - ready
  - needs_setup
  - sync_required
  - insufficient_data
- Setup/config belongs in SEO Settings, not analysis walls.
- Every major SEO insight should lead to an action.

---

## Finance-Specific Rules

- Service is the correct catalog term.
- Do not regress Quote / Invoice / Payment flows while doing UI work.
- Preserve company-linked history.
- Archive or status-change rather than destructive deletion where operational truth matters.

---

## Assets-Specific Rules

- Assets use archive-based removal.
- Expiry/renewal behavior must be date-driven, not fake.
- If only some asset types are implemented, say so honestly.

---

## Documentation Discipline

- Update docs after meaningful architecture or product-state changes.
- Keep docs compact, structured, and non-duplicative.
- Do not create throwaway progress docs.
- Keep module docs under `docs/modules/` only.
- Keep audit logs and test evidence under `docs/audits/` only.
- Keep docs under `docs/` only (no duplicate doc trees in `rankray-hq-frontend/` or `rankray-hq-backend/docs/`). Repo root: `AGENTS.md` only for agent bootstrap.
- Layout: `docs/core/` (rules, roadmap, architecture, file map), `docs/modules/`, `docs/seo/`, `docs/product/`, `docs/reference/`, `docs/operations/`, `docs/audits/`, `docs/archive/`.
- `docs/reference/keys.md` must stay sanitized; never store live secrets in docs.
- If module status changes, update `docs/core/ROADMAP.md`.
- If file ownership/module structure changes, update `docs/core/FILE_MAP.md`.
- If real bug root causes are fixed, update `docs/operations/BUG_LEDGER.md`.

---

## Verification Baseline

- For backend changes: run backend build.
- For frontend changes: run frontend build/type-check.
- For risky behavior changes: run targeted tests for the affected flow.
- Report exact files changed and real verification results.
- Do not say “fixed” unless the relevant flow was actually verified.

---

## Forbidden Behaviors

- Broad redesign without stabilization
- Re-auditing the whole repo for a small task
- Touching unrelated modules
- Introducing fake placeholder logic to look complete
- Leaving docs out of sync with implemented reality
