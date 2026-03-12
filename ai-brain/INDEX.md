Summary: This file routes AI and humans to the smallest useful part of `ai-brain` for a given task.

# Routing Index

Use this file before loading anything else. Read only the entries relevant to the current job.

| Path | Purpose | Use when | Skip when |
| --- | --- | --- | --- |
| `README.md` | Defines the system and its compact philosophy. | You need the overall intent of `ai-brain`. | You already know the operating model. |
| `INDEX.md` | Main routing file for selective loading. | Starting any new task in this brain. | You have already chosen the exact file to load. |
| `core/rules.md` | Global operating rules that should stay stable across tasks. | You need baseline behavior and quality bars. | A skill already covers the job-specific rules. |
| `core/task-routing.md` | Quick switchboard for picking the right skill by task type. | You need to choose a skill fast without reading multiple manifests. | The lead skill is already obvious. |
| `core/prompt-protocol.md` | Default protocol for choosing what to read and in what order. | You need a low-token decision path. | The task is already fully scoped. |
| `core/decision-framework.md` | Helps choose between shipping, debugging, refactoring, content, and publishing flows. | The task could fit multiple paths. | The task cleanly maps to one skill. |
| `core/foundation-context.md` | Durable context about the priorities behind this brain. | You need stable context for tradeoffs. | The task is purely mechanical. |
| `skills/seo/` | Skill for SEO research, prioritization, and search intent work. | The task is about SEO strategy or SERP-informed decisions. | The task is only writing copy without search goals. |
| `skills/seo/SKILL.md` | Entry manifest for the SEO skill. | You need the SEO workflow and boundaries. | You only need a specific SEO reference already known. |
| `skills/seo/seo.md` | Core SEO heuristics and outcome model. | You need quick SEO principles and prioritization. | You only need SERP or clustering details. |
| `skills/seo/serp-intelligence.md` | How to inspect live search results and infer intent. | You are analyzing competitors, ranking patterns, or content gaps. | The task has no SERP component. |
| `skills/seo/keyword-clustering.md` | How to group terms into pages and content plans. | You need clusters, topical maps, or page targeting. | You only need on-page optimization. |
| `skills/content-writing/` | Skill for structured, useful, search-aware content writing. | The task is drafting or improving reusable content. | The task is pure SEO research or CMS publishing only. |
| `skills/content-writing/SKILL.md` | Entry manifest for the content-writing skill. | You need the writing workflow and scope. | You only need a specific writing reference already known. |
| `skills/content-writing/content-writing.md` | Core writing standards for clarity, usefulness, and conversion. | You need general writing guidance. | You only need structure or E-E-A-T specifics. |
| `skills/content-writing/blog-structure.md` | Simple patterns for shaping articles and sections. | You are outlining or restructuring a post. | The content is not a blog-style asset. |
| `skills/content-writing/eeat.md` | Practical E-E-A-T signals to strengthen trust. | You need credibility, expertise, or evidence cues. | The asset is informal or internal-only. |
| `skills/wordpress-publisher/` | Skill for preparing and publishing content in WordPress. | The task involves WordPress entry, formatting, or pre-publish QA. | The task ends before CMS publishing. |
| `skills/wordpress-publisher/SKILL.md` | Entry manifest for the WordPress publisher skill. | You need the publishing workflow and boundaries. | You only need the checklist reference. |
| `skills/wordpress-publisher/wordpress-publisher.md` | Practical rules for preparing content for WordPress. | You need formatting and field decisions. | You only need the final QA checklist. |
| `skills/wordpress-publisher/publishing-checklist.md` | Pre-publish checklist for consistency and quality. | You are close to publishing. | You are still drafting or researching. |
| `skills/shipping-features/` | Skill for moving scoped work to a safe shipped state. | The task is implementation and delivery. | The task is mainly diagnosis or refactoring. |
| `skills/shipping-features/SKILL.md` | Entry manifest for the shipping-features skill. | You need the shipping workflow and handoff shape. | You only need the feature delivery reference. |
| `skills/shipping-features/shipping-features.md` | Small-batch delivery patterns and completion checks. | You need execution guidance for feature work. | The task is not feature delivery. |
| `skills/debugging/` | Skill for reproducing, isolating, and fixing defects. | The task is a bug, regression, or broken workflow. | You are building a new feature from scratch. |
| `skills/debugging/SKILL.md` | Entry manifest for the debugging skill. | You need the debugging flow and output standard. | You already know the debug checklist. |
| `skills/debugging/debugging.md` | Concrete debugging heuristics and evidence rules. | You need a disciplined diagnostic process. | The problem is design or planning only. |
| `skills/refactor-safely/` | Skill for changing structure while preserving behavior. | The task is cleanup, extraction, simplification, or reorganization. | The task changes product behavior materially. |
| `skills/refactor-safely/SKILL.md` | Entry manifest for the refactor-safely skill. | You need the refactor workflow and guardrails. | You only need the reference checklist. |
| `skills/refactor-safely/refactor-safely.md` | Practical refactor sequencing and safety checks. | You need to break a refactor into low-risk steps. | You are debugging or shipping new behavior. |
| `patterns/architecture-patterns.md` | Reusable system-structure patterns for projects and features. | You need a compact architecture decision aid. | The task is content or publishing work. |
| `patterns/backend-patterns.md` | Common backend patterns for services, APIs, and jobs. | You need implementation patterns on the server side. | The task is frontend-only or non-code. |
| `patterns/ui-ux-patterns.md` | Reusable interface and interaction patterns. | You need practical UI or UX direction. | The task has no user-facing interface. |
| `memory/lessons-learned.md` | Ledger of repeatable lessons worth reusing. | You want to avoid known failure patterns. | The task is trivial and low risk. |
| `memory/past-wins.md` | Ledger of approaches that have worked well before. | You want a fast path based on proven tactics. | Novel constraints make prior wins irrelevant. |
| `projects/project-template.md` | Template for adding a compact project-specific brain file later. | A project needs its own reusable context. | The work can stay inside the shared brain. |
| `maintenance/brain-update-process.md` | Rules for keeping the brain lean and current. | You are adding, merging, pruning, or reviewing files. | You are only consuming existing guidance. |
