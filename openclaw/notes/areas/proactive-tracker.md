# Proactive Tracker

## Overdue Behaviors
| Behavior | Due Date | Status | Notes |
|----------|----------|--------|-------|
| Lead generation: Fix model errors | 2026-05-07 | ✅ FIXED | All crons now use ollama/kimi-k2.6:cloud |
| SEO audits: Re-enable after user cleanup | 2026-05-07 | ✅ DONE | 7 crons active, models fixed |

## Patterns to Automate (3+ occurrences)
| Pattern | Count | Priority | Automation |
|---------|-------|----------|------------|
| Daily SEO reports | 5+ | HIGH | Already automated (7 cron jobs) |
| Content generation | 3+ | HIGH | Rank Ray Semantic Production cron |
| Lead scoring | 2+ | MEDIUM | Lead Generator cron every 6h |

## Decision Follow-ups (>7 days old)
| Decision | Date | Follow-up | Status |
|----------|------|-----------|--------|
| Switch to kimi-k2.6:cloud | 2026-05-06 | Verify all crons work | IN PROGRESS |
| Remove SEO audit crons | 2026-05-07 | Replaced with fixed versions | ✅ DONE |

## Proactive Ideas Queue
1. **Auto-fix broken links** — weekly scan + auto-repair on all 5 sites
2. **Competitor alert system** — daily SERP position monitoring
3. **Content freshness checker** — flag posts >6 months old for update
4. **Schema markup validator** — weekly check for all sites
5. **Internal linking optimizer** — auto-suggest new links based on KWs

## Anti-Drift Check
- Stability: ✅ All cron jobs use allowed model
- Explainability: ✅ Each cron has clear goal-driven verification
- Reusability: ✅ Same pattern across all SEO audits
- Scalability: ⚠️ Add more sites? Need template
- Novelty: ⚠️ Proactive ideas above need human approval
