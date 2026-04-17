# 🏛️ PLATFORM OVERHAUL BLUEPRINT

**Mission**: Standardize the "RankRay Signature Layout" across all core modules and resolve critical UI/Logic blockers.

---

## 🏗️ MILESTONE 1: SIDEBAR UNIFICATION
*Standardizing the "Double Sidebar" pattern (Finance-Style) across the platform.*

### [1.1] ASSET CENTER REBIRTH
- **Pattern**: Icon-Sidebar (Global) + Text-Sidebar (Module).
- **Categories**: 
  - 🌐 Domains
  - 🖥️ Hostings
  - 📄 Websites
  - 🏢 Real Estate
  - 🛋️ Office Accessories
  - 💻 Computers
  - ➕ Other / Custom
- **Feature**: "Manage Categories" button in sidebar.

### [1.2] TEAM & CLIENT V2
- **Action**: Add secondary sidebar to `src/modules/team/` and `src/modules/clients/`.
- **Style**: Inherit CSS tokens from `finance-sidebar.css`.

---

## 📊 MILESTONE 2: PROJECT DENSITY REFACTOR
*Removing "Big Box" placeholders for high-performance row lists.*

### [2.1] PROJECT ROWS
- **Change**: Replace large cards with compact, data-rich rows.
- **KPIs**: Status Badge, Deadline, Assignee, Progress Bar.
- **Action**: Add "Create Project" FAB/Button in top right.

---

## 🩺 MILESTONE 3: BUG RADIOLOGY
*Fixing logic failures and data-fetching issues.*

### [3.1] LEADS VALIDATION
- **Issue**: "Deal name is required" blocker.
- **Fix**: Align UI form submission with Zod/Backend schema.

### [3.2] EMPLOYEE ENGINE
- **Issue**: 404/Empty load on Employees module.
- **Fix**: Trace fetching hook and provider logic.

---

## 🚦 EXECUTION GATE
1. **Discuss Architecture** (Do we want a common `SidebarV2` component?)
2. **Execute 1.1** (Assets) -> **Review & Commit**
3. **Execute 1.2** (Team/Client) -> **Review & Commit**
...and so on.
