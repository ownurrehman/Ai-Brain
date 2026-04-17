# CRM API documentation

Backend: `rankray-hq-backend/src/crm/*`.

## Overview

CRM manages Companies, Contacts, Deals, and Activities via specialized services.

## Services

### CompanyService

CRUD for `Company`. `CrmController` delegates here.

### ContactService

CRUD for `Contact`; validates `companyId` in workspace.

### DealService

Deal lifecycle; stage transitions (`lead` → … → `won` / `lost`); audit `Activity` on change; side-effects on `won`.

### ActivityService

`findAll`, `create`; linked to Deal, Company, or Contact.

## Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/crm/companies` | List companies |
| GET | `/crm/companies/:id` | Get company |
| POST | `/crm/companies` | Create |
| PATCH | `/crm/companies/:id` | Update |
| DELETE | `/crm/companies/:id` | Delete |
| GET | `/crm/contacts` | List contacts |
| GET | `/crm/contacts/:id` | Get contact |
| POST | `/crm/contacts` | Create |
| PATCH | `/crm/contacts/:id` | Update |
| DELETE | `/crm/contacts/:id` | Delete |
| GET | `/crm/deals` | List deals |
| GET | `/crm/deals/:id` | Get deal |
| POST | `/crm/deals` | Create |
| PATCH | `/crm/deals/:id` | Update (lifecycle) |
| DELETE | `/crm/deals/:id` | Delete |
| GET | `/crm/activities` | List activities |
| POST | `/crm/activities` | Create activity |

## Security

`JwtAuthGuard`; all access scoped by `workspaceId`.
