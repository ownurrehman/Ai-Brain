# Rank Ray Mission Control — Phase 3: Architecture Design

**Status:** IN PROGRESS 🔄  
**Date:** 2026-04-22  
**ETA:** 30-45 minutes

---

## 1. SYSTEM ARCHITECTURE

```
┌─────────────────────────────────────────────────────────────────┐
│                         USER LAYER                               │
├─────────────────────────────────────────────────────────────────┤
│  Admin Dashboard  │  Team Dashboard  │  Client Portal  │  API  │
└─────────┬─────────┴────────┬──────────┴────────┬────────┴───┬───┘
          │                  │                   │            │
          └──────────────────┼───────────────────┘            │
                             │                                │
                    ┌────────▼────────┐                       │
                    │   Next.js 14    │                       │
                    │   (App Router)  │                       │
                    │   + API Routes  │                       │
                    └────────┬────────┘                       │
                             │                                │
          ┌──────────────────┼──────────────────┐             │
          │                  │                  │             │
    ┌─────▼─────┐    ┌──────▼──────┐    ┌──────▼──────┐      │
    │   Auth    │    │   Module    │    │  Shared     │      │
    │  Layer    │    │   Router    │    │ Components  │      │
    └─────┬─────┘    └──────┬──────┘    └──────┬──────┘      │
          │                  │                  │             │
          └──────────────────┼──────────────────┘             │
                             │                                │
                    ┌────────▼────────┐                       │
                    │     Prisma      │                       │
                    │      ORM        │                       │
                    └────────┬────────┘                       │
                             │                                │
          ┌──────────────────┼──────────────────┐             │
          │                  │                  │             │
    ┌─────▼─────┐    ┌──────▼──────┐    ┌──────▼──────┐      │
    │PostgreSQL │    │    Redis    │    │   File      │      │
    │ Database  │    │   (Cache)   │    │  Storage    │      │
    └───────────┘    └─────────────┘    └─────────────┘      │
                                                              │
┌─────────────────────────────────────────────────────────────┐
│                    EXTERNAL INTEGRATIONS                     │
├──────────────┬──────────────┬──────────────┬────────────────┤
│ Google SC    │ Google Analytics │ WordPress │ Shopify        │
│ OAuth + API  │ OAuth + API    │ REST API    │ REST API       │
└──────────────┴──────────────┴──────────────┴────────────────┘
```

---

## 2. MODULE PLUGIN ARCHITECTURE

```
/apps/
  /web/                    # Next.js frontend
    /app/
      /(auth)/             # Login, register, invite
      /(dashboard)/        # Main dashboard layout
        /admin/            # Super admin panel
        /team/             # Team workspace
        /client/           # Client portal
      /api/
        /v1/
          /auth/           # Auth endpoints
          /crm/            # CRM module
          /finance/        # Finance module
          /seo/            # SEO module
          /automation/     # Automation module
          /social/         # Social media module
          /hrm/            # HRM module
          /projects/       # Projects module
          /websites/       # Websites module
          /agents/         # Agents module
    /components/
      /ui/                 # shadcn/ui components (shared)
      /modules/            # Module-specific components
        /crm/
        /finance/
        /seo/
        ...
    /lib/
      /auth.ts             # NextAuth config
      /rbac.ts             # Role-based access control
      /modules.ts          # Module registry
    /styles/
      /globals.css         # MASTER CSS (Tailwind)
      /components.css      # Shared component styles
      # NO module-level CSS

/packages/
  /modules/
    /crm/                  # CRM module (self-contained)
      index.ts             # Module entry point
      components.tsx       # Module components
      api.ts               # Module API routes
      schema.prisma        # Module DB schema
      permissions.ts       # Module permissions
    /finance/
    /seo/
    /automation/
    ...

/docker/
  Dockerfile
  docker-compose.yml
  nginx.conf
  pm2.config.js
```

---

## 3. DATABASE SCHEMA

### Core Tables (Shared)

```prisma
// Users & Auth
model User {
  id            String    @id @default(uuid())
  email         String    @unique
  password      String?   // Hashed, null for invite-only
  name          String
  avatar        String?
  role          Role      @default(USER) // SUPER_ADMIN, ADMIN, TEAM, CLIENT, GUEST
  status        Status    @default(PENDING) // PENDING, ACTIVE, SUSPENDED
  companyId     String?
  company       Company?  @relation(fields: [companyId], references: [id])
  modules       ModuleAccess[]
  tasks         Task[]    @relation("AssignedTasks")
  createdAt     DateTime  @default(now())
  updatedAt     DateTime  @updatedAt
}

model Company {
  id            String    @id @default(uuid())
  name          String
  slug          String    @unique
  logo          String?
  address       String?
  phone         String?
  email         String?
  website       String?
  users         User[]
  websites      Website[]
  projects      Project[]
  invoices      Invoice[]
  createdAt     DateTime  @default(now())
  updatedAt     DateTime  @updatedAt
}

// Module Access Control
model ModuleAccess {
  id            String    @id @default(uuid())
  userId        String
  user          User      @relation(fields: [userId], references: [id])
  module        String    // crm, finance, seo, automation, etc.
  canView       Boolean   @default(false)
  canCreate     Boolean   @default(false)
  canEdit       Boolean   @default(false)
  canDelete     Boolean   @default(false)
  createdAt     DateTime  @default(now())
  
  @@unique([userId, module])
}

enum Role {
  SUPER_ADMIN
  ADMIN
  TEAM
  CLIENT
  GUEST
}

enum Status {
  PENDING
  ACTIVE
  SUSPENDED
}
```

### CRM Module

```prisma
model Contact {
  id            String    @id @default(uuid())
  companyId     String
  company       Company   @relation(fields: [companyId], references: [id])
  firstName     String
  lastName      String
  email         String
  phone         String?
  position      String?
  type          ContactType // CLIENT, PROSPECT, VENDOR, PARTNER
  tags          String[]
  customFields  Json?     // Module-specific fields
  createdAt     DateTime  @default(now())
  updatedAt     DateTime  @updatedAt
}

enum ContactType {
  CLIENT
  PROSPECT
  VENDOR
  PARTNER
}
```

### Websites Module

```prisma
model Website {
  id            String    @id @default(uuid())
  companyId     String
  company       Company   @relation(fields: [companyId], references: [id])
  name          String
  url           String    @unique
  platform      Platform  @default(WORDPRESS) // WORDPRESS, SHOPIFY, CUSTOM
  hosting       String?
  domain        String?
  sslStatus     SslStatus @default(PENDING)
  gscConnected  Boolean   @default(false)
  gaConnected   Boolean   @default(false)
  wpConnected   Boolean   @default(false)
  shopifyConnected Boolean @default(false)
  credentials   Json?     // Encrypted API keys, tokens
  seoData       SeoData?
  automations   Automation[]
  createdAt     DateTime  @default(now())
  updatedAt     DateTime  @updatedAt
}

enum Platform {
  WORDPRESS
  SHOPIFY
  CUSTOM
}

enum SslStatus {
  PENDING
  ACTIVE
  EXPIRED
}

model SeoData {
  id            String    @id @default(uuid())
  websiteId     String    @unique
  website       Website   @relation(fields: [websiteId], references: [id])
  keywords      Json?     // Tracked keywords + positions
  lastAudit     Json?     // Last SEO audit results
  lastSync      DateTime?
  createdAt     DateTime  @default(now())
  updatedAt     DateTime  @updatedAt
}
```

### Projects & Tasks Module

```prisma
model Project {
  id            String    @id @default(uuid())
  companyId     String
  company       Company   @relation(fields: [companyId], references: [id])
  name          String
  description   String?
  status        ProjectStatus @default(ACTIVE)
  startDate     DateTime?
  endDate       DateTime?
  budget        Decimal?
  tasks         Task[]
  members       User[]    @relation("ProjectMembers")
  createdAt     DateTime  @default(now())
  updatedAt     DateTime  @updatedAt
}

enum ProjectStatus {
  PLANNING
  ACTIVE
  ON_HOLD
  COMPLETED
  CANCELLED
}

model Task {
  id            String    @id @default(uuid())
  projectId     String
  project       Project   @relation(fields: [projectId], references: [id])
  title         String
  description   String?
  status        TaskStatus @default(TODO)
  priority      Priority   @default(MEDIUM)
  assigneeId    String?
  assignee      User?     @relation("AssignedTasks", fields: [assigneeId], references: [id])
  dueDate       DateTime?
  estimatedHours Decimal?
  actualHours   Decimal?
  tags          String[]
  customFields  Json?
  createdAt     DateTime  @default(now())
  updatedAt     DateTime  @updatedAt
}

enum TaskStatus {
  TODO
  IN_PROGRESS
  REVIEW
  DONE
}

enum Priority {
  LOW
  MEDIUM
  HIGH
  URGENT
}
```

### Finance Module

```prisma
model Invoice {
  id            String    @id @default(uuid())
  companyId     String
  company       Company   @relation(fields: [companyId], references: [id])
  number        String    @unique // INV-2026-0001
  type          InvoiceType @default(INVOICE)
  status        InvoiceStatus @default(DRAFT)
  issueDate     DateTime  @default(now())
  dueDate       DateTime
  items         Json      // [{description, quantity, rate, amount}]
  subtotal      Decimal
  tax           Decimal
  total         Decimal
  paidAmount    Decimal   @default(0)
  notes         String?
  attachments   String[]  // File URLs
  stripePaymentId String?
  createdAt     DateTime  @default(now())
  updatedAt     DateTime  @updatedAt
}

enum InvoiceType {
  QUOTE
  INVOICE
  RECURRING
  CREDIT_NOTE
}

enum InvoiceStatus {
  DRAFT
  SENT
  VIEWED
  PAID
  OVERDUE
  CANCELLED
}

model Expense {
  id            String    @id @default(uuid())
  companyId     String?
  company       Company?  @relation(fields: [companyId], references: [id])
  category      String
  description   String
  amount        Decimal
  date          DateTime
  receipt       String?   // File URL
  paymentMethod String
  createdAt     DateTime  @default(now())
  updatedAt     DateTime  @updatedAt
}
```

### Automation Module

```prisma
model Automation {
  id            String    @id @default(uuid())
  websiteId     String
  website       Website   @relation(fields: [websiteId], references: [id])
  name          String
  type          AutomationType
  status        AutomationStatus @default(INACTIVE)
  trigger       Json      // {type: "schedule", cron: "0 9 * * *"}
  actions       Json      // [{type: "publish_blog", template: "..."}]
  lastRun       DateTime?
  nextRun       DateTime?
  runCount      Int       @default(0)
  errorCount    Int       @default(0)
  lastError     String?
  createdAt     DateTime  @default(now())
  updatedAt     DateTime  @updatedAt
}

enum AutomationType {
  BLOG_GENERATION
  LANDING_PAGE_BULK
  SEO_AUDIT_SCHEDULE
  REPORT_GENERATION
  SOCIAL_POSTING
}

enum AutomationStatus {
  ACTIVE
  INACTIVE
  ERROR
}
```

### Agents Module

```prisma
model Agent {
  id            String    @id @default(uuid())
  name          String
  type          AgentType // OPENCLAW, HERMIS, CUSTOM
  status        AgentStatus @default(IDLE)
  config        Json      // Agent-specific configuration
  tasks         AgentTask[]
  lastActive    DateTime?
  createdAt     DateTime  @default(now())
  updatedAt     DateTime  @updatedAt
}

enum AgentType {
  OPENCLAW
  HERMIS
  CUSTOM
}

enum AgentStatus {
  IDLE
  BUSY
  ERROR
  OFFLINE
}

model AgentTask {
  id            String    @id @default(uuid())
  agentId       String
  agent         Agent     @relation(fields: [agentId], references: [id])
  type          String
  input         Json
  output        Json?
  status        TaskStatus @default(PENDING)
  error         String?
  startedAt     DateTime?
  completedAt   DateTime?
  createdAt     DateTime  @default(now())
  updatedAt     DateTime  @updatedAt
}
```

---

## 4. API ENDPOINTS (Per Module)

### Auth Module
```
POST   /api/v1/auth/invite          # Super admin invites user
POST   /api/v1/auth/register        # User sets password from invite
POST   /api/v1/auth/login           # Login
POST   /api/v1/auth/logout          # Logout
GET    /api/v1/auth/me              # Get current user
PUT    /api/v1/auth/me              # Update profile
```

### CRM Module
```
GET    /api/v1/crm/companies        # List companies
POST   /api/v1/crm/companies        # Create company
GET    /api/v1/crm/companies/:id    # Get company details
PUT    /api/v1/crm/companies/:id    # Update company
DELETE /api/v1/crm/companies/:id    # Delete company

GET    /api/v1/crm/contacts         # List contacts
POST   /api/v1/crm/contacts         # Create contact
GET    /api/v1/crm/contacts/:id     # Get contact
PUT    /api/v1/crm/contacts/:id     # Update contact
DELETE /api/v1/crm/contacts/:id     # Delete contact
```

### Websites Module
```
GET    /api/v1/websites             # List websites
POST   /api/v1/websites             # Add website
GET    /api/v1/websites/:id         # Get website
PUT    /api/v1/websites/:id         # Update website
DELETE /api/v1/websites/:id         # Delete website
POST   /api/v1/websites/:id/connect/gsc    # Connect GSC
POST   /api/v1/websites/:id/connect/ga     # Connect GA
POST   /api/v1/websites/:id/connect/wp     # Connect WordPress
POST   /api/v1/websites/:id/connect/shopify # Connect Shopify
GET    /api/v1/websites/:id/seo     # Get SEO data
POST   /api/v1/websites/:id/seo/sync # Sync SEO data
```

### Finance Module
```
GET    /api/v1/finance/invoices     # List invoices
POST   /api/v1/finance/invoices     # Create invoice
GET    /api/v1/finance/invoices/:id # Get invoice
PUT    /api/v1/finance/invoices/:id # Update invoice
DELETE /api/v1/finance/invoices/:id # Delete invoice
POST   /api/v1/finance/invoices/:id/send    # Send via email
POST   /api/v1/finance/invoices/:id/payment # Create Stripe payment

GET    /api/v1/finance/expenses     # List expenses
POST   /api/v1/finance/expenses     # Create expense
GET    /api/v1/finance/expenses/:id # Get expense
PUT    /api/v1/finance/expenses/:id # Update expense
DELETE /api/v1/finance/expenses/:id # Delete expense

GET    /api/v1/finance/reports      # Financial reports
```

### SEO Module
```
GET    /api/v1/seo/keywords         # List tracked keywords
POST   /api/v1/seo/keywords         # Add keywords
DELETE /api/v1/seo/keywords/:id     # Remove keyword
GET    /api/v1/seo/ranks            # Get current rankings
POST   /api/v1/seo/ranks/sync       # Sync rankings from GSC

GET    /api/v1/seo/audits           # List SEO audits
POST   /api/v1/seo/audits           # Run new audit
GET    /api/v1/seo/audits/:id       # Get audit results

GET    /api/v1/seo/backlinks        # Get backlink data
POST   /api/v1/seo/backlinks/sync   # Sync backlinks
```

### Automation Module
```
GET    /api/v1/automation           # List automations
POST   /api/v1/automation           # Create automation
GET    /api/v1/automation/:id       # Get automation
PUT    /api/v1/automation/:id       # Update automation
DELETE /api/v1/automation/:id       # Delete automation
POST   /api/v1/automation/:id/run   # Run manually
GET    /api/v1/automation/:id/logs  # Get run logs
```

### Projects Module
```
GET    /api/v1/projects             # List projects
POST   /api/v1/projects             # Create project
GET    /api/v1/projects/:id         # Get project
PUT    /api/v1/projects/:id         # Update project
DELETE /api/v1/projects/:id         # Delete project

GET    /api/v1/projects/:id/tasks   # List tasks
POST   /api/v1/projects/:id/tasks   # Create task
GET    /api/v1/tasks/:id            # Get task
PUT    /api/v1/tasks/:id            # Update task
DELETE /api/v1/tasks/:id            # Delete task
POST   /api/v1/tasks/:id/assign     # Assign to user
```

### Agents Module
```
GET    /api/v1/agents               # List agents
POST   /api/v1/agents               # Register agent
GET    /api/v1/agents/:id           # Get agent
PUT    /api/v1/agents/:id           # Update agent
DELETE /api/v1/agents/:id           # Delete agent

GET    /api/v1/agents/:id/tasks     # List agent tasks
POST   /api/v1/agents/:id/tasks     # Assign task to agent
GET    /api/v1/agents/:id/tasks/:taskId # Get task status
POST   /api/v1/agents/:id/tasks/:taskId/cancel # Cancel task
```

---

## 5. RBAC PERMISSION MATRIX

| Module | SUPER_ADMIN | ADMIN | TEAM | CLIENT | GUEST |
|--------|-------------|-------|------|--------|-------|
| **CRM** | Full | Full | View/Edit assigned | View own | None |
| **Finance** | Full | Full | View assigned | View own invoices | None |
| **Websites** | Full | Full | View/Edit assigned | View own | View only |
| **SEO** | Full | Full | View/Edit assigned | View own | None |
| **Automation** | Full | Full | View/Edit assigned | None | None |
| **Projects** | Full | Full | View/Edit assigned | View own | View only |
| **Tasks** | Full | Full | View/Edit assigned | View own | None |
| **Agents** | Full | Full | View assigned | None | None |
| **Settings** | Full | Full | None | None | None |
| **Users** | Full | View/Edit | None | None | None |

---

**Next: Phase 4 — Confirmation Checklist**
