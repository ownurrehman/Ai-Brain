#!/usr/bin/env python3
"""
Rank Ray Lead Tracker - Data Cleanup Script v2
Better preserves dates and existing data
"""
import json
import os
import re
from datetime import datetime
import sys

# CANONICAL IMPORT — use google_auth_helper for all Google OAuth
sys.path.insert(0, os.path.expanduser('~/Ai Works - Local/Ai Codes/Ai Brain/openclaw/scripts'))
from google_auth_helper import get_credentials
from googleapiclient.discovery import build

# Load OAuth token using canonical path
creds = get_credentials()

sheets_service = build('sheets', 'v4', credentials=creds)
SHEET_ID = '11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4'

# Read all data
result = sheets_service.spreadsheets().values().get(
    spreadsheetId=SHEET_ID,
    range='A1:T100'
).execute()

rows = result.get('values', [])
headers = rows[0]

# Parse and fix each row more carefully
fixed_rows = []

for i, row in enumerate(rows[1:], 2):
    # Pad row to match header length
    while len(row) < len(headers):
        row.append('')
    
    # Skip empty rows
    if not any(cell.strip() for cell in row):
        continue
    
    first_col = row[0].strip() if row[0] else ''
    second_col = row[1].strip() if len(row) > 1 else ''
    
    # Already proper format with Lead ID
    if first_col.startswith(('LR-', 'RR-')):
        # Keep as-is, pad if needed
        fixed_row = row[:len(headers)]
        fixed_rows.append(fixed_row)
        continue
    
    # Format detection logic
    fixed_row = [''] * len(headers)
    
    # Try to detect if this is the "Business Name first" format (rows 2-11 originally)
    if not first_col.startswith('http') and second_col in ['TBD', 'http://www.lakefs.io', 'http://www.aperio.ai', 'http://www.arcspan.com']:
        # Format: Business Name, Website/PainPoints, Pain Points, Status
        fixed_row[2] = first_col  # Business Name
        if second_col.startswith('http'):
            fixed_row[6] = second_col  # Website
            fixed_row[10] = row[2] if len(row) > 2 else ''  # Pain Points
            fixed_row[13] = row[3] if len(row) > 3 else 'Sourced'  # Status
        else:
            fixed_row[10] = row[2] if len(row) > 2 else ''  # Pain Points
            fixed_row[13] = row[4] if len(row) > 4 else 'Pending'  # Status
    
    # Format: Business Name, Website, Industry, Location, Phone, Pain Points, Date, Status
    elif len(row) > 6 and row[2] in ['Plumbing', 'Dentistry', 'Agency', 'N/A']:
        fixed_row[2] = row[0]  # Business Name
        fixed_row[6] = row[1] if row[1].startswith('http') else ''  # Website
        fixed_row[8] = row[2]  # Industry
        fixed_row[9] = row[3]  # Location
        fixed_row[5] = row[4]  # Phone
        fixed_row[10] = row[5]  # Pain Points
        fixed_row[1] = row[6]  # Date Added
        fixed_row[13] = row[7] if len(row) > 7 else 'Pending'  # Status
    
    else:
        # Default: Business Name is first column
        fixed_row[2] = first_col
        if len(row) > 1 and row[1]:
            if row[1].startswith('http'):
                fixed_row[6] = row[1]
            else:
                fixed_row[10] = row[1]
    
    fixed_rows.append(fixed_row)

# Assign Lead IDs to rows that don't have them
import random
for i, row in enumerate(fixed_rows):
    if not row[0]:  # No Lead ID
        # Try to extract date from row
        date_str = row[1] if row[1] else datetime.now().strftime('%Y-%m-%d')
        if date_str and re.match(r'^\d{4}-\d{2}-\d{2}$', date_str):
            date_part = date_str.replace('-', '')
        else:
            date_part = datetime.now().strftime('%Y%m%d')
        
        random_suffix = ''.join(random.choices('0123456789', k=3))
        row[0] = f"RR-FIXED-{date_part}-{random_suffix}"

print(f"Fixed {len(fixed_rows)} rows")

# Write back
body = {'values': [headers] + fixed_rows}
result = sheets_service.spreadsheets().values().update(
    spreadsheetId=SHEET_ID,
    range='A1',
    valueInputOption='RAW',
    body=body
).execute()

print(f"Done! Updated sheet with {len(fixed_rows)} leads")
