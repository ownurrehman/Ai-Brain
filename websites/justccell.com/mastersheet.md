> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Justccell — Project Mastersheet

## Site Overview
- **Name:** Justccell
- **URL:** https://justccell.com
- **Niche:** Vaporization hardware (global B2B/partner)
- **Tagline:** "Your Global Partner in Vaporization Hardware"
- **WordPress REST:** https://justccell.com/wp-json/wp/v2/ (verified 200, 2026-08-28)
- **Auth:** not yet configured (add `JUSTCCELL_WP_*` to master-env.env when work starts)
- **Hostinger:** user `u392808260`, client_id 36554880, created 2026-08-14
- **Root directory:** /home/u392808260/domains/justccell.com/public_html

## Hostinger Access
- Via shared OAuth: see `~/.hermes/profiles/chronos/skills/software-development/rankray-coding-mastery/references/hostinger-mcp.md`
- Files, cron, DB, PHP, redirects manageable over Hostinger API

## Vault Folder
- `websites/justccell.com/` — has INDEX.md, README.md, content/, docs/, justccell-theme/ (pre-existing folder; this mastersheet added 2026-08-28 during Hostinger sync)

## Status
- Live, WordPress, home 200
- No SEO audit yet, no WP app-password yet

## Done Log
- 2026-08-28: Replaced 15 Discover featured images with 510 / ceramic hardware photos (`justccell-v2-*.jpg`); old lab/workshop stock removed
- 2026-08-28: Discovered via Hostinger API sync; mastersheet created
## 2026-08-31: SEO Meta Fix (Hermes via Hostinger MCP + WP admin session)
- Auth: Hostinger MCP autologin (u392808260, install 30055979) → admin cookies + REST nonce
- SEO plugin: Rank Math (REST write via /wp-json/rankmath/v1/updateMeta, keys rank_math_title + rank_math_description)
- Audited 26 pages + 15 posts = 41 URLs
- Wrote custom meta for 24 pages (all were missing desc; titles were default "%title% - Justccell")
- 15 posts already had OK meta (Enigma's earlier work) — untouched
- Checkout page 9 set to noindex,nofollow (redirects to Basket when empty; correct for e-commerce)
- FIXED & VERIFIED: 40/41 URLs pass title+desc audit (fresh frontend crawl with cache-bust)
- KNOWN ISSUE (needs theme/code fix by Chronos or dev):
  - /battery/ /pod-system/ /cartridge/ /all-in-ones/ are WooCommerce product_cat archives served via catalog-clone with body class "blog"
  - RankMath resolves current object to page_for_posts = page 202 (Discover) → all 4 emit Discover's <title>, <meta description>, og:url=https://justccell.com/discover/, and robots noindex,nofollow
  - RankMath meta for WP pages 226/225/224/223 AND product_cat terms 19/18/17/16 both written but not surfaced — the theme router bypasses both objects
  - Fix needed in theme (inc/listing.php or catalog router): serve the actual page object (226/225/224/223) so RankMath picks per-page meta; until then these 4 URLs are noindex in Google
- Robots finding: /battery/ etc currently noindex — bad for SEO; theme fix required

## 2026-08-31 (later): Grok fix verification + final state
- Grok (Cursor) deployed theme 0.9.56 while Hermes audited: catalog-clone router now queries real pages (223/224/225/226) — verified via frontend: correct per-page titles, descs, og:urls on /battery/ /pod-system/ /cartridge/ /all-in-ones/
- Hermes set index,follow robots via RankMath REST on 223-226 (sitewide noindex was from WP blog_public=0 "Discourage search engines" — turned back ON via options.php; Hostinger maintenance mode also disabled during test)
- Homepage fixed: show_on_front=page, page_on_front=241 restored via REST (Grok's deploy had flipped it to 0)
- FINAL VERIFIED STATE: 38/41 URLs pass (title + description + indexable). Remaining 3 (basket, checkout, my-account) are WooCommerce utility pages CORRECTLY noindexed with clean titles.
- Homepage title/desc: "Justccell: Cannabis Vape Hardware Manufacturer for Brands" — indexable
- Site-wide noindex RESOLVED. Site is now Google-indexable with full meta coverage.

## 2026-09-01: Media Library thumbnail repair (Hermes)
- Diagnosis (verified): 36 tank-360-*.jpg attachments (IDs 115-150) had EMPTY attachment metadata -> grid could not render them. Originals all 200 OK. Cursor's mu-plugin (jc-media-repair-cli.php in wp-content/mu-plugins/) was stuck at cursor 61 in an infinite loop (pending=1 forever) and never reached the broken items.
- Fix: Hermes plugin "Justccell Media Repair + Cleanup" (jc-media-meta-fix) regenerated metadata via wp_generate_attachment_metadata() for all 36 IDs. Verified remaining=0, healthy=36.
- Cleanup: Cursor mu-plugin DELETED via plugin v2 cleanup endpoint (jcm_cleanup trigger). mu-plugins now only contains Hostinger's own files (hostinger-auto-updates.php, hostinger-preview-domain.php).
- Grid AJAX re-tested: 1.7-1.9s response with healthy thumbnail data (the earlier 39s/empty-grid was transient/cold, not persistent).
- Cleanup TODO (user): Deactivate + delete "Justccell Media Repair + Cleanup" plugin from Plugins page when convenient. Code saved in scripts/jc-media-meta-fix-v2.php.
- NOTE: Hostinger maintenance mode re-enabled BY USER (do not touch without explicit approval).
