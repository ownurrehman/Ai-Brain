Summary: This protocol helps an AI choose the smallest useful context before acting.

# Prompt Protocol

## Default read order

1. Read `INDEX.md`.
2. Pick one matching skill if the task is specialized.
3. Read one supporting reference file only if the skill calls for it.
4. Read a core file only when the skill does not already answer the question.
5. Stop loading once the next decision is clear.

## Routing questions

Ask these silently before opening more files:

1. What is the actual job: decide, write, debug, refactor, ship, or publish?
2. Which single file would most reduce uncertainty?
3. What can be skipped without affecting output quality?

## Escalation rule

Open broader context only if:

- the task crosses domains,
- the first file leaves a material ambiguity, or
- the output quality depends on durable constraints not yet loaded.

## Response discipline

- Summarize what was loaded.
- Avoid quoting large chunks of the brain.
- Convert guidance into action, not paraphrase.
