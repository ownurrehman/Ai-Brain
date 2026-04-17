# Crypto Trading Bots

Monorepo for **multiple** trading bot experiments. Each bot lives in its **own subdirectory** with its own `package.json`, `bot.js` (or entry), `.env`, and PM2 config.

## Bots

| Folder | Bot | Notes |
|--------|-----|--------|
| **`Legendary Bot/`** | Legendary Bot v3.1 | 7-layer SMC pipeline — Binance Spot + USDT-M Futures. Start here: `Legendary Bot/AGENTS.md`. |
| **`Sarmad Bot/`** | Sarmad Bot | Experimental sibling (`bot.js`, `package.json`); document here when you standardize runbooks. |

Add new bots as sibling folders (e.g. `Another Bot/`) and document them in this table.

## Run (Legendary Bot)

From **`Legendary Bot/`**:

```bash
cd "Legendary Bot"
npm install
# macOS: double-click RUN_BOT.command or:
npx pm2 start ecosystem.config.js
```

See **`Legendary Bot/README.md`** and **`Legendary Bot/AGENTS.md`** for strategy, env vars, and safety (`DRY_RUN`).
