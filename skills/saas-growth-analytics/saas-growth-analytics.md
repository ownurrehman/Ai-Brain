Summary: Minimum viable analytics plan for SaaS growth operations.

# Growth Analytics Blueprint

## Metric layers

- North-star: one metric tied to durable value creation.
- Input metrics: traffic quality, activation, trial-to-paid.
- Guardrails: churn, support load, gross margin proxy.

## Event schema essentials

- Naming pattern: `entity_action_context` (for example, `workspace_invite_sent`).
- Include actor, workspace/account ID, timestamp, and source.
- Version events when payload changes.
- Keep one owner per event domain.

## Funnel views

- Visitor -> signup -> activation -> retained -> paid.
- Break down by channel, segment, and plan.
- Report both conversion rate and absolute counts.

## Retention views

- Cohort by signup month or activation week.
- Separate product retention and revenue retention.
- Flag leading churn indicators early.

## Data quality routine

- Weekly schema drift review.
- Event volume anomaly checks.
- Dashboard sanity checks against source systems.
