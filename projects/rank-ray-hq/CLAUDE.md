# 🧠 Rank Ray HQ: Master Intelligence Guide

Welcome to the **"Super App" Command Center**. This master guide defines the separation of logic between our Planning and Building engines.

---

## 🏛️ Project Hierarchy (The Brain vs. The Engine)

| Layer | Path | Purpose |
| :--- | :--- | :--- |
| **The Router** | `CLAUDE.md` | This file. Your single entry point and AI index. |
| **The Brain** | `.claude/rules/` | Protocols, Elite Styling, and Verification Checklists. |
| **The Blueprint** | `docs/` | Roadmap (`rebirth-tracker.md`), Research, and Phase Plans. |
| **The Memory** | `todo.md` / `changelog.md` | Active task tracking and historical wins. |
| **The Logic** | `SEO Engine Ai - wordpress old plugin/` | Reference patterns from the legacy engine. |
| **The Engine** | `rankray-hq-backend/` | The core NestJS / Prisma automation engine. |
| **The Control** | `rankray-hq-frontend/` | The premium, high-density React Dashboard. |

---

## 🔄 Dual-AI Workflow: Planner & Builder

To maximize token efficiency and code quality, we follow a strict separation of duties:

### 1. Antigravity (The Navigator & Verifier)
*   **Role**: Strategic Architect, DevOps Lead, and Primary Verifier.
*   **Action**: I plan the phases, analyze the legacy logic, and **provide execution prompts** for the UI/UX.
*   **Terminal**: I am the only one who runs `npx`, `prisma`, `tsc`, and verification audits.
*   **Goal**: Zero code leakage, 100% architectural integrity.

### 2. Claude (The Pilot & Builder)
*   **Role**: Primary Feature Coder.
*   **Action**: Receives prompts from Antigravity. Performs the actual code modifications in `.tsx` and `.ts` files.
*   **Goal**: Rapid implementation of the "Elite" UI/UX density and backend business logic.

---

## 🛠️ Operational Protocol

1.  **Phase Initialization**: Check `docs/operations/rebirth-tracker.md` for the current objective.
2.  **Prompt Handoff**: Antigravity creates an **Implementation Plan** and **Claude Prompts**.
3.  **Building**: Feed the prompts to Claude.
4.  **Verification**: Once Claude finishes, Antigravity executes `npx tsc -b` and verification tests.
5.  **Audit**: Antigravity performs a final code review before marking the task in `todo.md`.

---

## 🚀 Active Commands

-   `npm run dev:all:clean`: Purge processes and launch HQ stack.
-   `npx tsc -b`: Terminal-wide TypeScript verification.
-   `npx prisma studio`: Audit relational data in the "Brain".

**OPERATIONAL LOCK**: Do not deviate from the roadmap in `rebirth-tracker.md` without explicit permission.
