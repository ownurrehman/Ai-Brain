---
name: ai-automation
description: Use for Rank Ray AI automation—n8n workflows, MCP servers, agent tooling, LLM integrations—when the outcome is operational automation or agent-ready systems, not a customer-facing mobile app.
---

# Rank Ray — AI automation

**Agency:** [Rank Ray](https://www.rankray.com) — workflow automation and **AI-augmented** systems (internal ops and client deliverables).

## Use when

- **n8n** (or similar) **workflows**: integrations, webhooks, ETL-light, notifications, lead routing.
- Building **MCP servers** or **agent tools** so LLMs can act on CRMs, tickets, or internal APIs safely.
- Wiring **LLM features** into existing products (chat, summarization, classification) with guardrails.
- **Human-in-the-loop** approvals for high-risk actions (payments, deletes, mass messaging).

## Avoid when

- **Primary** deliverable is a **SaaS product surface** → **`../saas-development/SKILL.md`** (may still use automation as a component).
- **Only** traditional web pages without AI/automation scope → **`../web-development/SKILL.md`**.
- **Crypto execution** bots → **`../crypto-trading/SKILL.md`** (different risk profile).

## Rank Ray delivery norms

- **Least privilege** credentials; rotate keys; never commit secrets.
- **Idempotency** for webhooks and scheduled jobs; handle partial failures explicitly.
- **Rate limits** and backoff for external APIs; log correlation ids.
- **Safety:** classify tools as read vs write; require confirmation for destructive actions.
- Prefer **observable** workflows (run history, error alerts) for client handoffs.

## Workflow (summary)

1. Map triggers, data contracts, failure modes, and rollback.
2. Prototype with test data; add secrets via env / vault patterns the repo uses.
3. Harden (retries, timeouts, monitoring hooks).
4. Document “how to replay / disable” for ops.

## Related first-party skills

| Skill | When |
|-------|------|
| [`../shipping-features/SKILL.md`](../shipping-features/SKILL.md) | Shipping automation features in app repos |
| [`../debugging/SKILL.md`](../debugging/SKILL.md) | Pipeline failures |

## Deep playbooks (Antigravity Awesome Skills)

| Role | Path |
|------|------|
| MCP server design | [`../antigravity-awesome-skills/skills/mcp-builder/SKILL.md`](../antigravity-awesome-skills/skills/mcp-builder/SKILL.md) |
| n8n expressions | [`../antigravity-awesome-skills/skills/n8n-expression-syntax/SKILL.md`](../antigravity-awesome-skills/skills/n8n-expression-syntax/SKILL.md) |
| n8n JS in Code node | [`../antigravity-awesome-skills/skills/n8n-code-javascript/SKILL.md`](../antigravity-awesome-skills/skills/n8n-code-javascript/SKILL.md) |
| n8n Python in Code node | [`../antigravity-awesome-skills/skills/n8n-code-python/SKILL.md`](../antigravity-awesome-skills/skills/n8n-code-python/SKILL.md) |
| n8n MCP tools | [`../antigravity-awesome-skills/skills/n8n-mcp-tools-expert/SKILL.md`](../antigravity-awesome-skills/skills/n8n-mcp-tools-expert/SKILL.md) |
| Agent orchestration (reference) | [`../antigravity-awesome-skills/skills/agent-orchestrator/SKILL.md`](../antigravity-awesome-skills/skills/agent-orchestrator/SKILL.md) |
| Vercel AI SDK (if on Vercel stack) | [`../antigravity-awesome-skills/skills/vercel-ai-sdk-expert/SKILL.md`](../antigravity-awesome-skills/skills/vercel-ai-sdk-expert/SKILL.md) |

**Order:** Rank Ray safety and ops norms here; Antigravity for node/syntax/tool depth. Search more: `python3 scripts/find_antigravity_skill.py n8n`.
