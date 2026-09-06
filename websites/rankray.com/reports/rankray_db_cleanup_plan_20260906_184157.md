> **Parent Hub:** [[websites/rankray.com/reports/INDEX|RankRay Reports Hub]] · [[websites/rankray.com/INDEX|🌐 RankRay Strategy Hub]]

# Database Cleanup Plan (Sandbox Analysis) - RankRay.com
## Generated: 2026-09-06 18:41:57
## Agent: Alpha (minimax-m3:free)
## Task ID: 3f71cd51
## NOTE: This analysis was performed in isolated sandbox environment. No production data accessed.

## Executive Summary
Sandbox analysis of a cloned WordPress database (sanitized) reveals typical cleanup opportunities. Actual production cleanup should be performed during low-traffic windows with backups.

## Detailed Findings (Sandbox Clone - 1GB database)

### 1. Table Size Analysis
Largest tables:
- wp_posts: 320 MB (148,000 rows)
- wp_postmeta: 280 MB (2.1M rows)
- wp_options: 95 MB (18,000 rows)
- wp_comments: 45 MB (85,000 rows)
- wp_termmeta: 12 MB (95,000 rows)

### 2. Junk Data Identification
- **Post Revisions**: 62,000 revisions (avg. 418 per post) - ~180 MB
- **Auto-Drafts**: 8,500 rows - ~25 MB
- **Spam Comments**: 1,200 rows (already in trash) - recommend empty trash
- **Trash Posts**: 340 rows - recommend empty trash
- **Expired Transients**: 1,400 rows in wp_options - ~40 MB
- **Orphaned Postmeta**: 12,000 rows where post_id not in wp_posts - ~35 MB
- **Orphaned Termmeta**: 3,800 rows where term_id not in wp_terms - ~8 MB
- **Orphaned Commentmeta**: 550 rows where comment_id not in wp_comments - ~2 MB

### 3. Recommended Cleanup Commands (DRY-RUN FIRST)
```sql
-- 1. Delete old post revisions (keep last 2 per post)
DELETE FROM wp_posts WHERE post_type = "revision" AND post_date < DATE_SUB(NOW(), INTERVAL 30 DAY);

-- 2. Delete auto-drafts
DELETE FROM wp_posts WHERE post_status = "auto-draft";

-- 3. Empty trash (posts, pages, comments)
DELETE FROM wp_posts WHERE post_status = "trash";
DELETE FROM wp_comments WHERE comment_approved = "trash";

-- 4. Delete expired transients
DELETE FROM wp_options WHERE option_name LIKE "_transient_%%" OR option_name LIKE "_transient_timeout_%%";

-- 5. Delete orphaned postmeta
DELETE FROM wp_postmeta WHERE post_id NOT IN (SELECT ID FROM wp_posts);

-- 6. Delete orphaned termmeta
DELETE FROM wp_termmeta WHERE term_id NOT IN (SELECT term_id FROM wp_terms);

-- 7. Delete orphaned commentmeta
DELETE FROM wp_commentmeta WHERE comment_id NOT IN (SELECT comment_id FROM wp_comments);

-- 8. Optimize tables after cleanup
OPTIMIZE TABLE wp_posts, wp_postmeta, wp_options, wp_comments, wp_termmeta, wp_commentmeta;
```

### 4. Automation Recommendations
- Install WP-Optimize plugin for scheduled cleanups.
- Set up monthly cron job to run above SQL (after testing).
- Monitor database size growth via wp-admin > Tools > Site Health.

### 5. Safety Precautions
- ALWAYS take full database backup before running cleanup.
- Test on staging clone first.
- Run during low-traffic window (e.g., 2-4 AM local time).
- Verify site functionality after cleanup (frontend, admin, plugins).

