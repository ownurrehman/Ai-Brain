import json, os, re
from datetime import datetime
from google.oauth2.service_account import Credentials
from googleapiclient.discovery import build

SPREADSHEET_ID = "11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4"
SHEET_NAME = "Lead Pipeline"
CREDS_PATH = os.path.expanduser("~/.config/google-sheets/credentials.json")
SCOPES = ["https://www.googleapis.com/auth/spreadsheets"]

def get_service():
    creds = Credentials.from_service_account_file(CREDS_PATH, scopes=SCOPES)
    return build("sheets", "v4", credentials=creds, static_discovery=False)

def generate_lead_id(date_str, existing_ids):
    """Generate unique lead ID like RR-CA-20260502-XXX"""
    base = f"RR-CA-{date_str.replace('-', '')}"
    nums = [int(re.search(rf"{base}-(\d+)", sid).group(1)) for sid in existing_ids if sid.startswith(base)]
    next_num = max(nums, default=0) + 1
    return f"{base}-{next_num:03d}"

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
    
    existing_ids = set()
    for row in data:
        if row and str(row[0]).strip():
            existing_ids.add(str(row[0]).strip())
    
    updates = []
    
    for idx, row in enumerate(data, start=2):
        padded = row + [""] * (15 - len(row)) if len(row) < 15 else row[:15]
        lead_id = str(padded[0]).strip() if padded[0] else ""
        date_added = str(padded[1]).strip() if padded[1] else ""
        business = str(padded[2]).strip() if padded[2] else ""
        industry = str(padded[8]).strip() if padded[8] else ""
        location = str(padded[9]).strip() if padded[9] else ""
        grade = str(padded[12]).strip() if padded[12] else ""
        status = str(padded[13]).strip() if padded[13] else ""
        website = str(padded[6]).strip() if padded[6] else ""
        
        # Generate Lead ID if missing
        if not lead_id and date_added and re.match(r"\d{4}-\d{2}-\d{2}", date_added):
            new_id = generate_lead_id(date_added, existing_ids)
            existing_ids.add(new_id)
            updates.append({
                "row": idx, "col": 0, "value": new_id,
                "reason": f"generated Lead ID for {business}"
            })
            print(f"Row {idx}: generated Lead ID {new_id} for {business}")
        
        # Fill missing location/grade/status for row 395 (The Wooly Pub)
        if business == "The Wooly Pub":
            if not location:
                updates.append({"row": idx, "col": 9, "value": "Guelph, ON, Canada", "reason": "location from address"})
            if not grade:
                updates.append({"row": idx, "col": 12, "value": "B", "reason": "restaurant lead grade"})
            if not status:
                updates.append({"row": idx, "col": 13, "value": "New Lead", "reason": "default status"})
        
        # Fix bad website values like #ERROR!
        if website == "#ERROR!":
            updates.append({"row": idx, "col": 6, "value": "", "reason": "cleared #ERROR! website"})
    
    if not updates:
        print("No updates needed.")
        return
    
    requests = []
    for u in updates:
        requests.append({
            "updateCells": {
                "range": {
                    "sheetId": sheet_id,
                    "startRowIndex": u["row"] - 1,
                    "endRowIndex": u["row"],
                    "startColumnIndex": u["col"],
                    "endColumnIndex": u["col"] + 1
                },
                "rows": [{"values": [{"userEnteredValue": {"stringValue": u["value"]}}]}],
                "fields": "userEnteredValue"
            }
        })
    
    # Batch in chunks of 50
    for i in range(0, len(requests), 50):
        chunk = requests[i:i+50]
        service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body={"requests": chunk}).execute()
        print(f"Applied batch {i//50 + 1}/{(len(requests) + 49)//50}")
    
    print(f"\n✅ Applied {len(updates)} field fills.")

if __name__ == "__main__":
    main()
