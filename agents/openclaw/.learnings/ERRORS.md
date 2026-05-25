
## [ERR-20260329-001] rankray_yoast_location_pages

**Logged**: 2026-03-29T10:05:00Z
**Priority**: high
**Status**: in_progress
**Area**: backend

### Summary
Yoast meta description and focus keyword writes are inconsistent across Rank Ray location pages via current REST/browser workflow.

### Error
- REST writes for Yoast fields persist on some location pages but not others.
- Browser automation path used in prior scripts is brittle and profile/auth attachment is inconsistent.

### Context
- Affected known pages include Seattle, Chicago, Los Angeles, New York, Vancouver.
- New York also had blank select_service during audit.
- Content, images, service taxonomy, and draft state are working; Yoast persistence is the unstable layer.

### Suggested Fix
Build a reliable browser-attached Yoast setter/verifier against the authenticated persistent Rank Ray profile, then run it across all SEO agency location pages and save evidence artifacts.

### Metadata
- Reproducible: yes
- Related Files: tmp/fix_batch1_browser.py, browser-ops/scripts/open-profile.js, memory/procedural/rankray-seo-city-page-execution-workflow.md

---
