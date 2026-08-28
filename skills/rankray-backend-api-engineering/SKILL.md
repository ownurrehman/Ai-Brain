---
name: rankray-backend-api-engineering
description: "Backend development guidelines: Node.js, Express, TypeScript, Prisma ORM, PostgreSQL relational modeling, Zod validation, and error middleware."
---

# 🛠️ RankRay Backend API & Database Engineering

> **Layered architecture standards for Node.js, Express, Prisma, and PostgreSQL.**

---

## 🏛️ 1. Layered Architecture Pattern
1. **Controller Layer:** Parses HTTP requests, validates payloads via Zod, calls services, and formats JSON responses.
2. **Service Layer:** Houses core business logic, permissions, and multi-model coordination.
3. **Repository / Prisma Layer:** Handles direct database reads, writes, and transactions.

---

## 🛡️ 2. Zod Payload Validation Example
```typescript
import { z } from 'zod';

export const CreateAuditSchema = z.object({
  targetUrl: z.string().url(),
  clientDomain: z.string().min(3),
  auditDepth: z.enum(['QUICK', 'STANDARD', 'FORENSIC']),
  assignedAgent: z.string().optional()
});

export type CreateAuditInput = z.infer<typeof CreateAuditSchema>;
```

---

## 💾 3. Prisma Transaction Safety
```typescript
const result = await prisma.$transaction(async (tx) => {
  const audit = await tx.auditRecord.create({
    data: { domain, status: 'PROCESSING' }
  });
  
  await tx.activityLog.create({
    data: { event: 'AUDIT_INITIATED', auditId: audit.id }
  });

  return audit;
});
```
