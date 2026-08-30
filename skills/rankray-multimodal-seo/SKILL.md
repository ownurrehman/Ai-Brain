---
name: rankray-multimodal-seo
description: "Multimodal and visual semantic SEO standards for video clips, audio transcripts, infographic entities, and AI image graph metadata."
category: seo
---

> **Parent Hub:** [[skills/_CATALOG_MAP|⚡ Skills Catalog]] · [[INDEX|🧠 Master Ai Brain Hub]]

# 🎨 RankRay Multimodal & Visual Semantic SEO Playbook

> **Framework for indexing, ranking, and extracting traffic from images, video timestamp clips, infographics, and multimodal AI search models.**

---

## 🎯 1. The Multimodal Shift

Google Lens, ChatGPT Vision, and Gemini multimodality ingest images, video frames, and infographics alongside text. Visual search and video SERP carousels drive high-converting traffic when properly structured.

---

## 🎬 2. Video Object Schema & Timestamp Optimization

For every video embedded on client sites:
1. Provide a full verbatim or semantic transcript directly beneath the video player.
2. Implement **`VideoObject`** JSON-LD schema with `hasPart` clip timestamps:
```json
{
  "@context": "https://schema.org",
  "@type": "VideoObject",
  "name": "How to Relieve Lower Back Pain at Home",
  "description": "Step-by-step physiotherapy exercises for acute lumbar pain relief.",
  "thumbnailUrl": "https://tonicphysio.com/wp-content/uploads/back-pain-thumb.jpg",
  "uploadDate": "2026-08-15T08:00:00+08:00",
  "hasPart": [
    {
      "@type": "Clip",
      "name": "Phase 1: Gentle Pelvic Tilts",
      "startOffset": 15,
      "endOffset": 90,
      "url": "https://tonicphysio.com/back-pain-exercises/#clip1"
    }
  ]
}
```

---

## 🖼️ 3. Image Entity Graph & Alt Text Standards

- **File Naming:** Descriptive, kebab-case (`milton-physiotherapy-spine-assessment.webp`).
- **Modern Formats:** Next-gen WebP/AVIF with responsive `srcset`.
- **Descriptive Alt Text:** Describe both the visual subject AND the semantic entity context:
  - *Bad:* `alt="physiotherapist"`
  - *Good:* `alt="Registered physiotherapist performing manual spinal mobilization at Tonic Physio clinic in Milton Ontario"`

---

## 🔗 Related Systems
- [[skills/rankray-schema-jsonld/SKILL|Schema JSON-LD]]
- [[skills/rankray-seo-content-writing/SKILL|SEO Content Writing]]
- [[rules/content/image-verification-rule|Image Verification Rules]]
