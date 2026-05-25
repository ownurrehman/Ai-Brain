# Safe Doctor Fix Protocol

## The Problem
`openclaw doctor --fix` auto-restores config from `openclaw.json.last-good`, which may strip recent changes (like Discord `visibleReplies`, new agents, etc.)

## Prevention

### Option 1: Backup Before Doctor (Recommended)
```bash
# Run this BEFORE `openclaw doctor --fix`
./bin/backup-config.sh

# Then run doctor
openclaw doctor --fix

# Check what changed
diff ~/.openclaw/config-backups/openclaw.json.pre-doctor.XXXXXX ~/.openclaw/openclaw.json

# Restore if needed
cp ~/.openclaw/config-backups/openclaw.json.pre-doctor.XXXXXX ~/.openclaw/openclaw.json
```

### Option 2: Update last-good After Valid Changes
After making config changes that work:
```bash
cp ~/.openclaw/openclaw.json ~/.openclaw/openclaw.json.last-good
```
This updates the "known good" baseline so doctor won't roll back.

### Option 3: Don't Use --fix Blindly
Run `openclaw doctor` first (without --fix) to see issues, then fix manually:
```bash
openclaw doctor          # Shows issues only
# Fix specific issues manually in openclaw.json
# Then restart gateway
```

## Critical Fields Doctor May Strip
- `messages.groupChat.visibleReplies` (Discord visibility)
- `agents.list` entries (new agents like enigma, chronos)
- `channels.discord.actions` sub-fields
- `plugins.*` entries

## Check After Doctor
Always verify these after running doctor:
```bash
# Check agents
grep -c '"id":' ~/.openclaw/openclaw.json

# Check Discord visibility
openclaw config get messages.groupChat

# Check channels are still configured
openclaw channels status
```

## Quick Restore
If doctor breaks things:
```bash
# From most recent backup
ls -t ~/.openclaw/config-backups/ | head -1

# Or from .bak files
ls -lt ~/.openclaw/openclaw.json.bak* | head -5
```
