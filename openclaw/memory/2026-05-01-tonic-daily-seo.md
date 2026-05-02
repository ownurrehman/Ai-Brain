# Session: 2026-05-01 — Daily SEO Operation (Chronos Agent)

- **Source**: Cron-triggered subagent (agent:chronos:subagent)
- **Scope**: Comprehensive daily SEO audit of tonicphysio.com

## Key Findings

### CRITICAL: Homepage Service Link Mismatches
All 9 service card "Learn more" links on the homepage point to WRONG pages. Example:
- "Registered Massage Therapy" links to `/physiotherapy-in-milton/neurological-physiotherapy/`
- "Manual Osteopathy" links to `/physiotherapy-in-milton/pediatric-physiotherapy/`
- ALL 9 cards are mismatched — this is a severe UX and SEO issue wasting link equity.

**Fix:** Update Elementor homepage widget — replace all 9 service card destination URLs.

### Plugin Confirmed: Yoast SEO
Previously thought to be Rank Math — confirmed Yoast SEO generating sitemaps. Sitemaps are clean and well-structured.

### Page Performance Issues
- `/registered-massage-therapy/`: 422KB (heaviest page) — needs immediate image compression
- `/physiotherapy-in-milton/`: 352KB — needs optimization
- Page load times average 2-2.7s across key pages

### Schema Status
- Homepage: MedicalOrganization + LocalBusiness ✅
- Physiotherapy page: Non-standard `medicalclinic` (lowercase) — change to `MedicalClinic`
- FAQ schema exists on physiotherapy page (14 Q&As) — strong for AEO
- No FAQ schema on massage therapy or shockwave pages

### Service URL Architecture
- 4 URLs use 301 redirects (acceptable but `/services/` page should link canonical URLs)
- Clean parent-child structure: `/physiotherapy-in-milton/[sub-service]/` and `/registered-massage-therapy/[sub-service]/`

## Keyword Gaps (Priority)
1. "pelvic floor physiotherapy Milton" — NO dedicated page; competitors dominate
2. "vestibular rehab Milton" — Only a guide post, no service page
3. "TMJ physiotherapy Milton" — Only a guide post
4. "shockwave therapy Milton" — Page exists but thin content

## Competitors
Omni Clinic, Hope Wellness, HCRC, Valeo Clinic, MEX Physio, Revolve Physio, ACT Physiotherapy, PhysioON — all competing for pelvic floor, vestibular, and shockwave terms.

## Action Items for Fawad (TonicPhysio client lead)
1. Login to wp-admin Elementor → fix 9 service card links on homepage (urgent)
2. Install image optimization (ShortPixel/TinyPNG) → compress images on massage therapy and physiotherapy pages
3. Create new page: `/pelvic-floor-physiotherapy-milton/` — target 2,000+ words with FAQ schema
4. Add FAQ schema to `/registered-massage-therapy/` and `/shockwave-therapy/`
5. Fix schema `@type` on physiotherapy page from `medicalclinic` to `MedicalClinic`

## GBP Status
- 4.9★ / 121 reviews — excellent standing
- No issues detected
- Recommend adding "pelvic floor physiotherapy" and "vestibular rehab" to GBP services list

## Report Location
Full detailed report: `/system/reports/tonicphysio/daily_seo_2026-05-01.md`
