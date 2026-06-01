# MEMORY.md

## Human
- **Name:** Sheikh Own; call him Sheikh / Own. Asia/Karachi timezone. Discord: sheikhown#0000.
- **Projects:** Rank Ray (SEO), CoinSfera (crypto OTC), TeamMotorcycle (content), Tonic Physio (clinic).
- **Preferences:** No paid APIs in cron jobs; prefer free/self-hosted tools and built-in OpenClaw utilities.

## Content Workflow Rule (CRITICAL)
- Before generating ANY blog/content ideas for a client site, check `websites/{domain}/site-index/` for existing content and topic gaps.
- NEVER suggest topics already covered. Create site-index first if it doesn't exist.

**Project Folders:** `projects/rankray-hq/`, `websites/tonicphysio.com/`, `websites/teammotorcycle.com/`, `websites/coinsfera.com/`

## Decisions & Notes
- Workspace: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/ai brain`
- Ai Brain root: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain`
- Credentials folder: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/credentials/google-oauth/`
- MEMORY.md created 2026-05-27 after removing Apify-dependent cron jobs (gmb-uk, usa, canada, uae, australia).
- Remaining crons referencing Serper still need fixing: `position-tracker`, `gsc-opportunity-scan`.

## Google OAuth — oliverjakeseo@gmail.com (CANONICAL)
- **Credentials:** `~/Ai Works - Local/Ai Codes/Ai Brain/credentials/google-oauth/oliverjakeseo@gmail.com-oauth-credentials.json`
- **Token:** `~/Ai Works - Local/Ai Codes/Ai Brain/credentials/google-oauth/oliverjakeseo@gmail.com-oauth-token.json`
- **Project:** `openclaw-rank-ray-automation` | **Client ID:** `803355012183-bfgbc7g540isfs1pkno6f3fknb135cqb.apps.googleusercontent.com`
- **Status:** ⏳ Awaiting initial auth (token placeholder created 2026-06-01)
- **Symlinks:** `~/.config/gcp/oauth-credentials.json` and `~/.config/gcp/token.json`
- **Skill:** `google-workspace-master` at `~/.openclaw/skills/google-workspace-master/`
- **Scripts:** `reauth_gmail.py`, `weekly_seo_report_emailer.py`
- **Scopes:** Gmail, Sheets, Drive, Docs, Calendar, Contacts, Tasks, GSC, GA4, Photos, YouTube

## Daily Lead Email Drafter
- **Cron ID:** `acd61434-cebe-49fa-a7e0-c8d5aeb49483`
- **Purpose:** Daily pipeline for Rank Ray outbound sales email drafting
- **Spreadsheet:** `Rank Ray Lead Tracker` (ID: `11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4`)
- **Credentials:** `~/.config/google-sheets/credentials.json` (service account — SEPARATE from OAuth)
- **Script:** `~/Ai Works - Local/Ai Codes/Ai Brain/ai brain/scripts/lead_email_drafter.py`
- **Config:** `~/Ai Works - Local/Ai Codes/Ai Brain/ai brain/scripts/spreadsheet_config.json`
- **Status:** ✅ Working - tested on 2026-05-30, processed 403 leads, drafted 382 emails
- **Issue:** Google Sheets API rate limits (429 error) - drafts saved locally as fallback
- **Report Channel:** #claw-emailer
- **Skill Created:** `rankray-email-drafter` in workspace skills directory
