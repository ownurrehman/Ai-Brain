# Rank Ray WordPress Login Guide

Purpose: keep future OpenClaw runs from wasting time on Rank Ray wp-admin login.

## Working facts
- Site: https://www.rankray.com
- Login page: https://www.rankray.com/wp-login.php
- Admin page: https://www.rankray.com/wp-admin/
- Valid username observed in the working browser session: `openclaw`
- Valid password observed in the working browser session: stored in the secure environment used for the session, not repeated here
- A separate browser context may still land on wp-login even after a successful login elsewhere
- The persistent browser profile is the key to keeping the session alive

## What actually worked
1. Opened wp-login in Playwright.
2. Signed in with the working credentials.
3. Reused the same browser context/profile.
4. Confirmed wp-admin loaded with the full WordPress admin menu.

## What failed before that
- Opening wp-admin in a fresh browser context.
- Assuming a login in one browser profile would persist in another.
- Trying to use the wrong local skill path for WordPress.
- Treating a successful human login in a different browser as reusable without saving session state.

## Best practice for future agents
- Always check whether the browser context is persistent before starting login work.
- If wp-admin redirects to wp-login, treat it as a session-state problem first.
- Do not ask the human to re-login unless the persistent state is genuinely missing.
- After a successful login, save browser storage state or use a persistent profile directory.
- Reuse the same profile for daily WordPress tasks.

## Recommended automation setup
### Option A: Persistent browser profile
Use one dedicated profile folder for Rank Ray.
Example:
- `.pw-rankray/`

Launch with Playwright persistent context and keep using it for all wp-admin work.

### Option B: Storage state file
After the first successful login:
- export browser state to a file such as `.browser-profiles/rankray-state.json`
- reload that state for future sessions

### Option C: Fallback login script
If the session is missing, run a small login check script that:
- opens wp-login
- fills username/password
- submits the form
- verifies wp-admin loads
- saves a screenshot and exits with a clear status

## Common failure signs
- Redirect loop back to wp-login
- WordPress error: incorrect username or password
- Browser profile does not match the one used during login
- Cookies or storage are not being reused

## Example login check script
Use this pattern when testing login state:
```js
const { chromium } = require('playwright');

(async() => {
  const context = await chromium.launchPersistentContext('/Users/sheikhown/.openclaw/workspace/.pw-rankray', {
    headless: false,
    viewport: { width: 1440, height: 1000 }
  });
  const page = context.pages()[0] || await context.newPage();
  await page.goto('https://www.rankray.com/wp-admin/', { waitUntil: 'domcontentloaded' });
  if (page.url().includes('wp-login.php')) {
    console.log('Not authenticated');
  } else {
    console.log('Authenticated');
  }
  await context.close();
})();
```

## Notes for Rank Ray work
- Use wp-admin for normal editing, ACF, Yoast, and site admin work.
- Use REST API where it is enough.
- Use the persistent profile for all daily runs.
- If login breaks, fix the profile first before debugging content or page work.

## Short rule for future agents
If Rank Ray wp-admin redirects to login, do not restart from scratch. Reuse the persistent browser profile, verify the session, and only then continue work.
