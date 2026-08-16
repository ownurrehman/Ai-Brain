import json, os, re, subprocess, time
from urllib.parse import urljoin, urlparse
from google.oauth2.service_account import Credentials
from googleapiclient.discovery import build

SPREADSHEET_ID = "11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4"
SHEET_NAME = "Lead Pipeline"
CREDS_PATH = os.path.expanduser("~/.config/google-sheets/credentials.json")
SCOPES = ["https://www.googleapis.com/auth/spreadsheets"]

EMAIL_RE = re.compile(r"[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}")
BAD_DOMAINS = ["example.com", "domain.com", "test.com", "yourdomain", "sentry.", "wixpress", "mailchimp", "gmail.com", "yahoo.com", "outlook.com", "hotmail.com", "icloud.com"]
UA = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"

def log(msg):
    print(msg, flush=True)

def get_service():
    log("Authenticating with service account...")
    creds = Credentials.from_service_account_file(CREDS_PATH, scopes=SCOPES)
    log("Building Sheets service...")
    service = build("sheets", "v4", credentials=creds, static_discovery=False)
    log("Service ready.")
    return service

def find_emails(text):
    emails = EMAIL_RE.findall(text)
    filtered = []
    for e in emails:
        e = e.lower()
        if any(bad in e for bad in BAD_DOMAINS):
            continue
        filtered.append(e)
    return list(dict.fromkeys(filtered))

def curl_fetch(url):
    """Returns (html, ok). Hard 12s total timeout at OS level."""
    try:
        result = subprocess.run(
            ["curl", "-sL", "--max-time", "8", "--connect-timeout", "5", "-A", UA, url],
            capture_output=True, text=True, timeout=12
        )
        if result.returncode == 0 and len(result.stdout) > 100:
            return result.stdout, True
    except Exception:
        pass
    return "", False

def fetch_all_pages(start_url):
    if not start_url.startswith("http"):
        start_url = "https://" + start_url
    html, ok = curl_fetch(start_url)
    if not ok:
        return ""
    all_html = html
    try:
        parsed = urlparse(start_url)
        base = f"{parsed.scheme}://{parsed.netloc}"
        for path in ["/contact", "/contact-us"]:
            page_html, page_ok = curl_fetch(urljoin(base, path))
            if page_ok:
                all_html += " " + page_html
    except Exception:
        pass
    return all_html

def pick_best_email(emails, website_domain):
    if not emails:
        return None
    website_domain = website_domain.lower().replace("www.", "")
    # Strongly prefer emails from the same domain
    same_domain = [e for e in emails if website_domain in e.split("@")[-1]]
    if same_domain:
        emails = same_domain
    priority = ["info@", "hello@", "contact@", "admin@", "support@", "enquiries@", "enquiry@", "office@", "reception@"]
    for p in priority:
        for e in emails:
            if e.startswith(p):
                return e
    return emails[0]

def main():
    service = get_service()
    
    log("Reading sheet metadata...")
    sheet_meta = service.spreadsheets().get(spreadsheetId=SPREADSHEET_ID).execute()
    sheet = next(s for s in sheet_meta["sheets"] if s["properties"]["title"] == SHEET_NAME)
    sheet_id = sheet["properties"]["sheetId"]
    
    log("Reading full data range...")
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
    log(f"Read {len(data)} data rows.")
    
    rows_to_enrich = []
    for idx, row in enumerate(data, start=2):
        padded = row + [""] * (15 - len(row)) if len(row) < 15 else row[:15]
        email = str(padded[4]).strip() if padded[4] else ""
        website = str(padded[6]).strip() if padded[6] else ""
        if not email and website:
            rows_to_enrich.append({
                "row": idx,
                "business": str(padded[2]).strip() if padded[2] else "",
                "website": website
            })
    log(f"Found {len(rows_to_enrich)} rows missing email with website.")
    
    progress_path = "/tmp/rankray_email_enrichment_progress.json"
    all_results = []
    if os.path.exists(progress_path):
        with open(progress_path) as f:
            all_results = json.load(f)
        log(f"Resumed from progress file: {len(all_results)} already done.")
    
    processed_rows = {r["row"] for r in all_results}
    batch_updates = []
    
    for i, item in enumerate(rows_to_enrich):
        if item["row"] in processed_rows:
            continue
        
        log(f"[{i+1}/{len(rows_to_enrich)}] Row {item['row']}: {item['business'][:45]} | {item['website']}")
        html = fetch_all_pages(item["website"])
        emails = find_emails(html)
        parsed = urlparse(item["website"]) if item["website"].startswith("http") else urlparse("https://" + item["website"])
        domain = parsed.netloc.replace("www.", "")
        best = pick_best_email(emails, domain)
        
        status = "✅" if best else ("🌐" if html else "❌")
        log(f"  -> {status} {best or emails[:3] or 'no email'}")
        
        res = {
            "row": item["row"],
            "business": item["business"],
            "website": item["website"],
            "fetch_ok": bool(html),
            "emails_found": emails[:5],
            "selected": best
        }
        all_results.append(res)
        
        # Save progress after every row
        with open(progress_path, "w") as f:
            json.dump(all_results, f, indent=2)
        
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
            # Update sheet every 10 emails to avoid huge batch
            if len(batch_updates) >= 10:
                body = {"requests": batch_updates}
                service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
                log(f"  -> Updated 10 rows in sheet (total saved so far: {len([r for r in all_results if r['selected']])})")
                batch_updates = []
        
        time.sleep(0.5)
    
    # Final batch update
    if batch_updates:
        body = {"requests": batch_updates}
        service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
        log(f"Updated final {len(batch_updates)} rows in sheet.")
    
    updated = len([r for r in all_results if r["selected"]])
    fetched = len([r for r in all_results if r["fetch_ok"]])
    log(f"\n✅ DONE. Processed {len(all_results)} rows.")
    log(f"Sites reachable: {fetched}")
    log(f"Emails found and updated: {updated}")
    log(f"No email found: {len(all_results) - updated}")

if __name__ == "__main__":
    main()
