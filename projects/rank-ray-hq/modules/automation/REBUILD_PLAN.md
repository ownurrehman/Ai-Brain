# Automation Module — Rebuild & Stabilization Plan

**Owner:** Rank Ray HQ
**Created:** 2026-04-24
**Status:** Draft — awaiting phased execution
**Related:** [blueprint.md](./blueprint.md), [SEO Engine Ai plugin reference](../../../../Rank%20Ray%20HQ/SEO%20Engine%20Ai%20-%20wordpress%20old%20plugin/)

---

## 1. Purpose

The `Automations` module is the orchestration layer of Rank Ray HQ. It connects external publishing targets (starting with WordPress), wires them to an LLM provider of the user's choice, and runs three engines on top:

1. **Content Lab** — single-post AI blog publisher with semantic-SEO rules, auto image injection, and draft/publish push.
2. **Bulk Engine** — programmatic page generator that maps CSV/spreadsheet rows into WordPress page fields (ACF / Gutenberg).
3. **Healing Engine** — *planned only*; documented here for forward-compatibility but not built until Content Lab + Bulk Engine are green.

The current state is partially wired and has multiple connection-layer bugs (site connect, LLM settings persistence, field discovery). This document is the authoritative scope for the rebuild pass.

---

## 2. Current Known Issues (observed)

| Area | Symptom | Suspected root cause |
| :--- | :--- | :--- |
| Site connect | User cannot reliably add a WordPress site via REST API | Missing/broken `wordpress.controller.ts` endpoints, no auth probe, no encrypted credential store |
| LLM settings | Ollama key entry exists but "Test Connection" flow is unreliable across providers | Provider interface not uniform; key encryption round-trip not verified |
| Content Lab | Generation runs but image injection + featured-image selection not wired end-to-end | Image provider service missing; h2-offset insertion logic not in processor |
| Bulk Engine | Field mapper UI exists but discovery of ACF/Gutenberg fields from a live site is flaky | Field-discovery endpoint incomplete; no caching of schema |
| General | Frontend swallows backend errors silently | Missing typed error envelopes; no toast/inline surface for failed probes |

These are the working hypotheses. Each phase below includes a verify-first step before code changes.

---

## 3. Target Architecture (summary)

```
rankray-hq-backend/src/automation/
  controllers/
    automation.controller.ts        # top-level orchestration
    wordpress.controller.ts         # site connect, probe, field discovery
    llm-settings.controller.ts      # provider CRUD, test-connection
  services/
    automation.service.ts
    wordpress.service.ts            # REST v2 client (auth, posts, pages, media, ACF)
    llm-settings.service.ts         # encrypt/decrypt, provider routing
    image-provider.service.ts       # Unsplash / Pexels / Pixabay (free sources)
    content-lab.service.ts
    bulk-engine.service.ts
  processors/
    automation.processor.ts         # BullMQ consumer — content jobs
    bulk.processor.ts               # BullMQ consumer — bulk-page jobs
  dto/
    automation.dto.ts, wordpress.dto.ts, llm.dto.ts
```

```
rankray-hq-frontend/src/modules/automation/
  Automation.tsx                    # shell + router
  features/
    content-automation/             # Content Lab
    bulk-pages/                     # Bulk Engine
    settings/                       # AI + Connections
    healing/                        # Planned placeholder
  services/
    automationService.ts
    wordpress.api.ts
    llm.api.ts
  types/automation-types.ts
```

---

## 4. Phased Execution

Each phase is **atomic** (one session = one phase) per the project protocol. Each phase has a spec envelope before it starts.

### Phase A — Connection Layer (WordPress site connect)

**Goal:** User can add a WordPress site, probe it, and see a green "connected" state that persists.

**Deliverables:**
- `POST /automation/wordpress/connect` — accepts `{ siteUrl, username, applicationPassword }`, probes `/wp-json/wp/v2/users/me`, stores encrypted credentials in `WordPressSite` table.
- `GET /automation/wordpress/sites` — lists connected sites with last-probe status.
- `DELETE /automation/wordpress/sites/:id` — revokes.
- Frontend: "Add a site" form → calls connect → shows live probe result → populates Connections sidebar.
- Typed error envelope surfaced in the UI.

**Success criteria:**
- Can connect a fresh site and see it in the sidebar within 3 seconds.
- Disconnecting removes it. Re-connecting works.
- Invalid credentials return a clear error, not a silent failure.

### Phase B — LLM Settings (Ollama-first, provider-agnostic)

**Goal:** User can select an LLM provider, save the key, and run "Test Connection" against a real endpoint.

**Deliverables:**
- `ai-provider.interface.ts` uniform contract: `testConnection()`, `listModels()`, `generate(prompt, opts)`.
- Providers: `ollama.provider.ts` (priority), `anthropic.provider.ts`, `openai.provider.ts`, `gemini.provider.ts`.
- `POST /automation/llm/settings` — encrypts and stores key; returns masked form.
- `POST /automation/llm/test` — runs provider's `testConnection()`, returns model list.
- Frontend: Active-provider dropdown → key input → Check Models + Test Connection buttons wired.

**Success criteria:**
- Ollama end-to-end: save key, list models, generate a 1-paragraph test.
- Keys never returned to the browser after save (AES-256 at rest).
- Switching providers preserves per-provider keys.

### Phase C — Content Lab (single-post publisher)

**Goal:** Generate a semantic-SEO blog post, inject images, pick featured image, push to WordPress as draft or published.

**Deliverables:**
- Markdown rules editor (default ruleset + user override, persisted per site).
- Generation pipeline:
  1. Outline → H2/H3 tree (LLM).
  2. Section-by-section body generation against rules.
  3. Image search per H2 via `image-provider.service` (Unsplash/Pexels/Pixabay).
  4. Inject images after each H2 (or once per article — user setting).
  5. Featured image = first relevant image or explicit user pick.
  6. Push to WP: `POST /wp/v2/posts` with `status: draft|publish`.
- Frontend: topic input → rules editor → generate → preview → push.

**Success criteria:**
- One-click flow from topic to live WP draft with images in ≤ 90 seconds for a ~1500-word post.
- Default ruleset is editable markdown; changes persist.
- Featured image appears on WP draft.

### Phase D — Bulk Engine (programmatic page generator)

**Goal:** Upload CSV / paste spreadsheet, map columns to WordPress page fields (ACF + core), generate N pages, push in batches.

**Reference:** `SEO Engine Ai - wordpress old plugin/` (80% complete) — port the field-mapping and batching logic, rebuild UI inside RRHQ.

**Deliverables:**
- Field discovery endpoint: `GET /automation/wordpress/sites/:id/page-schema` — returns core fields + ACF field groups.
- CSV upload + parse on frontend, column → field mapper UI.
- Bulk job queued via `bulk.processor.ts`, batched to respect WP rate limits.
- Progress UI with per-row success/failure.

**Success criteria:**
- Can generate and publish 100 pages from a CSV without manual intervention.
- Field mapping is saved per template.
- Failures are retryable per-row.

### Phase E — Healing Engine (planned, not built)

Deferred. Spec to be written once A–D are green. Expected scope: scheduled re-audit of published pages, diff vs. SEO rules, queue auto-repairs. Placeholder folder + route only.

---

## 5. Handoff Envelope (every phase)

Before Opus starts a phase, Antigravity (or the user) must deliver:

```
## Task — one sentence
## Files to touch — path:lines — change
## Context (facts, no prose)
## Success criteria
## Out of scope
```

No phase begins without this envelope. See [.claude/rules/protocol.md](../../../../Rank%20Ray%20HQ/.claude/rules/protocol.md).

---

## 6. QA Gate (per phase)

Follow [QA_CHECKLIST.md](../../../../Rank%20Ray%20HQ/.claude/rules/QA_CHECKLIST.md) plus phase-specific:

- No `any` in new code.
- All new endpoints have a DTO.
- Errors surface in UI.
- Keys never round-trip to browser.
- Manual UAT on `dev` before `main`.

---

## 7. Out of Scope (until later)

- Non-WordPress targets (Webflow, Ghost, Shopify) — parked.
- Multi-LLM ensemble generation — parked.
- Image generation (DALL·E / SDXL) — Phase C uses free stock only.
- Healing Engine full build.
- Scheduled / recurring content — Phase C ships one-shot only.
