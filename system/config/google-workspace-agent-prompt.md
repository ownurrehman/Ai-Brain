> **Parent Hub:** [[system/INDEX|⚙️ System Infrastructure Hub]] · [[INDEX|🧠 Ai Brain]]

# OpenClaw: Google Workspace Access Prompt

Use this when you need to create, update, or read Google Sheets, Docs, Drive, Gmail, or Calendar.

## 1. Ai Brain is the headquarters
Base path: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/`
All persistent credentials live here. Do NOT store tokens in `~/.hermes/`. That folder only has runtime symlinks.

## 2. Google OAuth credentials (already set up)
- **Token**: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/token.json`
  Account: `oliverjakeseo@gmail.com` — authenticated, auto-refreshes.
- **Client Secret**: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/client_secret.json`

## 3. How to verify auth works
```bash
GSETUP="python ${HERMES_HOME:-$HOME/.hermes}/skills/productivity/google-workspace/scripts/setup.py"
$GSETUP --check
```
Expected: `AUTHENTICATED: Token valid at ...`
If it fails, stop and ask the user. Do NOT try to re-auth yourself.

## 4. How to use Google APIs (Sheets, Gmail, etc.)
Set this shorthand:
```bash
GAPI="python ${HERMES_HOME:-$HOME/.hermes}/skills/productivity/google-workspace/scripts/google_api.py"
```

### Sheets
```bash
# Read
$GAPI sheets get SHEET_ID "Sheet1!A1:D10"

# Write
$GAPI sheets update SHEET_ID "Sheet1!A1:B2" --values '[["Name","Score"],["Alice","95"]]'

# Append rows
$GAPI sheets append SHEET_ID "Sheet1!A:C" --values '[["new","row","data"]]'

# Create a new spreadsheet (via Drive API / direct REST if needed)
```

### Gmail
```bash
# Search
$GAPI gmail search "is:unread" --max 10

# Send
$GAPI gmail send --to user@example.com --subject "Hello" --body "Message text"

# Reply (threaded)
$GAPI gmail reply MESSAGE_ID --body "Thanks, that works."
```

### Calendar
```bash
# List events (next 7 days)
$GAPI calendar list

# Create event
$GAPI calendar create --summary "Meeting" --start 2026-05-07T10:00:00-04:00 --end 2026-05-07T11:00:00-04:00
```

### Drive
```bash
$GAPI drive search "quarterly report" --max 10
```

## 5. Rules
- Always run `--check` before doing anything. Never assume auth is broken.
- Never re-authenticate without user permission. The token auto-refreshes.
- Never store new tokens or secrets outside `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/`.
- If `--check` fails, report the exact error and ask the user to confirm re-auth.

## 6. Env reference (also in master-env.env)
```
GOOGLE_OAUTH_TOKEN_PATH=/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/token.json
GOOGLE_OAUTH_CLIENT_SECRET_PATH=/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/client_secret.json
GOOGLE_OAUTH_ACCOUNT=oliverjakeseo@gmail.com
```
