# websites/

**Client and marketing websites.** RankRay HQ SaaS is `projects/rankray-hq/` — not here.

**Hostinger truth (synced 2026-08-28):** the Hostinger account (user `u392808260`, client_id 36554880) hosts 15 WordPress sites (14 catalogued below + 1 dummy testing subdomain deliberately not catalogued). Every domain below is live and verified via the Hostinger API + wp-json probe on 2026-08-28. Access: see rankray-coding-mastery skill, `references/hostinger-mcp.md`.

## Hostinger-hosted sites (all live)

| Site | Niche | Vault folder | Mastersheet | WP REST | WP creds |
|------|-------|--------------|-------------|---------|----------|
| `rankray.com` | Agency marketing | `rankray.com/` | yes | 200 | `RANKRAY_WP_*` |
| `tonicphysio.com` | Physiotherapy client | `tonicphysio.com/` | yes | 200 | `TONICPHYSIO_WP_*` |
| `seoengineai.com` | Product domain | `seoengineai.com/` | placeholder | 200 | - |
| `backlinkcrypto.com` | Crypto backlink marketplace | `backlinkcrypto.com/` | yes | 200 | `BACKLINKCRYPTO_WP_*` |
| `own-ur-rehman.com` | Personal domain | `own-ur-rehman.com/` | placeholder | 200 | - |
| `justccell.com` | Vaporizer hardware | `justccell.com/` | yes (new) | 200 | - |
| `gemstonespk.com` | Gemstones PK | `gemstonespk.com/` | yes (new) | 200 | - |
| `impactestatemarketing.com` | Real estate marketing | `impactestatemarketing.com/` | yes (new) | 200 | - |
| `classicshop.pk` | E-commerce PK | `classicshop.pk/` | yes (new) | 200 | - |
| `whiterosepvt.com` | General order supply | `whiterosepvt.com/` | yes (new) | 200 | - |
| `sellcryptoindubai.com` | Crypto OTC Dubai | `sellcryptoindubai.com/` | yes (new) | 200 | - |
| `sellbitcoinindubai.com` | Crypto OTC Dubai | `sellbitcoinindubai.com/` | yes (new) | 200 | - |
| `sellusdtindubai.com` | Crypto OTC Dubai | `sellusdtindubai.com/` | yes (new) | 200 | - |
| `mariaoasis.com` | Beauty salon | `mariaoasis.com/` | yes (new) | 200 | - |

Sites without WP creds yet: add `<SITE>_WP_*` application-password entries to `master-env.env` before WordPress work.

(One Hostinger staging/dummy subdomain exists on the account for testing only — deliberately not catalogued.)

## Non-Hostinger vault entries

| Path | Status | Notes |
|------|--------|-------|
| `coinsfera.com/` | Active | Hosted elsewhere; SSH/FTP creds in `master-env.env` (`COINSFERA_*`) |
| `teammotorcycle.com/` | Active | Mastersheet, audits |
| `outreach/` | Active | Outreach landing pages |
| `archive/` | Archived | `khanllp.com` |

Also in this folder: `keyword-targets.md`, tonicphysio backup dumps (2026-08-16). Cross-site backlink plan: `system/reports/backlink-strategy-2026-08-12.md`.

**Env:** `docs/ENV.md` + `master-env.env` + `credentials/websites/`. Never create `.env.<site>` inside RankRay HQ.

**Hostinger sync method:** `GET https://developers.hostinger.com/api/hosting/v1/websites` with Bearer token from `~/.config/hostinger-mcp/credentials.json`. Re-run when new sites are added to the account.