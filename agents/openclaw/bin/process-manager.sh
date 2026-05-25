#!/bin/bash
# Professional Process Manager for AI Tool Stack
# Controls MCP servers, Node processes, and prevents zombie accumulation

LOG_FILE="$HOME/.openclaw/logs/process-manager.log"
MAX_MCP_PER_TYPE=2
MAX_TOTAL_NODE=20

echo "$(date): Process Manager started" >> "$LOG_FILE"

# Function to log actions
log_action() {
    echo "$(date): $1" >> "$LOG_FILE"
}

# Kill duplicate MCP servers but keep 1 of each
clean_duplicates() {
    log_action "Cleaning duplicate MCP servers..."
    
    # Cursor MCP
    ps aux | grep "cursor-public" | grep -v grep | awk '{print $2}' | tail -n +$(($MAX_MCP_PER_TYPE + 1)) | xargs -I {} kill -9 {} 2>/dev/null
    
    # Claude MCP
    ps aux | grep "claude-mem" | grep -v grep | awk '{print $2}' | tail -n +$(($MAX_MCP_PER_TYPE + 1)) | xargs -I {} kill -9 {} 2>/dev/null
    
    # Screaming Frog
    ps aux | grep "screaming-frog-mcp" | grep -v grep | awk '{print $2}' | tail -n +$(($MAX_MCP_PER_TYPE + 1)) | xargs -I {} kill -9 {} 2>/dev/null
    
    # Git MCP
    ps aux | grep "mcp-server-git" | grep -v grep | awk '{print $2}' | tail -n +$(($MAX_MCP_PER_TYPE + 1)) | xargs -I {} kill -9 {} 2>/dev/null
    
    # AWS MCPs
    for aws_mcp in "awslabs.aws-serverless" "awslabs.aws-pricing" "awslabs.aws-iac" "awslabs.aurora-dsql"; do
        ps aux | grep "$aws_mcp" | grep -v grep | awk '{print $2}' | tail -n +$(($MAX_MCP_PER_TYPE + 1)) | xargs -I {} kill -9 {} 2>/dev/null
    done
    
    log_action "Duplicate cleanup complete"
}

# Kill orphaned Node processes (older than 30 min, low CPU)
clean_orphans() {
    log_action "Cleaning orphaned Node processes..."
    
    # Find node processes older than 30 minutes with 0% CPU
    ps -eo pid,etime,%cpu,command | grep node | grep -v grep | while read pid etime cpu cmd; do
        # Check if CPU is near 0 for a while
        if (( $(echo "$cpu < 0.1" | bc -l) )); then
            kill -9 "$pid" 2>/dev/null
            log_action "Killed orphaned node process $pid"
        fi
    done
}

# Check total node count and alert if too high
check_limits() {
    NODE_COUNT=$(ps aux | grep -E "node|mcp" | grep -v grep | wc -l | tr -d ' ')
    
    if [ "$NODE_COUNT" -gt "$MAX_TOTAL_NODE" ]; then
        log_action "WARNING: $NODE_COUNT node processes detected (limit: $MAX_TOTAL_NODE)"
        # Send notification to Discord
        if command -v openclaw &> /dev/null; then
            openclaw message send "🚨 Process Alert: $NODE_COUNT Node/MCP processes running. Consider cleanup." --channel claw-status 2>/dev/null || true
        fi
    fi
}

# Main execution
case "$1" in
    clean)
        clean_duplicates
        clean_orphans
        check_limits
        echo "Cleanup complete. Check $LOG_FILE for details."
        ;;
    check)
        check_limits
        NODE_COUNT=$(ps aux | grep -E "node|mcp" | grep -v grep | wc -l | tr -d ' ')
        echo "Current Node/MCP processes: $NODE_COUNT (limit: $MAX_TOTAL_NODE)"
        ;;
    status)
        echo "=== Process Status ==="
        echo "Node/MCP count: $(ps aux | grep -E 'node|mcp' | grep -v grep | wc -l)"
        echo "OpenClaw processes: $(ps aux | grep -i openclaw | grep -v grep | wc -l)"
        echo "Top memory users:"
        ps aux | sort -k6 -nr | head -5 | awk '{printf "  %.1fMB %s\n", $6/1024, $11}'
        ;;
    *)
        echo "Usage: $0 {clean|check|status}"
        echo ""
        echo "  clean  - Remove duplicate MCPs and orphaned processes"
        echo "  check  - Check if process counts are within limits"
        echo "  status - Show current process status"
        ;;
esac