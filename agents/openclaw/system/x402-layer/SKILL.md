---
name: x402-layer
version: 1.1.2
description: |
  This skill should be used when the user asks to "create x402 endpoint",
  "deploy monetized API", "pay for API with USDC", "check x402 credits",
  "consume API credits", "list endpoint on marketplace", "buy API credits",
  "topup endpoint", "browse x402 marketplace", use "Coinbase Agentic Wallet
  (AWAL)", or manage x402 Singularity Layer operations on Base or Solana networks.
homepage: https://studio.x402layer.cc/docs/agentic-access/openclaw-skill
metadata:
  clawdbot:
    emoji: "⚡"
    homepage: https://studio.x402layer.cc
    os:
      - linux
      - darwin
    requires:
      bins:
        - python3
      env:
        # Core credentials (required for payments)
        - WALLET_ADDRESS
        - PRIVATE_KEY
        # Solana payments (required for Solana network)
        - SOLANA_SECRET_KEY
        # Provider operations (required for endpoint management)
        - X_API_KEY
        - API_KEY
        # AWAL mode (optional - for Coinbase Agentic Wallet)
        - X402_USE_AWAL
        - X402_AUTH_MODE
        - X402_PREFER_NETWORK
        - AWAL_PACKAGE
        - AWAL_BIN
        - AWAL_FORCE_NPX
allowed-tools:
  - Read
  - Write
  - Edit
  - Bash
  - WebFetch
---


# x402 Singularity Layer

x402 is a **Web3 payment layer** enabling AI agents to:
- Pay for API access using USDC
- Deploy monetized endpoints
- Discover services via marketplace
- Manage endpoints and credits

**Networks:** Base (EVM) and Solana
**Currency:** USDC
**Protocol:** HTTP 402 Payment Required

---

## Quick Start

### 1. Install Dependencies
```bash
pip install -r {baseDir}/requirements.txt
```

### 2. Set Up Wallet (Choose One Mode)

#### Option A: Private Keys (existing mode)
```bash
# For Base (EVM)
export PRIVATE_KEY="0x..."
export WALLET_ADDRESS="0x..."

# For Solana (optional)
export SOLANA_SECRET_KEY="[1,2,3,...]"  # JSON array
```

#### Option B: Coinbase Agentic Wallet (AWAL)

For Base payments without exposing private keys, use Coinbase Agentic Wallet:

```bash
# First, install and set up AWAL (one-time setup)
npx skills add coinbase/agentic-wallet-skills

# Then enable AWAL mode for this skill
export X402_USE_AWAL=1
```

> **Note**: See [Coinbase AWAL docs](https://docs.cdp.coinbase.com/agentic-wallet/welcome) for full setup instructions.

---

## Security Notice

> **IMPORTANT**: This skill handles private keys for signing blockchain transactions.
> - Never use your primary custody wallet - Create a dedicated wallet with limited funds
> - Private keys are used locally only - They sign transactions locally and are never transmitted
> - Signed payloads are sent to api.x402layer.cc for payment settlement
> - For testing: Use a throwaway wallet with minimal USDC ($1-5 is enough)

---

## Scripts Overview

### CONSUMER MODE (Buying Services)

| Script | Purpose |
|--------|---------|
| pay_base.py | Pay for endpoint on Base network |
| pay_solana.py | Pay for endpoint on Solana network |
| consume_credits.py | Use pre-purchased credits (fast) |
| consume_product.py | Purchase digital products (files) |
| awal_cli.py | Run Coinbase Agentic Wallet CLI commands |
| check_credits.py | Check your credit balance |
| recharge_credits.py | Buy credit packs for an endpoint |
| discover_marketplace.py | Browse available services |

### PROVIDER MODE (Selling Services)

| Script | Purpose |
|--------|---------|
| create_endpoint.py | Deploy new monetized endpoint ($5) |
| manage_endpoint.py | View/update your endpoints |
| topup_endpoint.py | Recharge YOUR endpoint with credits |
| list_on_marketplace.py | Update marketplace listing |

---

## Consumer Flows

### A. Pay-Per-Request (Recommended)

```bash
# Pay with Base (EVM) - 100% reliable
python {baseDir}/scripts/pay_base.py https://api.x402layer.cc/e/weather-data

# Pay with Solana - includes retry logic
python {baseDir}/scripts/pay_solana.py https://api.x402layer.cc/e/weather-data

# Pay with AWAL (no private key needed)
export X402_USE_AWAL=1
python {baseDir}/scripts/pay_base.py https://api.x402layer.cc/e/weather-data
```

### B. Credit-Based Access (Fastest)

```bash
# Check your balance
python {baseDir}/scripts/check_credits.py weather-data

# Buy credits (consumer purchasing credits)
python {baseDir}/scripts/recharge_credits.py weather-data pack_100

# Use credits for instant access
python {baseDir}/scripts/consume_credits.py https://api.x402layer.cc/e/weather-data
```

---

## Provider Flows

### A. Create Endpoint ($5 one-time)

```bash
# Basic (not listed on marketplace)
python {baseDir}/scripts/create_endpoint.py my-api "My AI Service" https://api.example.com 0.01 --no-list

# With marketplace listing (recommended)
python {baseDir}/scripts/create_endpoint.py my-api "My AI Service" https://api.example.com 0.01 \
    --category ai \
    --description "AI-powered data analysis API"
```

**Available categories:** ai, data, finance, utility, social, gaming

### B. Manage Your Endpoint

```bash
# List your endpoints
python {baseDir}/scripts/manage_endpoint.py list

# View stats
python {baseDir}/scripts/manage_endpoint.py stats my-api
```

### C. Recharge Your Endpoint

```bash
# Add $10 worth of credits (5,000 credits)
python {baseDir}/scripts/topup_endpoint.py my-api 10
```

---

## Environment Reference

| Variable | Required For | Description |
|----------|--------------|-------------|
| PRIVATE_KEY | Base payments (private-key mode) | EVM private key (0x...) |
| WALLET_ADDRESS | All operations | Your wallet address |
| SOLANA_SECRET_KEY | Solana payments | JSON array of bytes |
| X402_USE_AWAL | AWAL mode | Set 1 to enable Coinbase Agentic Wallet for Base |
| X402_AUTH_MODE | Auth selection (optional) | auto, private-key, or awal |
| X402_PREFER_NETWORK | Network selection (optional) | base or solana |

---

## Resources

- ClawHub: https://clawhub.ai/ivaavimusic/x402-layer
- Documentation: https://studio.x402layer.cc/docs/agentic-access/openclaw-skill
- Marketplace: https://studio.x402layer.cc/marketplace
- GitHub: https://github.com/ivaavimusic/x402-Layer-Clawhub-Skill
