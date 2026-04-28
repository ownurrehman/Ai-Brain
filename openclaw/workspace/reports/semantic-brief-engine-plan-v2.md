# Implementation Plan: Semantic Content Brief Engine v2

## 1. Executive Summary
The Semantic Content Brief Engine is a production-grade automation system designed to replicate and scale the semantic SEO methodology of Koray Tuğberk Gübür. The goal is to transition a 6-hour manual brief process into a 15-minute automated pipeline, increasing the volume of high-quality briefs from 30/month to unlimited, while maintaining a rigorous data-driven approach to entity coverage and semantic framing.

**Core Objective:** Automate the generation of a 13-field technical specification per section, ensuring 100% coverage of the 9 semantic frames and 180+ critical entities per topic.

---

## 2. System Architecture & Data Flow

### 2.1 Agent Topology
The engine utilizes the existing Rank Ray agent structure, redistributing responsibilities to optimize for semantic precision:

| Agent | Role | Primary Responsibility in Engine |
| :--- | :--- | :--- |
| **Main (Ranki)** | Orchestrator / QA | Triggering the pipeline, managing state transitions, and performing the final Quality Gate verification. |
| **Researcher** | Semantic Architect | Keyword classification, entity extraction (PPR), SERP analysis, semantic frame mapping, and brief construction. |
| **Chronos** | API & Infrastructure | Managing Semrush, OpenSERP, and GSC API calls; handling Wikipedia/DBpedia graph fetches; WordPress REST API integration. |
| **Enigma** | Content Executor | Transforming the 13-field brief specs into high-performance semantic content. |

### 2.2 Data Flow Pipeline
1. **Trigger:** `Main` initiates a request for a topic/seed keyword.
2. **Extraction (Chronos $\rightarrow$ Researcher):** 
   - `Chronos` fetches 2,400+ queries via Semrush API and SERP data (snippets, PAA, Related) via OpenSERP.
   - `Chronos` fetches raw HTML/Markdown of top 10 competitors.
3. **Analysis (Researcher):**
   - Classifies queries into 5 intent streams.
   - Extracts entities $\rightarrow$ Classifies by Purpose, Property, Relationship (PPR).
   - Expands entities via Wikipedia/DBpedia.
   - Maps queries to headings and validates against 9 semantic frames.
4. **Brief Construction (Researcher):**
   - Generates the 13-field specification per section.
5. **Verification (Main):**
   - Validates the brief against quality gates (Entity count $\ge$ 180, Frames covered = 9/9).
6. **Execution (Enigma):**
   - Writes content based on the strict spec.
7. **Deployment (Chronos):**
   - Pushes content to WordPress via REST API, including Yoast SEO metadata.

---

## 3. API Integration Specifications

### 3.1 Semrush API (Keyword Intelligence)
- **Endpoints:** `keyword_analytics`, `phrase_keyword_analysis`.
- **Data Extraction:** Pulling full keyword clusters (2,400+ queries), search volume, keyword difficulty, and existing position.
- **Output:** CSV/JSON dataset of the semantic neighborhood.

### 3.2 OpenSERP (SERP Intelligence)
- **Deployment:** Self-hosted Go-based instance.
- **Data Extraction:** 
  - Organic result snippets (to identify "Information Gain" gaps).
  - "People Also Ask" (PAA) for query-to-heading mapping.
  - Related Searches for LSI expansion.
- **Interface:** REST API calls providing raw JSON of current Google SERP layout.

### 3.3 GSC API (Performance Layer)
- **Mock Layer:** Until API is provisioned, use a JSON mock dataset containing `query`, `impressions`, `clicks`, and `ctr` to simulate internal performance cross-referencing.
- **Final State:** Integration via Google Cloud Console to prioritize "striking distance" keywords in the brief.

### 3.4 Wikipedia/DBpedia (Knowledge Graph)
- **Method:** SPARQL queries to DBpedia and API calls to Wikipedia.
- **Extraction:** 
  - **Hypernyms:** Broader categories (Parent).
  - **Hyponyms:** Specific examples (Child).
  - **Synonyms/Antonyms:** Semantic equivalents/opposites.
  - **Properties:** Attributes of the entity (PPR classification).

---

## 4. Data Structures

### 4.1 Entity PPR Model
Entities are stored as objects to ensure semantic precision:
```json
{
  "entity": "Semantic SEO",
  "classification": {
    "purpose": "Methodology",
    "property": "Search Engine Optimization",
    "relationship": "Subset of Content Marketing"
  },
  "graph": {
    "hypernym": "Digital Marketing",
    "hyponyms": ["Entity SEO", "Topic Clusters", "Semantic Framing"],
    "synonyms": ["Contextual Optimization", "Knowledge Graph SEO"]
  }
}
```

### 4.2 The 13-Field Section Spec
Each section of the brief follows this rigid structure:
1. **Entity Map:** List of PPR-classified entities to be mentioned.
2. **Semantic Frame:** Assignment (e.g., "Comparative Frame", "Procedural Frame").
3. **Query Coverage:** Specific queries from the 2,400+ set to be answered here.
4. **Modality:** Format (Table, Bullet List, Comparison Grid, Paragraph).
5. **Boundaries:** Explicit "Do Not Cover" topics to avoid overlap.
6. **Information Gain:** Specific unique insight or data point to add (based on SERP gaps).
7. **Bold Guidance:** Key semantic terms to emphasize.
8. **Persona:** Target reader segment for this section.
9. **CTA:** Specific conversion angle for this section.
10. **Internal Links:** Target URLs from the sitemap.
11. **External References:** Required high-authority citations.
12. **Word Count:** Min/Max range.
13. **Dedup Check:** Existing internal URLs that cover this (to avoid cannibalization).

---

## 5. Implementation Roadmap

| Phase | Focus | Key Milestones | Est. Time |
| :--- | :--- | :--- | :--- |
| **Phase 1: Research** | Data Acquisition | Semrush $\rightarrow$ OpenSERP $\rightarrow$ DBpedia pipeline. Entity extraction engine. | 2 Weeks |
| **Phase 2: Outline** | Semantic Mapping | Intent classification $\rightarrow$ Heading mapping $\rightarrow$ Frame gap analysis. | 1 Week |
| **Phase 3: Brief** | Spec Generation | 13-field template automation and prompt engineering for Researcher. | 1 Week |
| **Phase 4: Verify** | Quality Gates | Main agent's validation logic and WP REST API integration. | 1 Week |

---

## 6. WordPress Integration Points

- **Content Push:** `POST /wp-json/wp/v2/posts` with HTML content.
- **SEO Metadata:** Integration with Yoast SEO REST API endpoints to set `yoast_wpseo_title` and `yoast_wpseo_metadesc`.
- **Media Library:** `POST /wp-json/wp/v2/media` for automated image placement based on modality.
- **Sitemap Sync:** `GET` sitemap to populate "Internal Link Targets" in the 13-field spec.

---

## 7. Cost & Risk Analysis

### 7.1 Cost Analysis
- **Semrush:** Existing Subscription (User provided).
- **OpenSERP:** Free (Self-hosted).
- **GSC API:** Free.
- **Wikipedia/DBpedia:** Free.
- **Compute:** LLM Tokens (Researcher/Enigma/Main) - Variable based on volume.

### 7.2 Risk Assessment
| Risk | Impact | Mitigation |
| :--- | :--- | :--- |
| **Rate Limits** | High | Implement exponential backoff in `Chronos` API wrapper. |
| **IP Blocks** | Medium | OpenSERP uses rotating proxies/headless browsers to avoid Google blocks. |
| **Data Quality** | High | `Main` agent performs a "Hard Gate" check; if entity count $<180$, brief is sent back to `Researcher`. |
| **Hallucinations**| Medium | Ground all entity claims in DBpedia/Wikipedia sources; no "guessing" of semantic relations. |
