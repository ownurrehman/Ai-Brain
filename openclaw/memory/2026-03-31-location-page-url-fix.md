# 2026-03-31 Location Page URL Fix

## Verified facts
- Rank Ray location SEO pages use the post type slug `location-page`.
- Correct admin list URL: `https://rankray.com/wp-admin/edit.php?post_type=location-page`
- Incorrect URL that caused `Invalid post type`: `https://rankray.com/wp-admin/edit.php?post_type=location_page`
- Related admin route for terms: `https://rankray.com/wp-admin/edit-tags.php?taxonomy=service-type&post_type=location-page`

## Operational rule
- For Rank Ray location page work, always use `location-page` in the admin URLs.
- Do not use `location_page`.

## Why this matters
- The wrong slug caused wasted login/debug time and a false invalid post type error.
- The correct slug is now verified from the live admin UI.
