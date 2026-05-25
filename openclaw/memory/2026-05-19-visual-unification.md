# Visual Unification Round 2 — 2026-05-19

## Summary
Continued micro-level visual unification across all RankRay HQ frontend modules.

## Dialog Widths Standardized
Standardized all `DialogContent` widths per the agreed rules:

| Module | File | Old | New |
|--------|------|-----|-----|
| CRM | AddActivityModal.tsx | 450px | 500px |
| Finance | Banks.tsx (×2) | 450px | 500px |
| Finance | CreditNotes.tsx (×2) | 520px | 500px |
| Finance | Items.tsx (×2) | 425px | 500px |
| Finance | PaymentsReceived.tsx (×2) | 520px | 500px |
| Finance | Quotes.tsx (×2) | 520px | 500px |
| Finance | RecurringInvoices.tsx | 520px | 500px |
| Finance | RetainerInvoices.tsx | 425px | 500px |
| Finance | SalesReceipts.tsx (create) | 700px | 600px |
| Finance | SalesReceipts.tsx (edit) | 520px | 500px |
| Outreach | Outreach.tsx (×3) | 450px | 500px |
| Settings | InviteUserDialog.tsx | 425px | 500px |
| Settings | Settings.tsx | 480px | 500px |
| Tasks | Tasks.tsx | 640px | 500px |
| SEO | AddKeywordDialog.tsx | max-w-md | 500px |
| Projects | Projects.tsx (×2) | 450px | 500px |

## Empty States Standardized
Replaced manual/custom empty states with the `Empty` component from `@/components/ui/empty`:

| Module | File | What was changed |
|--------|------|-----------------|
| Publishing | ImagesSubModule.tsx | Custom div → Empty component |
| Publishing | BlogsSubModule.tsx | Custom div → Empty component |
| Publishing | VideosSubModule.tsx | Custom div → Empty component |
| Settings | Settings.tsx | No teammates custom div → Empty component |
| Finance | Banks.tsx | No bank accounts card → Empty component |
| Finance | Items.tsx | `<p>No services found</p>` → `<EmptyTitle>` |
| Finance | SalesReceipts.tsx | `<p>No receipts found</p>` → `<EmptyTitle>` |
| Finance | CreditNotes.tsx | `<p>No credit notes found</p>` → `<EmptyTitle>` |
| Finance | PaymentsReceived.tsx | `<p>No payments found</p>` → `<EmptyTitle>` |
| Tasks | Tasks.tsx | "No tasks found" card → Empty component, table empty states → Empty components |
| Assets | AssetList.tsx | Custom div → Empty component |
| Outreach | Outreach.tsx | Campaigns/Template/Prospects tab raw text → Empty component |

## Files Modified (21 total)
1. `src/modules/crm/components/AddActivityModal.tsx`
2. `src/modules/finance/sections/Banks.tsx`
3. `src/modules/finance/sections/CreditNotes.tsx`
4. `src/modules/finance/sections/Items.tsx`
5. `src/modules/finance/sections/PaymentsReceived.tsx`
6. `src/modules/finance/sections/Quotes.tsx`
7. `src/modules/finance/sections/RecurringInvoices.tsx`
8. `src/modules/finance/sections/RetainerInvoices.tsx`
9. `src/modules/finance/sections/SalesReceipts.tsx`
10. `src/modules/outreach/Outreach.tsx`
11. `src/modules/projects/Projects.tsx`
12. `src/modules/publishing/sections/BlogsSubModule.tsx`
13. `src/modules/publishing/sections/ImagesSubModule.tsx`
14. `src/modules/publishing/sections/VideosSubModule.tsx`
15. `src/modules/seo/site/ranks/components/AddKeywordDialog.tsx`
16. `src/modules/settings/InviteUserDialog.tsx`
17. `src/modules/settings/Settings.tsx`
18. `src/modules/tasks/Tasks.tsx`
19. `src/modules/assets/components/AssetList.tsx`
20. `src/modules/automation/pages/AutomationDashboard.tsx`
21. `src/modules/hrm/HRM.tsx`

## Verification
- `npx tsc --noEmit` passes with 0 errors.
- All dialog widths now follow: form dialogs → 500px, confirmation → 400px, complex → 600px.
- Empty states use the standardized Empty component.

## Remaining Work (intentionally skipped for surgical approach)
- SEO module pages already use Empty component (title="No SEO websites yet", etc.)
- CRM CompanyProfile has 20+ inline empty texts that are contextual; left as-is since they\'re inline helper text, not standalone empty states.
- Dashboard, Analytics, Inbox, Automation inner pages have inline text within complex layouts — changing them would require broader refactoring.
- Diagnostics, AuditLogs are low-priority admin tools.
