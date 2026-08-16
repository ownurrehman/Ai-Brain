import json, os, re
from google.oauth2.service_account import Credentials
from googleapiclient.discovery import build

SPREADSHEET_ID = "11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4"
SHEET_NAME = "Lead Pipeline"
CREDS_PATH = os.path.expanduser("~/.config/google-sheets/credentials.json")
SCOPES = ["https://www.googleapis.com/auth/spreadsheets"]

COLUMN_NAMES = [
    "A - Lead ID",
    "B - Date Added",
    "C - Business Name",
    "D - Contact Person",
    "E - Email",
    "F - Phone",
    "G - Website",
    "H - Address",
    "I - Industry",
    "J - Location",
    "K - Pain Points",
    "L - Pitch/Solution",
    "M - Lead Grade",
    "N - Status",
    "O - Email Draft"
]

def get_service():
    creds = Credentials.from_service_account_file(CREDS_PATH, scopes=SCOPES)
    return build("sheets", "v4", credentials=creds, static_discovery=False)

def main():
    service = get_service()
    sheet_meta = service.spreadsheets().get(spreadsheetId=SPREADSHEET_ID).execute()
    sheet = next(s for s in sheet_meta["sheets"] if s["properties"]["title"] == SHEET_NAME)
    grid = sheet["properties"]["gridProperties"]
    last_row = grid.get("rowCount", 1000)
    last_col = grid.get("columnCount", 47)
    end_col = "A" + chr(ord("A") + (last_col - 27)) if last_col > 26 else chr(ord("A") + last_col - 1)
    
    range_name = f"{SHEET_NAME}!A1:{end_col}{last_row}"
    result = service.spreadsheets().values().get(spreadsheetId=SPREADSHEET_ID, range=range_name).execute()
    values = result.get("values", [])
    header = values[0] if values else []
    data = values[1:] if len(values) > 1 else []
    
    print(f"Total rows: {len(data)}")
    print(f"Header columns: {len(header)}")
    print(f"Header: {header[:15]}")
    
    incomplete_rows = []
    
    for idx, row in enumerate(data, start=2):
        # Pad to 15 columns
        padded = row + [""] * (15 - len(row)) if len(row) < 15 else row[:15]
        missing = []
        for col_idx, col_name in enumerate(COLUMN_NAMES):
            val = str(padded[col_idx]).strip() if padded[col_idx] else ""
            if not val or val == "#ERROR!":
                missing.append(col_name)
        if missing:
            incomplete_rows.append({
                "row": idx,
                "business": str(padded[2]).strip() if padded[2] else "",
                "missing": missing,
                "values": padded
            })
    
    print(f"\nRows with ANY empty column A-O: {len(incomplete_rows)}")
    
    if incomplete_rows:
        print("\n=== INCOMPLETE ROWS ===")
        for r in incomplete_rows:
            print(f"Row {r['row']}: {r['business'][:45]}")
            print(f"  Missing: {', '.join(r['missing'])}")
            print(f"  Values: {r['values'][:15]}")
    else:
        print("\n✅ All 368 rows have every column A-O filled.")
    
    with open("/tmp/rankray_incomplete_rows.json", "w") as f:
        json.dump(incomplete_rows, f, indent=2)
    
    return incomplete_rows

if __name__ == "__main__":
    main()
