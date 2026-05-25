# Browser Ops

Persistent browser operations for WordPress, Shopify, and custom admin sites.

## Architecture
- Core: Playwright persistent Chromium profiles
- Fallback: CDP attach to an already-open logged-in Chrome/Chromium session
- Scope: safe admin work where REST is insufficient: ACF fields, plugin UI, Yoast fields, backend settings, audits, technical fixes

## Per-site profile model
Each site gets its own profile folder and config.
This avoids cookie/session collisions across sites.

## Initial commands
- `node browser-ops/scripts/open-profile.js rankray`
- `node browser-ops/scripts/check-auth.js rankray`
- `node browser-ops/scripts/open-admin.js rankray`
- `node browser-ops/scripts/cdp-attach.js rankray`

## Workflow
1. Open persistent profile
2. User completes login/challenge once if needed
3. Verify auth in same profile
4. Reuse profile for future admin tasks
5. Use CDP attach if manual browser session is already open and trusted

## Notes
- Do not bypass Cloudflare, Turnstile, or Wordfence
- Prefer persistent sessions and trusted login reuse
- Keep one profile per site/account group
