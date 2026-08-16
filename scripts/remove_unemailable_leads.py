import json, os, re
from google.oauth2.service_account import Credentials
from googleapiclient.discovery import build

SPREADSHEET_ID = "11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4"
SHEET_NAME = "Lead Pipeline"
CREDS_PATH = os.path.expanduser("~/.config/google-sheets/credentials.json")
SCOPES = ["https://www.googleapis.com/auth/spreadsheets"]

def get_service():
    creds = Credentials.from_service_account_file(CREDS_PATH, scopes=SCOPES)
    return build("sheets", "v4", credentials=creds, static_discovery=False)

def main():
    service = get_service()
    sheet_meta = service.spreadsheets().get(spreadsheetId=SPREADSHEET_ID).execute()
    sheet = next(s for s in sheet_meta["sheets"] if s["properties"]["title"] == SHEET_NAME)
    sheet_id = sheet["properties"]["sheetId"]
    grid = sheet["properties"]["gridProperties"]
    last_row = grid.get("rowCount", 1000)
    last_col = grid.get("columnCount", 47)
    end_col = "A" + chr(ord("A") + (last_col - 27)) if last_col > 26 else chr(ord("A") + last_col - 1)
    
    range_name = f"{SHEET_NAME}!A1:{end_col}{last_row}"
    result = service.spreadsheets().values().get(spreadsheetId=SPREADSHEET_ID, range=range_name).execute()
    values = result.get("values", [])
    data = values[1:] if len(values) > 1 else []
    
    rows_to_delete = []
    for idx, row in enumerate(data, start=2):
        padded = row + [""] * (15 - len(row)) if len(row) < 15 else row[:15]
        email = str(padded[4]).strip() if padded[4] else ""
        website = str(padded[6]).strip() if padded[6] else ""
        if not email:
            rows_to_delete.append({
                "row": idx,
                "business": str(padded[2]).strip() if padded[2] else "",
                "website": website,
                "reason": "no email" if website else "no email + no website"
            })
    
    print(f"Rows to delete (no email): {len(rows_to_delete)}")
    for r in rows_to_delete[:20]:
        print(f"  Row {r['row']}: {r['business'][:45]} | {r['reason']}")
    if len(rows_to_delete) > 20:
        print(f"  ... and {len(rows_to_delete) - 20} more")
    
    # Build delete requests in descending order
    requests = []
    for r in sorted(rows_to_delete, key=lambda x: x["row"], reverse=True):
        requests.append({
            "deleteDimension": {
                "range": {
                    "sheetId": sheet_id,
                    "dimension": "ROWS",
                    "startIndex": r["row"] - 1,
                    "endIndex": r["row"]
                }
            }
        })
    
    if requests:
        # Delete in chunks to avoid huge requests
        for i in range(0, len(requests), 100):
            chunk = requests[i:i+100]
            service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body={"requests": chunk}).execute()
            print(f"Deleted batch {i//100 + 1}/{(len(requests) + 99)//100}")
    
    with open("/tmp/rankray_deleted_unemailable.json", "w") as f:
        json.dump(rows_to_delete, f, indent=2)
    
    print(f"\n✅ Deleted {len(rows_to_delete)} unemailable rows.")
    print(f"Final expected rows: {len(data) - len(rows_to_delete)}")

if __name__ == "__main__":
    main()
