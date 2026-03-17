Summary: This framework helps choose the right path before loading more context or doing work.

# Decision Framework

## Choose the primary mode

- Use `skills/debugging/` when something should work but does not.
- Use `skills/shipping-features/` when behavior must be added or completed.
- Use `skills/refactor-safely/` when structure must improve without changing behavior.
- Use `skills/content-writing/` when the output is content for humans to read.
- Use `skills/seo/` when search demand, intent, or ranking opportunity matters.
- Use `skills/wordpress-publisher/` when content must be prepared or published in WordPress.

## Break ties by outcome

- If the main risk is wrong diagnosis, debug first.
- If the main risk is incomplete delivery, use shipping.
- If the main risk is regression during cleanup, use refactor-safely.
- If the main risk is weak usefulness or clarity, use content-writing.
- If the main risk is poor discoverability, use SEO.
- If the main risk is formatting or publish errors, use WordPress publisher.

## Prefer one lead path

Pick one primary skill. Pull in a second file only when the work genuinely crosses boundaries.
