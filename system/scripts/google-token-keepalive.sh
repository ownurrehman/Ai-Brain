#!/bin/bash
# OAuth token keep-alive: refreshes Google token (setup.py --check triggers refresh when expired).
# Silent when healthy. Prints alert ONLY when token is dead -> cron delivers alert to Discord.
# After healthy check, syncs live token back to Ai Brain canonical store.
SETUP="/Users/sheikhown/.hermes/skills/productivity/google-workspace/scripts/setup.py"
CANONICAL="/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth"
OUT=$(python3 "$SETUP" --check 2>&1)
if echo "$OUT" | grep -q "AUTHENTICATED"; then
    cp "$HOME/.hermes/google_token.json" "$CANONICAL/oliverjakeseo@gmail.com-oauth-token.json"
    exit 0
fi
echo "GOOGLE OAUTH TOKEN DEAD — briefing/calendar/email will fail until re-auth."
echo "Error: $(echo "$OUT" | grep -E 'REFRESH_FAILED|invalid|CORRUPT|ERROR' | head -2)"