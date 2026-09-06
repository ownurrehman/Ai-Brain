> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Framework portability — cloning this theme to new ecommerce sites

**Purpose.** The `justccell-theme` is the **reusable ecommerce framework** for this client's stores. The next site is **eliteterpenez.com** (Hostinger `u984013785` / WP `30437919`), and 2–3 more WooCommerce sites will follow. This guide is the law for keeping the framework clone-safe: **design + logic are shared; per-site identity is config, never hardcode.** Read this before starting any clone or before adding anything that could make a page non-portable.

Related: [[websites/justccell.com/rules|rules.md]] (ACF §, §7.5), [[websites/justccell.com/features-code-map|features-code-map.md]], [[websites/justccell.com/docs/STATUS|STATUS.md]].

---

## 1. The three portability laws (never break)

1. **Bind ACF field groups to the page TEMPLATE, not the page slug.** Location rule = `Post Template is equal to page-templates/<file>.php`. Slugs are client-editable and site-specific; templates are shared code. This is why the `/ccell-3-0/` rename broke the old build (see §7.5) — do not repeat it. Only exception on live today: `group_jc_laser_page` (documented in features-code-map — shares the brand template, appears on one page).
2. **Site identity is a filter or an options-page value, never a literal in PHP/CSS/JS.** Brand name, canonical slugs, titles, phone/email, colours, and cross-site endpoints must be overridable per clone. Fallback literals in code are allowed only as the empty-state default (rules §1).
3. **Every clone keeps its own AI-brain folder in sync (rules §0.6).** `websites/<site>/` gets its own `rules.md` / `STATUS.md` / `BUILD-LOG.md` / `features-code-map.md`. Never let one site's docs describe another.

---

## 2. What is already portable (built right)

- **Template-bound ACF** — all 7 page groups (about, why, legal, locations, brand, bio, discover) bind to `page-templates/*.php` (0.9.297). Clone a page → assign the template → fields appear.
- **Canonical bio page** — slug/title come from `justccell_bio_canonical_slug()` / `justccell_bio_canonical_title()` in `inc/page-layouts.php`, both `apply_filters`-able. A clone overrides them without touching core.
- **Options-page glue** — sitewide links, socials, phone/email, cross-sell settings live on **Justccell → Storefront / Elite Cross-sell** (ACF options), not in templates.
- **WooCommerce-native commerce** — catalog is Woo-only (`justccell_catalog_from_woo()`), tier pricing, cart drawer, PDP ladder are data-driven. No per-site product hardcoding.
- **Local JSON + GUI ACF** — no PHP field arrays; a clone inherits the same field schema from `acf-json/` and the client sorts in the GUI.

---

## 3. What must be overridden per clone (the config surface)

| Concern | Where it lives now | How a clone overrides it |
|---|---|---|
| Bio page slug / title | `justccell_bio_canonical_slug()` / `_title()` | Filter `justccell_bio_canonical_slug` / `justccell_bio_canonical_title` |
| Brand display name | `__('...', 'justccell')` literals + options | Prefer an options-page "Brand name" field read by a helper; filter where a literal remains |
| Phone / email / socials | **Justccell → Storefront** options page | Re-enter per site in wp-admin (data, not code) |
| Colours / logo / fonts | `style.css` tokens + Media Library | Per-site CSS custom-property block + own Media assets |
| Legacy redirect map | `inc/catalog-redirects.php` | Site-specific; prune Justccell-only legacy paths on the clone |
| Cross-site coupon bridge | `inc/elite-cross-sell.php` (JC→Elite) | Different endpoint/account per pair; never ship JC secrets to a clone |
| SEO footprint rule | rules §10 (zero `ccell.com`) | Each clone gets its own "zero source-site footprint" rule |

---

## 4. Known portability debt (fix before / during first clone)

- **Function + text-domain prefix `justccell_` / `'justccell'`** is baked across `inc/*.php`. Two options:
  1. **Keep the prefix** (fastest): the code prefix is invisible to visitors; override only *display* strings via filters/options. Recommended for the first 1–2 clones to save time (the client's stated priority).
  2. **Rebrand the prefix** (cleanest long-term): scripted rename `justccell_` → `<site>_` and text-domain swap when the framework stabilises. Do this once, deliberately, not per small clone.
- **Remaining hardcoded brand strings** — audit `inc/static-pages.php`, `inc/header-menu.php`, `inc/nav-fallback.php`, `template-parts/**` for literal "Justccell" copy that should be options/filters before a clone reuses them.
- **`group_jc_laser_page` slug exception** — if a clone needs the laser page on a different slug, add a dedicated `page-templates/justccell-laser.php` and bind the group to it (removing the slug rule) rather than copying the exception.
- **Redirect map** — `inc/catalog-redirects.php` contains Justccell-specific legacy URLs; a clone should start from an empty/site-specific map.

---

## 5. Clone procedure (high level)

1. **New AI-brain folder** `websites/<site>/` with its own `rules.md` (copy + adapt: brand, account IDs, footprint rule), `STATUS.md`, `BUILD-LOG.md`, `features-code-map.md`, `INDEX.md` (parent-hub breadcrumb + linked in the websites directory).
2. **Copy the theme** to the clone's local source `websites/<site>/<site>-theme/`. Decide prefix strategy (§4). Update `style.css` header (Theme Name/Author) and the version constant name if rebranding.
3. **Set site identity** via filters in a small `inc/site-config.php` (bio slug/title, brand name) + fill the **Storefront** options page in wp-admin. Do **not** edit templates for identity.
4. **Assign templates** to each page (About/Why/Legal/Locations/Brand/Bio/Discover) so the template-bound ACF groups load. Never re-add slug rules.
5. **Own Media + Woo catalog** — upload the clone's own images (Media Library only, rules §2) and products; no shared assets, no source-site footprint.
6. **Deploy in place** to the clone's Hostinger account per that site's auto-deploy rule (Elite = `u984013785`). Never patch one site's files onto another's host.
7. **Sync docs same turn** (rules §0.6) — log the clone bring-up in the clone's `BUILD-LOG.md`.

---

## 6. Anti-patterns (these caused real bugs — do not repeat)

- Slug-bound ACF groups → fields vanish on rename (the `/ccell-3-0/` incident).
- Hardcoded brand copy / URLs in templates → every clone needs code edits and drifts.
- One force-revert path the docs didn't know about → code silently fights the client's manual change. Search for **all** seeders/canonicalizers/redirects before declaring a slug or title "done".
- Editing one site's files on another site's Hostinger account → cross-contamination. Respect per-site account IDs.
