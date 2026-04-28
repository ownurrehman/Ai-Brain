# Technical SEO Checklist — 200 Points

## Page-Level Elements (100 points)

### Title Tags (15 points)
- [ ] Exists on every page
- [ ] Under 60 characters
- [ ] Primary keyword in first 55 chars
- [ ] Descriptive and unique
- [ ] Brand at end (optional but preferred)

### Meta Descriptions (10 points)
- [ ] Exists on every page
- [ ] Under 160 characters
- [ ] Compelling click-worthy copy
- [ ] Primary keyword included naturally
- [ ] No double dashes

### Headings (15 points)
- [ ] One H1 per page exactly
- [ ] H1 contains primary keyword
- [ ] Logical H2 → H3 hierarchy
- [ ] No skipped levels (H1 to H3 without H2)
- [ ] Descriptive, not generic labels

### Content (20 points)
- [ ] Minimum 300 words on indexable pages
- [ ] Primary keyword in first 100 words
- [ ] LSI keywords naturally included
- [ ] Proper paragraph breaks
- [ ] Bullet lists for scannability

### Images (10 points)
- [ ] Alt text on all images
- [ ] Descriptive filenames
- [ ] File size <100KB or compressed
- [ ] Width/height attributes set
- [ ] Lazy loading for below-fold

### URLs (10 points)
- [ ] Lowercase characters only
- [ ] Hyphens, not underscores
- [ ] No unnecessary parameters
- [ ] Canonical self-referencing
- [ ] HTTPS enforced

### Links (10 points)
- [ ] Working internal links
- [ ] Descriptive anchor text
- [ ] No more than one link per target page
- [ ] No orphaned important pages
- [ ] Logical link depth (<4 clicks from home)

### Technical Markup (10 points)
- [ ] Valid HTML structure
- [ ] Proper viewport meta
- [ ] Language attribute set
- [ ] Encoding declared (UTF-8)
- [ ] No render-blocking resources

## Site-Wide Elements (60 points)

### XML Sitemap (15 points)
- [ ] Sitemap.xml exists and accessible
- [ ] Submitted to Search Console
- [ ] Under 50,000 URLs
- [ ] Lastmod dates accurate
- [ ] No non-indexable URLs included

### robots.txt (10 points)
- [ ] File exists and accessible
- [ ] Sitemap referenced
- [ ] No "Disallow: /" blocking crawl
- [ ] No important resources blocked
- [ ] Wildcard patterns correct

### Schema Markup (20 points)
- [ ] Organization schema on site
- [ ] Appropriate page-specific schemas
- [ ] Valid JSON-LD format
- [ ] No syntax errors
- [ ] Rich results test passed

### Navigation (15 points)
- [ ] Logical site structure
- [ ] Breadcrumbs where appropriate
- [ ] No infinite scroll without proper handling
- [ ] Pagination correctly marked
- [ ] Mobile-friendly navigation

## Performance & Security (40 points)

### Core Web Vitals (20 points)
- [ ] LCP < 2.5 seconds
- [ ] INP < 200 milliseconds
- [ ] CLS < 0.1
- [ ] No render-blocking JS
- [ ] Critical CSS inlined

### Mobile Usability (10 points)
- [ ] Responsive design
- [ ] Touch targets >48px
- [ ] No horizontal scroll
- [ ] Readable font sizes
- [ ] Viewport configured correctly

### Security (10 points)
- [ ] SSL certificate valid
- [ ] HTTPS redirects enforced
- [ ] No mixed content warnings
- [ ] Security headers present
- [ ] No sensitive data in URLs

## Scoring Guide

| Score | Grade | Action |
|-------|-------|--------|
| 180-200 | A+ | Monitor only |
| 160-179 | A | Minor improvements |
| 140-159 | B | Address warnings |
| 120-139 | C | Prioritize fixes |
| <120 | F | Critical audit needed |
