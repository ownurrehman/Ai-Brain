# 🦞 DEVOPS PLAN MODE — Enigma Architecture

**Purpose:** Multi-phase planning engine for big-scale development projects  
**Status:** Ready to activate  
**Trigger:** Say `devops plan mode: <your idea>`  
**Updates:** Every 15 minutes in #claw-dev (short listicle format)

---

## 🔄 HOW IT WORKS

### **PHASE 1 — IDEA LOCK (0-30 min)**
1. You describe your idea (features, goals, users)
2. Enigma asks clarifying questions (max 5)
3. You answer — idea becomes "LOCKED"
4. Output: `docs/devops-plan/<project>-idea.md`

### **PHASE 2 — TECH STACK RESEARCH (30-60 min)**
1. Enigma researches best technologies for:
   - Frontend (framework, UI library, state mgmt)
   - Backend (language, framework, API style)
   - Database (SQL vs NoSQL, specific engine)
   - Infrastructure (hosting, CI/CD, scaling)
   - Security (auth, encryption, compliance)
2. Compares 3+ options per category
3. You pick or Enigma recommends
4. Output: `docs/devops-plan/<project>-tech-stack.md`

### **PHASE 3 — ARCHITECTURE DESIGN (60-90 min)**
1. System architecture diagram (text-based)
2. Database schema design
3. API endpoint planning (REST/GraphQL)
4. Frontend component hierarchy
5. Deployment pipeline design
6. Output: `docs/devops-plan/<project>-architecture.md`

### **PHASE 4 — QUESTIONNAIRE (90-120 min)**
1. Enigma asks 5-10 confirmation questions
2. You answer yes/no or provide specifics
3. Any red flags = back to Phase 2 or 3
4. Output: `docs/devops-plan/<project>-confirmation.md`

### **PHASE 5 — DEVELOPMENT ORDER (120-150 min)**
1. Decide: Frontend-first or Backend-first?
   - API-driven = Backend first
   - UI-focused = Frontend first
   - Fullstack = Parallel with mock APIs
2. Create task breakdown (epics → stories → tasks)
3. Estimate time per task
4. Output: `docs/devops-plan/<project>-roadmap.md`

### **PHASE 6 — EXECUTION (150+ min)**
1. Start coding based on roadmap
2. Every 15 min: short progress update in #claw-dev
3. Format: `⏳ [Project] Phase X — [task] — [status]`
4. Blocked? Immediate alert with options

---

## 📋 QUESTION TEMPLATE (Phase 1)

When you trigger devops plan mode, Enigma will ask:

1. **What problem are you solving?** (1 sentence)
2. **Who is the user?** (demographics, technical level)
3. **What are must-have vs nice-to-have features?**
4. **What's the budget/timeframe?** (free tier? 1 week? 1 month?)
5. **Any existing tech constraints?** (must use WordPress? must be JS?)

---

## 🛠️ TECH STACK TEMPLATE (Phase 2)

For each category, Enigma will research and present:

```
## Frontend Framework
Option A: React + Next.js
  Pros: SEO-friendly, huge ecosystem, server components
  Cons: Learning curve, heavy bundle
  Best for: Content sites, SEO-focused apps

Option B: Vue + Nuxt
  Pros: Gentle learning curve, great DX
  Cons: Smaller ecosystem than React
  Best for: Rapid development, team comfort

Option C: SvelteKit
  Pros: Fastest runtime, smallest bundle
  Cons: Smaller community, fewer plugins
  Best for: Performance-critical apps

Enigma Recommendation: [Option X]
Reason: [why]
```

---

## 🏗️ ARCHITECTURE TEMPLATE (Phase 3)

```
## System Architecture

[User] → [CDN] → [Load Balancer] → [Frontend Server]
                                          ↓
                                    [API Gateway]
                                          ↓
                    ┌─────────────┬────────┴──────┬─────────────┐
                    ↓             ↓                ↓             ↓
              [Auth Service] [Core API]    [Database]    [File Storage]
                    ↓             ↓                ↓             ↓
              [JWT/OAuth2]  [Business Logic]  [PostgreSQL]   [S3/MinIO]

## Database Schema
Table: users
  id (PK), email, password_hash, role, created_at

Table: projects
  id (PK), user_id (FK), name, status, config_json

## API Endpoints
POST   /api/v1/auth/login
POST   /api/v1/auth/register
GET    /api/v1/projects
POST   /api/v1/projects
GET    /api/v1/projects/:id
PUT    /api/v1/projects/:id
DELETE /api/v1/projects/:id
```

---

## ✅ CONFIRMATION CHECKLIST (Phase 4)

```
## Before We Code — Confirm These:

- [ ] Tech stack locked (no changes after start)
- [ ] Database schema approved
- [ ] API design approved
- [ ] Frontend framework chosen
- [ ] Hosting/deployment decided
- [ ] Budget confirmed (free tier / paid)
- [ ] Timeline realistic
- [ ] Testing strategy defined
- [ ] Security requirements met
- [ ] Backup/disaster recovery plan
```

---

## 🛤️ ROADMAP TEMPLATE (Phase 5)

```
## Development Order: [Backend-First / Frontend-First / Parallel]

### Sprint 0: Foundation (Day 1)
- [ ] Project scaffold
- [ ] CI/CD pipeline
- [ ] Database setup
- [ ] Basic auth

### Sprint 1: Core API (Days 2-3)
- [ ] User endpoints
- [ ] Project CRUD
- [ ] Auth middleware

### Sprint 2: Frontend Shell (Days 4-5)
- [ ] Layout components
- [ ] Routing
- [ ] Auth UI

### Sprint 3: Integration (Days 6-7)
- [ ] API connection
- [ ] State management
- [ ] Error handling

### Sprint 4: Polish (Days 8-10)
- [ ] Styling
- [ ] Testing
- [ ] Performance
- [ ] Documentation
```

---

## ⏰ PROGRESS UPDATE FORMAT (Every 15 min)

**Posted to #claw-dev automatically:**

```
🦞 [Project Name] — Update

⏳ Phase: [X/6] — [Phase Name]
📝 Current: [what I'm working on]
✅ Done: [completed task]
⏭️ Next: [upcoming task]
⚠️ Blockers: [none / what needs help]
⏱️ ETA: [time remaining]
```

---

## 🚀 HOW TO ACTIVATE

**Say in #claw-dev:**
```
devops plan mode: I want to build a [your idea]
```

**Example:**
```
devops plan mode: I want to build a client dashboard where my SEO clients can log in and see their ranking reports, traffic analytics, and audit results. It should pull data from Google Search Console and GA4 automatically.
```

**Enigma will:**
1. Acknowledge and start Phase 1
2. Ask 5 clarifying questions
3. Begin research immediately
4. Post first update in 15 minutes

---

## 📁 OUTPUT FILES

All plans saved to:
```
/workspace/docs/devops-plan/
├── <project>/
│   ├── 01-idea.md
│   ├── 02-tech-stack.md
│   ├── 03-architecture.md
│   ├── 04-confirmation.md
│   ├── 05-roadmap.md
│   └── 06-execution-log.md
```

---

## 🛑 CANCEL / MODIFY

At any phase, say:
- `"Cancel devops plan mode"` — Stops everything, archives partial work
- `"Change [X] to [Y]"` — Goes back to appropriate phase
- `"Skip to Phase [N]"` — Jumps ahead (not recommended)

---

## 🧩 SKILLS ARSENAL — Tools Enigma Uses in DevOps Plan Mode

### **Phase 1-2: Research & Planning**
| Skill | Purpose | Install |
|-------|---------|---------|
| **find-skills** | Discover skills for any task | Built-in |
| **web-search** | Research latest tech, APIs, best practices | Built-in |
| **firecrawl** | Extract content from docs, competitor sites | Built-in |
| **tavily-search** | Deep research with citations | `npx skills add tavily-search` |
| **gemini** | Multi-modal research (images, diagrams) | `npx skills add gemini` |
| **github** | Explore open-source solutions | Built-in |

### **Phase 3-4: Architecture & Design**
| Skill | Purpose | Install |
|-------|---------|---------|
| **coding-agent** | Delegate coding tasks to Codex/Claude Code | Built-in |
| **frontend-design** | UI/UX best practices, component libraries | Built-in |
| **senior-architect** | System design patterns, scalability | Built-in |
| **developer** | Code patterns, API design | Built-in |
| **playwright** | Browser automation for UI testing | `npx skills add playwright` |
| **git** | Branching strategy, commit conventions | Built-in |

### **Phase 5-6: Build & Deploy**
| Skill | Purpose | Install |
|-------|---------|---------|
| **devops** | CI/CD, Docker, Kubernetes | Built-in |
| **docker** | Containerization | `npx skills add docker` |
| **domain-dns-ops** | DNS, SSL, domain management | Built-in |
| **performance-reporter** | Speed testing, optimization | Built-in |
| **healthcheck** | Service monitoring, uptime | Built-in |
| **github** | PR automation, releases | Built-in |
| **cost-optimizer** | Keep infrastructure costs low | Built-in |

### **Phase 6+: Documentation & Knowledge**
| Skill | Purpose | Install |
|-------|---------|---------|
| **obsidian** | Export plans to vault | Built-in |
| **notion** | Export plans to Notion | `npx skills add notion` |
| **docx** | Generate Word documents | Built-in |
| **xlsx** | Generate spreadsheets | Built-in |
| **pptx** | Generate presentations | Built-in |
| **pdf** | Generate PDF reports | Built-in |
| **memory-manager** | Persist learnings across sessions | Built-in |
| **self-improvement** | Log errors, learn from mistakes | Built-in |

### **Cross-Cutting: Security & Compliance**
| Skill | Purpose | Install |
|-------|---------|---------|
| **1password** | Secrets management | `npx skills add 1password` |
| **security-audit** | Vulnerability scanning | `npx skills add security-audit` |
| **compliance-check** | GDPR, SOC2, HIPAA | `npx skills add compliance` |

### **Cross-Cutting: Communication**
| Skill | Purpose | Install |
|-------|---------|---------|
| **discord** | Post updates to #claw-dev | Built-in |
| **slack** | Post updates to Slack | `npx skills add slack` |
| **zoho-mail** | Email stakeholders | Built-in |
| **apple-reminders** | Set reminders for milestones | `npx skills add apple-reminders` |

---

## 🎯 SKILL SELECTION LOGIC

**When planning a project, Enigma will:**

1. **Auto-detect** what skills are needed based on project type
2. **Check** if skill is installed (`openclaw skills check`)
3. **Install** missing skills automatically (if free tier)
4. **Use** skills during the appropriate phase

**Example:**
```
Project: SEO Client Dashboard
Detected needs:
  ✓ frontend-design (UI components)
  ✓ developer (API design)
  ✓ devops (deployment)
  ✓ github (CI/CD)
  ✓ obsidian (documentation)
  ✓ performance-reporter (speed testing)
  ✓ 1password (API key management)
  ✓ discord (progress updates)

Auto-installing missing skills...
```

---

## 📊 SKILL PRIORITY MATRIX

| Project Type | Must-Have Skills | Nice-to-Have |
|-------------|------------------|-------------|
| **Web App** | frontend-design, developer, devops, github | playwright, performance-reporter |
| **Mobile App** | frontend-design, developer, devops | firebase, app-store |
| **E-commerce** | frontend-design, developer, devops, cost-optimizer | payment-gateway, analytics |
| **SEO Tool** | web-search, firecrawl, developer, github | tavily-search, gemini |
| **AI/ML** | developer, github, performance-reporter | gemini, model-usage |
| **DevOps/Infra** | devops, domain-dns-ops, healthcheck | docker, kubernetes |
| **Documentation** | docx, xlsx, pptx, pdf, obsidian | notion |

---

## 🔄 HOW IT WORKS (Updated with Skills)

### **PHASE 1 — IDEA LOCK (0-30 min)**
1. You describe your idea
2. **SKILL: web-search + firecrawl** — Research similar products, competitors
3. Enigma asks 5 clarifying questions
4. Idea becomes "LOCKED"
5. Output: `01-idea.md`

### **PHASE 2 — TECH STACK RESEARCH (30-60 min)**
1. **SKILL: find-skills + web-search** — Find best tools for the job
2. **SKILL: github** — Check popular open-source solutions
3. Compare 3+ options per category
4. You pick or Enigma recommends
5. Output: `02-tech-stack.md`

### **PHASE 3 — ARCHITECTURE DESIGN (60-90 min)**
1. **SKILL: senior-architect** — System design patterns
2. **SKILL: frontend-design** — UI/UX planning
3. **SKILL: developer** — API design, code patterns
4. **SKILL: git** — Branching strategy
5. Output: `03-architecture.md`

### **PHASE 4 — QUESTIONNAIRE (90-120 min)**
1. **SKILL: self-improvement** — Log any assumptions
2. **SKILL: memory-manager** — Persist confirmations
3. Ask 10-point confirmation checklist
4. Any red flags = back to Phase 2 or 3
5. Output: `04-confirmation.md`

### **PHASE 5 — DEVELOPMENT ORDER (120-150 min)**
1. **SKILL: devops** — CI/CD pipeline design
2. **SKILL: github** — PR strategy, branch protection
3. **SKILL: cost-optimizer** — Infrastructure budget
4. Decide: Frontend-first or Backend-first
5. Create task breakdown with estimates
6. Output: `05-roadmap.md`

### **PHASE 6 — EXECUTION (150+ min)**
1. **SKILL: coding-agent** — Delegate to Codex/Claude Code
2. **SKILL: playwright** — Automated testing
3. **SKILL: healthcheck** — Monitor services
4. **SKILL: discord** — Post updates every 15 min
5. **SKILL: performance-reporter** — Speed testing
6. **SKILL: 1password** — Manage secrets safely
7. Output: `06-execution-log.md`

---

## 🚀 HOW TO ACTIVATE

**Say in #claw-dev:**
```
devops plan mode: I want to build a [your idea]
```

**Enigma will:**
1. Acknowledge and start Phase 1
2. Auto-install any missing skills (free tier only)
3. Ask 5 clarifying questions
4. Begin research immediately
5. Post first update in 15 minutes

---

**Ready when you are. Just say the trigger phrase.** 🦞
