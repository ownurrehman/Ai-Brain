# TonicPhysio Service Page Creation Protocol

## Overview
TonicPhysio.com service pages are built using a structured ACF (Advanced Custom Fields) template. The page content is NOT in the standard WordPress editor. Instead, it's entirely driven by ACF fields via the WordPress REST API.

## Critical Steps for Creating a New Service Page

### Step 1: Create the Page
- POST to `https://tonicphysio.com/wp-json/wp/v2/pages`
- Set `title` (service name)
- Set `status` to `draft`
- Set `page_category` to `[325]` (Service Page)
- Set `template` to `services-pages.php`
- **Important:** The template name must be `services-pages.php` exactly (not "tonic-physio-service")

### Step 2: Populate ACF Fields
After page creation, all ACF fields are automatically available. Update them via:
```
PUT https://tonicphysio.com/wp-json/wp/v2/pages/{page_id}
Content-Type: application/json
Authorization: Basic Dan:NMwZ 1LyJ YgbE fUjs pUYn 4SoZ
```

## Complete ACF Field Structure

### Header Section
| Field | Type | Purpose |
|-------|------|---------|
| `h1` | text | Main H1 heading |
| `paragraph_1` | text | Intro paragraph |

### First Content Block
| Field | Type | Purpose |
|-------|------|---------|
| `h2` | text | First H2 heading |
| `paragraph_2` | text | Content under first H2 |

### Why Choose Us Section
| Field | Type | Purpose |
|-------|------|---------|
| `why_choose_us_point_1` | text | Bullet point 1 |
| `why_choose_us_point_2` | text | Bullet point 2 |
| `why_choose_us_point_3` | text | Bullet point 3 |
| `why_choose_us_point_4` | text | Bullet point 4 |
| `why_choose_us_point_5` | text | Bullet point 5 |
| `why_choose_us_image` | image ID | Image for section |
| `paragraph_after_why_choose_us` | text | Transition text |

### Second Content Block
| Field | Type | Purpose |
|-------|------|---------|
| `h2_second` | text | Second H2 heading |
| `paragraph_for_h2_second` | text | Content under second H2 |

### Solutions/Benefits Section
| Field | Type | Purpose |
|-------|------|---------|
| `solution_1` | text | Benefit/solution 1 |
| `solution_2` | text | Benefit/solution 2 |
| `solution_3` | text | Benefit/solution 3 |
| `solution_4` | text | Benefit/solution 4 |
| `solution_5` | text | Benefit/solution 5 |
| `solutions_image` | image ID | Image for solutions |
| `paragraph_after_detailed_box` | text | Transition text |

### Third & Fourth Content Blocks
| Field | Type | Purpose |
|-------|------|---------|
| `h2_third` | text | Third H2 heading |
| `paragraph_for_h2_third` | text | Content under third H2 |
| `h2_fourth` | text | Fourth H2 heading |
| `paragraph_for_h2_fourth` | text | Content under fourth H2 |

### CTA Section
| Field | Type | Purpose |
|-------|------|---------|
| `h3_first` | text | Call-to-action H3 |
| `paragraph_for_h3_first` | text | CTA paragraph |

### FAQ Section
| Field | Type | Purpose |
|-------|------|---------|
| `faq_heading` | text | FAQ section title |
| `faq_q1` - `faq_q10` | text | FAQ questions (10 available) |
| `faq_a1` - `faq_a10` | text | FAQ answers (10 available) |
| `more_faqs` | text | Additional FAQ link (optional) |

## API Authentication
- **Username:** Dan
- **App Password:** NMwZ 1LyJ YgbE fUjs pUYn 4SoZ
- **Endpoint:** https://tonicphysio.com/wp-json/wp/v2/

## Image Requirements
- Maximum file size: 100kb
- File name and alt text must match page name (SEO optimized)
- Upload via media endpoint first, then use image ID in ACF fields
- No hotlinking; upload to WP Media Library

## Page Category Taxonomy
- **ID:** 325
- **Name:** Service Page
- **Slug:** service_page
- **Taxonomy:** page_category

## Workflow for New Service Pages
1. Create page with correct template and category
2. Upload featured image to media library
3. Generate SEO-optimized content for all ACF fields
4. Populate ACF fields via REST API
5. Set Yoast SEO fields (focus keyphrase, meta description, SEO title)
6. Review and publish

## Reference Page
- **Example:** Sports Physiotherapy (ID: 11895)
- **URL:** https://tonicphysio.com/physiotherapy-in-milton/sports-physiotherapy/
- **Template:** services-pages.php
- **Category:** Service Page (325)

## Important Notes
- The standard WordPress `content` field is always empty for these pages
- All content lives in ACF fields only
- Template must be set correctly for ACF fields to appear
- Page category must be set to 325 for service page functionality
- Always save as draft first, then update fields, then publish after review

## Test Page
- **ID:** 12403
- **Title:** Test Service Page Enigma
- **Status:** Draft
- **Created:** 2026-04-30
- **URL:** https://tonicphysio.com/?page_id=12403

## Files
- **Protocol Doc:** `tonicphysio/service-page-protocol.md`
- **Workspace:** `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw/tonicphysio/`
