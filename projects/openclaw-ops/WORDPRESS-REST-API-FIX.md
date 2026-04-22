# WordPress REST API Fix — Universal for All Sites

**Date:** 2026-04-21  
**Issue:** REST API authentication failing with 401 errors  
**Root Cause:** Using app password instead of REST API key  
**Solution:** Use REST API key + functions.php snippet

---

## 🔑 The Fix (CRITICAL — ALL AGENTS MUST KNOW)

### Problem
- ❌ **WRONG:** Using `admin:OpenClaw#Admin@2026` (app password)
- ❌ **Result:** 401 errors, Cloudflare Turnstile blocking
- ❌ **Affected:** All WordPress REST API POST requests

### Solution
- ✅ **CORRECT:** Use `openclaw:6Zz9 5gJL 8uyA QH4g RQDH GV1j` (REST API key)
- ✅ **Result:** Full REST API access, no authentication errors
- ✅ **Works on:** All sites (Rank Ray, Tonic Physio, Team Motorcycle, Khan LLP, Coinsfera)

---

## 📝 Authentication Pattern (ALL SITES)

### Rank Ray
```bash
WP_USER="openclaw"
WP_REST_KEY="6Zz9 5gJL 8uyA QH4g RQDH GV1j"
auth=$(echo -n "${WP_USER}:${WP_REST_KEY}" | base64)

# Upload media
curl -X POST "https://rankray.com/wp-json/wp/v2/media" \
  -H "Authorization: Basic ${auth}" \
  -F "file=@image.jpg"

# Create post
curl -X POST "https://rankray.com/wp-json/wp/v2/posts" \
  -H "Authorization: Basic ${auth}" \
  -H "Content-Type: application/json" \
  -d '{"title":"Post","status":"draft"}'
```

### Tonic Physio
```bash
WP_USER="tonicphysioseo@gmail.com"
WP_REST_KEY="4vFk 18fN UlLB twaw B2hU 0kRE"
```

### Team Motorcycle, Khan LLP, Coinsfera
- Check `.env` for `<SITE>_WP_REST_API_KEY`
- Use pattern: `<WP_USER>:<WP_REST_API_KEY>`

---

## 🧩 Yoast SEO REST API Enablement

### functions.php Snippet (REQUIRED — ALL SITES)

Add to WordPress theme's `functions.php`:

```php
// Enable Yoast fields in REST API for posts
add_action('rest_api_init', function() {
    
    // Register Yoast focus keyphrase
    register_rest_field('post', 'yoast_focuskw', [
        'get_callback' => function($post) {
            return get_post_meta($post['id'], '_yoast_focuskw', true);
        },
        'update_callback' => function($value, $post) {
            return update_post_meta($post->ID, '_yoast_focuskw', sanitize_text_field($value));
        },
        'schema' => [
            'description' => 'Yoast SEO Focus Keyphrase',
            'type' => 'string',
        ],
    ]);
    
    // Register Yoast meta description
    register_rest_field('post', 'yoast_metadesc', [
        'get_callback' => function($post) {
            return get_post_meta($post['id'], '_yoast_metadesc', true);
        },
        'update_callback' => function($value, $post) {
            return update_post_meta($post->ID, '_yoast_metadesc', sanitize_text_field($value));
        },
        'schema' => [
            'description' => 'Yoast SEO Meta Description',
            'type' => 'string',
        ],
    ]);
    
    // Register Yoast title
    register_rest_field('post', 'yoast_title', [
        'get_callback' => function($post) {
            return get_post_meta($post['id'], '_yoast_title', true);
        },
        'update_callback' => function($value, $post) {
            return update_post_meta($post->ID, '_yoast_title', sanitize_text_field($value));
        },
        'schema' => [
            'description' => 'Yoast SEO Title',
            'type' => 'string',
        ],
    ]);
    
});
```

### Result
After adding snippet, Yoast fields are writable via REST API:
```python
requests.post(
    "https://rankray.com/wp-json/wp/v2/posts/123",
    headers=HEADERS,
    json={
        "yoast_focuskw": "semantic seo services",
        "yoast_metadesc": "Meta description here",
        "yoast_title": "SEO Title Here"
    }
)
```

---

## 📚 Credential Locations

### Environment Variables (.env)
```bash
# Rank Ray
RANKRAY_WP_USER=openclaw
RANKRAY_WP_APP_PASSWORD=OpenClaw#Admin@2026      # Browser login ONLY
RANKRAY_WP_REST_API_KEY=6Zz9 5gJL 8uyA QH4g RQDH GV1j  # REST API

# Tonic Physio
TONIC_PHYSIO_WP_USER=tonicphysioseo@gmail.com
TONIC_PHYSIO_WP_PASS=RR#Admin@2026
TONIC_PHYSIO_REST_USER=tonicphysioseo@gmail.com
TONIC_PHYSIO_REST_PASS=4vFk 18fN UlLB twaw B2hU 0kRE
```

### Usage Rules
1. **REST API calls** → Use `WP_REST_API_KEY`
2. **Browser automation** → Use `WP_APP_PASSWORD`
3. **Never mix** → They are NOT interchangeable

---

## ✅ Verification Checklist

Before publishing, verify:
- [ ] Using correct credentials (`WP_USER:WP_REST_API_KEY`)
- [ ] functions.php snippet added to WordPress theme
- [ ] Yoast fields accessible via `GET /wp-json/wp/v2/posts/{ID}?context=edit`
- [ ] Media upload working with `POST /wp-json/wp/v2/media`
- [ ] Post creation working with `POST /wp-json/wp/v2/posts`

---

## 🚨 Common Errors

### Error: 401 Unauthorized
**Cause:** Using app password instead of REST API key  
**Fix:** Switch to `WP_USER:WP_REST_API_KEY`

### Error: Yoast fields not in response
**Cause:** functions.php snippet not added  
**Fix:** Add snippet to theme's functions.php

### Error: Cloudflare Turnstile blocking
**Cause:** Trying to use app password with REST API  
**Fix:** Use REST API key (bypasses login)

---

## 📋 Publishing Workflow (Complete)

```python
import requests
import base64

# Credentials
WP_USER = "openclaw"
WP_REST_KEY = "6Zz9 5gJL 8uyA QH4g RQDH GV1j"
WP_BASE = "https://rankray.com/wp-json/wp/v2"

auth = base64.b64encode(f"{WP_USER}:{WP_REST_KEY}".encode()).decode()
HEADERS = {"Authorization": f"Basic {auth}", "Content-Type": "application/json"}

# 1. Upload images
media_ids = []
for img in images:
    with open(img, 'rb') as f:
        resp = requests.post(f"{WP_BASE}/media", headers={"Authorization": HEADERS["Authorization"]}, files={"file": f})
        media_ids.append(resp.json()["id"])

# 2. Create post with Yoast fields
post_data = {
    "title": "Post Title",
    "content": "<p>HTML content</p>",
    "status": "draft",
    "featured_media": media_ids[0],
    "yoast_focuskw": "focus keyphrase",
    "yoast_metadesc": "Meta description",
    "yoast_title": "SEO title"
}
resp = requests.post(f"{WP_BASE}/posts", headers=HEADERS, json=post_data)
post_id = resp.json()["id"]

# 3. Update slug
requests.post(f"{WP_BASE}/posts/{post_id}", headers=HEADERS, json={"slug": "clean-slug"})

# 4. Verify
resp = requests.get(f"{WP_BASE}/posts/{post_id}?context=edit", headers=HEADERS)
print(resp.json())
```

---

## 📖 Related Files

- `MASTER-RULES.md` — Universal SEO rules
- `agents/enigma.md` — SEO agent with WordPress workflow
- `agents/chronos.md` — DevOps agent with API examples
- `agents/researcher.md` — Research agent with verification
- `memory/2026-04-21.md` — Daily activity log with fix documentation
