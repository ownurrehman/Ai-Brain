#!/bin/bash
# Mac process cleanup wrapper — runs the Ai Brain garbage collector silently.
# Appends output to ~/.hermes/logs/process_cleanup.log
# If >500MB reclaimed, writes /tmp/process_cleanup_note for Fleet Status Report to surface.

LOGFILE="$HOME/.hermes/logs/process_cleanup.log"
SCRIPT="/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/scripts/mac-process-cleanup.py"

mkdir -p "$HOME/.hermes/logs"
rm -f /tmp/process_cleanup_note

echo "===== $(date '+%Y-%m-%d %H:%M:%S') =====" >> "$LOGFILE"

BEFORE=$(ps aux | awk '{sum+=$6} END {print sum}')

python3 "$SCRIPT" >> "$LOGFILE" 2>&1
EXIT=$?

AFTER=$(ps aux | awk '{sum+=$6} END {print sum}')
RECLAIM_KB=$(( (BEFORE - AFTER) / 1024 ))
if [ "$RECLAIM_KB" -lt 0 ]; then RECLAIM_KB=0; fi
RECLAIM_MB=$((RECLAIM_KB / 1024))

if [ $EXIT -eq 0 ]; then
  echo "[OK] run complete, reclaimed ~${RECLAIM_MB}MB RAM" >> "$LOGFILE"
else
  echo "[WARN] cleanup exited with code ${EXIT}, reclaimed ~${RECLAIM_MB}MB" >> "$LOGFILE"
fi

# Surface to Fleet Status Report if big reclaim
if [ "$RECLAIM_MB" -gt 500 ] 2>/dev/null; then
  echo "Process cleanup reclaimed ${RECLAIM_MB}MB RAM at $(date '+%H:%M')" > /tmp/process_cleanup_note
fi