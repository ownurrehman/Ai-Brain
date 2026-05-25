# TonicPhysio Content Protocol (Updated 2026-05-01)

## Site: tonicphysio.com
**Owner:** Own-ur-Rehman Sheikh (Rank Ray CEO)  
**API Auth:** User: `Dan`, App Password: `NMwZ 1LyJ YgbE fUjs pUYn 4SoZ`  
**Endpoint:** `https://tonicphysio.com/wp-json/wp/v2/`  
**Template:** `services-pages.php`  
**Category ID:** `325` (Service Page)  
**Parent Page ID:** `6305` (`/physiotherapy-in-milton/`)  
**Phone:** `905-878-7775`

---

## CRITICAL RULES (Never Break These)

### 1. H1 Format
- **Format:** `Service Name + Benefit` (user-friendly headline)
- **CORRECT:** "TMJ Treatment for Lasting Jaw Pain Relief"
- **WRONG:** "TMJ Treatment in Milton | Jaw Pain Relief & Therapy" (no pipes, no location stuffing)
- **WRONG:** "Best TMJ Physiotherapy Clinic Milton Ontario" (no keyword spam)

### 2. Content Structure (Paragraph vs Bullet Fields)
- **Paragraph fields (`paragraph_1`, `paragraph_2`, etc.):** Flowing narrative prose only
- **Bullet fields (`why_choose_us_point_*`, `solution_*`):** Short benefit statements only
- **NEVER put bullet lists inside paragraph fields when dedicated bullet fields exist**
- **HTML allowed in paragraphs:** `<br>` for line breaks, `<strong>` for emphasis/mini-headers

### 3. Content Length
- **Total per page:** ~2,000–2,500 words
- **Individual paragraph fields:** 600–1,500 chars depending on section importance
- **FAQ answers:** 150–250 chars minimum
- **Why choose us points:** 40–80 chars
- **Solution points:** 40–80 chars

### 4. Yoast SEO (MANDATORY - Set During Page Creation)
- **Focus keyword:** Must include service + location (e.g., "pre-surgical rehabilitation milton")
- **Meta title:** `<60 chars | Service Milton | Tonic Physio`
- **Meta description:** `<160 chars | Keyword + LSI + Brand + CTA`
- **Format:** `{"meta": {"_yoast_wpseo_focuskw": "...", "_yoast_wpseo_title": "...", "_yoast_wpseo_metadesc": "..."}}`
- **DO NOT create pages without Yoast meta fields**

### 5. Workflow
- **Status:** All new pages must be `draft` - user publishes manually
- **Parent:** All condition/specialty/treatment pages must be children of `physiotherapy-in-milton` (ID: 6305)
- **Process:** Create page → Set Yoast metas → Verify via REST API response → User audits before next batch
- **Images:** User handles manually - do NOT attempt image uploads without explicit approval

---

## Complete ACF Field List

| Field | Type | Content Guidelines |
|-------|------|-------------------|
| `h1` | Headline | Service + Benefit only |
| `paragraph_1` | Prose | ~800-1200 chars, intro with `<br><br>` breaks |
| `h2` | Subhead | Section heading |
| `paragraph_2` | Prose | ~800-1200 chars, condition list with `<br>` |
| `why_choose_us_point_1-5` | Bullets | 40-80 chars each, benefit-focused |
| `why_choose_us_image` | Image ID | User sets manually |
| `paragraph_after_why_choose_us` | Prose | ~200-400 chars, bridging text |
| `h2_second` | Subhead | Treatment process heading |
| `paragraph_for_h2_second` | Prose | ~1000-1500 chars, treatment details |
| `solution_1-5` | Bullets | 40-80 chars each |
| `solutions_image` | Image ID | User sets manually |
| `paragraph_after_detailed_box` | Prose | ~200-400 chars |
| `h2_third` | Subhead | Assessment heading |
| `paragraph_for_h2_third` | Prose | ~1000-1500 chars, step-by-step |
| `h2_fourth` | Subhead | Partner/support heading |
| `paragraph_for_h2_fourth` | Prose | ~600-1000 chars |
| `h3_first` | CTA Head | "Book Your [Service] Today" |
| `paragraph_for_h3_first` | Prose | ~300-500 chars, phone number + hours + billing |
| `faq_heading` | Head | "Common Questions About [Service]" |
| `faq_q1-10` | Questions | Clear, conversational questions |
| `faq_a1-10` | Answers | 150-250 chars each |

---

## Lessons Learned (2026-04-30 to 2026-05-01)

### What Went Well
- Created 25 new draft service pages in systematic batch
- Created 11 additional missing pages to beat MexPhysio
- Total: 46 service pages (now exceeds MexPhysio's 44)
- Content consistently ~8,000-10,000 chars per page
- All pages include 10 FAQs with 150-250 char answers
- Fixed H1 format on existing pages (removed location stuffing)
- Added complete content to MVA, WSIB, TMJ, Shockwave pages

### Mistakes Made (CRITICAL)
1. **Did NOT set Yoast meta fields during initial page creation** - Fixed retroactively
2. **Did NOT verify Yoast metas before confirming completion** - Must verify via API response
3. **Attempted image uploads without explicit user approval** - User rejected, created friction
4. **Did not include phone number in CTA sections early on** - Fixed in later pages

### Processes to Improve
1. **Pre-flight checklist before marking pages "done":**
   - [ ] Yoast focus keyword set
   - [ ] Yoast title <60 chars
   - [ ] Yoast description <160 chars
   - [ ] Phone number in CTA
   - [ ] H1 is user-friendly (no pipes, no location stuffing)
   - [ ] No bullet lists in paragraph fields
   - [ ] 10 FAQs with 150-250 char answers
   - [ ] Page is draft status
   - [ ] Parent is 6305
   - [ ] Template is services-pages.php

2. **Never handle images without explicit user request**
3. **Always verify Yoast fields in API response**
4. **Create pages with Yoast metas in same API call when possible**

---

## Competitive Status (2026-05-01)

| Metric | TonicPhysio | MexPhysio |
|--------|-------------|-----------|
| Service Pages | 46 | 44 |
| Blog Posts | Multiple | Multiple |
| Locations | Milton | Milton + Oakville |
| Unique Offerings | Nutrition Coaching, Visual Therapy, B-Pulse | - |
| Yoast SEO | Set on all pages | Unknown |
| ACF Content | Complete with 10 FAQs | Unknown |

---

## Next Priority Actions
1. User publishes all 46 draft pages and adds images
2. Internal linking between related service pages
3. Schema markup (LocalBusiness, Service, FAQ)
4. Feature testimonials/reviews
5. Create exercise program content
6. Add "How We Treat" cross-linking sections

---

## API Quick Reference
```bash
# Create page
curl -s -u "Dan:NMwZ 1LyJ YgbE fUjs pUYn 4SoZ" -X POST "https://tonicphysio.com/wp-json/wp/v2/pages" \
-H "Content-Type: application/json" \
-d '{"title":"Service Name","status":"draft","page_category":[325],"template":"services-pages.php","parent":6305}'

# Update ACF + Yoast
curl -s -u "Dan:NMwZ 1LyJ YgbE fUjs pUYn 4SoZ" -X PUT "https://tonicphysio.com/wp-json/wp/v2/pages/{ID}" \
-H "Content-Type: application/json" \
-d '{"acf":{...},"meta":{"_yoast_wpseo_focuskw":"keyword milton","_yoast_wpseo_title":"Title","_yoast_wpseo_metadesc":"Description"}}'

# Verify
curl -s -u "Dan:NMwZ 1LyJ YgbE fUjs pUYn 4SoZ" "https://tonicphysio.com/wp-json/wp/v2/pages/{ID}?_fields=id,title,acf,meta"
```

---

## Files Created During This Project
- `/tmp/shockwave-update.json` - Shockwave page content
- `/tmp/tmj-complete.json` - TMJ page content
- `/tmp/mva-acf-update.json` - MVA page content
- `/tmp/wsib-expanded.json` - WSIB page content
- `/tmp/knee-pain-content.json` through `/tmp/womens-health-content.json` - 25 new pages
- `/tmp/pre-surgical-content.json` through `/tmp/visual-content.json` - 11 missing pages
- `/tmp/tonic-vs-mex-audit.md` - Competitive gap analysis

---

**Last Updated:** 2026-05-01  
**Status:** Active - Waiting for user to publish pages and add images
