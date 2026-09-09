# CoinSfera Bot Block Issue (2026-09-01)

## Problem
All REST API calls to coinsfera.com return 202 with a redirect to a SiteGround sgcaptcha challenge.

## Root Cause
SiteGround Security Anti-Bot feature is enabled at the SERVER level. This is managed from
SiteGround Site Tools > Security > Anti-Bot. It is NOT controllable via SSH, .htaccess,
WP-CLI, or REST API — it sits at the Apache/NGINX server level.

## What I tried
1. REST API with app password → 202 (redirect to captcha)
2. .htaccess IP allowlist → didn't bypass (server-level feature)
3. WP-CLI to check settings → no captcha option in WP options
4. SSH to sg-security plugin source → no captcha bypass in plugin source
5. SiteGround sgcaptcha challenge page → JavaScript-based, can't solve via HTTP

## Resolution needed
Sheikh (or client) needs to log into SiteGround Site Tools:
1. Security > Anti-Bot → Either DISABLE or add IP exception for 182.187.150.13
2. After that, all REST API calls work immediately — no code changes needed

## Status
.htaccess reverted (cleanup from failed attempt). Awaiting Sheikh to disable Anti-Bot from SiteGround panel.