---
name: rankray-react-nextjs-engineering
description: "Architecture rules for Next.js 14+ App Router, React 18/19, TypeScript strict mode, Server/Client components, and Tailwind CSS design systems."
---

> **Parent Hub:** [[skills/_CATALOG_MAP|⚡ Skills Catalog]] · [[INDEX|🧠 Master Ai Brain Hub]]

# ⚛️ RankRay React & Next.js Engineering

> **Best practices for Next.js App Router applications, type-safe components, and modern UI.**

---

## 🏗️ 1. App Router Directory Architecture
```
src/
├── app/
│   ├── (marketing)/
│   │   ├── page.tsx
│   │   └── layout.tsx
│   ├── (dashboard)/
│   │   ├── audit/
│   │   │   └── page.tsx
│   │   └── layout.tsx
│   ├── api/
│   │   └── route.ts
│   └── globals.css
├── components/
│   ├── ui/             ← Reusable primitives (Buttons, Modals, Inputs)
│   └── modules/        ← Feature-specific blocks
├── lib/
│   ├── db.ts           ← Prisma/PostgreSQL singleton
│   └── utils.ts
└── types/
```

---

## ⚡ 2. Server vs Client Component Rules
- **Default to Server Components:** Fetch data directly in server components using standard `async/await`.
- **Use `'use client'` strictly for interactivity:** State (`useState`), effects (`useEffect`), browser APIs, and form input handlers.
- **Never expose private env vars** in client components.

---

## 🎨 3. Design System & Styling
- Use Tailwind CSS with curated color tokens.
- Implement responsive viewport classes (`sm:`, `md:`, `lg:`, `xl:`).
- Always include explicit `alt`, `width`, and `height` on Next.js `<Image />` components.
