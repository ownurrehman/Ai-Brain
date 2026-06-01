# Ai Brain — Master Index

## Credential Storage

**Canonical location for ALL sensitive credentials:**

```
~/Ai Works - Local/Ai Codes/Ai Brain/credentials/
```

### google-oauth/
- `oliverjakeseo@gmail.com-oauth-credentials.json` — Google OAuth client credentials
- `oliverjakeseo@gmail.com-oauth-token.json` — Live OAuth tokens (auto-refreshed)
- **Email:** oliverjakeseo@gmail.com
- **Project:** openclaw-rank-ray-automation
- **Client ID:** 803355012183-bfgbc7g540isfs1pkno6f3fknb135cqb.apps.googleusercontent.com
- **Symlinks:** `~/.config/gcp/oauth-credentials.json` → canonical
- **Symlinks:** `~/.config/gcp/token.json` → canonical
- **DO NOT MOVE OR DUPLICATE THESE FILES**

### google-sheets/
- `credentials.json` — Service account for Google Sheets API (separate from OAuth)
- **Service Account:** rank-ray-sheets-bot-80@openclaw-rank-ray-automation.iam.gserviceaccount.com

## Key Websites
- `websites/rankray.com/` — Rank Ray Marketing Website
- `websites/tonicphysio.com/` — Tonic Physio Clinic
- `websites/coinsfera.com/` — CoinSfera OTC Exchange
- `websites/teammotorcycle.com/` — Team Motorcycle
- `websites/backlinkscrypto.com/` — Backlinkscrypto
- `websites/own-ur-rehman.com/` — Personal Website
- `websites/seoengineai.com/` — SEO Engine AI
- `websites/outreach/` — Outreach landing pages
- `websites/archive/` — Archived websites

## Key Projects
- `openclaw/` — Main OpenClaw workspace
- `projects/rank-ray-hq/` — Rank Ray HQ
- `projects/archives/` — Archived projects

## Skills
- `~/.openclaw/skills/` — OpenClaw skills
- `~/.openclaw/skills/google-workspace-master/` — Full Google Workspace automation

## Core Scripts (System Utilities)
- `scripts/google-oauth-manager.py` — Unified Google OAuth CLI (URL generation, PKCE exchange, manual renewal, status)
- `scripts/subagent-manager.py` — Transient subagent delegation orchestrator (SDP)
- `scripts/agent-ledger.py` — Distributed Agent Transaction Ledger (DATL) for log audits
- `scripts/mac-health-check.sh` — Local system stats checking script
- `scripts/docker-cleanup.sh` — Maintenance/Docker weekly cleanup shell utility
- `scripts/graphify-watch.sh` — Graphify monitor script

## Swarm Singularity & Symlinks

**Canonical Swarm Layer Configuration:**
- To ensure a single "source of truth" and prevent document duplication, the `/openclaw` workspace directory uses relative symlinks pointing directly back to root master files.
- **Active Symlinks:**
  - `openclaw/AGENTS.md` ➔ `../AGENTS.md`
  - `openclaw/IDENTITY.md` ➔ `../IDENTITY.md`
  - `openclaw/MEMORY.md` ➔ `../MEMORY.md`
  - `openclaw/SOUL.md` ➔ `../SOUL.md`
  - `openclaw/TOOLS.md` ➔ `../TOOLS.md`
  - `openclaw/USER.md` ➔ `../USER.md`
  - `openclaw/mastersheet.md` ➔ `../mastersheet.md`
  - `openclaw/memory` ➔ `../memory`
  - `openclaw/projects/rankray/email-drafts` ➔ `../../../websites/rankray.com/email-drafts`
- **WARNING TO ALL AGENTS:** Do not sever or delete these symlinks. Always write to the symlink target or root equivalent.
