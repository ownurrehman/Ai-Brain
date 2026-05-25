# SEO Engine AI

**Version:** 13.13.1
**Status:** Staging / integration hardening
**License:** GPL v2 or later

SEO Engine AI is a WordPress plugin for AI-assisted bulk page generation, ACF-connected content generation, article drafting, and technical SEO automation. The plugin has a working core, but the current focus is release hardening and WordPress validation rather than calling it finished production-ready software.

## Current Scope

- Bulk service × city landing page generation
- Field detection for WordPress core, SEO meta, ACF, and custom post meta
- Prompt-driven content generation with per-field prompt editing
- Automated article writing with WP-Cron scheduling
- Technical SEO features including schema, redirects, robots additions, hreflang, and paged sitemap output
- Logging, duplicate handling, and Adaptive Character Intelligence (ACI)

## Current Reality

- `dev` is the active integration branch
- `main` is the current stable baseline
- `origin/cursor/*` branches are preserved as older divergent experiments and are not part of the current release line
- Testing scaffolding exists, but production release still requires live WordPress validation and broader regression coverage

## Documentation

- Status: `docs/STATUS.md`
- Branch and release strategy: `docs/BRANCH-AND-RELEASE-STRATEGY.md`
- Feature matrix and roadmap: `docs/FEATURES-AND-ROADMAP.md`
- Release scorecard: `docs/RELEASE-SCORECARD.md`
- 2026 roadmap: `docs/ROADMAP-2026.md`
- Architecture: `docs/architecture-v13.md`
- Changelog: `CHANGELOG.md`

## Branch Model

- `main`: stable branch, updated only from validated `dev`
- `dev`: integration and staging branch
- `codex/*`: short-lived working branches when isolated implementation work is needed
- `origin/cursor/*`: historical branches, preserved but not merged blindly

## Release Direction

The next valid release priority is:

1. verify the unified generator path in a real WordPress install
2. close compatibility gaps for templates and page builders
3. add regression coverage for the critical generation flows
4. promote `dev` to `main` only after the release scorecard passes

## Requirements

- WordPress 5.8+
- PHP 7.4+ with PHP 8.x preferred
- OpenAI API key
- Admin access with `manage_options`

## Quick Start

1. Activate the plugin and configure the API key under `SEO Engine AI -> Settings`.
2. Open `SEO Engine AI -> Generate Pages`, choose a post type and template, then enter services and cities.
3. Select the fields to generate, review the preview, and run generation.
4. Use `SEO Engine AI -> Logs` and the SEO screens to inspect output quality and technical SEO behavior.

## Repository Layout

```text
admin/              Admin UI classes, assets, and views
includes/           Core plugin services and integrations
data/               Prompt preset data
docs/               Status, roadmap, governance, and architecture docs
seoengineaicloud/   Deferred server/SaaS track
Builds/             Packaged build artifacts
tests/              Restored base test harness
seoengineai.php     Main plugin bootstrap
```

## Deferred Scope

The server/SaaS path remains deferred until the standalone plugin is stable:

- license and entitlement management
- proxy/API mediation and usage controls
- credits and billing
- centralized multi-site orchestration

## License

GPL v2 or later: https://www.gnu.org/licenses/gpl-2.0.html
