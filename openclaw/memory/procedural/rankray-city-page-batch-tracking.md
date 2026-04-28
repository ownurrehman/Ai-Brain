# Rank Ray City Page Batch Tracking

## Purpose
Prevent loss of rollout state for Rank Ray SEO city pages.

## Rules
1. After defining a rollout, save the batch list in semantic memory.
2. After starting a batch, log the active batch and page slugs in episodic memory.
3. After each page is created or updated, log the slug and status.
4. After the batch ends, record one of: planned, running, blocked, completed.
5. Never say a batch is finished without verification from workspace artifacts, CMS state, or logs.

## Standard status format
- Batch: [name or number]
- Status: planned | running | blocked | completed
- Slugs:
  - slug-1
  - slug-2
  - slug-3
  - slug-4
  - slug-5
- Evidence:
  - file path, report path, CMS confirmation, or session note

## Current reference
Use `memory/semantic/rankray-seo-city-pages.md` as the canonical rollout reference.

## Latest verified batch
- Batch: 3
- Status: completed
- Slugs:
  - seo-agency-calgary
  - seo-agency-ottawa
  - seo-agency-mississauga
  - seo-agency-austin
  - seo-agency-seattle
- Evidence:
  - WP REST GET on 2026-03-30 confirmed publish status for all five slugs on rankray.com
  - `memory/episodic/2026-03-29.md` matches the working Batch 3 slug set
