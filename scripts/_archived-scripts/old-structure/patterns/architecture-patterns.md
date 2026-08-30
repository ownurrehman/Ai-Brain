> **Parent Hub:** [[scripts/_archived-scripts/INDEX|📦 Legacy Systems & Scripts Archive]] · [[scripts/INDEX|🛠️ Scripts Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

Summary: This file stores small, reusable architecture patterns that frequently help with system design.

# Architecture Patterns

## Favor

- Thin interfaces around unstable dependencies.
- Clear boundaries between domain logic and delivery layers.
- Small modules with explicit responsibilities.
- Eventual extension points only where repeated change justifies them.

## Avoid

- Abstracting for imagined future states.
- Spreading the same decision across many layers.
- Hiding critical flow inside clever indirection.
