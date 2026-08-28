# justccell.com

Single global storefront for **3Devices** (commercial domain: justccell.com, safety-net domain: 3devicescorp.com).

This folder is the project record: client requirements, architecture, build log, and the custom theme source.

| | |
|---|---|
| Live site | https://justccell.com/ |
| Safety-net domain | https://3devicescorp.com/ (must become the same platform, not a second WordPress) |
| **Working theme** | `justccell-theme/` (edit this only) |
| Docs | `docs/` |
| Frozen versions | `archive/theme-releases/` (local snapshots, not extra WordPress themes) |
| Photo seed | `archive/media-seed/photos/` (merged old media packs; import into Media Library) |

Do **not** put numbered `justccell-media-2` folders or `_deploy-theme-0.8.x` copies in this root again.

## Where we are

Read **[docs/STATUS.md](docs/STATUS.md)** first (updated 2026-08-28: Discover editorial, 15 posts). Then:

1. [Client requirements](docs/client-requirements.md) — what 3Devices asked for (sections 1–6; **2/6 = 2026-08-26 merchandising**)
2. [Architecture](docs/architecture.md) — one WordPress, country stores, languages, currencies
3. [Geo, language, currency](docs/geo-language-currency.md)
4. [Translation plugin](docs/translation-plugin.md) — WPML + WCML locked; geo is not the translation plugin’s job
5. [Accounts & VAT](docs/accounts-vat.md)
6. [Domains & email](docs/domains-email.md)
7. [Ownership & control](docs/ownership-control.md)
8. [Security](docs/security.md)
9. [Visibility / coming soon](docs/visibility.md)
10. [Roadmap](docs/ROADMAP.md)
11. [Build log](docs/BUILD-LOG.md) — dated record of what shipped
12. [Discover post registry](docs/post-registry.md) — 15 Guides / News / Blogs articles
13. [Open questions](docs/open-questions.md)

## Rule for this folder

**AI coders:** read **[rules.md](rules.md)** before changing theme or deploy paths.

Every implementation pass updates **BUILD-LOG.md** (what shipped) and **STATUS.md** (current snapshot). Do not leave the live site ahead of the docs.
