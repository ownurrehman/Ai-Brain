
## [LRN-20260329-001] best_practice

**Logged**: 2026-03-29T10:05:00Z
**Priority**: high
**Status**: pending
**Area**: config

### Summary
Rank Ray location-page production needs a single hardened Yoast write-and-verify routine instead of mixed ad hoc REST and browser attempts.

### Details
Previous batches used multiple fragmented scripts. They work for content creation, ACF population, images, and taxonomy, but Yoast persistence is inconsistent. The correct system improvement is to standardize one route with verification and artifact capture.

### Suggested Action
Create a dedicated Rank Ray location-page fixer that: opens authenticated profile, navigates to edit page, sets service checkbox if needed, writes focus keyphrase and meta description, saves, verifies visible persistence, and logs results for every page.

### Metadata
- Source: simplify-and-harden
- Related Files: browser-ops/scripts/open-profile.js, tmp/fix_batch1_browser.py
- Tags: rankray, yoast, wordpress, automation
- Pattern-Key: harden.rankray.yoast-location-pages
- Recurrence-Count: 1
- First-Seen: 2026-03-29
- Last-Seen: 2026-03-29

---
