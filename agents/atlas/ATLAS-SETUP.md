# Atlas — Deep-Analysis Specialist (Kaggle TPU Qwen3.8-27B)

> **Parent Hub:** [[system/INDEX|⚙️ System Infrastructure Hub]] · [[agents/FLEET-ORCHESTRATION|🤖 Agent Fleet]]

**Created:** 2026-09-08
**Channel:** #claw-atlas (Discord ID 1546964526325436516)
**Model:** Qwen3.8-27B bf16 (262k context) via Kaggle TPU v5e-8, ~130 tok/s
**Profile:** ~/.hermes/profiles/atlas/ (config.yaml, SOUL.md, atlas_proxy.py)

## Burst Workflow (quota-saving design)
1. User/Hermes starts server: `cd /tmp/kaggle-tpu-lab && PATH="$HOME/.kaggle/venv/bin:$PATH" python3 launch.py serve`
2. READY banner writes /tmp/qwen_endpoint.json (base_url + api_key)
3. atlas_proxy (127.0.0.1:20129, launchd ai.hermes.atlas-proxy) forwards to live endpoint
4. Atlas works via fleet harness / #claw-atlas
5. On ATLAS_TASK_COMPLETE: `python3 launch.py stop` — quota clock stops

## Components
| Piece | Location | Purpose |
|---|---|---|
| Profile | ~/.hermes/profiles/atlas/ | agent config + SOUL.md |
| Proxy | ai.hermes.atlas-proxy (launchd) | 127.0.0.1:20129 -> live TPU endpoint; 503 when offline |
| Launcher | /tmp/kaggle-tpu-lab (patched: writes endpoint file) | start/stop/status |
| Kaggle creds | ~/.kaggle/access_token + venv 2.2.4 | CLI auth (user rankray) |
| Roster | fleet_coordinator.py ROSTER + mention_router + fleet_harness COORDS | harness dispatch |

## Quota (checked 2026-09-08)
- TPU: 20h/wk, GPU: 30h/wk, resets Fri 00:00 UTC
- `~/.kaggle/venv/bin/kaggle quota` to check

## Pending (as of creation)
- Kaggle script-kernel queue: test kernel rankray/test-net-conn QUEUED >45 min (Kaggle-side load; v3 pushed)
- Internet-on-kernel unverified post phone-verification (queue block)
- Gateway multiplex: discord-atlas route added to config.yaml; gateway restart needed for Atlas channel to go live
