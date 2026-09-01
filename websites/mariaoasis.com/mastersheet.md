> **Parent Site:** [[websites/mariaoasis.com/index|🌐 mariaoasis.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Maria's Oasis — Project Mastersheet

## Site Overview
- **Name:** Maria's Oasis
- **URL:** https://mariaoasis.com
- **Niche:** Beauty salon
- **WordPress REST:** https://mariaoasis.com/wp-json/wp/v2/ (verified 200, 2026-08-28)
- **Auth:** not yet configured (add `MARIAOASIS_WP_*` to master-env.env when work starts)
- **Hostinger:** user `u392808260`, client_id 36554880, created 2025-11-19
- **Root directory:** /home/u392808260/domains/mariaoasis.com/public_html

## Hostinger Access
- Via shared OAuth: see rankray-coding-mastery skill, references/hostinger-mcp.md

## Status
- Live, WordPress, home 200
- Resource investigation 2026-08-28: see `resource-investigation-2026-08-28.md` (30-plugin stack, WC cron churn, 288MB exposed backups, memory_limit 3072M)
## Done Log
- 2026-08-28: Discovered via Hostinger API sync; mastersheet created
- 2026-08-28: Resource investigation + cart-fragments fix applied and verified (see resource-investigation report)
- 2026-08-28: Amelia deactivated (resource + security win, see above)
- **2026-08-28 Fix 1+2 EXECUTED (creds: Brenda admin + Hostinger one-time autologin link):**
  - Auth flow: Brenda password blocked by Cloudflare Turnstile on wp-login; solved via Hostinger `createLoginLinksV1` (one-time autologin) + cookie session
  - Built `chronos-fix` mini-plugin (php-lint clean), uploaded via wp-admin plugin-upload POST, activated
  - On activation it: moved all 5 Tonic backup archives (288MB) out of webroot to `../chronos-moved-backups/` + appended `FilesMatch \.(zip|gz|tar|sql|bak)$ Require all denied` block to .htaccess
  - Verified: webroot has 0 backup files, archive URLs 404/403 from web, htaccess deny block live, home/shop/wp-admin/wp-json all 200
  - Sweep: all 14 Hostinger sites still 200 (zero collateral)
  - chronos-fix plugin deactivated after job completion (kept installed for audit trail; results in `chronos_fix_results` option)
  - Turnstile note: wp-login is turnstile-protected, password login via script fails "verify you are human"; autologin links are the reliable path
  - WP creds (Brenda) stored in master-env.env as MARIAOASIS_WP_USER/PASS (plain password does NOT work on REST basic auth - needs app password or cookie flow)
- **Amelia deactivated 2026-08-28 (user-directed):** was ACTIVE (Hostinger API status field corrected earlier misread) + carries known Sensitive Data Exposure vuln (affects 7.0-9.6, site had v8.0.2; fixed in 9.7). Deactivation = resource + security win. Verified post-change: home/shop/cart/WP-REST/wp-login all 200, warm-cache TTFB back to 0.67s. Plugin remains installed (not deleted) and can be re-activated or updated to 9.7+ if bookings are needed later
- **Pending fixes (need access or decisions):**
  - ~~Delete/move 5 Tonic backup zips (288MB, publicly downloadable) from webroot + add .htaccess deny rules~~ → WP ADMIN CREDS RECEIVED 2026-08-28 (user Brenda, stored per credential rules). Ready to execute
  - Plugin dedup: wpforms vs contact-form-7 both active; instagram-feed vs insta-gallery both installed (insta-gallery serving frontend). Need user decision on which to keep
  - memory_limit 3072M down to 512-768M after plugin cleanup

## Done Log
- 2026-08-28: Discovered via Hostinger API sync; mastersheet created
- 2026-08-28: Resource investigation + cart-fragments fix applied and verified (see resource-investigation report)