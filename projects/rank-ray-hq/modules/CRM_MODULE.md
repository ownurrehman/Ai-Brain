# CRM Module (Source of Truth)

## Scope Model

- CRM owns relationship data and sales pipeline context.
- CRM must separate `Leads` from `Clients`.
- `Clients` are operational records represented by `Companies` and `Contacts`.
- No internal employee data belongs in CRM.

## Must Be In The App

- Leads board and lead stage progression.
- Clients entry in sidebar with submenu:
  - Companies
  - Contacts
- Company profile as control center for linked data.
- Contact management linked to company records.
- CRM activity/reporting for company/contact/deal events.

## Must Always Work

- Lead creation, stage changes, and assignment.
- Company and contact create/read/update/delete flows.
- Company -> profile navigation and profile loading.
- Contact -> company relationship integrity.
- CRM route stability:
  - `/crm/pipeline`
  - `/crm/companies`
  - `/crm/contacts`
  - `/crm/companies/:id`

## Linking Contracts (Non-Negotiable)

- `Company` is the owner business entity for client-side operations.
- `Contact` must belong to one `Company`.
- `Website` must link to `Company` (for SEO ownership).
- Finance records must link to `Company` (optionally to project where used).
- Tasks may link to CRM entities (`company`, `contact`, `deal`) through explicit entity references.
- Assets are company-owned and optionally website-linked.

## Guardrails

- Do not merge internal team records into CRM contacts.
- Do not treat `Lead` and `Client` as the same lifecycle state.
- Do not allow fake or orphan links between company/contact/website/task/finance records.
