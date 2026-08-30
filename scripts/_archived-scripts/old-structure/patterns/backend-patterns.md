> **Parent Hub:** [[scripts/_archived-scripts/INDEX|📦 Legacy Systems & Scripts Archive]] · [[scripts/INDEX|🛠️ Scripts Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

Summary: This file stores reusable backend implementation patterns that reduce common service and API mistakes.

# Backend Patterns

## Prefer

- Explicit input validation close to the boundary.
- Small service functions with predictable contracts.
- Idempotent operations where retries are likely.
- Structured logs around risky edges.

## Watch for

- Hidden side effects.
- Leaky database assumptions in handlers.
- Overloaded endpoints that mix unrelated responsibilities.
