# TonicPhysio.com SEO Fixes - Ready to Implement
**Generated:** 2025-02-19 | **Auditor:** Ranki

---

## FIX 1: Title Tag (Copy-Paste Ready)

### Current (Bad):
```
Tonic Physiotherapy And Rehabilitation Centre In Milton, CA
```
**Issues:** Uses "And" not "&", "CA" abbreviation not "ON", keyword order weak

### New (Optimized):
```
Physiotherapy & Rehab Centre Milton, ON | Tonic Physio
```
**Why this works:**
- Exact match keyword "Physiotherapy" front-loaded
- "Milton, ON" captures "Ontario" searches
- Ampersand saves 2 characters vs "And"
- Brand at end (less important than keywords)
- 59 characters (perfect for SERP display)

**How to update:**
1. Login: tonicphysio.com/wp-admin
2. Go to Pages → Home → Edit with Elementor
3. Click Page Settings (gear icon)
4. Under "Rank Math SEO" → Replace Title
5. Update & Clear Cache

---

## FIX 2: Meta Description (COPY-PASTE)

### Current (Bad):
```
Expert physiotherapy and rehab services at Tonic Physio. Move Better and Live Better with personalized care tailored to your needs.
```
**Issues:** No location, no CTA, generic, missing keywords

### New (Optimized - 158 chars):
```
Expert physiotherapy & rehabilitation in Milton, ON. Board-registered therapists, direct billing insurance. Book your assessment today. Call 905-878-7775.
```
**Why this works:**
- "Milton, ON" included (local ranking signal)
- "direct billing" = high-intent search term
- Phone number = instant contact option
- "Book...today" = urgency CTA
- "Board-registered" = trust signal (E-E-A-T)

**How to update:**
1. Same location as Title (Page Settings → Rank Math SEO)
2. Replace Description field
3. Save & clear cache (WP Rocket)

---

## FIX 3: Location Paragraph (Homepage Content)

### Where to add:
After the hero section, before "Services" section. Create a new "About" section or add to existing intro.

### Copy-Paste Content:

```html
<h2>Trusted Physiotherapy & Rehabilitation in Milton, Ontario</h2>

<p>Since 2019, Tonic Physio has been Milton's trusted destination for expert 
physiotherapy and rehabilitation services. Located in the heart of Milton 
at 100 Nipissing Road, our clinic serves residents throughout Milton, Ontario 
and surrounding communities including Georgetown, Acton, and Burlington.</p>

<p>Our team of board-registered physiotherapists and massage therapists 
specializes in treating sports injuries, chronic pain, post-surgical 
rehabilitation, and motor vehicle accident recovery. With direct insurance 
billing and same-week appointments available, getting the care you need has 
never been easier.</p>

<p>Whether you're recovering from an injury, managing arthritis, or seeking 
relief from back and neck pain, <a href="/services/">our comprehensive 
services</a> are designed to help you move better and live pain-free.</p>
```

**Why this works:**
- "Milton" appears 5 times in 3 paragraphs (keyword density)
- Surrounding cities mentioned (broader local capture)
- Internal link to services page (contextual linking)
- E-E-A-T signals: established date, location, credentials
- Natural language, not keyword-stuffed

---

## FIX 4: FAQ Schema Markup

### Where to add:
If you have an FAQ page or FAQ section on homepage, add this to the `<head>` or via Rank Math.

### JSON-LD Schema (Copy-Paste to HTML block or functions.php):

```json
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [{
    "@type": "Question",
    "name": "Do I need a referral for physiotherapy in Milton?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "No, you do not need a doctor's referral to see a physiotherapist in Ontario. You can book directly with Tonic Physio by calling 905-878-7775 or using our online booking system. We accept most major insurance plans with direct billing."
    }
  },{
    "@type": "Question",
    "name": "What conditions does Tonic Physio treat?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "We treat a wide range of conditions including sports injuries, back and neck pain, arthritis, frozen shoulder, sciatica, herniated discs, and post-surgical rehabilitation. We also specialize in motor vehicle accident recovery and workplace injury rehabilitation (WSIB)."
    }
  },{
    "@type": "Question",
    "name": "Do you offer direct billing to insurance?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Yes, Tonic Physio offers direct billing to most major insurance providers including Sun Life, Manulife, Canada Life, Green Shield, and more. We also accept MVA (motor vehicle accident) insurance and WSIB claims."
    }
  },{
    "@type": "Question",
    "name": "Where is Tonic Physio located in Milton?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Tonic Physio is located at 100 Nipissing Road, Suite 5, Milton, ON L9T 5B2. We are conveniently situated near the Milton Mall and accessible from Highway 401. Free parking is available on-site."
    }
  },{
    "@type": "Question",
    "name": "What are your clinic hours?",
    "acceptedAnswer": {
      "@type": "Answer",
      "text": "Our Milton clinic is open Monday to Friday from 9:00 AM to 7:00 PM, and Saturday from 9:00 AM to 3:00 PM. We offer early morning and evening appointments to accommodate busy schedules."
    }
  }]
}
</script>
```

**How to implement via Rank Math:**
1. Go to Rank Math → Titles & Meta → Homepage
2. Add custom schema (if available)
3. OR: Install "Schema Pro" plugin
4. OR: Add code block to footer via Elementor → Custom HTML

---

## FIX 5: Internal Links to Add

### Link 1: Services Contextual Link
**Location:** In the location paragraph above
**Anchor text:** `our comprehensive services`
**Links to:** `/services/`

### Link 2: Massage Therapy
**Location:** In "Registered Massage Therapy" section description
**Anchor text:** `registered massage therapy`
**Links to:** `/registered-massage-therapy/`

### Link 3: FAQ Link
**Location:** End of homepage, before footer CTA
**Anchor text:** `Read our frequently asked questions`
**Links to:** `/faq/`

### Link 4: Blog Integration (If you have blog posts)
**Location:** Create "Latest from Our Blog" section on homepage
**Anchor text:** `[Blog post title]`
**Links to:** `/blog/[blog-post-url]/`
**Preview text:** First 50 words of blog + "Read more →"

### Link 5: Contact/Booking CTA
**Location:** Persistent button in header or sticky footer
**Anchor text:** `Book Appointment Now`
**Links to:** `/contact/` or booking widget

---

## FIX 6: Image Alt Tags (5 Critical Images)

Update these specific images in Elementor:

| Image | Current Alt (Maybe) | New Alt Text |
|-------|---------------------|--------------|
| Hero image | [blank or generic] | `Physiotherapist treating patient at Milton clinic - Tonic Physio` |
| Logo | `Tonic Physio logo` | `Tonic Physio - Milton Physiotherapy & Rehab Centre` |
| Therapist headshots | `Therapist name` | `Registered Physiotherapist [Name] - Milton, ON` |
| Service icons | `Service icon` | `[Service name] therapy at Tonic Physio Milton` |
| Clinic interior | [blank] | `Tonic Physio clinic interior at 100 Nipissing Road Milton` |

---

## FIX 7: Local Business Schema (Bonus)

Add this to homepage head:

```json
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Physiotherapy",
  "name": "Tonic Physiotherapy and Rehabilitation Centre",
  "image": "https://tonicphysio.com/wp-content/uploads/2024/11/Tonicphysio.png",
  "@id": "https://tonicphysio.com",
  "url": "https://tonicphysio.com",
  "telephone": "+1-905-878-7775",
  "priceRange": "