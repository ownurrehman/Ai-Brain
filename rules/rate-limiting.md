> **Parent Hub:** [[rules/INDEX|📜 Operating Rules Hub]] · [[INDEX|🧠 Ai Brain]]

# Rate Limiting & Connection Safety Rules
# All Sites

## Core Principle
**Never burn bridges.** Always start slow, adapt to server response, and back off on ANY error.

---

## Default Safe Settings

| Parameter | Value | Why |
|-----------|-------|-----|
| Delay between requests | 0.5 - 1.0 seconds | Prevents 429s and IP blocks |
| Timeout per request | 20 seconds | Allows for slow servers; abort if hung |
| Max retries on failure | 3 | Each with exponential back-off |
| Batch size per run | 1 blog / burst | Push 1, verify, push next |
| Daily push cap | 5-10 blogs | Respect server limits |
| Connection header | `Connection: close` | Prevents stale connections over long runs |

---

## Exponential Back-Off

If a request fails:
1. Wait 2 seconds, retry
2. Wait 4 seconds, retry
3. Wait 8 seconds, retry
4. Stop : alert user, do NOT retry blindly

---

## Connection Reset Errors (`ConnectionResetError`, `RemoteDisconnected`)

These mean the server is actively dropping the TCP connection. Causes:
- WAF (Wordfence, Sucuri) blocking POSTs
- Rate limiter triggering
- DNS/Cloudflare IP reputation filter

**Immediate actions:**
1. Stop all requests
2. Switch to browser automation (wp-admin manual) as fallback
3. If REST API recovers after 10-15 minutes, resume at lower rate (2s delays)

---

## DNS Resolution Issues

If DNS resolves to wrong IP or server unreachable:
1. Check `dig tonicphysio.com` or `nslookup`
2. Compare with server IP on hosting panel
3. If IPs changed, flush local DNS cache and retry

---

## Browser Automation Fallback

When REST API is blocked:
1. Open `https://tonicphysio.com/wp-login.php`
2. Login as Dan with web password
3. Navigate to Posts -> Add New
4. Paste content block by block
5. Save as draft -> wait for user review
6. Repeat for each blog

**Do NOT automate browser for bulk push** : it's fragile and slow. Use only as fallback.

---

## Testing Before Bulk Push

Always run this FIRST, before any batch:
```python
# Load credentials from master-env.env before running
import requests
url = "https://{site}/wp-json/wp/v2/posts"
auth = (WP_USER, WP_REST_API_KEY)  # from master-env.env
payload = {"title": "CONNECTION TEST", "content": "test", "status": "draft"}
resp = requests.post(url, json=payload, auth=auth, timeout=20)
assert resp.status_code in (200, 201), f"Connection failed: {resp.status_code}"
```

If the test fails, DO NOT proceed with batch.

---

## Per-Site Rate Limits

| Site | Safe Delay | Notes |
|------|-----------|-------|
| TonicPhysio | 1.0s | Server is on shared hosting, strict |
| RankRay | 0.5s | Dedicated, faster, more forgiving |
| TeamMotorcycle | 1.0s | [TBD] |
| KhanLLP | 1.0s | [TBD] |
| Coinsfera | 1.0s | [TBD] |

---

**Always save a log of posted IDs** so you know what was sent and can resume if interrupted.
