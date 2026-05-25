#!/usr/bin/env python3
"""
Extract gov/edu and high-authority domains into separate lists
"""

FILE_PATH = "/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Clients/Working - Clients/Geray - Crypto Sites/Coinsfera/Disavow/disavow 20 may 2026.txt"

# Known high-authority platforms/sites
KNOWN_SITES = {
    # Social & Platforms
    'reddit.com', 'amp.reddit.com', 'twitter.com', 'facebook.com', 'linkedin.com',
    'instagram.com', 'pinterest.com', 'tiktok.com', 'snapchat.com', 'youtube.com',
    'tumblr.com', 'medium.com', 'quora.com',
    # Tech
    'github.com', 'github.io', 'gitlab.io', 'stackoverflow.com', 'codecademy.com',
    'cplusplus.com', 'godotengine.org', 'apple.com', 'microsoft.com', 'amazon.com',
    'cloudflare.com', 'netlify.app', 'vercel.app', 'herokuapp.com', 'web.app',
    # News & Media
    'bbc.com', 'bbc.co.uk', 'cnn.com', 'reuters.com', 'apnews.com', 'forbes.com',
    'bloomberg.com', 'nytimes.com', 'washingtonpost.com', 'collider.com', 'spreaker.com',
    # Publishing & Commerce
    'issuu.com', 'gumroad.com', 'app.gumroad.com', 'instructables.com', 'wanelo.co',
    'letterboxd.com', 'etsy.com', 'ebay.com', 'shopify.com',
    # URL Shorteners
    'ow.ly', 'bit.ly', 'tinyurl.com', 't.co', 'buff.ly', 'goo.gl', 'is.gd',
    # Education & Research (major ones)
    'harvard.edu', 'mit.edu', 'stanford.edu', 'yale.edu', 'princeton.edu',
    'ox.ac.uk', 'cam.ac.uk', 'berkeley.edu', 'cmu.edu',
    # Wikipedia
    'wikipedia.org', 'wikimedia.org',
    # Free hosts (keep these as they're likely spam subs)
}

def is_gov_or_edu(domain):
    """Check if domain is gov or edu related"""
    parts = domain.lower().split('.')
    if len(parts) >= 2:
        if parts[-1] in ('gov', 'edu', 'mil'):
            return True
        if len(parts) >= 3:
            if parts[-2] in ('go', 'ac', 'gov', 'edu'):
                return True
    return False

def is_known_site(domain):
    """Check if domain is a known high-authority site"""
    dom_lower = domain.lower()
    if dom_lower in KNOWN_SITES:
        return True
    for site in KNOWN_SITES:
        if dom_lower.endswith('.' + site):
            return True
    return False

def extract():
    with open(FILE_PATH, 'r', encoding='utf-8', errors='replace') as f:
        lines = f.readlines()

    domains = []
    for i, raw_line in enumerate(lines, 1):
        line = raw_line.strip()
        if not line or line.startswith('#'):
            continue
        if line.startswith('domain:'):
            domains.append((i, line[7:].strip()))

    gov_edu = []
    known = []
    
    for line_num, domain in domains:
        if is_gov_or_edu(domain):
            gov_edu.append((line_num, domain))
        elif is_known_site(domain):
            known.append((line_num, domain))

    # Write gov/edu list
    gov_file = "/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Clients/Working - Clients/Geray - Crypto Sites/Coinsfera/Disavow/GOV_EDU_DOMAINS.txt"
    with open(gov_file, 'w', encoding='utf-8') as f:
        f.write("# Government / Education / Academic Domains\n")
        f.write("# File: disavow 20 may 2026.txt\n")
        f.write("# Count: {}\n".format(len(gov_edu)))
        f.write("# Review these manually - may be compromised sites or accidental inclusions\n\n")
        for line_num, domain in gov_edu:
            f.write("Line {}: {}\n".format(line_num, domain))

    # Write known sites list
    known_file = "/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Clients/Working - Clients/Geray - Crypto Sites/Coinsfera/Disavow/HIGH_AUTHORITY_SITES.txt"
    with open(known_file, 'w', encoding='utf-8') as f:
        f.write("# Known High-Authority Sites\n")
        f.write("# File: disavow 20 may 2026.txt\n")
        f.write("# Count: {}\n".format(len(known)))
        f.write("# These are major platforms - verify before disavowing\n\n")
        for line_num, domain in known:
            f.write("Line {}: {}\n".format(line_num, domain))

    print("=" * 60)
    print("EXTRACTION COMPLETE")
    print("=" * 60)
    print(f"\nGov/Edu domains: {len(gov_edu)}")
    print(f"Known high-authority sites: {len(known)}")
    print(f"\nFiles saved:")
    print(f"  1. {gov_file}")
    print(f"  2. {known_file}")

if __name__ == '__main__':
    extract()
