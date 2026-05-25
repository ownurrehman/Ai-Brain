#!/usr/bin/env python3
"""
Combined clean version:
- Start from my FINAL (has IDN punycode, gov removal, AWS removal, subdomain dedup)
- Also remove legitimate sites that Gemini correctly identified
"""

FILE_PATH = "/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Clients/Working - Clients/Geray - Crypto Sites/Coinsfera/Disavow/disavow 20 may 2026 FINAL.txt"
OUTPUT_FILE = "/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Clients/Working - Clients/Geray - Crypto Sites/Coinsfera/Disavow/disavow 20 may 2026 COMBINED_CLEAN.txt"

# Additional domains Gemini correctly removed (legitimate sites)
LEGITIMATE_SITES = {
    'telegra.ph',        # Telegram publishing
    'pbase.com',         # Photo hosting
    'libredd.it',        # Reddit mirror
    'list.ly',           # Content curation
    'v.gd',              # URL shortener
    'gsmarena.com.pk',   # Tech site
    'naturenano.com',    # Nature journal
    'businessstandard.co.za',  # News
    'progettoviaggi.it', # Travel agency
    'annamaephoto.com',  # Photographer
    'elearning.go4films.nl',   # E-learning
    'blogs.luc.edu',     # Loyola University
}

def process():
    with open(FILE_PATH, 'r', encoding='utf-8', errors='replace') as f:
        lines = f.readlines()

    kept = []
    removed = []
    
    for i, raw_line in enumerate(lines, 1):
        line = raw_line.rstrip('\n')
        stripped = line.strip()
        
        if not stripped or stripped.startswith('#'):
            kept.append(raw_line)
            continue
        
        if not stripped.startswith('domain:'):
            kept.append(raw_line)
            continue
        
        domain = stripped[7:].strip().lower()
        
        if domain in LEGITIMATE_SITES:
            removed.append(f"Line {i}: Removed (legitimate site) → {domain}")
            continue
        
        kept.append(raw_line)
    
    # Write output
    with open(OUTPUT_FILE, 'w', encoding='utf-8') as f:
        f.write("# Disavow File - Combined Clean Version\n")
        f.write("# Date: 2026-05-20\n")
        f.write("# Changes: IDN→Punycode, removed gov/edu/AWS, deduped, removed legitimate sites\n")
        f.write("# Based on: disavow 20 may 2026.txt + Gemini audit\n\n")
        f.write("# domains\n")
        for entry in kept:
            if entry.strip() and not entry.strip().startswith('#'):
                f.write(entry)
    
    kept_domains = [l for l in kept if l.strip() and not l.strip().startswith('#')]
    
    print("=" * 60)
    print("COMBINED CLEAN FILE CREATED")
    print("=" * 60)
    print(f"\nOriginal my FINAL: {len([l for l in lines if l.strip().startswith('domain:')])} domains")
    print(f"Removed: {len(removed)} legitimate sites")
    print(f"Final count: {len(kept_domains)} domains")
    print(f"\nFile: {OUTPUT_FILE}")
    print(f"\n--- REMOVED ---")
    for r in removed:
        print(f"  {r}")

if __name__ == '__main__':
    process()
