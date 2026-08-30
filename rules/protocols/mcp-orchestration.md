---
name: mcp-orchestration
description: "Master MCP (Model Context Protocol) Server Registry & Tool Dispatch Matrix mapping which agent calls which MCP server with fallback rules."
category: protocols
---

> **Parent Hub:** [[rules/INDEX|📜 Agency Operating Rules Hub]] · [[system/INDEX|⚙️ System Infrastructure Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# 🔌 MCP (Model Context Protocol) Server Orchestration Matrix

> **Standard operating procedures for routing, invoking, and managing Model Context Protocol (MCP) servers across the agent fleet.**

---

## 🗺️ Master MCP Dispatch Matrix

| MCP Server | Primary Domain | Permitted Agents | Fallback Strategy |
|---|---|---|---|
| **`ga-mcp` (analytics-mcp)** | Google Analytics 4 (GA4) traffic & conversion telemetry | Chronos, Scout, Hermes | Fall back to cached reports in `system/reports/` |
| **`chrome-devtools-mcp`** | Headless browser testing, DOM inspection, a11y, LCP auditing | Scout, Nemo, Chronos | Fall back to Firecrawl or Python Playwright |
| **`hostinger-*`** | Hostinger Agency Hosting, DNS, Domains, VPS, WordPress | Chronos | Fall back to REST API via curl or WP-CLI |
| **`linear-mcp-server`** | Issue tracking, release pipelines, project roadmaps | Hermes, Nemo | Fall back to local markdown issue log in `projects/` |
| **`notion-mcp-server`** | External workspace docs, knowledge sync | Hermes, Emilia | Fall back to internal Obsidian vault notes |
| **`firebase-mcp-server`** | Firebase App Hosting, Firestore, Auth, Crashlytics | Chronos, Nemo | Fall back to Firebase CLI scripts |
| **`sequential-thinking`** | Deep multi-step reasoning, mathematical problem solving | All Agents | Fall back to internal chain-of-thought reasoning |

---

## ⚡ Execution Guidelines for Agents

1. **Lazy Loading:** Tool schemas are loaded on demand. Agents must check parameters in `mcp/<serverName>/` before firing calls.
2. **Timeout Boundaries:** Any MCP tool call taking $> 30\text{ seconds}$ must abort and trigger the self-healing fallback.
3. **No Direct Token Exposure:** Agents must never log or echo MCP auth tokens into markdown transcripts.
4. **Idempotency:** Write operations (e.g. `DNS_updateDNSRecordsV1`, `ecommerce_updateAProduct`) must be checked before mutating live production data.

---

## 🔗 Related Systems
- [[rules/protocols/agent-self-healing|Self-Healing & Error Recovery]]
- [[docs/ga-mcp-reference|GA4 MCP Reference Documentation]]
- [[docs/ENV|Credential Mapping (docs/ENV.md)]]
