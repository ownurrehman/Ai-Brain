#!/usr/bin/env python3
"""
Comprehensive Disavow File Fixer
Based on Gemini audit recommendations
"""

import re

INPUT_FILE = "/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Clients/Working - Clients/Geray - Crypto Sites/Coinsfera/Disavow/disavow 20 may 2026 copy.txt"
OUTPUT_FILE = "/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Clients/Working - Clients/Geray - Crypto Sites/Coinsfera/Disavow/disavow 20 may 2026 FIXED.txt"

# 1. DOMAINS TO REMOVE (High Authority / Accidental)
REMOVE_DOMAINS = {
    # High Authority Platforms
    'blogs.harvard.edu',
    'amp.reddit.com',
    'codecademy.com',
    'cplusplus.com',
    'instructables.com',
    'issuu.com',
    'gumroad.com',
    'app.gumroad.com',
    'collider.com',
    'godotengine.org',
    'spreaker.com',
    'letterboxd.com',
    'wanelo.co',
    'ow.ly',
    # Government Sites
    'nairobi.go.ke',
    'ethanol.nebraska.gov',
    'cambioclimatico.gob.mx',
    'muniyanatile.gob.pe',
    'karantina.pertanian.go.id',
    'bkd.tulungagung.go.id',
    'kms.bkd.tulungagung.go.id',
    'ppishp.jakarta.go.id',
    # Educational
    'zaragoza.unam.mx',
    'fch.unicen.edu.ar',
    'eie.unse.edu.ar',
    'www2.ufasta.edu.ar',
    'engineering.utm.my',
    'humanities.utm.my',
    'sps.utm.my',
    'geati.ifc-camboriu.edu.br',
    'cse.oauife.edu.ng',
    'newhousestrategy.syr.edu',
    # Subdomain redundancy (keep root, remove subs)
    'cdn.alborsanews.com',
    'beta.alborsanews.com',
    'cdn11.alborsanews.com',
    'subdomainfinder.c99.nl',
    # Keep example3.com per user
    # IP addresses (not valid in disavow)
    '192.99.8.166',
    '176.32.230.29',
}

# 2. IDN CONVERSIONS (non-ASCII → Punycode)
IDN_CONVERSIONS = {
    'マンションの売却ランキング.com': 'xn--r9j1d0b6i1e8byf8f8441b4n7b.com',
    'новостройки22.рф': 'xn--22-jlcecb8bvdcc8b5g.xn--p1ai',
    'ромашка30.рф': 'xn--30-6kcaqd7cd9c.xn--p1ai',
}

def process_disavow():
    with open(INPUT_FILE, 'r', encoding='utf-8', errors='replace') as f:
        lines = f.readlines()

    kept = []
    removed = []
    converted = []
    seen = set()
    removed_count = 0

    for i, raw_line in enumerate(lines, 1):
        line = raw_line.rstrip('\n')
        stripped = line.strip()

        # Keep comments and empty lines
        if not stripped or stripped.startswith('#'):
            kept.append(raw_line)
            continue

        # Must start with domain:
        if not stripped.startswith('domain:'):
            removed.append(f"Line {i}: Invalid format (no domain: prefix) → {stripped[:60]}")
            removed_count += 1
            continue

        domain = stripped[7:].strip().lower()

        # Remove if in REMOVE list
        if domain in REMOVE_DOMAINS:
            removed.append(f"Line {i}: Removed (high auth/accidental) → {domain}")
            removed_count += 1
            continue

        # Deduplicate
        if domain in seen:
            removed.append(f"Line {i}: Removed (duplicate) → {domain}")
            removed_count += 1
            continue

        # Convert IDN
        original_domain = domain
        for idn, puny in IDN_CONVERSIONS.items():
            if domain == idn.lower():
                domain = puny
                converted.append(f"Line {i}: Converted IDN → {original_domain} → {puny}")
                break

        seen.add(domain)
        kept.append(f"domain:{domain}\n")

    # Write output
    with open(OUTPUT_FILE, 'w', encoding='utf-8') as f:
        f.write("# Disavow File - Fixed & Audited by Enigma (Rank Ray)\n")
        f.write("# Date: 2026-05-20\n")
        f.write("# Changes: Removed high-authority/accidental domains, deduped, IDN→Punycode\n")
        f.write("# Original: disavow 20 may 2026 copy.txt\n\n")
        f.write("# domains\n")
        for entry in kept:
            if entry.strip() and not entry.strip().startswith('#'):
                f.write(entry)

    # Stats
    kept_domains = [l for l in kept if l.strip() and not l.strip().startswith('#')]
    print(f"=" * 60)
    print(f"DISAVOW FILE FIXED")
    print(f"=" * 60)
    print(f"\nOriginal lines: {len(lines)}")
    print(f"Removed entries: {removed_count}")
    print(f"IDN conversions: {len(converted)}")
    print(f"Final domain count: {len(kept_domains)}")
    print(f"\nOutput: {OUTPUT_FILE}")
    print(f"\n--- REMOVED ({len(removed)} total) ---")
    for r in removed[:50]:
        print(f"  {r}")
    if len(removed) > 50:
        print(f"  ... and {len(removed) - 50} more")
    if converted:
        print(f"\n--- IDN CONVERTED ---")
        for c in converted:
            print(f"  {c}")

    return OUTPUT_FILE

if __name__ == '__main__':
    process_disavow()
