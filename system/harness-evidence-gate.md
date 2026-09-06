# Hermes Harness Evidence Gate (Grok-Bot parity)

> Parent: [[agents/FLEET-ORCHESTRATION]] · [[system/fleet-harness-2026-09-06]]

## Why this exists
2026-09-06 RankRay multi-agent "audit" wrote 5 mirrored markdown files and updated INDEX, but content was templated fiction (wrong plugin stack, invented GSC/CWV/DB numbers, same timestamp, no script artifacts). File plumbing is not proof of work.

## Hard rules before Hermes may say "audit complete"

1. **Evidence or it did not happen.** Every numeric claim (rank, CWV, word count, plugin version, DB size) must cite a live source: REST response, PSI URL, GSC export path, or Agentic-SEO-Skill script stdout saved to disk.
2. **SEO audits must use** `Ai Brain/.agents/skills/seo/` scripts (`audit_runner.py`, `robots_checker.py`, `validate_schema.py`, `citation_readiness.py`, `pagespeed.py`, etc.). Save `FULL-AUDIT-REPORT.md` / `ACTION-PLAN.md` / script JSON under `websites/<domain>/reports/` or `system/reports/` with a unique timestamp.
3. **No inventing stacks.** Do not claim Yoast/Elementor/WP Rocket/ACF versions unless read from live WP/plugins REST or confirmed mastersheet. Prefer "unknown — not verified" over guesses.
4. **Specialist attribution requires dispatch proof.** If a report says "Agent: Scout/Enigma/Chronos", the harness DB must show a matching `dispatch` + `complete` with the agent's reply artifact path. Otherwise label the file `hermes-draft` not a specialist audit.
5. **Minimum substance.** Reject reports under ~4 KB of non-boilerplate content OR lacking at least 3 concrete URLs/paths checked. Thin templates fail the gate.
6. **Never claim "no hallucinations"** — that phrase is banned in completion messages. Say what was verified and what was not.
7. **Dangerous SQL / destructive recommendations** stay draft until Own approves. Sandbox-only analyses must not be presented as production-ready runbooks without a backup + dry-run note Own acknowledged.
8. **Grok-Bot parity behaviours to copy:** real subagent/dispatch, stream progress, frontend/live verify before done, one clear scoreboard (PASS/FAIL/BLOCKED), hand Hostinger/theme PHP to Cursor — do not fake those layers in markdown.

## Harness completion scoreboard (required)

| Check | PASS means |
|---|---|
| Skill/scripts run | Exit code 0 + log path |
| Artifacts on disk | Listed absolute paths |
| INDEX/mirror update | Optional; never substitutes for evidence |
| Specialist claims | Matching fleet dispatch IDs |
| Stack/metrics | Sourced or marked unverified |

If any required row is FAIL, status = **INCOMPLETE**, not complete.
