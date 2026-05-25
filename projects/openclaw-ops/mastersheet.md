# openclaw-ops Mastersheet

## Project Info
- **Site:** OpenClaw + Hermes Agent Infrastructure
- **Status:** Active
- **Last Updated:** 2026-05-15
- **Primary Contact:** Own

## Agent Cost Optimization Stack (Deployed 2026-05-15)

| Optimization | Status | File |
|-------------|--------|------|
| Hermes boot preloading | Live | `hermes/boot-context.md` |
| Hermes personality | Live | `config.yaml` (ranked persona) |
| Graphify auto-update script | Live | `system/scripts/graphify-watch.sh` |
| Shared memory template | Live | `rules/memory-template.md` |
| File artifact mandate | Live | `rules/file-artifact-mandate.md` |

## Integration Stack

| Tool | Status | Key |
|------|--------|-----|
| Firecrawl | Ready | master-env.env |
| Apify | Verified | master-env.env |
| CamoFox | Configured | localhost:9377 |
| YouTube Transcripts | Upgraded | yt-dlp + youtube-transcript-api |
| Google Workspace | Live | 14 scopes, auto-refresh |

## Notes
- Created during Ai Brain audit 2026-05-07
- Cost optimization stack deployed 2026-05-15
- Both agents now share memory protocol and file artifact rules
