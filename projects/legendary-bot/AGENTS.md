# AGENTS.md — Legendary Bot: Production Guide for AI Agents

> This file is the single source of truth for any AI agent, developer, or LLM assistant
> building, extending, or debugging the Legendary Bot trading system.
> Read this entirely before touching any file.

---

## 🧠 System Overview

**Legendary Bot** is a professional-grade multi-exchange cryptocurrency trading bot with:
- Real-time price aggregation across Binance, MEXC, and other exchanges (sub-10ms)
- A strategy spawner capable of running 100s of concurrent strategies (live + dry-run)
- A backtesting engine that stress-tests strategies against historical data
- An automated evaluator that ranks strategies by PnL, drawdown, Sharpe ratio
- A minimalist white dashboard with real-time metrics and full control panel
- A panic system to kill all trades instantly

**Performance Target:** Price updates < 10ms. Order execution < 50ms.

---

## 🗂️ Folder Structure

```
LegendaryBot/
├── agents/                  ← You are here. AI agent instructions.
│   └── AGENTS.md
├── config/                  ← All environment and exchange config
│   ├── default.yaml         ← Base config
│   ├── exchanges.yaml       ← API keys, rate limits per exchange
│   └── risk.yaml            ← Global risk limits
├── core/                    ← The beating heart — performance critical
│   ├── exchanges/           ← One file per exchange connector
│   │   ├── base.ts          ← Abstract exchange interface
│   │   ├── binance.ts       ← Binance WebSocket + REST
│   │   ├── mexc.ts          ← MEXC WebSocket + REST
│   │   ├── bybit.ts         ← Bybit connector (future)
│   │   └── index.ts         ← Exchange registry
│   ├── prices/              ← THE PRICE BUS — fastest layer
│   │   ├── PriceBus.ts      ← In-memory price store (Map + Redis)
│   │   ├── aggregator.ts    ← Merges prices from all exchanges
│   │   └── types.ts         ← Price tick types
│   └── orderbook/           ← Orderbook management
│       ├── OrderBook.ts     ← L2 orderbook per symbol
│       └── snapshot.ts      ← Orderbook snapshot handler
├── engine/                  ← Strategy lifecycle management
│   ├── spawner/
│   │   ├── StrategySpawner.ts   ← Spawns/kills strategy workers
│   │   └── WorkerPool.ts        ← Thread pool for strategies
│   ├── runner/
│   │   ├── StrategyRunner.ts    ← Executes a single strategy
│   │   ├── DryRunner.ts         ← Paper trading mode
│   │   └── LiveRunner.ts        ← Real money mode
│   ├── evaluator/
│   │   ├── Evaluator.ts         ← Ranks strategies by performance
│   │   ├── metrics.ts           ← PnL, Sharpe, Sortino, Drawdown
│   │   └── report.ts            ← Generates strategy report
│   └── risk/
│       ├── RiskManager.ts       ← Position sizing, stop-loss enforcement
│       └── PanicManager.ts      ← KILL ALL TRADES — panic button handler
├── strategies/
│   ├── templates/           ← Strategy implementations
│   │   ├── 21_EMA_RSI/
│   │   │   ├── strategy.ts  ← Logic: entry/exit conditions
│   │   │   ├── config.yaml  ← Default params (periods, thresholds)
│   │   │   └── README.md    ← What this strategy does
│   │   ├── 22_MACD_Bollinger/
│   │   ├── 23_Grid_Range/
│   │   ├── 24_Momentum_Scalp/
│   │   ├── 25_Mean_Reversion/
│   │   ├── 26_Volume_Profile/
│   │   ├── 27_Trend_Follow/
│   │   └── 28_Arbitrage_Simple/
│   ├── active/              ← Symlinks or copies of running strategies
│   ├── backtests/           ← Backtest run outputs (JSON)
│   └── research/            ← Experimental / WIP strategies
├── data/
│   ├── db/                  ← TimescaleDB (Postgres) schema + migrations
│   ├── cache/               ← Redis config, key schemas
│   ├── historical/          ← OHLCV data per exchange/pair
│   ├── logs/                ← Rotating log files
│   └── exports/             ← CSV/JSON exports from reports
├── dashboard/               ← React frontend
│   ├── src/
│   │   ├── components/
│   │   │   ├── widgets/     ← Individual dashboard cards
│   │   │   ├── charts/      ← PnL chart, equity curve, drawdown
│   │   │   └── modals/      ← Spawn strategy, confirm kill, settings
│   │   ├── pages/
│   │   │   ├── Dashboard.tsx     ← Main overview
│   │   │   ├── Strategies.tsx    ← All strategies list + spawn
│   │   │   ├── Backtest.tsx      ← Run & view backtests
│   │   │   └── Reports.tsx       ← Strategy performance reports
│   │   ├── hooks/           ← useWebSocket, usePrices, useStrategies
│   │   ├── store/           ← Zustand state management
│   │   └── utils/           ← Formatters, calculations
│   └── public/
├── reports/
│   ├── backtest/            ← Backtest HTML/JSON reports
│   ├── live/                ← Live trading session reports
│   └── exports/             ← Downloadable CSVs
├── scripts/
│   ├── seed-historical.ts   ← Download historical OHLCV data
│   ├── run-backtest.ts      ← CLI to trigger backtests
│   └── migrate-db.ts        ← Run DB migrations
└── tests/
    ├── unit/
    ├── integration/
    └── e2e/
```

---

## ⚡ The Price Bus (Most Critical Component)

**File:** `core/prices/PriceBus.ts`

The Price Bus is the single source of truth for all prices.
It must be:
- In-memory first (JavaScript Map or SharedArrayBuffer for workers)
- Redis pub/sub as secondary layer for inter-process communication
- Updated every WebSocket tick from all exchanges
- Read by all strategy workers without blocking

```typescript
// Price tick structure
interface PriceTick {
  symbol: string;        // e.g. "BTCUSDT"
  exchange: string;      // e.g. "binance"
  bid: number;
  ask: number;
  last: number;
  volume24h: number;
  timestamp: number;     // Unix ms — MUST be < 10ms old
}

// PriceBus API
class PriceBus {
  subscribe(symbol: string, cb: (tick: PriceTick) => void): void
  getPrice(symbol: string): PriceTick | undefined
  getAllPrices(): Map<string, PriceTick>
  getBestPrice(symbol: string): { bid: number, ask: number, exchange: string }
}
```

**Agent Rule:** Never add synchronous file I/O in the price update path. Only Maps and Redis pipelines.

---

## 📦 Exchange Connectors

Each connector in `core/exchanges/` must implement:

```typescript
interface ExchangeConnector {
  name: string
  connect(): Promise<void>
  disconnect(): void
  subscribeToTicker(symbols: string[]): void
  subscribeToOrderBook(symbols: string[], depth: number): void
  placeOrder(order: OrderRequest): Promise<OrderResult>
  cancelOrder(orderId: string, symbol: string): Promise<void>
  getBalance(): Promise<Balance[]>
  on(event: 'tick' | 'orderbook' | 'fill' | 'error', cb: Function): void
}
```

**Supported Exchanges (Priority Order):**
1. **Binance** — Primary. Use `binance-connector` npm package + raw WebSocket for speed
2. **MEXC** — Secondary. WebSocket streams
3. **Bybit** — Tertiary
4. **OKX** — Future
5. **KuCoin** — Future

---

## 🤖 Strategy Interface

Every strategy in `strategies/templates/*/strategy.ts` must implement:

```typescript
interface Strategy {
  id: string            // "21_EMA_RSI"
  name: string          // "EMA + RSI Crossover"
  version: string
  params: StrategyParams
  
  // Called once on startup
  init(context: StrategyContext): Promise<void>
  
  // Called on every price tick — MUST be synchronous and fast
  onTick(tick: PriceTick, candles: Candle[]): Signal | null
  
  // Called on confirmed order fill
  onFill(fill: Fill): void
  
  // Risk parameters
  getMaxPositionSize(): number
  getStopLoss(): number      // % from entry
  getTakeProfit(): number    // % from entry
}

type Signal = {
  action: 'buy' | 'sell' | 'close'
  symbol: string
  quantity: number
  orderType: 'market' | 'limit'
  price?: number
  reason: string
}
```

---

## 🎛️ Strategy Spawner

**File:** `engine/spawner/StrategySpawner.ts`

The spawner manages the full lifecycle of strategies:

```
spawn(strategyId, params, mode: 'live'|'dry'|'backtest')
  → creates StrategyWorker (Node.js worker_thread)
  → assigns symbols to watch
  → connects to PriceBus
  → starts emitting signals

kill(instanceId)         → gracefully close positions, stop worker
killAll()                → PANIC — market-sell everything, kill all workers
getRunning()             → list of all active strategy instances
```

**Concurrency Model:**
- Each strategy runs in a **Node.js Worker Thread** (not child process)
- Shared memory (SharedArrayBuffer) for price data — no serialization overhead
- Max 500 concurrent strategies before needing horizontal scaling

---

## 📊 Evaluator & Backtesting

**File:** `engine/evaluator/Evaluator.ts`

After a backtest or live session, the evaluator produces:

| Metric | Description |
|--------|-------------|
| Total PnL | Net profit/loss in USDT |
| Win Rate | % of winning trades |
| Sharpe Ratio | Risk-adjusted return |
| Sortino Ratio | Downside risk adjusted |
| Max Drawdown | Worst peak-to-trough |
| Calmar Ratio | PnL / Max Drawdown |
| Avg Trade Duration | Mean hold time |
| Trade Count | Total trades executed |
| Best Trade | Highest single trade profit |
| Worst Trade | Biggest single trade loss |
| Profit Factor | Gross profit / Gross loss |

**Batch Backtest Flow:**
```
1. Load historical OHLCV from data/historical/
2. Spawn N strategy instances with parameter variants
3. Feed candles sequentially (no lookahead bias)
4. Collect all trades per instance
5. Run Evaluator on each
6. Rank by Sharpe + PnL
7. Generate HTML report in reports/backtest/
```

---

## 🖥️ Dashboard Architecture

**Stack:** React + Vite + Zustand + Recharts + TailwindCSS
**Style:** Minimalist white, clean data, no clutter

### Pages

| Page | Route | Description |
|------|-------|-------------|
| Dashboard | `/` | Main overview — all widgets |
| Strategies | `/strategies` | List, spawn, kill strategies |
| Backtest | `/backtest` | Run backtests, view history |
| Reports | `/reports` | Performance reports + exports |
| Settings | `/settings` | Exchange API keys, risk params |

### Dashboard Widgets (Required)

| Widget | Data Source |
|--------|-------------|
| Total Wallet Size | Exchange balance API |
| Total PnL (All Time) | Trade history DB |
| Today's PnL | Trade history, filtered |
| Active Bots (Live) | SpawnerService |
| Active Dry Runs | SpawnerService |
| Open Positions | Exchange + runner state |
| Server Status | Health check endpoint |
| Exchange Connection | Per-exchange ping |
| Prices Feed | PriceBus (live ticks) |
| Top Performing Strategy | Evaluator ranking |

### Controls (Required)

| Control | Action |
|---------|--------|
| 🔴 PANIC — Kill All | Calls PanicManager.killAll() |
| ➕ Spawn Strategy | Modal: select strategy, params, mode |
| ⏸ Pause All | Pause signal generation (keep positions) |
| 📊 Export Report | Download CSV/JSON of all trades |

---

## 🔐 Security Rules

1. **API keys** live only in `config/exchanges.yaml` or `.env` — never hardcoded
2. **IP whitelist** all exchange API keys to your server IP
3. Use **read-only API keys** for price fetching connectors
4. Use **trade-enabled API keys** only in LiveRunner
5. **Withdraw permission** must be DISABLED on all exchange API keys
6. All API calls go through a rate limiter to avoid bans

---

## 🗄️ Data Layer

### Speed Hierarchy (fastest to slowest)

```
1. SharedArrayBuffer / Node.js Map     ~0ms    ← Price Bus (live ticks)
2. Redis (in-memory, local)            ~0.5ms  ← Candle cache, position state
3. TimescaleDB (Postgres + time)       ~5ms    ← Trade history, OHLCV storage
4. File System (JSON/CSV)              ~20ms   ← Reports, exports only
```

### Redis Key Schema

```
prices:{symbol}:{exchange}           → JSON PriceTick (TTL 5s)
candles:{symbol}:{tf}:{exchange}     → JSON Candle[] (TTL 60s)
strategy:{instanceId}:state          → JSON StrategyState
strategy:{instanceId}:trades         → List of Trade JSON
positions:live                       → Hash of open positions
```

### TimescaleDB Tables

```sql
trades          (id, strategy_id, symbol, side, qty, price, pnl, timestamp)
candles         (symbol, exchange, tf, open, high, low, close, volume, timestamp)
strategy_runs   (id, strategy_id, mode, started_at, ended_at, config)
evaluations     (run_id, sharpe, pnl, win_rate, drawdown, ...)
balances        (exchange, asset, free, locked, timestamp)
```

---

## 🔄 WebSocket Architecture

```
Exchange WebSocket
        ↓
  ExchangeConnector.onTick()
        ↓
  PriceBus.update(tick)           ← atomic Map write
        ↓
  [Broadcast via SharedArrayBuffer to all Worker Threads]
        ↓
  StrategyWorker.onTick()        ← each strategy reacts
        ↓
  Signal → StrategyRunner
        ↓
  RiskManager.validate(signal)
        ↓
  Exchange.placeOrder()
```

---

## 🚨 Panic System

**File:** `engine/risk/PanicManager.ts`

When panic is triggered (button or automated risk breach):

```
1. Set global PANIC flag in Redis ("panic:active" = true)
2. All StrategyRunners see flag, stop generating signals
3. For every open position: place market SELL order
4. Wait for fill confirmations (timeout: 10s)
5. Kill all Worker Threads
6. Emit "panic:complete" event to dashboard via WebSocket
7. Log full incident report
```

---

## 📡 Backend API (Express + WebSocket)

### REST Endpoints

```
GET  /api/health                  → Server + exchange status
GET  /api/prices/:symbol          → Current price
GET  /api/strategies              → List all strategy templates
GET  /api/runners                 → List active runner instances
POST /api/runners/spawn           → Spawn new strategy instance
DEL  /api/runners/:id             → Kill a specific runner
POST /api/runners/panic           → KILL ALL
GET  /api/trades                  → Trade history (paginated)
GET  /api/balance                 → Wallet balance per exchange
POST /api/backtest/run            → Trigger backtest
GET  /api/backtest/:id/report     → Get backtest report
```

### WebSocket Events (server → dashboard)

```
price:update      { symbol, price, exchange, ms }
strategy:signal   { strategyId, signal, timestamp }
trade:fill        { trade object }
runner:spawned    { instanceId, strategyId }
runner:killed     { instanceId, reason }
panic:triggered   { timestamp }
panic:complete    { closedPositions, totalLoss }
balance:update    { exchange, balances }
```

---

## 🧪 Testing Requirements

- **Unit:** Every strategy's signal logic must have tests with mock candle data
- **Integration:** PriceBus → StrategyRunner → DryRunner full flow
- **E2E:** Dashboard spawn → dry run → kill cycle
- **Backtest Integrity:** Verify no lookahead bias in historical testing

---

## 📋 Implementation Order for Agents

When building this system, follow this order strictly:

```
Phase 1 — Foundation ✅ COMPLETE (2026-04-12)
  [x] Folder structure
  [x] .env.example + config schema
  [x] core/prices/types.ts — PriceTick, BestPrice, Candle, SharedArrayBuffer layout
  [x] core/prices/PriceBus.ts — In-memory Map + SharedArrayBuffer broadcast, sub-ms updates
  [x] core/exchanges/base.ts — Abstract ExchangeConnector interface
  [x] core/exchanges/binance.ts — WebSocket ticker + bookTicker, auto-reconnect
  [x] core/exchanges/index.ts — ExchangeRegistry with factory pattern
  [x] strategies/types.ts — Strategy, Signal, StrategyInstance interfaces
  [x] strategies/templates/21-EMA-RSI/ — Full EMA+RSI strategy implementation
  [x] strategies/templates/22-MACD-Bollinger/ — Placeholder
  [x] strategies/templates/23-Grid-Range/ — Placeholder
  [x] engine/spawner/StrategySpawner.ts — Spawn/track/kill/panic, template discovery
  [x] engine/risk/PanicManager.ts — Sacred kill switch
  [x] server/index.ts — Express + WebSocket server, wires all services
  [x] server/api.ts — REST API: health, prices, runners, panic, strategies
  [x] dashboard/ — React + Vite + Zustand + TailwindCSS
  [x] Dashboard widgets: Wallet, PnL, Active Bots, Dry Runs, System Health
  [x] Panic button with 2-click confirm
  [x] Spawn Strategy modal with strategy/symbol/mode selection
  [x] Runner list with per-instance kill
  [x] Live price feed widget
  [x] WebSocket hook for real-time updates
  [x] data/db/schema.sql — TimescaleDB schema (candles, trades, strategy_runs, evaluations, balances)

Phase 2 — Worker Isolation & Live Data ✅ COMPLETE (2026-04-12)
  [x] StrategySpawner upgraded to real Node.js Worker Threads (execArgv: ['--import', 'tsx'])
  [x] strategy-worker.ts — Worker script: polls SharedArrayBuffer, builds 1-min candles, runs onTick()
  [x] Binance public WebSocket streams — live BTCUSDT/ETHUSDT ticker data (no API key)
  [x] PriceBus.onSymbolAdded() — notifies spawner when new symbols get SharedArrayBuffer slots
  [x] engine/runner/DryRunner.ts — Paper trading: simulates fills at PriceBus prices, tracks PnL
  [x] Dashboard signal/trade event handling — runner:signal + trade:fill WebSocket events
  [x] RunnerList shows trade count, last signal action, live PnL per instance
  [x] Zustand store: signalLog, tradeLog, per-runner lastSignal + trade/PnL updates
  [x] End-to-end flow: spawn dry 21-EMA-RSI → worker reads live prices → signals → DryRunner fills → dashboard
  [x] Panic button kills all Worker Threads immediately via worker.terminate()
  [x] Backend + dashboard tsc --noEmit clean

Phase 3 — Engine Metrics & Connectors (next)
  [ ] engine/runner/LiveRunner.ts — Real money execution (stub)
  [ ] engine/evaluator/metrics.ts — PnL, Sharpe, Sortino, Drawdown
  [ ] MEXC exchange connector
  [ ] Redis cache layer

Phase 3 — Backtesting
  [ ] scripts/seed-historical.ts — Download historical OHLCV
  [ ] Batch backtest runner
  [ ] Report generator (HTML + JSON)
  [ ] Dashboard reports page

Phase 4 — Production
  [ ] LiveRunner.ts (real money)
  [ ] RiskManager.ts (position sizing, stop-loss enforcement)
  [ ] All remaining exchange connectors (Bybit, OKX)
  [ ] Full test coverage (unit + integration + E2E)
  [ ] Deployment scripts + PM2 ecosystem
```

---

## ⚠️ Agent Rules

1. **Never commit API keys** — check .gitignore before every commit
2. **Never use `any` in TypeScript** — every price tick must be typed
3. **Never block the price update loop** — strategies run in workers
4. **Always validate signal before order** — RiskManager is not optional
5. **Log everything** — every trade, signal, error goes to data/logs/
6. **Dry run first** — all new strategies must pass 7-day dry run before live
7. **The Panic button is sacred** — it must always work, even if everything else is broken

---

*Last updated by: Phase 2 Build Agent | Version: 2.0.0*
*Next agent: Read Phase 3 tasks above and start with LiveRunner stub + evaluator metrics*
