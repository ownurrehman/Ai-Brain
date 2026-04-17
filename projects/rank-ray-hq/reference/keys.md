# RankRay Keys (Sanitized Reference)

This file is intentionally sanitized.  
Do not store real secrets in this file.

## Security Policy

- Keep real values only in local env files:
  - `rankray-hq-backend/.env`
  - `rankray-hq-frontend/.env.development.local`
- This file (`docs/reference/keys.md`) is a safe template; live secrets stay in env only.
- If any secret was ever written in docs/history, rotate it immediately.

## Position Tracking (SerpBear) Setup

RankRay supports two modes:

1. Embedded mode (recommended for this app)

- `SERPBEAR_BASE_URL=internal://rankray-serp`
- `SERPBEAR_API_TOKEN=embedded`

1. External SerpBear endpoint mode

- `SERPBEAR_BASE_URL=https://<your-serpbear-host>`
- `SERPBEAR_API_TOKEN=<your-serpbear-api-token>`

### ScrapingRobot mapping (sanitized)

- `SCRAPINGROBOT_API_KEY=<set-in-serpbear-or-private-config>`
- `SCRAPINGROBOT_TEST_URL=https://api.scrapingrobot.com/?token=<redacted>&url=https://www.bing.com`

Important:

- Do not use ScrapingRobot URL as `SERPBEAR_BASE_URL`.
- `SERPBEAR_BASE_URL` must be a SerpBear-compatible API host.
- Keep provider tokens in local env only.

## Backend env keys (template)

- `DATABASE_URL=<local-or-managed-db-url>`
- `JWT_SECRET=<strong-random-secret>`
- `JWT_EXPIRES_IN=24h`
- `PORT=3000`
- `SERVER_PUBLIC_URL=<backend-url>`
- `SERVER_PUBLIC_URL_FALLBACK=<optional-dev-url>`
- `FRONTEND_URL=<frontend-url>`
- `GOOGLE_CLIENT_ID=<google-oauth-client-id>`
- `GOOGLE_CLIENT_SECRET=<google-oauth-client-secret>`
- `ENCRYPTION_KEY=<32-char-secret>`
- `SEO_WP_MODE=mock|live`

## Frontend env keys (template)

- `VITE_API_URL=/api`

## SEO / Research Providers

- `SEMRUSH_API_KEY=<semrush-key>`
- `BRAVE_SEARCH_API_KEY=<brave-key>`

## WordPress Publishing

- `WORDPRESS_SITE_URL=<https://your-site.com>`
- `WORDPRESS_USERNAME=<wp-username>`
- `WORDPRESS_APP_PASSWORD=<wp-application-password>`

## AI Providers

- `OPENAI_API_KEY=<openai-key>`
- `ANTHROPIC_API_KEY=<anthropic-key>`
- `GEMINI_API_KEY=<gemini-key>`

## Quick Validation Checklist

- Backend starts with no missing env errors.
- SEO -> Connections shows Position Tracking connected.
- GSC OAuth redirect works with your configured public URL.
- WordPress publish works (or `SEO_WP_MODE=mock` for local testing).
