> **Parent Hub:** [[websites/mariaoasis.com/index|🌐 mariaoasis.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# mariaoasis.com Resource Investigation (2026-08-28)

**Request from Sheikh:** why is mariaoasis.com taking more resources? Checked via Hostinger MCP + public probes. Findings below.

## What was checked (Hostinger MCP, verified live)

1. **Cron jobs (account):** EMPTY. No scheduled jobs causing spikes
2. **PHP config:** PHP 8.2.30, `memory_limit 3072M`, `post_max_size 3072M`, `max_execution_time 480` — very generous, so runaway scripts run LONG before dying (resource sink amplifier, not root cause)
3. **Database inventory:** 20 DBs on account. mariaoasis DB NOT mapped by domain (pre-2026 API limitation; it is one of 13 unassigned DBs). WC log churn confirms active WP-Cron
4. **File tree:** no malware-looking root files; standard WP root + old UpdraftPlus backups

## Findings (ranked by likely resource impact)

### 1. 30 plugins, stacked heavy stack (main cause)
Full plugin list from server files (30 active candidates):
elementor + elementor-pro, woocommerce, ameliabooking, facebook-for-woocommerce, pw-woocommerce-gift-cards, instagram-feed + insta-gallery (quadlayers), wpforms-lite + contact-form-7 + wpcf7-recaptcha (3 form builders!), click-to-chat-for-whatsapp, boxzilla, updraftplus, hostinger + hostinger-reach, seo-by-rank-math, mailchimp-for-wp, wp-mail-smtp, envato-market, kirki, cherie-core (theme), header-footer-elementor, advanced-custom-fields, svg-support, classic-editor, insert-headers-and-footers, one-click-demo-import, simple-cloudflare-turnstile, litespeed-cache, boxzilla

Key hogs:
- **Amelia Booking** (ameliabooking): booking engine, constant admin-ajax polling + its own cron slots; wp-admin/admin-ajax.php POST probe took 2.16s (2-3x slower than average endpoint)
- **Elementor + Pro** homepage has 1548 elementor markers, 300KB HTML (largest page on the account)
- **WooCommerce + wc-admin** scheduler active: wc-logs show 2 log files EVERY day (30 days straight, no gaps) — `wc_logger` + `facebook_for_woocommerce` writing daily = WP-Cron + background processing constantly warm
- **Instagram Feed + Insta Gallery (two IG plugins doing the same job)** pulling external Instagram API; keeps `insta-gallery-logs/token_renewal.log` and `sb-instagram-feed-images/` cache going
- **facebook-for-woocommerce**: active external API sync (daily 23-25KB logs)

### 2. WC + uncacheable endpoints get hit by bots
- `x-litespeed-cache: hit` on homepage (cache works), BUT `wp-json/wc/store/v1/cart`, `?wc-ajax=get_refreshed_fragments`, `wp-admin/admin-ajax.php`, `wp-login.php`, `wp-cron.php` all serve 200 and hit PHP every request (0.7-1.2s TTFB each). Bots hammering these = CPU burn. xmlrpc disabled (good), wp-login exposed (normal)
- `get_refreshed_fragments` present in theme HTML = every page view triggers an uncached admin-ajax call even for guests

### 3. 288MB of UpdraftPlus backup ZIPs in webroot
Root contains 5 Tonic Physio backup archives (uploads.zip 135MB, plugins.zip 99MB, others.zip 27MB, db.gz 16MB, themes.zip 12MB) dated 2025-11-19. Disk churn + imagery of misconfigured backup destination; NOT protected by .htaccess (no zip/gz deny rule found) - publicly downloadable!

### 4. mainwp folder in uploads (unexpected)
`wp-content/uploads/mainwp/` exists (only an index.php, 0 bytes) — MainWP childremnants. Plugin not in current /plugins dir listing, but folder + maybe mu-plugins remain.

## Recommended fixes (ordered)

1. **Remove UpdraftPlus backups from webroot** (move off-web or delete via hPanel; they are public + 288MB), add htaccess deny for *.zip,*.gz
2. **Disable WooCommerce cart fragments for guests** (LiteSpeed Cache > Cache > or plugin `Disable Cart Fragments`) — kills 1 uncached admin-ajax per pageview
3. **Deactivate one of the duplicate plugins:** wpforms OR contact-form-7 (both active), instagram-feed OR insta-gallery (both active)
4. **Check Amelia usage:** if booking not actively used, deactivate (it is the heaviest single plugin present; its admin-ajax ran 3x slower than baseline)
5. **facebook-for-woocommerce:** if the FB shop sync is not used, deactivate (daily log churn = background API work)
6. **PHP hardening:** memory_limit 3072M is enormous for shared hosting; if Hostinger flags resource usage,各异 lower to 512M-768M AFTER fixing plugins (else Amelia breaks first)
7. **LiteSpeed Cache => Cron => enable "wp-cron via server cron"** to stop bot-triggered wp-cron spawns

## Monitoring limits

Hostinger MCP has NO live CPU/RAM/inode metrics tool. For per-site usage graphs, Sheikh should check hPanel → Websites → mariaoasis.com → Resource Usage. This investigation gives the structural causes that will show up there.

## Access used

- MCP tool loop via `npx hostinger-api-mcp` (cron list, PHP details, phpinfo, DB list, file listing, file content)
- Public probes: wp-json namespaces, TTFB timings, cache headers, page HTML analysis