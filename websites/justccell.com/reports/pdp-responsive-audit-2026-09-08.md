> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# PDP frontend responsive audit — 2026-09-08

Live check after theme **0.9.326** / Features **1.1.29** (stage gallery arrows + thumb rail). Pages: `/all-in-ones/mini-tank/`, `/all-in-ones/blanc/`. Logged-in storefront (coming soon stays on for guests). Viewports: ~1280 desktop, 768 tablet, 390 phone (Chrome device metrics).

No WordPress or WooCommerce core files were changed.

## What now works

- Hero stage has previous/next buttons (`.p-stage-nav`). Clicking next on Mini Tank and Blanc moved `is-on` from thumb 0 → 1 and the still `src` matched that thumb.
- Still images slide with `translateX` inside `.p-stage-viewport`. Colour/variation `paintStill` is unchanged.
- Phone thumbs use `clamp(2.75rem, 16vw, 5.5rem)` (Mini Tank measured **62px** at 390px). No horizontal page overflow (`scrollWidth` = viewport).
- Overflow rails add inline padding so thumb prev/next do not sit on top of the first/last thumbnail.
- Quantity row stays **50px** tall (no flex-grow gap).

## Issues found

| Severity | Where | Issue |
|---|---|---|
| **High** | Tablet and phone (`max-width: 1100px`) | Shop stacks image first. Stage is a full-width square (**358px** tall at 390px, **736px** at 768px). Colour / Tank Size / Add to cart sit **below the fold** until the visitor scrolls. Sticky right column only starts at **1101px**. |
| **Medium** | Blanc (and any SKU with empty variation price) | Left wholesale table still shows the empty “Select options to see pricing…” state (~96px). Harmless, but it looks like unused space until a combination is chosen. |
| **Medium** | 4–5 image galleries on phone | Thumbs now **fit** the row, so the thumbnail-rail arrows stay hidden. That is correct for Mini Tank/Blanc. SKUs with more stills will scroll; peek-fade only appears when `scrollWidth` exceeds the row. |
| **Low** | Stage arrows | Chevrons sit on the photo (by design). They are 48px (above 44px). WhatsApp/Telegram docks sit at the bottom-right and do not cover the stage arrows on the 390px pass. |
| **Low** | CSS leftover | Generic `.p-thumbs { max-width: calc(4 * 5.2vw + 60px) }` is still in `product.css`. Stage thumbs override it; do not rely on the generic rule for new galleries. |
| **Info** | Checkout screenshot | Checkout line qty is a badge on the thumbnail, not the PDP stepper. No checkout regression in this pass. |

## Not bugs (do not “fix” without a new ask)

- Do not restore the old **20rem** stage cap or shop-right `max-height` / inner scroll (that caused overlap in 0.9.319–0.9.321).
- First gallery thumb can still be 360° spin; arrows then move to stills.

## Suggested follow-ups (not shipped)

1. Keep the large desktop photo, but on tablet/phone cap **stage max-height** (for example `min(100vw, 52vh)`) so Colour and Add to cart stay in the first screen.
2. Collapse the empty wholesale table until a variation is selected (hide the “Select options…” shell instead of a 96px empty box).
3. Optional: keyboard left/right on the stage (buttons are already tabbable).
