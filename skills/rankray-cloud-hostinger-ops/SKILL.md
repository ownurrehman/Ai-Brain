---
name: rankray-cloud-hostinger-ops
description: "Cloud hosting operations: Hostinger API management, LiteSpeed cache purging, SSL certificates, DNS automation, and deployment pipelines."
---

# ☁️ RankRay Cloud & Hostinger Infrastructure Ops

> **Standard operating procedures for managing Hostinger cloud environments and DNS.**

---

## 🔑 1. API Authentication & Endpoints
- Base URL: `https://developers.hostinger.com/api/hosting/v1/`
- Header: `Authorization: Bearer <HOSTINGER_API_TOKEN>`
- Core Operations:
  - List Websites: `GET /websites`
  - Get Resource Usage: `GET /orders/{order_id}/resource-usage`
  - Clear LiteSpeed Cache: `POST /websites/{domain}/cache/purge`

---

## 🛡️ 2. Deployment Safeguards
1. **Pre-Deploy Verification:** Always test production builds locally (`npm run build` or `composer check`).
2. **Backup Point:** Ensure WordPress or database backups are generated before executing major core or plugin updates.
3. **Post-Deploy Cache Bust:** Immediately purge LiteSpeed and browser caches upon deploying critical asset changes.
