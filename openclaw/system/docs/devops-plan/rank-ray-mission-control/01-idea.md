# Rank Ray Mission Control — Phase 1: Idea Lock

**Status:** LOCKED ✅  
**Date:** 2026-04-22  
**Owner:** Sheikh Own (Rank Ray)  
**Agent:** Enigma

---

## 🎯 Problem Statement

Rank Ray needs a unified SaaS platform to manage all agency operations — clients, websites, projects, team, finances, SEO, automations — in one place instead of scattered tools.

---

## 👥 Users

| User Type | Role | Needs |
|-----------|------|-------|
| **Admin (You)** | Agency owner | Full control, analytics, billing |
| **Internal Team** | SEO, dev, content, finance | Task management, client work, time tracking |
| **External Clients** | Business owners | View reports, invoices, project status |
| **Guest/Dry Users** | Contractors, one-off | Limited access, no login required |

---

## 📦 Modules

### Core Modules (Required)
| Module | Purpose | Key Features |
|--------|---------|-------------|
| **HRM/CRM** | People & relationships | Clients, companies, team, contacts, leads |
| **Projects** | Work management | Tasks, milestones, time tracking, assignments |
| **Finance** | Money | Quotes, invoices, recurring billing, expenses |
| **Websites** | Asset management | Client websites, domains, hosting, SSL status |
| **SEO** | Search optimization | Rank tracking, audits, reports, keyword research |
| **Automation** | Workflows | Bulk landing pages, blog generation, publishing |
| **Social Media** | Marketing | Post scheduling, analytics, multi-platform |
| **Agents** | AI workers | OpenClaw-like task automation, Hermis integration |

### Sub-Modules (Per Module)
Each module has sub-modules for granular features:
- **HRM/CRM** → Clients, Companies, Team, Leads, Contacts, Pipeline
- **Projects** → Tasks, Milestones, Time Tracking, Kanban, Gantt
- **Finance** → Quotes, Invoices, Recurring, Expenses, Reports
- **Websites** → Domains, Hosting, SSL, Uptime, Backups
- **SEO** → Rank Tracking, Audits, Keywords, Backlinks, Reports
- **Automation** → Workflows, Templates, Bulk Actions, Scheduling
- **Social Media** → Calendar, Posts, Analytics, Accounts
- **Agents** → Tasks, Logs, Config, Results

---

## 🔗 Relationships

```
Company
  └── Clients
        └── Websites
              ├── SEO (rank tracking, audits)
              ├── Automation (landing pages, blogs)
              └── Social Media (posts, scheduling)
        └── Projects
              ├── Tasks (assigned to Team)
              ├── Time Tracking
              └── Finance (quotes, invoices)
        └── Finance (billing, recurring)

Team Member
  ├── Tasks
  ├── Time Tracking
  ├── Clients (assigned)
  └── Projects (assigned)

Guest/Dry User
  └── Limited access to specific project/client
```

---

## 🏗️ Architecture Principles

### Module System
- **Plugin architecture** — Each module is self-contained
- **No module-level CSS** — All styling from master CSS files
- **Shared components** — Tables, forms, modals, charts from core
- **API contracts** — Each module exposes REST API to others
- **Database isolation** — Module tables prefixed (e.g., `seo_keywords`, `finance_invoices`)
- **Lazy loading** — Modules load on demand

### Shared Core
| Component | Purpose |
|-----------|---------|
| **Master CSS** | All styling — variables, utilities, components |
| **Shared Components** | Tables, forms, modals, dropdowns, charts |
| **Auth System** | JWT/OAuth2, roles, permissions |
| **Database** | PostgreSQL with schema per module |
| **API Gateway** | Unified REST API, rate limiting |
| **Event Bus** | Module-to-module communication |
| **File Storage** | S3/MinIO for uploads, exports |
| **Notification** | Email, Slack, Discord, in-app |

---

## 🔐 User Access Levels

| Level | Login | Permissions |
|-------|-------|-------------|
| **Admin** | Required | Full access |
| **Team** | Required | Module-based access |
| **Client** | Required | Own data only |
| **Guest** | Optional | Project-specific |
| **Dry** | None | Magic link / token |

---

## ✅ LOCKED DECISIONS

- [x] Modular plugin architecture
- [x] 8 core modules defined
- [x] HRM/CRM blended with relational data
- [x] Finance linked to clients & projects
- [x] Websites feed into SEO + Automation
- [x] Multi-user with role-based access
- [x] Guest/dry access supported
- [x] Master CSS — no module-level styles
- [x] Self-contained modules with shared core

---

**Next: Phase 2 — Tech Stack Research**
