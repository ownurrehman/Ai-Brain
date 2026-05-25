# Rank Ray Location Pages — Rules & Reference

## Overview
Rank Ray has **37 location pages** split across two Custom Post Types (CPTs) with ACF custom fields:
- **SEO Agency pages** (slug prefix: `seo-agency-`)
- **Digital Marketing Agency pages** (slug prefix: `digital-marketing-agency-`)

Both types are fully dynamic — content is populated via ACF fields, NOT the WordPress content editor.

---

## Custom Post Type: `location-page`

| Property | Value |
|----------|-------|
| CPT Slug | `location-page` |
| REST Base | `location-page` |
| Icon | dashicons-location |
| Taxonomy | `service-type` |
| Hierarchical | false |
| Archive | false |

---

## Page Types

### 1. SEO Agency Pages (19 locations)
URL pattern: `https://rankray.com/seo-agency-{city}/`

| ID | City | Slug | Country |
|----|------|------|---------|
| 18020 | Dubai | seo-agency-dubai | UAE |
| 19284 | Abu Dhabi | seo-agency-abu-dhabi | UAE |
| 19299 | Sharjah | seo-agency-sharjah | UAE |
| 19300 | Ajman | seo-agency-ajman | UAE |
| 19302 | Doha | seo-agency-doha | Qatar |
| 19303 | Muscat | seo-agency-muscat | Oman |
| 19283 | Houston | seo-agency-houston | USA |
| 19282 | Dallas | seo-agency-dallas | USA |
| 19281 | Miami | seo-agency-miami | USA |
| 19285 | Austin | seo-agency-austin | USA |
| 19251 | Toronto | seo-agency-toronto | Canada |
| 19253 | New York | seo-agency-new-york | USA |
| 19254 | Los Angeles | seo-agency-los-angeles | USA |
| 19255 | Chicago | seo-agency-chicago | USA |
| 19246 | Milton | seo-agency-milton | Canada |
| 19252 | Vancouver | seo-agency-vancouver | Canada |
| 19307 | Calgary | seo-agency-calgary | Canada |
| 19308 | Ottawa | seo-agency-ottawa | Canada |
| 19309 | Mississauga | seo-agency-mississauga | Canada |
| 19311 | Seattle | seo-agency-seattle | USA |

### 2. Digital Marketing Agency Pages (17 locations)
URL pattern: `https://rankray.com/digital-marketing-agency-{city}/`

| ID | City | Slug | Country |
|----|------|------|---------|
| 17828 | Dubai | digital-marketing-agency-dubai | UAE |
| 17833 | Abu Dhabi | digital-marketing-agency-abu-dhabi | UAE |
| 17832 | London | digital-marketing-agency-london | UK |
| 17831 | New York | digital-marketing-agency-new-york | USA |
| 17835 | Toronto | digital-marketing-agency-toronto | Canada |
| 17834 | Milton | digital-marketing-agency-milton | Canada |
| 17989 | Karachi | digital-marketing-agency-karachi | Pakistan |
| 17990 | Lahore | digital-marketing-agency-lahore | Pakistan |
| 17991 | Sydney | digital-marketing-agency-sydney | Australia |
| 17992 | Los Angeles | digital-marketing-agency-los-angeles | USA |
| 17993 | Chicago | digital-marketing-agency-chicago | USA |
| 17994 | Houston | digital-marketing-agency-houston | USA |
| 17995 | Dallas | digital-marketing-agency-dallas | USA |
| 17996 | Miami | digital-marketing-agency-miami | USA |
| 17998 | Vancouver | digital-marketing-agency-vancouver | Canada |
| 15826 | Islamabad | digital-marketing-agency-islamabad | Pakistan |
| 18016 | Rawalpindi | digital-marketing-agency-rawalpindi | Pakistan |

---

## ACF Field Structure

### SEO Agency Pages — ACF Fields

#### Location Metadata
| Field | Type | Purpose |
|-------|------|---------|
| `location` | Text | City name (e.g., "Toronto") |
| `state` | Text | State/Province (e.g., "Ontario") |
| `country` | Text | Country name (e.g., "Canada") |
| `select_service` | Select | Service type ["SEO"] |

#### Hero Section
| Field | Type | Purpose |
|-------|------|---------|
| `location_h1` | Text | H1 heading (e.g., "Top-Rated SEO Agency in Toronto") |
| `below_h1` | WYSIWYG | Paragraph below H1 with intro text |
| `H2` | Text | First H2 heading |
| `H2_Paragraph_1` | WYSIWYG | First paragraph under H2 |
| `H2_Paragraph_2` | WYSIWYG | Second paragraph under H2 |

#### Portfolio/Case Study Section
| Field | Type | Purpose |
|-------|------|---------|
| `Portfolio_Heading` | Text | Portfolio section heading |
| `portfolio_paragraph` | WYSIWYG | Portfolio description text |

#### Proposal Form Section
| Field | Type | Purpose |
|-------|------|---------|
| `SEO_proposal_heading` | Text | Form section heading |
| `SEO_proposal_paragraph` | WYSIWYG | Form description text |

#### Services Section
| Field | Type | Purpose |
|-------|------|---------|
| `SEO_Services_Heading` | Text | Services section H2 |
| `SEO_Services_Paragraph` | WYSIWYG | Services intro paragraph |
| `seo_service_1_heading` to `seo_service_9_heading` | Text | 9 service card headings |
| `seo_service_1_paragraph` to `seo_service_9_paragraph` | WYSIWYG | 9 service card descriptions |
| `seo_quote` | Text | Quote/callout text |

#### Why Choose Us Section
| Field | Type | Purpose |
|-------|------|---------|
| `why_Our_SEO_heading` | Text | Section heading |
| `why_Our_SEO_paragraph` | WYSIWYG | Section description |
| `1_blue_box_heading` to `6_green_box_heading` | Text | 6 feature box headings (color-coded) |
| `1_blue_box_paragraph` to `6_green_box_paragraph` | WYSIWYG | 6 feature box descriptions |

#### Footer Form
| Field | Type | Purpose |
|-------|------|---------|
| `last_form_heading` | Text | Footer form heading |

---

### Digital Marketing Agency Pages — ACF Fields

#### Location Metadata
| Field | Type | Purpose |
|-------|------|---------|
| `location` | Text | City name |
| `state` | Text | State/Province |
| `country` | Text | Country |
| `select_service` | Select | Service type ["Digital Marketing"] |

#### Hero Section
| Field | Type | Purpose |
|-------|------|---------|
| `location_h1` | Text | H1 heading |
| `below_h1` | WYSIWYG | Paragraph below H1 |
| `first_paragraph` | WYSIWYG | First paragraph |
| `below_first_p_long_description` | WYSIWYG | Extended description |

#### Services Section
| Field | Type | Purpose |
|-------|------|---------|
| `first_h2` | Text | Services section H2 |
| `first_h2_paragraph` | WYSIWYG | Services intro |
| `second_h2_services` | Text | Second services H2 |
| `small_services_paragraph` | WYSIWYG | Services summary |

#### Service Descriptions (8 services)
| Field | Type | Purpose |
|-------|------|---------|
| `seo_service_description` | WYSIWYG | SEO service description |
| `smm_description` | WYSIWYG | Social Media Marketing description |
| `ppc_description` | WYSIWYG | PPC description |
| `web_dev_description` | WYSIWYG | Web Development description |
| `content_writing_description` | WYSIWYG | Content Writing description |
| `video_production_description` | WYSIWYG | Video Production description |
| `ai_automation_description` | WYSIWYG | AI Automation description |
| `cro_description` | WYSIWYG | CRO description |
| `email_marketing` | WYSIWYG | Email Marketing description |

#### Why Choose Us Section
| Field | Type | Purpose |
|-------|------|---------|
| `why_choose_us_heading` | Text | Section heading |
| `why_choose_us_paragraph` | WYSIWYG | Main description |
| `why_choose_us_paragraph_2` | WYSIWYG | Secondary description |
| `why_choose_us_paragraph_3` | WYSIWYG | Tertiary description |
| `why_choose_first_service_heading` to `why_choose_sixth_service_heading` | Text | 6 service headings |
| `why_choose_first_service_paragraph` to `why_choose_sixth_service_paragraph` | WYSIWYG | 6 service descriptions |

#### Awards Section
| Field | Type | Purpose |
|-------|------|---------|
| `rank_ray_awards_heading_h3` | Text | Awards heading |
| `rank_ray_awards_paragraph` | WYSIWYG | Awards description |

#### Footer Form
| Field | Type | Purpose |
|-------|------|---------|
| `last_form_heading` | Text | Footer form heading |

---

## Yoast SEO Fields

| Field | Source | Notes |
|-------|--------|-------|
| `_yoast_wpseo_focuskw` | ACF/Post meta | Focus keyword |
| `_yoast_wpseo_title` | Post meta | Custom title (empty = auto) |
| `_yoast_wpseo_metadesc` | Post meta | Meta description (<160 chars) |

---

## Content Rules (Non-Negotiable)

1. **No emojis** anywhere
2. **No double dashes** (`--`) — use proper punctuation
3. **Meta descriptions**: <160 chars, exact match KWD + LSI + "Rank Ray"
4. **Internal links**: Verified against sitemap, max 1 link per page per post
5. **Anchor text**: "Rank Ray" → homepage only. Others: descriptive
6. **Images**: <100kb, WebP preferred, matching filename/alt text
7. **H1**: Only one per page. Include exact match keyword near front
8. **H2s**: Minimum 4, include LSI keywords naturally
9. **First paragraph**: Must include exact match keyword in first 100 words
10. **City name**: Must appear naturally throughout all fields
11. **Dynamic templating**: Use `[acf field="location"]` for city name substitution in templates

---

## WordPress REST API Endpoints

### Base URL
```
https://rankray.com/wp-json/wp/v2/
```

### Authentication
```bash
USER="openclaw"
APP_PASSWORD="6Zz9 5gJL 8uyA QH4g RQDH GV1j"
AUTH=$(echo -n "$USER:$APP_PASSWORD" | base64)
```

### Key Endpoints

| Action | Endpoint | Method |
|--------|----------|--------|
| List all location pages | `/location-page?per_page=100` | GET |
| Get single location page | `/location-page/{id}` | GET |
| Update location page | `/location-page/{id}` | POST |
| Update with ACF | `/location-page/{id}?_fields=acf` | POST |

### Update Example
```bash
curl -X POST \
  -u "openclaw:6Zz9 5gJL 8uyA QH4g RQDH GV1j" \
  -H "Content-Type: application/json" \
  -d '{
    "acf": {
      "location_h1": "Top-Rated SEO Agency in Vancouver",
      "below_h1": "New intro text...",
      "H2": "SEO Services That Help Vancouver Businesses Rank Higher"
    }
  }' \
  "https://rankray.com/wp-json/wp/v2/location-page/19252"
```

---

## Page Template Structure (Frontend)

### SEO Agency Page Layout
1. **Hero**: H1 + below_h1 + Contact Form
2. **Breadcrumb**: Home / SEO Agency in {City}
3. **Services Intro**: H2 + paragraphs
4. **Client Logos Carousel**: Dynamic
5. **Case Studies**: 3 cards (Law Firm, Real Estate, Ecommerce)
6. **Proposal Form**: Location-specific form
7. **Detailed Services**: 9 service cards with icons
8. **Why Choose Us**: 6 color-coded boxes
9. **CTA Block**: Final conversion prompt with phone/form

### Digital Marketing Page Layout
1. **Hero**: H1 + below_h1 + Contact Form
2. **Breadcrumb**: Home / Digital Marketing Agency in {City}
3. **Services Overview**: H2 + paragraph
4. **Service Cards**: 8 services with icons
5. **Stats**: 4 counters (Clients, Revenue, Keywords, Countries)
6. **Testimonials**: 3 reviews
7. **CTA**: Free Proposal button
8. **Why Choose Us**: 6 service sections
9. **Awards**: Recognition section
10. **Footer Form**: Final CTA

---

## Last Updated
2026-05-08 by Enigma
