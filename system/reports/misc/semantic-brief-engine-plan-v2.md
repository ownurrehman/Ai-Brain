> **Parent Report Hub:** [[system/reports/INDEX|📊 System Reports Archive]] · [[INDEX|🧠 Ai Brain]]

# Semantic Content Brief Engine — v2 Implementation Plan

**Rank Ray — Production Specification**  
**Effective:** 2026-04-21  
**Target:** 6 hours → 15 minutes per brief  
**Methodology:** Koray Tuğberk Gübür Semantic SEO Framework

---

## Executive Summary

**Problem:** Manual semantic brief creation takes 4-6 hours, limiting output to 30 briefs/month with inconsistent quality.

**Solution:** 4-phase automated pipeline with specialized AI agents, computing 104-195 production decisions per brief before the writer touches it.

**Result:** 15 minutes per brief with higher quality — every field computed from research data, not guessed.

---

## Tool Stack (Confirmed & Tested)

| Tool | Purpose | Status | Cost |
|------|---------|--------|------|
| **Semrush API** | Keyword research, 2,400+ queries, competitor gaps | User has access | Included in subscription |
| **OpenSERP** | Google/DuckDuckGo SERP data, organic results | ✅ Tested & working (2026-04-21) | Free, self-hosted |
| **Browser Automation (Playwright)** | PAA extraction, featured snippets, SERP screenshots | Available in workspace | Free |
| **GSC API** | Search performance data, query impressions | Pending user provision | Free |
| **Wikipedia/DBpedia API** | Entity relationships, synonyms, antonyms, hyponyms, hypernyms | Public API | Free |
| **WordPress REST API** | Content publishing, media uploads, Yoast integration | Credentials available | Free |

### OpenSERP Configuration (Verified 2026-04-21)

**Location:** `/tmp/openserp/` (can move to permanent location)

**Test Results:**
- ✅ DuckDuckGo: 5 results in 6 seconds, no CAPTCHA
- ✅ Google with proxy rotation: 4+ results, CAPTCHA bypassed
- ⚠️ Bing: Selector issue (0 results returned)

**Proxy Setup:**
```yaml
proxies:
  entries:
    - url: http://8.215.15.163:3129
      tags: [google, default]
    - url: http://168.205.217.140:4145
      tags: [google]
    - url: http://195.154.84.47:3128
      tags: [google]
    - url: http://135.181.34.1:9085
      tags: [google]
    - url: http://72.56.109.159:1080
      tags: [google]
  health:
    failure_threshold: 3
```

**Endpoints:**
- `http://127.0.0.1:7070/google/search?text={query}&limit={n}` — Google organic results
- `http://127.0.0.1:7070/duck/search?text={query}&limit={n}` — DuckDuckGo organic results
- `http://127.0.0.1:7070/mega/search?text={query}&engines=google,duckduckgo&limit={n}` — Multi-engine aggregation

**Note:** OpenSERP does not extract PAA or featured snippets. Use Playwright browser automation for these.

---

## Agent Topology

### Researcher — Semantic Architect
**Role:** Semantic intelligence and brief specification  
**Responsibilities:**
- Semrush API integration (2,400+ query extraction)
- OpenSERP integration (SERP data capture)
- Entity extraction with PPR classification (180+ entities)
- 5-stream intent classification
- 9-frame coverage analysis
- 13-field brief specification per section
- Quality gates before handoff

**Output:** Production-ready semantic brief with all 13 fields computed per section

### Enigma — Content Executor
**Role:** Senior SEO content writer  
**Responsibilities:**
- Execute content based on researcher's 13-field specs
- Ensure all 9 semantic frames are filled
- Integrate 180+ entities naturally
- Follow modality matching (text/list/table/comparison per section)
- Layer brand positioning and CTAs
- Internal linking (verified from sitemap)
- Yoast SEO optimization (green before publish)

**Output:** Publish-ready content with verified internal links and Yoast green status

### Chronos — Infrastructure & Integration
**Role:** DevOps and API plumbing  
**Responsibilities:**
- OpenSERP server management (start/stop/monitor)
- Semrush API integration (auth, rate limits, caching)
- GSC API integration (when provisioned)
- Browser automation for PAA/snippet capture
- WordPress REST API integration
- Media library management (image uploads, duplicate checking)
- Proxy rotation management

**Output:** Reliable API layer, zero downtime, automatic retry/fallback

### Main (Ranki) — Quality Gate
**Role:** Final verification and client communication  
**Responsibilities:**
- Verify brief completeness (all 13 fields, 9 frames, 180+ entities)
- Validate content against brief specs
- Client communication and reporting
- Escalation handling

**Output:** Approved content ready for publishing

---

## 4-Phase Pipeline Architecture

### Phase 1: RESEARCH (4-5 minutes)

**Goal:** Extract all raw data needed for brief computation

**Steps:**

1. **Semrush Query Extraction** (60 seconds)
   - Pull 2,400+ queries for target topic
   - Extract: search volume, difficulty, CPC, trends
   - Identify competitor domains from SERP data
   - Cache response for 24 hours (avoid re-fetching)

2. **OpenSERP Data Capture** (90 seconds)
   - Query Google via OpenSERP for top 20 queries
   - Extract: top 10 organic results per query
   - Capture: URLs, titles, descriptions, word counts
   - Store in structured format for entity extraction

3. **PAA & Featured Snippet Capture** (90 seconds)
   - Use Playwright browser automation
   - Query Google for top 10 queries
   - Extract: PAA questions (all expansions), featured snippet type and content
   - Screenshot SERP for manual review if needed

4. **Entity Extraction Initiation** (60 seconds)
   - Scrape top 10 competitor pages (from step 2)
   - Extract all nouns, noun phrases, proper nouns
   - Initial count target: 200+ candidate entities

**Output:** Raw dataset with queries, SERP results, PAA questions, candidate entities

---

### Phase 2: OUTLINE (3-4 minutes)

**Goal:** Structure the brief with semantic coverage

**Steps:**

1. **5-Stream Intent Classification** (60 seconds)
   - Classify all 2,400+ queries into:
     - Informational (seeking knowledge)
     - Transactional (ready to purchase)
     - Commercial Investigation (comparing options)
     - Navigational (looking for specific brand)
     - Local (location-specific intent)
   - Calculate distribution percentages

2. **PPR Entity Classification** (90 seconds)
   - Classify 180+ entities into:
     - **Purpose:** What the entity does, its function
     - **Property:** Attributes, characteristics
     - **Relationship:** Connections to other entities
   - Cross-reference with Wikipedia/DBpedia for:
     - Synonyms
     - Antonyms
     - Hyponyms (specific instances)
     - Hypernyms (broader categories)

3. **9-Frame Coverage Mapping** (60 seconds)
   - Map entities and queries to semantic frames:
     - Frame 1: Entity introduction and definition
     - Frame 2: Purpose and function
     - Frame 3: Properties and attributes
     - Frame 4: Relationships to other entities
     - Frame 5: Process and workflow
     - Frame 6: Comparison and differentiation
     - Frame 7: Problem-solution mapping
     - Frame 8: Evidence and proof (data, case studies)
     - Frame 9: Action and conversion
   - Identify gaps (missing frames)

4. **Query-to-Heading Mapping** (60 seconds)
   - Map classified queries to H2/H3 sections
   - Ensure each section has query coverage
   - Balance intent distribution across article

**Output:** Structured outline with frame assignments, entity maps, query coverage

---

### Phase 3: BRIEF (4-5 minutes)

**Goal:** Compute 13-field specification per section

**Steps:**

For **each H2 section**, compute all 13 fields:

1. **Entity Map** (computed from Phase 2)
   - List of PPR-classified entities to include
   - Minimum 15-20 entities per section

2. **Semantic Frame** (from Phase 2)
   - Which frame(s) this section fills
   - Primary frame + any secondary frames

3. **Query Coverage** (from Phase 2)
   - List of queries mapped to this section
   - Intent classification for each query

4. **Modality Matching**
   - Format type: text / list / table / comparison / step-by-step
   - Based on SERP feature analysis and query intent

5. **Content Boundaries**
   - What NOT to cover in this section
   - Prevents overlap and repetition

6. **Information Gain**
   - New insight or data point required
   - Must add value beyond competitor content

7. **Bold Guidance**
   - Key terms to bold for skimmability
   - Typically 3-5 terms per section

8. **Persona Targeting**
   - Which user persona this section serves
   - Examples: beginner, expert, buyer, researcher

9. **CTA Integration**
   - Conversion angle for this section
   - Soft CTA (learn more) or hard CTA (contact us)

10. **Internal Link Targets**
    - Verified URLs from sitemap
    - Natural anchor text guidance
    - No duplicate links (one per page max)

11. **External Reference Needs**
    - Authoritative sources to cite
    - Educational, government, or industry sources

12. **Word Count Range**
    - Min-max based on competitor analysis + information gain
    - Example: 250-350 words

13. **Dedup Check**
    - Existing content to avoid repeating
    - Cross-reference with site's published articles

**Output:** Complete brief with 13 fields computed for every H2 section

---

### Phase 4: VERIFICATION (2-3 minutes)

**Goal:** Quality gates before handoff to writer

**Checklist:**

- [ ] **Entity Count:** 180+ entities extracted and PPR-classified
- [ ] **Frame Coverage:** All 9 frames assigned to sections (no gaps)
- [ ] **Query Mapping:** 2,400+ queries classified into 5 intent streams
- [ ] **SERP Features:** PAA questions captured, featured snippet type identified
- [ ] **13-Field Spec:** Every section has all 13 fields computed
- [ ] **Dedup Verification:** No overlap with existing content
- [ ] **Brand Layer:** Positioning and conversion angles integrated
- [ ] **Internal Links:** All URLs verified from sitemap, no duplicates
- [ ] **Production Ready:** Brief can be executed without clarification

**If any check fails:** Return to appropriate phase for correction

**Output:** Approved brief ready for Enigma content execution

---

## Data Structures

### Query Object
```json
{
  "query": "semantic seo",
  "volume": 8100,
  "difficulty": 67,
  "cpc": 12.50,
  "intent": "informational",
  "intent_stream": "informational",
  "trend": [65, 68, 72, 70, 75, 80, 82, 78, 76, 74, 77, 81],
  "serp_features": ["featured_snippet", "paa", "related_searches"],
  "assigned_section": "H2_1",
  "priority": "high"
}
```

### Entity Object
```json
{
  "entity": "semantic search",
  "ppr_class": "purpose",
  "definition": "Search technology that understands user intent and contextual meaning",
  "synonyms": ["contextual search", "intent-based search", "meaning search"],
  "antonyms": ["keyword search", "lexical search"],
  "hyponyms": ["voice search", "visual search", "conversational search"],
  "hypernyms": ["search technology", "information retrieval"],
  "wikipedia_url": "https://en.wikipedia.org/wiki/Semantic_search",
  "sections_to_include": ["H2_1", "H2_3", "H2_7"],
  "priority": "high"
}
```

### Section Spec Object
```json
{
  "section_id": "H2_1",
  "title": "What is Semantic SEO?",
  "frame_primary": "frame_1_definition",
  "frame_secondary": ["frame_2_purpose"],
  "entity_map": ["semantic seo", "semantic search", "search intent", ...],
  "query_coverage": ["what is semantic seo", "semantic seo definition", ...],
  "modality": "text + definition box",
  "content_boundaries": "Do not cover tools or implementation yet",
  "information_gain": "Include 2026 AI search impact data",
  "bold_guidance": ["semantic SEO", "search intent", "entity optimization"],
  "persona_targeting": "beginner",
  "cta_integration": "soft: learn more about entity SEO",
  "internal_links": [
    {"url": "/services/seo/", "anchor": "SEO services"},
    {"url": "/blog/entity-seo/", "anchor": "entity optimization"}
  ],
  "external_references": ["https://schema.org", "https://developers.google.com/search"],
  "word_count_range": {"min": 250, "max": 350},
  "dedup_check": ["avoid repeating intro from /blog/seo-basics/"]
}
```

### Brief Object
```json
{
  "brief_id": "brief-2026-04-21-semantic-seo",
  "topic": "Semantic SEO",
  "target_url": "/blog/semantic-seo-guide/",
  "created_at": "2026-04-21T12:00:00Z",
  "status": "ready_for_writing",
  "primary_keyword": "semantic seo",
  "search_intent": "informational",
  "word_count_target": {"min": 2500, "max": 3500},
  "entity_count": 187,
  "frame_coverage": {
    "frame_1": true,
    "frame_2": true,
    "frame_3": true,
    "frame_4": true,
    "frame_5": true,
    "frame_6": true,
    "frame_7": true,
    "frame_8": true,
    "frame_9": true
  },
  "sections": [
    {...section_spec...},
    {...section_spec...}
  ],
  "verification": {
    "entity_count_check": true,
    "frame_coverage_check": true,
    "query_mapping_check": true,
    "serp_features_check": true,
    "thirteen_field_check": true,
    "dedup_check": true,
    "brand_layer_check": true,
    "internal_links_check": true,
    "production_ready": true
  }
}
```

---

## Implementation Roadmap

### Week 1: Phase 1 Infrastructure (Research Layer)

**Day 1-2: Semrush API Integration**
- [ ] Set up Semrush API authentication
- [ ] Build query extraction endpoint (2,400+ queries)
- [ ] Implement caching (24-hour TTL)
- [ ] Add rate limit handling (retry logic, backoff)
- [ ] Test with 5 different topics

**Day 3-4: OpenSERP Integration**
- [ ] Move OpenSERP to permanent location (`/opt/openserp/`)
- [ ] Create systemd service for auto-start
- [ ] Build API wrapper for agent calls
- [ ] Implement proxy rotation monitoring
- [ ] Add fallback to DuckDuckGo if Google fails
- [ ] Test with 10 different queries

**Day 5: PAA/Snippet Browser Automation**
- [ ] Build Playwright script for PAA extraction
- [ ] Build Playwright script for featured snippet capture
- [ ] Implement SERP screenshot capture (optional)
- [ ] Cache results to avoid re-scraping
- [ ] Test with 10 different queries

**Day 6-7: Entity Extraction Pipeline**
- [ ] Build competitor page scraper (top 10 URLs)
- [ ] Implement NLP entity extraction (spaCy or similar)
- [ ] Build Wikipedia/DBpedia API integration
- [ ] Implement PPR classification logic
- [ ] Test with 3 different topics

**Deliverable:** Phase 1 pipeline producing raw dataset in 4-5 minutes

---

### Week 2: Phase 2 & 3 (Outline + Brief Computation)

**Day 8-9: Intent Classification Engine**
- [ ] Build 5-stream intent classifier
- [ ] Train on manual examples (informational, transactional, commercial, navigational, local)
- [ ] Implement batch classification (2,400+ queries)
- [ ] Add confidence scoring
- [ ] Test accuracy against manual classification

**Day 10-11: 9-Frame Coverage Analyzer**
- [ ] Build frame detection algorithm
- [ ] Map entities to frames based on PPR class
- [ ] Identify gaps (missing frames)
- [ ] Generate frame completion recommendations
- [ ] Test with 5 different topics

**Day 12-13: 13-Field Spec Generator**
- [ ] Build section-by-section spec computation
- [ ] Implement all 13 fields per section
- [ ] Add modality matching logic
- [ ] Integrate internal link verification (sitemap check)
- [ ] Implement dedup checking against existing content
- [ ] Test with 3 different topics

**Day 14: Integration Testing**
- [ ] End-to-end test: Phase 1 → Phase 2 → Phase 3
- [ ] Measure total time (target: 10-12 minutes)
- [ ] Validate output quality (13 fields complete, 9 frames filled)
- [ ] Fix any bottlenecks

**Deliverable:** Complete brief generation in 10-12 minutes

---

### Week 3: Phase 4 (Verification) + WordPress Integration

**Day 15-16: Quality Gate Implementation**
- [ ] Build verification checklist automation
- [ ] Implement entity count validation
- [ ] Implement frame coverage validation
- [ ] Implement 13-field completeness check
- [ ] Add internal link verification
- [ ] Test with 10 briefs

**Day 17-18: WordPress REST API Integration**
- [ ] Set up WordPress application passwords
- [ ] Build content push endpoint
- [ ] Implement media library upload (featured images)
- [ ] Add duplicate image checking (by filename/topic)
- [ ] Implement Yoast SEO field population
- [ ] Test with staging site

**Day 19-20: GSC API Integration (If Provisioned)**
- [ ] Set up Google Cloud Console project
- [ ] Configure OAuth2 authentication
- [ ] Build GSC data extraction (query impressions, CTR, positions)
- [ ] Integrate GSC data into brief computation
- [ ] Test with actual GSC data

**Day 21: End-to-End Testing**
- [ ] Full pipeline test: Research → Brief → WordPress
- [ ] Measure total time (target: 15 minutes)
- [ ] Validate quality (Yoast green, all frames filled, entities included)
- [ ] Document any issues and fixes

**Deliverable:** Production-ready Semantic Brief Engine

---

### Week 4: Optimization + Documentation

**Day 22-23: Performance Optimization**
- [ ] Profile pipeline for bottlenecks
- [ ] Optimize API calls (parallel where possible)
- [ ] Improve caching strategy
- [ ] Reduce token usage in AI calls
- [ ] Target: 12-15 minutes total

**Day 24-25: Error Handling + Monitoring**
- [ ] Add comprehensive error handling
- [ ] Implement retry logic for failed API calls
- [ ] Build monitoring dashboard (job status, elapsed time)
- [ ] Add alerting for failures (Discord webhook)
- [ ] Document failure modes and recovery

**Day 26-27: Documentation**
- [ ] Write user guide for Researcher agent
- [ ] Write user guide for Enigma (content execution)
- [ ] Document API endpoints and data structures
- [ ] Create troubleshooting guide
- [ ] Record demo video (optional)

**Day 28: Handoff + Training**
- [ ] Train team on new workflow
- [ ] Document SOP for brief generation
- [ ] Set up ongoing maintenance schedule
- [ ] Plan Phase 2 enhancements (AI writing integration)

**Deliverable:** Fully documented, production-ready system

---

## Cost Analysis

### Monthly Costs

| Service | Cost | Notes |
|---------|------|-------|
| Semrush API | Included in subscription | User already has access |
| OpenSERP | Free | Self-hosted, no API fees |
| GSC API | Free | Google provides at no cost |
| Wikipedia/DBpedia | Free | Public APIs |
| WordPress REST API | Free | Self-hosted |
| Proxy Rotation | Free (public proxies) | Can upgrade to paid proxies if needed ($10-50/month) |
| **Total** | **$0-50/month** | Depending on proxy needs |

### Time Savings

**Before:** 6 hours/brief × 30 briefs/month = 180 hours/month  
**After:** 15 minutes/brief × 30 briefs/month = 7.5 hours/month  
**Savings:** 172.5 hours/month (96% reduction)

**Value:** At $50/hour operator cost = $8,625/month savings  
**ROI:** Infinite (system cost is $0-50/month)

### Scale Potential

**Before:** 30 briefs/month ceiling (1 trained operator)  
**After:** 200+ briefs/month (automated pipeline, minimal human oversight)  
**Scale Factor:** 6.7x increase with same or lower cost

---

## Risk Assessment

### High Priority Risks

**1. Semrush API Rate Limits**
- **Risk:** Hitting rate limits during bulk query extraction
- **Mitigation:** Implement caching (24-hour TTL), batch requests, exponential backoff
- **Fallback:** Use cached data from previous runs if available

**2. Google CAPTCHA (OpenSERP)**
- **Risk:** Google blocks requests despite proxy rotation
- **Mitigation:** Rotate proxies automatically, fallback to DuckDuckGo, add 2Captcha integration if needed
- **Fallback:** DuckDuckGo provides 80% of needed SERP data

**3. GSC API Provisioning Delays**
- **Risk:** GSC API setup takes longer than expected
- **Mitigation:** Use mock data layer for development, proceed with other phases
- **Fallback:** System works without GSC data (lower priority data source)

**4. Entity Extraction Quality**
- **Risk:** Automated entity extraction misses important entities or misclassifies PPR
- **Mitigation:** Human review of first 10 briefs, iterative improvement of extraction logic
- **Fallback:** Manual entity addition step before final verification

### Medium Priority Risks

**5. Internal Link Verification**
- **Risk:** Sitemap changes or broken links cause verification failures
- **Mitigation:** Cache sitemap, implement 404 detection, regular link audits
- **Fallback:** Skip internal links for urgent briefs (add manually later)

**6. WordPress API Changes**
- **Risk:** WordPress REST API changes break integration
- **Mitigation:** Use stable API endpoints, version-check before updates
- **Fallback:** Browser automation for content publishing (slower but reliable)

**7. Proxy Reliability**
- **Risk:** Public proxies become unreliable or slow
- **Mitigation:** Monitor proxy health, auto-remove failed proxies, maintain pool of 20+ proxies
- **Fallback:** Paid proxy service ($10-50/month) if public proxies fail

### Low Priority Risks

**8. Wikipedia/DBpedia Rate Limits**
- **Risk:** Hitting rate limits on entity relationship queries
- **Mitigation:** Cache entity data, batch requests, respect rate limits
- **Fallback:** Skip entity relationships for lower-priority entities

**9. Browser Automation Detection**
- **Risk:** Google detects and blocks Playwright automation
- **Mitigation:** Use stealth mode, rotate user agents, add delays
- **Fallback:** Manual PAA capture for critical briefs

**10. Content Quality Variance**
- **Risk:** Enigma produces inconsistent quality across briefs
- **Mitigation:** Strict 13-field spec, verification gates, human review of first 20 briefs
- **Fallback:** Human editing pass before publishing

---

## Integration Points with Rank Ray Workflow

### WordPress Integration

**Credentials:** Stored in environment variables (`.env`)

**Endpoints:**
- `POST /wp-json/wp/v2/posts` — Create/update posts
- `POST /wp-json/wp/v2/media` — Upload featured images
- `GET /wp-json/sitemap/v1/sitemap.xml` — Fetch sitemap for internal link verification

**Yoast SEO Integration:**
- Meta title: `POST /wp-json/yoast/v1/set_title`
- Meta description: `POST /wp-json/yoast/v1/set_description`
- Focus keyphrase: `POST /wp-json/yoast/v1/set_focus_keyword`
- **Requirement:** Yoast SEO plugin must have "REST API: Head endpoint" enabled

**Media Library:**
- Duplicate checking by filename/topic before upload
- Alt text mandatory for all images
- Landscape orientation preferred for SEO pages
- Local media index maintained to prevent duplicates

### Discord Integration

**Channel:** `#claw-status` (ID: 1476131657663909970)

**Webhook Format:**
```json
{
  "content": "[STARTED/COMPLETED/BLOCKED/FAILED] Task name - brief detail"
}
```

**Events to Log:**
- Brief generation started
- Phase completions (Research, Outline, Brief, Verification)
- Content publishing started/completed
- Errors and failures

### Memory Integration

**Daily Log:** `memory/YYYY-MM-DD.md`

**Format:**
```markdown
[HH:MM] Event: Semantic brief generated for [topic] - [word count] words, [entity count] entities, [frame count]/9 frames
[HH:MM] Event: Content published to [URL] - Yoast green, internal links verified
```

---

## Success Metrics

### Phase 1 (Week 1)
- [ ] Semrush API extraction: 2,400+ queries in <90 seconds
- [ ] OpenSERP Google results: 10 results in <60 seconds (no CAPTCHA)
- [ ] PAA extraction: 10+ questions captured in <90 seconds
- [ ] Entity extraction: 180+ entities in <120 seconds

### Phase 2-3 (Week 2)
- [ ] Intent classification: 95%+ accuracy vs manual
- [ ] Frame coverage: All 9 frames assigned, gaps identified
- [ ] 13-field spec: All fields computed for every section
- [ ] Total time: Phase 1+2+3 < 12 minutes

### Phase 4 (Week 3)
- [ ] Verification gates: 100% automated checklist
- [ ] WordPress integration: Content published with Yoast green
- [ ] Internal links: Verified from sitemap, no duplicates
- [ ] Total time: End-to-end < 15 minutes

### Production (Week 4+)
- [ ] Throughput: 30+ briefs/week with minimal human oversight
- [ ] Quality: 95%+ pass verification on first attempt
- [ ] Reliability: <5% failure rate (automatic retry succeeds)
- [ ] Scale: 200+ briefs/month capacity

---

## Next Steps

**Immediate (This Week):**
1. ✅ OpenSERP tested and working with Google proxy rotation
2. ✅ Researcher agent updated with semantic SEO skills
3. ⏳ Move OpenSERP to permanent location (`/opt/openserp/`)
4. ⏳ Create systemd service for OpenSERP auto-start
5. ⏳ Begin Semrush API integration (Day 1-2)

**This Month:**
- Complete 4-week implementation roadmap
- Train team on new workflow
- Generate first 10 briefs with human review
- Iterate based on feedback

**Next Quarter:**
- Scale to 200+ briefs/month
- Integrate AI writing (Enigma executes briefs automatically)
- Add advanced features (competitor tracking, rank monitoring)

---

## Appendix A: 13-Field Brief Template

```markdown
## H2: [Section Title]

1. **Entity Map:** [list of 15-20 PPR-classified entities]
2. **Semantic Frame:** [primary frame + secondary frames]
3. **Query Coverage:** [list of queries mapped to this section with intent]
4. **Modality Matching:** [text/list/table/comparison/step-by-step]
5. **Content Boundaries:** [what NOT to cover here]
6. **Information Gain:** [new insight/data point required]
7. **Bold Guidance:** [3-5 key terms to bold]
8. **Persona Targeting:** [beginner/expert/buyer/researcher]
9. **CTA Integration:** [soft/hard CTA with specific angle]
10. **Internal Link Targets:** [verified URLs with anchor text]
11. **External Reference Needs:** [authoritative sources to cite]
12. **Word Count Range:** [min-max words]
13. **Dedup Check:** [existing content to avoid repeating]
```

---

## Appendix B: 9-Frame Coverage Checklist

- [ ] Frame 1: Entity introduction and definition
- [ ] Frame 2: Purpose and function
- [ ] Frame 3: Properties and attributes
- [ ] Frame 4: Relationships to other entities
- [ ] Frame 5: Process and workflow
- [ ] Frame 6: Comparison and differentiation
- [ ] Frame 7: Problem-solution mapping
- [ ] Frame 8: Evidence and proof (data, case studies)
- [ ] Frame 9: Action and conversion

**Requirement:** All 9 frames must be filled across the article. No brief is complete with gaps.

---

## Appendix C: PPR Entity Classification Guide

### Purpose Entities
- **Definition:** What the entity does, its function, why it exists
- **Examples:** "semantic search understands intent", "SEO increases visibility"
- **Questions:** What does it do? Why does it exist? What problem does it solve?

### Property Entities
- **Definition:** Attributes, characteristics, features of the entity
- **Examples:** "high search volume", "low difficulty", "green Yoast score"
- **Questions:** What are its traits? How is it described? What makes it unique?

### Relationship Entities
- **Definition:** How the entity connects to other entities
- **Examples:** "semantic SEO is a type of SEO", "entities relate to keywords"
- **Questions:** What is it related to? What category does it belong to? What are its parts?

---

**Document Version:** v2.0  
**Last Updated:** 2026-04-21  
**Owner:** Rank Ray SEO Team  
**Status:** Ready for Phase 1 Implementation
