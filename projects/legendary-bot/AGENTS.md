# AGENTS.md — Legendary Bot v4: Strategy Lab

> Single source of truth for AI agents building this system.

## System Overview

Crypto Strategy Lab — a dashboard-controlled trading system where:
- Strategies are defined as JSON (no code)
- Backtesting runs with fee/slippage simulation
- Paper and live trading controlled entirely from dashboard
- Kill switch works at all times

## API Endpoints

```
GET  /api/health                    → System + DB status
GET  /api/strategies                → List all strategies
POST /api/strategies                → Create strategy (JSON body)
GET  /api/strategies/:id            → Get strategy detail
PATCH /api/strategies/:id/activate  → Activate strategy
PATCH /api/strategies/:id/deactivate → Deactivate strategy
DELETE /api/strategies/:id          → Delete strategy
POST /api/backtests                 → Launch backtest (Celery task)
GET  /api/backtests                 → List backtest sessions
GET  /api/backtests/:id             → Get backtest results
GET  /api/backtests/:id/trades      → Get backtest trade history
GET  /api/risk                      → Get risk config
PATCH /api/risk                     → Update risk params
POST /api/risk/kill-switch          → Toggle kill switch
GET  /api/market/candles            → Get stored candles
WS   /ws                           → Real-time updates
```

## Database Tables

| Table | Purpose |
|-------|---------|
| strategies | JSON definitions, status, timestamps |
| bot_sessions | Backtest/paper/live sessions with results |
| trades | Individual trade records with PnL |
| candles | OHLCV data (TimescaleDB hypertable) |
| risk_config | Global risk parameters + kill switch |

## JSON Strategy Format

```json
{
  "name": "RSI_MACD_Cross",
  "version": "1.0",
  "description": "Buy when RSI oversold + MACD bullish cross",
  "pairs": ["BTCUSDT"],
  "timeframes": ["1h"],
  "rules": {
    "entry": {
      "conditions": [
        {"indicator": "RSI", "period": 14, "op": "<", "value": 30},
        {"indicator": "MACD", "signal": "bullish_cross"}
      ],
      "logic": "AND"
    },
    "exit": {
      "conditions": [
        {"indicator": "RSI", "period": 14, "op": ">", "value": 70}
      ],
      "logic": "OR"
    }
  },
  "risk": {
    "stop_loss_pct": 2.0,
    "take_profit_pct": 4.0,
    "max_position_pct": 5.0
  }
}
```

## Supported Indicators

| Indicator | Parameters |
|-----------|------------|
| RSI | period (default 14) |
| EMA | period |
| SMA | period |
| MACD | fast=12, slow=26, signal=9 |
| Bollinger Bands | period=20, std_dev=2.0 |
| VWAP | (session-based) |
| ATR | period=14 |

## Supported Condition Operators

`<`, `>`, `<=`, `>=`, `==`, `crosses_above`, `crosses_below`

## Supported Signal Functions

`bullish_cross` (MACD), `bearish_cross` (MACD)

## Backtest Metrics

| Metric | Description |
|--------|-------------|
| Total PnL | Net profit/loss in $ |
| Win Rate | % of winning trades |
| Sharpe Ratio | Risk-adjusted return (annualized) |
| Max Drawdown | Worst peak-to-trough % |
| Profit Factor | Gross profit / Gross loss |
| Total Trades | Number of round-trip trades |
| Best/Worst Trade | Extremes in $ |
| Equity Curve | Time-series of portfolio value |

## Implementation Phases

| Phase | Status | What |
|-------|--------|------|
| 1 | DONE | Scaffold, DB models, API routes, frontend shell |
| 2 | DONE | Indicators, strategy engine, candle fetcher, backtest engine, dashboard |
| 3 | DONE | Binance WebSocket, paper trading, real-time WS updates |
| 4 | **CURRENT** | Live trading, risk manager enforcement, MEXC connector, dashboard controls |
| 5 | Pending | Strategy ranking, walk-forward, tests, deployment |

## Agent Rules

1. Everything must be controllable from the dashboard — no terminal for the user
2. JSON strategies only — the engine interprets rules, users don't write Python
3. Kill switch is sacred
4. Never commit API keys
5. Backtest must simulate fees and slippage
6. No lookahead bias in backtesting
