> **Parent Site:** [[websites/rankray.com/index|🌐 rankray.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# RankRay Location Pages Audit — May 14 2026

Date: 2026-05-14
CPT: `location-page`
Count: 38 cities across 5 countries
Builder: ACF + Elementor hybrid (confirmed via WordPress REST API)
Sheet: `RankRay Content Audit - 2026-05-14` (Tab: Location Pages)

## Cities

### Canada (5)
- Toronto (`seo-agency-toronto`, ID 19251)
- Milton (`seo-agency-milton`, ID 19246)
- Vancouver (`seo-agency-vancouver`, ID 19252)
- Calgary (`seo-agency-calgary`, ID 19307)
- Ottawa (`seo-agency-ottawa`, ID 19308)
- Mississauga (`seo-agency-mississauga`, ID 19309)

### USA (7)
- New York (`seo-agency-new-york`, ID 19253)
- Chicago (`seo-agency-chicago`, ID 19254)
- Los Angeles (`seo-agency-los-angeles`, ID 19255)
- Miami (`seo-agency-miami`, ID 19281)
- Dallas (`seo-agency-dallas`, ID 19282)
- Houston (`seo-agency-houston`, ID 19283)
- Austin (`seo-agency-austin`, ID 19285)
- Seattle (`seo-agency-seattle`, ID 19311)

### UAE / GCC (6)
- Dubai (`real-estate-seo-agency-dubai`, ID 18999)
- Abu Dhabi (`seo-agency-abu-dhabi`, ID 19284)
- Sharjah (`seo-agency-sharjah`, ID 19299)
- Ajman (`seo-agency-ajman`, ID 19300)
- Muscat (`seo-agency-muscat`, ID 19303)
- Doha (`seo-agency-doha`, ID 19302)

### Pakistan (6)
- Islamabad
- Rawalpindi
- Lahore
- Karachi

### Australia (2)
- Sydney

## Why These Were Missing

The `location-page` CPT was **not queried** during the first audit because:
1. The initial audit only scanned `wp/v2/pages` (standard Pages)
2. CPTs require explicit `wp/v2/location-page` endpoint
3. No rule in content-rules.md or INDEX.md mentioned CPT scanning

## Fix Applied
1. Queried `wp/v2/types` to discover `location-page` CPT
2. Fetched all 38 location pages with Yoast meta
3. Populated the **Location Pages** tab of Google Sheet with ID/Title/Slug/URL/Status/Service Type/Yoast data
4. Shared Sheet with rankrayofficial@gmail.com (editor access verified)
5. Added **Google Sheet Creation Rule** to content-rules.md line 80

## Outstanding Tasks
- ACF field content expansion is pending (waiting on rate limit cooldown)
- Elementor page design verification needed (user will switch templates manually)
- Yoast fixes on location pages may need audit against latest content-rules.md
