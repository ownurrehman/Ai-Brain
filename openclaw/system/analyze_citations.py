import csv

def analyze_citations():
    tracker_file = 'reports/khanllp-citation-tracker-2026-04-21.csv'
    master_file = 'reports/khanllp-citation-master-list-2026-04-21.csv'
    
    needs_verification = []

    # Process Tracker
    with open(tracker_file, mode='r', encoding='utf-8-sig') as f:
        reader = csv.DictReader(f)
        for row in reader:
            # Check if Listing Type is missing or NAP status is missing/needs verification
            # NAP status is essentially 'Listing Found?' and 'NAP Consistent?'
            listing_type = row.get('Listing Type (Paid/Free)', '').strip()
            found = row.get('Listing Found?', '').strip()
            nap = row.get('NAP Consistent?', '').strip()
            
            if not listing_type or found in ['No', 'Needs Verification', ''] or nap in ['No', 'N/A', '']:
                needs_verification.append({
                    'name': row.get('Directory Name'),
                    'url': row.get('URL'),
                    'type': listing_type,
                    'found': found,
                    'nap': nap
                })

    # Process Master (to find ones not in tracker or missing info)
    with open(master_file, mode='r', encoding='utf-8-sig') as f:
        reader = csv.DictReader(f)
        for row in reader:
            listing_type = row.get('Listing Type (Paid/Free)', '').strip()
            found = row.get('Listing Found?', '').strip()
            nap = row.get('NAP Consistent?', '').strip()
            
            if not listing_type or found in ['No', 'Needs Verification', ''] or nap in ['No', 'N/A', '']:
                # Avoid duplicates from tracker
                if not any(item['url'] == row.get('URL') for item in needs_verification):
                    needs_verification.append({
                        'name': row.get('Directory Name'),
                        'url': row.get('URL'),
                        'type': listing_type,
                        'found': found,
                        'nap': nap
                    })

    return needs_verification

if __name__ == "__main__":
    results = analyze_citations()
    print(f"Found {len(results)} listings needing verification.")
    for r in results:
        print(f"{r['name']} | {r['url']} | Type: {r['type']} | Found: {r['found']} | NAP: {r['nap']}")
