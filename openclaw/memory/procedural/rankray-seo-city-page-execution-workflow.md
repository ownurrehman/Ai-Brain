# Rank Ray SEO City Page Execution Workflow

## Canonical source recovered from prior batch artifacts
Recovered from workspace implementation files used for Batch 1 and Batch 2:
- `tmp/batch1_seo_city_pages.js`
- `tmp/batch2_seo_city_pages.js`
- `tmp/fix_h2_paragraph2_batch1.js`
- `tmp/batch1_images.py`
- `tmp/fix_batch1_browser.py`
- `tmp/set_meta_batch1.py`

## Actual working flow used previously

### 1. Create location-page drafts via WordPress REST
Endpoint:
- `POST https://rankray.com/wp-json/wp/v2/location-page`

Auth used previously:
- user: `openclaw`
- application password stored in local artifact scripts

Payload structure used:
- title: `SEO Agency in {City}`
- slug: `seo-agency-{city}`
- status: `draft`
- excerpt: city-specific SEO agency summary
- acf object containing:
  - `location`
  - `state`
  - `country`
  - `select_service` initially blank in REST payload
  - `location_h1`
  - `below_h1`
  - `H2`
  - `H2_Paragraph_1`
  - `H2_Paragraph_2`
  - `Portfolio_Heading`
  - `portfolio_paragraph`
  - `SEO_proposal_heading`
  - `SEO_proposal_paragraph`
  - `SEO_Services_Heading`
  - `SEO_Services_Paragraph`
  - `seo_service_1_heading` ... `seo_service_9_heading`
  - `seo_service_1_paragraph` ... `seo_service_9_paragraph`
  - `seo_quote`
  - `why_Our_SEO_heading`
  - `why_Our_SEO_paragraph`
  - `Q1/a1` through `q7/a7`
  - `last_form_heading`

### 2. Improve H2 Paragraph 2 where needed
A later refinement updated `H2_Paragraph_2` with the longer version:
- strategic
- more commercial
- better visual balance for the page layout

This was done via REST patch to the existing location-page post.

### 3. Add image via REST
Previous batches used REST media upload:
- download source image locally
- optimize locally
- strip metadata
- upload to `/wp-json/wp/v2/media`
- set `alt_text`
- attach as `featured_media` on the location-page draft

### 4. Set Yoast/browser-side fields
Previously done in browser automation, not pure REST:
- open edit screen for the draft post ID
- ensure `SEO` service checkbox is selected in admin UI
- set Yoast focus keyphrase: `seo agency {city}`
- set meta description in browser
- save draft

### 5. Draft remains draft
Do not publish automatically.

## Known previous post IDs
### Batch 1 / early pages
- Milton: 19246
- Toronto: 19251
- Vancouver: 19252
- New York: 19253
- Los Angeles: 19254
- Chicago: 19255

## Known prior issue to remember
- Batch labeling drift happened before.
- Actual execution artifacts matter more than remembered labels.
- Workflow must be stored immediately after successful batch execution.

## Operational rule
When doing future batches:
1. Save the exact city list.
2. Save created post IDs.
3. Save media IDs.
4. Save meta descriptions used.
5. Save whether browser-side SEO/service checkbox step was completed.
