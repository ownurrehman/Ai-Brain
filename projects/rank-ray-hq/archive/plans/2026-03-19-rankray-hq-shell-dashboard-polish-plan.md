# RankRay HQ Shell + Dashboard Polish Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Deliver a 2-3 day, demo-first UI polish for shell + dashboard so RankRay HQ feels sellable, calm, and premium without changing core business logic.

**Architecture:** Keep current React + Tailwind + Framer Motion architecture, but introduce a tighter visual system (spacing/type/surface/motion), then apply it to shell and dashboard only. Use test-first Playwright checks for visible polish regressions and preserve existing navigation/store behavior.

**Tech Stack:** React 19, TypeScript, Tailwind CSS, Framer Motion, shadcn/Radix UI, Playwright, ESLint

---

### Task 1: Add Failing Demo-Polish E2E Guardrails

**Files:**
- Create: `rankray-hq-frontend/e2e/shell-dashboard-polish.spec.ts`
- Modify: `rankray-hq-frontend/e2e/dashboard.spec.ts`
- Test: `rankray-hq-frontend/e2e/shell-dashboard-polish.spec.ts`

**Step 1: Write the failing test**

Add a new Playwright spec that asserts:
- Header and sidebar are visible and stable on `/dashboard`
- Dashboard top area exposes concise trust markers (no flashing overlay after initial load)
- Quick actions and quick links are visible with consistent sizing hooks via `data-testid`
- Main content container uses new density tokens/classes (to be added in Task 2)

Use this shape:

```ts
import { test, expect } from './fixtures';
import { loginAsAdmin } from './utils';

test('shell + dashboard polish baseline', async ({ page }) => {
  await loginAsAdmin(page);
  await page.goto('http://127.0.0.1:5173/dashboard');
  await expect(page.getByTestId('sidebar-root')).toBeVisible();
  await expect(page.getByTestId('app-main-content')).toHaveClass(/rr-surface-main/);
  await expect(page.getByTestId('dashboard-heading')).toBeVisible();
  await expect(page.getByTestId('dashboard-module-links')).toBeVisible();
});
```

**Step 2: Run test to verify it fails**

Run: `cd rankray-hq-frontend && npm run test:e2e -- e2e/shell-dashboard-polish.spec.ts`
Expected: FAIL on missing new class/test hooks.

**Step 3: Add minimal test hooks in existing dashboard test**

In `dashboard.spec.ts`, keep existing route assertions and add one assertion for the future polish hooks (`app-main-content` class or new quick-action IDs), so both legacy flow and new polish are covered.

**Step 4: Re-run targeted test**

Run: `cd rankray-hq-frontend && npm run test:e2e -- e2e/dashboard.spec.ts e2e/shell-dashboard-polish.spec.ts`
Expected: still FAIL until UI implementation lands.

**Step 5: Commit**

```bash
git add rankray-hq-frontend/e2e/shell-dashboard-polish.spec.ts rankray-hq-frontend/e2e/dashboard.spec.ts
git commit -m "test: add shell and dashboard polish guardrails"
```

### Task 2: Define Calm Enterprise Surface + Motion Tokens

**Files:**
- Modify: `rankray-hq-frontend/src/index.css`
- Modify: `rankray-hq-frontend/src/App.tsx`
- Test: `rankray-hq-frontend/e2e/shell-dashboard-polish.spec.ts`

**Step 1: Add failing style expectations in test**

Extend `shell-dashboard-polish.spec.ts` to expect:
- Main surface class (`rr-surface-main`)
- Standardized content spacing class (`rr-content-spacing`)
- No large animated mesh elements present (or present only in reduced subtle mode)

**Step 2: Run test to verify it fails**

Run: `cd rankray-hq-frontend && npm run test:e2e -- e2e/shell-dashboard-polish.spec.ts`
Expected: FAIL because new classes are not yet implemented.

**Step 3: Implement minimal CSS token layer**

In `src/index.css`, add a dedicated utility block for this pass:
- `rr-surface-main` (clean background, minimal visual noise)
- `rr-content-spacing` (consistent padding/gap)
- `rr-card` and `rr-card-muted` (reduced glass/no heavy glow)
- `rr-motion-fast` and `rr-motion-base` classes (duration/easing consistency)

Keep old utilities for compatibility; do not delete broad legacy styles in this task.

**Step 4: Apply App shell token classes**

In `src/App.tsx`:
- Replace heavy gradient mesh container with subtle static/de-emphasized decorative layer
- Apply `rr-surface-main` to app root and `rr-content-spacing` to content region
- Normalize module transition timing to a single motion profile

**Step 5: Run test to verify pass**

Run: `cd rankray-hq-frontend && npm run test:e2e -- e2e/shell-dashboard-polish.spec.ts`
Expected: PASS.

**Step 6: Commit**

```bash
git add rankray-hq-frontend/src/index.css rankray-hq-frontend/src/App.tsx rankray-hq-frontend/e2e/shell-dashboard-polish.spec.ts
git commit -m "feat: introduce calm shell surface and motion tokens"
```

### Task 3: Normalize Header + Sidebar Interaction Language

**Files:**
- Modify: `rankray-hq-frontend/src/components/layout/Header.tsx`
- Modify: `rankray-hq-frontend/src/components/layout/Sidebar.tsx`
- Test: `rankray-hq-frontend/e2e/sidebar-navigation.spec.ts`
- Test: `rankray-hq-frontend/e2e/shell-dashboard-polish.spec.ts`

**Step 1: Write failing interaction assertions**

In `shell-dashboard-polish.spec.ts`, assert:
- Header quick create trigger is visible and not oversized
- Sidebar nav remains stable after module click (no unintended jump/flash)
- Search trigger and profile controls remain accessible at demo resolution

**Step 2: Run tests to verify they fail**

Run: `cd rankray-hq-frontend && npm run test:e2e -- e2e/sidebar-navigation.spec.ts e2e/shell-dashboard-polish.spec.ts`
Expected: FAIL on sizing/stability expectations.

**Step 3: Implement minimal header polish**

In `Header.tsx`:
- Tighten title/subtitle visual hierarchy (reduce loud accent colors in normal state)
- Standardize control heights (`h-9` family) for search/theme/notifications/create
- Keep same actions, routes, and menu content; only polish visual and interaction consistency

**Step 4: Implement sidebar polish**

In `Sidebar.tsx`:
- Normalize nav row height/spacing and active-state contrast
- Reduce decorative gradients where not informational
- Keep expand/collapse and routing behavior unchanged

**Step 5: Re-run tests**

Run: `cd rankray-hq-frontend && npm run test:e2e -- e2e/sidebar-navigation.spec.ts e2e/shell-dashboard-polish.spec.ts`
Expected: PASS.

**Step 6: Commit**

```bash
git add rankray-hq-frontend/src/components/layout/Header.tsx rankray-hq-frontend/src/components/layout/Sidebar.tsx rankray-hq-frontend/e2e/shell-dashboard-polish.spec.ts
git commit -m "feat: polish header and sidebar for consistent demo UX"
```

### Task 4: Dashboard Hierarchy + Trust-State Cleanup

**Files:**
- Modify: `rankray-hq-frontend/src/modules/dashboard/Dashboard.tsx`
- Modify: `rankray-hq-frontend/src/modules/dashboard/components/DashboardWidgets.tsx`
- Test: `rankray-hq-frontend/e2e/dashboard.spec.ts`
- Test: `rankray-hq-frontend/e2e/shell-dashboard-polish.spec.ts`

**Step 1: Add failing dashboard-specific assertions**

In `dashboard.spec.ts`, add checks for:
- Stable heading block and concise status indicator
- Quick links container with normalized button sizing
- No blocking full-screen loading overlay after initial data fetch

**Step 2: Run test to verify failure**

Run: `cd rankray-hq-frontend && npm run test:e2e -- e2e/dashboard.spec.ts`
Expected: FAIL on new trust/state constraints.

**Step 3: Implement dashboard shell cleanup**

In `Dashboard.tsx`:
- Simplify top header copy and status chips to enterprise tone
- Replace full-screen "syncing" overlay with in-context loading states
- Add explicit `data-testid` hooks for polished sections:
  - `dashboard-topbar`
  - `dashboard-status-chip`
  - `dashboard-module-links`

**Step 4: Implement widget surface cleanup**

In `DashboardWidgets.tsx`:
- Replace heavy glass/terminal aesthetics with restrained card treatment for this pass
- Normalize typographic scale (`text-xs`/`text-sm` consistency) and row density
- Preserve all data bindings and click actions

**Step 5: Re-run dashboard tests**

Run: `cd rankray-hq-frontend && npm run test:e2e -- e2e/dashboard.spec.ts e2e/shell-dashboard-polish.spec.ts`
Expected: PASS.

**Step 6: Commit**

```bash
git add rankray-hq-frontend/src/modules/dashboard/Dashboard.tsx rankray-hq-frontend/src/modules/dashboard/components/DashboardWidgets.tsx rankray-hq-frontend/e2e/dashboard.spec.ts rankray-hq-frontend/e2e/shell-dashboard-polish.spec.ts
git commit -m "feat: streamline dashboard hierarchy and trust states"
```

### Task 5: Final Verification + P1 Optional Copy Polish

**Files:**
- Optional Modify: `rankray-hq-frontend/src/components/layout/Header.tsx`
- Optional Modify: `rankray-hq-frontend/src/modules/dashboard/Dashboard.tsx`
- Test: `rankray-hq-frontend/e2e/ui-click-audit.spec.ts`
- Test: `rankray-hq-frontend/e2e/theme-system.spec.ts`

**Step 1: Run quality gates**

Run:
- `cd rankray-hq-frontend && npm run lint`
- `cd rankray-hq-frontend && npm run build`
- `cd rankray-hq-frontend && npm run test:e2e -- e2e/dashboard.spec.ts e2e/sidebar-navigation.spec.ts e2e/theme-system.spec.ts e2e/shell-dashboard-polish.spec.ts`

Expected: all pass.

**Step 2: Optional P1 copy tightening (only if all P0 complete)**

Tighten noisy copy to concise enterprise tone in shell/dashboard only. Do not add new features, banners, or additional states.

**Step 3: Run optional audit**

Run: `cd rankray-hq-frontend && npm run test:e2e -- e2e/ui-click-audit.spec.ts`
Expected: no new BROKEN regressions for dashboard/shell paths.

**Step 4: Commit**

```bash
git add rankray-hq-frontend/src/components/layout/Header.tsx rankray-hq-frontend/src/modules/dashboard/Dashboard.tsx
git commit -m "chore: tighten shell and dashboard copy for demo readiness"
```

---

## Definition of Done

- Shell + dashboard follow a calm enterprise visual style with reduced noise.
- Header/sidebar/dashboard feel consistent in spacing, control sizes, and motion timing.
- Dashboard no longer uses a blocking full-screen post-load overlay for normal refresh.
- New polish regression spec exists and passes.
- Lint, build, and targeted Playwright suites pass.

## Out of Scope (This Plan)

- Full module-wide redesign beyond shell + dashboard
- Routing architecture migration
- Backend/data model changes
- Large IA changes

