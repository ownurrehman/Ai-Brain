# Ollama Cloud Plan — Rate Comparison (2026-09-01)

## Plan: Pro ($20/month)
- $60 usage credits per month
- 3 concurrent requests
- Access to larger pro models
- Usage resets monthly
- No logging, no training, zero data retention

## Token Rates (per million tokens, off-peak)

| Model | Input | Cached Input | Output | ~Tokens on $60 credits |
|---|---|---|---|---|
| gpt-oss:20b | $0.07 | $0.035 | $0.30 | ~370M |
| glm-5.3-flash | $0.15 | $0.03 | $0.50 | ~207M |
| deepseek-v4-flash | $0.22 | $0.007 | $0.66 | ~152M |
| gpt-oss:120b | $0.15 | $0.014 | $0.60 | ~190M |
| deepseek-v4-pro | $0.66 | $0.022 | $1.98 | ~51M |
| glm-5.3 | $1.40 | $0.26 | $4.40 | ~27M |
| glm-5.2 | $1.40 | $0.26 | $4.40 | ~27M |
| kimi-k2.6 | $0.95 | $0.16 | $4.00 | ~28M |
| kimi-k3 | $3.00 | $0.30 | $15.00 | ~15M |

## Peak Pricing (12:00–18:00 UTC, Mon–Fri)
DeepSeek V4 Flash: $0.44/$1.32 (doubles)
DeepSeek V4 Pro: $1.32/$3.96 (doubles)
All other models: no peak pricing

## What This Means
- glm-5.3-flash is the CHEAPEST = maximum token budget on $60 credits
- DeepSeek V4 Flash costs 1.5x more per token than glm-5.3-flash
- DeepSeek V4 Pro costs 4x more per token
- Off-peak pricing halves DeepSeek costs (use before 5PM or after 11PM PKT)
- Peak pricing doubles DeepSeek costs (12PM–6PM UTC = 5PM–11PM PKT)