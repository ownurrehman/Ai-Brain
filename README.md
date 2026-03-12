Summary: This repository now supports folder-based agents powered mostly by markdown files plus a thin Python loader.

# AI Brain Repository

## Current architecture

The existing [`ai-brain/`](ai-brain/) folder remains the shared knowledge base: compact rules, skills, patterns, memory, and maintenance guidance. It is human-edited markdown and is not tied to any one runtime agent.

The new [`ai_brain/`](ai_brain/) package adds the runtime layer. Each folder inside [`ai_brain/agents/`](ai_brain/agents/) is an agent definition. The runtime becomes a different agent by loading a different folder.

## How agent folders work

An agent is just a folder of markdown files. The runtime reads top-level `.md` files from the chosen folder, orders them deterministically, and composes them into one payload for the model.

Example:

```text
ai_brain/
  agents/
    researcher/
      identity.md
      instructions.md
      tasks.md
      tools.md
      memory.md
```

## Deterministic load order

The loader prefers these files first when present:

1. `identity.md`
2. `instructions.md`
3. `tasks.md`
4. `planning.md`
5. `coding_standards.md`
6. `tools.md`
7. `memory.md`

Any other top-level markdown files in the agent folder are loaded afterward in alphabetical order. This keeps conventions simple while allowing extension without new code.

## How to add a new agent

1. Create a folder under [`ai_brain/agents/`](ai_brain/agents/).
2. Add `identity.md` and `instructions.md`.
3. Add only the extra markdown files that materially change behavior.
4. Keep the files human-readable and operational.
5. Run the loader or tests to confirm the agent composes correctly.

## How to run an agent

List available agents:

```bash
python3 -m ai_brain --list-agents
```

Load by name:

```bash
python3 -m ai_brain --agent researcher
```

Load by folder path:

```bash
python3 -m ai_brain --agent-path ai_brain/agents/coder
```

Show payload plus source files:

```bash
python3 -m ai_brain --agent strategist --show-files
```

## Notes

- The runtime is intentionally thin. Most behavior belongs in markdown.
- The shared [`ai-brain/`](ai-brain/) folder remains intact for reusable knowledge.
- If you later need permissions or tool metadata, add them as markdown first and introduce structured config only when markdown is no longer enough.
