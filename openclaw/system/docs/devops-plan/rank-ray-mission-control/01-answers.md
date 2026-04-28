# Rank Ray Mission Control — Phase 1: Answers & Locked Decisions

**Status:** LOCKED ✅  
**Date:** 2026-04-22

---

## 📋 Answers to Clarifying Questions

### 1. User Flow & Access Control
**Answer:** Admin-controlled user creation with email invites

**Details:**
- Super user (you) adds user emails
- User receives invite email → sets password → account created
- Role-based access control (RBAC) — you decide which modules each user sees
- **Client view:** Sees only their own data:
  - Their website(s)
  - SEO stats (ranks, audits)
  - Tasks tagged with their company/project
  - Invoices/quotes for their account
  - Project progress/milestones
- **Team view:** Sees assigned tasks, clients, projects
- **Admin view:** Full access, analytics, billing control

**Decision:** Multi-tenant with row-level security (RLS) + role-based module access

---

### 2. Development Approach
**Answer:** Phased build with audit checkpoints

**Details:**
- Build module by module
- After each phase: you audit, verify, approve
- Then proceed to next phase
- No skipping — each phase must pass audit

**Decision:** Waterfall-style phases with mandatory audit gates

---

### 3. Hosting
**Answer:** Self-hosted VPS

**Details:**
- Full control over server
- Cost-effective for SaaS
- Can scale as needed

**Decision:** VPS (DigitalOcean/Hetzner/Linode) with Docker

---

### 4. Integrations
**Answer:** All free integrations

**Required:**
- Google Search Console (SEO data)
- Google Analytics (traffic stats)
- WordPress (content publishing)
- Shopify (e-commerce clients)
- Stripe/PayPal (payments)
- Slack/Discord (notifications)
- Email (SMTP/SendGrid)

**Decision:** OAuth2-based integrations, free tier only

---

### 5. Mobile
**Answer:** Responsive web first, native app later

**Details:**
- Frontend must be fully responsive
- Mobile app only after web is finalized
- PWA consideration for interim

**Decision:** Mobile-responsive design from day 1

---

## ✅ PHASE 1 COMPLETE — ALL DECISIONS LOCKED

| Decision | Locked Value |
|----------|-------------|
| User creation | Admin invites via email |
| Access control | RBAC + row-level security |
| Client view | Own data only (website, SEO, tasks, invoices) |
| Development | Phased with mandatory audits |
| Hosting | Self-hosted VPS (Docker) |
| Integrations | Free tier: GSC, GA, WordPress, Shopify, Stripe |
| Mobile | Responsive web first, native app later |

---

**Next: Phase 2 — Tech Stack Research**
