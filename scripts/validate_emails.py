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

def is_valid_email(email, website_domain):
    if not email or "@" not in email:
        return False
    parts = email.split("@")
    if len(parts) != 2:
        return False
    local, domain = parts
    if not local or not domain:
        return False
    # Reject image filenames and weird strings
    if ".webp" in email or ".png" in email or ".jpg" in email or ".jpeg" in email or ".gif" in email:
        return False
    if email.startswith("u0022") or email.startswith("u003") or "@1x" in email:
        return False
    # Reject third-party generic SEO/reporting emails
    bad_locals = ["webreporting", "shutterstock", "noreply", "no-reply", "salesforce", "hubspot", "mailgun", "sendgrid"]
    if any(bl in local.lower() for bl in bad_locals):
        return False
    # Prefer same domain, but allow common contact emails from related domains
    if website_domain and website_domain.lower() not in domain.lower():
        # Allow if it's a clear contact email on same TLD
        if not any(local.lower().startswith(x) for x in ["info", "hello", "contact", "office", "admin", "support", "reception", "welcome", "helpdesk", "help", "desk"]):
            return False
    return True

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
        website = str(padded[6]).strip() if padded[6] else ""
        if not email:
            continue
        parsed = re.sub(r"^https?://", "", website).replace("www.", "")
        domain = parsed.split("/")[0] if parsed else ""
        if not is_valid_email(email, domain):
            print(f"Row {idx}: BAD EMAIL '{email}' for {website}")
            bad_emails.append({"row": idx, "email": email, "website": website, "business": str(padded[2]).strip()})
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
        print(f"\nClearing {len(requests)} bad emails...")
        body = {"requests": requests}
        service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
        print("Done.")
    else:
        print("\nNo bad emails found.")
    
    with open("/tmp/rankray_bad_emails.json", "w") as f:
        json.dump(bad_emails, f, indent=2)

if __name__ == "__main__":
    main()
