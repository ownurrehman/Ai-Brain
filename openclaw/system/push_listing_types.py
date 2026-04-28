import csv
import json
import os
from googleapiclient.discovery import build
from google.oauth2 import service_account

# Data to push
tracker_path = '/Users/sheikhown/.openclaw/workspace/reports/khanllp-citation-tracker-2026-04-21.csv'
spreadsheet_id = '1EnYut8v6-FO4PtPD7QibhJrvOcvbvXxgXVjpeaGKmmQ'

try:
    SCOPES = ['https://www.googleapis.com/auth/spreadsheets']
    SERVICE_ACCOUNT_FILE = '/Users/sheikhown/.openclaw/workspace/skills/google-sheet/service-account.json'
    
    creds = service_account.Credentials.from_service_account_file(
        SERVICE_ACCOUNT_FILE, scopes=SCOPES)
    service = build('sheets', 'v4', credentials=creds)

    # Read local tracker
    cost_map = {}
    with open(tracker_path, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            url = row.get('URL')
            cost = row.get('Listing Type (Paid/Free)', '') or row.get('Cost', '')
            if url and cost:
                cost_map[url] = cost

    # Read the sheet to map URLs to rows
    sheet = service.spreadsheets().values().get(
        spreadsheetId=spreadsheet_id, range="Sheet1!A1:B100").execute()
    values = sheet.get('values', [])

    if not values:
        print("No data found in sheet")
        exit(1)

    updates = []
    for i, row in enumerate(values[1:], start=2):
        if len(row) > 1:
            url = row[1]
            cost = cost_map.get(url)
            if cost:
                updates.append({
                    'range': f'Sheet1!J{i}',
                    'values': [[cost]]
                })

    if updates:
        # Note: batchUpdate for values is different from batchUpdate for formatting.
        # For simple value updates in different cells, we can use values().update
        # but for efficiency, we use a loop or a specific batch update if supported.
        # Since we are updating a single column, we can actually just write the column
        # if the rows align perfectly, but they might not.
        
        # Using a loop for reliability in this specific case
        for update in updates:
            service.spreadsheets().values().update(
                spreadsheetId=spreadsheet_id,
                range=update['range'],
                valueInputOption='RAW',
                body={'values': update['values']}
            ).execute()
        print(f"Successfully updated {len(updates)} rows in Google Sheet.")
    else:
        print("No matching URLs found to update.")

except Exception as e:
    print(f"Error: {e}")
