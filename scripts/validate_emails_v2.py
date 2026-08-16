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

def is_truly_bad_email(email):
    if not email or "@" not in email:
        return False
    e = email.lower()
    # Only clear obviously fake/bugged emails
    if ".webp" in e or ".png" in e or ".jpg" in e or ".jpeg" in e or ".gif" in e:
        return True, "image filename"
    if e.startswith("u0022") or e.startswith("u003") or "@1x" in e:
        return True, "escaped/encoded"
    if "gargle.com" in e or "shutterstock" in e or "mailchimp" in e or "wixpress" in e:
        return True, "tool/placeholder"
    # Valid format check
    parts = email.split("@")
    if len(parts) != 2 or not parts[0] or not parts[1] or "." not in parts[1]:
        return True, "invalid format"
    # Clear very suspicious SEO/third-party generic emails
    if "webreporting@" in e or "sharekco.com" in e or "mypatientlink.com" in e or "gargle.com" in e:
        return True, "third-party service"
    return False, ""

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
    
    bad_emails = []
    requests = []
    
    for idx, row in enumerate(data, start=2):
        padded = row + [""] * (15 - len(row)) if len(row) < 15 else row[:15]
        email = str(padded[4]).strip() if padded[4] else ""
        if not email:
            continue
        is_bad, reason = is_truly_bad_email(email)
        if is_bad:
            print(f"Row {idx}: BAD EMAIL '{email}' - {reason}")
            bad_emails.append({"row": idx, "email": email, "business": str(padded[2]).strip(), "reason": reason})
            requests.append({
                "updateCells": {
                    "range": {
                        "sheetId": sheet_id,
                        "startRowIndex": idx - 1,
                        "endRowIndex": idx,
                        "startColumnIndex": 4,
                        "endColumnIndex": 5
                    },
                    "rows": [{"values": [{"userEnteredValue": {"stringValue": ""}}]}],
                    "fields": "userEnteredValue"
                }
            })
    
    if requests:
        print(f"\nClearing {len(requests)} truly bad emails...")
        body = {"requests": requests}
        service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
        print("Done.")
    else:
        print("\nNo bad emails found.")
    
    with open("/tmp/rankray_bad_emails_v2.json", "w") as f:
        json.dump(bad_emails, f, indent=2)

if __name__ == "__main__":
    main()
