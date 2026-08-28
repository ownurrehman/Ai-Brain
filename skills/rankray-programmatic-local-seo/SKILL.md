---
name: rankray-programmatic-local-seo
description: "Architecture, dynamic variable injection, and content generation for multi-location programmatic landing pages."
---

# 🗺️ RankRay Programmatic & Multi-Location SEO

> **Blueprint for scalable, anti-doorway local landing page clusters.**

---

## 🏛️ 1. URL & Information Architecture
- **URL Pattern:** `domain.com/[service]-[city]/` (e.g. `/physiotherapy-milton/`, `/link-building-agency-austin/`).
- **Hierarchy:**
  - `/locations/` (State/Region Hub)
  - `/locations/[city]/` (City Overview)
  - `/[service]-[city]/` (Transactional Service Landing Page)

---

## 🧩 2. Dynamic Template Variables & Quality Safeguards

To prevent thin content or Google doorway page penalties, each programmatic page must dynamically inject unique local data:

```markdown
# {{service_name}} in {{city_name}}, {{state_code}}

[Authoritative 100-word intro specifically referencing {{city_name}} neighborhoods and landmarks.]

## Why Choose Us for {{service_name}} in {{city_name}}
- **Local Address:** {{office_address_or_service_radius}}
- **Service Area:** Serving {{neighborhood_1}}, {{neighborhood_2}}, and surrounding regions.
- **Driving Directions:** Conveniently located near {{major_cross_street_or_highway}}.

## Local Client Reviews & Outcomes
> "[Quote from actual local customer in {{city_name}}]"
> — {{customer_name}}, {{city_name}} Resident
```
