# Legendary Bot v4 — Strategy Lab

Local repo: `/Users/sheikhown/Ai Works - Local/Ai Codes/Legendary Bot`

## Current State

- **Version:** v4.0 (full rebuild)
- **Phase 1 (Foundation):** COMPLETE
- **Phase 2 (Strategy Engine + Backtesting):** COMPLETE
- **Phase 3 (Paper Trading + Live Data):** NEXT
- **Priority:** CRITICAL

## Stack

- Python 3.12+ / FastAPI / Celery / Redis
- Next.js 15 / React 19 / TailwindCSS v4 / shadcn/ui
- PostgreSQL + TimescaleDB
- Raw WebSocket for exchange data

## Architecture

```
Dashboard (Next.js :3000)
    → REST/WS → FastAPI (:8000)
        → Celery → Redis
        → Strategy Engine (JSON → signals)
        → Backtest Engine (trade simulation)
    → PostgreSQL (data layer)
```

## Key Change from v3

- Backend: Python (was Node.js)
- Strategies: JSON-based (was TypeScript classes)
- No CLI needed — everything from dashboard
- Backtest-first pipeline

---
*Last synced: 2026-04-21*
