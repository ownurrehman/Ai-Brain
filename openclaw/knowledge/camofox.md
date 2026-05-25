# CamoFox Browser Tool

**Location:** `~/camofox-tool-backup` (Moved from Ai Brain workspace)

## What It Is
Stealth browser automation tool for bypassing Cloudflare and bot detection. Built on Playwright with fingerprint randomization.

## Key Features
- **Stealth Mode:** Masked fingerprints, randomized viewport
- **Profile Persistence:** Saves cookies/localStorage across sessions
- **VNC Support:** Remote browser viewing via Docker
- **Docker Ready:** Containerized for consistent environments

## Quick Start
```bash
cd ~/camofox-tool-backup
npm install
npx camofox --help
```

## Config
Main config file: `camofox.config.json`
- Profiles stored at: `~/.camofox/profiles/`
- Default port: `9377`

## Usage in Scripts
```javascript
const { camofox } = require('~/camofox-tool-backup');
const browser = await camofox.launch({ headless: false });
```

## Notes
- **Repo:** Originally cloned from `jo-inc/camofox-browser`
- **Size:** ~115MB (mostly node_modules + Docker assets)
- **Status:** Active tool, moved out of Ai Brain to reduce workspace bloat

## Ai Brain History
- Previously located at: `openclaw/camofox-tool/`
- Migrated: May 1, 2026
- Reason: Workspace optimization, keeping only text/config in Ai Brain
