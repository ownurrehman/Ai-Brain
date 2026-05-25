#!/usr/bin/env python3
"""
Second-pass audit of the fixed disavow file
"""

import re

FILE_PATH = "/Users/sheikhown/.openclaw/media/inbound/disavow_20_may_2026---a455572f-90a3-4b28-aceb-d608930ef0da.txt"

# Extended list of high-authority domains to NEVER disavow
HIGH_AUTHORITY = {
    # Major platforms
    'reddit.com', 'harvard.edu', 'codecademy.com', 'cplusplus.com',
    'instructables.com', 'issuu.com', 'gumroad.com', 'collider.com',
    'godotengine.org', 'spreaker.com', 'letterboxd.com', 'wanelo.co',
    'ow.ly', 'bit.ly', 'tinyurl.com', 't.co', 'buff.ly',
    # Google properties
    'google.com', 'youtube.com', 'blogspot.com', 'googleusercontent.com',
    'github.com', 'github.io', 'gitlab.io',
    # Social
    'facebook.com', 'twitter.com', 'linkedin.com', 'instagram.com',
    'pinterest.com', 'tiktok.com', 'snapchat.com',
    # Major sites
    'wikipedia.org', 'medium.com', 'tumblr.com', 'wordpress.com',
    'apple.com', 'microsoft.com', 'amazon.com', 'cloudflare.com',
    # News
    'bbc.com', 'bbc.co.uk', 'cnn.com', 'reuters.com', 'apnews.com',
    'forbes.com', 'bloomberg.com', 'nytimes.com', 'washingtonpost.com',
    # Gov/Edu broad patterns
    'gov', 'edu', 'ac.uk', 'ac.jp', 'ac.in', 'ac.id', 'ac.th',
    'go.id', 'go.ke', 'go.jp', 'gob.mx', 'gob.pe', 'gob.ar',
    # URL shorteners
    'bit.do', 'shorte.st', 'adf.ly', 'goo.gl', 'ow.ly', 'tiny.cc',
    't2m.io', 'rb.gy', 'is.gd', 'cli.re',
}

# Known PBN / spam farm patterns that are OK to keep but let's flag the scale
PBN_PATTERNS = [
    'seo-anomaly-', 'bhs-links-', 'links-bhs-', 'blogspot.com',
    'lowescouponn.com', 'tearosediner.net', 'iamarrows.com',
    'lucialpiazzale.com', 'cavandoragh.org', 'almoheet-travel.com',
    'wpsuo.com', 'theglensecret.com', 'yousher.com', 'huicopper.com',
    'bearsfanteamshop.com', 'raidersfanteamshop.com',
]

def audit_file():
    with open(FILE_PATH, 'r', encoding='utf-8', errors='replace') as f:
        lines = f.readlines()

    domains = []
    issues = []
    warnings = []
    
    for i, raw_line in enumerate(lines, 1):
        line = raw_line.strip()
        if not line or line.startswith('#'):
            continue
        if not line.startswith('domain:'):
            issues.append(f"Line {i}: Not a domain entry → {line[:60]}")
            continue
        
        domain = line[7:].strip().lower()
        domains.append((i, domain))
    
    print("=" * 70)
    print("SECOND-PASS AUDIT REPORT")
    print("=" * 70)
    print(f"\nTotal domain entries: {len(domains)}")
    
    # 1. Check for remaining high-authority / gov / edu
    print("\n" + "=" * 70)
    print("1. HIGH-AUTHORITY / GOV / EDU CHECK")
    print("=" * 70)
    high_auth_found = []
    gov_edu_found = []
    for line_num, domain in domains:
        parts = domain.split('.')
        # Check against explicit list
        if domain in HIGH_AUTHORITY or any(domain.endswith('.' + ha) for ha in HIGH_AUTHORITY if '.' in ha):
            high_auth_found.append((line_num, domain))
        # Check TLD patterns
        if '.gov' in domain or '.edu' in domain or '.go.' in domain or '.ac.' in domain:
            gov_edu_found.append((line_num, domain))
    
    if high_auth_found:
        print(f"\n Found {len(high_auth_found)} high-authority domains:")
        for line_num, domain in high_auth_found[:30]:
            print(f"   Line {line_num}: {domain}")
        if len(high_auth_found) > 30:
            print(f"   ... and {len(high_auth_found) - 30} more")
    else:
        print("\n No high-authority domains found. ✓")
    
    if gov_edu_found:
        print(f"\n Found {len(gov_edu_found)} gov/edu/ac domains:")
        for line_num, domain in gov_edu_found[:30]:
            print(f"   Line {line_num}: {domain}")
        if len(gov_edu_found) > 30:
            print(f"   ... and {len(gov_edu_found) - 30} more")
    else:
        print("\n No gov/edu domains found. ✓")
    
    # 2. Check for IP addresses
    print("\n" + "=" * 70)
    print("2. IP ADDRESS CHECK")
    print("=" * 70)
    ip_pattern = re.compile(r'^(\d{1,3}\.){3}\d{1,3}$')
    ip_found = [(ln, d) for ln, d in domains if ip_pattern.match(d)]
    if ip_found:
        print(f"\n Found {len(ip_found)} IP addresses:")
        for line_num, domain in ip_found:
            print(f"   Line {line_num}: {domain}")
    else:
        print("\n No IP addresses found. ✓")
    
    # 3. Check for non-ASCII / IDN domains
    print("\n" + "=" * 70)
    print("3. NON-ASCII / IDN CHECK")
    print("=" * 70)
    idn_found = []
    for line_num, domain in domains:
        try:
            domain.encode('ascii')
        except UnicodeEncodeError:
            idn_found.append((line_num, domain))
    if idn_found:
        print(f"\n Found {len(idn_found)} non-ASCII domains (need Punycode):")
        for line_num, domain in idn_found:
            print(f"   Line {line_num}: {domain}")
    else:
        print("\n All domains are ASCII-safe. ✓")
    
    # 4. Check for remaining major platform subdomains
    print("\n" + "=" * 70)
    print("4. MAJOR PLATFORM SUBDOMAINS (Review these)")
    print("=" * 70)
    platform_patterns = [
        ('.netlify.app', 'Netlify'),
        ('.vercel.app', 'Vercel'),
        ('.herokuapp.com', 'Heroku'),
        ('.web.app', 'Firebase'),
        ('.github.io', 'GitHub Pages'),
        ('.gitlab.io', 'GitLab Pages'),
        ('.azurewebsites.net', 'Azure'),
        ('.blogspot.com', 'Blogger'),
        ('.wordpress.com', 'WordPress.com'),
        ('.webs.com', 'Webs'),
        ('.free.fr', 'Free.fr'),
        ('.weebly.com', 'Weebly'),
        ('.wixsite.com', 'Wix'),
        ('.squarespace.com', 'Squarespace'),
        ('.hatenablog.com', 'Hatena'),
        ('.altervista.org', 'Altervista'),
        ('.nation2.com', 'Nation2'),
        ('.blog5.net', 'Blog5'),
        ('.unblog.fr', 'Unblog'),
        ('.phorum.pl', 'Phorum'),
        ('.xsrv.jp', 'XServer'),
        ('.sakura.ne.jp', 'Sakura'),
        ('.webd.pro', 'Webd'),
        ('.x10host.com', 'X10hosting'),
        ('.worldsecuresystems.com', 'NetSuite'),
        ('.page.tl', 'Page.tl'),
        ('.booklikes.com', 'Booklikes'),
        ('.thebittimes.com', 'TheBitTimes'),
    ]
    
    for pattern, name in platform_patterns:
        matches = [(ln, d) for ln, d in domains if pattern in d]
        if matches:
            print(f"\n  {name} ({len(matches)} entries):")
            for line_num, domain in matches[:5]:
                print(f"    Line {line_num}: {domain}")
            if len(matches) > 5:
                print(f"    ... and {len(matches) - 5} more")
    
    # 5. Check for AWS EC2 instances
    print("\n" + "=" * 70)
    print("5. AWS / CLOUD INSTANCES")
    print("=" * 70)
    aws_pattern = re.compile(r'ec2-.*\.amazonaws\.com|compute\.amazonaws\.com')
    aws_found = [(ln, d) for ln, d in domains if aws_pattern.search(d)]
    if aws_found:
        print(f"\n Found {len(aws_found)} AWS EC2 entries:")
        for line_num, domain in aws_found:
            print(f"   Line {line_num}: {domain}")
        print("   ⚠️ These should be URL-level disavows, not domain-level")
    else:
        print("\n No AWS entries found.")
    
    # 6. Subdomain redundancy check
    print("\n" + "=" * 70)
    print("6. SUBDOMAIN REDUNDANCY (Root + Sub both present)")
    print("=" * 70)
    from collections import defaultdict
    root_map = defaultdict(list)
    for line_num, domain in domains:
        parts = domain.split('.')
        if len(parts) >= 3:
            root = '.'.join(parts[-2:])
            root_map[root].append((line_num, domain))
    
    redundancy_found = []
    for root, subs in root_map.items():
        if len(subs) > 1:
            # Check if any entry IS the root domain
            root_entries = [(ln, d) for ln, d in subs if d == root]
            if root_entries:
                redundancy_found.append((root, root_entries, subs))
    
    if redundancy_found:
        print(f"\n Found {len(redundancy_found)} root domains with both root + subs listed:")
        for root, root_entries, subs in redundancy_found:
            print(f"\n   Root '{root}' has {len(subs)} entries:")
            for ln, d in subs:
                print(f"      Line {ln}: {d}")
    else:
        print("\n No subdomain redundancy found. ✓")
    
    # 7. Check for potential .com / .org / .net broad blocks (shouldn't exist)
    print("\n" + "=" * 70)
    print("7. BROAD TLD BLOCKS")
    print("=" * 70)
    broad_blocks = [(ln, d) for ln, d in domains if d in ('com', 'org', 'net', 'edu', 'gov', 'io', 'co', 'info')]
    if broad_blocks:
        print(f"\n ⚠️ Found {len(broad_blocks)} dangerous broad TLD blocks:")
        for line_num, domain in broad_blocks:
            print(f"   Line {line_num}: {domain}")
    else:
        print("\n No dangerous broad TLD blocks. ✓")
    
    # 8. PBN scale check
    print("\n" + "=" * 70)
    print("8. PBN / SPAM FARM SCALE")
    print("=" * 70)
    for pattern in PBN_PATTERNS:
        matches = [(ln, d) for ln, d in domains if pattern in d]
        if matches:
            print(f"\n  '{pattern}': {len(matches)} entries")
    
    # Summary
    print("\n" + "=" * 70)
    print("SUMMARY")
    print("=" * 70)
    total_issues = len(high_auth_found) + len(gov_edu_found) + len(ip_found) + len(idn_found) + len(aws_found)
    if redundancy_found:
        total_issues += len(redundancy_found)
    if broad_blocks:
        total_issues += len(broad_blocks)
    
    print(f"\nTotal issues/warnings: {total_issues}")
    print(f"  - High-authority domains: {len(high_auth_found)}")
    print(f"  - Gov/Edu domains: {len(gov_edu_found)}")
    print(f"  - IP addresses: {len(ip_found)}")
    print(f"  - Non-ASCII/IDN: {len(idn_found)}")
    print(f"  - AWS EC2 instances: {len(aws_found)}")
    print(f"  - Subdomain redundancies: {len(redundancy_found)}")
    print(f"  - Broad TLD blocks: {len(broad_blocks)}")
    
    if total_issues == 0:
        print("\n ✅ File looks clean! No critical issues found.")
    
    return {
        'high_auth': high_auth_found,
        'gov_edu': gov_edu_found,
        'ips': ip_found,
        'idns': idn_found,
        'aws': aws_found,
        'redundancy': redundancy_found,
        'broad': broad_blocks,
    }

if __name__ == '__main__':
    audit_file()
