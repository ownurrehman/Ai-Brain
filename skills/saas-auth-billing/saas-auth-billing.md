Summary: Practical checklist for auth + subscription correctness in SaaS products.

# Auth and Billing Checklist

## Identity and access

- Clear separation: user, workspace, role, permission.
- Server-side authorization on every protected operation.
- Session and token expiration rules are explicit.
- Account recovery flow exists and is tested.

## Billing lifecycle

- States include trial, active, past_due, canceled, and grace handling.
- Plan upgrades and downgrades are deterministic.
- Entitlement changes apply immediately and consistently.
- Invoice or payment failures map to clear user-facing actions.

## Webhooks and sync

- Verify event signatures.
- Make handlers idempotent.
- Store processed event IDs.
- Reconcile daily to catch missed events.

## User experience safeguards

- Clear messaging for access denied due to plan.
- Billing portal and payment update path is reachable.
- Admin can view current plan and renewal status.
