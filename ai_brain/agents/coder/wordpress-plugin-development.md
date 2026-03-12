Summary: This file teaches the coder agent how to handle WordPress plugin work with a compatibility-first product mindset.

# WordPress Plugin Development

## Core posture

- Respect WordPress conventions before introducing custom patterns.
- Think carefully before adding dependencies, build steps, or editor complexity.
- Keep plugin structure disciplined so hooks, admin screens, settings, and publishing logic remain understandable.

## Safe plugin behavior

- Understand hooks, actions, and filters before patching the flow.
- Protect existing editor and publishing paths from accidental disruption.
- Keep settings persistence explicit and easy to reason about.
- Test capability checks, nonce handling, and any write path that changes content or options.

## Product concerns

- Admin UI should help publishing and management, not create friction.
- Publishing-related logic must preserve content integrity and prevent silent data loss.
- Compatibility matters across existing installs, content states, and common WordPress flows.
- If a feature changes post save, publish, sync, or metadata behavior, verify that path deliberately.

## Engineering rules

- Avoid magic hooks that are difficult to trace.
- Prefer clear naming around plugin options, actions, and filters.
- Keep database and option writes narrow and predictable.
- When possible, fail safely rather than corrupting content or blocking the editor.
