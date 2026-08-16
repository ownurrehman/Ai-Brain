import json, os, re, time
from urllib.parse import urlparse
from google.oauth2.service_account import Credentials
from googleapiclient.discovery import build
import requests

SPREADSHEET_ID = "11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4"
SHEET_NAME = "Lead Pipeline"
CREDS_PATH = os.path.expanduser("~/.config/google-sheets/credentials.json")
SCOPES = ["https://www.googleapis.com/auth/spreadsheets"]
FIRECRAWL_API_KEY = os.environ.get("FIRECRAWL_API_KEY")

EMAIL_RE = re.compile(r"[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}")
BAD_DOMAINS = ["example.com", "domain.com", "test.com", "gmail.com", "yahoo.com", "hotmail.com", "outlook.com", "icloud.com", "gargle.com", "wixpress", "mailchimp", "sentry.io"]


def log(msg):
    print(msg, flush=True)


def get_service():
    creds = Credentials.from_service_account_file(CREDS_PATH, scopes=SCOPES)
    return build("sheets", "v4", credentials=creds, static_discovery=False)


def find_emails(text, website_domain):
    emails = EMAIL_RE.findall(text)
    website_domain = website_domain.lower().replace("www.", "") if website_domain else ""
    filtered = []
    for e in emails:
        e = e.lower()
        if any(bad in e for bad in BAD_DOMAINS):
            continue
        if ".webp" in e or ".png" in e or ".jpg" in e or ".jpeg" in e or ".gif" in e:
            continue
        if e.startswith("u0022") or e.startswith("u003") or "@1x" in e:
            continue
        if website_domain and website_domain not in e:
            if not any(e.startswith(x) for x in ["info", "hello", "contact", "office", "admin", "support", "reception", "welcome", "helpdesk", "help", "desk"]):
                continue
        filtered.append(e)
    return list(dict.fromkeys(filtered))


def pick_best_email(emails, website_domain):
    if not emails:
        return None
    website_domain = website_domain.lower().replace("www.", "")
    same_domain = [e for e in emails if website_domain in e.split("@")[-1]]
    if same_domain:
        emails = same_domain
    priority = ["info", "hello", "contact", "office", "admin", "support", "reception", "welcome", "helpdesk", "help", "desk"]
    for p in priority:
        for e in emails:
            if e.startswith(p + "@"):
                return e
    return emails[0]


def firecrawl_scrape(url):
    if not FIRECRAWL_API_KEY:
        return None
    try:
        headers = {"Authorization": f"Bearer {FIRECRAWL_API_KEY}", "Content-Type": "application/json"}
        payload = {"url": url, "formats": ["markdown"]}
        resp = requests.post("https://api.firecrawl.dev/v1/scrape", headers=headers, json=payload, timeout=30)
        if resp.status_code == 200:
            data = resp.json()
            if data.get("success") and data.get("data"):
                return data["data"].get("markdown", "")
    except Exception as e:
        log(f"    Firecrawl error for {url}: {e}")
    return None


def main():
    log("Reading sheet...")
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

    progress_path = "/tmp/rankray_email_enrichment_firecrawl_seq.json"
    all_results = []
    if os.path.exists(progress_path):
        with open(progress_path) as f:
            all_results = json.load(f)
        log(f"Resumed: {len(all_results)} already done.")

    processed_rows = {r["row"] for r in all_results}
    pending = [item for item in rows_to_enrich if item["row"] not in processed_rows]

    batch_updates = []
    for i, item in enumerate(pending):
        parsed = urlparse(item["website"]) if item["website"].startswith("http") else urlparse("https://" + item["website"])
        domain = parsed.netloc.replace("www.", "")
        url = item["website"] if item["website"].startswith("http") else "https://" + item["website"]

        log(f"[{i+1}/{len(pending)}] Row {item['row']}: {item['business'][:45]} | {domain}")
        markdown = firecrawl_scrape(url)
        if not markdown:
            log(f"    -> Firecrawl failed")
            all_results.append({"row": item["row"], "business": item["business"], "website": item["website"], "emails_found": [], "selected": None})
            with open(progress_path, "w") as f:
                json.dump(all_results, f, indent=2)
            continue

        emails = find_emails(markdown, domain)
        best = pick_best_email(emails, domain)
        status = "✅" if best else "🌐"
        log(f"    -> {status} {best or emails[:3] or 'no email'}")

        res = {"row": item["row"], "business": item["business"], "website": item["website"], "emails_found": emails[:5], "selected": best}
        all_results.append(res)
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
            if len(batch_updates) >= 10:
                body = {"requests": batch_updates}
                service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
                log(f"    -> Batch updated {len(batch_updates)} rows in sheet")
                batch_updates = []

        time.sleep(0.3)

    if batch_updates:
        body = {"requests": batch_updates}
        service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
        log(f"    -> Final batch updated {len(batch_updates)} rows in sheet")

    updated = len([r for r in all_results if r["selected"]])
    log(f"\n✅ DONE. Processed {len(all_results)} rows.")
    log(f"Emails found and updated: {updated}")
    log(f"No email found: {len(all_results) - updated}")


if __name__ == "__main__":
    main()
