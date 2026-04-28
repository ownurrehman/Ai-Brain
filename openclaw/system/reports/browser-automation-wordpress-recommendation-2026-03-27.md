# Browser automation recommendation for persistent WordPress admin work

Date: 2026-03-27

## Executive recommendation

Use **Playwright as the core**, with **one persistent Chromium profile per site/account group** and an optional **remote-debugging / CDP attach mode** for handoff to an already logged-in browser.

Do **not** make Browser Use, Stagehand, or Lightpanda the primary layer for WordPress admin work. They can be useful helpers, but the base layer should stay Playwright because WordPress admin, ACF fields, plugin UIs, media modals, and backend fixes need:
- deterministic selectors
- stable waits
- screenshots/traces
- repeatable login/session reuse
- easy fallback to manual intervention when Cloudflare, Turnstile, or unusual admin plugins appear

## Why this is the best fit

WordPress admin automation is not a generic browsing problem. It mixes:
- persistent logged-in sessions
- admin menus and plugin pages
- ACF field groups and repeaters
- classic editor / block editor / plugin-specific UIs
- occasional CAPTCHAs or Cloudflare checks
- high need for evidence when changing production data

Playwright is already installed in this workspace, OpenClaw already has a **Playwright skill**, and local notes show the main failure mode was **opening a separate unauthenticated context**, not a lack of browser capability.

## What I found locally

### Existing relevant capabilities in this workspace
- Playwright is already installed in `/Users/sheikhown/.openclaw/workspace/node_modules/playwright`
- There is an existing OpenClaw skill: `Playwright (Automation + MCP + Scraper)`
- There is also a `Screenshot` skill
- There is a `clawhub` skill for installing more skills if needed
- I did **not** find an installed WordPress-admin-specific skill or a dedicated browser automation skill beyond Playwright/Screenshot

### Important local evidence
Workspace notes on 2026-03-27 say:
- a persistent browser profile/session was active
- auth was not retained in a reusable status check
- a finalize script failed due to a **separate unauthenticated browser context**

That strongly suggests the right fix is architectural: **standardize session reuse** instead of adding a more agentic browser framework.

## Options evaluated

### 1) Playwright persistent profile
**Verdict:** Best primary solution

Pattern:
- launch Chromium with `launchPersistentContext(userDataDir, ...)`
- keep one named profile directory per site or account set
- reuse the same context for follow-up tasks

Why it fits:
- preserves cookies/local storage/session state
- works well for repeated wp-admin tasks
- deterministic enough for ACF and plugin screens
- easy to capture screenshots, HTML, traces, console logs
- easy to combine human-assisted login with later automated reuse

Tradeoffs:
- profile hygiene matters
- persistent profiles can drift or get corrupted over time
- concurrent use of the same profile must be controlled carefully

Best use:
- daily WordPress admin operations across a known set of sites

### 2) Playwright + Chrome remote debugging / CDP attach
**Verdict:** Best secondary mode for handoff and rescue

Pattern:
- user logs into a normal Chrome/Chromium profile manually
- browser runs with `--remote-debugging-port=9222`
- automation attaches using CDP instead of launching a fresh browser

Why it fits:
- excellent when manual login or CAPTCHA clearance is required
- avoids re-solving login challenges in a fresh automation context
- good for continuing from a live human browser session

Tradeoffs:
- CDP attach is less clean than owning the entire browser lifecycle
- more fragile if multiple tabs/profiles are open
- session discovery/routing logic must be written carefully

Best use:
- Cloudflare, Turnstile, 2FA, or “finish manually then let automation continue” flows

### 3) Stagehand
**Verdict:** Useful optional helper, not the foundation

What it is:
- AI-assisted browser automation built around actions like act/extract/observe/agent

Pros:
- faster for exploratory tasks and semi-structured pages
- helpful when selectors are unknown
- can reduce scripting effort for audits and discovery

Cons for this use case:
- adds another abstraction layer on top of browser control
- less ideal than raw Playwright for production-grade admin changes
- WordPress admin and ACF often reward explicit selectors and predictable state handling

Best use:
- audits, discovery, rough-first-pass flows, action discovery
- not my first choice for reliable admin editing across many sites

### 4) Browser Use
**Verdict:** More agentic than needed; not the right default

Pros:
- useful for open-ended browser tasks
- good for agent-style workflows

Cons for this use case:
- agentic browser stacks are usually worse than explicit Playwright for production admin work
- harder to reason about exact actions on fragile plugin/admin UIs
- less suitable when correctness matters more than autonomy

Best use:
- experimental agent browsing, not core WordPress ops

### 5) Lightpanda
**Verdict:** Not suitable as primary solution here

Pros:
- performance-focused headless browser approach
- attractive for large-scale scraping workloads

Cons for this use case:
- WordPress admin work benefits from mainstream browser compatibility and real-session behavior
- headed/manual handoff and login persistence matter more than raw speed
- plugin-heavy admin UIs are exactly where compatibility risk is costly

Best use:
- large-scale scraping/crawling, not persistent wp-admin operations

### 6) tmux + long-lived browser process / manual terminal orchestration
**Verdict:** Useful support tool, not the core architecture

Pros:
- can keep a browser or automation worker alive across sessions
- handy for recovery and debugging

Cons:
- solves process persistence more than browser-state design
- becomes messy without a proper profile/session manager

Best use:
- support layer for long-running workers, not the primary browser abstraction

## Recommended architecture

### Tier 1: Deterministic core
Build a small local WordPress browser automation layer around Playwright with:
- persistent profile directories
- one task runner
- site-specific selectors/helpers
- screenshots/traces on every mutating task
- explicit “read-only” and “mutating” modes

### Tier 2: Session handoff mode
Add CDP attach support so automation can attach to a browser the user already logged into.

### Tier 3: Optional AI helper
If desired later, add Stagehand for exploration and audit tasks only, not for primary content/admin mutation flows.

## Practical design for this workspace

Create a small package or folder such as:

`/Users/sheikhown/.openclaw/workspace/browser-ops/`

Suggested structure:

- `browser-ops/profiles/`
  - `rankray/`
  - `teammotorcycle/`
  - `tonicphysio/`
  - `khanllp/`
  - `coinsfera/`
- `browser-ops/config/sites.json`
- `browser-ops/lib/session.js`
- `browser-ops/lib/wordpress.js`
- `browser-ops/lib/acf.js`
- `browser-ops/lib/evidence.js`
- `browser-ops/tasks/`
  - `open-admin.js`
  - `site-audit.js`
  - `update-post-fields.js`
  - `acf-update.js`
  - `plugin-settings-read.js`
- `browser-ops/output/`
  - screenshots
  - traces
  - logs

## Session model I recommend

### Mode A: persistent profile launch
Use for normal repeated work.

- one persistent userDataDir per site/account group
- always launch the same browser channel/profile for that site
- never mix ephemeral contexts for tasks that expect login reuse
- serialize access per profile

### Mode B: attach to live browser
Use for login challenges.

Flow:
1. open Chrome/Chromium with remote debugging enabled
2. user logs in manually and clears any anti-bot checks
3. automation attaches over CDP
4. automation continues inside that live session

## Guardrails

For production WordPress admin work, the framework should enforce:
- dry-run/read-only mode by default
- screenshots before and after changes
- trace capture for mutating operations
- per-site allowlist of hosts
- explicit confirmation step before destructive actions
- human review for plugin settings writes and bulk edits

## What to install or build next

### Install / confirm
1. Keep using the existing Playwright install
2. Prefer a branded Chrome/Chromium channel for session realism if needed
3. No urgent need to install Browser Use, Stagehand, or Lightpanda for the core solution

### Build next in this workspace
1. **Build a browser session manager first**
   - named persistent profiles
   - lock file to prevent concurrent profile use
   - optional CDP attach support

2. **Build WordPress primitives second**
   - login/status check
   - open wp-admin page
   - navigate left menu reliably
   - detect block editor vs classic editor
   - detect ACF field groups / repeaters / flexible content
   - save/update and verify success notices

3. **Build evidence + recovery third**
   - screenshot helper
   - trace recording on writes
   - HTML snapshot on failure
   - current URL / title / admin screen logging

4. **Build site adapters fourth**
   - selectors/config per site and plugin mix
   - handle Rank Ray first, then generalize

## Suggested phased implementation plan

### Phase 1: foundation
- create `browser-ops/`
- implement persistent profile launcher
- implement CDP attach mode
- add simple `open-admin` and `whoami` checks

### Phase 2: WordPress-safe primitives
- detect whether auth is valid
- open posts/pages list
- open edit screen
- save draft/update post
- verify admin notices
- capture evidence artifacts automatically

### Phase 3: ACF + audits
- add field discovery helpers
- support repeaters/flexible content carefully
- add read-only audit scripts for titles, metas, internal link checks, plugin notices

### Phase 4: optional exploration layer
- evaluate adding Stagehand only for exploratory navigation and audits

## Bottom line

If the goal is robust multi-site WordPress admin automation with persistent logged-in sessions on macOS inside OpenClaw, the answer is:

**Build on Playwright, standardize persistent profiles, add CDP attach as the escape hatch, and keep agentic browser frameworks optional.**

That is the cleanest match for ACF fields, audits, backend fixes, admin UI work, and the session-reuse problem already observed in this workspace.

## Short next-step recommendation

Next action in this workspace should be:

1. create `browser-ops/` with a persistent-profile launcher
2. add a `site registry` file for the main WordPress sites
3. implement `open-admin`, `check-auth`, and `attach-cdp` scripts
4. test on Rank Ray first
5. only after that, consider Stagehand for audits/discovery
