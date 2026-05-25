#!/usr/bin/env python3
"""
Rank Ray Lead Tracker - Data Cleanup Script
Fixes misaligned columns without deleting any leads
"""
import json
import os
import re
from datetime import datetime
from google.auth.transport.requests import Request
from google.oauth2.credentials import Credentials
from googleapiclient.discovery import build

# Load OAuth token
token_path = os.path.expanduser('~/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/token.json')
with open(token_path) as f:
    token_data = json.load(f)

creds = Credentials(
    token=token_data['token'],
    refresh_token=token_data['refresh_token'],
    token_uri=token_data['token_uri'],
    client_id=token_data['client_id'],
    client_secret=token_data['client_secret'],
    scopes=token_data['scopes']
)

if creds.expired:
    creds.refresh(Request())
    token_data['token'] = creds.token
    with open(token_path, 'w') as f:
        json.dump(token_data, f, indent=2)

sheets_service = build('sheets', 'v4', credentials=creds)
SHEET_ID = '11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4'

# Read all data
result = sheets_service.spreadsheets().values().get(
    spreadsheetId=SHEET_ID,
    range='A1:T100'
).execute()

rows = result.get('values', [])
headers = rows[0]
print(f"Headers: {headers}")
print(f"Total rows to process: {len(rows)-1}")

# Generate next lead ID
existing_ids = []
for row in rows[1:]:
    if row and row[0] and str(row[0]).startswith(('LR-', 'RR-')):
        existing_ids.append(row[0])

print(f"Found {len(existing_ids)} existing lead IDs")

# Parse and fix each row
fixed_rows = []
skipped = 0

for i, row in enumerate(rows[1:], 2):
    # Pad row to match header length
    while len(row) < len(headers):
        row.append('')
    
    # Skip empty rows
    if not any(cell.strip() for cell in row):
        skipped += 1
        continue
    
    # Determine which format this row is in
    first_col = row[0].strip() if row[0] else ''
    
    # Check if it's already in proper format (starts with Lead ID)
    if first_col.startswith(('LR-', 'RR-')):
        # Already proper format - keep as is but ensure all fields
        fixed_row = row[:len(headers)]
        fixed_rows.append(fixed_row)
    
    # Check Format 2: Business Name, Website, Pain Points, "Sourced"
    elif row[1].startswith('http') and row[3] == 'Sourced':
        # This is Format 2: Business Name, Website, Pain Points, Sourced
        fixed_row = [''] * len(headers)
        fixed_row[2] = row[0]  # Business Name
        fixed_row[6] = row[1]  # Website
        fixed_row[10] = row[2]  # Pain Points
        fixed_row[13] = 'Sourced'  # Status
        fixed_rows.append(fixed_row)
    
    # Check Format 3: Business Name, Website, Industry, Location, Phone, Pain Points, Date, Status
    elif row[2].startswith('http') and row[3] in ['Plumbing', 'Dentistry', 'Agency', '']:
        fixed_row = [''] * len(headers)
        fixed_row[2] = row[0]  # Business Name
        fixed_row[6] = row[1]  # Website
        fixed_row[8] = row[2]  # Industry
        fixed_row[9] = row[3]  # Location
        fixed_row[5] = row[4]  # Phone
        fixed_row[10] = row[5]  # Pain Points
        fixed_row[1] = row[6]  # Date Added
        fixed_row[13] = row[7] if len(row) > 7 else 'Pending'  # Status
        fixed_rows.append(fixed_row)
    
    # Format 1: Business Name, TBD, Pain Points, Yes, Pending
    elif row[1] == 'TBD' and row[3] in ['Yes', 'No', '']:
        fixed_row = [''] * len(headers)
        fixed_row[2] = row[0]  # Business Name
        fixed_row[10] = row[2]  # Pain Points
        fixed_row[13] = row[4] if len(row) > 4 else 'Pending'  # Status
        fixed_rows.append(fixed_row)
    
    # Default: treat as Business Name in first column
    else:
        fixed_row = [''] * len(headers)
        fixed_row[2] = row[0]  # Business Name
        # Try to extract any other data
        if len(row) > 1 and row[1]:
            if row[1].startswith('http'):
                fixed_row[6] = row[1]  # Website
            else:
                fixed_row[10] = row[1]  # Pain Points
        fixed_rows.append(fixed_row)

print(f"\nFixed {len(fixed_rows)} rows, skipped {skipped} empty rows")

# Assign Lead IDs to rows that don't have them
import random
for i, row in enumerate(fixed_rows):
    if not row[0]:  # No Lead ID
        # Generate ID based on date or generic
        date_str = row[1] if row[1] else datetime.now().strftime('%Y-%m-%d')
        # Extract date from existing or use today
        if date_str and len(date_str) == 10:
            date_part = date_str.replace('-', '')
        else:
            date_part = datetime.now().strftime('%Y%m%d')
        
        # Generate unique ID
        random_suffix = ''.join(random.choices('0123456789', k=3))
        row[0] = f"RR-FIXED-{date_part}-{random_suffix}"

# Now write back to a new sheet or overwrite
print("\nSample fixed rows:")
for row in fixed_rows[:5]:
    print(f"  {row[:5]}...")

# Clear and rewrite
print("\nClearing sheet and rewriting...")
body = {'values': [headers] + fixed_rows}
result = sheets_service.spreadsheets().values().update(
    spreadsheetId=SHEET_ID,
    range='A1',
    valueInputOption='RAW',
    body=body
).execute()

print(f"Done! Updated {len(fixed_rows)} rows")
print(f"Total leads preserved: {len(fixed_rows)}")
