> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Media Library & Asset Replacement Workflow — eliteterpenez.com

Protocol for ingesting, optimizing, and wiring production images and videos into the WordPress Media Library on `eliteterpenez.com`.

---

## 1. Core Mandate: Media Library Only

Every photo, aroma diagram, bottle mockup, and video visible to site visitors **must be an attachment in the local WordPress Media Library** (`wp-content/uploads/...`).

### Prohibitions:
- ❌ **No External CDNs:** Never load images from external CDNs, third-party domains, or competitor sites.
- ❌ **No Theme Path Hardcoding:** Never output `<img src="/wp-content/themes/.../assets/img/...">` in production template markup.
- ❌ **No Source Leaks:** Never upload images with filenames containing "Abstrax" or source hashes (e.g. `abstrax_banner.jpg`, `abstrax-og-kush.png`).

---

## 2. Filename Hygiene & SEO Naming

All uploaded files must adhere to the standardized Elite Terpenes naming syntax:
`elite-terpenes-[context]-[descriptor].[ext]`

### Examples:
- ✅ `elite-terpenes-home-hero-botanical.webp`
- ✅ `elite-terpenes-product-og-kush-bottle.webp`
- ✅ `elite-terpenes-sensory-radar-citrus.webp`
- ✅ `elite-terpenes-lab-iso-certified-badge.svg`
- ❌ `abstrax-hero-2026.jpg` (BANNED — source leak)
- ❌ `screenshot_2026-09-04.png` (BANNED — unoptimized/unbranded)

---

## 3. Image Optimization Standards

Before uploading assets to `wp-admin → Media → Add New`:
1. **Format:**
   - Photographs, heroes, and lifestyle imagery: **WebP** (80%–85% quality).
   - Transparent badges, icons, and bottle renders: Optimized **WebP** or clean **PNG-24**.
   - Vector icons & logos: Clean **SVG** (sanitized).
2. **Resolution & Dimensions:**
   - Full-width hero banners: Maximum `2560px` width, target file size `< 300KB`.
   - Card and product images: Maximum `1200px` width/height, target file size `< 120KB`.
   - Icons and badges: Maximum `400px` width, target file size `< 30KB`.
3. **Metadata Scrubbing:**
   - Strip all camera EXIF data, GPS coordinates, author tags, and software metadata prior to upload.

---

## 4. Front-End Template Integration

In PHP templates and template parts, always retrieve and render media using WordPress native attachment APIs:

```php
<?php
// Retrieve attachment ID from ACF or WooCommerce
$image_id = get_field('hero_image') ?: get_post_thumbnail_id();

if ($image_id) {
    echo wp_get_attachment_image(
        $image_id,
        'full',
        false,
        [
            'class'   => 'et-hero__image',
            'loading' => 'eager', // Eager for above-the-fold hero, lazy for below-the-fold
            'alt'     => esc_attr(get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: get_the_title()),
        ]
    );
}
?>
```

This guarantees that:
1. Native `srcset` and `sizes` attributes are generated automatically.
2. Replacing an image in the WordPress Media Library instantly updates all pages referencing that attachment ID.
3. No broken paths occur when staging or migrating domains.
