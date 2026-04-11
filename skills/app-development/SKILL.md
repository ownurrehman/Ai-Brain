---
name: app-development
description: Use for Rank Ray mobile app work—React Native, Flutter, or cross-platform delivery, store readiness, and native-adjacent concerns—not marketing websites (web-development) or headless SaaS-only APIs.
---

# Rank Ray — App development

**Agency:** [Rank Ray](https://www.rankray.com) — mobile applications for clients (cross-platform preferred unless native is specified).

## Use when

- **iOS / Android** apps, **React Native**, **Flutter**, or **Expo**-style projects.
- Scope includes **navigation, state, offline, push, deep links**, or **app store** listing prep.
- Client needs **one codebase** across platforms with shared business logic.

## Avoid when

- **Browser-only** product → **`../web-development/SKILL.md`**.
- **Backend-only** or **SaaS tenancy** as the core → **`../saas-development/SKILL.md`**.
- **Automation / agents** without a shipped app → **`../ai-automation/SKILL.md`**.

## Rank Ray delivery norms

- Confirm **minimum OS versions**, **device matrix**, and **release channel** (TestFlight, Play Internal, etc.).
- **Privacy:** permissions, data collection, store disclosures—flag gaps early.
- Prefer **thin native, thick shared** logic; avoid duplicate business rules across platforms.
- Plan **OTA vs store** update strategy if using Expo or similar.

## Workflow (summary)

1. Lock requirements: platforms, auth, offline, analytics, push.
2. Scaffold architecture (navigation, state, env/config, error boundaries).
3. Implement vertical slices with device testing.
4. Prepare store assets and review guidelines checklist.

## Related first-party skills

| Skill | When |
|-------|------|
| [`../shipping-features/SKILL.md`](../shipping-features/SKILL.md) | Milestone delivery |
| [`../debugging/SKILL.md`](../debugging/SKILL.md) | Crashes / regressions |
| [`../saas-auth-billing/SKILL.md`](../saas-auth-billing/SKILL.md) | If app uses subscriptions / Clerk-style auth |

## Deep playbooks (Antigravity Awesome Skills)

| Role | Path |
|------|------|
| Mobile (RN / Flutter / native patterns) | [`../antigravity-awesome-skills/skills/mobile-developer/SKILL.md`](../antigravity-awesome-skills/skills/mobile-developer/SKILL.md) |

**Order:** Client requirements and store constraints here first; use **`mobile-developer`** for deep implementation patterns. Add **`../antigravity-awesome-skills/skills/expo`** or similar via `scripts/find_antigravity_skill.py expo` if the stack is Expo-specific.
