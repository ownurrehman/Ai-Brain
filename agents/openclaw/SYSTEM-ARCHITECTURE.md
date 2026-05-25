# Professional AI Tool Stack Management

## Current Chaos (Pre-Fix)
- **142 Node/MCP processes** (should be ~10-15)
- **8x duplicate Cursor MCPs**
- **8x duplicate Claude MCPs**
- **7x duplicate Screaming Frog**
- **Multiple IDE plugins spawning their own MCPs**
- **OpenClaw, Hermes, Discord bot all competing for resources**

## The Fix: Process Manager

### 1. Automated Cleanup Script
**Location:** `bin/process-manager.sh`

**Functions:**
- `clean` - Removes duplicate MCPs, kills orphaned processes
- `check` - Alerts if process count exceeds limits
- `status` - Shows current process breakdown

**Usage:**
```bash
# Manual cleanup
./bin/process-manager.sh clean

# Check status
./bin/process-manager.sh status
```

### 2. Cron Job (Auto-cleanup)
Runs every hour automatically:
```cron
0 * * * * /Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw/bin/process-manager.sh clean
```

### 3. Per-Tool Limits
| Tool | Max Processes | Notes |
|------|--------------|-------|
| Cursor MCP | 1 | IDE plugin |
| Claude MCP | 1 | IDE plugin |
| Screaming Frog | 1 | SEO tool |
| Git MCP | 1 | Version control |
| AWS MCPs | 1 each | Cloud services |
| OpenClaw | 3 | Gateway + agents |
| Hermes | 1 | WhatsApp bridge |
| Discord Bot | 1 | Already managed |

## Prevention Strategies

### IDE Configuration
**Cursor:**
- Disable auto-start MCP servers in settings
- Use `.cursor/mcp.json` with explicit `autoStart: false`

**Claude Desktop:**
- Edit `claude_desktop_config.json`
- Set `"autoStart": false` for non-essential MCPs

### OpenClaw Management
```bash
# Before running doctor --fix (ALWAYS backup first)
./bin/backup-config.sh && openclaw doctor --fix

# After making working config changes
./bin/update-last-good.sh
```

### WhatsApp/Hermes
- Hermes runs single bridge process (already efficient)
- Don't run multiple WhatsApp bridges

## Monitoring Dashboard

Create a simple status check:
```bash
# Add to .zshrc
alias ai-status="~/Ai Works - Local/Ai Codes/Ai Brain/openclaw/bin/process-manager.sh status"
```

## Resource Allocation

| Service | Expected RAM | Expected CPU |
|---------|-------------|-------------|
| OpenClaw Gateway | 200-400MB | 1-5% |
| OpenClaw Agent | 100-300MB | 0-10% |
| Discord Bot | 50-100MB | 1% |
| Hermes WhatsApp | 50-100MB | 1% |
| Cursor MCP | 20-50MB | 1% |
| Claude MCP | 20-50MB | 1% |
| **Total Expected** | **~1GB** | **~15%** |

**If system shows >2GB Node usage → Run cleanup immediately**

## Emergency Procedures

### If Gateway Crashes in Loop
```bash
# 1. Kill everything
pkill -f openclaw
pkill -f node

# 2. Clean up
./bin/process-manager.sh clean

# 3. Restart
gateway restart
```

### If Discord Messages Not Showing
```bash
# Check gateway capability
openclaw gateway status | grep Capability

# If read-only, restart after cleanup
./bin/process-manager.sh clean
openclaw gateway restart
```

## Weekly Maintenance
- [ ] Run `./bin/process-manager.sh clean`
- [ ] Check logs: `tail ~/.openclaw/logs/process-manager.log`
- [ ] Update `openclaw.json.last-good` if config is stable
- [ ] Review cron jobs: `crontab -l`

## Contact
If process chaos returns:
1. Run `./bin/process-manager.sh status`
2. Run `./bin/process-manager.sh clean`
3. Check this document for specific tool limits