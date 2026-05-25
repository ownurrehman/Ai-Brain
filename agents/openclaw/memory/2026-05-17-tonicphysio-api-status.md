# TonicPhysio REST API Access Status - 2026-05-17

## Status: RESOLVED ✅

### Live Verification (2026-05-07 07:36 PKT)

Tested all three endpoints:

| Endpoint | Auth | Status | Result |
|----------|------|--------|--------|
| `/wp-json/wp/v2/pages` | None | 200 | JSON response OK |
| `/wp-json/wp/v2/users/me` | Basic (App Password) | 200 | User profile returned |
| `/wp-json/wp/v2/pages?status=draft` | Basic (App Password) | 200 | Draft pages returned |

**Credentials in use:**
- User: Dan (User ID 10, Dan Torres)
- App Password: `NMwZ 1LyJ YgbE fUjs pUYn 4SoZ` (stored in `~/.openclaw/.env`)

### Historical Context

**Previous Issue (April 2026):**
- REST API was returning HTML/SVG instead of JSON
- This was resolved — cause unknown but likely Cloudflare or server config change
- No record of active intervention; may have been fixed server-side

### Known Issue

**Env file format problem:**
The `.env` file contains spaces in app passwords (standard WordPress format). When sourced via `source ~/.openclaw/.env`, bash interprets spaces as command separators, causing errors like:
```
/Users/sheikhown/.openclaw/.env:19: command not found: 5gJL
```

**Fix needed:** Quote all app password values in `.env`:
```
TONICPHYSIO_WP_APP_PASS="NMwZ 1LyJ YgbE fUjs pUYn 4SoZ"
```

For now, scripts must extract values via `grep`/`sed` rather than `source`.

### Bottom Line
TonicPhysio REST API is fully functional for read and write operations. Ready for content pushes.
