# Ownership and control

Non-negotiable client requirement: **3Devices is the ultimate owner and administrator**. Developers build; they are never the only people who can log in.

This exists because they previously lost a site after a dispute with an external developer team.

## What 3Devices must own (checklist)

| Asset | 3Devices must have | Developer may have |
|---|---|---|
| Domain registrar (justccell.com, 3devicescorp.com) | Login + 2FA + ability to change NS | View-only or none |
| Cloudflare | Own account, zones transferred in, 2FA | Member role, not Super Admin only |
| Hostinger | Own account (`u392808260` or a new 3Devices account with site moved) | Collaborator |
| WordPress | Administrator user **in their name/email** | Separate `dev-*` admin that they can disable |
| WooCommerce / customers / orders | Same WP; exports they can run | No exclusive access |
| Database | phpMyAdmin / Hostinger DB password in their vault | Same, shared |
| SFTP / SSH | Credentials in their vault; developer keys revocable | Own key, listed |
| Email (info@) | Mailbox they control | Forward only |
| Backups | Offsite destination **they** control (Google Drive / S3 on their billing) | Can trigger backups |
| Payment gateway | Stripe/PayPal on 3Devices legal entity | Dashboard invite |
| SSL | Cloudflare Universal + Hostinger as needed | — |

## Current risk (2026-08-14)

The live WordPress admin email observed during setup was a **Rank Ray Gmail**. Hostinger and Cloudflare were operated from the build session. That is acceptable for a few days of scaffolding. It is **not** acceptable as the steady state.

Until 3Devices has:

1. Their own Hostinger login (or full ownership of this account)
2. Their own Cloudflare login
3. A WP administrator that is not the developer
4. Backup destination they own
5. Registrar login

…the “Indian developers” failure mode is still possible. Treat this as **P0 process**, parallel to the clone.

## Access model going forward

- Password manager vault titled **3Devices — justccell** owned by the client. Every secret lives there.
- 2FA on Hostinger, Cloudflare, WP (Wordfence or WP 2FA plugin).
- Developer accounts named, dated, and removable in one hour.
- No “we’ll send you the password later.”
- After go-live: monthly check that 3Devices can log into WP, Hostinger, Cloudflare, and download a backup without the developer.

## Runbook stub

When credentials move, log it in [BUILD-LOG.md](BUILD-LOG.md) with date and *what* moved (never paste passwords into this repo).
