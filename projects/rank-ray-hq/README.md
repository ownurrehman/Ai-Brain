# RankRay HQ

RankRay HQ is the all-in-one operations dashboard for RankRay — an SEO agency management platform that handles CRM, HRM, Finance, Projects, Publishing, SEO tools, and more under one roof.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Frontend | React 18 + TypeScript + Vite |
| Styling | Tailwind CSS + shadcn/ui |
| State | Zustand |
| Backend | Node.js / Express (see `rankray-hq-backend/`) |
| Icons | Lucide React |
| Charts | Recharts |

---

## Quick Start

```bash
# 1. Install dependencies
npm install

# 2. Start the dev server
npm run dev

# 3. Open http://localhost:5173
```

**Requirements:** Node.js 18+, npm 9+

---

## Project Structure

```
rankray-hq-frontend/
├── src/
│   ├── components/ui/      # Reusable UI primitives (shadcn/ui + custom)
│   │   ├── button.tsx
│   │   ├── card.tsx
│   │   ├── dialog.tsx
│   │   ├── empty.tsx         # Standardized empty state component
│   │   └── ...
│   ├── modules/            # Feature modules (one folder per domain)
│   │   ├── admin/          # Admin panel (users, plans, workspaces)
│   │   ├── analytics/        # Analytics & reporting
│   │   ├── assets/         # Asset manager (images, videos, docs)
│   │   ├── auth/           # Login, register, forgot password
│   │   ├── automation/     # Workflow automation builder
│   │   ├── billing/        # Billing & invoicing
│   │   ├── crm/            # CRM (contacts, companies, deals, pipeline)
│   │   ├── dashboard/      # Main dashboard / home
│   │   ├── finance/        # Full finance suite (invoices, payments, quotes)
│   │   ├── hrm/            # HR module (employees, leave, attendance)
│   │   ├── inbox/          # Unified inbox (emails, messages)
│   │   ├── marketing/      # Marketing campaigns
│   │   ├── outreach/       # Email outreach & sequences
│   │   ├── projects/       # Project management
│   │   ├── publishing/     # Content publishing (blogs, images, videos)
│   │   ├── seo/            # SEO tools & rank tracking
│   │   ├── settings/       # App settings, team, integrations
│   │   └── tasks/          # Task management
│   ├── lib/                # Utilities, helpers, API clients
│   ├── hooks/              # Shared React hooks
│   ├── stores/             # Zustand state stores
│   ├── types/              # Shared TypeScript types
│   ├── App.tsx             # Root app component
│   ├── index.css           # GLOBAL DESIGN TOKENS — read this!
│   └── main.tsx            # Entry point
├── docs/                   # Project documentation
│   └── design-tokens.md    # How to change the entire app's look
├── tailwind.config.js      # Tailwind theme (maps CSS variables)
├── vite.config.ts
└── package.json
```

---

## Design System (One-File Control)

The entire app's visual identity is controlled from **`src/index.css`**. Change one variable → the whole app updates.

### Key Tokens

```css
:root {
  /* --- Radii --- */
  --radius: 0.75rem;        /* Base — controls all corners */

  /* --- Shadows --- */
  --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);

  /* --- Colors (HSL) --- */
  --primary: 239 84% 67%;
  --background: 0 0% 100%;
  --card: 0 0% 100%;
  --border: 240 6% 90%;

  /* Semantic */
  --error: 0 84% 60%;
  --success: 155 72% 36%;
  --warning: 36 92% 44%;
}
```

**See `docs/design-tokens.md` for the full reference.**

### Component Standards

| Element | Radius | Height | Background | Border |
|---|---|---|---|---|
| Small (badges, avatars) | `rounded-lg` | — | — | — |
| Buttons | `rounded-xl` | `h-9` (std) / `h-10` (cta) | `bg-primary` | — |
| Inputs / Selects | `rounded-xl` | `h-10` | `bg-background` | `border-border/50` |
| Cards / Containers | `rounded-2xl` | — | `bg-card` | `border-border/50` |
| Dialogs | `rounded-3xl` | — | `bg-card` | `border-border/50` |
| Sections | `rounded-2xl` | — | `bg-card` or `bg-muted/20` | `border-border/50` |
| Tables | — | — | — | `border-border` (row dividers) |

**Shadow rule:** Use `shadow-sm` on cards only. Remove for nested/internal cards.

---

## Module Development Guide

### Adding a New Module

1. **Create folder:** `src/modules/your-module/`
2. **Entry component:** `src/modules/your-module/YourModule.tsx`
3. **Register in App.tsx:** Add to the module switcher / routing
4. **Follow conventions:**
   - Use `Empty` component for empty states (not raw `<p>`)
   - Use `rounded-2xl` for card containers
   - Use `border-border/50` on all bordered cards
   - Dialog widths: forms → `500px`, confirmations → `400px`

### Standard Patterns

```tsx
// Card container
<Card className="rounded-2xl border border-border/50 bg-card p-4 shadow-sm">
  ...
</Card>

// Empty state
<Empty
  icon={IconName}
  title="No Items Yet"
  description="Create your first item to get started."
  action={<Button>Add Item</Button>}
/>

// Form dialog
<DialogContent className="sm:max-w-[500px] rounded-3xl">
  ...
</DialogContent>

// Confirmation dialog
<DialogContent className="sm:max-w-[400px] rounded-3xl">
  ...
</DialogContent>
```

---

## State Management

- **Global state:** Zustand stores in `src/stores/`
- **Module state:** Co-locate in module folder if module-specific
- **Server state:** Use custom hooks in `src/hooks/`

---

## API Integration

API clients live in `src/lib/api/`. Each module has a service file (e.g., `src/modules/crm/services/crmApi.ts`).

Base URL is configured via environment variable:
```
VITE_API_URL=http://localhost:3001
```

---

## Common Commands

```bash
npm run dev          # Start dev server
npm run build        # Production build
npm run preview      # Preview production build
npm run lint         # ESLint check
npx tsc --noEmit     # TypeScript check (no output files)
```

---

## Troubleshooting

| Issue | Fix |
|---|---|
| `className` conflicts | Use `cn()` from `src/lib/utils.ts` |
| Styles not updating | Check `index.css` tokens — most values derive from there |
| Dialog looks off | Verify `sm:max-w-[500px]` and `rounded-3xl` |
| Empty state looks different | Use `<Empty />` component from `components/ui/empty.tsx` |

---

## Contributing Rules

1. **Surgical changes** — touch only what you need
2. **Match existing style** — don't introduce new radius/shadow patterns
3. **Verify with TypeScript** — run `npx tsc --noEmit` before committing
4. **Use tokens** — no hardcoded colors, no `bg-white`, no `text-gray-*`
5. **Log to memory** — after completing work, write to `memory/YYYY-MM-DD.md`

---

## Resources

- [shadcn/ui docs](https://ui.shadcn.com)
- [Tailwind CSS docs](https://tailwindcss.com)
- [Lucide icons](https://lucide.dev)
- `docs/design-tokens.md` — changing the entire app from one file

---

Built by RankRay 🦞
