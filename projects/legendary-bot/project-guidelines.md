

### Exported from Ai-Brain: SKILLS.md

# Ai-Brain skills — one map

**Goal:** Agents pick the smallest right playbook. **Rank Ray voice** lives in **`skills/`**; **breadth** lives in **`antigravity-awesome-skills/`** (same level — git submodule).

**Agency:** [Rank Ray](https://www.rankray.com)

## Rank Ray — service lines (`skills/`)

Start here when the task matches a **client service** on the website. Each folder has **`SKILL.md`** + Antigravity deep links.

| Folder | Service |
|--------|---------|
| `skills/digital-marketing/` | Digital marketing (multi-channel, strategy + coordination) |
| `skills/seo-services/` | SEO services (client delivery; pair with `seo/` for methodology) |
| `skills/web-development/` | Web development (sites, WP, React/Next/Vite) |
| `skills/app-development/` | App development (mobile / cross-platform) |
| `skills/saas-development/` | SaaS app development (tenancy, billing, product engineering) |
| `skills/ai-automation/` | AI automation (n8n, MCP, agents, integrations) |
| `skills/crypto-trading/` | Crypto trading engineering (risk-first; see workspace bot `AGENTS.md`) |

Then use **specialized** skills below (CRO, paid, RankRay SEO UI, etc.) as needed.

## Layer A — first-party (`skills/`)

These **`SKILL.md` files are intentional control layers** (triggers, inputs, Rank Ray rules)—not incomplete copies of the catalog. Each links to **Deep playbooks** under `antigravity-awesome-skills/`. Full mapping: **`skills/_CATALOG_MAP.md`**.

| Folder | Role |
|--------|------|
| `skills/seo/` | SEO research & search intent |
| `skills/content-writing/` | Structured, search-aware copy |
| `skills/wordpress-publisher/` | WordPress / Gutenberg publishing |
| `skills/rankray-seo-ui/` | RankRay product & SEO UI patterns |
| `skills/saas-go-to-market/` | ICP, positioning, GTM |
| `skills/paid-acquisition/` | Paid campaign structure |
| `skills/conversion-rate-optimization/` | Landing / funnel experiments |
| `skills/saas-app-foundation/` | Tenancy & app boundaries |
| `skills/saas-auth-billing/` | Auth & subscriptions |
| `skills/saas-growth-analytics/` | Funnel metrics & instrumentation |
| `skills/shipping-features/` | Shipping cadence & scope |
| `skills/refactor-safely/` | Safe refactors |
| `skills/debugging/` | Debugging discipline |

Each folder has a **`SKILL.md`**. If a task spans domains, choose **one primary** skill and pull specifics from **`core/`** or the library as needed.

## Layer B — Antigravity pool (`antigravity-awesome-skills/`)

~1,300 community and upstream **`SKILL.md`** files. **Git submodule** ([upstream repo](https://github.com/sickn33/antigravity-awesome-skills)) — init with `git submodule update --init --recursive`. Update from upstream: `cd antigravity-awesome-skills && git pull` (see **`ANTIGRAVITY.md`**).

- **Search:** `python3 scripts/find_antigravity_skill.py <keywords>`
- **Browse:** `antigravity-awesome-skills/CATALOG.md` or [hosted catalog](https://sickn33.github.io/antigravity-awesome-skills/)

## Precedence

**`skills/*` overrides** a library playbook when both could apply.

## Deeper detail

- **`skills/README.md`** — two-layer rationale and ripgrep examples
- **`ANTIGRAVITY.md`** — submodule clone, bump, Cursor installer


### Exported from Ai-Brain: skills/README.md

# Skills in this brain (two layers)

## 1) First-party — this folder (`./`)

These are **not dummy files**. Each **`SKILL.md`** is the **Rank Ray control layer**: triggers, inputs, workflow outline, checks, and **product-specific** rules (e.g. RankRay-HQ SEO UI, no fake metrics). They stay short on purpose.

**Depth** (long playbooks, hundreds of lines) lives in **`../antigravity-awesome-skills/skills/<id>/SKILL.md`**. Every first-party skill links there under **Deep playbooks (Antigravity Awesome Skills)**. See **`_CATALOG_MAP.md`** for the full mapping table.

**Always open the first-party `SKILL.md` first**, then load catalog files if you need more detail. When both could apply, **first-party wins** for Rank Ray constraints; the catalog **extends**, not replaces, this folder.

### [Rank Ray](https://www.rankray.com) service-line skills

| Folder | Use for |
|--------|---------|
| `digital-marketing/` | Multi-channel marketing, retainers, cross-channel plans |
| `seo-services/` | SEO client delivery (audits, roadmaps, implementation support) |
| `web-development/` | Websites, WordPress, React/Next/Vite |
| `app-development/` | Mobile / React Native / Flutter |
| `saas-development/` | SaaS products, tenancy, billing, APIs |
| `ai-automation/` | n8n, MCP, agent tooling, LLM integrations |
| `crypto-trading/` | Trading bots & risk — **`Crypto Trading Bots/Legendary Bot/AGENTS.md`** |

Full one-page map: **`../SKILLS.md`**.

---

## 2) Antigravity Awesome Skills — sibling folder, not inside `skills/`

The full **[Antigravity Awesome Skills](https://github.com/sickn33/antigravity-awesome-skills)** catalog is a **git submodule** at:

```text
../antigravity-awesome-skills/skills/<skill-id>/SKILL.md
```

Same level as **`skills/`** under **Ai-Brain** — fewer nested folders, easy to update from [upstream `main`](https://github.com/sickn33/antigravity-awesome-skills).

**Why we do not copy those folders into `skills/`:**

- **Size & noise** — 1,300+ directories.
- **Updates** — `cd antigravity-awesome-skills && git pull` then commit the submodule pointer in Ai-Brain.
- **Name clashes** — upstream ids could collide with your custom folder names.

Agents **are expected to read Antigravity skills** when first-party coverage is not enough.

See **`../ANTIGRAVITY.md`** for clone, bump, and optional Cursor install.

---

## How agents should find an Antigravity skill

1. **Machine-readable search** (preferred):

   ```bash
   cd /path/to/Ai-Brain
   python3 scripts/find_antigravity_skill.py security
   python3 scripts/find_antigravity_skill.py playwright
   ```

   Then open the printed path under `antigravity-awesome-skills/…`.

2. **JSON** — `../antigravity-awesome-skills/skills_index.json`.

3. **Catalog** — `../antigravity-awesome-skills/CATALOG.md` or the [hosted catalog](https://sickn33.github.io/antigravity-awesome-skills/).

4. **Ripgrep:**

   ```bash
   rg -i "playwright" antigravity-awesome-skills/skills_index.json | head
   ```

---

## Cursor `@` mentions (optional)

Cursor does not auto-index the submodule path. For **@skill** discovery:

```bash
npx antigravity-awesome-skills --cursor
```

See **`../ANTIGRAVITY.md`** and the upstream README.


### Exported from Ai-Brain: skills/crypto-trading/SKILL.md

---
name: crypto-trading
description: Use for Rank Ray crypto trading engagements—bots, risk controls, exchange integration, operational safety—not generic web marketing. Treat capital loss and regulatory exposure as first-class risks; default to paper/dry-run until explicitly approved.
---

# Rank Ray — Crypto trading

**Agency:** [Rank Ray](https://www.rankray.com) — trading automation and related engineering **only** with explicit client risk acceptance and compliance awareness.

## Use when

- Client work involves **CCXT-style exchange APIs**, **bot logic**, **risk layers**, **state persistence**, or **execution workflows**.
- You are working in this workspace’s **Legendary Bot** tree or a similar client repo.
- Task is **operational safety** (DRY_RUN, confirmations, kill switch) as much as strategy.

## Avoid when

- **Investment advice** or **guaranteed returns**—out of scope; stay in engineering and risk disclosure.
- **Pure DeFi protocol auditing** without a clear build scope—engage specialized security review.
- General **SaaS** product work → **`../saas-development/SKILL.md`**.

## Rank Ray delivery norms

- **Default `DRY_RUN=true`** (or equivalent) until the client signs off on live trading.
- **Secrets:** API keys in env only; never log keys or full payloads with credentials.
- **Multi-layer confirmations** before live orders; surface **notional risk** and **exchange limits**.
- Document **failure modes**: exchange downtime, partial fills, stale balances, clock skew.
- **Compliance:** jurisdictions, KYC/AML, and marketing claims are **client/legal**—flag unknowns.

## Workspace reference

- **Legendary Bot:** `./Crypto Trading Bots/Legendary Bot` — see **`Crypto Trading Bots/Legendary Bot/AGENTS.md`** (or workspace **`AGENTS.md`** / **`Mastersheet.md`**).
- Prefer **incremental** changes with backtests or paper trading evidence when the repo supports it.

## Workflow (summary)

1. Read the **bot repo `AGENTS.md`** and current **`state.json` / config** conventions.
2. Trace order path: signal → risk checks → execution → persistence.
3. Add or tighten **guardrails** before expanding strategy complexity.
4. Verify with **dry run** and logged decision trail.

## Related first-party skills

| Skill | When |
|-------|------|
| [`../debugging/SKILL.md`](../debugging/SKILL.md) | Execution bugs / race conditions |
| [`../shipping-features/SKILL.md`](../shipping-features/SKILL.md) | Controlled releases of bot changes |

## Deep playbooks (Antigravity Awesome Skills)

There is **no dedicated “crypto trading”** skill in the catalog. Use **general** security and API discipline:

| Role | Path |
|------|------|
| API security patterns | [`../antigravity-awesome-skills/skills/api-security-best-practices/SKILL.md`](../antigravity-awesome-skills/skills/api-security-best-practices/SKILL.md) |
| Reliable / production APIs | [`../antigravity-awesome-skills/skills/api-endpoint-builder/SKILL.md`](../antigravity-awesome-skills/skills/api-endpoint-builder/SKILL.md) |

**Order:** Capital safety and repo conventions **dominate**; Antigravity only for generic API/security depth. Run `python3 scripts/find_antigravity_skill.py exchange risk` to discover newer catalog ids over time.


### Restored Global AI Operational Rules

1. **Persistence first** (where the project has state—e.g. bot `state.json`, HQ DB).
2. **Safety guards** — no live trading / production flags without explicit owner confirmation.
3. **Modular, layered changes** — especially trading strategy and SaaS modules.
4. **Document everything** — update **`Mastersheet.md`** and the relevant **`AGENTS.md`** (workspace root + project folder) on material changes.
5. **Rank Ray line** — after meaningful work on **RankRay-HQ**, **SEO Engine Ai**, or **Rank Ray Plugins**, add a dated **Recent Updates** entry here.
6. **Agentic brain** — when adding or changing **skills** / agent behavior for coding, prefer **`Ai-Brain/`** (`skills/`, `ai_brain/agents/`) and note significant additions in **Recent Updates** if they affect how other repos should be worked on.
7. **Session start** — for a **new conversation** or **non-trivial task**, consult **`Ai-Brain/INDEX.md`** before deep-diving; use **`Ai-Brain/antigravity-awesome-skills/`** only when first-party skills do not cover the need.
