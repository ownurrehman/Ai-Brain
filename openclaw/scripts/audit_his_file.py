#!/usr/bin/env python3
"""
Audit the actual file: disavow 20 may 2026.txt
"""

import re

FILE_PATH = "/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Clients/Working - Clients/Geray - Crypto Sites/Coinsfera/Disavow/disavow 20 may 2026.txt"

# Extended list of high-authority domains to NEVER disavow
HIGH_AUTHORITY = {
    'reddit.com', 'harvard.edu', 'codecademy.com', 'cplusplus.com',
    'instructables.com', 'issuu.com', 'gumroad.com', 'collider.com',
    'godotengine.org', 'spreaker.com', 'letterboxd.com', 'wanelo.co',
    'ow.ly', 'bit.ly', 'tinyurl.com', 't.co', 'buff.ly',
    'google.com', 'youtube.com', 'blogspot.com', 'googleusercontent.com',
    'github.com', 'github.io', 'gitlab.io',
    'facebook.com', 'twitter.com', 'linkedin.com', 'instagram.com',
    'pinterest.com', 'tiktok.com', 'snapchat.com',
    'wikipedia.org', 'medium.com', 'tumblr.com', 'wordpress.com',
    'apple.com', 'microsoft.com', 'amazon.com', 'cloudflare.com',
    'bbc.com', 'bbc.co.uk', 'cnn.com', 'reuters.com', 'apnews.com',
    'forbes.com', 'bloomberg.com', 'nytimes.com', 'washingtonpost.com',
}

def is_gov_or_edu(domain):
    parts = domain.lower().split('.')
    if len(parts) >= 2:
        if parts[-1] in ('gov', 'edu', 'mil'):
            return True
        if len(parts) >= 3:
            if parts[-2] in ('go', 'ac', 'gov', 'edu'):
                return True
    return False

def audit():
    with open(FILE_PATH, 'r', encoding='utf-8', errors='replace') as f:
        lines = f.readlines()

    domains = []
    for i, raw_line in enumerate(lines, 1):
        line = raw_line.strip()
        if not line or line.startswith('#'):
            continue
        if line.startswith('domain:'):
            domains.append((i, line[7:].strip()))

    print("=" * 70)
    print("AUDIT: disavow 20 may 2026.txt")
    print("=" * 70)
    print(f"\nTotal domain entries: {len(domains)}")

    # 1. High-authority
    print("\n" + "=" * 70)
    print("1. HIGH-AUTHORITY DOMAINS")
    print("=" * 70)
    found = []
    for line_num, domain in domains:
        dom_lower = domain.lower()
        if dom_lower in HIGH_AUTHORITY:
            found.append((line_num, domain))
        else:
            for ha in HIGH_AUTHORITY:
                if dom_lower.endswith('.' + ha):
                    found.append((line_num, domain))
                    break
    if found:
        print(f"\n Found {len(found)} high-authority domains:")
        for line_num, domain in found[:30]:
            print(f"   Line {line_num}: {domain}")
        if len(found) > 30:
            print(f"   ... and {len(found) - 30} more")
    else:
        print("\n None found. ✓")

    # 2. Gov/Edu
    print("\n" + "=" * 70)
    print("2. GOV / EDU / AC DOMAINS")
    print("=" * 70)
    gov_edu = [(ln, d) for ln, d in domains if is_gov_or_edu(d)]
    if gov_edu:
        print(f"\n Found {len(gov_edu)} gov/edu/ac domains:")
        for line_num, domain in gov_edu:
            print(f"   Line {line_num}: {domain}")
    else:
        print("\n None found. ✓")

    # 3. IP addresses
    print("\n" + "=" * 70)
    print("3. IP ADDRESSES")
    print("=" * 70)
    ip_pattern = re.compile(r'^(\d{1,3}\.){3}\d{1,3}$')
    ips = [(ln, d) for ln, d in domains if ip_pattern.match(d)]
    if ips:
        print(f"\n Found {len(ips)} IP addresses:")
        for line_num, domain in ips:
            print(f"   Line {line_num}: {domain}")
    else:
        print("\n None found. ✓")

    # 4. Non-ASCII
    print("\n" + "=" * 70)
    print("4. NON-ASCII / IDN DOMAINS")
    print("=" * 70)
    non_ascii = []
    for line_num, domain in domains:
        try:
            domain.encode('ascii')
        except UnicodeEncodeError:
            non_ascii.append((line_num, domain))
    if non_ascii:
        print(f"\n Found {len(non_ascii)} non-ASCII domains (need Punycode):")
        for line_num, domain in non_ascii:
            print(f"   Line {line_num}: {domain}")
    else:
        print("\n None found. ✓")

    # 5. AWS
    print("\n" + "=" * 70)
    print("5. AWS / CLOUD INSTANCES")
    print("=" * 70)
    aws = [(ln, d) for ln, d in domains if 'ec2-' in d.lower() or 'amazonaws' in d.lower()]
    if aws:
        print(f"\n Found {len(aws)} AWS entries:")
        for line_num, domain in aws:
            print(f"   Line {line_num}: {domain}")
    else:
        print("\n None found. ✓")

    # Summary
    total_issues = len(found) + len(gov_edu) + len(ips) + len(non_ascii) + len(aws)
    print("\n" + "=" * 70)
    print("SUMMARY")
    print("=" * 70)
    print(f"\nTotal issues: {total_issues}")
    print(f"  High-authority: {len(found)}")
    print(f"  Gov/Edu: {len(gov_edu)}")
    print(f"  IP addresses: {len(ips)}")
    print(f"  Non-ASCII: {len(non_ascii)}")
    print(f"  AWS: {len(aws)}")

if __name__ == '__main__':
    audit()
