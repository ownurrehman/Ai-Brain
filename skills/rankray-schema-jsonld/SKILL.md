---
name: rankray-schema-jsonld
description: "Structured data engineering templates and validation rules for Organization, LocalBusiness, MedicalBusiness, FAQPage, Service, and Breadcrumbs."
---

> **Parent Hub:** [[skills/_CATALOG_MAP|⚡ Skills Catalog]] · [[INDEX|🧠 Master Ai Brain Hub]]

# 📐 RankRay Schema JSON-LD Engineering

> **Production-ready structured data templates for search engine entity recognition.**

---

## 🏢 1. Organization & Agency Schema
```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ProfessionalService",
  "name": "Rank Ray",
  "url": "https://rankray.com",
  "logo": "https://rankray.com/wp-content/uploads/2026/08/rankray-logo.webp",
  "description": "Technical SEO and High-Authority Link Building Agency for B2B SaaS and Enterprise Brands.",
  "sameAs": [
    "https://www.linkedin.com/company/rankray",
    "https://twitter.com/rankray"
  ]
}
</script>
```

---

## ❓ 2. FAQPage Schema
```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "How long does a technical SEO audit take?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Our comprehensive 7-Day Forensic Technical Audit is delivered within 5-7 business days with line-by-line developer remediation code."
      }
    }
  ]
}
</script>
```
