#!/bin/bash
# OpenClaw Memory Backup Script
# Run this on your Mac before moving to VPS

set -e

BACKUP_DIR="$HOME/openclaw-backup-$(date +%Y-%m-%d)"
echo "Creating backup at: $BACKUP_DIR"

mkdir -p "$BACKUP_DIR"

# 1. MEMORIES (most important)
echo "Backing up memories..."
cp -r ~/.openclaw/memory "$BACKUP_DIR/"

# 2. Agent configs & identities
echo "Backing up agents..."
cp -r ~/.openclaw/agents "$BACKUP_DIR/"

# 3. Skills
echo "Backing up skills..."
cp -r ~/.openclaw/skills "$BACKUP_DIR/"

# 4. Credentials
echo "Backing up credentials..."
cp -r ~/.openclaw/credentials "$BACKUP_DIR/"

# 5. Environment & config
echo "Backing up config..."
cp ~/.openclaw/.env "$BACKUP_DIR/"
cp ~/.openclaw/openclaw.json "$BACKUP_DIR/" 2>/dev/null || true

# 6. Cron jobs
echo "Backing up cron jobs..."
cp -r ~/.openclaw/cron "$BACKUP_DIR/"

# 7. Discord settings
echo "Backing up Discord settings..."
cp -r ~/.openclaw/discord "$BACKUP_DIR/" 2>/dev/null || true

# 8. Local workspace
echo "Backing up workspace..."
cp -r ~/Ai\ Works\ -\ Local/Ai\ Codes/Ai\ Brain/openclaw "$BACKUP_DIR/workspace-local"

# 9. Create archive
echo "Creating compressed archive..."
cd "$HOME"
tar -czf "openclaw-vps-backup.tar.gz" "$(basename $BACKUP_DIR)"

# 10. Show results
echo ""
echo "DONE! Your backup is ready."
echo "Archive: $HOME/openclaw-vps-backup.tar.gz"
echo "Size: $(du -h $HOME/openclaw-vps-backup.tar.gz | cut -f1)"
echo ""
echo "Upload this to your VPS with:"
echo "  scp ~/openclaw-vps-backup.tar.gz root@YOUR_VPS_IP:/home/openclaw/"
