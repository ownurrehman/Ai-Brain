# Semantic Content Brief Engine: Implementation Plan (Rank Ray)

## Executive Summary
The Semantic Content Brief Engine transforms a manual 6-hour research process into a 15-minute automated pipeline. By implementing Koray Tuğberk Gübür's semantic SEO framework, the engine shifts from "keyword targeting" to "topical authority coverage" through entity-based mapping and semantic frame saturation.

**Core Objective:** Deliver 104-195 production decisions per brief, ensuring 100% semantic coverage of the target topic.

---

## 1. System Architecture & Data Flow

### High-Level Logic Flow
`Target Topic` $\rightarrow$ `Researcher (Extraction)` $\rightarrow$ `Entity Graph (PPR)` $\rightarrow$ `Enigma (Semantic Mapping)` $\rightarrow$ `Chronos (WP Integration)` $\rightarrow$ `Production Brief`

### The Pipeline
1.  **Ingestion Layer:** Semrush API pulls $\sim$2,400 queries $\rightarrow$ Filtered by GSC performance $\rightarrow$ Grouped into 5 intent streams.
2.  **Intelligence Layer:** 10 competitor pages ingested $\rightarrow$ DOM analysis $\rightarrow$ Entity extraction $\rightarrow$ Wikipedia PPR validation.
3.  **Mapping Layer:** 9-Frame coverage audit $\rightarrow$ Query-to-Heading mapping $\rightarrow$ Modality & Format planning.
4.  **Output Layer:** Generation of the 13-field structured brief $\rightarrow$ Delivery to WordPress/Markdown.

---

## 2. Agent Topology

| Agent | Role in Engine | Primary Responsibility | Key Tools |
| :--- | :--- | :--- | :--- |
| **Researcher** | Intelligence Lead | Competitor extraction, Entity mapping, SERP capture, Wikipedia LSI pulls. | Semrush API, OpenSource SERP Fetcher, Wikipedia API, GSC API (Pending) |
| **Enigma** | Semantic Architect | Intent classification, Frame gap analysis, 13-field brief construction, Voice/CTA layering. | LLM (High Context Window), Semantic Framework Library |
| **Chronos** | Integration Engineer | Data storage, WP REST API push, Asset management, Pipeline automation. | Python, SQLite/PostgreSQL, WP-JSON |
| **Main (Ranki)** | Quality Gate | Final validation, Strategic alignment, Production sign-off. | Review Dashboard |

---

## 3. Tool & API Requirements

| Tool/API | Purpose | Requirement | Cost Tier |
| :--- | :--- | :--- | :--- |
| **Semrush API** | Query & Competitor Data | Pulling keyword clusters, volumes, and competitor gaps. | Paid |
| **OpenSource SERP Fetcher** | SERP Intelligence | PAA boxes, Featured Snippets, Related Searches, SERP Screenshots. | Free |
| **Wikipedia API** | Entity Expansion | Synonyms, Antonyms, Hyponyms, Hypernyms. | Free |
| **GSC API** | Performance Validation | Cross-referencing real-world clicks/impressions. | Free (Pending Provision) |
| **DBpedia / Wikidata** | PPR Classification | Validating entity relationships (Property/Purpose). | Free |
| **LLM (Gemma 4/Qwen)** | Analysis & Drafting | Semantic classification and brief generation. | API-based |

### Integration Notes
- **Semrush API:** Requires API key. Rate limits are strictly enforced per account tier. Data is returned in JSON. Integration will focus on `Keyword Research` and `Domain Analysis` endpoints.
- **GSC API:** Pending provisioning. Requires OAuth2 authentication and service account setup. Data will be pulled for specific URL prefixes to identify "low-hanging fruit" (high impression/low CTR).
- **OpenSource SERP Fetcher:** Self-hosted/local tool. Requires proxy management to avoid IP blocks. Output is raw HTML/JSON. Integration requires a parsing layer to extract specific SERP elements (PAAs, Snippets).

---

## 4. Data Structures

### A. Entity Map (PPR Format)
```json
{
  "entity": "Keyword/Concept",
  "classification": {
    "purpose": "The 'why' of the entity in this context",
    "property": "Defining characteristics",
    "relationship": "How it connects to the core topic"
  },
  "semantic_network": {
    "synonyms": [],
    "antonyms": [],
    "hyponyms": [],
    "hypernyms": []
  }
}
```

### B. Query Stream
```json
{
  "intent_stream": "Informational / Transactional / etc",
  "queries": ["query 1", "query 2"],
  "target_heading": "H2/H3 identifier",
  "gsc_status": "high_impression/low_click"
}
```

### C. Semantic Frame Status
A boolean matrix of the 9 frames (Definition, Cause, Effect, Comparison, Process, Example, Solution, Tool, Nuance) mapped against each core section.

---

## 5. The 13-Field Brief Specification
Every section in the brief must contain these 13 fields to eliminate writer guesswork:

1.  **Entity Map:** List of mandatory entities to mention.
2.  **Frame Context:** Which of the 9 semantic frames this section satisfies.
3.  **Modality Matching:** Expected format (e.g., Table, Bullet List, Step-by-Step).
4.  **Content Boundaries:** What *not* to cover (to prevent duplication).
5.  **Dedup Tracking:** ID of the semantic concept to avoid repetition across sections.
6.  **Bold Guidance:** Specific terms/phrases to emphasize for scannability.
7.  **Persona Targeting:** The specific user state (e.g., "Frustrated Beginner").
8.  **CTA Integration:** The conversion angle for this specific section.
9.  **Information Gain:** The unique insight/data point that beats competitors.
10. **Semantic Distance:** Degree of relevance to the primary head term (Core vs. Supporting).
11. **Intent Saturation:** Checklist of query-answers that must be present.
12. **Topical Authority Link:** The specific internal page to link to for support.
13. **Cognitive Load:** Complexity level (Simple $\rightarrow$ Technical) for this section.

---

## 6. Implementation Roadmap

### Phase 1: Intelligence Extraction (Weeks 1-2)
- [ ] Integrate Semrush API for automated query pulling and gap analysis.
- [ ] Set up OpenSource SERP Fetcher for PAA, Snippets, and Related search extraction.
- [ ] Implement the Wikipedia/DBpedia PPR extraction script.
- [ ] **Pending:** Provision GSC API and integrate performance validation.
- [ ] **Estimate:** 40 hours dev.

### Phase 2: Semantic Mapping (Weeks 3-4)
- [ ] Develop the 5-stream intent classifier.
- [ ] Build the 9-frame coverage auditor.
- [ ] Create the Query-to-Heading mapping logic.
- [ ] **Estimate:** 30 hours dev.

### Phase 3: Brief Generation & Integration (Weeks 5-6)
- [ ] Engineer the 13-field prompt sequence for Enigma.
- [ ] Build the "Brief $\rightarrow$ WP Draft" pipeline via Chronos.
- [ ] Implement the Quality Gate validation checklist.
- [ ] **Estimate:** 30 hours dev.

---

## 7. Cost & Risk Analysis

### Cost Analysis
- **Manual Cost:** 6 hours $\times$ Hourly Rate $\times$ 30 briefs $\approx$ Massive overhead.
- **Automated Cost:** Semrush API costs (Fixed monthly fee) + OpenSource SERP Fetcher (Free) + Token costs $\approx$ Highly optimized.
- **ROI:** $\approx$ 95% reduction in time-to-brief; increased topical coverage $\rightarrow$ higher rankings.

### Risk Assessment
| Risk | Impact | Mitigation |
| :--- | :--- | :--- |
| **Semrush API Limits** | High | Implement request queuing and caching in Chronos to avoid daily limit exhaustion. |
| **SERP Fetcher Blocks** | Medium | Utilize rotating residential proxies and random user-agent strings for the OpenSource fetcher. |
| **Entity Hallucination** | Medium | Cross-verify LLM entities against Wikipedia/DBpedia hard data. |
| **GSC Provision Delay** | Low | Build a "mock" GSC data layer so the engine can be tested before API access is granted. |
| **WP API Failures** | Medium | Implement local SQLite staging before pushing to production WordPress. |
