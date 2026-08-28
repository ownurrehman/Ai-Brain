> **Parent Hub:** [[INDEX|🧠 Master Ai Brain Hub]]

# Google Analytics 4 MCP — Agent Quick Reference

**Server ID:** `analytics-mcp`
**Package:** `pipx run analytics-mcp` v0.6.0
**Auth:** ADC via `ga-mcp-adc.json` (oliverjakeseo@gmail.com)
**Project:** `openclaw-rank-ray-automation`

---

## When to Use

| Task | Tool to Call |
|------|-------------|
| "What GA4 properties do we have?" | `get_account_summaries` |
| "Show me traffic for rankray.com last 30 days" | `run_report` |
| "What events are most popular?" | `run_report` with eventName dimension |
| "Are users converting on the contact page?" | `run_funnel_report` |
| "Any custom dimensions set up?" | `get_custom_dimensions_and_metrics` |
| "How many users are on the site right now?" | `run_realtime_report` |
| "Which pages have the highest bounce rate?" | `run_report` with bounceRate metric |

---

## Tool Details

### `get_account_summaries`
Lists all Google Analytics accounts and their properties.
- No parameters needed.

### `get_property_details`
Returns details about a specific property.
- **Parameter:** `property_id` (string) — e.g. `properties/276090163`

### `list_property_annotations`
Lists annotations (notes/markers) for a property.
- **Parameter:** `property_id` (string)

### `list_google_ads_links`
Returns linked Google Ads accounts.
- **Parameter:** `property_id` (string)

### `run_report`
Runs a GA4 Data API report.
- **Parameters:**
  - `property_id` (string) — e.g. `properties/276090163`
  - `date_ranges` (array) — e.g. `[{"start_date": "30daysAgo", "end_date": "today"}]`
  - `dimensions` (array of strings) — e.g. `["pageTitle", "pagePath"]`
  - `metrics` (array of strings) — e.g. `["sessions", "engagementRate", "bounceRate"]`
  - `limit` (number) — optional, max rows

### `run_realtime_report`
Realtime data for the last 30 minutes.
- **Parameters:**
  - `property_id` (string)
  - `dimensions` (array of strings) — optional
  - `metrics` (array of strings) — e.g. `["activeUsers"]`

### `run_funnel_report`
Funnel analysis.
- **Parameters:**
  - `property_id` (string)
  - `date_ranges` (array)
  - `funnel` (object) — funnel definition

### `run_conversions_report`
Conversion events report.
- **Parameters:**
  - `property_id` (string)
  - `date_ranges` (array)
  - `dimensions` / `metrics` (arrays of strings)

### `get_custom_dimensions_and_metrics`
Lists custom definitions for a property.
- **Parameter:** `property_id` (string)

---

## Known Properties

| Account | Property Name | Property ID | Timezone |
|---------|--------------|-------------|----------|
| Towel Depot | The Towel Depot | `properties/276090163` | America/Los_Angeles |

> ⚠️ **Important:** Only "Towel Depot" is currently visible via the ADC auth. If other sites (rankray.com, tonicphysio.com, coinsfera.com, teammotorcycle.com) use different Google accounts for GA4, those properties will NOT appear here. To add them:
> 1. Check which Google account owns each site's GA4 property
> 2. Either add `oliverjakeseo@gmail.com` as a user in those GA4 properties, OR
> 3. Create separate ADC credentials for each account and register additional MCP servers

---

## Example Prompts for Agents

- "Run a GA4 report via analytics-mcp for property properties/276090163 — show me sessions and engagement rate by page path for the last 30 days."
- "Use analytics-mcp to get the realtime active users for property properties/276090163."
- "Check analytics-mcp for any custom dimensions set up on property properties/276090163."
- "Run a funnel report via analytics-mcp for property properties/276090163 — track homepage → product page → checkout."
- "Get account summaries from analytics-mcp to see what GA properties we have access to."
- "Run a conversions report via analytics-mcp for property properties/276090163 for the last 7 days."

---

*Canonical doc: `docs/ga-mcp-reference.md` in Ai Brain root.*
