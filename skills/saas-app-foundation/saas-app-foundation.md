Summary: Baseline architecture checklist for SaaS web applications.

# SaaS Foundation Baseline

## Core decisions

- Single-tenant vs multi-tenant strategy.
- Monolith-first vs service split timing.
- Database ownership and migration policy.
- Sync APIs vs async jobs by workload type.

## Security baseline

- Centralized auth with role and permission checks.
- Environment-level secret management.
- Input validation and safe default authorization.
- Audit trail for sensitive account actions.

## Delivery baseline

- Staging mirrors production risk profile.
- Automated migrations with rollback notes.
- CI checks for tests, lint, and build.
- Release notes and deployment runbook.

## Operability baseline

- Request and job tracing IDs.
- Error alerting with actionable metadata.
- Health checks and dependency status.
- Backup and restore verification routine.
