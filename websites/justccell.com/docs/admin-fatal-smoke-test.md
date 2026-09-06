> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Admin edit-screen smoke test (pre/post deploy)

**Why this exists.** On 2026-09-06 a theme change made every wp-admin **edit** screen (all pages *and* products) white-screen with a WordPress critical error, while the front end and the admin post *list* stayed fine. Root cause: a theme filter returned `false` from `acf/load_field_group`, which fatals under WPML's ACFML (see `rules.md` §1 hard law and [BUILD-LOG.md](BUILD-LOG.md) 0.9.303). The old verification only checked front-end pages and field-render counts, so the admin fatal slipped through. **This checklist is the mandatory gate for any change touching ACF, WPML, `admin_init`, `save_post`, field groups, or `functions.php` includes.**

## The 60-second gate (run before AND after every admin/ACF deploy)

1. **Get an admin session** — MCP: `hosting_createLoginLinksV1` (`username u392808260`, `software 30055979`) → open the `login` URL in a browser. No password needed.
2. **Open a PAGE edit screen:** `https://justccell.com/wp-admin/post.php?post=12&action=edit` (Contact us). Must render the full editor — title field, ACF metaboxes, Publish box. A blank/near-blank page = fatal.
3. **Open a PRODUCT edit screen:** `https://justccell.com/wp-admin/post.php?post=331665&action=edit` (GemBox). Must render Product data, tabs, ACF metaboxes.
4. **Open the product LIST:** `https://justccell.com/wp-admin/edit.php?post_type=product`. (Lists can render even when edit screens fatal — that is why you must open an actual **edit** screen, step 2–3, not just the list.)
5. **Read the debug log** for the ACFML signature. MCP: `hosting_getWebsiteFileContentV1` on `wp-content/jc-admin-fatal.log` (or the active Debug Log Manager file). Grep for `must be of type array, false given` and `load_field_group`. Zero new hits = pass.
6. **Check the safety-net log** (`error_log`): a line `[jc-acfml-safety] acf/load_field_group returned ... — restored to prevent ACFML fatal` means the crash was *caught by the net* — the site stayed up, but some code is still illegally returning `false`. **Find and fix that filter**; do not rely on the net.

Pass = steps 2, 3, 4 all render and steps 5, 6 are clean. Any fail → roll back the change, fix, re-run.

## Automatable version (headless)

`hosting_createLoginLinksV1` returns a one-shot auto-login PHP URL. A CI/agent run can `curl -sSL -c jar.txt "<login-url>"` to capture the auth cookies, then request each edit URL with that cookie jar and assert the response is HTTP 200 **and** contains a known editor marker (e.g. `id="titlediv"` for pages, `id="woocommerce-product-data"` for products) and does **not** contain `There has been a critical error` / `must be of type array, false given`. Run for one page ID and one product ID.

## Hard rules this protects

- Never return `false`/non-array from `acf/load_field_group` — use `acf/location/rule_match` or Local JSON location rules (`rules.md` §1).
- Safety net: `plugins/jc-acfml-safety/jc-acfml-safety.php` (active on live, `u392808260`/`30055979`). Keep it active. It is defense-in-depth, not a licence to return `false`.

## Related

- [[websites/justccell.com/docs/BUILD-LOG|BUILD-LOG]] — 0.9.303 ACFML hotfix + this guardrail.
- [[websites/justccell.com/features-code-map|features-code-map]] — safety-net plugin entry.
- [[websites/justccell.com/docs/OPUS-4.8-REPORT-AND-FIXES|Opus 4.8 report]] — full incident writeup.
