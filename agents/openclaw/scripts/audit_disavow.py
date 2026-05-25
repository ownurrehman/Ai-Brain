#!/usr/bin/env python3
"""
Disavow File Auditor
Checks for: duplicates, syntax errors, TLD blocks, broad matches, malformed entries
"""

import re
import sys
from collections import Counter

FILE_PATH = "/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Clients/Working - Clients/Geray - Crypto Sites/Coinsfera/Disavow/disavow 20 may 2026 copy.txt"

# Common TLDs that should NEVER be disavowed at domain level
DANGEROUS_TLDS = {
    'com', 'org', 'net', 'edu', 'gov', 'io', 'co', 'info', 'biz', 'us', 'uk',
    'de', 'fr', 'jp', 'cn', 'ru', 'in', 'au', 'ca', 'eu', 'nl', 'it', 'es',
    'pl', 'br', 'mx', 'ar', 'cl', 'co.uk', 'com.au', 'co.in', 'co.jp',
    'blogspot.com', 'wordpress.com', 'github.io', 'herokuapp.com',
    'webs.com', 'webnode.com', 'wixsite.com', 'squarespace.com',
    'google.com', 'youtube.com', 'facebook.com', 'twitter.com',
    'linkedin.com', 'instagram.com', 'pinterest.com', 'reddit.com',
    'wikipedia.org', 'medium.com', 'tumblr.com', 'blogspot.com',
    'apple.com', 'microsoft.com', 'amazon.com', 'cloudflare.com',
    'akamaized.net', 'fbcdn.net', 'ggpht.com', 'googleusercontent.com',
    'appspot.com', 'azurewebsites.net', 'vercel.app', 'netlify.app',
    'github.io', 'gitlab.io', 'web.app', 'firebaseapp.com',
    # IDN TLDs that are actually domains
    'рф', '中国', '日本',
}

# Known safe patterns (subdomains of shared platforms that ARE spam but specific)
# These are fine to disavow as domain:xxx.blogspot.com etc.
SAFE_SUBDOMAIN_PATTERNS = [
    'blogspot.com', 'wordpress.com', 'webs.com', 'webnode.com',
    'wixsite.com', 'squarespace.com', 'weebly.com', 'hatenablog.com',
    'tinyblogging.com', 'edublogs.org', 'blog5.net', 'page.tl',
    'de.tl', 'nation2.com', 'fotosdefrases.com', 'yousher.com',
    'theburnward.com', 'theglensecret.com', 'huicopper.com',
    'lowescouponn.com', 'trexgame.net', 'tearosediner.net',
    'cavandoragh.org', 'raidersfanteamshop.com', 'bearsfanteamshop.com',
    'timeforchangecounselling.com', 'image-perth.org', 'iamarrows.com',
    'almoheet-travel.com', 'lucialpiazzale.com', 'wpsuo.com',
    'fotosdefrases.com', 'booklikes.com', 'ezblogz.com', 'link4blogs.com',
    'bloginder.com', 'full-design.com', 'newsbloger.com', 'get-blogging.com',
    'educationalimpactblog.com', 'bleepblogs.com', 'blogstival.com',
    'digiblogbox.com', 'pointblog.net', 'dailyblogzz.com', 'review-blogger.com',
    'blogprodesign.com', 'targetblogs.com', 'blogvivi.com', 'blogocial.com',
    'blogdal.com', 'link4blogs.com', 'websrvcs.com', 'altervista.org',
]

def is_valid_domain(domain):
    """Basic domain validation"""
    if not domain or len(domain) > 253:
        return False
    # Must have at least one dot (except localhost which we don't want)
    if '.' not in domain:
        return False
    # No spaces
    if ' ' in domain:
        return False
    # No consecutive dots
    if '..' in domain:
        return False
    # Must not start or end with hyphen
    parts = domain.split('.')
    for part in parts:
        if part.startswith('-') or part.endswith('-'):
            return False
        if not part:
            return False
    return True

def get_base_domain(domain):
    """Extract the base domain (last 2 parts, or last 3 if it's a known ccSLD)"""
    parts = domain.lower().split('.')
    if len(parts) >= 3 and parts[-2] in ('co', 'com', 'org', 'net', 'gov', 'edu', 'blogspot'):
        return '.'.join(parts[-3:])
    if len(parts) >= 2:
        return '.'.join(parts[-2:])
    return domain

def audit_disavow_file(filepath):
    with open(filepath, 'r', encoding='utf-8', errors='replace') as f:
        lines = f.readlines()

    domains = []
    urls = []
    comments = []
    issues = []
    line_issues = []
    
    domain_entries = []  # (line_num, domain, raw_line)
    url_entries = []     # (line_num, url, raw_line)
    
    in_url_section = False
    
    for i, raw_line in enumerate(lines, 1):
        line = raw_line.strip()
        
        # Empty lines
        if not line:
            continue
            
        # Comments
        if line.startswith('#'):
            comments.append((i, line))
            if 'url' in line.lower():
                in_url_section = True
            continue
        
        # Check for malformed lines (no domain: or url: prefix)
        if not line.startswith('domain:') and not line.startswith('url:'):
            # Could be a URL without protocol, or just a domain name
            if '.' in line and ' ' not in line:
                issues.append(f"Line {i}: Missing 'domain:' or 'url:' prefix → '{line[:60]}'")
                # Treat as potential domain for analysis
                domain_entries.append((i, line, raw_line))
            else:
                issues.append(f"Line {i}: Unrecognized format → '{line[:60]}'")
            continue
        
        if line.startswith('domain:'):
            in_url_section = False
            domain = line[7:].strip()  # Remove 'domain:' prefix
            domain_entries.append((i, domain, raw_line))
            
        elif line.startswith('url:'):
            in_url_section = True
            url = line[4:].strip()
            url_entries.append((i, url, raw_line))
    
    # ===== ANALYZE DOMAINS =====
    print("=" * 60)
    print("DISAVOW FILE AUDIT REPORT")
    print("=" * 60)
    print(f"\nTotal lines: {len(lines)}")
    print(f"Domain entries: {len(domain_entries)}")
    print(f"URL entries: {len(url_entries)}")
    print(f"Comments: {len(comments)}")
    
    # 1. DUPLICATES
    print("\n" + "=" * 60)
    print("1. DUPLICATE CHECK")
    print("=" * 60)
    domain_counts = Counter(d[1].lower() for d in domain_entries)
    duplicates = {d: c for d, c in domain_counts.items() if c > 1}
    if duplicates:
        print(f"\n Found {len(duplicates)} duplicate domains:")
        for dom, count in sorted(duplicates.items(), key=lambda x: -x[1]):
            lines_where = [e[0] for e in domain_entries if e[1].lower() == dom]
            print(f"   '{dom}' — appears {count} times (lines: {lines_where})")
    else:
        print("\n No duplicates found.")
    
    # 2. SYNTAX ERRORS
    print("\n" + "=" * 60)
    print("2. SYNTAX ERRORS")
    print("=" * 60)
    syntax_issues = []
    for line_num, domain, raw in domain_entries:
        # Check for spaces in domain
        if ' ' in domain:
            syntax_issues.append(f"Line {line_num}: Space in domain → '{domain}'")
        # Check for protocol included
        if '://' in domain:
            syntax_issues.append(f"Line {line_num}: Protocol in domain entry → '{domain[:60]}'")
        # Check for paths
        if '/' in domain:
            syntax_issues.append(f"Line {line_num}: Path in domain entry → '{domain[:60]}'")
        # Check for wildcards (Google doesn't support wildcard in domain:)
        if '*' in domain:
            syntax_issues.append(f"Line {line_num}: Wildcard in domain entry → '{domain}'")
        # Invalid characters
        if re.search(r'[^a-zA-Z0-9._-]', domain):
            # Allow IDN/punycode
            pass  # Could be unicode
        # Check valid domain structure
        if not is_valid_domain(domain):
            syntax_issues.append(f"Line {line_num}: Invalid domain format → '{domain}'")
    
    if syntax_issues:
        print(f"\n Found {len(syntax_issues)} syntax issues:")
        for issue in syntax_issues[:30]:
            print(f"   {issue}")
        if len(syntax_issues) > 30:
            print(f"   ... and {len(syntax_issues) - 30} more")
    else:
        print("\n No syntax errors found.")
    
    # 3. DANGEROUS BROAD MATCHES
    print("\n" + "=" * 60)
    print("3. DANGEROUS / TOO-BROAD DISAVOWS")
    print("=" * 60)
    dangerous = []
    for line_num, domain, raw in domain_entries:
        dom_lower = domain.lower()
        parts = dom_lower.split('.')
        
        # Check for direct TLD match (e.g., domain:com, domain:org)
        if len(parts) == 1 and parts[0] in DANGEROUS_TLDS:
            dangerous.append((line_num, domain, "DISAVOWING ENTIRE TLD!"))
        
        # Check for 2-part domains that are actually just TLDs
        if len(parts) == 2:
            if parts[0] in DANGEROUS_TLDS or parts[1] in DANGEROUS_TLDS:
                # e.g., domain:com.uk or something weird
                pass
        
        # Check if domain is just a TLD like domain:co.uk
        base = get_base_domain(domain)
        if base in DANGEROUS_TLDS:
            dangerous.append((line_num, domain, f"Base domain '{base}' is a major TLD/platform"))
        
        # Check for very short first-level domains that look suspicious
        # e.g., domain:a.com, domain:b.org (could be intentional but flag it)
        if len(parts) >= 2 and len(parts[0]) == 1 and parts[1] in ('com', 'org', 'net', 'co'):
            dangerous.append((line_num, domain, "Single-char + major TLD — verify this is intentional"))
    
    if dangerous:
        print(f"\n⚠️ Found {len(dangerous)} potentially dangerous entries:")
        for line_num, domain, reason in dangerous[:30]:
            print(f"   Line {line_num}: '{domain}' — {reason}")
        if len(dangerous) > 30:
            print(f"   ... and {len(dangerous) - 30} more")
    else:
        print("\n No dangerous broad matches found.")
    
    # 4. SUBDOMAIN vs ROOT DOMAIN ANALYSIS
    print("\n" + "=" * 60)
    print("4. SUBDOMAIN vs ROOT DOMAIN ANALYSIS")
    print("=" * 60)
    subdomain_issues = []
    root_domains = {}
    for line_num, domain, raw in domain_entries:
        parts = domain.lower().split('.')
        if len(parts) >= 3:
            root = '.'.join(parts[-2:])
            if root not in root_domains:
                root_domains[root] = []
            root_domains[root].append((line_num, domain))
    
    # Find roots with many subdomains disavowed
    multi_subdomain_roots = {r: subs for r, subs in root_domains.items() if len(subs) >= 5}
    if multi_subdomain_roots:
        print(f"\n Found {len(multi_subdomain_roots)} root domains with 5+ subdomains disavowed:")
        print("   (Consider switching to root domain disavow to save lines)")
        for root, subs in sorted(multi_subdomain_roots.items(), key=lambda x: -len(x[1]))[:20]:
            print(f"\n   '{root}' — {len(subs)} subdomains:")
            for line_num, sub in subs[:5]:
                print(f"      Line {line_num}: {sub}")
            if len(subs) > 5:
                print(f"      ... and {len(subs) - 5} more")
    else:
        print("\n No root domains with excessive subdomains.")
    
    # 5. IP ADDRESSES
    print("\n" + "=" * 60)
    print("5. IP ADDRESS ENTRIES")
    print("=" * 60)
    ip_entries = []
    ip_pattern = re.compile(r'^(\d{1,3}\.){3}\d{1,3}$')
    for line_num, domain, raw in domain_entries:
        if ip_pattern.match(domain):
            ip_entries.append((line_num, domain))
    if ip_entries:
        print(f"\n Found {len(ip_entries)} IP address entries:")
        for line_num, ip in ip_entries[:20]:
            print(f"   Line {line_num}: {ip}")
        if len(ip_entries) > 20:
            print(f"   ... and {len(ip_entries) - 20} more")
    else:
        print("\n No IP address entries found.")
    
    # 6. URL SECTION ANALYSIS
    print("\n" + "=" * 60)
    print("6. URL SECTION ANALYSIS")
    print("=" * 60)
    if url_entries:
        print(f"\n Found {len(url_entries)} URL entries.")
        url_issues = []
        for line_num, url, raw in url_entries:
            if not url.startswith('http://') and not url.startswith('https://'):
                url_issues.append(f"Line {line_num}: URL missing protocol → '{url[:60]}'")
        if url_issues:
            print(f"\n URL issues ({len(url_issues)}):")
            for issue in url_issues[:10]:
                print(f"   {issue}")
        else:
            print("\n All URLs have proper protocols.")
    else:
        print("\n No URL entries found.")
    
    # 7. CLEANUP: Generate cleaned file
    print("\n" + "=" * 60)
    print("7. GENERATING CLEANED FILE")
    print("=" * 60)
    
    # Remove duplicates (keep first occurrence)
    seen_domains = set()
    seen_urls = set()
    cleaned_domains = []
    cleaned_urls = []
    removed_count = 0
    
    for line_num, domain, raw in domain_entries:
        dom_lower = domain.lower().strip()
        if dom_lower in seen_domains:
            removed_count += 1
            continue
        seen_domains.add(dom_lower)
        cleaned_domains.append(raw)
    
    for line_num, url, raw in url_entries:
        url_lower = url.lower().strip()
        if url_lower in seen_urls:
            removed_count += 1
            continue
        seen_urls.add(url_lower)
        cleaned_urls.append(raw)
    
    print(f"\n Removed {removed_count} duplicate entries.")
    print(f" Cleaned domains: {len(cleaned_domains)}")
    print(f" Cleaned URLs: {len(cleaned_urls)}")
    
    # Write cleaned file
    output_path = filepath.replace('.txt', '_CLEANED.txt')
    with open(output_path, 'w', encoding='utf-8') as f:
        f.write("# Disavow File - Cleaned & Audited by Enigma\\n")
        f.write("# Original: disavow 20 may 2026 copy.txt\\n")
        f.write("# Duplicates removed, syntax checked\\n\\n")
        f.write("# domains\\n")
        for entry in cleaned_domains:
            f.write(entry)
        if cleaned_urls:
            f.write("\\n# urls\\n")
            for entry in cleaned_urls:
                f.write(entry)
    
    print(f"\n Cleaned file written to: {output_path}")
    
    # SUMMARY
    print("\n" + "=" * 60)
    print("SUMMARY")
    print("=" * 60)
    print(f"  Total issues found: {len(issues) + len(syntax_issues) + len(dangerous)}")
    print(f"  - Missing prefix/format issues: {len(issues)}")
    print(f"  - Syntax errors: {len(syntax_issues)}")
    print(f"  - Dangerous broad matches: {len(dangerous)}")
    print(f"  - Duplicates removed: {len(duplicates) if duplicates else 0} unique domains, {sum(duplicates.values()) - len(duplicates) if duplicates else 0} total duplicate lines")
    print(f"  - Final cleaned entries: {len(cleaned_domains)} domains + {len(cleaned_urls)} URLs")
    
    return {
        'issues': issues,
        'syntax_issues': syntax_issues,
        'dangerous': dangerous,
        'duplicates': duplicates,
        'cleaned_path': output_path,
        'domain_count': len(cleaned_domains),
        'url_count': len(cleaned_urls),
    }

if __name__ == '__main__':
    result = audit_disavow_file(FILE_PATH)
