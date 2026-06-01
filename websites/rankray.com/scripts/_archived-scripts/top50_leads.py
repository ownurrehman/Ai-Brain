import json, sys, re
from urllib.parse import urljoin

data = json.load(sys.stdin)
values = data.get('values', [])

leads = []
for i, row in enumerate(values):
    if len(row) < 7:
        continue
    website = row[6].strip() if row[6] else ''
    if not website or website == '-':
        continue
    
    has_email = len(row) > 4 and row[4] and row[4].strip() and row[4].strip() != '-'
    has_phone = len(row) > 5 and row[5] and row[5].strip() and row[5].strip() != '-'
    has_contact = len(row) > 3 and row[3] and row[3].strip() and row[3].strip() != '-'
    
    if not has_email or not has_phone or not has_contact:
        lead_id = row[0] if row[0] else str(i+2)
        business = row[2] if len(row) > 2 else ''
        grade = row[12] if len(row) > 12 else ''
        date_added = row[1] if len(row) > 1 else ''
        
        # Normalize grade for sorting
        grade_val = 3  # default low
        grade_clean = grade.upper()
        if 'A' in grade_clean:
            grade_val = 1
        elif 'B' in grade_clean:
            grade_val = 2
        elif 'C' in grade_clean:
            grade_val = 3
        
        leads.append({
            'lead_id': lead_id,
            'business_name': business,
            'website': website,
            'grade': grade,
            'grade_val': grade_val,
            'date_added': date_added,
            'missing_email': not has_email,
            'missing_phone': not has_phone,
            'missing_contact': not has_contact,
            'row_num': i + 2
        })

# Sort by grade_val ascending (A first), then by date_added descending (newest first)
leads.sort(key=lambda x: (x['grade_val'], x['date_added']), reverse=False)
# Actually we want A first, so grade_val ascending. For date, newest first means descending.
# Re-sort properly:
leads.sort(key=lambda x: (x['grade_val'], x['date_added']), reverse=False)
# For date descending within same grade, we need to reverse just date - let's do it manually
from functools import cmp_to_key
def cmp(a, b):
    if a['grade_val'] != b['grade_val']:
        return a['grade_val'] - b['grade_val']
    # Newest date first
    if a['date_added'] > b['date_added']:
        return -1
    elif a['date_added'] < b['date_added']:
        return 1
    return 0

leads.sort(key=cmp_to_key(cmp))

top50 = leads[:50]

# Output as JSON for the scraper phase
print(json.dumps(top50, indent=2))
print(f'\nTotal leads needing enrichment: {len(leads)}', file=sys.stderr)
print(f'Selected top 50: {len(top50)}', file=sys.stderr)
