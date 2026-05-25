---
name: "wordpress-automation"
description: "When the user wants to automate WordPress tasks: REST API operations, plugin management, theme customization, ACF field updates, bulk content operations, database optimization, backup automation, security hardening. Use when the user mentions 'WordPress automation,' 'WP REST API,' 'ACF fields,' 'bulk update,' 'WordPress plugin,' 'theme customization,' 'WP-CLI,' or 'WordPress database.' NOT for general web development (use backend) or Shopify development (use shopify-developer)."
license: MIT
metadata:
  version: 1.0.0
  author: Ranki (adapted from claude-skills)
  category: devops
  updated: 2026-04-21
---

# WordPress Automation

You are an expert in WordPress development and automation. Your goal is to help automate WordPress operations, customize themes/plugins, manage ACF fields, and optimize WordPress performance and security.

## WordPress REST API Reference

### Authentication Methods

| Method | Use Case | Security |
|--------|----------|----------|
| Application Passwords | REST API automation | ✅ Recommended for scripts |
| Cookie authentication | Browser-based | ✅ For logged-in users |
| OAuth 2.0 | Third-party apps | ✅ For public integrations |
| JWT | Headless WordPress | ⚠️ Requires plugin |

**Application Password Setup:**
1. Go to Users → Profile → Application Passwords
2. Enter name (e.g., "OpenClaw Automation")
3. Click "Add New Application Password"
4. Save the generated password (one-time display)
5. Use with username for Basic Auth in API calls

### Common REST API Endpoints

```
GET    /wp-json/wp/v2/pages          - List pages
GET    /wp-json/wp/v2/pages/{id}     - Get single page
POST   /wp-json/wp/v2/pages          - Create page
PUT    /wp-json/wp/v2/pages/{id}     - Update page
DELETE /wp-json/wp/v2/pages/{id}     - Delete page

GET    /wp-json/wp/v2/media          - List media
POST   /wp-json/wp/v2/media          - Upload media
GET    /wp-json/wp/v2/media/{id}     - Get media details

GET    /wp-json/wp/v2/categories     - List categories
GET    /wp-json/wp/v2/tags           - List tags

GET    /acf/v3/pages/{id}            - Get ACF fields (requires ACF to REST)
```

### ACF Field Update via REST API

**Requirements:** ACF to REST API plugin must be installed

**Example: Update ACF fields on a page**
```javascript
const response = await fetch('https://domain.com/wp-json/acf/v3/pages/{id}', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Basic ' + btoa('username:app-password')
  },
  body: JSON.stringify({
    acf: {
      field_name_1: 'value1',
      field_name_2: 'value2',
      // ... all ACF fields
    }
  })
});
```

### Image Upload via REST API

```javascript
// Step 1: Upload image
const formData = new FormData();
formData.append('file', imageBlob);
formData.append('title', 'Image Title');
formData.append('alt_text', 'Descriptive alt text with keyword');

const uploadResponse = await fetch('https://domain.com/wp-json/wp/v2/media', {
  method: 'POST',
  headers: {
    'Authorization': 'Basic ' + btoa('username:app-password')
  },
  body: formData
});

const media = await uploadResponse.json();
const mediaId = media.id;

// Step 2: Attach to page
await fetch(`https://domain.com/wp-json/wp/v2/pages/${pageId}`, {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Authorization': 'Basic ' + btoa('username:app-password')
  },
  body: JSON.stringify({
    featured_media: mediaId
  })
});
```

## WP-CLI Commands

### Content Management
```bash
# List pages
wp post list --post_type=page --fields=ID,post_title,post_status

# Update page content
wp post update {id} --post_content="New content"

# Update post meta (Yoast fields)
wp post meta update {id} _yoast_wpseo_title "SEO Title"
wp post meta update {id} _yoast_wpseo_metadesc "Meta description"

# Bulk update
wp post list --post_type=page --format=ids | xargs -n1 wp post update --post_status=draft
```

### Database Optimization
```bash
# Clean post revisions
wp post delete $(wp post list --post_type=revision --format=ids)

# Clean spam comments
wp comment delete $(wp comment list --status=spam --format=ids)

# Optimize database
wp db optimize

# Export database
wp db export > backup-{date}.sql

# Import database
wp db import backup.sql
```

### Plugin & Theme Management
```bash
# List plugins
wp plugin list

# Activate plugin
wp plugin activate {plugin-name}

# Install plugin
wp plugin install {plugin-name} --activate

# Update all plugins
wp plugin update --all

# List themes
wp theme list

# Switch theme
wp theme activate {theme-name}
```

## ACF Field Groups

### Common ACF Field Types for Service Pages

| Field Name | Type | Purpose |
|------------|------|---------|
| hero_title | Text | Page hero section title |
| hero_subtitle | Textarea | Hero section description |
| service_overview | Wysiwyg | Main service description |
| benefits | Repeater | List of benefits with icon |
| faqs | Repeater | FAQ pairs (question/answer) |
| testimonial | Repeater | Client testimonials |
| cta_text | Text | Call-to-action button text |
| cta_url | Url | CTA button link |
| gallery | Gallery | Image gallery |
| related_services | Post Object | Internal linking |

### ACF Field Validation Checklist

- [ ] All required fields populated
- [ ] No placeholder text left in fields
- [ ] FAQ answers under 200 characters each
- [ ] Paragraphs 400-750+ characters (detailed)
- [ ] Images have alt text
- [ ] Internal links verified (no 404s)
- [ ] External links open in new tab
- [ ] CTA buttons have working URLs

## WordPress Security Hardening

### Essential Security Plugins

| Plugin | Purpose | Priority |
|--------|---------|----------|
| Wordfence | Firewall + malware scan | Critical |
| WP Rocket | Caching + performance | High |
| UpdraftPlus | Backups | Critical |
| ACF to REST API | ACF field access | Medium |
| Yoast SEO | SEO optimization | Critical |
| Redirection | 301 redirects | Medium |

### Security Hardening Checklist

- [ ] Change default admin username (not "admin")
- [ ] Strong passwords enforced
- [ ] Two-factor authentication enabled
- [ ] XML-RPC disabled (if not needed)
- [ ] File editing disabled in wp-config.php
- [ ] Database prefix changed from wp_
- [ ] SSL certificate installed
- [ ] Regular backups scheduled (daily)
- [ ] Security headers configured
- [ ] Login attempt limiting enabled

## Performance Optimization

### Core Web Vitals Optimization

| Metric | Target | Optimization |
|--------|--------|--------------|
| LCP | <2.5s | Optimize images, reduce render-blocking |
| FID | <100ms | Reduce JavaScript execution time |
| CLS | <0.1 | Set image dimensions, avoid layout shifts |

### Speed Optimization Checklist

- [ ] Caching plugin configured (WP Rocket/W3 Total Cache)
- [ ] Image compression (ShortPixel/Smush)
- [ ] Lazy loading enabled
- [ ] CDN configured (Cloudflare/StackPath)
- [ ] Database optimized (revisions cleaned)
- [ ] Unused plugins deactivated
- [ ] Theme optimized (no bloated page builders)
- [ ] Gzip compression enabled
- [ ] Browser caching configured
- [ ] Minify CSS/JS enabled

## Proactive Triggers

Flag these without being asked:
- **ACF to REST API plugin missing** → Can't automate ACF field updates
- **Application password not created** → API authentication blocked
- **Yoast fields not updating via API** → Use WP-CLI workaround
- **No backup system** → Critical risk before bulk operations
- **Plugin update pending** → Security/compatibility risk
- **Database not optimized** → Slow queries, bloated tables

## Output Artifacts

| When you ask for... | You get... |
|---|---|
| REST API script | Node.js/Python script for WordPress automation |
| WP-CLI command | Exact command for task + explanation |
| ACF field mapping | Complete field structure + validation rules |
| Security audit report | Vulnerability scan + hardening checklist |
| Performance optimization plan | Speed fixes prioritized by impact/effort |
| Backup automation setup | Scheduled backup configuration |

## Related Skills

- **security-auditor**: Security scanning before installing plugins/skills
- **backend**: General backend development (APIs, databases)
- **devops**: Infrastructure and deployment (CI/CD, hosting)
- **seo-audit**: SEO optimization (complementary for WordPress SEO)
