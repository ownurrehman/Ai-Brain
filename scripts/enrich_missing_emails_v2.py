import json, os, re, time
from google.oauth2.service_account import Credentials
from googleapiclient.discovery import build
import requests

SPREADSHEET_ID = "11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4"
SHEET_NAME = "Lead Pipeline"
CREDS_PATH = os.path.expanduser("~/.config/google-sheets/credentials.json")
SCOPES = ["https://www.googleapis.com/auth/spreadsheets"]

EMAIL_RE = re.compile(r"[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}")
HEADERS = {
    "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
}

BAD_DOMAINS = ["example.com", "domain.com", "test.com", "yourdomain", "sentry.", "wixpress", "mailchimp", "gmail.com", "yahoo.com", "outlook.com", "hotmail.com"]

def get_service():
    creds = Credentials.from_service_account_file(CREDS_PATH, scopes=SCOPES)
    return build("sheets", "v4", credentials=creds, static_discovery=False)

def find_emails(text):
    emails = EMAIL_RE.findall(text)
    filtered = []
    for e in emails:
        e = e.lower()
        if any(bad in e for bad in BAD_DOMAINS):
            continue
        filtered.append(e)
    return list(dict.fromkeys(filtered))

def fetch_page(url):
    try:
        if not url.startswith("http"):
            url = "https://" + url
        resp = requests.get(url, headers=HEADERS, timeout=8, allow_redirects=True)
        if resp.status_code == 200:
            return resp.text
    except Exception:
        pass
    return ""

def pick_best_email(emails):
    if not emails:
        return None
    priority = ["info@", "hello@", "contact@", "admin@", "support@", "enquiries@", "enquiry@"]
    for p in priority:
        for e in emails:
            if e.startswith(p):
                return e
    return emails[0]

def main():
    service = get_service()
    sheet_meta = service.spreadsheets().get(spreadsheetId=SPREADSHEET_ID).execute()
    sheet = next(s for s in sheet_meta["sheets"] if s["properties"]["title"] == SHEET_NAME)
    sheet_id = sheet["properties"]["sheetId"]
    grid = sheet["properties"]["gridProperties"]
    last_row = grid.get("rowCount", 1000)
    last_col = grid.get("columnCount", 47)
    
    end_col = "A"
    if last_col > 26:
        end_col = "A" + chr(ord("A") + (last_col - 27))
    else:
        end_col = chr(ord("A") + last_col - 1)
    
    range_name = f"{SHEET_NAME}!A1:{end_col}{last_row}"
    result = service.spreadsheets().values().get(spreadsheetId=SPREADSHEET_ID, range=range_name).execute()
    values = result.get("values", [])
    data = values[1:] if len(values) > 1 else []
    
    rows_to_enrich = []
    for idx, row in enumerate(data, start=2):
        padded = row + [""] * (15 - len(row)) if len(row) < 15 else row[:15]
        email = str(padded[4]).strip() if padded[4] else ""
        website = str(padded[6]).strip() if padded[6] else ""
        if not email and website:
            rows_to_enrich.append({"row": idx, "business": str(padded[2]).strip() if padded[2] else "", "website": website})
    
    print(f"Found {len(rows_to_enrich)} rows missing email with website")
    
    all_results = []
    batch_updates = []
    
    for i, item in enumerate(rows_to_enrich):
        print(f"[{i+1}/{len(rows_to_enrich)}] Row {item['row']}: {item['business']} | {item['website']}")
        html = fetch_page(item["website"])
        emails = find_emails(html)
        best = pick_best_email(emails)
        print(f"  Emails found: {emails[:5]} | Best: {best}")
        
        all_results.append({
            "row": item["row"],
            "business": item["business"],
            "website": item["website"],
            "emails_found": emails[:5],
            "selected": best
        })
        
        if best:
            batch_updates.append({
                "updateCells": {
                    "range": {
                        "sheetId": sheet_id,
                        "startRowIndex": item["row"] - 1,
                        "endRowIndex": item["row"],
                        "startColumnIndex": 4,
                        "endColumnIndex": 5
                    },
                    "rows": [{"values": [{"userEnteredValue": {"stringValue": best}}]}],
                    "fields": "userEnteredValue"
                }
            })
        
        # Update sheet in batches of 25 to avoid huge requests
        if len(batch_updates) >= 25:
            body = {"requests": batch_updates}
            service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
            print(f"  -> Updated {len(batch_updates)} rows in sheet")
            batch_updates = []
        
        time.sleep(0.3)
    
    # Final batch
    if batch_updates:
        body = {"requests": batch_updates}
        service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
        print(f"  -> Updated final {len(batch_updates)} rows in sheet")
    
    out_path = "/tmp/rankray_email_enrichment_results_v2.json"
    with open(out_path, "w") as f:
        json.dump(all_results, f, indent=2)
    
    updated = len([r for r in all_results if r["selected"]])
    print(f"\n✅ Done. Processed {len(rows_to_enrich)} rows.")
    print(f"Emails found and updated: {updated}")
    print(f"No email found: {len(rows_to_enrich) - updated}")
    print(f"Results saved to {out_path}")

if __name__ == "__main__":
    main()
