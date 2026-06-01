# Agent Team & Operation Rules
# PRIMARY SOURCE OF TRUTH FOR ALL AGENT CONFIG

## Team Structure & Model Mapping

The workspace is operated by a specialized 3-agent team. Each agent is assigned a model optimized for their specific domain.

| Agent | Role | Primary Model | Focus |
| :--- | :--- | :--- | :--- |
| **Main (Antigravity)** | Strategist & Orchestrator | `ollama/gemma4:31b-cloud` | Planning, SEO Strategy, and Final QA. |
| **Nemo** | Extreme Engineering | `nvidia/qwen/qwen3-coder-480b-a35b-instruct` | Complex coding, architecture, and bug fixing. |
| **Chronos** | Production & Research | `ollama/deepseek-v4-pro:cloud` | Writing, SERP analysis, and technical execution. |

---

## Rules Source of Truth

ALL operational rules live in `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/`.

| Rule File | Path | Purpose |
| :--- | :--- | :--- |
| **Master Index** | `MASTER-INDEX.md` | This file: entry point for the rules hub. |
| **Agent Reference** | `00-agents-reference.md` | Detailed agent roles and technical SOPs. |
| **Content Quality** | `../rules/content/quality-rules.md` | Core writing rules and banned phrases. |
| **Semantic SEO** | `../rules/content/semantic-seo-writer.md` | Koray method for pillar content generation. |
| **Self Audit Protocol** | `../rules/content/self-audit-protocol.md` | Mandatory verification before delivery. |
| **Pre-Publish Checklist**| `../rules/content/pre-publish-checklist.md`| Final gate for WordPress content. |

---

## Project Mastersheets

| Project | Mastersheet | Core Protocols |
| :--- | :--- | :--- |
| **Rank Ray** | [RankRay Master](../projects/rankray-hq/mastersheet.md) | Semantic SEO, Link Library |
| **Tonic Physio** | [TonicPhysio Master](../projects/tonicphysio.com/mastersheet.md) | Health Voice, WP Category 325 |
| **Khan LLP** | [KhanLLP Master](../projects/khanllp/mastersheet.md) | Legal Expertise, Trust Factors |

---

## Operational Guide

### Delegation Protocol
- **Research & SERP analysis** : Handle via `Chronos`.
- **Complex Engineering** : Handle via `Nemo`.
- **Content & Management** : Handle via `Main`.

### Communication Hygiene
- No raw tool calls in messages.
- Use `self-audit-protocol.md` for all high-value work.
- Provide a progress heartbeat every 5-10 minutes for long tasks.

---

## Technical Infrastructure
- [.env Configuration](../../.env) : API keys and credentials.
- [Scripts Directory](../../scripts/) : Custom automation tools.
- [Skill Reference](../../skills/) : Specialized capabilities.
