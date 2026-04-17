# Finance Module (Source of Truth)

## Scope Model

- Finance owns billing and payment history.
- Finance is client-linked and company-first.
- Finance must be accounting-safe (no hidden state jumps).

## Must Be In The App

- Invoice lifecycle.
- Payment recording and reconciliation.
- Sales receipts.
- Expenses.
- Reporting screens backed by real data.
- Finance settings required for numbering and defaults.

## Must Always Work

- Invoice states must be valid and reversible where allowed.
- Payment posting must update balances deterministically.
- Receipt and expense records must remain auditable.
- Archived or inactive companies must retain full finance history.
- Route stability for core finance entry (`/finance/dashboard`).

## Linking Contracts (Non-Negotiable)

- Every finance record must belong to a `Company`.
- Finance may optionally link to a project when that project model is used.
- Finance entries must remain discoverable from company context.
- Task follow-ups from finance events must preserve `entityType` + `entityId`.

## Guardrails

- No destructive history deletes for posted finance records.
- No fake paid/sent states.
- No detached finance records without owning company.
