import json, os
from google.oauth2.credentials import Credentials
from google_auth_oauthlib.flow import InstalledAppFlow
from google.auth.transport.requests import Request
from googleapiclient.discovery import build
from googleapiclient.errors import HttpError

SPREADSHEET_ID = "11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4"
SHEET_NAME = "Leads"
CREDS_PATH = os.path.expanduser("~/.config/gcp/oauth-credentials.json")
TOKEN_PATH = os.path.expanduser("~/.config/gcp/token.json")
SCOPES = ["https://www.googleapis.com/auth/spreadsheets"]

def get_creds():
    creds = None
    if os.path.exists(TOKEN_PATH):
        creds = Credentials.from_authorized_user_file(TOKEN_PATH, SCOPES)
    if not creds or not creds.valid:
        if creds and creds.expired and creds.refresh_token:
            creds.refresh(Request())
        else:
            flow = InstalledAppFlow.from_client_secrets_file(CREDS_PATH, SCOPES)
            creds = flow.run_local_server(port=0)
        with open(TOKEN_PATH, "w") as token:
            token.write(creds.to_json())
    return creds

def main():
    service = build("sheets", "v4", credentials=get_creds(), static_discovery=False)
    # Get full sheet grid properties to know range
    sheet_meta = service.spreadsheets().get(spreadsheetId=SPREADSHEET_ID).execute()
    sheet = next(s for s in sheet_meta["sheets"] if s["properties"]["title"] == SHEET_NAME)
    grid = sheet["properties"]["gridProperties"]
    last_row = grid.get("rowCount", 1000)
    last_col = grid.get("columnCount", 26)
    end_col = chr(ord("A") + last_col - 1) if last_col <= 26 else "Z"
    range_name = f"{SHEET_NAME}!A1:{end_col}{last_row}"
    print(f"Reading range: {range_name}")
    result = service.spreadsheets().values().get(spreadsheetId=SPREADSHEET_ID, range=range_name).execute()
    values = result.get("values", [])
    print(f"Total rows read: {len(values)}")
    if not values:
        print("No data found.")
        return
    print("\n--- HEADER ---")
    print(values[0])
    print(f"\n--- FIRST 20 DATA ROWS ---")
    for i, row in enumerate(values[1:21], start=2):
        print(f"Row {i}: {row}")
    # Save full dump for inspection
    out_path = "/tmp/rankray_leads_dump.json"
    with open(out_path, "w") as f:
        json.dump(values, f, indent=2)
    print(f"\nFull dump saved to: {out_path}")

if __name__ == "__main__":
    main()
