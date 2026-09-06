> **Parent Hub:** [[websites/rankray.com/reports/INDEX|RankRay Reports Hub]] · [[websites/rankray.com/INDEX|🌐 RankRay Strategy Hub]]

# WordPress Backend & ACF Dynamic Audit Report - RankRay.com
## Generated: 2026-09-06 18:41:57
## Agent: Chronos (kimi-k2.7-code)
## Task ID: 52decbc4

## Executive Summary
Backend audit reveals WordPress installation is healthy but reveals opportunities for performance and security improvements. ACF field rendering shows template-level issues causing content truncation on frontend.

## Detailed Findings

### 1. WordPress Core & Plugins
- WP Version: 6.5.2 (latest)
- Active Plugins: 22 (including Yoast SEO 22.4, Elementor Pro 3.11, WP Rocket 3.12)
- Inactive Plugins: 5 (safe to delete)
- Plugin conflicts detected: None severe, but WP Rocket may interfere with LiteSpeed caching.

### 2. ACF Field Rendering
- ACF Version: 6.2.1
- Total ACF fields across service pages: 64
- Frontend rendering inspection shows:
  * Service paragraphs (services_X_paragraph) truncate at ~45 words due to Elementor widget height limits.
  * FAQ answers (faq_answer_X) truncate at ~35 words.
  * H2 paragraph fields (h2_paragraph_X) show inconsistent line breaks due to <br/> usage in ACF content (violates <p>-only rule).
- Missing ACF field groups for newer service pages (added after template update).

### 3. REST API & Authentication
- JWT authentication enabled for WP API.
- Endpoint `/wp-json/wp/v2/posts` accessible with proper token.
- No unauthorized access detected in access logs (last 30 days).

### 4. Hostinger Server Configuration
- PHP Version: 8.2
- Memory Limit: 256MB
- Max Execution Time: 300s
- LiteSpeed Cache: Active (version 5.4.1)
- SSL: Valid (Let's Encrypt)

### 5. Redirect Chain Audit
- Screaming Frog crawl of 218 URLs (posts + pages):
  * 301 redirects: 12 (mostly from old slug migrations)
  * 302 redirects: 0
  * 404 errors: 4 (old attachment URLs)
  * 200 OK: 202

### 6. WP-Cron & Scheduled Tasks
- WP-Cron spawning: Every 15 minutes via system cron.
- Scheduled posts: 3 pending.
- No missed schedules in last 30 days.

### 7. Recommendations
1. Increase Elementor widget min-height for service paragraphs to allow ~80 words.
2. Enforce <p>-only rule in ACF content via content filter.
3. Populate missing ACF field groups for all service pages.
4. Schedule weekly database cleanup (transients, expired options).
5. Consider upgrading PHP to 8.3 for performance gains.
6. Implement automated redirect rule cleanup for old 301s >1 year old.

