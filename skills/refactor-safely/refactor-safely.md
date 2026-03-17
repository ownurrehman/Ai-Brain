Summary: Use this file for compact sequencing rules that keep refactors safe and reviewable.

# Refactor Safely

## Safe sequencing

- Isolate behavior changes from structural changes.
- Prefer extraction before replacement.
- Keep commits or logical steps small enough to reason about.
- Stop when clarity improves; do not refactor for its own sake.

## Safety checks

- Know the invariant behavior.
- Keep the public surface stable unless explicitly changing it.
- Use targeted tests or reproductions on the touched path.
