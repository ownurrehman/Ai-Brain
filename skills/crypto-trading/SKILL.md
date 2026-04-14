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
