---
name: cro-optimization
description: "Master playbook for Conversion Rate Optimization (CRO) and UX/UI audits. Use to optimize signup flows, user onboarding, form completions, paywalls, popups, and run 6-pillar heuristic UX audits."
risk: safe
source: community
date_added: "2026-06-03"
---

> **Parent Hub:** [[skills/_archived-2026-08-28/INDEX|📦 Archived Skills Hub]] · [[skills/_CATALOG_MAP|⚡ Skills Catalog]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Conversion Rate Optimization & UX Audit Playbook

## Overview
This playbook provides a unified framework for identifying conversion bottlenecks and optimizing user experience across B2B SaaS, mobile apps, marketplaces, and web applications. It covers signup flow friction, time-to-value onboarding, pricing/paywall upgrades, behavioral popups, field-level forms, and heuristic audits.

---

## 1. Initial Assessment & Goals
Before recommending or implementing changes, verify:
1. **Business Context:** B2B vs. B2C, monetisation model (freemium, free trial, paid, waitlist).
2. **Key Conversion Metric:** Signup completion, "Aha!" activation milestone, free-to-paid upgrade, or page checkout.
3. **Current Drop-off State:** Identify where the largest percentage of users bounce (e.g. email verification step, payment info form, or first-run empty state).

---

## 2. Signup Flow Optimization
### Friction Reduction Rules
* **Minimize Required Fields:** Collect only what is absolutely necessary to create the account (Email, Password, Name). Defer secondary details (role, company size, phone) to onboarding or progressive profiling.
* **Optimize Social Auth:** Place Google, Apple, or Microsoft SSO options prominently. Ensure clear visual separation from traditional email signup.
* **Inline Error Validation:** Validate inputs (email typos, password requirements) in real-time, not just on form submission. Keep error messages positive and specific.
* **Password Fields:** Include a show/hide toggle. Avoid rigid complexity rules; display strength indicators instead.
* **Single vs. Multi-step:** Use single-step for <=3 fields. Use multi-step with a progress bar for longer flows to reduce cognitive load, leading with low-friction inputs.

---

## 3. Onboarding & User Activation
### Time-to-Value (TTV)
* **Define the "Aha!" Moment:** Identify the exact action most highly correlated with long-term retention (e.g., inviting a teammate, installing a tracking pixel, exporting a file).
* **Onboarding Checklists:** Use 3–7 items ordered by immediate value. Include a progress indicator, start with a quick early win, and show the estimated time to complete.
* **Interactive Guided Tours:** Keep tours under 3-5 steps. Always allow users to dismiss the tour. Do not lock features behind tour completion.
* **Actionable Empty States:** Never show a blank screen. Empty states must explain the feature, show a preview/placeholder with dummy data, and present a clear primary CTA.
* **Re-engagement Loops:** Map behavior-based drip email sequences (e.g. welcome email, incomplete onboarding reminder at 24h, and feature highlights).

---

## 4. Form & Popup CRO
### Form Field Best Practices
* **Label Visibility:** Place descriptive labels above fields, not inside them as placeholders. Placeholder text should show examples, not labels.
* **Input Types:** Use mobile-native keyboards (`type="email"`, `type="tel"`, `autocomplete="username"`).
* **Tab Flow & Targets:** Maintain logical keyboard tab indices and design touch targets at 44x44px minimum.

### Behavioral Popups & Modals
* **Intrusive Interstitials:** Avoid interrupting the main user flow. Never show popups immediately upon page load.
* **Exit-Intent & Scroll Triggers:** Trigger popups when the user's cursor leaves the viewport or after they scroll 50%+ of the page.
* **Value-Exchange CTAs:** Offer direct value (templates, guides, discount codes) instead of generic newsletter subscriptions.

---

## 5. Paywall, Pricing & Upgrade Flows
* **Clear Value Comparison:** Use structured feature comparison tables showing pricing tiers side-by-side. Highlight the recommended plan.
* **Billing Transparency:** Display pricing clearly (e.g., "$19/mo billed annually"). Make the toggle between monthly and annual plans obvious.
* **Trial Expiry Reminders:** Send clear, non-deceptive reminders before free trials transition to paid subscriptions.
* **Frictionless Upgrades:** Keep card input fields inline. Never redirect the user to a separate checkout page if it can be done within the dashboard.

---

## 6. The 6-Pillar Heuristic UX Audit
Use these evaluation pillars to audit any interface:
1. **Aesthetic & Minimalist Design:** Eliminate page noise. Highlight primary actions with high-contrast buttons, while keeping secondary actions muted.
2. **Match Between System & Real World:** Use clear, user-friendly language. Avoid developer-facing jargon or internal system terms.
3. **User Control & Freedom:** Provide easy exits ("Cancel", "Dismiss", "Undo"). Let users go back in multi-step flows without losing entered data.
4. **Consistency & Standards:** Adhere to common web UI patterns (e.g. search in the top right, profile in the top right, primary CTA on the left/bottom).
5. **Error Prevention:** Disable buttons when inputs are invalid. Warn users before destructive actions (like deletion).
6. **Help & Documentation:** Keep contextual tooltips, FAQs, or contact support links easily accessible.

---

## 7. Metrics & Analytics
* **Signup Completion Rate:** `(Submits / Page Visits) * 100`
* **Onboarding Activation Rate:** `(Activated Users / Total Signups) * 100`
* **Form Field Drop-off:** Track which specific field causes users to exit the form.
* **NPS / In-app Feedback:** Ask qualitative questions ("What's blocking you?") to stalled cohorts.
