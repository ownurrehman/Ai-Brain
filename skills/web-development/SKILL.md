---
name: web-development
description: Use for Rank Ray web builds and refactors—marketing sites, WordPress, React/Next/Vite stacks, performance and accessibility—not mobile-native apps (use app-development) or SaaS tenancy design alone (use saas-development).
---

# Rank Ray — Web development

**Agency:** [Rank Ray](https://www.rankray.com) — websites and web apps for clients (and internal tools where scoped as “web”).

## Use when

- **Marketing / corporate sites**, landing systems, **WordPress** themes/plugins, or **React / Next.js / Vite** frontends.
- Tasks need **routing, performance, a11y, SEO-friendly structure**, or API-backed UI.
- You are **integrating** with Rank Ray’s WP plugin line or **RankRay-HQ** frontend patterns.

## Avoid when

- **Mobile-first native** app (iOS/Android) → **`../app-development/SKILL.md`**.
- **Multi-tenant SaaS architecture** as the main problem → **`../saas-development/SKILL.md`** + **`../saas-app-foundation/SKILL.md`**.
- **Only** CMS content entry → **`../wordpress-publisher/SKILL.md`**.

## Rank Ray delivery norms

- Match **repo `AGENTS.md`** for the active project (e.g. **RankRay-HQ**, **SEO Engine AI**, **WP Markdown for AI**).
- Prefer **existing design system** (Tailwind, shadcn, etc.) over one-off styling.
- **Security:** env secrets, CSP where relevant, sanitize user-facing HTML in WP.
- Ship with **smoke checks** (build, lint where configured, critical path).

## Workflow (summary)

1. Read target repo **`AGENTS.md`** and **`docs/README.md`** order.
2. Align URLs, meta, and Core Web Vitals **if** the site is SEO-facing.
3. Implement in small reviewable slices; verify in browser.

## Related first-party skills

| Skill | When |
|-------|------|
| [`../wordpress-publisher/SKILL.md`](../wordpress-publisher/SKILL.md) | Editor/publish workflows |
| [`../shipping-features/SKILL.md`](../shipping-features/SKILL.md) | Scoped feature delivery |
| [`../refactor-safely/SKILL.md`](../refactor-safely/SKILL.md) | Behavior-preserving cleanup |
| [`../debugging/SKILL.md`](../debugging/SKILL.md) | Defect investigation |

## Deep playbooks (Antigravity Awesome Skills)

| Role | Path |
|------|------|
| Next.js App Router | [`../antigravity-awesome-skills/skills/nextjs-app-router-patterns/SKILL.md`](../antigravity-awesome-skills/skills/nextjs-app-router-patterns/SKILL.md) |
| Next.js practices | [`../antigravity-awesome-skills/skills/nextjs-best-practices/SKILL.md`](../antigravity-awesome-skills/skills/nextjs-best-practices/SKILL.md) |
| React performance | [`../antigravity-awesome-skills/skills/react-best-practices/SKILL.md`](../antigravity-awesome-skills/skills/react-best-practices/SKILL.md) |
| shadcn / UI | [`../antigravity-awesome-skills/skills/shadcn/SKILL.md`](../antigravity-awesome-skills/skills/shadcn/SKILL.md) |
| WordPress | [`../antigravity-awesome-skills/skills/wordpress/SKILL.md`](../antigravity-awesome-skills/skills/wordpress/SKILL.md) |
| WP plugins | [`../antigravity-awesome-skills/skills/wordpress-plugin-development/SKILL.md`](../antigravity-awesome-skills/skills/wordpress-plugin-development/SKILL.md) |
| Frontend patterns (meta) | [`../antigravity-awesome-skills/skills/cc-skill-frontend-patterns/SKILL.md`](../antigravity-awesome-skills/skills/cc-skill-frontend-patterns/SKILL.md) |

**Order:** Repo standards and Rank Ray stack first; Antigravity for framework depth.
