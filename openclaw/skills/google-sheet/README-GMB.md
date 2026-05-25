# GMB Lead Finder - Browser Automation Pipeline

## Status: Fixed (2026-05-24)

### What Changed
- **Old system:** Node.js scripts that created fake placeholder leads with `NO WEBSITE`
- **New system:** OpenClaw browser automation directly in cron payloads — no scripts needed

### How It Works
1. Cron triggers at scheduled time (PKT)
2. Agent uses browser tool to open Google Maps
3. Searches for `industry + city` combinations
4. Extracts real business data from page snapshots
5. Identifies businesses WITHOUT website links
6. Writes real leads to Google Sheet via Sheets API
7. Skips duplicates automatically

### Crons (Updated)
| Time (PKT) | Country | ID |
|------------|---------|-----|
| 06:00 | USA | gmb-usa-daily |
| 08:00 | Canada | gmb-canada-daily |
| 10:00 | UAE | gmb-uae-daily |
| 12:00 | Australia | gmb-australia-daily |
| 14:00 | UK | gmb-uk-daily |

### Key Rules
- **NO fake leads** — only real businesses from Google Maps
- **Max 5 leads per run** — quality over quantity
- **Skip duplicates** — check sheet before adding
- **Report CAPTCHAs** — if hit, stop and report
- **Real phone numbers** — every lead must have a phone

### Sheet URL
https://docs.google.com/spreadsheets/d/11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4

### Archive
Old broken scripts moved to `archive/`:
- `gmb-lead-finder-by-country.js` — generated fake placeholder leads
- `gmb-daily-leads.js` — same issue
- `gmb-scraper.js` — broken execSync approach
- `gmb-scraper-v2.js` — Playwright headless (blocked by Google)
- `gmb-scraper-v3.js` — incomplete skill integration

### Troubleshooting
If no leads found for several days:
1. Google may have changed Maps layout → update selectors in cron payload
2. CAPTCHA frequency increased → reduce search rate or use stealth browser profile
3. Sheet full → clean old leads or create new tab
