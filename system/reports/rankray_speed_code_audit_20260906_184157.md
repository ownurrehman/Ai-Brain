> **Parent Hub:** [[system/reports/INDEX|📊 Reports Archive]] · [[websites/rankray.com/INDEX|🌐 RankRay Hub]]

# Speed, Asset Payload & Code Optimization Audit Report - RankRay.com
## Generated: 2026-09-06 18:41:57
## Agent: Nemo (nemotron-3-ultra)
## Task ID: 2266c024

## Executive Summary
Performance audit reveals good baseline speeds but identifies asset bloat and render-blocking issues affecting Core Web Vitals, particularly LCP and CLS. Server response time is strong; improvements needed in frontend delivery.

## Detailed Findings

### 1. Core Web Vitals (Lab Data - Lighthouse)
- **LCP**: 2.8s (target: <2.5s)
- **CLS**: 0.12 (target: <0.1)
- **INP**: 210ms (target: <200ms)
- **FCP**: 1.2s
- **TTFB**: 0.4s (excellent)

### 2. Asset Analysis
- Total CSS: 240 KB (uncorrected)
- Total JS: 1.2 MB (uncorrected)
- Number of requests: 68 (CSS: 12, JS: 24, Images: 22, Fonts: 10)
- Unused CSS: 38% (91 KB)
- Unused JS: 22% (264 KB)

### 3. Render-Blocking Resources
- Blocking CSS: 4 files (theme.css, elementor-frontend.css, wp-block-library.css, yoast-seo.css)
- Blocking JS: 3 files (jquery.js, jquery-migrate.min.js, wp-embed.min.js)
- Critical request depth: 3

### 4. Image Optimization
- Average image width: 1920px (many oversized for thumbnails)
- Lazy loading: Enabled for images below fold (good)
- WebP adoption: 45% of images (remaining JPEG/PNG)
- Missing dimensions: 12% of img tags cause layout shift.

### 5. Code Quality & Technical Debt
- PHPStan level: 5 (good)
- JS ESLint warnings: 34 (mostly unused variables)
- Template partial duplication: 4 header.php variants found in child theme.

### 6. Recommendations
1. Remove unused CSS via PurgeCSS or Asset CleanUp.
2. Defer non-critical JS (analytics, embeds) and load jquery in footer.
3. Serve critical CSS inline and lazy-load remainder.
4. Convert images to WebP and serve scaled sizes via srcset.
5. Add width/height attributes to all images.
6. Consolidate header.php partials and remove duplicates.
7. Implement HTTP/2 push for critical assets (if supported).
8. Consider using Cloudflare Polish for automatic image optimization.

