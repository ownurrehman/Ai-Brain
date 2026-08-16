#!/usr/bin/env bash
# Ai Brain — runtime health check (read-only)
set -euo pipefail

export NVM_DIR="${NVM_DIR:-$HOME/.nvm}"
# shellcheck disable=SC1091
[ -s "$NVM_DIR/nvm.sh" ] && . "$NVM_DIR/nvm.sh"
nvm use default >/dev/null 2>&1 || nvm use 22 >/dev/null 2>&1 || true

echo "=== Ai Brain runtime check ==="
echo "date:        $(date '+%Y-%m-%d %H:%M:%S %Z')"
echo "shell node:  $(node -v 2>/dev/null || echo MISSING) @ $(command -v node 2>/dev/null || echo none)"
echo "nvm default: $(nvm alias default 2>/dev/null | tr -d '\n' || echo unknown)"
echo "npm:         $(npm -v 2>/dev/null || echo MISSING)"
echo

echo "--- other node binaries (should NOT win over nvm) ---"
for p in /opt/homebrew/bin/node /usr/local/bin/node "$HOME/.hermes/node/bin/node"; do
  if [ -x "$p" ]; then
    echo "  $p => $($p -v 2>/dev/null)"
  else
    echo "  $p => (absent)"
  fi
done
if [ -x /opt/homebrew/opt/node/bin/node ]; then
  echo "  /opt/homebrew/opt/node/bin/node => $(/opt/homebrew/opt/node/bin/node -v) (gemini shebang OK)"
fi
echo

echo "--- tools ---"
for t in openclaw gemini hermes cursor; do
  if command -v "$t" >/dev/null 2>&1; then
    echo "  $t => $(command -v "$t")"
  else
    echo "  $t => MISSING"
  fi
done
echo

if command -v openclaw >/dev/null 2>&1; then
  echo "--- openclaw ---"
  openclaw --version 2>&1 | head -1 || true
  openclaw gateway status 2>&1 | rg -i 'CLI version|Gateway version|Runtime|Connectivity|Listening|Service config issue' || true
  echo
fi

if command -v hermes >/dev/null 2>&1; then
  echo "--- hermes ---"
  hermes --version 2>&1 | head -3 || true
  launchctl print "gui/$(id -u)/ai.hermes.gateway" 2>&1 | rg -i 'state = |pid = ' | head -6 || true
  echo
fi

if command -v gemini >/dev/null 2>&1; then
  echo "--- gemini ---"
  head -1 "$(command -v gemini)" 2>/dev/null || true
  gemini --version 2>&1 | head -3 || echo "  (gemini --version failed)"
  echo
fi

echo "--- Ai Brain ---"
echo "  $HOME/Ai Works - Local/Ai Codes/Ai Brain"
echo "Done."
