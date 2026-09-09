# BrowserOS — First Agentic Browser for the Fleet

> **Parent Hub:** [[system/INDEX|⚙️ System Infrastructure Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

**Installed:** 2026-09-08 (v0.50.3, arm64)
**Location:** `/Applications/BrowserOS.app`
**Profile data:** `~/Library/Application Support/BrowserOS/`
**Bundle ID:** `com.browseros.BrowserOS`
**License:** AGPL-3.0, free & open source (Y Combinator backed, 12k+ GitHub stars)

---

## What It Is

Open-source Chromium fork (v151 base) with a built-in AI agent layer. Two products:

| Product | Purpose |
|---|---|
| **BrowserOS** (installed) | Daily-driver browser with AI agent in new tab, skills, SOUL.md personality, scheduled tasks, agent memory, 53 tools, 40+ MCP integrations |
| **BrowserOS neo** | Companion browser specifically for agent harnesses (Claude Code, Cursor, Codex, OpenClaw, Hermes) driving tabs with real logins |

## Why It Matters for the Fleet

- **Real Chrome fingerprint** — Chromium fork, not headless. This is the key property.
- **Cloudflare Turnstile TEST PASSED (2026-09-08):** Loaded `demo.turnstile.workers.dev`, the widget auto-verified to **green Success!** with ZERO interaction, form filled (`hermes_agent`), Sign in flow exercised. No captcha-solving service needed.
- **Chrome extensions work** (it's a Chromium fork) — SEO extensions from Chrome (Nightwatch, Detailed SEO) can be imported.
- **Profile import:** one-click bookmarks/passwords/history from Chrome.
- **MCP server built-in:** connectable to Hermes, Claude Code, Codex, Cursor — agents get 53 browser tools.
- **Session replay:** every agent run recorded as scrubbable video, stored locally.

## How Hermes Uses It

1. **Backlink/profile creation** — captcha-protected signups (reCAPTCHA-adjacent Turnstile flows pass natively; reCAPTCHA v2 still needs solving on many sites but Turnstile-protected directories now work).
2. **Google services** with logged-in accounts (bot-detection previously blocked Playwright — real Chromium fingerprint helps).
3. **WordPress admin visual verification** — real browser, real session.
4. **Agent-driven browsing** via its MCP server — can be registered into Hermes config as MCP server when needed.

## Limitations (honest)

- computer_use control of BrowserOS works but click delivery is AX-routed and sometimes unverifiable (same as Chrome). For DOM-accurate control, use its **built-in MCP server** rather than screen-clicking.
- SiteGround sgcaptcha edge wall (coinsfera.com issue) is server-side — BrowserOS does not bypass that class of wall.
- reCAPTCHA v2 (image grids) is NOT auto-solved — Turnstile is the win.

## Related

- [[system/fleet-harness-2026-09-06|Fleet Harness]] — where agent dispatch flows live
- Backlink strategy: `Ai Brain/websites/backlink-strategy-v2-2026-08-13.md` — captcha-blocked sites list may now be partially unblocked
- [[rules/obsidian-vault-graph-integrity|Vault Integrity Standard]]