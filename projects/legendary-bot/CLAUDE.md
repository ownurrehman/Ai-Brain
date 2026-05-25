# Legendary Bot v4 — Crypto Strategy Lab

## Identity

Dashboard-first crypto strategy lab. No CLI required — everything from UI clicks.
JSON-based strategies. Backtest-first pipeline.

## Stack

- **Backend:** Python 3.12+ / FastAPI / Celery / Redis
- **Frontend:** Next.js 15 / React 19 / TailwindCSS v4 / shadcn/ui / Recharts
- **Database:** PostgreSQL + TimescaleDB for candle hypertables
- **Workers:** Celery + Redis for parallel strategy execution
- **Exchange:** Raw WebSocket (Binance primary, MEXC secondary)

## Commands

```bash
# Infrastructure
docker compose up -d                    # Postgres + Redis

# Backend
cd backend
pip install -r requirements.txt
uvicorn app.main:app --reload           # API on :8000
celery -A app.workers.celery_app worker # Celery worker

# Frontend
cd frontend
npm install
npm run dev                             # Next.js on :3000

# Database migrations
cd backend
alembic revision --autogenerate -m "msg"
alembic upgrade head
```

## Architecture

```
Dashboard (Next.js :3000)
    → REST/WS → FastAPI (:8000)
        → Celery tasks → Redis
        → Strategy Engine (JSON → signals)
        → Backtest Engine (simulate trades)
        → Paper/Live Engine (Phase 3/4)
    → PostgreSQL (strategies, trades, results)
    → TimescaleDB (candle hypertables)
```

## Key Files

| Path | Role |
|------|------|
| `backend/app/main.py` | FastAPI entry + CORS + route registration |
| `backend/app/core/strategy_engine.py` | JSON strategy parser → signal generator |
| `backend/app/core/backtest_engine.py` | Trade simulation with fees/slippage |
| `backend/app/indicators/technical.py` | RSI, MACD, EMA, Bollinger, VWAP, ATR |
| `backend/app/data/market_data.py` | Binance candle fetcher + DB storage |
| `backend/app/models/database.py` | SQLAlchemy models (all tables) |
| `backend/app/models/schemas.py` | Pydantic request/response schemas |
| `backend/app/workers/tasks.py` | Celery tasks (run_backtest) |
| `frontend/src/app/page.tsx` | Dashboard home |
| `frontend/src/app/strategies/page.tsx` | Strategy CRUD + JSON upload |
| `frontend/src/app/backtests/page.tsx` | Backtest launcher |
| `frontend/src/app/backtests/[id]/page.tsx` | Results + equity curve |
| `frontend/src/app/risk/page.tsx` | Risk management + kill switch |

## Strategy Flow

```
User adds JSON strategy → Stored in DB →
Backtest runs (Celery worker) → Results stored →
Top strategies → Paper trading (Phase 3) →
If stable → Live trading (Phase 4)
```

## Build Status

| Phase | Status |
|-------|--------|
| Phase 1 — Foundation (scaffold, DB, API, frontend) | COMPLETE |
| Phase 2 — Strategy Engine + Backtesting | COMPLETE |
| Phase 3 — Paper Trading + Live Data (Binance WS, paper engine, dashboard) | COMPLETE |
| Phase 4 — Live Trading + Risk | **CURRENT** |
| Phase 5 — Polish | Pending |

## Obsidian Vault (Second Brain)

Vault: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain`
After significant work, update via `user-obsidian` MCP:
- `projects/legendary-bot/changelog.md` — append what was done
- `projects/legendary-bot/todo.md` — sync task status
- `Mastersheet.md` — if milestone status changed

## Rules

1. No CLI required for the user — everything via dashboard
2. JSON strategies — no Python code needed to create strategies
3. Kill switch is sacred — always works
4. Backtest integrity — no lookahead bias, fees/slippage mandatory
5. Never commit API keys
6. Update Obsidian vault after significant work
