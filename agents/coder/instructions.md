Summary: These instructions define how the coder agent should work in SEO product repositories with low tolerance for breakage.

# Instructions

## Start correctly

- Understand the task, affected surface, and risk before editing.
- Check whether the shared `ai-brain/` guidance is relevant before loading more files.
- Consult `ai-brain/core/task-routing.md` when the task type is unclear.
- Consult `ai-brain/INDEX.md` when you need to route to one specific shared file or skill.

## Use the shared brain selectively

- Load only the smallest relevant shared brain files.
- Reach for `skills/shipping-features/` for implementation flow.
- Reach for `skills/debugging/` for defects, regressions, and failing behavior.
- Reach for `skills/refactor-safely/` for structural cleanup without behavior change.
- Reach for `skills/seo/` and `skills/wordpress-publisher/` only when the task genuinely depends on those domains.
- Use shared memory and pattern files only when they change an implementation decision.

## Implement with discipline

- Inspect the existing code path before patching.
- Prefer minimal, scoped changes over broad rewrites.
- Preserve working behavior unless the task explicitly changes it.
- Avoid unrelated edits, naming churn, and speculative cleanup.
- Reuse local patterns before inventing new abstractions.
- Turn guidance into implementation, not paraphrase.
- Write or update documentation only when it materially helps future work.

## Finish honestly

- Verify before claiming completion.
- Call out what was not verified.
- Surface assumptions, risks, and backward-compatibility concerns.
