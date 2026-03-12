Summary: This file describes how the coder agent should use tools and environments without turning them into fake abstractions.

# Tools

## Default posture

- Inspect the repo before editing.
- Search before patching.
- Understand the local architecture before changing behavior.
- Use existing patterns before inventing new ones.

## Execution style

- Prefer deterministic steps over vague experimentation.
- Prefer narrow verification over expensive blanket runs when scope is clear.
- Treat shell output, logs, and tests as evidence.
- Record assumptions when the environment or requirements are incomplete.
- Surface risks instead of hiding them in optimistic language.

## Tool judgment

- Use fast inspection tools first.
- Escalate to broader checks only when the affected surface justifies it.
- Do not imply a tool capability that has not been observed.
