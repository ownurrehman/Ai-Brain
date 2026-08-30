---
name: web-development
description: "Master playbook for Rank Ray web builds, application development, and SaaS building. Covers Next.js, React, TypeScript, Tailwind, Radix UI, Shadcn, SaaS multi-tenancy, Stripe billing/auth, backend APIs (REST/GraphQL/tRPC), safe refactoring, and code shipping pipelines."
risk: safe
source: community
date_added: "2026-06-03"
---

> **Parent Hub:** [[skills/_archived-2026-08-28/INDEX|📦 Archived Skills Hub]] · [[skills/_CATALOG_MAP|⚡ Skills Catalog]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Web Development, SaaS Architecture & Design Playbook

## Overview
This master playbook defines the technical standards for all Rank Ray web development projects, client marketing sites, custom WordPress modules, and SaaS applications. It integrates clean code standards, responsive design, accessible UI foundations, billing systems, and safe deployment sequences.

---

## 1. Project Delivery Norms
* **Repo Alignment:** Always consult the project's specific `AGENTS.md` and `docs/README.md` first.
* **Security Constraints:** Keep environment credentials secure. Implement proper Content Security Policies (CSP) and sanitize user input.
* **Linting & Verification:** Never commit code that breaks builds. Always run local linting and compilation checks (`npm run build` or equivalent) before shipping.

---

## 2. Frontend Development & Design
### React & Next.js Best Practices
* **Server Components First:** Leverage React Server Components (RSC) for data fetching and rendering. Keep Client Components (`"use client"`) at the leaves of your component tree.
* **Next.js App Router:** Follow standard file-based routing rules. Use loading states (`loading.tsx`), error boundaries (`error.tsx`), and route segment configurations.
* **Polymorphic Elements (`asChild`):** Always use Radix's `asChild` prop on interactive elements to prevent nesting `button` or `a` tags within another, keeping the accessibility tree clean.

### Accessibility (Radix & Shadcn)
* **Headless Primitives:** Use unstyled Radix UI primitives to handle complex states, focus traps, and keyboard navigation (`Tab`, `Escape`, arrow keys).
* **Styling with Tailwind CSS:** Configure design tokens (colors, font family, border-radii) in CSS variables. Style components using Tailwind class names and merge utilities using `clsx` and `tailwind-merge`.
* **Dark Mode:** Leverage CSS-first theme classes (e.g. `dark:bg-slate-900`). Keep background filters and glassmorphism subtle.

### TypeScript Standards
* **Strong Type Typing:** Avoid using `any`. Use generics and strict types for props, API responses, and database schemas.
* **Zod Validation:** Validate all external API inputs and environment variables at the application boundary using Zod.

---

## 3. SaaS Architecture & Systems
### Multi-Tenant Architecture
* **Tenant Isolation:** Ensure client databases or schemas are separated. Apply tenant row-level security (RLS) on database tables.
* **Authentication & Billing (Stripe):**
  * Integrate standard login/SSO flows.
  * Map subscription tiers in Stripe dashboard. Align with local value packages.
  * Use Webhooks to handle events (e.g., `invoice.payment_succeeded`, `customer.subscription.deleted`).
* **Churn Prevention & Save-flows:** Standardize user cancellation flows with a cancellation save-screen offering downgrades or pausing, ensuring no hard-locked cancel buttons.

---

## 4. Backend APIs & Architecture
* **API Framework Selection:**
  * **tRPC:** For type-safe backend-to-frontend communication.
  * **REST (Next.js Routes):** For public APIs, webhooks, and WordPress integrations.
  * **GraphQL:** For complex, relational query schemas.
* **Database & Service Layers:** Keep database query operations isolated within service layers. Do not bleed SQL or Prisma queries directly into UI route handlers.

---

## 5. Refactoring & Feature Shipping
### Refactoring Checklist
1. **Behavior Preservation:** Before rewriting legacy modules, write smoke tests or unit tests covering current outcomes.
2. **Atomic Changes:** Modify one file or concept at a time. Commit changes in logical groups.
3. **Dead Code Cleanup:** Remove unused imports, dead components, and obsolete CSS classes immediately.

### Shipping Sequence
* [ ] Run `npm run lint` and verify zero warnings.
* [ ] Run local compiler/build sequence to ensure zero TypeScript errors.
* [ ] Create feature branch (`feature/description`).
* [ ] Submit Pull Request with detailed descriptions and visual screenshots of the interface changes.
