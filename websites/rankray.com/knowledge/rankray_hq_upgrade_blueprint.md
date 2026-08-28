> **Parent Site:** [[websites/rankray.com/index|🌐 rankray.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Strategic Blueprint: Rank Ray HQ - 'Healing Engine' Upgrade

## 1. Current Understanding & Technical Map

### Infrastructure Overview
- **Project Name**: Rank Ray HQ
- **Frontend**: React + TypeScript (Vite)
  - **Location**: `/Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-frontend`
  - **Architecture**: Modular feature-based structure. Components are organized by domain (e.g., `src/modules/automation`).
  - **Navigation**: Centralized in `Sidebar.tsx` using a custom `navigateTo` routing mechanism.
- **Backend**: NestJS + TypeScript
  - **Location**: `/Users/sheikhown/Ai Works - Local/Ai Codes/Rank Ray HQ/rankray-hq-backend`
  - **ORM**: Prisma
  - **Database**: SQLite (`dev.db`)
- **Integration Layer**: The system leverages WordPress REST API connections (user pass/website rest) to execute bulk operations, which is the same pattern the Healing Engine will utilize.

### Automation Module Analysis
The `automation` module currently houses:
- `bulk-pages`: Likely for mass page generation.
- `content-automation`: General content workflows.
The Healing Engine will be integrated as a third pillar within this module to ensure a unified "Agentic SEO" experience.

---

## 2. Audit Findings

### Technical Debt & Bottlenecks
- **Routing Synchronization**: The frontend uses `window.history.pushState` without a formal routing library (like React Router) in some areas, which may lead to state synchronization issues when adding complex nested tabs.
- **Service Coupling**: `automationService.ts` is becoming a "god service." The Healing Engine should have its own dedicated service to avoid bloating existing automation logic.
- **State Management**: The current UI depends heavily on global stores. Introducing a high-frequency "Pulse" feed will require optimized state updates (e.g., using a polling mechanism or WebSockets) to avoid re-rendering the entire dashboard.

### Injection Points
- **Sidebar**: `src/components/layout/Sidebar.tsx` $->$ Add entry for 'Healing Engine'.
- **Module Wrapper**: `src/modules/automation/Automation.tsx` $->$ Add routing/tab logic for the new engine.
- **Backend API**: `rankray-hq-backend/src` $->$ Create a new `HealingModule` in NestJS.

---

## 3. Healing Engine Architecture

### The Agentic Loop: Monitor $->$ Propose $->$ Execute

#### A. Pulse (Monitoring Feed)
- **Function**: Anomaly detection.
- **Logic**: A background agent periodically scans the target site for:
  - 404 Errors (Broken links)
  - Meta-tag omissions
  - Indexing issues (noindex tags on critical pages)
  - Sudden rank drops for target keywords.
- **UI**: A streaming feed of "Health Alerts" categorized by severity (Critical, Warning, Info).

#### B. Battle Room (Approval Queue)
- **Function**: Human-in-the-loop validation.
- **Logic**: When an anomaly is detected, the agent generates a "Heal Proposal."
  - *Example*: "Detected 404 on `/old-service`. Proposal: Redirect to `/new-service` via WP-REST API."
- **UI**: A queue of proposal cards with:
  - **Action**: What will be changed.
  - **Reasoning**: Why this fix is necessary.
  - **Confidence Score**: Agent's certainty in the fix.
  - **Controls**: `Approve` (Execute) | `Reject` (Ignore) | `Modify` (Edit Proposal).

#### C. History (Healing Log)
- **Function**: Impact auditing.
- **Logic**: Logs every execution and monitors the result over time.
- **UI**: A chronological log showing:
  - Issue $->$ Fix $->$ Result.
  - Metric delta (e.g., "Page load time decreased by 200ms" or "Rank improved from #12 to #8").

### Technical Specifications

#### API Endpoints (NestJS)
- `GET /healing/pulse`: Retrieve active anomalies.
- `POST /healing/propose`: Trigger a manual scan/proposal generation.
- `PATCH /healing/action/:id`: Update status of a proposal (Approve/Reject).
- `GET /healing/history`: Fetch logs of completed heals.

#### Database Schema (Prisma)
```prisma
model HealingIssue {
  id          String   @id @default(uuid())
  siteId      String
  type        String   // e.g., "404_ERROR", "META_MISSING"
  severity    String   // "CRITICAL", "WARNING", "INFO"
  description String
  status      String   // "PENDING", "APPROVED", "REJECTED", "HEALED"
  createdAt   DateTime @default(now())
  actions     HealingAction[]
}

model HealingAction {
  id          String   @id @default(uuid())
  issueId     String
  issue       HealingIssue @relation(fields: [issueId], references: [id])
  actionTaken String
  result      String
  rankDelta   Int?     // Difference in rank position
  executedAt  DateTime @default(now())
}
```

---

## 4. Proposed Advanced Features

- **Autonomous Auto-Heal**: Allow users to toggle "Auto-Heal" for low-risk categories (e.g., automatically fix missing alt tags based on page content).
- **Internal Link Weaver**: Agent analyzes content gaps and proposes internal links from high-authority pages to "orphan" pages.
- **Schema Repair Bot**: Detects invalid JSON-LD and automatically corrects syntax errors to ensure Google Rich Snippets.
- **Competitive Healing**: Monitor a competitor's rank gain on a specific keyword and propose a content "healing" update to reclaim the position.

---

## 5. Implementation Roadmap

### Phase 1: Foundation (Backend)
1. [ ] **Schema Update**: Deploy `HealingIssue` and `HealingAction` models via Prisma.
2. [ ] **Service Layer**: Implement `HealingService` for anomaly detection logic.
3. [ ] **API Controllers**: Create the REST endpoints for Pulse and Battle Room.

### Phase 2: UI Implementation (Frontend)
1. [ ] **Module Setup**: Create `src/modules/automation/features/healing-engine`.
2. [ ] **Pulse Tab**: Build the anomaly monitoring feed.
3. [ ] **Battle Room Tab**: Build the approval card system.
4. [ ] **History Tab**: Build the result tracking table.

### Phase 3: Integration & Loop Closure
1. [ ] **Sidebar Injection**: Add 'Healing Engine' to `Sidebar.tsx`.
2. [ ] **Credential Bridge**: Connect `HealingService` to the `website rest` authentication layer.
3. [ ] **Agentic Loop Test**: End-to-end test: Scan $->$ Propose $->$ Approve $->$ Verify Fix.

### Phase 4: Optimization
1. [ ] **Performance Tuning**: Implement caching for the Pulse feed.
2. [ ] **Advanced Features**: Integrate Internal Link Weaver.
