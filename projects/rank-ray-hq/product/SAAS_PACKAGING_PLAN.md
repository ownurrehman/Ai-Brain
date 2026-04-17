# SaaS packaging plan (parallel track)

Use when selling **many unrelated customers** on **one** hosted product (true multi-tenant SaaS). For **per-customer deployment** (single-tenant install), many items below simplify but **billing** and **updates** still need an owner.

## 1. Billing and entitlements

- **Stripe** (or equivalent): products, prices, trial, tax, invoices, customer portal for payment method updates.
- **Entitlements** mapped to workspace: module flags (e.g. SEO automation, future rank-intel), seat limits, website limits.
- **Webhook handlers** idempotent: `checkout.session.completed`, `customer.subscription.updated`, `invoice.paid`, `invoice.payment_failed`.
- **Grace period** and read-only mode when subscription lapses (avoid silent data loss).

## 2. Onboarding

- Self-serve **signup → workspace creation → invite flow**.
- **Email verification** and password reset (or SSO later).
- **Guided setup**: first company, first website, first integration (GSC or “run audit only”).
- Optional: **sample data** toggle clearly labeled as demo-only (aligns with [ROADMAP.md](../core/ROADMAP.md) / [RULES.md](../core/RULES.md) — no fake metrics for real workspaces).

## 3. Multi-tenant limits

- Hard limits: users per workspace, websites, API job concurrency, storage for uploads (finance receipts, video assets).
- **Rate limiting** per workspace on expensive endpoints (crawls, AI calls).
- **Observability**: per-tenant usage metrics for support and upsell.

## 4. Client portal ([ROADMAP.md](../core/ROADMAP.md) — planned)

- Separate **client role** with scoped access: approved reports, invoices, optional task comments.
- **No** full internal CRM/finance write access unless explicitly shared.
- Audit log of client actions.

## 5. Compliance and operations

- **Audit log** (already a backend direction per ARCHITECTURE) exposed for enterprise questions.
- **Data export**: company + websites + key financial summaries (format TBD; CSV/JSON minimum).
- **Backup strategy**: DB snapshots, RTO/RPO targets, restore drill documented.
- **Secrets**: no provider keys in client bundles; workspace-scoped server storage only.

## 6. Implementation sequencing (suggested)

1. Stripe + workspace entitlement flags + webhook hardening.  
2. Onboarding + invite email.  
3. Usage limits + rate limits on crawl/AI.  
4. Client portal MVP (read-only reports + invoices).  
5. Export + backup runbooks.

Cross-link implementation tasks to [RELEASE_GATE.md](./RELEASE_GATE.md) when gating production SaaS launch.
