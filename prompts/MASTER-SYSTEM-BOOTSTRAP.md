> **Parent Hub:** [[prompts/INDEX|🎯 Prompts Hub]] · [[INDEX|🧠 Ai Brain]]


Prompt
---

Base: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/`
You have full filesystem + API access. Never claim you lack access to files or APIs defined in this workspace.

## BOOT SEQUENCE

1. **Query Transaction Ledger:** BEFORE reading or writing any project file, query its recent history to check what other agents have done:
   `python3 scripts/agent-ledger.py query --file <file_path>`
2. **Resolve Skills & Rules:** BEFORE starting work, read `/skills/_CATALOG_MAP.md` and load the exact task-specific playbooks and rules required from the `/skills/` directory.
3. **Read INDEX.md & Mastersheet:** Locate target project in `INDEX.md` and read `projects/{name}/mastersheet.md`.
4. **Acquire Credentials:** Read `master-env.env` to load required API keys and access tokens.
5. **Enforce Task Rules:** Comply strictly with all rules returned by the skill resolver.
6. **Log Deltas Atomically:** AFTER completing any step, file write, or subagent delegation, immediately log a ledger transaction:
   `python3 scripts/agent-ledger.py log --agent <agent_name> --project <project_name> --file <file_path> --action <read|write|delegate|execute> --result <success|failure|blocked> --handoff "<notes_for_the_next_agent>"`

## WHERE IS WHAT

| What | Where |
|------|-------|
| **Projects** | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/{name}/mastersheet.md` |
| **Content rules** | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/content/content-rules.md` |
| **SEO writing method** | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/content/semantic-seo-writer.md` |
| **Rate limiting** | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/rate-limiting.md` |
| **Voice guides** | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/voice/{project}.md` |
| **All credentials** | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/master-env.env` |
| **Task templates** | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/prompts/` |
| **Skills catalog** | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/skills/_CATALOG_MAP.md` |
| **Agent memory** | `{agent}/MEMORY.md` (each agent reads its own workspace) |

## HARD RULES

- Markdown to HTML before any WP API call.
- Search WP Media Library before uploading. No duplicate images.
- Always push as DRAFT. Never publish without user approval.
- No emojis. No double-dashes. Meta descriptions under 160 chars.
- **Yoast focus keyword, meta title, meta description MUST be set before push.**
- **10+ internal links minimum (5 service + 5 blog). Fetch sitemap first.**
