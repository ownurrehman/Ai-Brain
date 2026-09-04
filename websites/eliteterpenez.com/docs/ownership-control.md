> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Ownership & Administrative Control — eliteterpenez.com

Governance policy ensuring **3Devices / Mr Nas** maintains ultimate ownership, administrative control, and disaster recovery autonomy over `eliteterpenez.com`.

---

## 1. Core Mandate: Client Ownership

**The client (3Devices / Mr Nas) is the ultimate owner and primary administrator of all digital infrastructure, source code, customer databases, and payment assets.**

Developers build and maintain the platform; developers are **never** the sole owners or exclusive holders of administrative access.

---

## 2. Asset Ownership Checklist

| Digital Asset | Client Ownership (Mandatory) | Developer Access |
|---|---|---|
| **Domain Registrar (`eliteterpenez.com`)** | Client master account + 2FA + DNS control | View-only or none |
| **Hostinger Account (`u984013785`)** | Master account holder / primary billing | Collaborator or temporary build access |
| **Cloudflare DNS & Edge** | Client-owned Cloudflare zone + 2FA | Member / Administrator role |
| **WordPress Admin** | Primary Administrator under client's official email | Separate `dev-*` accounts, revocable at any time |
| **WooCommerce Customer Database** | Full access to customer orders, export tools, HPOS tables | Operational access for debugging |
| **Payment Gateway (Stripe/PayPal/Revolut)** | Client corporate entity & bank account | Developer restricted dashboard invite |
| **Offsite Backups** | Client-owned Google Drive, AWS S3, or Dropbox | Configured by dev; billing owned by client |
| **Mailbox (`info@eliteterpenez.com`)** | Client master mailbox | Forwarding only |

---

## 3. Account Lifecycle & Developer Handover

1. **Named Developer Accounts:**
   - All agency or developer accounts must use standardized prefixes (e.g. `dev-antigravity`, `dev-rankray`).
   - Generic shared accounts (e.g. `admin`) are strictly forbidden.
2. **Revocability:**
   - The client must be able to revoke developer access across WordPress, Hostinger, and Cloudflare in under 15 minutes without disrupting storefront operations.
3. **No Secrets in Repositories:**
   - Passwords, consumer secrets, and database credentials must never be committed to git repositories or Markdown files.
   - Credentials must be stored in a shared password manager vault owned by 3Devices.
