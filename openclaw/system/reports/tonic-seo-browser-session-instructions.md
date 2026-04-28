# Tonic Physio: WordPress Browser Session Instructions
**Date:** 2026-04-20  
**Status:** Browser Profile Ready - Manual Login Required

---

## Current Situation

The WordPress REST API is returning 401 errors for edit operations. A browser-based approach is required.

**Browser Profile Location:** `~/.openclaw/workspace/.browser-profiles/tonic-wp/`

---

## Step 1: Open Browser Session

Run this command to open a browser at the WordPress login page:

```bash
node /Users/sheikhown/.openclaw/workspace/browser/tonic-login.js
```

**Credentials:**
- Email: `Dan`
- Application Password: `4vFk 18fN UlLB twaw B2hU 0kRE`

---

## Step 2: Update 17 Existing Pages

After logging in, navigate to **Pages → All Pages** and update each page:

### Quick Reference Table:

| Page | Focus Keyphrase | Meta Description |
|------|-----------------|------------------|
| Physiotherapy in Milton | `physiotherapy in Milton` | Physiotherapy in Milton for pain relief & recovery. Expert rehab, manual therapy & direct billing at Tonic Physio. Book today. |
| Compression Socks | `compression socks Milton` | Compression socks in Milton for recovery & circulation support. Expert fitting & advice at Tonic Physio. In-person consultations available. |
| Custom Orthotics | `custom orthotics Milton` | Custom orthotics in Milton for foot support & pain relief. Personalized assessment & gait analysis at Tonic Physio. Book consultation. |
| Custom Bracing | `custom bracing Milton` | Custom and OTC bracing in Milton for injury recovery & joint stability. Expert fitting at Tonic Physio. Knee, ankle & posture braces available. |
| Massage Therapy | `registered massage therapy Milton` | Registered massage therapy in Milton for pain relief & stress reduction. Personalized hands-on care at Tonic Physio. RMTs available. Book now. |
| Shockwave Therapy | `shockwave therapy Milton` | Shockwave therapy in Milton for fast injury recovery. Pain relief & healing acceleration at Tonic Physio. Book your session today. |
| MVA Physiotherapy | `MVA physiotherapy Milton` | Motor vehicle accident physiotherapy in Milton. MVA injury recovery, pain relief & mobility restoration at Tonic Physio. Direct billing available. |
| WSIB Care | `WSIB care programs Milton` | WSIB care programs in Milton for workplace injury recovery. Expert physiotherapy & direct billing at Tonic Physio. Get back to work faster. |
| Manual Osteopathy | `manual osteopathy Milton` | Manual osteopathy in Milton for pain relief & mobility. Gentle hands-on treatment by experienced osteopaths at Tonic Physio. Book assessment. |
| Orthopedic Physio | `orthopedic physiotherapy Milton` | Orthopedic physiotherapy in Milton for joint pain & mobility recovery. Personalized rehab plans at Tonic Physio. Lasting results. Book today. |
| Neurological Physio | `neurological physiotherapy Milton` | Neurological physiotherapy in Milton for movement & strength recovery. Personalized care for stroke, Parkinson's & conditions at Tonic Physio. |
| Pediatric Physio | `pediatric physiotherapy Milton` | Pediatric physiotherapy in Milton for children's mobility & strength. Developmental care for kids at Tonic Physio. Book child assessment today. |
| Acupuncture | `acupuncture therapy Milton` | Acupuncture therapy in Milton for pain relief & stress reduction. Natural healing & balance restoration at Tonic Physio. Book session today. |
| Joint Pain | `joint pain treatment Milton` | Joint pain and stiffness treatment in Milton. Personalized physiotherapy to restore mobility & reduce discomfort at Tonic Physio. Book now. |
| Rheumatoid Arthritis | `rheumatoid arthritis therapy Milton` | Rheumatoid arthritis therapy in Milton for pain relief & mobility. Joint function improvement at Tonic Physio. Expert care. Book consultation. |
| Back & Neck Pain | `back and neck pain Milton` | Back and neck pain treatment in Milton. Expert physiotherapy for lasting pain relief at Tonic Physio. Personalized care. Book assessment today. |
| Sports Physio | `sports physiotherapy Milton` | Sports physiotherapy in Milton for injury recovery & performance. Athlete-focused rehab at Tonic Physio. Direct billing. Book now. |

---

## Step 3: Verify 4 New Pages

These pages already exist but need SEO verification:

| Page | ID | URL | Current Status |
|------|-----|-----|----------------|
| Herniated Disc | 6996 | `/physiotherapy-in-milton/herniated-disc-treatment/` | ✅ Published |
| Sciatica | 7001 | `/physiotherapy-in-milton/sciatica-treatment/` | ✅ Published |
| Cervical Spondylosis | 7006 | `/physiotherapy-in-milton/cervical-spondylosis/` | ✅ Published |
| B-Pulse Pelvic | 11603 | `/b-pulse-pelvic-floor-strengthening/` | ✅ Published |

### SEO Values for New Pages:

| Page | Focus Keyphrase | Meta Description |
|------|-----------------|------------------|
| Herniated Disc | `herniated disc treatment Milton` | Herniated disc treatment in Milton for back pain relief. Expert physiotherapy, spinal mobilization & core strengthening at Tonic Physio. Book today. |
| Sciatica | `sciatica treatment Milton` | Sciatica treatment in Milton for leg pain relief. Nerve flossing, pelvic alignment & decompression therapy at Tonic Physio. Book consultation. |
| Cervical Spondylosis | `cervical spondylosis treatment Milton` | Cervical spondylosis treatment in Milton for neck pain relief. Joint mobilization, posture correction & soft tissue therapy at Tonic Physio. |
| B-Pulse Pelvic | `B-Pulse pelvic floor strengthening Milton` | B-Pulse pelvic floor strengthening in Milton. Non-invasive treatment for incontinence & core stability. Expert care at Tonic Physio. Book now. |

---

## Step 4: Image Assignment for New Pages

Use the Media Library to assign these images:

| Page | Featured Image ID | Why Choose Us ID | Solutions ID |
|------|-------------------|------------------|--------------|
| Herniated Disc | 11848 | 11849 | 11850 |
| Sciatica | 11695 | 11694 | 11693 |
| Cervical Spondylosis | 9597 | 11651 | 11650 |
| B-Pulse Pelvic | 11808 | 11815 | 11822 |

---

## Step 5: Verification Checklist

After completing all updates:

- [ ] All 21 pages have Focus Keyphrase set in Yoast
- [ ] All 21 pages have optimized Meta Description in Yoast
- [ ] All 21 pages show Green Yoast status
- [ ] 4 new pages have Featured Images assigned
- [ ] All pages are Published and accessible

---

## Step 6: Update Monitor Report

After completion, update `reports/tonic-service-monitor-2026-04-20.md`:
- Change all SEO statuses from ❌ to ✅
- Mark all pages as **"100% Complete"**

---

## Full Guide

See `reports/tonic-wordpress-manual-guide.md` for detailed step-by-step instructions.

---

*Instructions generated by FrontEnd, Rank Ray*
