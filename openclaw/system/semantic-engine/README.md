# Semantic Content Brief Engine — Phase 1 Implementation

**Rank Ray — Production System**  
**Version:** 2.0.0  
**Status:** Phase 1 Implementation (Research Layer)

---

## Quick Start

### Prerequisites

1. **OpenSERP** — Already installed and running at `http://127.0.0.1:7070`
   - Location: `~/openserp/`
   - Auto-starts on login via launchd
   - Status: ✅ Tested and working

2. **Python 3.10+** — Check with `python3 --version`

3. **Playwright** — For PAA extraction
   ```bash
   pip install playwright
   playwright install
   ```

4. **Semrush API Key** — Get from https://www.semrush.com/api/
   - Add to environment: `export SEMRUSH_API_KEY="your_key_here"`

### Directory Structure

```
semantic-engine/
├── config/
│   └── settings.json          # Engine configuration
├── scripts/
│   ├── semrush-extractor.py   # Semrush API integration
│   ├── openserp-fetcher.py    # OpenSERP API integration
│   ├── paa-extractor.py       # PAA extraction (Playwright)
│   └── run-phase1.py          # Phase 1 orchestrator
├── cache/                      # API response cache
├── logs/                       # Execution logs
├── screenshots/                # SERP screenshots (debugging)
└── reports/                    # Phase output reports
```

---

## Phase 1: Research Layer

### What It Does

Phase 1 extracts all raw data needed for semantic brief computation:

1. **Semrush Query Extraction** — 2,400+ queries with volume, difficulty, CPC, trends
2. **OpenSERP SERP Capture** — Top 10 organic results for top 20 queries
3. **PAA Extraction** — People Also Ask questions for top 10 queries
4. **Entity Extraction** — Candidate entities from SERP titles/descriptions

**Target Duration:** 4-5 minutes  
**Output:** Raw dataset in JSON format

### How to Run

```bash
# Navigate to semantic-engine directory
cd /Users/sheikhown/.openclaw/workspace/semantic-engine

# Set Semrush API key (if you have it)
export SEMRUSH_API_KEY="your_semrush_api_key"

# Run Phase 1 for a topic
python3 scripts/run-phase1.py "semantic seo"
```

### Expected Output

```
[2026-04-21 12:55:00] === PHASE 1: RESEARCH ===
[2026-04-21 12:55:00] Topic: semantic seo
[2026-04-21 12:55:00] Step 1: Extracting queries from Semrush API...
[2026-04-21 12:55:02] ✓ Semrush extraction complete: 20 queries in 2.1s
[2026-04-21 12:55:00] Step 2: Capturing SERP data from OpenSERP...
[2026-04-21 12:55:15] ✓ SERP capture complete: 20 queries in 13.4s
[2026-04-21 12:55:15] Step 3: Extracting PAA questions...
[2026-04-21 12:55:30] ✓ PAA extraction complete: 47 questions in 15.2s
[2026-04-21 12:55:30] Step 4: Initiating entity extraction...
[2026-04-21 12:55:32] ✓ Entity extraction complete: 156 candidate entities in 1.8s
[2026-04-21 12:55:32] === PHASE 1 COMPLETE ===
[2026-04-21 12:55:32] Total duration: 32.4s (target: 240-300s)
[2026-04-21 12:55:32] Queries extracted: 20
[2026-04-21 12:55:32] SERP analyses: 20
[2026-04-21 12:55:32] PAA questions: 47
[2026-04-21 12:55:32] Candidate entities: 156
[2026-04-21 12:55:32] Phase 1 report saved: reports/phase1-semantic-seo-20260421-125532.json

✅ Phase 1 Complete in 32.4s
📄 Report: reports/phase1-semantic-seo-20260421-125532.json
```

### Output Report Structure

```json
{
  "topic": "semantic seo",
  "started_at": "2026-04-21T12:55:00",
  "phase": "research",
  "steps": {
    "semrush_extraction": {
      "status": "completed",
      "duration_seconds": 2.1,
      "query_count": 20,
      "data": { ... }
    },
    "serp_capture": {
      "status": "completed",
      "duration_seconds": 13.4,
      "queries_analyzed": 20,
      "data": { ... }
    },
    "paa_extraction": {
      "status": "completed",
      "duration_seconds": 15.2,
      "queries_analyzed": 10,
      "total_paa_questions": 47,
      "data": { ... }
    },
    "entity_extraction": {
      "status": "completed",
      "duration_seconds": 1.8,
      "candidate_entity_count": 156,
      "entities": [...]
    }
  },
  "completed_at": "2026-04-21T12:55:32",
  "total_duration_seconds": 32.4
}
```

---

## Individual Scripts

### Semrush Extractor

```bash
python3 scripts/semrush-extractor.py "semantic seo" [api_key]
```

**Output:** JSON with 2,400+ queries (mock data until real API key provided)

**Note:** Currently uses mock data structure. Replace with actual Semrush API calls when you provide API key.

---

### OpenSERP Fetcher

```bash
# Single engine
python3 scripts/openserp-fetcher.py "semantic seo" google 10

# Multi-engine (Google + DuckDuckGo)
python3 scripts/openserp-fetcher.py "semantic seo" multi 10
```

**Output:** JSON with organic SERP results

---

### PAA Extractor

```bash
# Headless mode (default)
python3 scripts/paa-extractor.py "semantic seo"

# Headful mode (for debugging)
python3 scripts/paa-extractor.py "semantic seo" --headful
```

**Output:** JSON with PAA questions + screenshot path

**Note:** Requires Playwright installation:
```bash
pip install playwright
playwright install
```

---

## Configuration

Edit `config/settings.json` to customize:

- OpenSERP base URL and timeout
- Semrush API settings and rate limits
- PAA extraction method and cache TTL
- Entity extraction targets
- WordPress integration settings
- Discord webhook for logging

---

## Caching

All API responses are cached to avoid re-fetching:

- **Semrush:** 24-hour TTL
- **OpenSERP:** 24-hour TTL
- **PAA:** 7-day TTL (changes less frequently)

Cache location: `cache/` directory

To clear cache:
```bash
rm -rf cache/*
```

---

## Logging

All execution logs are saved to `logs/` directory:

- `semrush-YYYY-MM-DD.log` — Semrush API calls
- `openserp-YYYY-MM-DD.log` — OpenSERP fetches
- `paa-YYYY-MM-DD.log` — PAA extraction logs
- `orchestrator-YYYY-MM-DD.log` — Phase 1 orchestration

---

## Troubleshooting

### OpenSERP Not Running

```bash
# Check status
curl http://127.0.0.1:7070/health

# Restart service
launchctl unload ~/Library/LaunchAgents/com.openserp.server.plist
launchctl load ~/Library/LaunchAgents/com.openserp.server.plist
```

### Playwright Errors

```bash
# Reinstall Playwright
pip uninstall playwright
pip install playwright
playwright install
```

### Semrush API Errors

- Check API key is valid: `echo $SEMRUSH_API_KEY`
- Check rate limits in your Semrush plan
- Use cached data if available (24-hour TTL)

---

## Next Steps

**After Phase 1 completes successfully:**

1. Review the output report in `reports/`
2. Verify query count, SERP data, PAA questions, and entities
3. Proceed to Phase 2 implementation (Outline + Brief computation)

**Phase 2 will:**
- Classify queries into 5 intent streams
- Perform PPR entity classification
- Map 9-frame coverage
- Generate 13-field spec per section

---

## Status

**Phase 1 Implementation:**
- ✅ OpenSERP installed and running
- ✅ Semrush extractor script (mock data until API key provided)
- ✅ OpenSERP fetcher script (tested and working)
- ✅ PAA extractor script (requires Playwright installation)
- ✅ Phase 1 orchestrator script
- ✅ Configuration file
- ⏳ Testing with real topic

**Ready for testing.** Run `python3 scripts/run-phase1.py "your topic"` to begin.
