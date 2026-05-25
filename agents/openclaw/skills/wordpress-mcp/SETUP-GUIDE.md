# WordPress MCP Setup Guide for Tonic Physio

## Goal
Enable MCP (Model Context Protocol) on tonicphysio.com so OpenClaw can manage content, ACF fields, and media reliably without authentication issues.

## Why MCP?
- ✅ No more 401 REST API errors
- ✅ Automatic session management
- ✅ Full ACF field support
- ✅ Media upload handling
- ✅ Better error recovery

---

## Step 1: Install AI Engine Plugin

### Option A: Via WordPress Admin (Recommended - 2 minutes)

1. **Login to WordPress Admin**
   - URL: `https://tonicphysio.com/wp-admin`
   - Username: `Dan`
   - Password: `RR#Tonic@2026`

2. **Install Plugin**
   - Go to: **Plugins → Add New**
   - Search: **"AI Engine"**
   - Look for: **"AI Engine - Chatbot, Content Generation, AI Writing Assistant"** by Meow Apps
   - Click: **Install Now**
   - Click: **Activate**

### Option B: Manual Upload (If Option A fails)

1. Download AI Engine plugin:
   - URL: https://downloads.wordpress.org/plugin/ai-engine.zip

2. Upload via WordPress:
   - Go to: **Plugins → Add New → Upload Plugin**
   - Choose the downloaded ZIP file
   - Click: **Install Now**
   - Click: **Activate**

---

## Step 2: Enable MCP Server

1. **Navigate to MCP Settings**
   - In WordPress Admin: **AI Engine → Settings → MCP**

2. **Enable MCP Server**
   - Toggle: **"Enable MCP Server"** → ON

3. **Enable Features** (under MCP Features)
   - ✅ **WordPress** (Core - required)
   - ✅ **ACF** (For custom fields - required)
   - ✅ **Dynamic REST** (For raw API access - recommended)
   - ⬜ WooCommerce (optional - only if needed)
   - ⬜ SEO Engine (optional)
   - ⬜ Polylang (optional - only if multilingual)

4. **Set Bearer Token**
   - In MCP settings, find **"Bearer Token"** field
   - Generate or use this token: `tonic-mcp-2026-openclaw-secure-token`
   - Save the token

5. **Save Settings**
   - Click: **Save Changes**

---

## Step 3: Test MCP Connection

Run this command to verify:

```bash
curl -s -X POST "https://tonicphysio.com/wp-json/mcp/v1/http" \
  -H "Authorization: Bearer tonic-mcp-2026-openclaw-secure-token" \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","id":1,"method":"tools/list"}' | jq
```

**Expected Output:**
```json
{
  "jsonrpc": "2.0",
  "id": 1,
  "result": {
    "tools": [
      {"name": "mcp_ping", ...},
      {"name": "wp_get_post", ...},
      {"name": "wp_create_post", ...},
      {"name": "wp_update_post", ...},
      {"name": "acf_get_fields", ...},
      {"name": "acf_update_fields", ...}
    ]
  }
}
```

---

## Step 4: Update OpenClaw Configuration

Once MCP is working, update this file:

**File:** `/Users/sheikhown/.openclaw/workspace/skills/wordpress-mcp/CONFIG.md`

Add:
```markdown
## Tonic Physio (tonicphysio.com) - ✅ CONNECTED

- **URL:** `https://tonicphysio.com/wp-json/mcp/v1/http`
- **Bearer Token:** `tonic-mcp-2026-openclaw-secure-token`
- **Status:** Active
- **Enabled Features:** WordPress, ACF, Dynamic REST
```

---

## Step 5: Use MCP for WordPress Tasks

### Example: Update Herniated Disc Page (ID: 6996)

```bash
# Get post with all ACF fields
curl -s -X POST "https://tonicphysio.com/wp-json/mcp/v1/http" \
  -H "Authorization: Bearer tonic-mcp-2026-openclaw-secure-token" \
  -H "Content-Type: application/json" \
  -d '{
    "jsonrpc":"2.0",
    "id":1,
    "method":"tools/call",
    "params":{
      "name":"wp_get_post_snapshot",
      "arguments":{"post_id":6996}
    }
  }' | jq

# Update ACF fields
curl -s -X POST "https://tonicphysio.com/wp-json/mcp/v1/http" \
  -H "Authorization: Bearer tonic-mcp-2026-openclaw-secure-token" \
  -H "Content-Type: application/json" \
  -d '{
    "jsonrpc":"2.0",
    "id":1,
    "method":"tools/call",
    "params":{
      "name":"acf_update_fields",
      "arguments":{
        "post_id":6996,
        "fields":{
          "h1":"Get Rid of Herniated Disc Pain in Milton",
          "paragraph_1":"Expert herniated disc treatment...",
          "h2":"Expert Care for Disc Injuries in Milton"
        }
      }
    }
  }' | jq
```

---

## Troubleshooting

### MCP endpoint returns 404
- AI Engine plugin not activated
- MCP Server not enabled in settings

### Bearer token authentication fails
- Check token in AI Engine → Settings → MCP
- Ensure no extra spaces in token
- Verify Authorization header format: `Bearer <token>`

### ACF fields not updating
- Ensure ACF feature is enabled in MCP Features
- Check if post type supports ACF (pages/posts do)
- Verify field names match exactly

---

## Next Steps After Setup

1. Test herniated disc page update (ID: 6996)
2. Migrate all WordPress automation to use MCP
3. Deprecate old REST API scripts
4. Document MCP workflows in SOP

---

**Estimated Time:** 5-10 minutes
**Difficulty:** Easy (follow steps above)
