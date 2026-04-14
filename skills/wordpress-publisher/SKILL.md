---
name: wordpress-publisher
description: Use this skill when content must be prepared, formatted, quality-checked, or published in WordPress. Do not use it for writing from scratch, SEO research, or general web development work. This skill turns finished content into a publish-ready WordPress entry with clean formatting and pre-publish validation.
---

Summary: This skill handles WordPress publishing tasks with minimal CMS-specific context.

# Purpose

Prepare finished content for WordPress and reduce avoidable publish-time errors.

## Use when

- A task involves WordPress post entry, formatting, metadata, links, media placement, or final QA.
- The content exists and now needs CMS execution.
- The risk is publish inconsistency or sloppy formatting.

## Avoid when

- The task is still early-stage research or drafting.
- The task is SEO analysis without CMS action.
- The task is broader plugin or theme development.

## Required inputs

- Final or near-final content.
- Post type or destination.
- Required metadata or publishing constraints.

## Workflow

1. Confirm the content is stable enough to publish.
2. Prepare title, headings, links, media, and metadata.
3. Apply clean WordPress formatting.
4. Run the publish checklist before finalizing.
5. Flag anything that needs human approval before going live.

## Expected outputs

- Publish-ready post or page.
- Clean formatting and link structure.
- Missing-item list if anything blocks publishing.

## Checks before done

- Heading hierarchy is clean.
- Links, media, and metadata are present where needed.
- No obvious placeholder text or formatting artifacts remain.

## Common failure modes

- Publishing draft content too early.
- Broken heading levels or inconsistent formatting.
- Missing slug, category, excerpt, or featured image when required.

## Deep playbooks (Antigravity Awesome Skills)

This file is the **Rank Ray control layer** (publish-ready CMS work). For full WordPress development and plugin-level depth:

| Role | Path |
|------|------|
| WordPress workflow bundle | [`../antigravity-awesome-skills/skills/wordpress/SKILL.md`](../antigravity-awesome-skills/skills/wordpress/SKILL.md) |
| Plugin architecture (if extending WP) | [`../antigravity-awesome-skills/skills/wordpress-plugin-development/SKILL.md`](../antigravity-awesome-skills/skills/wordpress-plugin-development/SKILL.md) |

**Order:** Keep publishing QA and checklist discipline from this file; use catalog when building or hardening WP code.

## Token-saving guidance

- Start here for scope and workflow.
- Use `wordpress-publisher.md` for formatting rules.
- Use `publishing-checklist.md` near the end only.

## References

- [`wordpress-publisher.md`](wordpress-publisher.md)
- [`publishing-checklist.md`](publishing-checklist.md)
