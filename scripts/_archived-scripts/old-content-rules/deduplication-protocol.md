> **Parent Hub:** [[scripts/_archived-scripts/INDEX|📦 Legacy Systems & Scripts Archive]] · [[scripts/INDEX|🛠️ Scripts Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# 🛡️ Deduplication & Entity Clustering Protocol

**Status**: MANDATORY
**Applicability**: All agents transitioning a topic from `Planned` to `Drafting`.

## 1. The Pre-Draft Gate
Before generating an outline or starting a draft for any content piece, the agent **must** verify that the intent is unique and not already covered by an existing or planned article.

### Required Action:
Run the deduplication script:
```bash
python3 core/scripts/semantic_dedup.py
```
Check the output at `openclaw/DEDUPLICATED_QUEUE.md`.

## 2. Decision Logic
If the script (or manual semantic analysis) identifies an overlap with another planned topic or existing page:

### A. Intent Overlap (Keywords share >40% core entities)
- **Action**: MERGE.
- **Pillar Structure**:
    - The most competitive or action-oriented keyword becomes the **Primary H1**.
    - The overlapping long-tail keywords are converted into **H2 or H3 sub-headers**.
    - The resulting article must be expanded (3,000+ words) to cover all semantic nuances.

### B. Entity Duplication (Same subject, different phrasing)
- **Action**: CONSOLIDATE.
- **Rule**: Never publish two articles where the "What is [X]" section is effectively identical.

## 3. Workflow Integration (OpenClaw)
1. **Planned**: Topics are added to `hermes/rankray-30-day-blog-strategy.md` or similar.
2. **Pre-Processing (New)**: Run `semantic_dedup.py`.
3. **Refactored Queue**: Review `openclaw/DEDUPLICATED_QUEUE.md`.
4. **Drafting**: Only proceed with topics marked as `UNIQUE` or the new `PILLAR MERGE` results.

## 4. Failure to Comply
Any article generated that creates a cannibalization issue (two pages competing for the same primary intent) is considered a failure of the **Karpathy Simplicity Principle** and must be trashed/merged immediately.
