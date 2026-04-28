# Core Web Vitals Optimization Guide

## Overview

Core Web Vitals are Google's page experience signals measuring loading, interactivity, and visual stability. They directly impact rankings.

---

## LCP — Largest Contentful Paint

**What:** Time to render the largest visible element
**Target:** < 2.5s (good), < 4s (needs improvement)

### Main LCP Elements
- Background images
- `<img>` elements
- Video poster images
- Block-level text

### Optimization Tactics

1. **Optimize server response**
   - Use fast hosting/CDN
   - Cache HTML
   - Preconnect to required origins

2. **Preload critical resources**
   ```html
   <link rel="preload" as="image" href="hero.jpg">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   ```

3. **Compress images**
   - Use WebP format
   - Responsive images with srcset
   - Lazy load off-screen images

4. **Remove render-blocking resources**
   - Inline critical CSS
   - Defer non-critical CSS
   - Async or defer JavaScript

---

## INP — Interaction to Next Paint

**What:** Time from user interaction to next paint update
**Target:** < 200ms (good), < 500ms (needs improvement)

### Replaces FID (First Input Delay) since March 2024

### Common Causes of Slow INP
- Long JavaScript tasks
- Heavy third-party scripts
- Unoptimized event handlers
- Main thread blocking

### Optimization Tactics

1. **Break up long tasks**
   - Use `requestIdleCallback` for non-critical work
   - Yield to main thread

2. **Optimize event listeners**
   - Use passive listeners for scroll/touch
   - Debounce scroll handlers
   ```javascript
   element.addEventListener('scroll', handler, { passive: true });
   ```

3. **Minimize JavaScript**
   - Remove unused code
   - Code splitting
   - Tree shaking

4. **Optimize third parties**
   - Lazy load analytics
   - Defer non-essential scripts
   - Self-host critical fonts

---

## CLS — Cumulative Layout Shift

**What:** Visual stability during page load
**Target:** < 0.1 (good), < 0.25 (needs improvement)

### Common Causes of CLS
- Images without dimensions
- Ads/embeds without reserved space
- Web fonts causing FOIT/FOUT
- Dynamic content insertion

### Optimization Tactics

1. **Set image dimensions**
   ```html
   <img src="photo.jpg" width="800" height="600" alt="Description">
   ```

2. **Reserve space for ads/iframes**
   ```css
   .ad-container {
     min-height: 250px;
     min-width: 300px;
   }
   ```

3. **Preload fonts**
   ```html
   <link rel="preload" href="/fonts/font.woff2" as="font" type="font/woff2" crossorigin>
   ```

4. **Use `font-display: swap`**
   ```css
   @font-face {
     font-family: 'CustomFont';
     src: url('/fonts/font.woff2') format('woff2');
     font-display: swap;
   }
   ```

5. **Avoid inserting content above existing content**
   - Fixed headers (ok, reserve space)
   - Popup banners (reserve space or use overlay)

---

## Measuring CWV

### Field Data (Real Users)
- **CrUX Report** — Google Chrome User Experience Report
- **Search Console** — Core Web Vitals report
- **PageSpeed Insights** — Field + Lab data
- **Web Vitals Extension** — Chrome extension

### Lab Data (Simulated)
- **Lighthouse** — Local or CI
- **WebPageTest** — Detailed waterfall
- **GTmetrix** — Lab only

### Real User Monitoring
```javascript
import { getCLS, getFID, getLCP } from 'web-vitals';

getCLS(console.log);
getFID(console.log);
getLCP(console.log);
```

---

## Quick Wins Checklist

- [ ] Enable text compression (gzip/Brotli)
- [ ] Optimize images (WebP, responsive)
- [ ] Minimize render-blocking resources
- [ ] Add width/height to images
- [ ] Preload critical resources
- [ ] Lazy load below-fold content
- [ ] Minify CSS/JS
- [ ] Use a CDN
- [ ] Cache aggressively
- [ ] Defer non-critical scripts
