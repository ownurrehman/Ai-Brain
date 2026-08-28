> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Security

Client: protect the site, customer data, and payment environment from day one. Regular backups.

Payments will go through a PCI-compliant gateway. WordPress must never store card numbers.

## Already in place (2026-08-14)

| Control | State |
|---|---|
| HTTPS | Cloudflare Full (Strict), Always HTTPS, min TLS 1.2, HTTP/2+3, Brotli |
| PHP | 8.3.30, `expose_php` Off, `log_errors` On, `session.cookie_secure` + `httponly`, sodium |
| Caching | LiteSpeed Cache + Memcached |
| Backups | UpdraftPlus installed (destination must become 3Devices-owned) |
| Wordfence | Installed, **inactive** — activate after ownership 2FA so we do not lock the only admin out |
| Rocket Loader | Off (does not fight the theme JS) |
| Page builders | Not used (smaller attack surface) |

## Must ship before storing real customer PII / taking payment

1. **Wordfence** (or equivalent) active: firewall, login lockout, 2FA for administrators.
2. Cloudflare WAF / Bot Fight (Free plan has limits; still enable what we have).
3. Cache **bypass** for `/cart`, `/checkout`, `/my-account`, `?wc-ajax`, nonce URLs.
4. Disable XML-RPC if unused; restrict `wp-login.php` by CF rule or 2FA.
5. Least-privilege WP users; no generic `admin`.
6. Hide WP version / disable file editor (`DISALLOW_FILE_EDIT`).
7. Inquiry form: nonce, honeypot/rate limit, no open redirect; do not log full VAT numbers in public logs.
8. UpdraftPlus to offsite + test a restore once.
9. Hostinger automatic backups confirmed + who can restore.
10. Privacy policy + cookie notice before EU B2C traffic (GDPR). Legal copy is the client’s.

## Hosting notes

- Redis extension is **not** available on this PHP; do not plan Redis object cache.
- Same origin IP is shared with other sites on the account (rankray.com, mariaoasis.com). Compromise of a neighbour vhost is a residual risk; isolate when 3Devices can move to a dedicated environment if they want a higher bar.
- SFTP only; no leftover installer scripts in `public_html`.

## Incident expectation

If the site is defaced or malware appears: Cloudflare “Under Attack”, restore from last known-good backup 3Devices can access, rotate all WP/Hostinger/Cloudflare passwords, notify the client. Do not depend on a developer laptop as the only backup copy.
