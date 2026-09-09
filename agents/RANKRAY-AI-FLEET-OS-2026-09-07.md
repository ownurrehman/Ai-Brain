# Rank Ray AI Fleet Operating System — 2026-09-07

Orchestrator: **Chronos** (Grok Bot captain). Mac stack: **Hermes** + **Cursor / Antigravity**.

## Intent (Own, 2026-09-07)
- **Daily = do the work.** Agents execute SEO improvements every weekday on the four fleet sites — not reminder-only, not Monday-only.
- **Weekly = plan + track.** Monday produces step-by-step plans and per-agent todos, then a scoreboard of site progress vs last week so Own sees movement.
- **Authorization:** Own authorizes the fleet to work on client + agency sites listed below. Chronos assigns; agents execute. Own still approves spendy, public, or destructive actions. Evidence only — no invented metrics.

## Hard stops / locks
- **justccell.com:** HANDS OFF until Own reopens (price system — Grok 4.6 fixing).
- **Cold email:** OPEN (2026-09-07) — From `oliverjakeseo@gmail.com` only, Reply-To `contact@rankray.com`, max 1/min. Never `rankrayofficial@gmail.com`. No `rankray.com` URLs in cold bodies (SURBL). Forms/LI captcha still parked. Emilia = Oliver/contact replies; Forge owns send lists.
- **Social:** Oliver `oliverjakeseo@gmail.com` accounts only — never Own personal LI/Reddit.
- **Evidence:** Mac GSC/GA via rankrayofficial@gmail.com first; OpenSEO free GSC backup only; Semrush when units available; live frontend. Never invent metrics. Never burn OpenSEO credits for own GSC/GA.
- **Ai Brain first:** `INDEX.md` → site `rules.md` / `mastersheet.md` → `master-env.env`.

## Data plane (Own lock 2026-09-07)
| Source | Role |
|--------|------|
| **Mac + `rankrayofficial@gmail.com`** | **PRIMARY** free first-party GSC + GA4 (Chrome / Hermes / exports). Do this first. |
| **OpenSEO MCP** | **Free-only backup** for GSC when a project is linked. Never burn OpenSEO credits for own GSC/GA. Paid keyword/SERP/backlinks only if Own explicitly asks. |
| **Semrush** | Secondary cross-check when API units available |


### OpenSEO projects (created 2026-09-07)
| Site | Project name | projectId | Default market |
|------|--------------|-----------|----------------|
| coinsfera.com | Default | `b1b7ec48-453a-4dea-83b5-4fe188d0103e` | 2840/en (US) |
| tonicphysio.com | Tonic Physio | `baefbf84-bff9-4591-a207-c7d7bdc5c9a9` | 2124/en (CA) |
| teammotorcycle.com | Team Motorcycle | `c29fa9e2-452c-4973-9f25-311c3e69d6b4` | 2840/en (US) |
| rankray.com | Rank Ray | `21bb688c-5e62-403f-9537-d396a2adb3f7` | 2840/en (US) |

## Sites in continuous SEO scope (authorized to work)
| Site | Focus | Primary markets |
|------|--------|-----------------|
| tonicphysio.com | Local clinic SEO, blogs, YMYL | Milton / Canada |
| teammotorcycle.com | Ecommerce SEO, content | USA |
| coinsfera.com | OTC crypto SEO (careful WAF) | Turkey (Istanbul only) |
| rankray.com | Agency SEO + proof | UAE, US, Canada, Australia, UK |

## Role matrix
| Lane | Owner | Does daily | Weekly artifact |
|------|--------|------------|-----------------|
| Orchestration | **Chronos** | Assign 1 improvement/site focus day; unblock; stream progress | Monday plan + weekly scoreboard to Own |
| Content / on-page / Rank Math | **Enigma** | Ship content/on-page from the plan | Todo list + done/not-done |
| Theme / PHP / Hostinger (non-Justccell) | **Alpha** + **Cursor** | Tech fixes with evidence (one Cursor brief at a time) | Tech backlog burn-down |
| SERP / competitors / keywords | **Scout** | Gap notes + SERP checks for focus site | Competitor/gap delta vs last week |
| BD | **Forge** + **Emilia** | Hunts, referrals, inbox (sends only if unlocked) | Pipeline scoreboard |
| Mac / Hermes bridge | **Mew** + **Hermes** | Mac-only scripts / Discord visibility | Ops blockers |

## Cadence
### Daily (weekdays) — EXECUTE
1. Chronos: today's focus site from rotation; pick **one** high-leverage improvement from the active weekly plan (content OR tech).
2. Dispatch the owning agent with a concrete task + success criteria.
3. Agent does the work, reports evidence + done/not-done.
4. Chronos streams short progress to Own when something real finished or blocked.
5. Forge/Emilia keep BD moving in parallel (not instead of SEO execute).

**Rotation:** Mon Tonic · Tue TeamMotorcycle · Wed Coinsfera · Thu Rank Ray · Fri catch-up / publish / internal links across all.

### Weekly (Monday) — PLAN + TRACK (not “work only on Monday”)
1. Pull OpenSEO GSC/GA (+ Semrush) evidence for all four sites.
2. Write **step-by-step weekly plans** and **per-agent todo lists** for Enigma, Scout, Alpha/Cursor, Forge/Emilia.
3. Publish a **progress scoreboard** vs last week (rankings/CTR/coverage/content shipped/tech closed/BD).
4. Save under `Ai Brain/websites/{site}/reports/YYYYMMDD-weekly/` (fallback `/workspace/fleet/weekly/`).
5. Daily execute then burns that plan Mon–Fri.

## Weekly audit checklist (per site) — for the Monday scoreboard
1. OpenSEO GSC: top queries, losing queries, URL inspect on priority pages
2. OpenSEO GA (if linked): organic landings / key events
3. Domain overview + Semrush cross-check (credit-aware)
4. Frontend / audit: CWV sample, broken links, schema, title/meta
5. Content shipped + internal links
6. Action plan + owners for the coming week

## Success metrics (review with Own)
- Each fleet site: ≥1 real improvement executed most weeks
- Weekly scoreboard shows movement or honest blockers
- Agents work daily off the plan; Monday is planning/tracking, not the only work day
