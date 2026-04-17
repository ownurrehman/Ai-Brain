# Settings Module (Source of Truth)

## Scope Model

- Settings owns workspace-level configuration.
- Settings is global and sits at the bottom of sidebar navigation.
- Settings controls identity, access, diagnostics, and integration defaults.

## Must Be In The App

- Users and roles management.
- Workspace profile and branding configuration.
- Integration/provider configuration surfaces.
- Diagnostics and audit-related settings areas.

## Must Always Work

- User/role access management.
- Workspace preference persistence.
- Settings route stability (`/settings/*`).
- Safe loading and saving with clear success/error states.

## Linking Contracts (Non-Negotiable)

- Settings changes affect all modules, but must not mutate module data ownership.
- RBAC in Settings is the enforcement source for module permissions.
- Provider/setup values used by SEO/Publishing/Finance must be centrally readable.

## Guardrails

- No hidden permission escalation paths.
- No settings writes without workspace scope.
- No fake "configured" status when provider setup is incomplete.
