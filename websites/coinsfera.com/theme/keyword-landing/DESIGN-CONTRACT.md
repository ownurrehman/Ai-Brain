> **Parent Site:** [[websites/coinsfera.com/index|🌐 coinsfera.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Keyword Landing — design contract

Four designs share one data layer and one calculator engine. Everything else — layout, type, colour, radius, shadow, density, section order, markup — belongs to the design. Two designs should not look like relatives.

## Files a design owns

For design slug `<slug>`:

```
build/assets/css/design-<slug>.css
build/template-parts/keyword-landing/designs/<slug>/page.php     ← section order lives here
build/template-parts/keyword-landing/designs/<slug>/*.php        ← one file per section
```

Nothing else. Do not edit shared PHP, the base stylesheet, or another design's folder.

`page.php` is the whole layout. It calls `cfkl_part( 'hero' )`, `cfkl_part( 'calc' )` and so on in whatever order the design wants, and may skip sections that do not suit it.

## Scoping

Every rule must be scoped so it cannot leak to the rest of the site:

```css
.cfkl--<slug> .thing { }        /* inside <main> */
body.cfkl-design-<slug> .topbar { }  /* only to restyle the shared header */
```

Prefix classes `cfkl-<slug>-*`. No `!important`. No global element selectors outside `.cfkl--<slug>`.

## Base layer already handled

`keyword-landing.css` loads first and gives you: the fixed-header surface fix, an element reset wrapped in `:where()` (zero specificity, your rules always win), `.cfkl-container`, `.cfkl-sr`, focus rings, `.cfkl-reveal` scroll animation, reduced-motion handling, and `--cf-container` / `--cf-header` / `--cf-ease`.

Redefine any token you like inside `.cfkl--<slug>`. You are expected to define your own palette, type scale, radius and shadow language.

Add `cfkl-reveal` to a section to have it fade up on scroll. Do not animate the hero.

## Content API

All helpers are already loaded. `cfkl_get( 'name', $default )` reads a field, `cfkl_rows( 'name' )` reads a repeater. Always check for emptiness and skip the section rather than printing an empty shell.

| Section | Fields |
|---|---|
| Hero | `banner_tagline`, `banner_heading`, `banner_subtext`, `banner_cta_label`, `banner_cta_url`, `hero_image`, `banner_stats[value,label]` |
| Office | `cfkl_office()` → `label,address,url,cta,rating`; `office_title`, `office_text`, `office_hours[days,hours]`, `office_directions[label,desc]`, `office_map` |
| Intro | `intro_title`, `intro_text` (wysiwyg → `wp_kses_post`) |
| Trust | `trust_title`, `trust_text`, `trust_image`, `trust_points[title,desc]` |
| Steps | `steps_title`, `steps[title,desc,image]` |
| Requirements | `req_title`, `req_text`, `req_cards[title,desc,image]` |
| Features | `features_title`, `features[title,desc,image]` |
| Services | `services_title`, `services[title,desc,url,icon]` |
| Calculator | `calc_title`, `calc_text`, `calc_cta_label`, `calc_note`, `calc_default_coin`, `calc_default_currency` |
| Rate board | `rates_title`, `rates_text`, `cfkl_rate_board()` → rows of `symbol,label,usd,eur,try,change` |
| Coins | `coins_title`, `coins_text`, `coins_list[symbol,name,url]` |
| Comparison | `compare_title`, `compare_text`, `compare_col_us`, `compare_col_b`, `compare_col_c`, `compare_rows[label,us,b,c]` |
| Fees | `fees_title`, `fees_text`, `fees_rows[label,value,note]` |
| Reviews | `reviews_title`, `reviews_rating`, `reviews_count`, `reviews_url`, `reviews_items[text,name,meta]` |
| FAQ | `faq_title`, `faq_items[title,desc]` |
| CTA | `cta_title`, `cta_label`, `cta_url` |

Helpers: `cfkl_icon( $name, $class )` returns inline SVG for `pin check cross star clock shield wallet arrow swap bolt phone building`. `cfkl_image( $array, $size, $attrs )` renders an image with width and height. `cfkl_hero_image( $class )` renders the hero image with LCP attributes — use it exactly once, in the hero. `cfkl_money( $value, $currency )` formats a server-side rate. `cfkl_shared( 'map' )` renders the lazy Maps iframe.

## Calculator contract

`keyword-landing-calc.js` finds elements by data attribute, never by class, so the markup and layout are entirely yours. Put `data-cfkl-calc` on the root, then include whichever controls and outputs your design wants — anything you leave out is skipped.

Root, with optional starting state:

```html
<form class="cfkl-<slug>-calc" data-cfkl-calc
      data-calc-default-coin="BTC"
      data-calc-default-currency="usd"
      data-calc-default-mode="buy">
```

Controls:

| Attribute | On | Behaviour |
|---|---|---|
| `data-calc-mode="buy"` / `"sell"` | buttons | switches direction, gets `aria-pressed` and `.is-active` |
| `data-calc-coin` | `<select>`, or buttons with `data-value` | coin choice |
| `data-calc-currency` | `<select>`, or buttons with `data-value` | USD / EUR / TRY |
| `data-calc-fiat` | `<input type="number">` | fiat amount, drives the crypto field |
| `data-calc-crypto` | `<input type="number">` | crypto amount, drives the fiat field |
| `data-calc-quick data-amount="1000"` | buttons | fills the fiat field |
| `data-calc-cta` | `<a>` | href becomes a WhatsApp link with the quote written out; optional `data-calc-message` template supporting `{mode} {crypto} {coin} {fiat} {currency}` |

Outputs — `<span data-calc-out="KEY">`:

`rate` `rate-plain` `total` `total-plain` `crypto` `coin` `coin-label` `currency` `currency-symbol` `unit` `direction` `spread` `status` `updated` `change`

`change` also receives `data-trend="up|down"` and is hidden when the fallback feed carries no change figure. The root receives `data-calc-state="live|stale"` and `data-calc-active-mode="buy|sell"` — style from those rather than inventing your own state classes.

Server-render sensible starting values inside the output spans using `cfkl_money()` so the calculator is not empty before JavaScript runs, and so it still says something useful if JavaScript never runs.

## Non-negotiables

- Escape everything: `esc_html`, `esc_attr`, `esc_url`, `wp_kses_post` for wysiwyg.
- Wrap every string in `__( '…', 'coinsfera' )`; never concatenate a sentence from fragments.
- One `<h1>`, in the hero. Sections use `<h2>`; cards use `<h3>`.
- FAQ uses `<details>`/`<summary>` so it works without JavaScript.
- Every image needs width and height. Only the hero image is eager.
- No new fonts, libraries, build steps, plugins or external requests. System font stacks are allowed; the theme already ships Circular Pro (`circularbook`, `Circular`, `circularbold`).
- Tables must stay readable on a phone — either scroll horizontally in a labelled region or restack.
- Interactive elements need a visible focus state and a real accessible name.
- Test the design at 360px, 768px, 1280px and 1600px in your head before writing the CSS; no horizontal scroll at any of them.
