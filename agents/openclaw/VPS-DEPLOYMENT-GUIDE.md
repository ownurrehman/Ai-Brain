# OpenClaw VPS Deployment Guide

> **Source:** Full transcript from Renato Dinis tutorial + your local setup scan
> **Purpose:** Move your entire OpenClaw (memories, skills, agents, cron jobs) from Mac to VPS
> **Date:** 2026-05-12

---

## Phase 1: Backup Everything From Your Local Mac

### 1.1 Create a backup archive

```bash
# Create a backup directory
mkdir -p ~/openclaw-backup-2026-05-12

# 1. Backup all memories (most important)
cp -r ~/.openclaw/memory ~/openclaw-backup-2026-05-12/

# 2. Backup agent configs and identities
cp -r ~/.openclaw/agents ~/openclaw-backup-2026-05-12/

# 3. Backup custom skills
cp -r ~/.openclaw/skills ~/openclaw-backup-2026-05-12/

# 4. Backup credentials
cp -r ~/.openclaw/credentials ~/openclaw-backup-2026-05-12/

# 5. Backup main config (API keys, Discord settings)
cp ~/.openclaw/.env ~/openclaw-backup-2026-05-12/
cp ~/.openclaw/openclaw.json ~/openclaw-backup-2026-05-12/ 2>/dev/null || echo "No openclaw.json found"

# 6. Backup cron jobs
cp -r ~/.openclaw/cron ~/openclaw-backup-2026-05-12/

# 7. Backup Discord channel settings
cp -r ~/.openclaw/discord ~/openclaw-backup-2026-05-12/

# 8. Backup your workspace (projects, reports, rules)
cp -r ~/Ai\ Works\ -\ Local/Ai\ Codes/Ai\ Brain/openclaw ~/openclaw-backup-2026-05-12/workspace-local

# 9. Backup identity files
cp -r ~/.openclaw/identity ~/openclaw-backup-2026-05-12/

# 10. Create a tar.gz archive
cd ~ && tar -czf openclaw-vps-backup.tar.gz openclaw-backup-2026-05-12/
```

### 1.2 Verify the backup

```bash
# Check the archive size
ls -lh ~/openclaw-vps-backup.tar.gz

# List what's inside
tar -tzf ~/openclaw-vps-backup.tar.gz | head -40
```

### 1.3 Securely note your API keys

Your `.env` file contains these keys. Verify you have them:
- `OPENAI_API_KEY` (or Ollama key)
- `DISCORD_BOT_TOKEN`
- `NVIDIA_API_KEY`
- `GOOGLE_PLACES_API_KEY`
- `BRAVE_SEARCH_API_KEY`
- `FIRECRAWL_API_KEY`
- WordPress credentials
- Any other service keys

> **Never commit `.env` to Git. Keep it in a password manager.**

---

## Phase 2: Get and Configure Your VPS

### 2.1 Choose a provider

Recommended for OpenClaw:
- **Contabo** (cheapest, what the video uses)
- **Hetzner** (good performance/price)
- **DigitalOcean** (easiest UI)
- **Vultr** or **Linode**

### 2.2 Minimum specs

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| RAM | 4GB | 8GB+ |
| CPU | 2 cores | 4 cores |
| Storage | 50GB SSD | 100GB SSD |
| OS | Ubuntu 22.04 LTS | Ubuntu 24.04 LTS |
| Network | 100Mbps | 1Gbps |

### 2.3 Create VPS and get credentials

1. Sign up on your chosen provider
2. Create an Ubuntu 22.04/24.04 instance
3. **Save the root password** or use SSH key
4. **Copy the IP address** of your VPS

---

## Phase 3: SSH Into Your VPS

### 3.1 Connect via SSH

```bash
ssh root@YOUR_VPS_IP
```

When prompted:
```
# Type 'yes' to accept the fingerprint
Are you sure you want to continue connecting? yes

# Paste your root password (won't show on screen)
root@YOUR_VPS_IP's password: [paste password]
```

### 3.2 Update the system

```bash
apt update && apt upgrade -y
```

### 3.3 Create a non-root user (security best practice)

```bash
# Create user 'openclaw' (or any name)
adduser openclaw
usermod -aG sudo openclaw

# Switch to that user
su - openclaw
```

> **The tutorial used root for speed. For production, always use a non-root user.**

---

## Phase 4: Install OpenClaw on VPS

### 4.1 Run the Quick Start installer

Go to https://openclaw.ai/ and copy the one-liner for Ubuntu, or run:

```bash
# For Linux (Ubuntu/Debian)
curl -fsSL https://openclaw.ai/install.sh | bash
```

Wait for installation. It installs all dependencies automatically. This takes 2-5 minutes depending on your VPS specs.

### 4.2 Verify installation

```bash
which openclaw
openclaw --version
```

### 4.3 Start the onboarding process

```bash
openclaw onboard
```

You'll be asked:

| Question | Answer |
|----------|--------|
| Security warning | `yes` |
| Setup type | `quickstart` |
| AI model provider | `openai` (or `ollama` if self-hosting) |
| API key | Paste your key |
| Default model | `gpt-4o` (or your preferred model) |
| Chat channel | `discord` |
| Discord bot token | We'll create this next (skip for now if you don't have one) |

---

## Phase 5: Create Your Discord Bot

### 5.1 Create a new Discord server

1. Open Discord (web or app)
2. Click the green **+** button on the left sidebar
3. Choose "Create My Own"
4. Name it (e.g., "RankRay Agents")
5. Create

### 5.2 Enable Developer Mode

1. Click your **user settings** (gear icon at bottom left)
2. Scroll down to **Advanced**
3. Toggle **Developer Mode** ON
4. Close settings

### 5.3 Create the bot application

1. Go to https://discord.com/developers/applications
2. Click **New Application** (top right)
3. Name it (e.g., "Jarvis" or "Enigma")
4. Complete CAPTCHA if asked

### 5.4 Get your bot token

1. In your app, click **Bot** on the left sidebar
2. Click **Reset Token** (or **Copy** if you already have one)
3. **Save this token somewhere safe - you can only see it once**
4. Keep this page open

### 5.5 Enable all required intents

In the **Bot** section, scroll to **Privileged Gateway Intents** and ENABLE all three:
- **Presence Intent**
- **Server Members Intent**
- **Message Content Intent**

Click **Save Changes** at the bottom.

### 5.6 Set up OAuth2 permissions

1. Click **OAuth2** on the left
2. Scroll to **OAuth2 URL Generator**
3. Under **Scopes**, check:
   - `bot`
   - `applications.commands`
4. Under **Bot Permissions**, check these:
   - **General**: View Channels, Manage Channels, Manage Roles, Manage Webhooks
   - **Text**: Send Messages, Send Messages in Threads, Create Public Threads, Create Private Threads, Embed Links, Attach Files, Read Message History, Mention Everyone, Use External Emojis, Use External Stickers, Add Reactions, Use Slash Commands
5. Scroll down and click **Copy** next to the generated URL

### 5.7 Invite bot to your server

1. Paste the copied URL into a new browser tab
2. Select your Discord server from the dropdown
3. Click **Continue**
4. Verify permissions match what you selected
5. Click **Authorize**
6. Complete CAPTCHA
7. You should see "Authorized" and the bot will appear in your server

### 5.8 Get your channel IDs

Back in Discord:
1. Create channels for your agents (e.g., `#general`, `#claw-developer`, `#rankray`, etc.)
2. Right-click each channel → **Copy Channel ID**
3. Save these IDs - you'll need them for the allowlist

### 5.9 Get your server (guild) ID

1. Right-click your server name → **Copy Server ID**
2. Save this ID

---

## Phase 6: Complete OpenClaw Onboarding

### 6.1 Continue the onboard process

Back in your VPS terminal where `openclaw onboard` is running:

1. Paste your **Discord bot token** when asked
2. For channel configuration, choose **allowlist** (recommended)
3. Paste your channel IDs separated by commas:
   ```
   1476025453599789191,1272860753535307817,1156128279430959165
   ```
4. If channels show as `*` (unresolved), it's because the bot wasn't invited yet. Go invite it (step 5.7), then continue.
5. Skip search provider setup for now (not critical)
6. Enable **ClawHub** when asked (for installing skills later)
7. Skip additional API keys for now - you can add them later
8. For hooks, enable the initial hatching hook
9. When asked where to hatch the agent, choose **later** (we'll do it properly after restoring memories)

### 6.2 Verify the gateway is running

```bash
openclaw gateway status
```

You should see all services as **running**.

---

## Phase 7: Restore Your Memories and Config

### 7.1 Transfer your backup to VPS

**From your Mac, run:**

```bash
# Method 1: SCP (secure copy)
scp ~/openclaw-vps-backup.tar.gz root@YOUR_VPS_IP:/home/openclaw/

# Method 2: If you have the backup on cloud storage, download it on VPS
curl -o /home/openclaw/openclaw-vps-backup.tar.gz [YOUR_DOWNLOAD_LINK]
```

### 7.2 Extract on VPS

```bash
# SSH back to VPS
ssh openclaw@YOUR_VPS_IP

# Extract
cd /home/openclaw
tar -xzf openclaw-vps-backup.tar.gz
```

### 7.3 Stop OpenClaw before restoring

```bash
openclaw gateway stop
```

### 7.4 Restore memories

```bash
# Backup the fresh empty memories first (just in case)
cp -r ~/.openclaw/memory ~/.openclaw/memory-fresh-backup

# Restore your real memories
cp -r ~/openclaw-backup-2026-05-12/memory/* ~/.openclaw/memory/
```

### 7.5 Restore agents

```bash
# Remove fresh agent configs
rm -rf ~/.openclaw/agents/*

# Restore your agents
cp -r ~/openclaw-backup-2026-05-12/agents/* ~/.openclaw/agents/
```

### 7.6 Restore skills

```bash
# Restore your custom skills
cp -r ~/openclaw-backup-2026-05-12/skills/* ~/.openclaw/skills/
```

### 7.7 Restore credentials and cron

```bash
# Credentials
cp -r ~/openclaw-backup-2026-05-12/credentials/* ~/.openclaw/credentials/

# Cron jobs
cp -r ~/openclaw-backup-2026-05-12/cron/* ~/.openclaw/cron/

# Discord settings
cp -r ~/openclaw-backup-2026-05-12/discord/* ~/.openclaw/discord/
```

### 7.8 Restore your workspace files

```bash
# Create workspace directory
mkdir -p ~/.openclaw/workspace

# Copy your project files
cp -r ~/openclaw-backup-2026-05-12/workspace-local/* ~/.openclaw/workspace/
```

### 7.9 Restore environment variables

Your `.env` file from the backup has your API keys. **Carefully merge** them:

```bash
# View the backup .env
cat ~/openclaw-backup-2026-05-12/.env

# Either copy it entirely (if VPS .env is empty)
cp ~/openclaw-backup-2026-05-12/.env ~/.openclaw/.env

# Or manually edit and add missing keys
nano ~/.openclaw/.env
```

> **Important:** The VPS `.env` might have new keys (like `OPENCLAW_GATEWAY_TOKEN`). Don't overwrite those. Merge carefully.

---

## Phase 8: Fix Discord Configuration

### 8.1 Update Discord guild ID

Your backup might have the old guild ID. Update it:

```bash
nano ~/.openclaw/openclaw.json
```

Find the Discord section and update:
- `guilds` → paste your new server ID
- `allowlist` → verify your new channel IDs are listed

### 8.2 Fix unresolved channels

If any channel shows `*` instead of the name, it means the bot can't resolve it. Fix by:
1. Making sure the bot is invited to the server
2. Making sure the bot has permission to view those channels
3. Restarting the gateway

### 8.3 Allow messaging without tagging

If you want the bot to respond without being tagged:

```bash
nano ~/.openclaw/openclaw.json
```

In the Discord config section, add:
```json
{
  "channels": {
    "discord": {
      "token": "YOUR_TOKEN",
      "guilds": ["YOUR_GUILD_ID"],
      "allowlist": ["CHANNEL_ID_1", "CHANNEL_ID_2"],
      "requireMention": false
    }
  }
}
```

### 8.4 Add your user ID to authorized users

1. In Discord, right-click your own username → **Copy User ID**
2. Edit config:

```bash
nano ~/.openclaw/openclaw.json
```

Add under Discord config:
```json
{
  "channels": {
    "discord": {
      "users": ["YOUR_DISCORD_USER_ID"]
    }
  }
}
```

This allows you to use slash commands like `/new`.

---

## Phase 9: Start OpenClaw and Verify

### 9.1 Start the gateway

```bash
openclaw gateway start
```

### 9.2 Check status

```bash
openclaw gateway status
```

### 9.3 Test Discord replies

1. Go to your Discord server
2. In an allowed channel, mention the bot: `@Jarvis hi`
3. The bot should respond

If it doesn't respond:
- Check `openclaw logs`
- Verify the bot is online in Discord
- Check if channels are in the allowlist

---

## Phase 10: Access Control UI (Dashboard)

### 10.1 Create SSH tunnel

**From your Mac:**

```bash
ssh -L 8080:localhost:8080 openclaw@YOUR_VPS_IP
```

Keep this terminal open. This maps port 8080 on the VPS to port 8080 on your Mac.

### 10.2 Get the auth link

**In a new terminal, SSH to VPS:**

```bash
ssh openclaw@YOUR_VPS_IP
openclaw dashboard
```

This will print a URL like:
```
http://localhost:8080/?token=abc123...
```

### 10.3 Open in browser

Copy that URL and open it in your Mac browser. The token is pre-filled, so you won't get the "token mismatch" error.

### 10.4 Wake your main agent

In the Control UI:
1. Go to **Agents**
2. Find your main agent
3. Click **Wake**
4. Set identity and user info

Or via terminal:
```bash
openclaw agents wake main
```

---

## Phase 11: Multi-Agent Setup (Optional)

### 11.1 List current agents

```bash
openclaw agents list
```

### 11.2 Create a new agent

```bash
openclaw agents add paul
```

It will ask for:
- Workspace location (press Enter for default)
- Model provider (press Enter to use default)
- Chat channels (press `n` - we'll bind manually)

### 11.3 Bind a channel to a specific agent

Edit the config:

```bash
nano ~/.openclaw/openclaw.json
```

Add a bindings section:
```json
{
  "bindings": [
    {
      "type": "route",
      "agent": "paul",
      "match": {
        "kind": "discord",
        "peer": "channel",
        "id": "YOUR_CHANNEL_B_ID"
      }
    }
  ]
}
```

This routes all messages from channel B to agent "paul" instead of the default agent.

### 11.4 Test the routing

1. Go to the bound channel in Discord
2. Type: `what's your name?`
3. It should respond as "Paul" instead of your main agent

### 11.5 Verify in Control UI

Go to the Control UI → Agents and check:
- Both agents appear
- Each has correct identity/user settings
- Tool permissions are set appropriately

---

## Phase 12: Security Hardening (CRITICAL)

### 12.1 Secure SSH

```bash
# Edit SSH config
sudo nano /etc/ssh/sshd_config
```

Make these changes:
```
PermitRootLogin no
PasswordAuthentication no  # Use SSH keys only
MaxAuthTries 3
```

Then:
```bash
sudo systemctl restart sshd
```

### 12.2 Set up firewall

```bash
# Install UFW if not present
sudo apt install ufw -y

# Allow only necessary ports
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP (if needed)
sudo ufw allow 443/tcp   # HTTPS (if needed)

# Enable firewall
sudo ufw enable
```

### 12.3 Close unnecessary ports

Your OpenClaw dashboard runs on port 8080 locally. Only access it via SSH tunnel - don't expose it to the internet.

### 12.4 Use a non-root user

You should have created `openclaw` user in Phase 3. Always use that user, not root.

### 12.5 Regular updates

```bash
sudo apt update && sudo apt upgrade -y
```

Set up automatic security updates:
```bash
sudo apt install unattended-upgrades -y
sudo dpkg-reconfigure -plow unattended-upgrades
```

---

## Phase 13: Final Verification Checklist

### 13.1 Test everything

- [ ] Bot responds in Discord when tagged
- [ ] Bot responds without being tagged (if `requireMention: false`)
- [ ] `/new` command works (you're in authorized users list)
- [ ] Control UI accessible via SSH tunnel
- [ ] Memories are intact (ask about past conversations)
- [ ] Custom skills work
- [ ] Cron jobs are scheduled
- [ ] Agent routing works (if multi-agent)

### 13.2 Commands reference

```bash
# Check gateway status
openclaw gateway status

# View logs
openclaw logs --follow

# List agents
openclaw agents list

# List cron jobs
openclaw cron list

# Restart gateway
openclaw gateway restart

# Stop gateway
openclaw gateway stop

# Start gateway
openclaw gateway start
```

---

## Troubleshooting

### Issue: Bot doesn't respond in Discord

**Fix:**
1. Check `openclaw gateway status` - is it running?
2. Check `openclaw logs` for errors
3. Verify bot is invited to the server
4. Verify bot has correct permissions
5. Verify channel ID is in the allowlist
6. Check if you need to tag the bot (see Phase 8.3)

### Issue: "Not authorized to use this command"

**Fix:**
1. Get your Discord user ID (right-click yourself)
2. Add it to the `users` array in `openclaw.json`
3. Restart gateway

### Issue: Token mismatch when accessing Control UI

**Fix:**
1. Use `openclaw dashboard` command to get the full URL with token
2. Make sure SSH tunnel is active

### Issue: Channels show as `*` in config

**Fix:**
1. The bot wasn't invited to the server when channels were added
2. Invite the bot (Phase 5.7)
3. Get the server ID (Phase 5.9)
4. Update `guilds` in `openclaw.json` with the correct server ID

### Issue: Multi-agent routing not working

**Fix:**
1. Check `openclaw agents list` - does the agent exist?
2. Verify the channel ID in the binding is correct
3. Check that the binding `type` is `"route"`
4. Restart gateway after config changes

---

## Important Notes

### API Keys on VPS

Your `.env` file is your lifeline. Keep it backed up separately. If you lose it, you'll need to regenerate all API keys.

### Memory Persistence

Your memories are now on the VPS. If the VPS dies, you lose them. Set up:
- Daily backups of `~/.openclaw/memory/`
- Or sync to S3/rsync

### Cron Jobs

Your cron jobs from the backup are restored. Verify they still work:
```bash
openclaw cron list
```

### Model Provider

If you used Ollama locally, you'll need to either:
- Install Ollama on the VPS (recommended for privacy)
- Switch to OpenAI/Anthropic API (easier, costs money)

---

## Your Specific Agent Setup

Based on your local config, you have these agents:
- `main` (Ranki / Dark)
- `enigma` (SEO/Content expert)
- `nemo` (Elite Code)
- `chronos` (Deep Research)
- `emilia` (Outreach)

After restoring, verify each:
```bash
openclaw agents list
```

Then wake them and verify their identities in the Control UI.

---

## Done!

Your OpenClaw is now running 24/7 on your VPS with all your memories, skills, and agents intact.

**Next steps:**
1. Set up automatic backups of `~/.openclaw/` to S3 or another VPS
2. Consider setting up a reverse proxy (nginx + SSL) if you need web access
3. Monitor VPS resources: `htop`, `df -h`
4. Set up log rotation to prevent disk filling up

**To connect from anywhere:**
```bash
# From any computer with the SSH key
ssh -L 8080:localhost:8080 openclaw@YOUR_VPS_IP
# Then open: http://localhost:8080
```

---

*Guide compiled from Renato Dinis tutorial transcript + your local setup scan.*
*Last updated: 2026-05-12*
