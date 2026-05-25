#!/usr/bin/env python3
"""
Rank Ray Lead Tracker - Final Cleanup
Remove duplicates, standardize statuses
"""
import json
import os
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

# Remove duplicates and empty rows
seen_business_names = set()
cleaned_rows = []

for i, row in enumerate(rows[1:], 2):
    while len(row) < len(headers):
        row.append('')
    
    # Skip empty rows
    if not any(cell.strip() for cell in row):
        continue
    
    business_name = row[2].strip() if len(row) > 2 else ''
    
    # Skip duplicates (keep first occurrence)
    if business_name and business_name in seen_business_names:
        print(f"Removing duplicate: {business_name} (row {i})")
        continue
    
    if business_name:
        seen_business_names.add(business_name)
    
    # Standardize status
    status = row[13].strip() if len(row) > 13 else 'New Lead'
    status_map = {
        'Pending': 'New Lead',
        'New': 'New Lead',
        'Sourced': 'Sourced',
        'Pending Email 1': 'Email Queue',
        '': 'New Lead'
    }
    row[13] = status_map.get(status, status)
    
    cleaned_rows.append(row)

print(f"Cleaned {len(cleaned_rows)} rows (removed {len(rows)-1 - len(cleaned_rows)} duplicates)")

# Write back
body = {'values': [headers] + cleaned_rows}
result = sheets_service.spreadsheets().values().update(
    spreadsheetId=SHEET_ID,
    range='A1',
    valueInputOption='RAW',
    body=body
).execute()

print(f"Done! Sheet updated with {len(cleaned_rows)} unique leads")

# Print summary by status
from collections import Counter
statuses = [row[13] for row in cleaned_rows if len(row) > 13]
print("\nStatus breakdown:")
for status, count in Counter(statuses).most_common():
    print(f"  {status}: {count}")
