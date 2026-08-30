> **Parent Hub:** [[memory/entities/INDEX|🌐 Entity Knowledge Graph Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# 🪙 Industry Ontology: Crypto, Web3 & FinTech

> **Semantic schema and entity ontology mapping for cryptocurrency, OTC trading, and blockchain fintech.**

---

## 🗺️ Core Entity Hierarchy

```mermaid
graph TD
    Crypto["Cryptocurrency & FinTech"] --> Settlement["Settlement & Exchange Layer"]
    Crypto --> Assets["Digital Asset Classes"]
    Crypto --> Regulatory["VARA & Compliance Frameworks"]

    Settlement --> OTC["Over-The-Counter (OTC) Desks"]
    Settlement --> DEX["Decentralized Exchanges"]
    Settlement --> Ramp["Fiat On/Off Ramps"]

    Assets --> Stablecoins["USDT, USDC, DAI"]
    Assets --> StoreOfValue["Bitcoin (BTC)"]
    Assets --> SmartContract["Ethereum (ETH), Solana (SOL)"]
```

---

## 📌 Standard Entity Triples for Content & Schema
- `USDT` $\rightarrow$ `is pegged to` $\rightarrow$ `United States Dollar (USD)`
- `OTC Desk` $\rightarrow$ `enables` $\rightarrow$ `High-Volume Non-Slippage Trades`
- `VARA` $\rightarrow$ `regulates virtual assets in` $\rightarrow$ `Dubai, UAE`
- `Cold Storage` $\rightarrow$ `protects private keys via` $\rightarrow$ `Air-Gapped Hardware`
