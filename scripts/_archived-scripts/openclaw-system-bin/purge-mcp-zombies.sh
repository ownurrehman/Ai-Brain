#!/bin/zsh
# Purge duplicate MCP servers, keeping only the most recent instance of each.

MCP_PATTERNS=(
  "mcp-server-filesystem"
  "semrush-mcp"
  "playwright-mcp"
  "context7-mcp"
  "mcp-server-memory"
  "mcp-pdf-server"
  "mcpvault"
)

for pattern in "${MCP_PATTERNS[@]}"; do
  # Get all PIDs for this pattern, sorted by start time (newest first)
  pids=($(ps aux | grep "$pattern" | grep -v grep | awk '{print $2}'))
  
  if [ ${#pids[@]} -gt 1 ]; then
    # Keep the first one (newest usually, or just one), kill the rest
    for (( i=1; i<${#pids[@]}; i++ )); do
      kill -9 ${pids[$i]} 2>/dev/null
    done
  fi
done
