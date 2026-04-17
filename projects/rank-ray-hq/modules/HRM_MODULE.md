# HRM Module (Source of Truth)

## Scope Model

- HRM owns internal workforce operations only.
- HRM is represented in UI as `Team`.
- HRM must not contain client contacts.

## Must Be In The App

- Employee directory.
- Attendance and leave workflows.
- Payroll and role-aware internal operations.
- Team-focused management views under HRM routes.

## Must Always Work

- Employee records create/read/update flows.
- Leave request lifecycle and approvals.
- Attendance visibility for internal employees.
- Separation from CRM contacts at data and UI levels.
- Route stability for team entry (`/hrm/employees`).

## Linking Contracts (Non-Negotiable)

- HRM users are internal and workspace-scoped.
- HRM users can be assignees for tasks across modules.
- HRM must never own CRM contact records.
- Permissions and RBAC must gate HRM admin actions.

## Guardrails

- Do not mix internal employee entities with client contacts.
- Do not bypass workspace isolation for HRM records.
- No fake headcount or payroll status values.
