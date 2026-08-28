> **Parent Agent:** [[agents/emilia/MEMORY|🤖 Emilia Dossier]] · [[agents/FLEET-ORCHESTRATION|Agent Fleet]] · [[INDEX|🧠 Ai Brain]]

# 2026-08-17 — Rank Ray Outreach Batch 1

## What happened
- Oliver asked me to revive the dead outreach pipeline. The Ai Brain had 382 drafts from May/June 2026 that were never sent (no inbox at the time).
- Verified sheikhown@agentmail.to is live (AgentMail free tier, REST API).
- Reviewed the 5 sample drafts I'd prepared; Oliver said they were bad (rightly — AI slop).
- Oliver decided: use main Hermes to rewrite 10 emails with real observations scraped from each prospect's site, vertical diversity.
- Delegated to Hermes (deleg_aa558b45) — completed in 16m 41s. 10 plain-text drafts in /tmp/emails_v2/, all compliance checks passed.
- Oliver approved all 10. I built `/tmp/send_batch.py` and fired.

## What got sent
8/10 sent successfully at 2026-08-18T04:19:24Z → 04:20:28Z:
- Al MAFHOOM TECH LLC (info@almafhoom.ae)
- Klinika Dental Clinic (info@klinika.ae)
- AME Auto Repairing Garage (info@ameauto.ae) — domain hijack observation
- MADO UAE (marketing@mado.ae) — "0 + Years of Heritage 0 + Locations 0" placeholder
- Law Firm UAE (info@lawfirmuae.com)
- LuxuryProperty.com (hello@luxuryproperty.com)
- GymNation (getfit@gymnation.com)
- Medcare (info@medcare.ae)

## What's queued
2/10 hit AgentMail new-account 10-recipient/week limit. Limit lifts 2026-08-20T13:08:07Z:
- Al Habtoor Hospitality (info@habtoorhospitality.com)
- Washmen (Support@washmen.com)

## Key technical lessons (record in MEMORY.md)
1. **AgentMail send endpoint**: REST is POST `/v0/inboxes/{id}/messages/send` (WITH `/send` suffix).
   The skill doc at `~/.hermes/skills/email/agentmail/SKILL.md` shows `client.inboxes.messages.send()` which works,
   but raw REST needs the `/send` suffix. POST `/messages` alone returns 404 "Route not found".
   Verified 2026-08-17 during batch send.
2. **AgentMail free tier**: 10 distinct recipients per week for new accounts. Sender itself counts (so 10 not 11).
   Limit lifts 7 days after inbox creation. After first 7 days, higher quotas apply.
3. **Labels**: Messages labeled `sent` are outbound, `received` are inbound. Use this to filter replies vs sent.
   `from_` field is empty on outbound messages — rely on labels, not the from field, to detect replies.
4. **Self-test cost**: My endpoint-probe self-test email to oliver@rankray.com used up 1 of the 10-recipient budget.
   Net result: only 9 prospects got the first batch instead of 10. Lesson: probe with non-recipient endpoints
   (use a dry-run mode or check docs first, don't waste recipient slots on tests).
5. **Inbox check script**: `/tmp/inbox_check.py` — uses `/messages?labels=outreach&limit=50` (not `/threads` which
   returned stale data during testing). Groups by `thread_id` to identify which threads need replies.

## Cron job
- Created job `6efc5efc71a7` named `rankray-outreach-reply-monitor`, runs every 6h, deliver=origin,all
- It will:
  - Run /tmp/inbox_check.py
  - Surface any replies to the 8 sent emails
  - NOT auto-reply (human-in-the-loop)
  - After 2026-08-20T13:08:07Z: also retry sending the 2 queued (Habtoor + Washmen)

## Follow-up plan (after 2026-08-20 limit lift)
- Day 3 (2026-08-21): Send follow-up #1 to anyone who hasn't replied. New value: a specific shortlist of fixes
  (1-page pdf or inline) — NOT "just checking in."
- Day 7 (2026-08-25): Send follow-up #2 with a case study or specific resource. Then close the loop.

## Files in workspace
- Drafts: /tmp/emails_v2/email_01..10_*.json
- Send script: /tmp/send_batch.py (with corrected endpoint)
- Reply monitor: /tmp/inbox_check.py
- Send log: /Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/agents/emilia/memory/2026-08-17/batch1-send-log.json
