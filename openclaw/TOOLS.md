# TOOLS.md

## Paths
Workspace: /Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw
Config: ~/.openclaw/openclaw.json
Env: ~/.openclaw/.env

## Quick health commands (terminal)
Cron list:
- openclaw cron list

Logs:
- openclaw logs --follow

Gateway restart:
- pkill -f openclaw-gateway
- openclaw gateway

Env sanity:
- echo "$NVIDIA_API_KEY" | cut -c1-6
- echo "$OPENCLAW_GATEWAY_TOKEN" | cut -c1-8

## Low token tactics
- Use compaction when a session grows.
- Prefer file artifacts over long chat outputs.
- For audits, output to system/reports/ and summarize in chat.

## Audio notes (no paid API)
Goal: local transcription only.
Preferred approach:
- Use `whisper` CLI (installed via brew) for voice note transcription.
- Command: `whisper <audio_file> --model tiny --output_format txt --output_dir /tmp/whisper-out`
- Fallback: faster-whisper in venv at ~/.openclaw/whisper-env
- Also: custom script at workspace/bin/whisper-transcribe
Never use paid APIs unless user explicitly approves.

<!-- clawx:begin -->
## ClawX Tool Notes

### uv (Python)

- `uv` is bundled with ClawX and on PATH. Do NOT use bare `python` or `pip`.
- Run scripts: `uv run python <script>` | Install packages: `uv pip install <package>`

### Browser

- `browser` tool provides full automation (scraping, form filling, testing) via an isolated managed browser.
- Flow: `action="start"` → `action="snapshot"` (see page + get element refs like `e12`) → `action="act"` (click/type using refs).
- Open new tabs: `action="open"` with `targetUrl`.
- To just open a URL for the user to view, use `shell:openExternal` instead.
<!-- clawx:end -->
