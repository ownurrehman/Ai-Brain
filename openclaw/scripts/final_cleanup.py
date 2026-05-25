#!/usr/bin/env python3
"""
Final cleanup - address real issues found in second-pass audit
"""

import re
import unicodedata

INPUT_FILE = "/Users/sheikhown/.openclaw/media/inbound/disavow_20_may_2026---a455572f-90a3-4b28-aceb-d608930ef0da.txt"
OUTPUT_FILE = "/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Clients/Working - Clients/Geray - Crypto Sites/Coinsfera/Disavow/disavow 20 may 2026 FINAL.txt"

# IDN domains that need Punycode conversion (with their Punycode forms)
# Using idna encoding
import idna

# AWS/EC2 entries to remove
REMOVE_PATTERNS = [
    'ec2-',  # AWS EC2 instances
    '.compute.amazonaws.com',
]

# Gov/Edu domains to review (some may be compromised sites with spam)
# These are subdomains on gov/edu - they MIGHT be legitimate or compromised
# For safety, let's flag but NOT auto-remove (user should verify)

def is_gov_or_edu(domain):
    """Check if domain is gov or edu related"""
    parts = domain.lower().split('.')
    if len(parts) >= 2:
        if parts[-1] in ('gov', 'edu', 'mil'):
            return True
        # Check for ccSLD patterns like .go.id, .ac.th, etc.
        if len(parts) >= 3:
            if parts[-2] in ('go', 'ac', 'gov', 'edu'):
                return True
    return False

def convert_idn(domain):
    """Convert IDN to Punycode"""
    try:
        return idna.encode(domain).decode('ascii')
    except:
        return None

def process():
    with open(INPUT_FILE, 'r', encoding='utf-8', errors='replace') as f:
        lines = f.readlines()

    kept = []
    removed = []
    converted = []
    gov_edu_flagged = []
    
    for i, raw_line in enumerate(lines, 1):
        line = raw_line.rstrip('\n')
        stripped = line.strip()
        
        if not stripped or stripped.startswith('#'):
            kept.append(raw_line)
            continue
        
        if not stripped.startswith('domain:'):
            kept.append(raw_line)
            continue
        
        domain = stripped[7:].strip()
        domain_lower = domain.lower()
        
        # Check for AWS/EC2
        if 'ec2-' in domain_lower or '.compute.amazonaws.com' in domain_lower:
            removed.append(f"Line {i}: AWS EC2 removed → {domain}")
            continue
        
        # Check for non-ASCII
        try:
            domain.encode('ascii')
            # It's ASCII, keep as-is
            final_domain = domain
        except UnicodeEncodeError:
            # Try to convert to Punycode
            puny = convert_idn(domain)
            if puny:
                converted.append(f"Line {i}: IDN converted → {domain} → {puny}")
                final_domain = puny
            else:
                # Can't convert, keep original but flag
                converted.append(f"Line {i}: IDN conversion FAILED → {domain}")
                final_domain = domain
        
        # Check gov/edu
        if is_gov_or_edu(final_domain):
            gov_edu_flagged.append((i, final_domain))
        
        kept.append(f"domain:{final_domain}\n")
    
    # Write output
    with open(OUTPUT_FILE, 'w', encoding='utf-8') as f:
        f.write("# Disavow File - Final Cleaned Version\n")
        f.write("# Date: 2026-05-20\n")
        f.write("# Changes: Removed AWS EC2, converted IDN to Punycode\n")
        f.write("# Note: Review gov/edu entries manually (see report)\n\n")
        f.write("# domains\n")
        for entry in kept:
            if entry.strip() and not entry.strip().startswith('#'):
                f.write(entry)
    
    # Report
    print("=" * 70)
    print("FINAL CLEANUP REPORT")
    print("=" * 70)
    print(f"\nTotal entries processed: {len(lines)}")
    print(f"Removed (AWS EC2): {len(removed)}")
    print(f"Converted (IDN → Punycode): {len(converted)}")
    
    if removed:
        print("\n--- REMOVED ---")
        for r in removed:
            print(f"  {r}")
    
    if converted:
        print("\n--- IDN CONVERTED ---")
        for c in converted:
            print(f"  {c}")
    
    if gov_edu_flagged:
        print(f"\n--- GOV/EDU DOMAINS FLAGGED FOR REVIEW ({len(gov_edu_flagged)}) ---")
        for line_num, domain in gov_edu_flagged:
            print(f"  Line {line_num}: {domain}")
    
    print(f"\n✅ Final file saved: {OUTPUT_FILE}")
    
    return OUTPUT_FILE

if __name__ == '__main__':
    process()
