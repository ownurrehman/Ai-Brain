# Agent Fleet Orchestration Dashboard

**Last Updated:** 2026-08-28 by Hermes — Shared Memory Architecture: ALL agents (except Alpha) store MEMORY.md/USER.md in Ai Brain/agents/<name>/ via symlinks. Obsidian-style shared brain: every agent can read what others are doing. Alpha stays fully isolated.
**Ai Brain Location:** `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/`

---

## Fleet Overview

| Agent | Model | Provider | Role | Profile | Status |
|---|---|---|---|---|---|
| **Hermes** | glm-5.2 | Ollama Cloud | Main manager, coordinator, WhatsApp | default | ONLINE (WhatsApp gateway running) |
| **Chronos** | kimi-k2.7-code | Ollama Cloud | Dev, infra, coding, automation | chronos | Configured, idle (spawn on demand) |
| **Enigma** | qwen3.5:397b | Ollama Cloud | Content writing, SEO, blogs | enigma | Configured, idle (spawn on demand) |
| **Nemo** | nvidia/nemotron-3-ultra-550b-a55b | NVIDIA API | Elite code, architecture | nemo | Configured, idle (spawn on demand) |
| **Scout** | gpt-oss:20b | Ollama Cloud | Research, competitor intel | scout | Configured, idle (spawn on demand) |
| **Emilia** | minimax-m3 | Ollama Cloud | Outreach, backlinks, email | emilia | Configured, idle (spawn on demand) |

**Spawn command:** `hermes -p <profile> chat -q "task" -t safe` (foreground) or `terminal(background=true)` for background tasks

**CLI wrappers:** `chronos`, `enigma`, `nemo`, `scout`, `emilia` (available as shell commands)

---

## Active Automated Jobs

| Job | ID | Schedule | Delivery | Status | What It Does |
|---|---|---|---|---|---|
| AgentMail Inbox Monitor | 214b36b8d3a8 | Every 1h | Local (silent) | ACTIVE | Checks sheikhown@agentmail.to for new emails, processes backlink replies, logs locally. Only surfaces to user via Fleet Status Report. |
| Fleet Status Report | df10a4d168af | Every 3h | WhatsApp (origin) | ACTIVE | Short summary of all agents, cron status, new emails, and issues. Only reports meaningful updates. |

---

## Current Task Assignments

### Hermes (Main) — IN PROGRESS
- [x] TonicPhysio site restore: 29 posts restored, all 77 published posts expanded to 2000+ words, 0 em-dashes
- [x] AgentMail integration + inbox monitor cron
- [x] Agent fleet setup (6 profiles with models, SOUL.md, API keys)
- [x] Fleet orchestration dashboard created
- [ ] **RankRay SEO audit fixes** — 148 posts audited. Found: 20 under 2000w, 47 with <3 internal links, 1 em-dash issue. Needs fixing.
- [ ] RankRay 10 draft blogs (IDs 23900-23919) — need publish decision from user
- [ ] TonicPhysio 28 draft posts — need publish decision from user

### Enigma (Content/SEO) — IDLE
**Available for:** Blog writing, content expansion, SEO optimization
**Suggested tasks:**
- RankRay: Expand 20 thin posts to 2000+ words
- RankRay: Write new buying-intent blog posts (USA/Canada/UAE focus)
- TonicPhysio: Write new GSC gap-targeted articles
- Coinsfera: Write new blog posts for crypto/finance SEO
- BacklinkCrypto: Write marketplace blog content

### Chronos (Dev/Infra) — IDLE
**Available for:** Coding, server config, automation scripts, WordPress API
**Suggested tasks:**
- WordPress automation scripts (bulk operations, PHP snippets)
- Cron job maintenance and monitoring
- API integrations (GSC, GA4, Search Console)
- Server configuration, deployment
- RankRay HQ SaaS development

### Nemo (Elite Code) — IDLE
**Available for:** Complex architecture, refactoring, advanced algorithms
**Suggested tasks:**
- RankRay HQ backend architecture
- Complex multi-service integrations
- Performance optimization
- Code review and refactoring

### Scout (Research) — IDLE
**Available for:** Web research, competitor analysis, keyword research
**Suggested tasks:**
- RankRay GSC keyword gap analysis
- Competitor backlink research for all client sites
- SERP analysis for all client sites
- Market research for SEO packages
- Content gap analysis across all sites

### Emilia (Outreach) — IDLE
**Available for:** Email outreach, backlink building, directory submissions
**Suggested tasks:**
- RankRay backlink building via AgentMail (sheikhown@agentmail.to)
- Directory submissions for all client sites
- Outreach email campaigns
- HARO/Qwoted pitch writing

---

## Site Status Summary

| Site | Posts | Pages | Drafts | Last Work | Urgent Needs |
|---|---|---|---|---|---|
| rankray.com | 148 pub | 70 | 10 drafts | Aug 16 | 20 thin posts, 47 low-link posts, 1 em-dash. Drafts need publish decision. |
| tonicphysio.com | 77 pub | 96 | 28 drafts | Aug 17 | 28 drafts need publish decision. All published posts now 2000+ words. |
| coinsfera.com | ~100 blog + 408 news (noindex) | ~26 | 0 | Jul 20 | Cannibalization fixes not executed. Needs new content. |
| teammotorcycle.com | Shopify | - | - | Aug 13 | Needs content strategy. |
| backlinkcrypto.com | 18 pub | 15 | 0 | Jul 18 | Needs more content. AIOSEO meta issues. |

---

## Orchestration Rules

1. **Hermes is the orchestrator** — all tasks flow through Hermes to other agents
2. **Hermes spawns agents** via `hermes -p <profile> chat -q "task"` or terminal background
3. **All agents read Ai Brain first** — INDEX.md, mastersheet, credentials, rules
4. **Agents report back to Hermes** — Hermes relays results to user on WhatsApp
5. **Only Hermes has WhatsApp gateway** — other agents are headless workers
6. **User talks only to Hermes** — Hermes dispatches to the right specialist
7. **Parallel execution** — multiple agents can work simultaneously on different tasks
8. **No agent crosses into another's domain** unless explicitly assigned by Hermes
9. **Profile servers do NOT need to run persistently** — they spawn on demand. Running them idle wastes RAM on 16GB M1.
10. **AgentMail monitor runs silent** — logs locally, only surfaces via Fleet Status Report

## How to Assign Tasks

Tell Hermes in plain language:
- "Have Enigma write 5 RankRay blogs about SEO services"
- "Tell Chronos to fix the WordPress bulk noindex script"
- "Have Scout research competitor backlinks for Coinsfera"
- "Tell Emilia to start outreach for RankRay backlinks"
- "Have Nemo review the RankRay HQ architecture"

Hermes will spawn the right agent with the task and report back when done.

---

## Model Configuration

| Setting | Value |
|---|---|
| Hermes provider | ollama-cloud |
| Hermes model | glm-5.2 |
| Hermes fallback | gemma4:31b-cloud |
| Vision (auxiliary) | kimi-k2.6 via ollama-cloud |
| NVIDIA API key | In .env as NVIDIA_API_KEY (Nemo only) |
| Ollama Cloud API key | In .env as OLLAMA_API_KEY (shared across all Ollama profiles) |
| xai-oauth | DISABLED — logged out, not in use |

### Available Ollama Cloud Models (17 confirmed working)
deepseek-v4-flash:preview, deepseek-v4-pro:0813, deepseek-v4-pro:preview, gemma4:31b, glm-5.1, glm-5.2, gpt-oss:120b, gpt-oss:20b, kimi-k2.6, kimi-k2.7-code, minimax-m2.7, minimax-m3, mistral-large-3:675b, nemotron-3-nano:30b, nemotron-3-super, nemotron-3-ultra, qwen3.5:397b

### NVIDIA API Models (102 total, Nemo uses)
nvidia/nemotron-3-ultra-550b-a55b (primary), nvidia/nemotron-3-super-120b-a12b, nvidia/nemotron-3-nano-30b-a3b, and 99 others