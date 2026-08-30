> **Parent Hub:** [[memory/procedural/INDEX|🛠️ Procedural Memory Hub]] · [[skills/rankray-cold-email-outreach/SKILL|Cold Email Outreach]] · [[INDEX|🧠 Master Ai Brain Hub]]

# ✉️ SOP: B2B Cold Email Campaign Execution

> **Standard operating procedure for launching high-converting, deliverability-protected B2B outreach sequences.**

---

## 🎯 Phase 1: Lead List Build & Enrichment
1. **Target Criteria:** Decision makers (Founder, CMO, Head of Marketing) in targeted geos (USA, Canada, UAE).
2. **Scrape & Enrich:** Run `scripts/enrich_missing_emails_v5.py` and `scripts/enrich_contact_details_v2.py`.
3. **SMTP Hygiene:** Run `scripts/validate_emails_v2.py` and `scripts/remove_unemailable_leads.py`. Ensure $<2\%$ hard bounce threshold.

---

## ✍️ Phase 2: Copywriting & Custom Diagnostics
1. **Word Count:** 80–110 words maximum.
2. **Hook:** Specific pain point or gap detected on prospect's website (e.g. *"Noticed your site ranks #14 for [City + Service], losing ~350 monthly visits to [Competitor]"*).
3. **CTA:** Frictionless interest CTA (*"Open to a 3-minute video breakdown of how to reclaim that spot?"*).

---

## 🚀 Phase 3: Launch & Inbox Monitoring
1. **Daily Send Volume:** Max 35 emails/inbox/day to protect deliverability.
2. **Inbox Listener Cron:** Automated hourly check via AgentMail (`sheikhown@agentmail.to`).
3. **Lead Logging:** All positive responses logged to sheet and escalated to user via WhatsApp within 15 minutes.
