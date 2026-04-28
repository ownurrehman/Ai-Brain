import csv
import os

tracker_path = '/Users/sheikhown/.openclaw/workspace/reports/khanllp-citation-tracker-2026-04-21.csv'
master_path = '/Users/sheikhown/.openclaw/workspace/reports/khanllp-citation-master-list-2026-04-21.csv'
verified_path = '/Users/sheikhown/.openclaw/workspace/reports/khanllp-citation-verified-final-2026-04-21.csv'

# 1. Extract mapping from Tracker
cost_map = {}
with open(tracker_path, 'r', encoding='utf-8') as f:
    reader = csv.DictReader(f)
    for row in reader:
        url = row.get('URL')
        cost = row.get('Cost')
        if url:
            cost_map[url] = cost

# 2. Update Tracker (Rename Cost -> Listing Type (Paid/Free))
with open(tracker_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

header = lines[0].strip().split(',')
try:
    cost_idx = header.index('Cost')
    header[cost_idx] = 'Listing Type (Paid/Free)'
    lines[0] = ','.join(header) + '\n'
except ValueError:
    print("Cost column not found in tracker")

with open(tracker_path, 'w', encoding='utf-8', newline='') as f:
    f.writelines(lines)

# 3. Update Master (Add Listing Type (Paid/Free))
if os.path.exists(master_path):
    with open(master_path, 'r', encoding='utf-8') as f:
        reader = csv.reader(f)
        rows = list(reader)
    
    header = rows[0]
    if 'Listing Type (Paid/Free)' not in header:
        header.append('Listing Type (Paid/Free)')
        rows[0] = header
    
    lt_idx = header.index('Listing Type (Paid/Free)')
    url_idx = header.index('URL') if 'URL' in header else -1
    
    for row in rows[1:]:
        if url_idx != -1 and len(row) > url_idx:
            url = row[url_idx]
            # Ensure row is long enough
            while len(row) < len(header):
                row.append('')
            row[lt_idx] = cost_map.get(url, '')
            
    with open(master_path, 'w', encoding='utf-8', newline='') as f:
        writer = csv.writer(f)
        writer.writerows(rows)

# 4. Update Verified (Add Listing Type (Paid/Free))
if os.path.exists(verified_path):
    with open(verified_path, 'r', encoding='utf-8') as f:
        reader = csv.reader(f)
        rows = list(reader)
        
    header = rows[0]
    if 'Listing Type (Paid/Free)' not in header:
        header.append('Listing Type (Paid/Free)')
        rows[0] = header
        
    lt_idx = header.index('Listing Type (Paid/Free)')
    url_idx = header.index('URL') if 'URL' in header else -1
    
    for row in rows[1:]:
        if url_idx != -1 and len(row) > url_idx:
            url = row[url_idx]
            while len(row) < len(header):
                row.append('')
            row[lt_idx] = cost_map.get(url, '')
            
    with open(verified_path, 'w', encoding='utf-8', newline='') as f:
        writer = csv.writer(f)
        writer.writerows(rows)

print("Successfully updated all files with 'Listing Type (Paid/Free)' column.")
