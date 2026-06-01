# TOOLS.md - Local Notes

Skills define _how_ tools work. This file is for _your_ specifics — the stuff that's unique to your setup.

## What Goes Here

Things like:

- Camera names and locations
- SSH hosts and aliases
- Preferred voices for TTS
- Speaker/room names
- Device nicknames
- Anything environment-specific

## Examples

```markdown
### Cameras

- living-room → Main area, 180° wide angle
- front-door → Entrance, motion-triggered

### SSH

- home-server → 192.168.1.100, user: admin

### TTS

- Preferred voice: "Nova" (warm, slightly British)
- Default speaker: Kitchen HomePod
```

## Why Separate?

Skills are shared. Your setup is yours. Keeping them apart means you can update skills without losing your notes, and share skills without leaking your infrastructure.

---

Add whatever helps you do your job. This is your cheat sheet.

### Google OAuth — oliverjakeseo@gmail.com (CANONICAL)

**⚠️ ALL agents MUST use these exact paths. Never duplicate or move.**

- **Credentials file:** `~/Ai Works - Local/Ai Codes/Ai Brain/credentials/google-oauth/oliverjakeseo@gmail.com-oauth-credentials.json`
- **Token file:** `~/Ai Works - Local/Ai Codes/Ai Brain/credentials/google-oauth/oliverjakeseo@gmail.com-oauth-token.json`
- **Symlinks:** `~/.config/gcp/oauth-credentials.json` → canonical creds
- **Symlinks:** `~/.config/gcp/token.json` → canonical token
- **Google Cloud Project:** `ai-brain-rank-ray-automation`
- **Client ID:** `803355012183-bfgbc7g540isfs1pkno6f3fknb135cqb.apps.googleusercontent.com`
- **Email:** `oliverjakeseo@gmail.com`
- **Scopes:** Gmail (send/read/modify), Sheets, Drive, Docs, Calendar, Contacts, Tasks, GSC, GA4, Photos, YouTube

**Scripts using this auth:**
- `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw/scripts/reauth_gmail.py` — Re-authentication browser flow
- `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/rankray.com/scripts/weekly_seo_report_emailer.py` — Weekly SEO email sender

**Skill:** `google-workspace-master` at `~/.openclaw/skills/google-workspace-master/`

### Google Sheets (Service Account — separate from OAuth)

- **Rank Ray Lead Tracker:** ID `11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4`
- **Credentials:** `~/.config/google-sheets/credentials.json`
- **Service Account:** `rank-ray-sheets-bot-80@openclaw-rank-ray-automation.iam.gserviceaccount.com`
- **Script:** `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/rankray.com/scripts/lead_email_drafter.py`

### Rank Ray Automation

- **Daily Lead Email Drafter:** Process A/B grade leads from Google Sheet
- **Output Directory:** `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/rankray.com/email-drafts/`
- **Reports:** `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/rankray.com/audits/`
- **Skill:** `rankray-email-drafter` (workspace skills directory)
