#!/usr/bin/env python3
"""
Rank Ray Lead Tracker - Data Cleanup Script v3
Preserves ALL data by analyzing each row's content
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

# Read original data (before my fixes)
# First let's read from a backup if available, or we need to reconstruct
result = sheets_service.spreadsheets().values().get(
    spreadsheetId=SHEET_ID,
    range='A1:T100'
).execute()

rows = result.get('values', [])
headers = rows[0]

print("Reconstructing data from current sheet...")
print(f"Headers: {headers}")

# Show what we have now to verify structure
print("\n=== Current structure sample ===")
for i in range(min(5, len(rows)-1)):
    row = rows[i+1]
    while len(row) < len(headers):
        row.append('')
    print(f"Row {i+2}: ID={row[0]}, Name={row[2]}, Status={row[13]}")

# Now let's properly populate missing fields based on what we know
# The issue is that original data had pain points in column C but we moved them

print("\n\nNOTE: The early rows (2-11) had pain points data that got misaligned.")
print("I need to check if we can recover the original pain points from memory or if we need to re-scrape.")

# Check rows that have data in Pain Points column
print("\n=== Rows with Pain Points ===")
for i, row in enumerate(rows[1:], 2):
    while len(row) < len(headers):
        row.append('')
    if row[10].strip():  # Pain Points Found column
        print(f"Row {i}: {row[2]} - Pain Points: {row[10][:60]}...")

print("\n=== Summary ===")
print(f"Total leads: {len(rows)-1}")
print(f"Leads with Pain Points: {sum(1 for r in rows[1:] if len(r) > 10 and r[10].strip())}")
print(f"Leads with Website: {sum(1 for r in rows[1:] if len(r) > 6 and r[6].startswith('http'))}")
print(f"Leads with Email: {sum(1 for r in rows[1:] if len(r) > 4 and r[4].strip())}")
print(f"Leads with Phone: {sum(1 for r in rows[1:] if len(r) > 5 and r[5].strip())}")
