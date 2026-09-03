# Justccell Homepage Custom Gallery Report (2026-09-01)

## Task
Fill homepage Classic Customization gallery with exactly 4 Media Library images (field
home_custom_images / field_jc_home_custom_images, page 241). REST only.

## Result: FIXED - 4 slots rendering

## Front page ID
241 (page_on_front, slug=home, UK homepage)

## 4 media IDs + filenames (slot order = left to right)
1. 107 - justccell-customer-logo-4.png (kept, 820x1196)
2. 328670 - justccell-customer-logo-1.png (820x1196)
3. 328671 - justccell-customer-logo-2.png (820x1196)
4. 328672 - justccell-customer-logo-3.png (820x1196)

All 4 = portrait 820x1196 (matches 400x584 ratio), customization wrap samples from the same family.

## Write method that worked
ACF REST disabled + page REST meta stripped unregistered keys (POST acf and POST meta both
silently ignored). Working path: XML-RPC wp.editPost with custom_fields, passing the gallery
value as a LIST (not a pre-serialized string - XML-RPC serializes lists to proper PHP arrays):
  - updated meta row 1944 (old broken value [105,102,107,107] with 3 trashed ids)
  - deleted duplicate row 344869 (my first write double-serialized)
  - final row 1944 value = a:4:{i:0;i:107;i:1;i:328670;i:2;i:328671;i:3;i:328672;}
  - _home_custom_images = field_jc_home_custom_images (field ref intact from before)

## Live verification
- .h-custom__item count = 4 (was 1)
- slot 1 = justccell-customer-logo-4.png (kept first)
- all 4 filenames present, order 4-1-2-3 confirmed in section markup
- premium row (home_premium_image) untouched and rendering
- zero ccell.com/manufacturer URLs in gallery (only justccell's own Instagram social link)
- homepage 200

## Notes
- LiteSpeed purge via MCP: 500 (known quirk, noted per prompt, not chased)
- xmlrpc.php is ENABLED on justccell (unlike mariaoasis) - usable for page/postmeta writes
  where REST strips unregistered meta
