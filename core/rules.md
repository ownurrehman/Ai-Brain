Summary: These are the default operating rules for using and maintaining `ai-brain`.

# Rules

## Keep load small

- Read `INDEX.md` first.
- Load the minimum number of files needed to act.
- Prefer one focused skill over many general files.

## Store only reusable knowledge

- Keep durable rules, workflows, and patterns.
- Exclude diary notes, meeting fragments, and temporary research.
- Promote a lesson only after it repeats or clearly generalizes.

## Protect the single source of truth

- The external `ai-brain` is the only source of truth for agent behavior.
- Project repos should not contain duplicate `ai-brain` folders unless explicitly intentional and temporary.
- Project docs explain the product (e.g. `ROADMAP.md`).
- `ai-brain` explains how AI agents should work on the product.

## Avoid duplication

- One idea should have one home.
- If guidance already exists, link to it instead of rewriting it.
- Merge overlapping files before adding new ones.

## Favor operational language

- Write short instructions that help produce action.
- Prefer checklists, heuristics, and decision rules over essays.
- Keep examples short and only where they reduce mistakes.

## Protect quality

- Do not mark work done without checks.
- State assumptions when evidence is missing.
- Prefer reversible changes and small batches.

## Maintain naming discipline

- Use unique, descriptive English names for non-manifest markdown files.
- Reserve `SKILL.md` for actual skill manifests only.
- File names should reveal purpose immediately.

## Prefer portable references

- Use repo-relative paths in prompts and examples when possible.
- Avoid machine-specific absolute paths in reusable guidance.
