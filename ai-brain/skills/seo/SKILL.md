---
name: seo
description: Use this skill when work depends on search demand, ranking opportunity, SERP analysis, topic targeting, or on-page SEO decisions. Do not use it for generic copywriting, CMS publishing, or broad marketing strategy without a search goal. This skill turns a topic or keyword set into SEO-informed recommendations, content direction, and prioritization.
---

Summary: This skill handles compact, search-informed SEO work without loading broad marketing context.

# Purpose

Turn topics, keywords, or pages into clear SEO actions grounded in intent, competition, and page targeting.

## Use when

- A task mentions SEO, keywords, search intent, rankings, topical authority, or organic traffic.
- You need page targeting, cluster decisions, or SERP-aware recommendations.
- Content direction depends on what searchers want and what already ranks.

## Avoid when

- The task is pure copywriting with no search goal.
- The task is only WordPress formatting or publishing.
- The task is a full marketing strategy that extends beyond organic search.

## Required inputs

- Topic, keyword, page, or site scope.
- Target market or geography if it matters.
- Desired outcome such as ranking, traffic, leads, or page consolidation.

## Workflow

1. Define the SEO unit of work: page, cluster, audit, or brief.
2. Identify the primary search intent and likely content type.
3. Check whether live SERP inspection or keyword clustering is needed.
4. Produce a prioritized recommendation, not a dump of observations.
5. State assumptions where live data is missing.

## Expected outputs

- Page or cluster recommendation.
- Clear intent summary.
- Prioritized SEO actions.
- Risks, assumptions, and next checks.

## Checks before done

- One primary intent is named.
- Recommendations map to a page or cluster, not vague site advice.
- Low-confidence claims are marked as assumptions.

## Common failure modes

- Mixing multiple intents into one page.
- Giving generic SEO tips with no prioritization.
- Treating keywords as separate pages when they belong in one cluster.

## Token-saving guidance

- Start here, then load only one reference file if needed.
- Use `seo.md` for general decisions.
- Use `serp-intelligence.md` only when live SERP patterns matter.
- Use `keyword-clustering.md` only when grouping terms or pages.

## References

- [`seo.md`](seo.md)
- [`serp-intelligence.md`](serp-intelligence.md)
- [`keyword-clustering.md`](keyword-clustering.md)
