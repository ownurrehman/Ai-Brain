#!/usr/bin/env python3
"""Quick Google Sheets connectivity test for oliverjakeseo@gmail.com"""
import json, os, sys

# Load env
env_path = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/master-env.env"
with open(env_path) as f:
    for line in f:
        if '=' in line and not line.startswith('#'):
            k, v = line.strip().split('=', 1)
            os.environ.setdefault(k, v)

from google.oauth2.credentials import Credentials
from googleapiclient.discovery import build
from google.auth.transport.requests import Request

token_path = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/token.json"

with open(token_path) as f:
    token_data = json.load(f)

# Build credentials object
creds = Credentials(
    token=token_data.get("token") or token_data.get("access_token"),
    refresh_token=token_data.get("refresh_token"),
    token_uri="https://oauth2.googleapis.com/token",
    client_id=os.environ["GOOGLE_CLIENT_ID"],
    client_secret=os.environ["GOOGLE_CLIENT_SECRET"],
    scopes=token_data.get("scope", "").split()
)

# Auto-refresh if expired
if creds.expired or not creds.valid:
    print("Token expired or invalid, attempting refresh...")
    try:
        creds.refresh(Request())
        print("Refresh SUCCESS")
        # Save back
        token_data["token"] = creds.token
        token_data["access_token"] = creds.token
        with open(token_path, 'w') as f:
            json.dump(token_data, f, indent=2)
        print("Token saved back to file")
    except Exception as e:
        print(f"Refresh FAILED: {e}")
        sys.exit(1)
else:
    print("Token is valid")

# Test Sheets API
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
