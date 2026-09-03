# Justccell ACF Media Attachment Report (2026-08-31)

## Task
Attach already-uploaded Media Library files to 37 products per upload-manifest.csv (155 rows).
REST only: no browser, no re-upload, no scraping.

## Result: 37/37 slugs processed, 0 missing media, 0 errors
- Woo featured + gallery images updated per slug (featured first, gallery in NN order)
- ACF clone fields written via Woo meta_data API (ACF REST disabled on site):
  clone_card_image, clone_banner, clone_gallery, clone_evomax_bg, clone_spin, clone_details
- Features repeater (feature-NN): left untouched where products had no existing repeater image rows
  (per prompt: only replace row image ids in order; no rows = nothing to replace)
- m3-plus: no gallery in CSV, so previous gallery value [328060] kept (prompt: only swap CSV images)

## Verification (REST + live HTML)
- mini-tank /all-in-ones/mini-tank/: 5 refs to mini-tank-justccell-vape-featured.png (og:image + main img)
- tank: 6 refs to tank-justccell-vape-* images (featured + banner + gallery live)
- voca-pro: 5 refs to voca-pro-justccell-vape-* images
- LiteSpeed purge: attempted via MCP (500 - API quirk); pages already serve fresh images; if needed
  purge manually in hPanel or LiteSpeed admin.

## Auth used
- JWT endpoint returned 500 (known API quirk) -> fallback per prompt: JUSTCCELL_WP_USER + JUSTCCELL_WP_APP_PASS
  basic auth (spaces stripped). Proven with GET /users/me = 200 (Rank Ray, id 1).

## Key files
- Manifest: Ai Brain/websites/justccell.com/media-upload-ready/upload-manifest.csv (155 rows, 37 slugs)
- Results dump: /tmp/justccell_results.json (per-slug product_id + media_ids)
- Optional media title/alt updates: skipped (filenames already descriptive; not required by prompt)
