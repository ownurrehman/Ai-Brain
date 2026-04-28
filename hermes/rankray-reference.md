# Rank Ray — Master Reference
Last updated: 2026-04-28

---

## Sites Managed

| Site | CMS | Primary Language |
|------|-----|-----------------|
| rankray.com | WordPress | EN |
| teammotorcycle.com | WordPress | EN |
| tonicphysio.com | WordPress | EN |
| khanllp.com | WordPress | EN |
| coinsfera.com | WordPress | EN |

---

## RankRay WordPress REST API

- **URL:** `https://rankray.com/wp-json/wp/v2/`
- **User:** openclaw
- **App Password:** `6Zz9 5gJL 8uyA QH4g RQDH GV1j`
- **Auth:** Basic HTTP (`curl -u "openclaw:6Zz9 5gJL 8uyA QH4g RQDH GV1j"`)

### Endpoints Used
- Get draft posts: `GET /posts?status=draft&per_page=10`
- Get single post: `GET /posts/{id}` - returns full post JSON
- Update post: `POST /posts/{id}` - requires Basic Auth header
- Get pages: `GET /pages?per_page=100`
- Get all posts: `GET /posts?per_page=100`

---

## Active Tasks / Pending Posts (as of 2026-04-28)

### 4 Draft Posts Awaiting Semantic SEO Review & Internal Linking
| ID | Title | Slug | Status |
|----|-------|------|--------|
| 19919 | Agentic SEO and AI-Driven Growth for Canadian Businesses in 2026 | agentic-seo-ai-driven-growth-canadian-businesses-2026 | Draft — Pending Semantic SEO Audit |
| 19913 | Generative Engine Optimization: Complete GEO Guide for 2026 | generative-engine-optimization-geo-guide | Draft — Pending Semantic SEO Audit |
| 8425 | Top Digital Marketing Trends for 2026 | top-digital-marketing-trends-for-2026 | Draft — Pending Semantic SEO Audit |
| 18913 | Emerging AI Trends Transforming Digital Marketing in 2026 | emerging-ai-trends-transforming-digital-marketing-in-2026 | Draft — Pending Semantic SEO Audit |

### What needs fixing (from initial scan):
- Content needs 100% humanized rewrite (no AI tell-tale signs)
- Heading structure needs semantic SEO alignment
- Internal links need gap analysis against sitemap
- Double dashes found in posts #19919 (10x), #18913 (2x)
- Meta descriptions need audit for <160 chars + exact keyword + LSI + Brand name
- Must remain in Draft until Own approves

---

## Non-Negotiable Rules (from Own)
1. Meta descriptions must be <160 chars, include exact keyword + LSI + "Rank Ray"
2. No double dashes anywhere (`--`)
3. No emojis in any content
4. Internal links verified against rankray.com sitemap only
5. No duplicate internal links on same page
6. Images: <100kb, filename matches alt text (handled by Own)
7. Close all browser tabs immediately after use
8. Content must be 100% humanized, conversational, lead-converting (not AI-sounding)
9. Keep posts in Draft after editing until Own audits and publishes

---

## Command Quick Reference
```bash
# Fetch all draft posts
curl -s -u "openclaw:6Zz9 5gJL 8uyA QH4g RQDH GV1j" "https://rankray.com/wp-json/wp/v2/posts?status=draft&per_page=10"

# Fetch single post
curl -s -u "openclaw:6Zz9 5gJL 8uyA QH4g RQDH GV1j" "https://rankray.com/wp-json/wp/v2/posts/19919" > /tmp/post19919.json

# Update post content
curl -X POST -u "openclaw:6Zz9 5gJL 8uyA QH4g RQDH GV1j" \
  -H "Content-Type: application/json" \
  -d '{"content":"NEW_CONTENT","status":"draft"}' \
  "https://rankray.com/wp-json/wp/v2/posts/19919"
```
