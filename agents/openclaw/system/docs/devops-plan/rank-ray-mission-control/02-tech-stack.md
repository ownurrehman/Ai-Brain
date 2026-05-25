# Rank Ray Mission Control — Phase 2: Tech Stack Research

**Status:** IN PROGRESS  
**Date:** 2026-04-22  
**Researching:** Best technologies for modular SaaS

---

## 🎯 Research Goals

1. Find best stack for modular plugin architecture
2. Ensure scalability on VPS
3. Free-tier integrations
4. Self-hosted friendly
5. Mobile-responsive by default

---

## 1. FRONTEND FRAMEWORK

### Option A: React + Next.js 14 (App Router)
**Pros:**
- SSR/SSG for SEO
- Huge ecosystem
- App Router for layouts
- API routes built-in
- Vercel-style but self-hostable

**Cons:**
- Learning curve
- Heavy bundle
- Complex for beginners

**Best for:** SEO-focused SaaS, content-heavy apps

---

### Option B: Vue 3 + Nuxt 3
**Pros:**
- Gentle learning curve
- Excellent DX
- Auto-imports
- File-based routing
- Smaller bundle than React

**Cons:**
- Smaller ecosystem
- Fewer enterprise tools

**Best for:** Rapid development, team comfort

---

### Option C: SvelteKit
**Pros:**
- Fastest runtime
- Smallest bundle
- Built-in animations
- Server-side rendering

**Cons:**
- Smallest ecosystem
- Fewer UI libraries

**Best for:** Performance-critical, modern apps

---

### Option D: React + Vite (No Next.js)
**Pros:**
- Lightweight
- Fast HMR
- Full control
- No framework lock-in

**Cons:**
- Manual routing
- No SSR out of box

**Best for:** Custom architectures, full control

---

## 🏆 ENIGMA RECOMMENDATION: React + Next.js 14

**Reason:**
- App Router perfect for modular layout system
- API routes eliminate need for separate backend
- SSR helps with SEO module
- Largest ecosystem = more plugins/libraries
- Self-hostable with Docker
- File-based routing matches module structure

---

## 2. BACKEND/API

### Option A: Next.js API Routes (Monolith)
**Pros:**
- Same codebase as frontend
- Built-in auth
- Easy deployment

**Cons:**
- Tight coupling
- Harder to scale backend separately

---

### Option B: Express.js + Next.js Frontend (Decoupled)
**Pros:**
- Separate scaling
- Flexibility
- Standard REST

**Cons:**
- Two codebases
- More complex

---

### Option C: Fastify + Next.js
**Pros:**
- Faster than Express
- Better performance
- Plugin architecture matches our needs

**Cons:**
- Smaller ecosystem

---

## 🏆 ENIGMA RECOMMENDATION: Next.js API Routes (Monolith for MVP)

**Reason:**
- Single codebase = faster development
- API routes in `app/api/` = natural module separation
- Easy auth with NextAuth.js
- Can split later if needed

**Future:** If backend needs scaling → migrate to Fastify microservices

---

## 3. DATABASE

### Option A: PostgreSQL
**Pros:**
- Relational (perfect for our data model)
- Row-level security (RLS)
- JSONB for flexible module data
- Free, open-source
- Scales well

**Cons:**
- Requires management
- Setup complexity

---

### Option B: MySQL/MariaDB
**Pros:**
- Widely used
- WordPress uses it (familiar)

**Cons:**
- Less flexible than PostgreSQL
- No RLS

---

### Option C: MongoDB
**Pros:**
- Flexible schema
- Good for modules

**Cons:**
- No transactions easily
- No relational features

---

## 🏆 ENIGMA RECOMMENDATION: PostgreSQL

**Reason:**
- RLS = perfect for multi-tenant client isolation
- JSONB columns = module-specific data without schema changes
- Relational = clients, projects, tasks all linked
- Free and self-hosted

---

## 4. AUTHENTICATION

### Option A: NextAuth.js (Auth.js)
**Pros:**
- Built for Next.js
- OAuth providers (Google, GitHub)
- JWT or database sessions
- Free

**Cons:**
- v5 still evolving

---

### Option B: Clerk
**Pros:**
- Modern auth
- User management UI
- Organizations/multi-tenancy

**Cons:**
- Paid for scale
- Vendor lock-in

---

### Option C: Custom JWT
**Pros:**
- Full control
- No dependencies

**Cons:**
- Security risk if done wrong
- More code

---

## 🏆 ENIGMA RECOMMENDATION: NextAuth.js v5 (Auth.js)

**Reason:**
- Free
- Built for Next.js
- OAuth for integrations (Google, WordPress)
- Database sessions = track user activity
- Can add custom credentials provider for invite flow

---

## 5. CSS/STYLING

### Option A: Tailwind CSS
**Pros:**
- Utility-first
- Small bundle (purged)
- Consistent design system
- Easy to share across modules

**Cons:**
- HTML gets verbose
- Learning curve

---

### Option B: CSS Modules
**Pros:**
- Scoped styles
- No conflicts

**Cons:**
- Harder to share
- More files

---

### Option C: Styled Components / Emotion
**Pros:**
- Component-based
- Dynamic styles

**Cons:**
- Runtime overhead
- Larger bundle

---

## 🏆 ENIGMA RECOMMENDATION: Tailwind CSS

**Reason:**
- Master CSS file = all modules use same utilities
- No module-level CSS needed
- Configurable theme = brand consistency
- Small production bundle
- Easy for team to learn

---

## 6. STATE MANAGEMENT

### Option A: Zustand
**Pros:**
- Lightweight
- Simple API
- Good for module state

---

### Option B: Redux Toolkit
**Pros:**
- Mature
- DevTools

**Cons:**
- Boilerplate
- Overkill for most apps

---

### Option C: React Context + useReducer
**Pros:**
- Built-in
- No extra deps

**Cons:**
- Performance issues with large state

---

## 🏆 ENIGMA RECOMMENDATION: Zustand

**Reason:**
- Lightweight
- Perfect for module-specific state
- Can persist to localStorage
- Easy to learn

---

## 7. DATABASE ORM

### Option A: Prisma
**Pros:**
- Type-safe
- Auto-generated types
- Migrations
- Great DX

**Cons:**
- Query engine binary
- Build step

---

### Option B: Drizzle
**Pros:**
- Lightweight
- SQL-like syntax
- Faster

**Cons:**
- Newer, smaller community

---

### Option C: Raw SQL
**Pros:**
- Full control
- No overhead

**Cons:**
- Manual types
- Error-prone

---

## 🏆 ENIGMA RECOMMENDATION: Prisma

**Reason:**
- Type safety across frontend/backend
- Auto-generated API types
- Migration system for schema changes
- Excellent with Next.js

---

## 8. DEPLOYMENT & INFRASTRUCTURE

### VPS Setup
| Component | Tool | Purpose |
|-----------|------|---------|
| **Server** | Hetzner/DigitalOcean | $5-10/mo VPS |
| **OS** | Ubuntu 22.04 LTS | Stable, supported |
| **Container** | Docker + Docker Compose | Easy deployment |
| **Reverse Proxy** | Nginx | SSL, routing, static files |
| **SSL** | Let's Encrypt (Certbot) | Free HTTPS |
| **Database** | PostgreSQL 16 | Self-hosted |
| **Cache** | Redis | Sessions, cache, queues |
| **PM2** | Node.js process manager | Keep app running |
| **Monitoring** | Uptime Kuma | Free uptime monitoring |
| **Logs** | Loki + Grafana | Log aggregation |

---

## 9. INTEGRATIONS STACK

| Integration | Method | Free Tier |
|-------------|--------|-----------|
| **Google Search Console** | OAuth2 + API | ✅ Free |
| **Google Analytics** | OAuth2 + API | ✅ Free |
| **WordPress** | REST API + Application Password | ✅ Free |
| **Shopify** | REST API + OAuth | ✅ Free (partner) |
| **Stripe** | REST API | ✅ Free (2.9% + 30¢) |
| **PayPal** | REST API | ✅ Free |
| **Slack** | Webhooks + OAuth | ✅ Free |
| **Discord** | Webhooks + Bot API | ✅ Free |
| **Email** | SMTP or SendGrid | ✅ Free (100/day) |

---

## 📊 FINAL TECH STACK

| Layer | Technology | Reason |
|-------|-----------|--------|
| **Frontend** | Next.js 14 (App Router) | SSR, API routes, modular |
| **Backend** | Next.js API Routes | Single codebase, fast dev |
| **Database** | PostgreSQL 16 | RLS, relational, JSONB |
| **ORM** | Prisma | Type-safe, migrations |
| **Auth** | NextAuth.js v5 | Free, OAuth, sessions |
| **CSS** | Tailwind CSS | Master CSS, no module styles |
| **State** | Zustand | Lightweight, module-friendly |
| **UI Components** | shadcn/ui | Accessible, customizable |
| **Icons** | Lucide React | Consistent, lightweight |
| **Charts** | Recharts | React-native, customizable |
| **Tables** | TanStack Table | Sorting, filtering, pagination |
| **Forms** | React Hook Form + Zod | Validation, performance |
| **Server** | VPS (Hetzner/DigitalOcean) | Self-hosted, $5-10/mo |
| **Container** | Docker + Compose | Easy deploy |
| **Proxy** | Nginx | SSL, routing |
| **SSL** | Let's Encrypt | Free HTTPS |
| **Cache** | Redis | Sessions, queues |
| **Process** | PM2 | Keep app running |
| **Monitoring** | Uptime Kuma | Free uptime checks |
| **Logs** | Loki + Grafana | Free log aggregation |

---

## 💰 COST ESTIMATE (Monthly)

| Item | Cost |
|------|------|
| VPS (2 vCPU, 4GB RAM) | $6-10 |
| Domain | $1-2 |
| SSL (Let's Encrypt) | Free |
| Database (self-hosted) | Free |
| Monitoring (Uptime Kuma) | Free |
| Logs (Loki) | Free |
| **Total** | **$7-12/mo** |

---

**Next: Phase 3 — Architecture Design**
