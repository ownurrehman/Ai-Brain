> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Dev environment — dev.justccell.com (deprecated)

> **Deprecated 2026-09-07:** `dev.justccell.com` is **permanently retired**. All work deploys to **https://justccell.com/** only (Master Sync · `rules.md` §0.10). This doc is kept for historical Hostinger paths only — do not deploy or QA on dev.

**Policy (superseded):** Production-only until v1.0.0 go-live. Coming soon + discourage search engines remain on live for client QA.

## URLs & Hostinger

| | Production | Development |
|---|---|---|
| **Public URL** | https://justccell.com/ | https://dev.justccell.com/ |
| **wp-admin** | https://justccell.com/wp-admin/ | https://dev.justccell.com/wp-admin/ |
| **Hostinger user** | `u392808260` | same account |
| **WP software id** | `30055979` | `30476463` (clone 2026-09-06) |
| **Theme version** | **0.9.302** | **0.9.307** (checkout desktop grid fix) |
| **Document root** | `public_html/` | `public_html/dev/` |
| **Theme path (TUS)** | `wp-content/themes/justccell-theme/` | `dev/wp-content/themes/justccell-theme/` |

Cloudflare NS: `eugene.ns.cloudflare.com`, `joyce.ns.cloudflare.com`. Subdomain `dev` → origin `187.124.156.180` (proxied; Flexible SSL on dev only).

## Visibility (both sites — same policy)

Both environments stay **pre-launch**:

1. **Settings → Reading → Discourage search engines** checked (`blog_public=0`).
2. **Minimal Coming Soon & Maintenance Mode** plugin — anonymous visitors see coming soon; logged-in admins see the real site.
3. **Hostinger WordPress maintenance mode** — enabled on both installs (hPanel / API).

Do not disable any of these on either site until go-live is explicitly approved.

## One-time clone (keep dev = prod)

The old dev clone (`WP 30311599`) was removed. Subdomain **`dev.justccell.com`** was recreated (2026-09-06) pointing at `public_html/dev/`.

**Full file + database clone** uses Hostinger hPanel (not available via MCP API):

1. hPanel → **Websites** → **justccell.com** → **Dashboard**.
2. Sidebar → **WordPress** → **Copy website**.
3. **Copy from:** `justccell.com` · **To:** `dev.justccell.com`.
4. Confirm overwrite (dev folder is empty / default page only).
5. Wait until status shows complete (~5–15 min for this DB size).
6. Log in at https://dev.justccell.com/wp-admin/ — same users as production.
7. **Settings → Permalinks → Save** (flush rewrite rules).
8. Confirm **Settings → General** shows `https://dev.justccell.com` for Site URL and Home.
9. Confirm theme version matches production (`JUSTCCELL_VERSION` in `style.css`).

After clone: enable Hostinger maintenance on dev if not already on; verify coming-soon plugin and `blog_public=0`.

## Caching policy (staging off · production on)

| Layer | dev.justccell.com | justccell.com (live) |
|---|---|---|
| **LiteSpeed Cache plugin** | Bypassed (no HTML/CSS/JS cache) | **On** — full page cache + optimisations |
| **Memcached object cache** | **Off** (disable in hPanel after clone) | **On** |
| **Hostinger server cache** | Bypassed via no-cache response headers on dev | **On** |
| **Cloudflare** | Proxied; dev gets fresh HTML because origin sends `no-cache` | **On** — normal CDN caching |

**Why not Hostinger “cacheless mode” on the whole domain?** That toggle in hPanel applies to the **justccell.com** website slot and would also weaken production. Staging cache is instead controlled **per WordPress install** on dev.

### What we ship in code (vault)

1. **Theme** `inc/environment.php` — detects `dev.justccell.com` (or `WP_ENVIRONMENT_TYPE=staging`) and disables LiteSpeed filters + sends `Cache-Control: no-store` on every response. Safe on production (never activates there).
2. **Dev-only mu-plugin** `dev-mu-plugins/justccell-dev-environment.php` — deploy to **`dev/wp-content/mu-plugins/` only** (not production). Loads before LiteSpeed and sets `LSCACHE_NO_CACHE`.

### After Copy Website — dev wp-config (recommended)

Add above `/* That's all, stop editing! */` in **`public_html/dev/wp-config.php`**:

```php
define('WP_ENVIRONMENT_TYPE', 'staging');
```

Optional explicit flag:

```php
define('JUSTCCELL_ENV', 'dev');
```

### After Copy Website — hPanel (dev install only)

1. **WordPress → Overview → Object cache (Memcached)** → **Deactivate** on dev.
2. Leave **LiteSpeed Cache plugin active** on dev — theme + mu-plugin bypass it (easier than mismatched plugin versions).
3. **Do not** enable hPanel **Development mode / cacheless** for `justccell.com` — that hits production too.

Production stays: Memcached **on**, LiteSpeed **on**, Hostinger cache **on**, Cloudflare **on**.

Verify staging: response headers include `X-Justccell-Environment: staging` and `X-LiteSpeed-Cache-Control: no-cache`. Hard-refresh a product page twice — changes should appear without purging cache.

## Deploy workflow (updated 2026-09-06 — dev paused)

| Action | Target |
|---|---|
| Normal coding / QA | **Production** — TUS into `wp-content/themes/justccell-theme/` |
| Client preview | Production (logged in; coming-soon bypass for admins) |
| dev.justccell.com | **Paused** — do not deploy unless user re-enables dev post go-live |

Deploy script: production TUS batches under `websites/justccell.com/_deploy/` (e.g. `tus-prod-*.sh`).

When dev is re-enabled later, restore the dev-first table here and `.cursor/rules/justccell-dev-first.mdc`.

## What to test on dev before promote

- Cart drawer + `/cart/` + `/checkout/`
- FedEx / UPS rate UI (when plugins configured on dev)
- Viva Smart Checkout sandbox
- Laser engraving → cart → checkout meta
- ACF sync after JSON changes
- Elite cross-sell (optional on dev — uses live Elite API; use test mode or disable if needed)

## Sync drift

If production was patched while dev was stale, re-run **Copy website** (dev ← prod) or promote theme files from vault to both paths in one batch.

Reference: [[websites/justccell.com/docs/visibility|visibility.md]] · [[websites/justccell.com/docs/STATUS|STATUS.md]]
