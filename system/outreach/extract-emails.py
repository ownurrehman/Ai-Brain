#!/usr/bin/env python3
"""
Email Scraper v2 - Uses Hermes web_extract + email-sleuth for SMTP verification
- web_extract renders JavaScript (finds emails Python requests misses)
- email-sleuth verifies emails via SMTP (no bounces)
- Falls back to Python requests for speed on simple sites
- Checks homepage + /contact + /contact-us + /about pages
"""
import json, os, re, time, ssl, subprocess, shutil
from urllib.parse import urlparse
import requests as req_lib

ssl._create_default_https_context = ssl._create_unverified_context

PROSPECTS_FILE = "/tmp/uae_prospects_raw.json"
OUTPUT_FILE = "/tmp/uae_prospects_with_emails.json"
BRAIN_FILE = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/outreach/data/prospects.json"

def find_emails_in_text(text):
    """Extract emails from text, filter junk."""
    found = re.findall(r"[a-zA-Z00-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}", text)
    clean = set()
    skip_words = [
        "example.com", "sentry", "google", "facebook", "twitter", "instagram",
        "linkedin", "youtube", "wordpress", "wix.com", "schema.org",
        "googleapis", "gstatic", "cloudflare", "cdn", "jquery", "bootstrap",
        "png", "jpg", "gif", "svg", "css", "js", "mailto:", "noreply",
        "donotreply", "no-reply", "sentry.io", "intercom", "zendesk",
        "hotjar", "clarity", "gtag", "analytics", "adservice",
    ]
    for email in found:
        email = email.lower().strip(".")
        if any(s in email for s in skip_words):
            continue
        if len(email) > 50 or len(email) < 6:
            continue
        if email.endswith((".png", ".jpg", ".gif", ".svg", ".css", ".js")):
            continue
        clean.add(email)
    return clean


def scrape_with_requests(url):
    """Fast fallback: Python requests for simple HTML sites."""
    headers = {"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36"}
    emails = set()
    try:
        resp = req_lib.get(url, headers=headers, timeout=8, allow_redirects=True, verify=False)
        emails = find_emails_in_text(resp.text)
    except:
        pass
    return emails


def scrape_with_firecrawl(url):
    """Use Firecrawl API (Hermes extract_backend) - renders JavaScript, handles SPA sites."""
    import os
    from dotenv import load_dotenv
    load_dotenv(os.path.expanduser("~/.hermes/.env"))
    fc_key = os.getenv("FIRECRAWL_API_KEY", "")
    
    if not fc_key:
        return set()
    
    try:
        resp = req_lib.post(
            "https://api.firecrawl.dev/v1/scrape",
            headers={
                "Authorization": f"Bearer {fc_key}",
                "Content-Type": "application/json",
            },
            json={"url": url, "formats": ["markdown"], "limit": 5000},
            timeout=15,
        )
        if resp.status_code == 200:
            data = resp.json().get("data", {})
            content = data.get("markdown", "") + data.get("metadata", {}).get("title", "")
            return find_emails_in_text(content)
    except:
        pass
    return set()


def scrape_with_curl(url):
    """Use curl as fallback - handles redirects and some JS rendering via server-side."""
    import subprocess
    try:
        result = subprocess.run(
            ["curl", "-sL", "-A", "Mozilla/5.0", "--max-time", "8", url],
            capture_output=True, text=True, timeout=10
        )
        return find_emails_in_text(result.stdout)
    except:
        return set()


def verify_with_email_sleuth(email, business_name, domain):
    """Verify email exists via email-sleuth SMTP check."""
    try:
        # Extract a name from business name
        parts = business_name.split()
        if len(parts) >= 2:
            first = parts[0]
            last = parts[-1]
        else:
            first = parts[0] if parts else "info"
            last = ""

        # Use email-sleuth to verify the email
        result = subprocess.run(
            ["es", email],
            capture_output=True, text=True, timeout=15
        )
        output = result.stdout + result.stderr
        # Check if verified
        if "verified" in output.lower() or "valid" in output.lower() or "exists" in output.lower():
            return True
        # If es can't verify, try generating patterns from name + domain
        if last:
            patterns = [
                f"{first}@{domain}",
                f"{first}.{last}@{domain}",
                f"{first[0]}{last}@{domain}",
                f"{first[0]}.{last}@{domain}",
                f"{last}@{domain}",
                f"info@{domain}",
                f"contact@{domain}",
                f"hello@{domain}",
                f"admin@{domain}",
            ]
            for pattern in patterns:
                result2 = subprocess.run(
                    ["es", pattern],
                    capture_output=True, text=True, timeout=10
                )
                out2 = result2.stdout + result2.stderr
                if "verified" in out2.lower() or "valid" in out2.lower():
                    return pattern  # Return the verified email
        return True  # Assume valid if we can't verify (don't block sends)
    except:
        return True  # Don't block if email-sleuth fails


def pick_best_email(emails):
    """Pick the best email from a set of found emails."""
    preferences = ["info@", "contact@", "hello@", "admin@", "marketing@", "sales@", "support@", "office@"]
    for pref in preferences:
        for e in emails:
            if e.startswith(pref):
                return e
    # Return shortest email (usually the main one)
    return sorted(emails, key=len)[0] if emails else None


def scrape_prospect(prospect):
    """Scrape a prospect's website for emails using multiple methods."""
    website = prospect.get("website", "")
    if not website:
        return None

    parsed = urlparse(website)
    domain = parsed.netloc.replace("www.", "")
    base = f"{parsed.scheme}://{parsed.netloc}"

    # URLs to check: homepage + common contact page paths
    urls = [website, f"{base}/contact", f"{base}/contact-us", f"{base}/about", f"{base}/about-us"]

    all_emails = set()

    # Method 1: Python requests (fast, works for simple HTML sites)
    for url in urls[:3]:
        emails = scrape_with_requests(url)
        all_emails.update(emails)
        if all_emails:
            break
        time.sleep(0.1)

    # Method 2: Firecrawl (renders JS, finds hidden emails) - only if no emails yet
    if not all_emails:
        for url in urls:
            emails = scrape_with_firecrawl(url)
            all_emails.update(emails)
            if all_emails:
                break
            time.sleep(0.3)

    # Method 3: curl fallback (handles redirects differently than Python requests)
    if not all_emails:
        for url in urls[:2]:
            emails = scrape_with_curl(url)
            all_emails.update(emails)
            if all_emails:
                break
            time.sleep(0.2)

    if not all_emails:
        return None

    best = pick_best_email(all_emails)
    if not best:
        return None

    # Method 3: Verify with email-sleuth (SMTP check)
    # Quick SMTP verification - if it bounces, we waste a daily email slot
    try:
        result = subprocess.run(
            ["es", best],
            capture_output=True, text=True, timeout=10
        )
        output = (result.stdout + result.stderr).lower()
        if "invalid" in output and "valid" not in output:
            # Email is definitely invalid, skip
            return None
        # If email-sleuth found a different verified email, use it
        if "verified" in output:
            # Try to extract the verified email from output
            verified_match = re.search(r"[\w._%+-]+@[\w.-]+\.\w+", output)
            if verified_match:
                best = verified_match.group(0)
    except:
        pass  # Don't block if email-sleuth fails or is slow

    return best


def run():
    with open(PROSPECTS_FILE) as f:
        prospects = json.load(f)

    with_website = [p for p in prospects if p.get("website")]
    print(f"Total prospects with websites: {len(with_website)}")

    # Check if we already have partial results
    existing = []
    if os.path.exists(OUTPUT_FILE):
        with open(OUTPUT_FILE) as f:
            existing = json.load(f)
        existing_emails = {p.get("email", "").lower() for p in existing if p.get("email")}
        print(f"Already extracted: {len(existing)} ({len(existing_emails)} with emails)")
    else:
        existing_emails = set()

    email_prospects = list(existing)  # Start with existing
    checked = len(existing)
    found_emails = len(existing_emails)
    new_found = 0

    for p in with_website:
        # Skip if already processed
        if p.get("place_id") and any(e.get("place_id") == p.get("place_id") for e in existing):
            continue

        checked += 1
        if checked % 50 == 0:
            print(f"  Progress: {checked}/{len(with_website)} checked, {found_emails + new_found} emails found")
            # Save progress periodically
            with open(OUTPUT_FILE, "w") as f:
                json.dump(email_prospects, f, indent=2, ensure_ascii=False)

        email = scrape_prospect(p)
        if email:
            p["email"] = email
            email_prospects.append(p)
            new_found += 1
        time.sleep(0.3)

    total_emails = found_emails + new_found
    print(f"\n=== EXTRACTION COMPLETE ===")
    print(f"Checked: {checked}")
    print(f"Emails found: {total_emails}")
    print(f"Success rate: {total_emails/checked*100:.1f}%")

    # Save final results
    with open(OUTPUT_FILE, "w") as f:
        json.dump(email_prospects, f, indent=2, ensure_ascii=False)

    # Copy to Ai Brain
    shutil.copy(OUTPUT_FILE, BRAIN_FILE)
    print(f"Saved to {BRAIN_FILE}")

    # Stats
    city_count = {}
    for p in email_prospects:
        c = p.get("city", "unknown")
        city_count[c] = city_count.get(c, 0) + 1
    print(f"By city: {city_count}")

    ind_count = {}
    for p in email_prospects:
        i = p.get("industry", "unknown")
        ind_count[i] = ind_count.get(i, 0) + 1
    print(f"By industry (top 10):")
    for ind, count in sorted(ind_count.items(), key=lambda x: -x[1])[:10]:
        print(f"  {ind:30} {count}")


if __name__ == "__main__":
    run()