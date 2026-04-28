# Schema Markup Reference — JSON-LD Examples

## Organization Schema (Every Site)

```json
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "Rank Ray",
  "url": "https://rankray.com",
  "logo": "https://rankray.com/logo.png",
  "sameAs": [
    "https://facebook.com/rankray",
    "https://linkedin.com/company/rankray",
    "https://twitter.com/rankray"
  ],
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+1-555-555-5555",
    "contactType": "customer service",
    "areaServed": "PK",
    "availableLanguage": ["en", "ur"]
  }
}
```

## LocalBusiness Schema (Service Locations)

**Physiotherapy Example:**
```json
{
  "@context": "https://schema.org",
  "@type": "MedicalBusiness",
  "@id": "https://tonicphysio.com",
  "name": "Tonic Physio",
  "description": "Expert physiotherapy clinic in Milton",
  "url": "https://tonicphysio.com",
  "telephone": "+1-555-123-4567",
  "email": "info@tonicphysio.com",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "123 Main Street",
    "addressLocality": "Milton",
    "addressRegion": "ON",
    "postalCode": "L9T 1A1",
    "addressCountry": "CA"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 43.5231,
    "longitude": -79.8832
  },
  "openingHoursSpecification": [
    {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday"],
      "opens": "09:00",
      "closes": "18:00"
    },
    {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": "Friday",
      "opens": "09:00",
      "closes": "16:00"
    }
  ],
  "priceRange": "$$",
  "image": "https://tonicphysio.com/images/clinic.jpg"
}
```

**Law Firm Example:**
```json
{
  "@context": "https://schema.org",
  "@type": "LegalService",
  "name": "Khan LLP",
  "description": "Civil litigation law firm serving Milton and Toronto",
  "url": "https://khanllp.com",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "456 Law Street",
    "addressLocality": "Toronto",
    "addressRegion": "ON",
    "postalCode": "M5V 2K4",
    "addressCountry": "CA"
  },
  "telephone": "+1-555-987-6543",
  "areaServed": ["Milton", "Toronto", "Oakville", "Mississauga"]
}
```

## Article Schema (Blog Posts)

```json
{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "10 SEO Tips for 2024",
  "description": "Improve your search rankings with these proven strategies",
  "image": [
    "https://rankray.com/images/seo-tips.jpg"
  ],
  "author": {
    "@type": "Organization",
    "name": "Rank Ray"
  },
  "publisher": {
    "@type": "Organization",
    "name": "Rank Ray",
    "logo": {
      "@type": "ImageObject",
      "url": "https://rankray.com/logo.png"
    }
  },
  "datePublished": "2024-01-15",
  "dateModified": "2024-02-20"
}
```

## Product Schema (Ecommerce)

```json
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "Motorcycle Helmet Model X",
  "image": [
    "https://teammotorcycle.com/images/helmet-1.jpg",
    "https://teammotorcycle.com/images/helmet-2.jpg"
  ],
  "description": "DOT certified full face motorcycle helmet",
  "sku": "HELMET-X-BLK",
  "brand": {
    "@type": "Brand",
    "name": "HelmetCo"
  },
  "offers": {
    "@type": "Offer",
    "url": "https://teammotorcycle.com/products/helmet-x",
    "priceCurrency": "USD",
    "price": "199.99",
    "priceValidUntil": "2024-12-31",
    "availability": "https://schema.org/InStock"
  },
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.5",
    "reviewCount": "128"
  }
}
```

## Breadcrumb Schema

```json
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "https://rankray.com/"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Services",
      "item": "https://rankray.com/services/"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "SEO Agency Pakistan",
      "item": "https://rankray.com/services/seo-agency-pakistan/"
    }
  ]
}
```

## FAQ Schema

```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "What is SEO?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "SEO stands for Search Engine Optimization. It is the practice of improving your website to increase its visibility in search engine results."
      }
    },
    {
      "@type": "Question",
      "name": "How long does SEO take?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "SEO is a long-term strategy. Most businesses see significant improvements within 6-12 months of consistent optimization."
      }
    }
  ]
}
```

---

## Validation

**Always validate with:**
- Google Rich Results Test: https://search.google.com/test/rich-results
- Schema Validator: https://validator.schema.org

## Quick Checks

- [ ] JSON is valid syntax
- [ ] Required fields are present
- [ ] @context is "https://schema.org"
- [ ] @type matches content
- [ ] URLs are absolute (start with https://)
- [ ] Dates in ISO 8601 format (YYYY-MM-DD)
