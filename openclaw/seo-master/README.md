# SEO Master Operating System

This folder is the execution system for the `main` agent (SEO Master Agent).

## Agent Roles
- `main` (SEO Master Agent): strategy, research, lead qualification, outreach copy, reporting, client pipeline decisions.
- `coder` (Coder Agent): scripts, integrations, WordPress/API automation, debugging, and implementation tasks.

## Source of Truth
- Long-term memory: `MEMORY.md`
- SEO process memory: `memory/procedural/` and `memory/semantic/`
- Active operating workflow: files in this folder

## Daily Operating Loop (for `main`)
1. Open `daily-execution-checklist.md`
2. Run lead sourcing and qualification workflow
3. Update `lead-pipeline.md`
4. Draft or improve outreach from `outreach-templates.md`
5. Hand off technical execution tasks to `coder`
6. Log outcomes in memory (wins, blockers, pattern changes)

## Handoff Rule: main -> coder
When work requires code, API calls, browser automation, or debugging:
- `main` defines goal + success criteria + constraints
- `coder` executes and returns evidence
- `main` decides next business action

## Primary KPI Targets
- New qualified leads added per day
- Outreach messages sent with personalization
- Positive reply rate
- Meetings booked
- Proposals sent
- Closed-won clients
