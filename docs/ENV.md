# Env & credentials map

**Never paste values into chat, git, or `projects/rankray-hq`.** This file lists *names and locations only*.

## Canonical files

| File | What it holds | Git |
|------|----------------|-----|
| `master-env.env` (vault root) | Agent runtime + WP + Google + API keys | gitignored |
| `credentials/google-oauth/` | OAuth client, token, GA4 ADC | gitignored |
| `credentials/websites/coinsfera.env` | CoinSfera WP / FTP / SSH extras | gitignored |
| `credentials/websites/tonicphysio-wp-cookies.txt` | Local WP admin cookie jar | gitignored |
| `credentials/google-sheets/credentials.json` | Sheets service account (symlink) | gitignored |

RankRay HQ has its **own** app env (`projects/rankray-hq/rankray-hq-backend/.env`). That is the SaaS database/API config — not client WordPress logins.

## WordPress (client sites)

Load from `master-env.env`:

| Site | Folder | Keys |
|------|--------|------|
| rankray.com | `websites/rankray.com/` | `RANKRAY_WP_USER`, `RANKRAY_WP_PASS`, `RANKRAY_WP_APP_PASS` |
| tonicphysio.com | `websites/tonicphysio.com/` | `TONICPHYSIO_WP_URL`, `TONICPHYSIO_WP_USER`, `TONICPHYSIO_WP_PASS`, `TONICPHYSIO_WP_APP_PASS`, `TONICPHYSIO_App_Name` |
| coinsfera.com | `websites/coinsfera.com/` | `COINSFERA_WP_URL` (use **www** REST URL), `COINSFERA_WP_USER`, `COINSFERA_WP_APP_PASS`, plus GSC/GA4/FTP/SSH/`COINSFERA_WP_PATH` |
| backlinkcrypto.com | `websites/backlinkcrypto.com/` | `BACKLINKCRYPTO_WP_URL`, `BACKLINKCRYPTO_WP_USER`, `BACKLINKCRYPTO_WP_APP_PASS` |

CoinSfera extras also live in `credentials/websites/coinsfera.env` (same keys). Prefer `master-env.env` so every agent sees one file.

## Google

| Key / file | Purpose |
|------------|---------|
| `GOOGLE_OAUTH_ACCOUNT` | `oliverjakeseo@gmail.com` |
| `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` | OAuth client |
| `GOOGLE_OAUTH_TOKEN_PATH` | Token JSON (canonical under `credentials/google-oauth/`) |
| `GOOGLE_OAUTH_CLIENT_SECRET_PATH` | Client secret JSON |
| `GOOGLE_OAUTH_SCOPES` | Gmail, Drive, Sheets, GSC, GA4, etc. |
| `credentials/google-oauth/ga-mcp-adc.json` | GA4 MCP (`analytics-mcp`) |
| `GOOGLE_PLACES_API_KEY` | Places |

There is **no** copy of the Sheets JSON inside git. The service account file is:

- `credentials/google-sheets/credentials.json` → symlink to `~/.config/google-sheets/credentials.json`
- Account: `rank-ray-sheets-bot-80@openclaw-rank-ray-automation.iam.gserviceaccount.com`

OAuth (`oliverjakeseo@gmail.com`) is still the account for Drive/Gmail/GSC/GA4 user flows. Sheets automation may use the service account above.

## Other keys in `master-env.env`

Runtime: `TERMINAL_*`, `BROWSER_*`, `HERMES_*`, `CAMOFOX_URL`, `HASS_URL`, `WHATSAPP_*`, debug flags.

Providers: `APIFY_API_KEY`, `NVIDIA_API_KEY`, `OLLAMA_*`, `BRAVE_SEARCH_API_KEY`, `NOTION_API_KEY`, `FIRECRAWL_API_KEY`, `WANDB_API_KEY`, `PEXELS_API_KEY`, `TINKER_API_KEY`, `DISCORD_BOT_TOKEN`, `ZAPIER_MCP_TOKEN`.

## Rules for agents

1. Read `INDEX.md`, then this file, then `master-env.env` (values).
2. Client website work → `websites/<domain>/`. Never `projects/rankray-hq/`.
3. RankRay HQ SaaS work → `projects/rankray-hq/` only.
4. Do not create `.env.<site>` at the HQ repo root.
5. Cookie jars and FTP/SSH passwords stay under `credentials/`.
