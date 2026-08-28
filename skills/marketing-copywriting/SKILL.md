---
name: marketing-copywriting
description: "Master playbook for conversion copywriting, email marketing, and cold outreach. Includes Copy Brief locks, direct response copy rules, B2B cold email design, lifecycle nurture sequences, deliverability standards (SPF/DKIM/DMARC), and behavioral psychology triggers."
risk: safe
source: community
date_added: "2026-06-03"
---

# Copywriting, Marketing & Email Outreach Playbook

## Overview
This playbook provides a unified standard for writing high-converting marketing copy, cold outreach sequences, and automated drip campaigns. It ensures copy is research-backed, clear, credible, and technically configured for deliverability.

---

## 1. Copy Brief Lock (Mandatory Gate)
Before writing any marketing copy, you must present a **Copy Brief Summary** to the user and obtain approval:
* **Page/Campaign Goal:** Primary action (CTA) and secondary action (if any).
* **Target Audience:** Exact role, primary problem they face, and objections/hesitations.
* **Core Value Proposition:** What is being offered and the primary differentiator/transformation.
* **Awareness Level:** Traffic source and current understanding (unaware, problem-aware, solution-aware, product-aware).
* **Assumptions:** List any assumptions explicitly.

> **DO NOT proceed to writing copy until the brief has been explicitly confirmed by the user.**

---

## 2. Direct Response Copywriting Principles
* **Clarity Beats Cleverness:** Choose clear, simple words over complex jargon.
* **Feature → Benefit → Outcome:** Every feature must map to a tangible customer benefit and ultimate outcome (e.g. "Auto-saving [Feature] ensures you never lose progress [Benefit], giving you complete peace of mind [Outcome]").
* **Write to One Person:** Use active voice and write directly to a single reader using "you/your".
* **No Exaggerated Claims:** Never fabricate testimonials, metrics, or guarantees. Mark empty statistics placeholders clearly as `[Metric Placeholder]` for the user to populate.
* **Scannable Layouts:** Use short paragraphs (maximum 3 sentences or 60 words for web copy) and bullet points.

---

## 3. B2B Cold Email & Outreach
### Writing Standards
* **Write Like a Peer, Not a Vendor:** The tone should sound like a colleague noticed something relevant and is sharing a helpful note. Avoid formal openings like "I hope this email finds you well" or "My name is...".
* **Ruthlessly Brief:** Keep cold emails under 100-150 words. Every sentence must earn its place.
* **Short, Lowercase Subject Lines:** Use 2–4 word, internal-looking subject lines (e.g. "reply rates", "hiring ops", "tonic physio"). Never use emojis, urgency tricks, or capitalization.
* **Interest-Based CTAs:** End with low-friction, conversational questions (e.g. "Worth exploring?", "Open to a quick look?") rather than asking for a 30-minute call.
* ** Cadence & Rotation:** Use 3–5 total touches. Each follow-up must add new value (a fresh case study, a helpful resource, or a new angle) rather than "just checking in".

---

## 4. Lifecycle & Nurture Sequences
### Welcome Series Cadence (7-Part Framework)
1. **Email 1 (Immediate):** Welcome + deliver what was promised (lead magnet or access).
2. **Email 2 (Day 1-2):** Enable a quick win (simplest value action).
3. **Email 3 (Day 3-4):** Origin story (why we built this, building an emotional connection).
4. **Email 4 (Day 5-6):** Social proof (case study or customer testimonial).
5. **Email 5 (Day 7-8):** Addressing objections (common hesitations and reframing them).
6. **Email 6 (Day 9-11):** Feature spotlight (highlighting an underused capability).
7. **Email 7 (Day 12-14):** Conversion offer (direct call-to-action to upgrade/buy).

---

## 5. Email Systems & Deliverability
Before launching any outreach or newsletter campaign, ensure the following technical checks are met:
* **Authentication:** Verify that **SPF**, **DKIM**, and **DMARC** records are correctly configured on the sending domain.
* **Domain Reputation:** Use secondary domain variants for cold outreach to protect the primary domain's reputation.
* **Custom Tracking Domains:** Set up a custom tracking domain in your sending tool (e.g., Instantly, Lemlist, Hubspot) to avoid shared tracking IP blacklists.
* **Warm-up Protocols:** Warm up new sending accounts for a minimum of 14-21 days before initiating active campaigns, starting with low volumes (<5-10 per day) and scaling gradually.
* **Plain Text Primacy:** Keep outreach emails in plain text format. Avoid HTML templates, heavy images, or multiple links, as they trigger spam filters.

---

## 6. Psychology & Cognitive Triggers
* **Empathy-First Problem Framing (PAS):** Define the **P**roblem, **A**gitate the pain, and then present the **S**olution.
* **Risk Reversal:** Always offer risk mitigation (e.g., "14-day free trial, no credit card required" or "money-back guarantee").
* **Authority & Proof:** Lead with measurable metrics (e.g. "40% faster loading times") and logo bars.

## 7. Cold Email Anti-AI Checklist (Mandatory Pre-Send Gate)

LLM-written cold emails are *immediately recognizable* and get deleted without being read. Before showing any cold email draft to the user OR queuing it for send, the draft MUST pass this checklist. **If it fails, rewrite and re-check. Do not ship a draft that fails this checklist "to see what the user thinks."**

Run `scripts/cold_email_check.py <draft_file>` (in this skill's `scripts/` directory) for an automated pass/fail. The script flags every line that violates the rules below.

### Subject line rules
* 2–4 words, lowercase, internal-looking. `plumbing sharjah` ✓ — `Your Sharjah plumbing site is using generic template content` ✗ (clickbait + em-dash + tells the recipient it's an SEO audit pitch before open).
* No em-dashes (`—`), no emojis, no all-caps, no urgency tricks (`urgent`, `act now`, `last chance`).

### Body rules
* **Plain text only.** No HTML, no images, no multiple links. (HTML + images hurt deliverability and feel like a blast.)
* **Opener**: just `Hi,` followed by a sentence with substance. **Banned**: `Hi there,`, `I hope this email finds you well`, `My name is...`, `Great to e-meet you`.
* **Sign-off**: just the first name (`Oliver`). **Banned**: `Best,`, `Kind regards,`, `Best, Rank Ray Team` (brand not person), full name + title + phone + calendar link (looks like a footer template).
* **CTA**: low-friction. `Reply if useful.` / `Worth a look?` / `Reply and I'll send.` **Banned**: `Worth a 10-minute call?`, `Book a free audit`, `Schedule a consultation`, anything that asks for calendar time in the first touch.
* **No em-dashes** anywhere in the body. Use commas, periods, or parentheses.
* **No "It's not just X; it's Y"** negative parallelism.
* **No rule-of-three** in prose ("X, Y, and Z" lists of features/pain points read as AI).
* **No fake case studies**, fabricated metrics, or "we got client X from Y to Z in 90 days" claims. Only include specifics you can defend if the recipient replies `actually our schema is fine`.
* **No buzzwords**: `leverage`, `robust`, `seamless`, `cutting-edge`, `transformative`, `elevate`, `unlock`, `navigate the landscape`, `in today's world`, `dive deep`.
* **1–2 REAL observations** from the actual prospect website per email. If you didn't scrape the site, say so to the user and ask whether to fetch first OR write with explicitly framed industry-pattern observations. **Default to scraping first** — credibility beats speed.

### Word count
* 80–110 words for the first touch. Hard cap 150. If you can't fit it in 110, you have fluff.

### Workflow rule (added 2026-08-17)
* Never show a user a "preview" of cold emails that fails this checklist. Show only drafts that pass the checker, or show the *raw* research/scratch and ask before composing. Showing AI-slop drafts burns trust and wastes the user's review time.

## 8. Reference: Humanizer Skill
For the full 34-pattern anti-AI writing guide (em-dash overuse, copula avoidance, false ranges, sycophantic tone, etc.), load the `humanizer` skill. The cold-email checker script in `scripts/` encodes the most common cold-email-specific violations; the humanizer covers general prose.

## 9. Templates
See `templates/cold_email_good.txt` and `templates/cold_email_bad.txt` for side-by-side examples that pass and fail this checklist. Copy the good template as a starting point; the bad one exists so the differences are visible at a glance.

## Common Pitfalls
* **HTML signatures hurt deliverability.** A fancy signature block with logos, social icons, and calendar links can single-handedly drop a campaign into spam. Plain-text sign-off is part of the plain-text-primacy rule.
* **Em-dashes sneak in.** Watch for the `—` character in copy-pasted output. `text` mode in Python f-strings often auto-converts `--` to em-dash. Strip them before sending.
* **Brand-as-person sign-off loses replies.** `Best, Rank Ray Team` is treated like a marketing blast; `Oliver` reads as a person worth replying to. The reply rate gap is usually 2–4x.
* **Asking for calendar time on first touch kills response rates.** The first email should offer value (an audit, a snippet, an example) and let the recipient volunteer for the call. Asking for 10 minutes before giving anything reads as salesy.
* **Showing unchecked drafts to the user is a process bug.** If the user has to tell you the drafts are bad, you've skipped the pre-flight gate. Run the checker first, fix what fails, *then* show the user.
