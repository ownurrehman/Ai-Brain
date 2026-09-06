# 9Router Setup (2026-09-05)

**What it is:** Local AI gateway/proxy on `127.0.0.1:20128` that sits between
Hermes and 40+ upstream LLM providers. Auto-rotates when one throttles.

## Why

- Ollama Cloud hit its 5h/weekly rate limit during the justccell catalog cut (Aug 30+).
- OpenRouter direct worked but has its own rate limits per model.
- 9Router aggregates 40+ free providers, RTK token compression, OpenAI-compat API,
  and handles key rotation automatically.

## Status (as of 2026-09-05)

- **Installed:** `npm install` in `/Users/sheikhown/9router/cli` + `9router` on PATH (`/Users/sheikhown/.nvm/versions/node/v24.18.0/bin/9router`, v0.5.59)
- **Running:** tray mode, PID stored by launchd, port 20128
- **Configured providers:** OpenRouter (with real key), LLM7 (placeholder, no key), NVIDIA free (placeholder), openai-compat node pointing to OpenRouter for `minimax/` and `openrouter/` prefixes
- **Pre-existing model catalog:** 433 models (all free tiers)
- **End-to-end test:** `openrouter/minimax/minimax-m3:free` → 2.9s, valid reply

## Files

- **Code:** `/Users/sheikhown/9router/` (cloned)
- **DB:** `/Users/sheikhown/.9router/db/data.sqlite`
- **Log:** `/tmp/9router.log`
- **Startup script:** `~/.hermes/scripts/start-9router.sh` (idempotent: starts if down, refreshes API key, writes `NINEROUTER_API_KEY` to master-env.env)
- **Env vars (master-env.env):** `NINEROUTER_URL=http://127.0.0.1:20128/v1`, `NINEROUTER_API_KEY=sk-c7e...` (auto-refreshed)

## Routing (for the agents)

Model name format: `<provider>/<model>`

- `openrouter/minimax/minimax-m3:free` → OpenRouter via 9Router
- `openai-compatible-chat-XXX/minimax-m3:free` → use node ID directly
- Anything else: 9Router resolves the prefix to a registered provider/connection

Dashboard: <http://127.0.0.1:20128> (default password `123456`)

## Failed attempts (documented so I don't retry)

1. **9router `--help` exit-on-TTY-close:** the server boots but exits when stdin closes. Fix: use `-t` (tray mode) for detached.
2. **Bearer key forwarding:** sending the OpenRouter key as `Authorization: Bearer` does NOT get forwarded to the upstream. 9Router uses its own key DB.
3. **Free pre-loaded providers (LLM7, NVIDIA):** connections exist but with no real key. Real key needed.
4. **Model prefix routing without node:** `minimax/minimax-m3:free` alone → "No credentials for provider: minimax" (no openai-compat node registered for that prefix). Fix: register the node + add a connection with the OpenRouter key.
5. **Master-env / config.yaml edits:** Hermes config is security-locked. I added the env vars only; the user edits `config.yaml` if they want 9Router in the fallback chain.

## Next: decide if I edit config.yaml

The user's `~/.hermes/config.yaml` is agent-locked. To put 9Router in the official fallback chain (so every agent auto-uses it), the user needs to add it under `fallback_providers`:

```yaml
fallback_providers:
  - provider: openai
    base_url: http://127.0.0.1:20128/v1
    api_key_env: NINEROUTER_API_KEY
    model: openrouter/minimax/minimax-m3:free
```

If 9Router is up, this kicks in last (after direct OpenRouter) and adds ~40
more free providers as a final safety net.
