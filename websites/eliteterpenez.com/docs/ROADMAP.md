> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Project Roadmap — eliteterpenez.com

Strategic execution tracks and milestone definition for **Elite Terpenes** (`eliteterpenez.com`).

---

## Track A: Abstrax Tech Design Clone & Front-End UI

| Milestone | Deliverable | Acceptance Criteria | Status |
|---|---|---|---|
| **A1** | **Theme Scaffolding** | `eliteterpenez-theme` initialized; modular CSS (`globals.css`, `chrome.css`, `home.css`); modern sans typography; zero external CDN dependencies. | 🟡 In Progress |
| **A2** | **Homepage Clone** | Hero section with botanical photography, aroma category cards, lab purity credentials, dual CTA buttons, 100% backend editable. | ⚪ Scheduled |
| **A3** | **Header & Footer Chrome** | Sticky navigation header, mobile slide-out drawer, aroma category dropdown, comprehensive footer with legal links. | ⚪ Scheduled |
| **A4** | **Terpene Product PDP** | Single product layout with aroma notes, sensory radar widget, strain type badges (Indica/Sativa/Hybrid), dominant terpene chips, bottle size variations. | ⚪ Scheduled |
| **A5** | **Zero-Footprint QA** | Audit shipped HTML, CSS, JS, JSON-LD schema, and media filenames for zero occurrences of "Abstrax". | ⚪ Scheduled |

---

## Track B: Cross-Site Free Delivery Synergy

| Milestone | Deliverable | Acceptance Criteria | Status |
|---|---|---|---|
| **B1** | **Inbound Bridge Deployment** | `justccell-coupon-bridge` deployed to `wp-content/plugins/` on Hostinger account `u984013785`. | ✅ Verified |
| **B2** | **REST Key Integration** | REST credentials generated and linked in Justccell settings; automated `JC-{order_id}` coupon creation verified. | ✅ Verified |
| **B3** | **Shipping Zone Configuration** | WooCommerce Free Shipping method with coupon requirement active in Zone 0. | ✅ Verified |
| **B4** | **Magic Link UX** | Incoming `?apply_coupon=...` URLs smoothly persist to customer session without query pollution. | 🟡 In Progress |
| **B5** | **Outbound Generator (Reverse Flow)** | Action Scheduler queues `ET-{order_id}` coupon generation on `justccell.com` upon Elite order completion. | ⚪ Scheduled |

---

## Track C: WooCommerce Catalog & Backend Editability

| Milestone | Deliverable | Acceptance Criteria | Status |
|---|---|---|---|
| **C1** | **Taxonomy Setup** | Categories (`botanical-terpenes`, `live-resin-terpenes`, `cannabis-derived`, `terpene-isolates`) & global attributes (`pa_strain_type`, `pa_aroma_profile`, `pa_dominant_terpenes`). | ⚪ Scheduled |
| **C2** | **Compact ACF Pro Fields** | Field groups configured with tabs, 2-line textareas (`'rows' => 2`), table-layout repeaters, and attachment ID returns. | ⚪ Scheduled |
| **C3** | **100% Backend Content Mapping** | Every headline, body copy string, button text, and destination URL wired to native or ACF fields with empty fallbacks. | ⚪ Scheduled |
| **C4** | **Wholesale B2B Mode** | Toggle between open WooCommerce checkout and wholesale inquiry lead capture modal. | ⚪ Scheduled |

---

## Track D: Live Ops, Performance & Launch

| Milestone | Deliverable | Acceptance Criteria | Status |
|---|---|---|---|
| **D1** | **Hostinger MCP Deploy Pipeline** | In-place file sync via TUS to `wp-content/themes/eliteterpenez-theme/`; version bumping in `functions.php` and `style.css`. | 🟡 In Progress |
| **D2** | **Media Library Only** | All imagery imported into WordPress Media Library; WebP format; stripped EXIF metadata; zero hotlinks. | ⚪ Scheduled |
| **D3** | **Performance & Cache Warming** | LiteSpeed Cache configured; CLS < 0.1, LCP < 2.5s on desktop and mobile. | ⚪ Scheduled |
| **D4** | **Coming Soon Cutover** | Client approval to disable `woocommerce_coming_soon` and launch public storefront. | ⚪ Scheduled |

---

## Definition of Done (Launch Gate)

1. Client can edit all copy and images on every page without touching code.
2. A customer ordering on Justccell receives a valid 48-hour free shipping coupon that applies automatically on Elite Terpenes.
3. The site visually replicates Abstrax Tech's botanical science look with zero mentions or links to Abstrax.
4. LiteSpeed Cache and Cloudflare are active with fast TTFB and zero layout shift.
5. All live architecture changes are reflected in `features-code-map.md`, `rules.md`, and `docs/BUILD-LOG.md`.
