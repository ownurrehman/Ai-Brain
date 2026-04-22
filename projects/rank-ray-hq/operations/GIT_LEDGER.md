# Git Ledger: Rank Ray HQ

This ledger tracks all significant feature merges, architectural pivots, and Phase-level task completions.

| Date | Phase | Task | Commit/Activity | Status |
| :--- | :--- | :--- | :--- | :--- |
| 2026-04-16 | Phase 4 | WordPress Bridge | Added `wordpress.controller.ts`, `wordpress.api.ts`, and connection UI in `ContentAutomation.tsx`. | **In Review** |
| 2026-04-17 | Phase 4.1| Domain-Feature Refactor | Restructured `src/modules/automation` into a hierarchical Domain-Feature architecture with `@automation` aliases. | **Active** |
| 2026-04-17 | Stability | Build Fix | Fixed literal `->` token in `AutomationWizard.tsx` blocking frontend builds. | **Fixed** |

## Architectural Standards (Pivot 2026-04-17)

All new UI modules must follow the **Hierarchical Module** pattern implemented in `src/modules/automation`:

- **Root**: `src/modules/[domain]/`
- **Modules**: `src/modules/[domain]/modules/[feature]/`
- **Shared Logic**: `src/modules/[domain]/core/` (services, constants, hooks)
- **Path Aliases**: Use `@[domain]/*` for internal imports to simplify nesting.
- **Complexity Limit**: No single `.tsx` file should exceed 10KB/300 lines (Refactor & Modularize if exceeded).
