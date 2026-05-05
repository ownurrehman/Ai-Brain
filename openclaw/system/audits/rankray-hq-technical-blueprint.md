# RankRay HQ Technical Blueprint: Source of Truth

## 1. System Understanding

### 1.1 Architectural Map
The RankRay HQ ecosystem is currently a **hybrid-automation framework** consisting of a live WordPress site and a set of external operational scripts. It is not yet a unified application but a collection of "automation satellites" orbiting the CMS.

*   **Frontend:** WordPress (PHP/MySQL) hosting `rankray.com`.
*   **Backend (CMS):** WordPress Admin Dashboard + WordPress REST API.
*   **Automation Layer (External):** 
    *   **Node.js Scripts:** Located in `/browser/` and `/headless-browser-scripts/`. Used for programmatic content injection, Yoast SEO updates, and login simulation.
    *   **Python Scripts:** Used for media checks and potentially deeper data analysis.
*   **Data Flow:** 
    `Operational Markdown/JSON` $->$ `Node.js Script` $->$ `WordPress REST API` $->$ `MySQL Database` $->$ `Publicly Rendered Page`.
*   **Critical Dependencies:**
    *   **WordPress REST API:** The primary pipe for autonomous updates.
    *   **Yoast SEO:** The target for meta-optimization via API.
    *   **Application Passwords:** Used for `Basic Auth` in REST calls.
    *   **Puppeteer/Playwright:** Used for browser-level tasks where REST API is insufficient (e.g., certain admin-only settings).

### 1.2 Current Technical State
The "codebase" is currently a set of procedural scripts designed for specific tasks (e.g., `rankray-rest-update.js`, `rankray-faq-fix.js`). There is no central "orchestrator" or state machine; execution is manual or triggered via subagents.

---

## 2. Error & Debt Analysis

### 2.1 Logic Flaws & Brittle Sections
*   **Hardcoded Identifiers:** `rankray-rest-update.js` and `rankray.config.json` use hardcoded `postId` (12055). This makes the scripts single-purpose rather than scalable tools.
*   **Basic Auth Exposure:** Application passwords are hardcoded in scripts (e.g., `rankray-rest-update.js` line 5), posing a security risk.
*   **Fragile Parsing:** The `loadDraft` function in `rankray-common.js` relies on specific markdown headers (`## Proposed SEO title`). If the draft format changes, the injection fails.
*   **Error Handling:** Most scripts use basic `try-catch` or ignore failures, resulting in "silent drops" where an API call fails but the script continues.

### 2.2 Performance & Security Bottlenecks
*   **Security:** Use of `Basic Auth` over REST is standard for WP, but storing credentials in plain text in the workspace is a critical vulnerability.
*   **Performance:** Scripts are synchronous and execute one-off calls. Large-scale updates (e.g., 100+ city pages) would be slow and prone to timeouts.

---

## 3. Potentials & Optimizations

### 3.1 Immediate Stability Wins (Low-Hanging Fruit)
*   **Credential Externalization:** Move all API keys and passwords to a `.env` file or `~/.openclaw/.env`.
*   **Dynamic Post ID Resolution:** Implement a lookup function that finds `postId` based on the slug instead of hardcoding it.
*   **Standardized Logging:** Replace `console.log` with a structured logging system that writes to `/workspace/logs/rankray-hq.log`.

### 3.2 Maintainability Refactors
*   **Unified SDK:** Convert the scattered scripts in `/browser/` into a single `RankRaySDK` class that handles authentication, content injection, and SEO updates.
*   **Template-Driven Generation:** Move from manual regex parsing to a structured JSON schema for content drafts.

---

## 4. Feature Suggestions & Healing Engine Roadmap

### 4.1 High-Impact Features
1.  **Sitemap Watcher:** A script that crawls the sitemap and flags pages with missing meta-descriptions or low word counts.
2.  **Competitor Delta Tracker:** A tool that snapshots the top 3 competitors' headings every 30 days and highlights "Content Gaps."
3.  **Automatic Internal Linker:** An agent that scans new posts and suggests 3-5 internal links from existing high-authority pages.

### 4.2 Healing Engine Integration
The codebase must be prepared for the **Healing Engine** (as specified in `architecture/self-healing-seo.md`).

**Implementation Path:**
*   **Telemetry Layer:** Create a `telemetry.js` module that logs the "Before" and "After" state of every automated edit.
*   **Correction Triggers:** The "Watcher" (SERP API) should trigger the `RankRaySDK` only when a rank drop is detected.
*   **Healing Loop Placement:**
    *   **Trigger:** RankRay HQ $->$ SERP API $->$ Anomaly Detector.
    *   **Action:** `Strategist Agent` $->$ `RankRaySDK.updatePost()`.
    *   **Validation:** `Watcher Agent` $->$ `rankray-status.js` (enhanced).

**Proposed Telemetry Point:**
Inject a hidden HTML comment `<!-- RankRay-Healing-ID: [UUID] -->` into the bottom of every autonomously edited page. This allows the engine to track exactly which version of a "healing" attempt is currently live.
