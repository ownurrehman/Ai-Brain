# WordPress MCP Configuration

## Tonic Physio (tonicphysio.com)

**Required Setup:**
1. Install AI Engine plugin: https://wordpress.org/plugins/ai-engine/
2. Enable MCP Server: WP Admin → AI Engine → Settings → MCP
3. Set Bearer Token in MCP settings

**Connection Details:**
- **URL:** `https://tonicphysio.com/wp-json/mcp/v1/http`
- **Bearer Token:** [Set in AI Engine MCP settings]
- **Status:** Pending setup

## Rank Ray (rankray.com)

**Required Setup:**
1. Install AI Engine plugin (free)
2. Enable MCP Server in AI Engine settings
3. Configure Bearer Token

**Connection Details:**
- **URL:** `https://www.rankray.com/wp-json/mcp/v1/http`
- **Bearer Token:** [Set in AI Engine MCP settings]
- **Status:** Pending setup

## Usage

Once MCP is enabled on a site:

```bash
# List available tools
curl -s -X POST <URL> \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","id":1,"method":"tools/list"}'

# Test connectivity
curl -s -X POST <URL> \
  -H "Authorization: Bearer <TOKEN>" \
  -H "Content-Type: application/json" \
  -d '{"jsonrpc":"2.0","id":1,"method":"tools/call","params":{"name":"mcp_ping","arguments":{}}}'
```

## Next Steps

1. Install AI Engine plugin on tonicphysio.com
2. Enable MCP Server feature
3. Set Bearer Token
4. Test connection with `tools/list`
5. Update this config with actual token

## Features to Enable

In AI Engine → Settings → MCP Features, enable:
- ✅ **WordPress** (default) - Posts, pages, media, users
- ✅ **ACF** - For custom field management
- ⬜ **WooCommerce** - If needed for ecommerce
- ⬜ **SEO Engine** - For SEO analysis
- ⬜ **Polylang** - If multilingual
