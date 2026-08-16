import json, os, re, subprocess, time, concurrent.futures
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

def fetch_with_curl(url):
    """Fetch with OS-level timeout. Returns (html, success)."""
    try:
        result = subprocess.run(
            ["curl", "-sL", "--max-time", "8", "--connect-timeout", "5", "-A", UA, url],
            capture_output=True,
            text=True,
            timeout=12
        )
        if result.returncode == 0 and len(result.stdout) > 100:
            return result.stdout, True
    except Exception as e:
        pass
    return "", False

def fetch_contact_pages(base_url, homepage_html):
    """Quickly try a few contact page URLs"""
    parsed = urlparse(base_url)
    base = f"{parsed.scheme}://{parsed.netloc}"
    paths = ["/contact", "/contact-us", "/about", "/about-us", "/team"]
    all_html = homepage_html
    for path in paths:
        url = urljoin(base, path)
        html, ok = fetch_with_curl(url)
        if ok:
            all_html += " " + html
    return all_html

def process_one(item):
    website = item["website"]
    if not website.startswith("http"):
        website = "https://" + website
    
    html, ok = fetch_with_curl(website)
    if ok:
        html = fetch_contact_pages(website, html)
    
    emails = find_emails(html)
    
    # Pick best email
    best = None
    if emails:
        priority = ["info@", "hello@", "contact@", "admin@", "support@", "enquiries@", "enquiry@"]
        for p in priority:
            for e in emails:
                if e.startswith(p):
                    best = e
                    break
            if best:
                break
        if not best:
            best = emails[0]
    
    return {
        "row": item["row"],
        "business": item["business"],
        "website": item["website"],
        "fetch_ok": ok,
        "emails_found": emails[:5],
        "selected": best
    }

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
    
    # Process with thread pool
    all_results = []
    with concurrent.futures.ThreadPoolExecutor(max_workers=8) as executor:
        future_to_item = {executor.submit(process_one, item): item for item in rows_to_enrich}
        for i, future in enumerate(concurrent.futures.as_completed(future_to_item), 1):
            try:
                res = future.result(timeout=45)
                all_results.append(res)
                status = "✅" if res["selected"] else ("🌐" if res["fetch_ok"] else "❌")
                print(f"[{i}/{len(rows_to_enrich)}] Row {res['row']}: {res['business'][:40]} | {status} {res['selected'] or res['emails_found'][:1]}")
            except Exception as e:
                item = future_to_item[future]
                print(f"[{i}/{len(rows_to_enrich)}] Row {item['row']}: {item['business'][:40]} | ERROR {e}")
                all_results.append({"row": item["row"], "business": item["business"], "website": item["website"], "fetch_ok": False, "emails_found": [], "selected": None})
    
    # Update sheet in batches
    batch_updates = []
    for res in all_results:
        if res["selected"]:
            batch_updates.append({
                "updateCells": {
                    "range": {
                        "sheetId": sheet_id,
                        "startRowIndex": res["row"] - 1,
                        "endRowIndex": res["row"],
                        "startColumnIndex": 4,
                        "endColumnIndex": 5
                    },
                    "rows": [{"values": [{"userEnteredValue": {"stringValue": res["selected"]}}]}],
                    "fields": "userEnteredValue"
                }
            })
    
    if batch_updates:
        # Google allows max ~100 requests per batchUpdate safely
        for i in range(0, len(batch_updates), 50):
            chunk = batch_updates[i:i+50]
            body = {"requests": chunk}
            service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
            print(f"Updated {len(chunk)} rows in sheet")
    
    out_path = "/tmp/rankray_email_enrichment_results_v3.json"
    with open(out_path, "w") as f:
        json.dump(all_results, f, indent=2)
    
    updated = len([r for r in all_results if r["selected"]])
    fetched = len([r for r in all_results if r["fetch_ok"]])
    print(f"\n✅ Done. Processed {len(rows_to_enrich)} rows.")
    print(f"Sites reachable: {fetched}")
    print(f"Emails found and updated: {updated}")
    print(f"No email found: {len(rows_to_enrich) - updated}")
    print(f"Results saved to {out_path}")

if __name__ == "__main__":
    main()
