# WordPress REST API Credentials
# All Sites - PRIMARY SOURCE OF TRUTH
# Updated: 2026-04-30

---

## TonicPhysio.com (tonicphysio.com)

| Field | Value |
|-------|-------|
| **URL** | `https://tonicphysio.com/wp-json/wp/v2/` |
| **Username** | `Dan` |
| **Web Login** | `TP#Admin@2026` (for browser/wp-admin ONLY) |
| **Application Password** | `NMwZ 1LyJ YgbE fUjs pUYn 4SoZ` (for REST API) |
| **App Name** | `openclaw` (name in WP UI, not login) |
| **User Role** | Administrator |
| **Template** | `services-pages.php` (for service pages) |
| **Page Category ID** | 325 (Service Page) |
| **Status** | ACTIVE ✅ |

### Notes
- Previous Application Password (`6Zz9 5gJL 8uyA QH4g RQDH GV1j`) for `openclaw` user was REVOKED. Do NOT use.
- `Dan` is the actual admin username for both REST API and wp-admin login.
- `openclaw` is just the label/display name of the Application Password in WordPress UI.
- REST API POST works. Use 60s timeout, `Connection: close` header recommended.

---

## RankRay.com (rankray.com)

| Field | Value |
|-------|-------|
| **URL** | `https://rankray.com/wp-json/wp/v2/` |
| **Username** | `openclaw` |
| **Application Password** | `6Zz9 5gJL 8uyA QH4g RQDH GV1j` |
| **User Role** | Administrator |
| **Status** | ACTIVE ✅ |

### Notes
- Confirmed working for both GET and POST.
- Rate limit: ~100 requests/sec. Use 0.5s delay between posts.

---

## TeamMotorcycle.com

| Field | Value |
|-------|-------|
| **URL** | `https://teammotorcycle.com/wp-json/wp/v2/` |
| **Username** | [TBD] |
| **Application Password** | [TBD] |
| **Status** | NOT CONFIGURED ❌ |

---

## KhanLLP.com

| Field | Value |
|-------|-------|
| **URL** | `https://khanllp.com/wp-json/wp/v2/` |
| **Username** | [TBD] |
| **Application Password** | [TBD] |
| **Status** | NOT CONFIGURED ❌ |

---

## Coinsfera.com

| Field | Value |
|-------|-------|
| **URL** | `https://coinsfera.com/wp-json/wp/v2/` |
| **Username** | [TBD] |
| **Application Password** | [TBD] |
| **Status** | NOT CONFIGURED ❌ |

---

## Authentication Header Format

For REST API calls:
```
Authorization: Basic base64(USERNAME:APPLICATION_PASSWORD)
Content-Type: application/json
Accept: application/json
User-Agent: Mozilla/5.0
```

In Python:
```python
auth = requests.auth.HTTPBasicAuth("Dan", "NMwZ 1LyJ YgbE fUjs pUYn 4SoZ")
# OR for RankRay:
auth = requests.auth.HTTPBasicAuth("openclaw", "6Zz9 5gJL 8uyA QH4g RQDH GV1j")
```

---

## Common Errors & Fixes

| Error | Meaning | Fix |
|-------|---------|-----|
| `401 rest_cannot_create` | User lacks `create_posts` | Check user is Administrator, not Editor/Author |
| `401 rest_not_logged_in` | App Password invalid | Regenerate App Password in WP Users → Profile |
| `Connection reset` | Server blocked the IP | Rate limit / WAF. Wait 5 min, reduce request rate |
| `403` | REST API disabled | Check `/wp-admin/options-permalink.php` or plugins blocking API |
| `404` | Endpoint wrong | Check URL spelling, `/wp-json/wp/v2/` path |

---

**NEVER store credentials in scripts.** Use environment variables or .env files.
