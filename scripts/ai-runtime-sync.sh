#!/usr/bin/env bash
# Ai Brain — sync Node + OpenClaw to the canonical nvm runtime
set -euo pipefail

export NVM_DIR="${NVM_DIR:-$HOME/.nvm}"
# shellcheck disable=SC1091
. "$NVM_DIR/nvm.sh"

echo "=== Ai Brain runtime sync ==="
echo "Installing / selecting latest Node 22 via nvm..."
nvm install 22
nvm alias default 22
nvm use 22
hash -r

echo "Node: $(node -v) @ $(which node)"
echo "npm:  $(npm -v)"

echo "Installing openclaw@latest on this Node..."
npm install -g openclaw@latest

echo "Keeping Homebrew node installed for Gemini shebang, but unlinked from PATH..."
if command -v brew >/dev/null 2>&1 && brew list node >/dev/null 2>&1; then
  brew unlink node >/dev/null 2>&1 || true
fi

echo "Reinstalling OpenClaw gateway LaunchAgent from this shell Node..."
openclaw gateway install --force --port 18789
openclaw gateway restart || true
sleep 3

echo
bash "$(dirname "$0")/ai-runtime-check.sh"
