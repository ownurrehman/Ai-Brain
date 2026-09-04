> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Media Library replacement workflow

**Goal:** Fresh uploads in Media Library, pages/products using them, grid view loads fast.

Go to **Justccell → Media replace** in wp-admin.

## Before you start

- Both upload batches (in-use + unused) should already be in **Media → Library**
- The **yellow reconnect box (v1.3.0)** is the only tool to use — ignore any other reconnect buttons

## Steps (run in order)

### 1. Unmark fresh uploads

Clears the “legacy” tag from your new `justccell-*` files if they were tagged by mistake.

### 2. Scan for matches

Finds pairs: old broken file → your new upload (matched by SEO filename).

**Target:** ~690 pairs (168 in-use + ~522 unused).

If the count is very low after Step 1, tell us — we’ll check filenames.

### 3. Reconnect (batched)

Updates ACF fields, product images, page heroes, alt text, etc.  
Runs **one pair per request** — safe, may take several minutes. **Do not close the tab.**

### Verify the live site

Check homepage, About, and 2–3 product pages before Step 4.

### 4. Delete legacy files

Removes old broken attachments that were already reconnected.  
This is what makes **Media Library grid** fast again (~690 files instead of ~2000).

## After completion

- **Media → Library → Grid** should load quickly
- All pages should show your uploaded images
- Only fresh SEO-named files remain in the library

## Do NOT use

- Old **“Step 4: Reconnect matched uploads”** theme button (crashes/timeouts)
- Any bulk reconnect that runs thousands of items in one request

## Plugins involved

| Plugin | Purpose |
|--------|---------|
| `jc-media-reconnect-repair` v1.3.0 | Safe batched reconnect (active during workflow) |
| `jc-acf-guard` | Prevents memory issues in header/menu |

Remove recovery plugins after the site is stable.
