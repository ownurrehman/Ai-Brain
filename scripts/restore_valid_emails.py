import json, os, re
from google.oauth2.service_account import Credentials
from googleapiclient.discovery import build

SPREADSHEET_ID = "11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4"
SHEET_NAME = "Lead Pipeline"
CREDS_PATH = os.path.expanduser("~/.config/google-sheets/credentials.json")
SCOPES = ["https://www.googleapis.com/auth/spreadsheets"]

# Emails that are truly bad/wrong and should stay cleared
TRULY_BAD = {
    "shutterstock_1091175569@1x-1.webp",
    "u0022usc2thdoc@cox.net",
    "webreporting@gargle.com",
}

def get_service():
    creds = Credentials.from_service_account_file(CREDS_PATH, scopes=SCOPES)
    return build("sheets", "v4", credentials=creds, static_discovery=False)

def main():
    with open("/tmp/rankray_bad_emails.json") as f:
        bad_emails = json.load(f)
    
    to_restore = [r for r in bad_emails if r["email"] not in TRULY_BAD]
    print(f"Restoring {len(to_restore)} emails that were cleared too aggressively:")
    for r in to_restore:
        print(f"  Row {r['row']}: {r['email']} ({r['business']})")
    
    if not to_restore:
        print("Nothing to restore.")
        return
    
    service = get_service()
    sheet_meta = service.spreadsheets().get(spreadsheetId=SPREADSHEET_ID).execute()
    sheet = next(s for s in sheet_meta["sheets"] if s["properties"]["title"] == SHEET_NAME)
    sheet_id = sheet["properties"]["sheetId"]
    
    requests = []
    for r in to_restore:
        requests.append({
            "updateCells": {
                "range": {
                    "sheetId": sheet_id,
                    "startRowIndex": r["row"] - 1,
                    "endRowIndex": r["row"],
                    "startColumnIndex": 4,
                    "endColumnIndex": 5
                },
                "rows": [{"values": [{"userEnteredValue": {"stringValue": r["email"]}}]}],
                "fields": "userEnteredValue"
            }
        })
    
    body = {"requests": requests}
    service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
    print(f"\n✅ Restored {len(to_restore)} emails.")

if __name__ == "__main__":
    main()
