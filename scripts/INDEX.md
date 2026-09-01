# 🛠️ Scripts & Automation Hub

> **Parent Hub:** [[INDEX|🧠 Master Ai Brain Hub]] · [[system/INDEX|⚙️ System Infrastructure Hub]]
> **Legacy Archive:** [[scripts/_archived-scripts/INDEX|📦 Legacy Systems & Scripts Archive]]

---

## 🚀 Active Python & Shell Automation Tooling

### 1. 📊 Google Sheets & Lead Enrichment Pipeline
- `enrich_missing_emails.py` / `v2` / `v3` / `v4` / `v5` — Incremental lead email discovery & validation
- `enrich_missing_emails_firecrawl.py` / `_seq.py` — Firecrawl deep scraping for contact info
- `enrich_contact_details.py` / `_v2.py` — Contact phone, role, and domain enrichment
- `clean_rankray_sheet.py` / `restore_valid_emails.py` — Sheet cleanup and sanitation
- `inspect_rankray_sheet.py` / `inspect_rankray_sheet_sa.py` — Service Account sheet inspector
- `analyze_rankray_sheet.py` / `_v2.py` — Lead analytics & segment categorization
- `verify_all_columns.py` / `final_fill_all_columns.py` / `fill_remaining_fields.py` — Column completeness verification
- `backup_rankray_sheet.py` — Automated local JSON/CSV backup generator
- `remove_unemailable_leads.py` — Bounced / non-routable email filtering

### 2. 🔑 OAuth, MCP & Infrastructure Services
- `google-oauth-manager.py` / `google_reauth_full_scopes.py` — Google OAuth 2.0 token management
- `fix_google_token.py` — Refresh token renewal & scope fixer
- `setup-ga-mcp-adc.py` — Google Analytics MCP ADC credential setup
- `ai-runtime-check.sh` / `ai-runtime-sync.sh` — Agent runtime environment sync
- `docker-cleanup.sh` — Docker volume & container garbage collector
- `mac-health-check.sh` — Host machine performance and process diagnostic
- `mac-process-cleanup.py` — Automated RAM & orphaned process garbage collector (runs every 6h)

### 3. 🤖 Agent Ledger & Content Validation
- `agent-ledger.py` — Agent transaction & task audit logging
- `subagent-manager.py` — Subagent spawn, monitor, and state tracker
- `content-pre-push-validator.py` — Markdown content quality & link gatekeeper
- `log-step.sh` — Autonomous agent step logger

---

## 📦 Legacy Archive
For historic configurations, prompt collections, and legacy memory dumps, visit:
- [[scripts/_archived-scripts/INDEX|📦 Legacy Systems & Scripts Archive]]
