> **Parent Hub:** [[prompts/INDEX|🎯 Prompts Hub]] · [[INDEX|🧠 Master Ai Brain Hub]] · [[agents/FLEET-ORCHESTRATION|🤖 Agent Fleet]]

# 🚀 Master System Bootstrap & Agency Agent Directives

Base Workspace: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/`
You have direct filesystem, terminal, and API access within this workspace. Never state that you lack access to files or APIs configured in this environment.

---

## ⚡ 1. Boot Sequence (Execute Before Any Code/Content Work)

1. **Query Transaction Ledger:** BEFORE reading or modifying any project file, inspect recent activity and agent handoffs:
   ```bash
   python3 scripts/agent-ledger.py query --file <file_path>
   ```
2. **Resolve Target & Mastersheet:**
   - For website portfolio: Read `websites/{domain}/mastersheet.md` and `websites/{domain}/INDEX.md`.
   - For internal projects & systems: Read `projects/{name}/mastersheet.md` and `INDEX.md`.
3. **Resolve Skills & Playbooks:** Consult `skills/_CATALOG_MAP.md` and load relevant playbooks from `skills/` (RankRay modular skills) or `.agents/skills/`.
4. **Acquire Credentials:** Inspect `master-env.env` to load verified API tokens, application passwords, and database credentials.
5. **Enforce Project Architecture & Rules:**
   - For WordPress themes/plugins: Read target site's `rules.md` and `features-code-map.md`.
   - For SEO & content: Read `rules/content/content-rules.md` and `rules/content/semantic-seo-writer.md`.
6. **Log Transaction Atomically:** AFTER completing any significant step, file write, or agent delegation, immediately record the ledger entry:
   ```bash
   python3 scripts/agent-ledger.py log --agent <agent_name> --project <project_name> --file <file_path> --action <read|write|delegate|execute> --result <success|failure|blocked> --handoff "<notes_for_the_next_agent>"
   ```

---

## 🗺️ 2. File & Resource Map

| Resource | Path | Description |
|:---|:---|:---|
| **Website Portfolios** | `websites/{domain}/` | Client storefronts, themes, plugins, docs, and audit reports |
| **Internal Projects** | `projects/{name}/` | Internal agency tools, automation swarms, and ventures |
| **Master Index** | `INDEX.md` | Primary knowledge navigation hub |
| **All Credentials** | `master-env.env` | Central environment variables & API tokens (Do NOT commit) |
| **Skills Catalog** | `skills/_CATALOG_MAP.md` | Index of all production modular skills |
| **General Content Rules** | `rules/content/content-rules.md` | Copywriting standards, zero em-dash rule, and QA gates |
| **Semantic SEO Engine** | `rules/content/semantic-seo-writer.md` | Entity-dense AEO/GEO article generation guidelines |
| **Rate Limiting** | `rules/rate-limiting.md` | API backoff policies and concurrency rules |
| **Brand Voice Guides** | `rules/voice/{project}.md` | Brand persona, vocabulary, and stylistic boundaries |
| **Prompt Templates** | `prompts/` | Reusable master prompts and system blueprints |
| **Agent Memory** | `memory/` or `{agent}/MEMORY.md` | Dated daily notes and persistent agent working memory |

---

## 🚨 3. Master Agency Non-Negotiables

* **Draft Mode Only:** Always push new posts, products, and landing pages as **DRAFT** (`status: draft`). Never publish directly without explicit user sign-off.
* **Media Library De-duplication:** Always search the WordPress Media Library before uploading new files. Reuse existing media IDs. Never create orphan duplicates or hardcode external CDN URLs.
* **Zero Em-Dash & Emojis Rule:**
  * Absolutely NO em-dashes (`—`) or double-hyphens (`--`) in published prose or copy. Use commas, colons, or parentheses.
  * NO emojis in business body copy unless specifically requested in a social media brief.
* **Meta & SEO Completeness:**
  * Focus keyword, meta title, and meta description (< 160 characters) MUST be set before pushing content.
  * For Rank Math sites: write via `/wp-json/rankmath/v1/updateMeta` (`rank_math_title`, `rank_math_description`, `rank_math_focus_keyword`).
  * For Yoast sites: write via native post meta (`_yoast_wpseo_title`, `_yoast_wpseo_metadesc`, `_yoast_wpseo_focuskw`).
* **HTML Conversion:** Always convert Markdown to clean, semantic HTML5 before sending payloads to WordPress REST endpoints.
* **Internal Linking Threshold:** Every published article must contain a minimum of **10+ internal links** (at least 5 service/category pages + 5 relevant informational articles). Fetch the XML sitemap first to verify live slugs.
