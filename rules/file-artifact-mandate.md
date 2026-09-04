> **Parent Hub:** [[rules/INDEX|📜 Operating Rules Hub]] · [[INDEX|🧠 Ai Brain]]

# File Artifact Mandate — Both Agents
# Enforced: 2026-05-15

## Rule: Chat is NOT a storage medium

**NEVER dump large outputs in chat.** Chat is for coordination, not for reports.

### What Counts as "Large"

| Type | Threshold | Action |
|------|-----------|--------|
| Bullet list | >10 items | Write to file, link in chat |
| Table | >5 rows | Write to file, link in chat |
| Code block | >50 lines | Write to file, link in chat |
| Text output | >500 words | Write to file, link in chat |
| Audit results | Any size | Always write to `reports/` |
| Research findings | Any size | Always write to `reports/` |
| Content drafts | Any size | Always write to `websites/{site}/drafts/` |

### Where to Write

| Output Type | Directory | Filename Pattern |
|-------------|-----------|-------------------|
| SEO audits | `reports/` | `{site}-audit-{YYYY-MM-DD}.md` |
| Research | `reports/` | `{topic}-research-{YYYY-MM-DD}.md` |
| Content drafts | `websites/{site}/drafts/` | `{slug}-{YYYY-MM-DD}.md` |
| Code docs | `system/docs/` | `{component}-{YYYY-MM-DD}.md` |
| Cron logs | `memory/` | `{YYYY-MM-DD}-cron-{name}.md` |
| Quick state | `memory/` | `{YYYY-MM-DD}.md` |

### Chat Response Format

```
Done. Written to: `reports/rankray-audit-2026-05-15.md`

Key findings (5 bullets max):
- Finding 1
- Finding 2
- Finding 3
```

**NEVER paste:**
- Full audit tables
- Raw script output
- Multi-paragraph explanations
- JSON dumps
- Error traces (link to log file instead)

### Before/After Examples

**BAD (wastes tokens):**
```
Here is the full audit of all 430 collection pages:

| Page | Status | Meta | H1 |
|------|--------|------|----|
| /helmets | Missing | - | OK |
| /jackets | Missing | - | OK |
... (430 rows)
```

**GOOD (saves tokens):**
```
Audit complete. 430 pages checked, 428 missing meta descriptions.

Written to: `reports/teammotorcycle-collection-meta-audit-2026-05-15.md`

Critical: `/collections/helmets` returns 404 — needs redirect.

Next: Fix meta descriptions in batches of 50?
```

## Verification Rule

Before saying "done":

1. [ ] Output file written to correct directory
2. [ ] Filename follows pattern
3. [ ] File starts with a Parent Hub navigation breadcrumb (`> **Parent Hub:** [[...]] · [[INDEX|🧠 Master Ai Brain Hub]]`)
4. [ ] File is linked in its directory's `INDEX.md` and relevant `mastersheet.md`
5. [ ] Zero naked `#tags` or placeholder hashes (`#XXXX`, `#123`) in prose — all wrapped in backticks (see [[rules/obsidian-vault-graph-integrity|Obsidian Graph Integrity Standard]])
6. [ ] Chat response references the file
7. [ ] Chat response has ≤5 bullets of summary
8. [ ] No raw tool output in chat

## Exception: Debugging

When actively debugging with user, short error snippets (<5 lines) are OK in chat for real-time iteration. Still write full trace to file.

## Cost Impact

| Behavior | Tokens per Task |
|----------|-----------------|
| Old: Dump everything in chat | ~2000-5000 |
| New: File artifact + 5 bullets | ~200-500 |
| **Savings** | **~80%** |

## Enforcement

Both Hermes and OpenClaw agents MUST follow this.
User can flag violations with "too long" — agent must immediately rewrite as file artifact.
