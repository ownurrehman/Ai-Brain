# First-party skill → Antigravity deep playbooks

Each folder under **`skills/`** holds a **short Rank Ray control file** (`SKILL.md`): when to use it, inputs, checks, and product-specific rules. **Depth** (long checklists, edge cases) lives in the submodule **`../antigravity-awesome-skills/skills/<id>/SKILL.md`**.

Update the catalog: `cd ../antigravity-awesome-skills && git pull` (then commit the submodule pointer in Ai-Brain).

| `skills/` folder | Suggested deep playbooks (open `SKILL.md` in each) |
|------------------|-----------------------------------------------------|
| `digital-marketing/` | `product-marketing-context`, `launch-strategy`, `paid-ads`, `marketing-ideas`, `free-tool-strategy` |
| `seo-services/` | `seo`, `seo-fundamentals`, `seo-aeo-landing-page-writer`, `local-legal-seo-audit` (adapt for vertical) |
| `web-development/` | `nextjs-app-router-patterns`, `nextjs-best-practices`, `react-best-practices`, `shadcn`, `wordpress`, `wordpress-plugin-development`, `cc-skill-frontend-patterns` |
| `app-development/` | `mobile-developer` (+ search `expo` / stack-specific ids) |
| `saas-development/` | `saas-multi-tenant`, `clerk-auth`, `monetization`, `payment-integration`, `analytics-product`, `nextjs-app-router-patterns` |
| `ai-automation/` | `mcp-builder`, `n8n-expression-syntax`, `n8n-code-javascript`, `n8n-code-python`, `n8n-mcp-tools-expert`, `agent-orchestrator`, `vercel-ai-sdk-expert` |
| `crypto-trading/` | `api-security-best-practices`, `api-endpoint-builder` (search catalog for newer trading-related ids) |
| `seo/` | `seo` |
| `debugging/` | `bug-hunter`, `debugger` |
| `shipping-features/` | `subagent-driven-development`, `conductor-implement` |
| `refactor-safely/` | `code-refactoring-refactor-clean` |
| `content-writing/` | `beautiful-prose`, `avoid-ai-writing` |
| `wordpress-publisher/` | `wordpress` |
| `rankray-seo-ui/` | `react-best-practices`, `shadcn`, `seo` |
| `saas-app-foundation/` | `saas-multi-tenant` |
| `saas-auth-billing/` | `clerk-auth`, `monetization`, `payment-integration` |
| `saas-growth-analytics/` | `analytics-product` |
| `saas-go-to-market/` | `product-marketing-context`, `launch-strategy` |
| `paid-acquisition/` | `paid-ads` |
| `conversion-rate-optimization/` | `seo-aeo-landing-page-writer`, `analytics-product` |

Find more: `python3 scripts/find_antigravity_skill.py <keywords>` from **Ai-Brain** root.
