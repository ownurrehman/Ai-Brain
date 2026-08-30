---
name: pii-data-sanitization
description: "Strict data redaction and masking rules to prevent client PII, health patient queries, passwords, and sensitive API secrets from leaking into models or git."
category: security
---

> **Parent Hub:** [[rules/INDEX|📜 Agency Operating Rules Hub]] · [[docs/ENV|🔑 Credentials Map]] · [[INDEX|🧠 Master Ai Brain Hub]]

# 🔒 Client PII & Sensitive Data Sanitization Directives

> **Absolute mandatory confidentiality guardrails for all agents handling patient, customer, financial, or authentication data.**

---

## 🚫 1. Zero-Leakage Data Categories

The following data classes must **NEVER** be committed to git, output in public markdown reports, or passed unmasked to third-party public LLMs:

1. **Healthcare / Patient Inquiries (e.g. Tonic Physio):** Patient names, phone numbers, injury intake forms, health card numbers, and insurance policy IDs.
2. **Financial Data (e.g. Coinsfera):** Bank account details, wallet private keys, crypto transaction amounts linked to personal identities.
3. **Authentication Secrets:** Application passwords, private tokens, OAuth client secrets, and `.env` files.

---

## 🛡️ 2. Redaction & Masking Rules

When logging test cases, email samples, or debug traces:
- **Names:** Replace with `[Client Name]` or pseudonym (`John D.`).
- **Emails:** Mask as `j***@domain.com` or `user@example.com`.
- **Phone Numbers:** Mask as `+1 (555) ***-****`.
- **API Keys / Secrets:** Strictly load from `master-env.env` via environment variables. Never hardcode inline string literals.

---

## 🔗 Related Systems
- [[docs/ENV|Credentials & Environment Mapping]]
- [[rules/rate-limiting|Rate Limiting Rules]]
- [[rules/INDEX|Agency Operating Rules Hub]]
