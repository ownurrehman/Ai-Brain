> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Domains and email

## Roles

| Domain | Role |
|---|---|
| **justccell.com** | Commercial brand, canonical URLs, customer-facing |
| **3devicescorp.com** | Legal safety net (CCELL trademark). Same WordPress. Can become primary if justccell.com must be retired |
| justccelldevices.com | Exists on the same Hostinger account. **Ask client** if it is in scope (park, redirect, or ignore) |

## How 3devicescorp.com must work

Same platform, same country detection, same cookies.

**Target behaviour**

- DNS: same Cloudflare proxy → same origin as justccell.com **or** 301 everything to justccell.com (path-preserving).
- Prefer **301 to justccell.com** while justccell.com is the commercial brand (cleaner SEO, one canonical).
- WordPress `WP_HOME` / `WP_SITEURL` stay `https://justccell.com`.
- If they must flip brand overnight: change Cloudflare + WP URLs + canonical; catalog and customers stay. That only works if 3devicescorp is **not** a second WP database.

**Today (2026-08-14):** Hostinger has a **separate** WordPress on 3devicescorp.com (`30055771`). That is a split-brain risk. Plan: convert to parked/alias or 301-only; do not keep a second admin.

Cloudflare already has a 3devicescorp.com zone pointing at the same origin IP (`187.124.156.180`). Align Hostinger so public_html is not a second install.

## Email

| Address | Behaviour |
|---|---|
| info@justccell.com | Primary mailbox (Hostinger or Google Workspace — **3Devices-owned**) |
| info@3devicescorp.com | Forward to info@justccell.com |

Also plan (not all in the brief, but needed):

- wordpress@ / no-reply@ for transactional mail (Woo, inquiry form)
- SPF / DKIM / DMARC on **both** domains
- justccell.com already has an SPF TXT on Cloudflare; complete DKIM when mailbox is final

Inquiry form currently uses `wp_mail` to the WP admin email. After ownership transfer, admin email must be a 3Devices inbox, not a developer Gmail.

## Trademark note (for the file, not legal advice)

Client wants the safety domain because CCELL is a registered mark. justccell.com is the trading name they are using now. We do not opine on whether that use is allowed; we make the **switch** operationally cheap: one WP, two hostnames, email forward, documented runbook in this folder when we write `runbooks/domain-cutover.md` (not created until cutover is scheduled).
