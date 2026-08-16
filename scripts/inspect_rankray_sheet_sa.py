import json, os
from google.oauth2.service_account import Credentials
from googleapiclient.discovery import build

SPREADSHEET_ID = "11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4"
SHEET_NAME = "Lead Pipeline"
CREDS_PATH = os.path.expanduser("~/.config/google-sheets/credentials.json")
SCOPES = ["https://www.googleapis.com/auth/spreadsheets"]

def main():
    creds = Credentials.from_service_account_file(CREDS_PATH, scopes=SCOPES)
    service = build("sheets", "v4", credentials=creds, static_discovery=False)
    
    sheet_meta = service.spreadsheets().get(spreadsheetId=SPREADSHEET_ID).execute()
    sheet = next((s for s in sheet_meta["sheets"] if s["properties"]["title"] == SHEET_NAME), None)
    if not sheet:
        print(f"Sheet '{SHEET_NAME}' not found. Available sheets:")
        for s in sheet_meta["sheets"]:
            print(" -", s["properties"]["title"])
        return
    
    grid = sheet["properties"]["gridProperties"]
    last_row = grid.get("rowCount", 1000)
    last_col = grid.get("columnCount", 26)
    print(f"Sheet dimensions: {last_row} rows x {last_col} cols")
    
    # Read all data
    end_col = chr(ord("A") + last_col - 1) if last_col <= 26 else "Z"
    range_name = f"{SHEET_NAME}!A1:{end_col}{last_row}"
    result = service.spreadsheets().values().get(spreadsheetId=SPREADSHEET_ID, range=range_name).execute()
    values = result.get("values", [])
    print(f"Total rows with data: {len(values)}")
    
    if not values:
        print("No data found.")
        return
    
    print("\n--- HEADER ---")
    print(values[0])
    print(f"\n--- FIRST 30 DATA ROWS ---")
    for i, row in enumerate(values[1:31], start=2):
        print(f"Row {i}: {row}")
    
    out_path = "/tmp/rankray_leads_dump.json"
    with open(out_path, "w") as f:
        json.dump(values, f, indent=2)
    print(f"\nFull dump saved to: {out_path}")

if __name__ == "__main__":
    main()
