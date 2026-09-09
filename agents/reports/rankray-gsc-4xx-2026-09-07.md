# rankray.com GSC 4xx alert — 2026-09-07

**Alert:** Oliver Gmail `sc-noreply@google.com` 2026-09-06 21:40 UTC — "Blocked due to other 4xx issue" for https://rankray.com/

## Root cause (confirmed live)
Google still had **`https://rankray.com/location-sitemap.xml`** submitted in Search Console.
That URL now returns **HTTP 404** (Yoast "Page Not Found", `noindex`).

Yoast’s current `sitemap_index.xml` no longer lists `location-sitemap.xml`. It correctly lists:
- post-sitemap.xml
- page-sitemap.xml
- **location-page-sitemap.xml** (200 OK, 38 location pages)
- category / author sitemaps

GSC sitemap list (before fix) showed `location-sitemap.xml` with **errors: 1** and last download 2025-10-07.

## Not the primary story
Live location pages (e.g. `/seo-agency-dubai/`, `/locations/`) return **200**.
Blog listicle `/blog/best-200-profile-creation-backlinks/` returns **200**.
Leftover slug `/blog/best-200-profile-creation-backlinks-2/` returns **404** (known old `-2` collision) — worth a 301 if it still gets hits, but not the sitemap 4xx alert driver.

## Fix
1. **Done (API):** Delete submitted sitemap `https://rankray.com/location-sitemap.xml` from GSC via SA.
2. Keep only `https://rankray.com/sitemap_index.xml` (or current child sitemaps) submitted.
3. Optional: 301 `-2` blog slug → canonical listicle; re-check Page indexing in GSC UI in 2–7 days for 4xx count drop.
4. Do not recreate `location-sitemap.xml` — `location-page-sitemap.xml` is the live Yoast feed.

## Evidence commands
- `curl -sI https://rankray.com/location-sitemap.xml` → 404
- `curl -sL https://rankray.com/sitemap_index.xml` → no location-sitemap.xml
- GSC sitemaps.list via SA showed stale feed with errors:1
