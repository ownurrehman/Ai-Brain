# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Repository Structure

This is a **monorepo** for multiple trading bot experiments. Each bot lives in its own subdirectory with its own `package.json` and entry point.

| Folder | Bot | Status |
|--------|-----|--------|
| `Legendary Bot/` | Legendary Bot v3.1 | Active — 7-layer SMC pipeline |
| `Sarmad Bot/` | Sarmad Bot | **OFF LIMITS — do not modify** |
| `archive/` | Old versions | Reference only |

## Workspace Rules

- **`Sarmad Bot/`** must never be touched — no refactors, no "while we're here" edits.
- All active development happens in `Legendary Bot/`.

## Legendary Bot — Commands

```bash
cd "Legendary Bot"
npm install

# Start via PM2
npx pm2 start ecosystem.config.js

# Apply DB schema (TimescaleDB/Postgres required)
psql $DATABASE_URL -f schema.sql
```

## Legendary Bot — Architecture

The system is built around a **performance-critical price pipeline** with TypeScript:

```
Exchange WebSocket
      ↓
ExchangeConnector.onTick()
      ↓
PriceBus.update(tick)          ← in-memory Map, O(1), hot path
      ↓
[SharedArrayBuffer → Worker Threads]
      ↓
StrategyWorker.onTick()        ← each strategy in its own worker thread
      ↓
Signal → RiskManager.validate()
      ↓
Exchange.placeOrder()
```

**Key files in `Legendary Bot/`:**
- `PriceBus.ts` — Singleton price store (Map-based, < 10ms latency). Primary source of truth for all prices across exchanges. Never add synchronous file I/O here.
- `strategy.ts` — Reference implementation of Strategy 21 (EMA + RSI Crossover). All strategies must implement `onTick(candles): Signal | null` — this must be **synchronous and fast**.
- `schema.sql` — TimescaleDB schema for `candles`, `trades`, `strategy_runs`, `evaluations`, `balances` tables.
- `index.html` — Dashboard frontend (vanilla HTML/CSS; uses DM Mono + Syne fonts, minimalist white design).

## Planned Structure (per `AGENTS.md`)

The full architecture being built out:

```
Legendary Bot/
├── core/
│   ├── exchanges/       ← One file per exchange (Binance primary, MEXC secondary, Bybit tertiary)
│   └── prices/          ← PriceBus.ts lives here
├── engine/
│   ├── spawner/         ← StrategySpawner.ts — Node.js Worker Threads per strategy
│   ├── runner/          ← DryRunner.ts / LiveRunner.ts
│   ├── evaluator/       ← PnL, Sharpe, Sortino, Drawdown metrics
│   └── risk/            ← RiskManager.ts + PanicManager.ts
├── strategies/
│   └── templates/       ← Numbered strategies (21_EMA_RSI, 22_MACD_Bollinger, ...)
├── data/
│   ├── db/              ← schema.sql + migrations
│   └── cache/           ← Redis key schema
├── dashboard/           ← React + Vite + Zustand + Recharts + TailwindCSS
└── scripts/             ← seed-historical.ts, run-backtest.ts, migrate-db.ts
```

## Data Layer Speed Hierarchy

```
1. SharedArrayBuffer / Node.js Map     ~0ms    ← PriceBus (never block this)
2. Redis (local)                       ~0.5ms  ← Candle cache, position state
3. TimescaleDB                         ~5ms    ← Trade history, OHLCV
4. File System                         ~20ms   ← Reports/exports only
```

## TypeScript Rules

- Never use `any` — every price tick and signal must be fully typed.
- `onTick()` must remain synchronous — strategies run in Worker Threads reading from SharedArrayBuffer.
- The Panic button (`PanicManager.killAll()`) is the highest-priority feature — it must always work.

## Security

- API keys live only in `config/exchanges.yaml` or `.env` — never in code.
- **Withdraw permission must be DISABLED** on all exchange API keys.
- Use read-only keys for price connectors; trade-enabled keys only in LiveRunner.
- All new strategies must pass a 7-day dry run before going live.

## Implementation Order

See the phased checklist in `Legendary Bot/AGENTS.md` under "Implementation Order for Agents" for the current build sequence (Foundation → Engine → Dashboard → Backtesting → Production).
