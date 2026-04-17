# Changelog

- (2026-04-11): Migrated project to isolated folder structure with dedicated `.ai` tracking system.


### Restored Mastersheet Update Update

- **Apr 07, 2026**: **Freqtrade Integration Research**: Downloaded and analyzed the `freqtrade` repository. Identified key modules (Backtesting, Hyperopt, FreqAI) and established a baseline understanding of its Python-based modular architecture for potential cross-pollination with Legendary Bot.


### Restored Mastersheet Update Update

- **Apr 07, 2026**: Documented Legendary Bot strategy confirmation flow and `state.json` flag meanings in its `AGENTS.md`. Checked for logic leaks (none found); confirmed strategy keys: `STRATEGY_VAL_1` (MACD), `STRATEGY_VAL_2` (RSI), `STRATEGY_VAL_3` (Volume).


### Restored Mastersheet Update Update

- **Legendary Bot (Tactical Launcher Update)** — *2026-04-07*
  - **Refined Single Strategy Launcher**: Upgraded the `/api/control/run-strategy` UI to a full "Tactical Launcher" with fields for mode (dry/real), scan limit, risk %, concurrency, throttle, and WS market/user flags.
  - **Isolated Sessions**: Each tactical run now spawns a new Terminal with an isolated `STATE_FILE`, preventing state collision between different experimental runs.
  - **Override Logic**: Form values in the UI now correctly override `.env` defaults for that specific process, allowing granular live testing without file edits.


### Restored Mastersheet Update Update

- **Legendary Bot (UI/UX overhaul: Premium Dark Glassmorphism Dashboard)** — *2026-04-07*
  - **Visual Overhaul**: Completely redesigned the browser dashboard to follow high-end "Modern Premium" aesthetics.
  - **Glassmorphism**: Implemented a frosted-glass effect with translucent cards, deep background saturation, and sleek borders.
  - **Dynamic Theme**: Dark-mode primary with neon-cyan accents and harmonic status-based colors (Trade: Buy=Cyan, Sell=Rose; Status: Live=Emerald, Offline=Amber).
  - **Typography**: Integrated `Outfit` and `Inter` from Google Fonts for a distinct, high-tech financial UI feel.
  - **Micro-animations**: Added subtle hover scaling, smooth layout transitions, and pulsating status indicators.
  - **KPI Real-time Refresh**: Fixed dashboard flickering by using state-aware DOM patching instead of full-table re-renders.
  - Added Lucide-based iconography for all metrics (Trades, Win Rate, PnL, Market Health).
  - **Responsive Layout**: Ensured the grid-based Mission Control scales gracefully from desktop to tablet viewports.


### Restored Mastersheet Update Update

- **Legendary Bot (Strategic Multi-Bot Commander HQ)** — *2026-04-07*
  - **Unified Portfolio Management**: Transformed the dashboard from a single-file viewer into a multi-bot orchestration hub. It now automatically discovers all bot state files (e.g., `state-dry-launch-...json`) in the `/state` directory.
  - **Dynamic Orchestration**: Users can now hot-swap between different active bot instances via a "Switch Commander" dropdown, with real-time UI updates for the selected portfolio.
  - **Per-Bot Command Routing**: Upgraded the command system to route actions (Stop, Close Trades, etc.) to the specific bot instance currently being viewed, enabling granular control over a distributed fleet of trading agents.
  - **State Discovery Backend**: Implemented an intensive discovery layer that crawls `/state` and maps PIDs to JSON snapshots, allowing the dashboard to represent both legacy runs and fresh tactical sessions.
  - **UI/UX Polish**: Enhanced the Premium Dark Suite with bot-specific identifiers, session indicators, and improved grid layouts for multi-account monitoring.


### Restored **Legendary Bot (Fleet Mission Control: Dashboard multi-bot aggregator & health blocks)** — *2026-04-07*

- **Fleet Overview**: Completely overhauled the dashboard into a "Fleet Mission Control" hub. It now aggregates real-time metrics (PnL, trades, win rate) from ALL active bot instances discovered in the `state/` directory.
- **Scan Blocks & SMC Transparency**: Re-introduced visual "Scan Blocks" to the dashboard, providing live feedback on which strategy layers (VWAP, FVG, Regime, RSI, etc.) are currently blocking or allowing trades.
- **Runtime Metrics**: Added a "Fleet Vitals" panel showing shared market service health, websocket latency, and worker process efficiency.
- **Bot-Specific Navigation**: Implemented a "Commander Select" sidebar, allowing operators to switch between a high-level fleet overview and detailed diagnostic views for individual bot sessions.
- **Live Stream Architecture**: Upgraded the SSE (Server-Sent Events) backend to broadcast aggregated fleet data, reducing frontend compute and ensuring sub-second sync across multiple browser tabs.


### Restored **Legendary Bot (v3.2: SSE Dashboard, WS Shared Market, & CLI Launcher)** — *2026-04-07*

- Removed all remaining inline `style=""` attributes in `dashboard/public/index.html`, replacing them with reusable classes (`is-hidden`, `row-spaced`, `mb-1`, `full-span`, disabled button class state).
- Moved **Entry blocks (counters)** into the top KPI grouping (`core`) so it renders with the primary metrics row instead of the lower panel section.
- Refactored UI state toggles to class-based updates (visibility/disabled state) for cleaner architecture and reduced lint/style drift risk.

- Fixed state discovery: Dashboard now correctly looks for `state/trading.json` (with `state.json` fallback), resolving "file not found" errors after the bot's state-folder migration.
- **Strategy 69 v3.2 Refactor**:
  - Fixed negative expectancy: Enforced 1:2 RR ratio (inclusive of fees).
  - Activated Advanced Filters: HF engine now runs full L1-L6 SMC gates (VWAP, FVG, Regime, Sweep, CHoCH, OTE).
  - Capped Overtrading: Reduced `MAX_OPEN_POSITIONS` from 25 to 3.
  - Trailing Stop: Integrated software-based ATR trailing stop in `monitorActivePositions`.
  - Slippage Guard: Added check to reject entries with >0.15% price deviation from signal.
- Verified `STATE.positions` schema updates for `maxPrice`/`minPrice` persistence.


### Restored **Legendary Bot (dashboard: paper $100×4, simpler price health, removed extra cards)** — *2026-04-07*

- Mission Control now includes **Switch to Tickerplant Mode** (auto-stops `market-data-service.js`, then starts Python tickerplant) to avoid port-3987 conflicts.
- **Stop All Bot Sessions** now uses server-side PID discovery/termination (no fragile shell matching), fixing lingering “active sessions” after stop.
- **Close all bot trades** now requests **stop bots first** before flattening bot-tracked positions, reducing immediate re-open race conditions.
- Dry paper defaults: **$100 each** in **Spot / Futures / Margin / Funding** (**$400** total); launcher passes four buckets; dashboard drops **Default rule set** and **Advanced signal counters**; **Price feed health** is plain-language + optional % line.


### Restored **Legendary Bot (Python tickerplant — shared market HTTP)** — *2026-04-07*

- Optional **`tickerplant/`** service (**cryptofeed** + Binance REST 24h ranking) exposes the same HTTP API as **`market-data-service.js`** for **`WS_SHARED_MARKET_URL`**. **`npm run market-data:tickerplant`**, venv + **`pip install -r tickerplant/requirements.txt`** — see **`Legendary Bot/AGENTS.md`**.


### Restored **Legendary Bot (dashboard security hardening)** — *2026-04-07*

- Optional **`DASHBOARD_API_KEY`** + UI session sign-in, bind guard (**no `0.0.0.0` / `::` without a key**), POST + SSE rate limits, strict JSON bodies, allowlisted **`stateRel`/`stateFile`** for close APIs, **per-session dry vs live** close semantics (basename + fallback to env). See **`Legendary Bot/AGENTS.md`** (Browser dashboard).


### Restored **Legendary Bot (dry benchmark — 33 keys, caps, paper bankrupt exit)** — *2026-04-07*

- Default **`BENCHMARK_KEYS`** includes **`0`–`10`** plus research **`11`–`30`** (33 workers). **`dry-run-benchmark`** passes **`MAX_OPEN_POSITIONS_NON_HF`**, **`MAX_OPEN_POSITIONS`**, **`S1_MAX_OPEN_POSITIONS`** default **20** and **`DRY_PAPER_BANKRUPT_EXIT=true`** so depleted paper exits cleanly when flat. See **`Legendary Bot/AGENTS.md`**.


### Restored **Legendary Bot (dashboard live KPI aggregation)** — *2026-04-07*

- Snapshot + SSE now **sum realized PnL, closed trade stats, wallets, SMC block counters, cycle counts, and `runtimeMetrics`** across the **same session state files** as open positions (fixes “stuck” PnL when many benchmark workers run). SSE tick **~1s**, poll fallback **~1.5s**; UI hints explain default `.env` strategy, 24h test progress, live-feed health, and advanced signal counters.
- **Follow-up:** runtime session list and aggregation **no longer drop** workers when state JSON is quiet for more than ~3 minutes; **benchmark workers** missing from `benchmark.pids.json` are inferred from **`BENCHMARK_WORKER=1`** in `ps`; UI shows **stale save** vs **fresh** and a per-session state-file list. See **`Legendary Bot/AGENTS.md`** (Browser dashboard).


### Restored **Legendary Bot (dashboard kill switch — close all bot trades)** — *2026-04-07*

- Open trades panel: **Close all bot trades** + `POST /api/control/close-all-positions` flattens every bot-tracked position across aggregated session state files (live: market orders + typed `CLOSE ALL`; dry: state only). Hint to stop bot sessions first so entries do not reopen.


### Restored **Legendary Bot (dashboard dry paper + strategy 69 preset)** — *2026-04-07*

- Launcher UI: **paper spot/futures** (USDT), **dry min order** (simulation floor below live 11 USDT), and one-click **Preset: 69 · dry · 400 · 1% · $50/$50**. Bot reads **`PAPER_SPOT_USDT`**, **`PAPER_FUTURES_USDT`**, **`PAPER_MIN_ORDER_USDT`**, and optional **`PAPER_BALANCE`** for dry wallets; small-notional dry mode relaxes sizing only when **`PAPER_MIN_ORDER_USDT`** is below **`MIN_ORDER_USDT`**.


### Restored **Legendary Bot (dry benchmark report — minimal terminal table)** — *2026-04-07*

- `scripts/dry-benchmark-report.js` now prints only **Strategy | PnL$ | PnL% | W/L | Open**, drops WS/REST/cycles/balance/merged columns, removes the long state-files map, and ends with one-line **Best / Worst** (+ optional duplicate-file note). `--full` still shows all strategies.


### Restored **Legendary Bot (report readability + open-trades aggregation)** — *2026-04-06*

- Upgraded `scripts/dry-benchmark-report.js` to group duplicate state files by strategy ID, colorize PnL output in terminal, default to **top 15** rows, and support `--full` for complete output.
- Dashboard snapshot now aggregates `openPositions` across all active strategy state files (from runtime worker sessions), so manually launched multi-strategy runs no longer hide trades from the Open Positions table.


### Restored **Legendary Bot (WS-first 24h benchmark architecture)** — *2026-04-06*

- Added shared WS cache service (`market-data-service.js`) with freshness checks + shard health telemetry, plus enriched feed metadata in `market-feed-ws.js`.
- Refactored `bot.js` market-data reads to cache-first (`WS_SHARED_MARKET_URL`) with strict stale-mode guard (`WS_STRICT_MARKET_DATA`) and runtime source/fallback metrics persisted in state.
- Added one-command WS-first orchestrator (`npm run benchmark-24h-ws`) to run 33-strategy dry benchmarks with shared cache, and extended stop/report flows to include WS service lifecycle + fallback/freshness metrics.
- Upgraded dashboard control center with a one-click **24h WS Benchmark (33x200)** action and benchmark progress visibility (profile, elapsed time, shared-cache service status).


### Restored **Legendary Bot (dashboard CSS cleanup + KPI ordering)** — *2026-04-06*

- Removed all remaining inline `style=""` attributes in `dashboard/public/index.html`, replacing them with reusable classes (`is-hidden`, `row-spaced`, `mb-1`, `full-span`, disabled button class state).
- Moved **Entry blocks (counters)** into the top KPI grouping (`core`) so it renders with the primary metrics row instead of the lower panel section.
- Refactored UI state toggles to class-based updates (visibility/disabled state) for cleaner architecture and reduced lint/style drift risk.


### Restored **Legendary Bot (mission-control live dashboard)** — *2026-04-06*

- Dashboard settings grids now render cleanly in **2 columns per row** for better width utilization and consistent alignment.
- Removed redundant **Open positions** summary KPI card; cycle context moved under Regime to reduce visual noise.
- Added SSE live stream endpoint (`/api/stream`) so dashboard receives continuous runtime snapshots without manual page refresh polling.
- Open positions now enrich from shared market cache (`WS_SHARED_MARKET_URL`) for live **Now** prices + per-trade **PnL** coloring.
- Shared market service root (`/`) now returns a helpful JSON response (not `not_found`) with endpoint hints.


### Restored **Legendary Bot (monitoring & kill-switch hardening)** — *2026-04-06*

- **Futures brackets:** `fetchPositions` matching fixed for CCXT unified symbols (`BTC/USDT` vs `BTC/USDT:USDT`) so TP/SL fills are not misread as “flat” on the first tick (which dropped positions and broke live runs).
- **Spot OCO:** Removed the `openOrders.length === 0` exit heuristic (false positives after entry / API lag). Exchange OCO still protects; the bot uses price-based SL/TP for state sync on spot.
- **Kill switch:** Drawdown only computed when `dailyStartBalance` is set and positive; avoids bad math on first live fetch.
- **Scanner:** `quoteVolume` sort tolerates missing ticker fields.
- **Kill-switch halt:** `slowLoop` still refreshes the dashboard when halted so the process looks alive.
- **Dashboard control center:** upgraded `dashboard/` from read-only snapshot to an operator panel with one-click PM2/benchmark actions and safe allowlisted `.env` runtime controls (strategy, scan limit, dry/live confirm, HF risk %, WS toggles).
- **Ops UX cleanup:** removed legacy `.command` launcher icons (bot/benchmark/status) and kept dashboard-first workflow with only `DASHBOARD.command` and `STOP_DASHBOARD.command`.
- **Strategy launcher UI:** dashboard now includes a one-click single-strategy launcher (strategy dropdown + dry/real + scan limit + HF risk + WS toggles + concurrency + Run) to replace brittle 10-strategy batch flows and reduce API stress.
- **Scope:** No HMM/LSTM/DRL/WebSocket stack added — **`Research_gemini.md`** remains research-only; **`Sarmad Bot/`** unchanged (friend’s backup baseline).


### Restored **Crypto Trading Bots (monorepo + Legendary Bot)** — *2026-04-04*

- **Layout:** Repo root **`./Crypto Trading Bots`**; first bot at **`Legendary Bot/`** (all former root files moved there). Add sibling folders for A/B tests. See **`Crypto Trading Bots/README.md`**.


### Restored **Legendary Bot — v3.0 strategy** — *2026-04-05*




### Restored **Legendary Bot (Multi-Platform Discovery Update)** — *2026-04-06*

- **Dynamic Discovery**: Implemented a `GLOBAL_WHITE_LIST` validation system. The bot now automatically checks the exchange's `loadMarkets` output for the presence of Macro/Commodity tickers (XAU, XAG, OIL, etc.) before scanning.
- **Auto-Filter**: If a platform (e.g., Binance Spot vs. BingX Futures) lacks a specific commodity or uses a different ticker variant, the bot will now **automatically ignore** the missing ticker without crashing.
- **Future-Proof**: This architecture allows the same bot logic to be used for BingX, Forex apps, or other exchanges without manually rewriting the scan list per platform.
- **UI**: Logged as `DISCOVERED MACRO` on the sync cycle to show how many white-list targets are active on the current platform.


### Restored **Legendary Bot (High-Cap Scanner Update)** — *2026-04-06*

- **Refined Scanner**: Transitioned to a **Top 200 by Volume** scan to balance market coverage with API performance and rate-limit safety.
- **Commodity Inclusion**: Added a **forced scan list** for commodities including **Gold (PAXG), Silver, Oil, and Gas**. These are now scanned every cycle regardless of their volume rank.
- **API Safety**: By capping the volume list at 200 (down from global rest), we ensure faster cycles and consistent IP health.
- **UI**: Added a custom `Commodities 🛢️` indicator to the dashboard scanner display.


### Restored **Legendary Bot (Global Scanner Update)** — *2026-04-06*

- **Scanner**: Removed the `TOP_N_COINS` limit. The bot now scans **all available USDT markets** (Spot and USDS-M Futures) on Binance.
- **Safety**: Preserved the 200ms per-symbol delay to prevent IP rate-limiting by the exchange.
- **UI**: Dashboard now displays `Scanner: 🌎 ALL MARKETS` for clarity.
- **Pairs**: Filters for USDT base pairs only, excluding leveraged tokens (UP/DOWN).


### Restored **Legendary Bot (Kill Switch Update)** — *2026-04-06*

- **Safety Layer**: Implemented a 3% Daily Max Drawdown Kill Switch based on UTC day start balance.
- **Panic Liquidation**: If triggered, the bot automatically closes all active positions and cancels pending OCO/OTO orders on the exchange.
- **Hard Halt**: Added `SHUTDOWN_ON_KILL_SWITCH` to immediately stop the process and prevent further "revenge trading" or logic errors during excessive volatility.
- **Auto-Reset**: Logic resets at 00:00 UTC unless the kill switch is hard-halted, requiring manual restart.


### Restored **Legendary Bot (OCO/OTO Update)** — *2026-04-06*

- **Implementation**: Added native OCO (Spot) and OTO/Linked (Futures) order logic to `bot.js`.
- **Safety**: Entry orders now trigger exchange-side Stop Loss and Take Profit orders immediately, reducing reliance on the bot's manual monitoring loop for exits.
- **Logic**: Added `isManagedByExchange` flag to positions in `state.json` to prevent collisions between manual and exchange-side exits.
- **Fallback**: Manual monitoring remains active as a fallback and for `DRY_RUN` mode.


### Restored Mastersheet Update Update

- [Apr 7, 2026] Legendary Bot: Fixed overtrading (Strategy 69) with atomic entry locks and WS cache warming (restoring instant scan reactivity).


### Restored Mastersheet Update Update

- [Apr 6, 2026] Legendary Bot: Finalized Strategy Engine HF Futures (Aggressive Scalper) for Legendary Bot v3.1.
