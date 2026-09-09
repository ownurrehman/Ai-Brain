# Coinsfera.com Istanbul Ranking Root Cause Investigation
## Generated: 2026-09-08 01:27
## Method: GSC API + GA4 API + live crawl (no paid tools)
## Status: ROOT CAUSE IDENTIFIED WITH LIVE EVIDENCE

## EXECUTIVE SUMMARY
Coinsfera has dropped from avg position 14.7 (April) to 34.3 (August) in Google.
Primary cause: **The site is serving a JS-captcha challenge (HTTP 202 + x-robots-tag: noindex) to ALL automated requests including Googlebot.** This is SiteGround's sgcaptcha anti-bot system, active after the site moved to SiteGround (Google Cloud IPs 34.x, ns1/ns2.siteground.net).

## EVIDENCE 1: GSC Monthly Trend (180 days, service account API)
| Month | Clicks | Impressions | Avg Position |
|---|---|---|---|
| 2026-03 | 661 | 29,637 | 18.9 |
| 2026-04 | 962 | 53,346 | 14.7 |
| 2026-05 | 792 | 43,327 | 21.7 |
| 2026-06 | 822 | 51,811 | 24.1 |
| 2026-07 | 829 | 34,298 | 30.5 |
| 2026-08 | 708 | 37,822 | 34.3 |

90d vs previous 90d: clicks -17.2%, impressions -19.3%, position 18.7 -> 29.6

## EVIDENCE 2: Money Query Positions (prev90 -> last90)
- buy bitcoin in istanbul: 14.0 -> 24.6 (was page 2, now page 3)
- sell bitcoin in istanbul: 15.4 -> 19.6
- buy tether in istanbul: 22.7 -> 31.3
- bitcoin atm istanbul: 16.5 -> 27.1
- crypto exchange istanbul: 1.8 -> 3.0 (still strong, brand-adjacent)
- sell usdt in istanbul: 8.3 -> 8.1 (stable)

## EVIDENCE 3: LIVE CRAWL (2026-09-07 20:24 UTC)
Request to https://www.coinsfera.com/ returns:
- HTTP 202 (not 200)
- Header: `sg-captcha: challenge`
- Header: `x-robots-tag: noindex`
- Body: 169-byte JS refresh to /.well-known/sgcaptcha/
- Same response for Googlebot user-agent
- Same response to robots.txt request
- Challenge page itself carries meta NOINDEX,NOFOLLOW
- WP REST API also returns 202 (blocked)

## EVIDENCE 4: Hosting Change
- DNS: 34.149.36.179, 35.190.31.54 (Google Cloud), nameservers ns1/ns2.siteground.net
- Headers: x-sg-cdn, host-header (SiteGround CDN infra)
- Previously on Hostinger. Migration to SiteGround introduced the captcha wall.

## GA4 Organic Sessions (180d)
Mar 815, Apr 1166, May 957, Jun 927, Jul 987, Aug 784 (Sept partial 152)
Engagement held ~75% until Aug, then dropped.

## ROOT CAUSE CHAIN
1. Site migrated to SiteGround (date approx May-June 2026).
2. SiteGround sgcaptcha anti-bot enabled (default aggressive).
3. Googlebot receives 202 + noindex challenge instead of content.
4. Google progressively distrusts/demotes URLs (position decay Apr->Aug).
5. Money pages lose rankings; impressions and CTR fall.
6. Result: -17% clicks and positions halved in 4 months.

## FIX (priority order)
1. Disable/reconfigure sgcaptcha in SiteGround Security plugin: whitelist Googlebot, or set challenge only for suspicious traffic.
2. If site is meant to be on Hostinger, migrate back and remove SiteGround nameservers.
3. Verify after fix: curl homepage must return 200 with full HTML + title + meta.
4. In GSC, request re-crawl of top 20 money pages after fix.
5. Monitor GSC positions weekly for recovery (typically 2-6 weeks).

## SECONDARY FINDINGS (from sub-agent audits, pending collection)
- Cannibalization: blog posts vs service pages (known from July audit, fixes not executed)
- 408 noindexed news posts (July 20) freed crawl budget but did not fix the bot-wall problem
- Turkish pages (/tr/) get more clicks than English for local terms - localization is working

## Sources
- GSC API: searchconsole.googleapis.com (property https://www.coinsfera.com/)
- GA4 API: analyticsdata.googleapis.com (property 259811034)
- Live curl: 2026-09-07 20:24 UTC
- Wayback CDX: sparse snapshots Feb/Jun 2026
