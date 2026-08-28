> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Visibility control (coming soon)

The public and the client must **not** see the real storefront until you say go live.

## How it is controlled

**Minimal Coming Soon & Maintenance Mode** stays **on**. Anonymous visitors get the coming-soon page. That is the gate.

| Who | What they see |
|---|---|
| Public / client (logged out) | Coming soon only |
| You, logged into WordPress as admin | The real site (`/uk/`, `/us/`, `/ae/`, …) so we can build and QA |
| Search engines | Coming-soon / noindex while the plugin is active |

Go live later = deactivate that plugin (and confirm Google Search Console). Not a theme change.

## Preview without opening the site

1. **Staging (draft for the client):** https://dev.justccell.com/ — same coming-soon gate. Log in at `https://dev.justccell.com/wp-login.php` (same users as the clone). Do not treat staging as a second product; it is a Hostinger copy for showing work.
2. **Live, logged in:** `https://justccell.com/wp-login.php` — then visit any store URL.
3. In the plugin settings, keep **“Disable for logged-in users”** enabled (default we want).
4. If the plugin offers a **secret preview link**, use that for a non-admin teammate. Do not send the client the raw `/es/` URL until you want them to see it.

Do not turn the plugin off to “test prefixes.” Log in instead.

## What happened 2026-08-14

Coming soon was switched off briefly so country URLs could be verified, then it is switched **back on**. The real theme stays deployed behind the gate.
