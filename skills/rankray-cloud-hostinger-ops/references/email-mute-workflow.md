# Hostinger Site Email-Noise Mute (15 sites, 2026-09-01)

**Source session:** Email-noise fix. User was drowning in WP auto-update success/fail emails
+ Wordfence alerts from 15 sites hosted on Hostinger. Fixed by patching the active theme's
`functions.php` with a `RANKRAY_EMAIL_MUTE` snippet + killing Wordfence alert emails via
admin-ajax. Documented here so the next "stop the noise" job doesn't rediscover it.

## Two changes per site

### 1) functions.php: kill WP core update emails

Append this PHP to the active theme's `functions.php` (via theme-editor POST or TUS):

```php
// RANKRAY_EMAIL_MUTE — disable WP auto-update notifications (Hostinger noise)
add_filter('auto_plugin_update_send_email', '__return_false');
add_filter('auto_theme_update_send_email', '__return_false');
add_filter('auto_core_update_send_email', '__return_false');
add_filter('send_update_notification_email', '__return_false');
```

The `RANKRAY_EMAIL_MUTE` marker is a detection string — search for it after theme updates
to know whether the snippet was wiped. Theme updates will require re-patching (idempotent).

### 2) Wordfence admin-ajax: kill all alert emails

Wordfence 9 sites have it; 6 do not. REST config endpoint rejects the Authorization header
format, so use admin-ajax + the `wordfence_` action prefix:

```bash
CJ=/tmp/cj_<site>.txt
curl -s -m 30 -b $CJ -c $CJ -L \
  "https://<site>/wp-admin/admin-ajax.php?action=wordfence_saveOptions" \
  -H "X-Requested-With: XMLHttpRequest" \
  --data-urlencode "changes=$(python3 -c '
import json
changes = {
  \"alertOn_update\": False,
  \"alertOn_block\": False,
  \"alertOn_loginLockout\": False,
  \"alertOn_breachLogin\": False,
  \"alertOn_lostPasswdForm\": False,
  \"alertOn_adminLogin\": False,
  \"alertOn_wafDeactivated\": False,
  \"alertOn_scanIssues\": False,
  \"alertOn_newAdminUser\": False,
  \"alertOn_passwordAdminChange\": False,
  \"alertOn_userRolesAdminChange\": False,
  \"alertOn_wordfenceDeactivated\": False,
  \"alertOn_pluginDeactivated\": False,
  \"alertOn_pluginActivated\": False,
  \"alertOn_themesDeactivated\": False,
  \"alertOn_themesActivated\": False,
  \"alertOn_optionsGeneralChange\": False,
  \"alertOn_permalinkChange\": False,
  \"alertOn_postStatusChange\": False,
  \"alertOn_commentSpam\": False,
  \"alertOn_commentApproved\": False,
  \"email_summary_enabled\": False,
}
print(json.dumps(changes))
')"
```

**Verify** by reloading `/wp-admin/?page=WordfenceOptions` and checking that all
`alertOn_*` show false and `email_summary_enabled` is false.

## Autologin single-use gotcha (CRITICAL)

`hosting_createLoginLinksV1` returns an autologin PHP URL that is **single-use**:
calling `curl -I` on it to probe headers consumes the link before the cookie-capturing
call. Always do exactly ONE `curl -c $CJ -L` against the URL.

## How auth reached wp-admin

Path: `hosting_getInstallationJWTTokenV1` returned empty → fallback to
`hosting_createLoginLinksV1` (autologin PHP URL) → `curl -c /tmp/cj_<site>.txt -L`
captured the wp-admin cookies → admin-ajax + theme-editor POSTs work as the logged-in
admin user.

## Sites patched (full list 2026-09-01)

u250652900 (crypto): sellusdtindubai.com 5339387, sellcryptoindubai.com 27167201,
sellbitcoinindubai.com 11130377. u392808260 (everything else): mariaoasis.com 24763441,
rankray.com 24767915, tonicphysio.com 24786500, olive-lapwing-638249.hostingersite.com 27167171,
whiterosepvt.com 28059395, classicshop.pk 28061058, impactestatemarketing.com 28061384,
own-ur-rehman.com 28062575, backlinkcrypto.com 28516569, gemstonespk.com 29116299,
seoengineai.com 29553322, justccell.com 30055979.

Wordfence present: sellusdt, sellcrypto, sellbitcoin, rankray, tonicphysio, whiterosepvt,
classicshop, impactestatemarketing, own-ur-rehman (9 of 15).

Final verification: 9/9 WF sites all alerts OFF + summary OFF, 15/15 front-end HTTP 200.

## Reusable scripts

These were created in `/tmp/` during the session (volatile — re-create if needed):
- `site_mute.py` — full per-site flow (autologin → functions.php patch → WF saveOptions)
- `patch_funcs.py` — functions.php patch only
- `hmcp.py` / `hmcp2.py` / `hwcp.py` — Hostinger MCP stdio JSON-RPC drivers

## Theme-editor POST gotcha

`curl -F` file-upload syntax fails on the theme editor form. Use Python urllib with
`urllib.parse.urlencode` + the form's nonce from the GET response, or `--form-string`.

**Active theme discovery fallback** when `theme-editor.php` returns "The requested theme
does not exist": grep the homepage HTML for the active theme directory in
`/wp-content/themes/<name>/` (e.g. `wp-content/themes/twentytwentyfive/`).
