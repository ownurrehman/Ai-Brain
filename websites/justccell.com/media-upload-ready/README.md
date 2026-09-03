# Media upload pack — named from live justccell.com products

Built from the 47 Woo products on justccell.com. Source files are our own uploads, renamed.
Do not use `media-replace-ready/unused/` — that split was wrong (hash names still used on pages).

## Names

- File: `mini-tank-justccell-vape-featured.png` (lowercase; WPCode lowercases uploads)
- Media title / alt: `Mini Tank - Justccell - Vape`
- Gallery: `Mini Tank - Justccell - Vape - Gallery 01`

## Folders

- `upload/` — every file in one folder. Select all → Media → Add New
- `upload-manifest.csv` — title, alt, where to attach
- `products-with-no-images.txt` — SKUs with nothing on the live page yet

## Upload (Hermes or Media → Add New)

1. Upload files. Keep the filenames.
2. Set Title and Alt from the CSV.
3. Attach:
   - `featured` → Product image + ACF listing card image
   - `banner` → ACF Banner image
   - `gallery-NN` → ACF Thumbnail gallery (order in the filename)
   - `360-NN` → ACF 360 spin frames
   - `feature-NN` → ACF Feature slider backgrounds
   - `evomax` → ACF EVOMAX background
   - `detail-NN` → ACF Detail mosaic

Do not run Justccell → Media replace.
