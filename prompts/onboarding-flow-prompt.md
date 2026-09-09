> **Parent Hub:** [[prompts/INDEX|🎯 Prompts Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# 🚀 Master Prompt: Frictionless SaaS Onboarding & User Activation

**Role:** Principal Product Growth Engineer, UX/UI Architect, and Retention Specialist.  
**Objective:** Design and engineer a frictionless signup, onboarding, and activation workflow for `[Product Name]` that minimizes Time-To-Value (TTV) and maximizes day-1 activation.

---

## 📋 Input Parameters (Fill Before Prompting)

* **Product / App Name:** `[e.g. RankRay SEO Engine / Agency Hub]`
* **Core "Aha! Moment" (Activation Event):** `[The single milestone that proves value, e.g. Generating first audit report / connecting first domain]`
* **Target User Persona (ICP):** `[e.g. Agency Marketers / Solo Developers / Non-technical Store Owners]`
* **Target Time-To-Value (TTV):** `[e.g. Under 60 seconds from signup to first outcome]`
* **Application Tech Stack:** `[e.g. Next.js 14 App Router, Tailwind CSS, Prisma, PostgreSQL, Supabase/NextAuth]`

---

## ⚡ 1. Boot Sequence (Execute Before Code/Flow Design)

1. **Locate Target Project:** Check `INDEX.md` and read `projects/{name}/mastersheet.md` or `websites/{domain}/mastersheet.md`.
2. **Review Tech Stack & Architecture:** Inspect project architecture docs and API conventions.
3. **Load Production Skills & Frameworks:**
   * **Activation Metrics:** `.agents/skills/startup-metrics-framework/SKILL.md` (Funnel drop-off, activation milestones, user retention)
   * **CRO Copywriting:** `skills/rankray-cro-copywriting/SKILL.md` (Value-driven microcopy and momentum hooks)
   * **Frontend Engineering:** `skills/rankray-react-nextjs-engineering/SKILL.md` (State machines, optimistic UI, progressive disclosure)
   * **Backend API Engineering:** `skills/rankray-backend-api-engineering/SKILL.md` (Clean auth controllers, session persistence)

---

## 🗺️ 2. The 7-Stage Frictionless Onboarding Blueprint

Structure the onboarding experience using this high-momentum activation sequence:

### 1. Minimalist Signup & Authentication
* **Single-Click Auth:** Support OAuth (Google, GitHub) alongside email magic link / single-field passwordless login.
* **Deferred Verification:** Allow immediate entry into the app upon signup. Verify email asynchronously or during sensitive operations to prevent drop-off before first value.
* **Minimalist UI:** Clean split-screen or centered card with zero navigation escape hatches and a prominent testimonial badge.

### 2. Progressive Profiling & Branching (15–30 Seconds)
* **Goal Alignment Survey:** Exactly 1 or 2 targeted questions (e.g. *"What do you want to accomplish first?"* with 3 visual options).
* **Dynamic Journey Branching:** Personalize the dashboard layout and recommended next step based on the selected use case.
* **Progress Bar:** High-clarity indicator (e.g. *"Step 2 of 3 • 30 seconds remaining"*).

### 3. Killing the Blank Slate (Pre-Populated States)
* **Never show a dead empty screen:** Pre-load realistic sandbox data or sample projects so users immediately see what the completed outcome looks like.
* **Interactive One-Click Templates:** Provide single-click starter templates or demo workspaces.

### 4. Direct Route to the "Aha! Moment"
* **Bypass Long Guided Tours:** Replace 10-step unskippable modal tours with contextual, inline spot-action prompts.
* **Interactive First-Run Checklist:** A collapsible 3-step checklist pinned to the corner with real-time checkmarks:
  1. Connect your domain / Create first project
  2. Run initial analysis / Trigger first event
  3. View your live dashboard / Export first result
* **Micro-Celebration:** Deliver subtle, satisfying dopamine cues (smooth green checkmarks, micro-animations) when the primary milestone is reached.

### 5. High-Momentum UX Microcopy
* Every button label must state the immediate reward, never generic administrative words:
  * ❌ *Submit*, *Next*, *Continue*
  * ✅ *Launch My Workspace*, *Generate Free Audit*, *View My Dashboard*

### 6. Bulletproof Form Usability & Error Recovery
* Real-time inline field validation (feedback on blur, not on form submit).
* Auto-save all entered state in local storage so page refreshes or connection drops never wipe user inputs.
* Clear, non-punitive error messages that tell users exactly how to fix the issue.

### 7. Telemetry & Analytics Instrumentation
* Instrument precise analytics events for every funnel gate:
  * `onboarding_started`
  * `signup_method_selected (google | email)`
  * `profile_goal_selected`
  * `aha_moment_triggered` (Primary Activation KPI)
  * `checklist_completed`

---

## 🚨 3. Engineering & UX Non-Negotiables

* **Zero Blockers:** Never lock a new user behind mandatory billing or credit card forms before they experience core value unless explicitly instructed.
* **Mobile-First Responsiveness:** Every onboarding card and modal must render flawlessly on mobile viewport sizes (touch-friendly targets $\ge$ 44px).
* **Keyboard Navigable:** Full WCAG 2.1 AA keyboard support (`Tab`, `Enter`, `Escape` to dismiss modals).
* **Fast Load Times:** Keep initial onboarding assets lightweight (CWV LCP < 1.5s).
