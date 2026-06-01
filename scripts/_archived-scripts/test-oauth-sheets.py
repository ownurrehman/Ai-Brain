#!/usr/bin/env python3
"""Quick Google Sheets connectivity test for oliverjakeseo@gmail.com"""
import json, os, sys

# CANONICAL IMPORT — use google_auth_helper for all Google OAuth
sys.path.insert(0, os.path.expanduser('~/Ai Works - Local/Ai Codes/Ai Brain/openclaw/scripts'))
from google_auth_helper import get_credentials
from googleapiclient.discovery import build

# Load credentials (auto-refreshes if needed)
creds = get_credentials()

print("Token is valid")

# Test Sheets API
sheets = build('sheets', 'v4', credentials=creds)

SHEET_ID = '11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4'

try:
    result = sheets.spreadsheets().get(spreadsheetId=SHEET_ID).execute()
    print(f"Connected to sheet: {result['properties']['title']}")
    print(f"Last modified: {result['properties']['modifiedTime']}")
except Exception as e:
    print(f"Error: {e}")
    sys.exit(1)

print("Google Sheets API is working!")
print("\nTesting Google Sheets API...")
sheets = build('sheets', 'v4', credentials=creds, cache_discovery=False)

# Try to create a test spreadsheet
body = {
    'properties': {'title': 'OAuth Test Sheet - May 25'},
    'sheets': [{'properties': {'title': 'Sheet1'}}]
}
try:
    spreadsheet = sheets.spreadsheets().create(body=body).execute()
    print(f"✅ CREATE SUCCESS: {spreadsheet['spreadsheetUrl']}")
    print(f"   Spreadsheet ID: {spreadsheet['spreadsheetId']}")
except Exception as e:
    print(f"❌ CREATE FAILED: {e}")
    sys.exit(1)

# Try to write data
print("\nTesting write...")
try:
    sheets.spreadsheets().values().update(
        spreadsheetId=spreadsheet['spreadsheetId'],
        range='Sheet1!A1',
        valueInputOption='RAW',
        body={'values': [['OAuth Test', 'Status'], ['oliverjakeseo', 'WORKING']]}
    ).execute()
    print("✅ WRITE SUCCESS")
except Exception as e:
    print(f"❌ WRITE FAILED: {e}")

# Try to read it back
print("\nTesting read...")
try:
    result = sheets.spreadsheets().values().get(
        spreadsheetId=spreadsheet['spreadsheetId'],
        range='Sheet1!A1:B2'
    ).execute()
    print(f"✅ READ SUCCESS: {result.get('values', [])}")
except Exception as e:
    print(f"❌ READ FAILED: {e}")

# Try Drive list (to verify Drive scope)
print("\nTesting Drive API list...")
drive = build('drive', 'v3', credentials=creds, cache_discovery=False)
try:
    files = drive.files().list(pageSize=5, fields="files(name, id)").execute()
    print(f"✅ DRIVE LIST SUCCESS: {len(files.get('files', []))} files found")
except Exception as e:
    print(f"❌ DRIVE LIST FAILED: {e}")

print("\n=== ALL TESTS COMPLETE ===")
print(f"Account: oliverjakeseo@gmail.com")
print(f"New spreadsheet: {spreadsheet['spreadsheetUrl']}")
