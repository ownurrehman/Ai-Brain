# Tonic Physio Service Page SOP (Mandatory)

**Effective:** 2026-04-20  
**Applies to:** All tonicphysio.com service pages  
**Status:** Active - Must follow for every service page update

---

## Step 1: Page Setup (MUST complete before ACF fields)

1. Navigate to the page in wp-admin (`/wp-admin/post.php?post={ID}&action=edit`)
2. Check **page_category** → must include "service page" (term ID: 325)
3. Set **template** → "default template"
4. Update/save the page
   - Use REST API if possible: `POST /wp-json/wp/v2/pages/{ID}` with `page_category` and `template`
   - Fall back to browser automation if REST is restricted

**REST API Example:**
```json
{
  "page_category": [325],
  "template": "default template"
}
```

---

## Step 2: ACF Field Content Requirements

Write well-researched content for each field with appropriate word lengths:

### Header Section
| Field | Word Count | Notes |
|-------|------------|-------|
| h1 | 5-8 words | Main headline, include primary keyword |
| paragraph_1 | 60-100 words | Intro paragraph, pain point + solution |
| h2 | 4-7 words | Section heading, benefit-focused |
| paragraph_2 | 80-120 words | Supporting paragraph, approach explanation |

### Why Choose Us Section
| Field | Word Count | Notes |
|-------|------------|-------|
| why_choose_us_point_1 | 10-20 words | Unique benefit #1 |
| why_choose_us_point_2 | 10-20 words | Unique benefit #2 |
| why_choose_us_point_3 | 10-20 words | Unique benefit #3 |
| why_choose_us_point_4 | 10-20 words | Unique benefit #4 |
| why_choose_us_point_5 | 10-20 words | Unique benefit #5 |
| why_choose_us_image | N/A | Upload landscape image to media library |
| paragraph_after_why_choose_us | 40-60 words | Bridge paragraph, transition to solutions |

### Solutions Section
| Field | Word Count | Notes |
|-------|------------|-------|
| h2_second | 5-8 words | Section heading |
| paragraph_for_h2_second | 60-90 words | Intro to treatment methods |
| solution_1 | 12-18 words | Treatment method #1 |
| solution_2 | 12-18 words | Treatment method #2 |
| solution_3 | 12-18 words | Treatment method #3 |
| solution_4 | 12-18 words | Treatment method #4 |
| solution_5 | 12-18 words | Treatment method #5 |
| solutions_image | N/A | Upload landscape image to media library |
| paragraph_after_detailed_box | 40-60 words | Closing paragraph for solutions |

### Process Section
| Field | Word Count | Notes |
|-------|------------|-------|
| h2_third | 3-6 words | "Service Process" or similar |
| paragraph_for_h2_third | 60-90 words | Process overview, steps explained |

### CTA Section
| Field | Word Count | Notes |
|-------|------------|-------|
| h2_fourth | 4-7 words | "Looking for [Service Name]" |
| paragraph_for_h2_fourth | 50-80 words | CTA intro, urgency/benefit |
| h3_first | 3-5 words | "Book a Session" or similar |
| paragraph_for_h3_first | 50-70 words | Booking CTA, clear next step |

### FAQ Section
| Field | Word Count | Notes |
|-------|------------|-------|
| faq_heading | N/A | "[Service Name] FAQs" |
| faq_q1 through q10 | 5-12 words each | Common questions |
| faq_a1 through a10 | 25-50 words each | Clear, helpful answers |

---

## Step 3: Image Upload Rules

1. **Before uploading:** Check media library for existing suitable images (avoid duplicates)
2. **Image specs:** Landscape orientation preferred, optimized WebP/JPG, descriptive filename
3. **Alt text:** Mandatory, include relevant keywords naturally
4. **Upload method:** Use WordPress REST API for media:
   ```bash
   POST /wp-json/wp/v2/media
   Content-Type: multipart/form-data
   ```
5. **Assign to ACF:** Use the media ID returned from upload

---

## Step 4: Completion Rule

**DO NOT mark page as done until:**
- [ ] page_category includes "service page"
- [ ] template is set to "default template"
- [ ] ALL 47 ACF fields are filled (no empty fields)
- [ ] Both images uploaded and assigned
- [ ] Frontend HTML verified (no empty gaps)
- [ ] Yoast SEO fields set (meta title <60 chars, meta description <160 chars)

**Verification command:**
```bash
curl -s "https://tonicphysio.com/wp-json/wp/v2/pages/{ID}?context=edit" \
  --user "{user}:{app_password}" | jq '.acf'
```

---

## Notes

- REST API for ACF fields may be restricted; use browser automation (Playwright) if needed
- All content must be human-level English, no AI fluff or repetition
- Maintain consistent tone: professional, empathetic, action-oriented
- Always verify frontend HTML after update to ensure no empty gaps
- Log completion to Discord bot-logs channel

---

## Common Issues & Fixes

| Issue | Solution |
|-------|----------|
| REST API returns 401 for ACF | Use browser automation instead |
| ACF fields not showing on frontend | Clear cache, verify template is "default template" |
| Images not displaying | Check alt text is set, verify media ID in ACF |
| Yoast fields not updating via REST | Enable "REST API: Head endpoint" in Yoast settings |
