import json, os, re
from google.oauth2.service_account import Credentials
from googleapiclient.discovery import build
from googleapiclient.errors import HttpError
from datetime import datetime

SPREADSHEET_ID = "11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4"
SOURCE_SHEET = "Lead Pipeline"
CREDS_PATH = os.path.expanduser("~/.config/google-sheets/credentials.json")
SCOPES = ["https://www.googleapis.com/auth/spreadsheets"]

def get_service():
    creds = Credentials.from_service_account_file(CREDS_PATH, scopes=SCOPES)
    return build("sheets", "v4", credentials=creds, static_discovery=False)

def main():
    service = get_service()
    date_str = datetime.now().strftime("%Y%m%d-%H%M%S")
    backup_name = f"Lead Pipeline Backup {date_str}"
    
    # Copy sheet
    body = {
        "destinationSpreadsheetId": SPREADSHEET_ID
    }
    sheet_meta = service.spreadsheets().get(spreadsheetId=SPREADSHEET_ID).execute()
    source = next(s for s in sheet_meta["sheets"] if s["properties"]["title"] == SOURCE_SHEET)
    source_id = source["properties"]["sheetId"]
    
    result = service.spreadsheets().sheets().copyTo(
        spreadsheetId=SPREADSHEET_ID,
        sheetId=source_id,
        body=body
    ).execute()
    
    new_sheet_id = result["sheetId"]
    
    # Rename the copied sheet
    req = {
        "requests": [{
            "updateSheetProperties": {
                "properties": {
                    "sheetId": new_sheet_id,
                    "title": backup_name
                },
                "fields": "title"
            }
        }]
    }
    service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=req).execute()
    print(f"✅ Backup created: '{backup_name}' (sheetId: {new_sheet_id})")

if __name__ == "__main__":
    main()
