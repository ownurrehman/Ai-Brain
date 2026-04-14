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
