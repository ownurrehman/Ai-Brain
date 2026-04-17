# Crypto Trading Bots

## Tech Stack
Node.js, Python

## Core Architecture
Monorepo for multiple crypto trading bots (Legendary Bot v3.1 using Node.js, Freqtrade using Python). Legendary Bot uses 7-layer confirmation and state.json persistence.

## Primary Purpose
Centralized repository for cryptocurrency trading bots and backtesting infrastructure.


### Strategy implementation status vs research

| Research recommendation | Status | Notes |
| :--- | :--- | :--- |
| **VWAP filter** | ✅ | |
| **FVG** | ✅ | |
| **Market regime** | ⚡ Simplified | Rule-based proxy |
| **Liquidity sweep / CHoCH / OTE** | ✅ | |
| **HMM + LSTM / DRL / WebSocket / cap-based scan / AWS / Docker** | ❌ | Not yet |
| **OTO + OCO / Kill switches** | ✅ | Both active. OCO/OTO handled by exchange; Kill Switch (3%) handles logic-level panic exit & halt. |
---
