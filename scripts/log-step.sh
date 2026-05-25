#!/bin/zsh
# log-step.sh — Write a step to the daily Obsidian note
# Usage: log-step.sh "Step Name" "Action" "Result" "File Changed"

VAULT_PATH="/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain"
DATE=$(date +%Y-%m-%d)
TIME=$(date +%H:%M:%S)
NOTE="$VAULT_PATH/memory/$DATE.md"

STEP_NAME="$1"
ACTION="$2"
RESULT="$3"
FILE_CHANGED="$4"

# Create memory directory if it doesn't exist
mkdir -p "$VAULT_PATH/memory"

# Create daily note if it doesn't exist
if [ ! -f "$NOTE" ]; then
    cat > "$NOTE" <<EOF
---
date: $DATE
agent: ${AGENT_NAME:-unknown}
status: in-progress
---

# Session Log: $DATE

## Steps

EOF
fi

# Append step
cat >> "$NOTE" <<EOF

### [$TIME] $STEP_NAME
- **Action:** $ACTION
- **Result:** $RESULT
- **File:** $FILE_CHANGED

EOF

echo "Logged to $NOTE"
