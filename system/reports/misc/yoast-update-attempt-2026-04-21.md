> **Parent Report Hub:** [[system/reports/INDEX|📊 System Reports Archive]] · [[INDEX|🧠 Ai Brain]]

# Yoast SEO Update Attempt Report
**Date:** 2026-04-21
**Task:** Update Yoast SEO fields on 5 Tonic Physio WordPress pages

## Pages to Update

| ID | Page Name | SEO Title | Meta Description |
|----|-----------|-----------|------------------|
| 11603 | B-Pulse Pelvic Floor | B-Pulse Pelvic Floor Strengthening Milton \| Tonic Physio | B-Pulse pelvic floor strengthening in Milton at Tonic Physio. Expert treatment for postpartum recovery, incontinence & pelvic pain. Book consultation. |
| 6971 | Joint Pain | Joint Pain Treatment Milton \| Tonic Physio | Relieve joint pain and stiffness in Milton at Tonic Physio. Expert physiotherapy for arthritis, injury & chronic pain. Book your appointment. |
| 1791 | Orthopedic Physiotherapy | Orthopedic Physiotherapy Milton \| Tonic Physio | Expert orthopedic physiotherapy in Milton at Tonic Physio. Joint & muscle rehab, post-surgery recovery & pain relief. Book assessment today. |
| 1793 | Pediatric Physiotherapy | Pediatric Physiotherapy Milton \| Tonic Physio | Pediatric physiotherapy in Milton at Tonic Physio. Expert care for children with developmental delays, injuries & mobility issues. Book now. |
| 6587 | Hot Stone Massage | Hot Stone Massage Milton \| Tonic Physio | Hot stone massage in Milton at Tonic Physio. Therapeutic heat therapy for muscle tension, stress relief & relaxation. Book your session. |

## Methods Attempted

### 1. WordPress REST API (Application Password)
**Credentials Used:**
- User: Dan
- Password: 4vFk 18fN UlLB twaw B2hU 0kRE
- **Result:** ✗ Failed - 401 "rest_cannot_edit"

**Credentials Used:**
- User: rankrayagency@gmail.com  
- Password: RR#Tonic@2026
- **Result:** ✗ Failed - 401 "rest_cannot_edit"

The REST API allows GET requests but POST requests for editing are blocked. This suggests the application password does not have sufficient permissions or REST API editing is disabled for these credentials.

### 2. Browser Automation (Playwright)
**Approach:** Full browser automation with headless=False
**Result:** ⚠ Partial - Browser launches and takes screenshots but process gets killed before completing all pages

**Screenshots captured:**
- /tmp/wp-page-11603-before.png (259KB)
- /tmp/wp-page-11603-after.png (198KB)
- /tmp/wp-page-6971-before.png (295KB)

The browser successfully logs in and navigates to pages, but the automation script times out before completing all updates.

## Root Cause Analysis

1. **REST API:** Application passwords don't have edit permissions via REST API
2. **Browser Automation:** Process timeout (SIGKILL) - likely due to:
   - Long-running browser session
   - Headless mode issues
   - Resource constraints

## Recommended Next Steps

### Option 1: Manual WordPress Admin Update
Login to https://tonicphysio.com/wp-admin and manually update Yoast fields for each page:

1. Go to Pages → All Pages
2. Edit each page by ID
3. Scroll to Yoast SEO section
4. Click "Edit snippet"
5. Update SEO title and meta description
6. Click Update

### Option 2: Fix REST API Permissions
The application password needs proper capabilities. Check:
- User role (should be Administrator)
- REST API access enabled
- Application password has edit_posts capability

### Option 3: Use wp-cli
If wp-cli is available on the server, it can update Yoast fields directly:
```bash
wp post update 11603 --meta_input='{"_yoast_wpseo_title":"...","_yoast_wpseo_metadesc":"..."}'
```

## Verification Checklist

After updates are complete, verify:
- [ ] SEO title is under 60 characters
- [ ] Meta description is under 160 characters  
- [ ] "Tonic" appears in each meta description
- [ ] Primary keyword appears in title
- [ ] Changes visible in page source

## Current Status

**Status:** BLOCKED
**Blocker:** Authentication/permission issues with both REST API and browser automation timeout

**Action Required:** Either:
1. Manual update via WordPress admin
2. Fix REST API application password permissions
3. Run browser automation with shorter timeout per page
