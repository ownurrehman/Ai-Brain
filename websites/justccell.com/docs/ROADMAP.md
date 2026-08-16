# Roadmap

Work is ordered so client requirements (geo, VAT, domains, ownership, security) are **designed in**, not patched after 40 cloned pages.

## Now — track A: design clone (visible to client)

| # | Item | Requirement it serves |
|---|---|---|
| A1 | Homepage clone live | Visual approval |
| A2 | Single product page (Tank first), then catalog grid | Visual approval — **Tank live in 0.4.6** |
| A3 | About, technology, safety, research, manufacture | Visual approval |
| A4 | Header mega-menu completeness + footer pixel match | Visual approval |
| A5 | Replace CCELL reference images with 3Devices assets | Legal / brand |
| A6 | Quote form fields: B2B/B2C + VAT + country (even before checkout) | 3/6 — **fields in theme 0.3.0**; Woo tax still open |

Keep all theme links prefix-safe (`home_url()`, permalinks).

## Now — track B: control & platform (not visible, still mandatory)

| # | Item | Requirement |
|---|---|---|
| B1 | 3Devices owns Hostinger, Cloudflare, registrar, WP admin, backups | 5/6 |
| B2 | `info@justccell.com` mailbox + `info@3devicescorp.com` forward | 4/6 |
| B3 | Retire second WP on 3devicescorp.com → 301/alias | 1/6 + 4/6 |
| B4 | Activate Wordfence + admin 2FA + backup restore test | 6/6 |
| B5 | Cloudflare cache bypass for account/checkout | 6/6 |

## Next — track C: storefront axes

| # | Item | Requirement |
|---|---|---|
| C1 | MU-plugin: `/{store}/{lang}/` + CF geo + cookies | 1/6 — **`/{store}/` + `?lang=` live in 0.4.0** |
| C2 | Language selector top-right (5 languages) | 1/6 — **UI in 0.3.0**; translations + store prefixes still open |
| C3 | GBP vs EUR by store | 1/6 |
| C4 | WPML Multilingual CMS + WCML — after C1 so `/es/` is never “Spanish language” | 1/6 — **plugins active 2026-08-14**; parameter mode locked in theme 0.4.2; translations/currencies still to configure |
| C5 | hreflang in Rank Math sitemap via WPML SEO (not `<head>`) | SEO — **plugin switch done 2026-08-16**; verify sitemap after coming soon off |

## Then — track D: accounts & tax

| # | Item | Requirement |
|---|---|---|
| D1 | Registration: B2B vs B2C | 3/6 |
| D2 | VIES + Spanish origin tax + OSS rates (accountant CSV) | 3/6 |
| D3 | Invoices / PDF wording | 3/6 |
| D4 | Payment gateway on 3Devices entity | 5/6 + 6/6 |

## Later

- Real checkout vs inquiry-only (client may stay quote-first for B2B; still need tax-correct quotes).
- Domain cutover runbook if CCELL trademark forces a switch to 3devicescorp.com.
- Performance: WebP/AVIF, smaller banners, LCP budget.
- **2/6** whenever the client sends it.

## Done when

A visitor from Spain lands on `/es/es/`, can switch to English on `/es/en/` with EUR, can register as B2B with a validated EU VAT number and see net prices, 3Devices can log into every control plane without us, and 3devicescorp.com is the same storefront.
