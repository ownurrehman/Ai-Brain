> **Parent Hub:** [[rules/INDEX|📜 Operating Rules Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Obsidian Vault Graph Integrity Standard — Zero Floating Nodes Mandate
**Enforced:** 2026-09-04 | **Scope:** All AI Bots (Hermes, Cursor, Grok, Chronos, Enigma, Antigravity, OpenClaw)

---

## 🚨 Core Directives: The 3 Non-Negotiable Rules

The Obsidian Graph View is the live visual map of the agency's brain and memory. Disconnected notes or phantom tags destroy the knowledge topology and create isolated "floating balls" detached in space.

### 1. Mandatory Parent Hub Breadcrumb (Line 1 of EVERY Note)
Every markdown file created anywhere in `Ai Brain` MUST begin with a navigation breadcrumb linking back to its folder index, project hub, and the master hub:
```markdown
> **Parent Hub:** [[websites/{site}/INDEX|🌐 {site} Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]
```
*(Or for internal docs, rules, agents: `> **Parent Hub:** [[rules/INDEX|📜 Rules Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]`)*

**Never create a markdown document without an incoming and outgoing link.**

### 2. Immediate Directory & Mastersheet Indexing (Same Turn)
Creating a file on disk without indexing it in the graph is strictly incomplete:
- Add the note link to the directory's `INDEX.md` under the appropriate section.
- If the note represents an audit, deploy, or work log, add a dated one-line entry linking to the note in the site's `mastersheet.md` and/or root `mastersheet.md`.

### 3. Strict Hashtag Hygiene — Zero Accidental Tags
Obsidian's parser treats `#word`, `#XXXX`, `#123`, or `#tag` in prose as an Obsidian Tag node.
When a document containing a naked hash has no other links, Obsidian creates:
`[Note Node] <--------> [#Tag Node]`
Because both nodes are connected to each other, Obsidian's "Hide Orphans" filter does **not** hide them. Instead, they appear as **two floating balls** floating off in empty space on the graph view!

#### Rules for Hashes:
1. **Always wrap IDs, order numbers, hex codes, and placeholders in backticks:**
   - ❌ WRONG: `Admin order page shows "Linked order: #XXXX on site"`
   - ✅ RIGHT: `Admin order page shows \`Linked order: #XXXX on site\``
   - ❌ WRONG: `Color code is #FFFFFF`
   - ✅ RIGHT: `Color code is \`#FFFFFF\``
   - ❌ WRONG: `Task #124 completed`
   - ✅ RIGHT: `Task \`#124\` completed`
2. **Never use `#` for bullet list emphasis or placeholders.**
3. **Only use `#` for markdown headings (`# Heading 1`, `## Heading 2`) or deliberately planned taxonomical tags.**

---

## 🛡️ Pre-Publish Graph Checklist for AI Agents

Before declaring any file creation or memory update complete:
- [ ] Line 1 has a valid `[[...]]` wikilink to its parent hub and master `[[INDEX]]`.
- [ ] The file is added as a link in its parent folder's `INDEX.md`.
- [ ] No naked `#XXXX`, `#123`, or `#tags` exist in the document text outside of backticks or headings.
- [ ] The file is referenced in `mastersheet.md` if it documents system state.
