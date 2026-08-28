#!/usr/bin/env bash
# Quick runner for CrewAI Agency Growth Flow
set -e

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
PYTHON="/Users/sheikhown/Ai Works - Local/Ai Codes/Apps/agency-flows/.venv/bin/python"
if [ ! -f "$PYTHON" ]; then
    PYTHON="$DIR/.venv/bin/python"
fi

if [ ! -f "$PYTHON" ]; then
    echo "Virtual environment not found. Run setup first."
    exit 1
fi

"$PYTHON" "$DIR/agency_growth_flow.py" "$@"
