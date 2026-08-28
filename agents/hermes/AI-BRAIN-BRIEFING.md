# Ai Brain Briefing — Hermes Agent Context

**Purpose:** Bridge between Ai Brain and Hermes. Wake up knowing where things actually live.

**Last Updated:** 2026-08-21

---

## What Ai Brain Is

Central hub at `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/`. Used by Hermes, OpenClaw, Cursor, Gemini, Claude.

Hermes memory: `agents/hermes/MEMORY.md` (not `system/memory/`).

**Split:** `websites/rankray.com/` = marketing site. `projects/rankray-hq/` = SaaS. Do not mix.

---

## Boot Sequence (Every Session, Every Task)

1. Read `INDEX.md`
2. Read `docs/ENV.md` then `master-env.env` (never hardcode secrets)
3. Read the target `websites/<domain>/mastersheet.md` or HQ docs
4. Load skills + `rules/content/content-rules.md`
5. After the task: update that site's mastersheet (+ post-registry if content)

---

## Websites

| Site | Mastersheet |
|------|-------------|
| rankray.com | `websites/rankray.com/mastersheet.md` |
| tonicphysio.com | `websites/tonicphysio.com/mastersheet.md` |
| teammotorcycle.com | `websites/teammotorcycle.com/mastersheet.md` |
| coinsfera.com | `websites/coinsfera.com/mastersheet.md` |
| backlinkcrypto.com | `websites/backlinkcrypto.com/mastersheet.md` |
| khanllp.com | `websites/archive/khanllp.com/mastersheet.md` (archived) |

## Internal projects

| Project | Path | Notes |
|---------|------|--------|
| RankRay HQ | `projects/rankray-hq/` | SaaS. Not rankray.com. |
| RankRay plugins | `projects/rankray-plugins/` | |
| Legendary Bot | `projects/legendary-bot/` | |
| Lead gen | `projects/lead-generation-system/` | Stub (`output/audits/` only) |

---

## Key files

| File | Path |
|------|------|
| INDEX | `INDEX.md` |
| Env map | `docs/ENV.md` |
| Secrets | `master-env.env` |
| Google OAuth | `credentials/google-oauth/` |
| Site extras | `credentials/websites/` |
| Root audit sheet | `mastersheet.md` |
| Content rules | `rules/content/content-rules.md` |
| WP REST | `rules/access/wordpress-rest-api-setup.md` |
| Hermes memory | `agents/hermes/MEMORY.md` |
| RankRay post registry | `websites/rankray.com/post-registry.md` |
| ACF reference | `websites/rankray.com/knowledge/ACF-SERVICE-PAGE-REFERENCE.md` |

---

## Credentials

See `docs/ENV.md`. There is no `credentials/google-sheets/` folder — Sheets use OAuth (`oliverjakeseo@gmail.com`).

---

## Non-Negotiable Rules

1. ALWAYS read INDEX.md first
2. NEVER delete WP content without approval — trash only
3. NEVER modify/delete credential files except to merge new keys the user asked for
4. Client websites → `websites/<domain>/`. HQ SaaS → `projects/rankray-hq/`
5. No em-dashes, no double dashes, no emojis in published content
6. No FAQ sections (Google deprecated)
7. No "content calendar" — user banned this term
8. Meta descriptions <160 chars
9. Internal links: contextual only
10. Tables max 3 columns
11. Featured images: new from Pexels/Unsplash
12. After EVERY site task: update that mastersheet + post-registry + INDEX.md
13. Rate limiting: 1 WP API call / 2 seconds
14. Hermes = WhatsApp only. Discord = OpenClaw only.
15. ALWAYS load relevant skills before starting any task (marketing-copywriting for outreach, seo-aeo-blog-writer for content, etc.)
16. NEVER write "RankRay" or "rankray" in blog content — always "Rank Ray" with space
17. ALWAYS convert markdown to clean HTML before pushing to WordPress
18. Outreach engine at `system/outreach/` — daily 9 AM sends, every 3h reply checks
19. email-sleuth (`es` command) installed for SMTP email verification
