#!/bin/bash
# Backup openclaw.json before running doctor --fix
# Usage: ./backup-config.sh

CONFIG="$HOME/.openclaw/openclaw.json"
BACKUP_DIR="$HOME/.openclaw/config-backups"
TIMESTAMP=$(date +%Y%m%d-%H%M%S)

mkdir -p "$BACKUP_DIR"
cp "$CONFIG" "$BACKUP_DIR/openclaw.json.pre-doctor.$TIMESTAMP"
echo "Config backed up to: $BACKUP_DIR/openclaw.json.pre-doctor.$TIMESTAMP"
