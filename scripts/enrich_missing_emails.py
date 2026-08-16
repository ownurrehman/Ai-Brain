import json, os, re, time
from urllib.parse import urljoin, urlparse
from google.oauth2.service_account import Credentials
from googleapiclient.discovery import build
import requests
from bs4 import BeautifulSoup

SPREADSHEET_ID = "11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4"
SHEET_NAME = "Lead Pipeline"
CREDS_PATH = os.path.expanduser("~/.config/google-sheets/credentials.json")
SCOPES = ["https://www.googleapis.com/auth/spreadsheets"]

EMAIL_RE = re.compile(r"[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}")
HEADERS = {
    "User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
}

def get_service():
    creds = Credentials.from_service_account_file(CREDS_PATH, scopes=SCOPES)
    return build("sheets", "v4", credentials=creds, static_discovery=False)

def find_emails(text):
    emails = EMAIL_RE.findall(text)
    # Filter out common false positives
    filtered = []
    for e in emails:
        e = e.lower()
        if any(bad in e for bad in ["example.com", "domain.com", "test.com", "yourdomain", "sentry.", "wixpress", "mailchimp"]):
            continue
        filtered.append(e)
    return list(dict.fromkeys(filtered))  # preserve order, remove duplicates

def fetch_page(url):
    try:
        if not url.startswith("http"):
            url = "https://" + url
        resp = requests.get(url, headers=HEADERS, timeout=15, allow_redirects=True)
        if resp.status_code == 200:
            return resp.text, resp.url
    except Exception as e:
        pass
    return None, None

def extract_emails_from_site(homepage_url):
    """Try homepage and common contact/about pages"""
    emails = []
    all_text = ""
    
    # Normalize homepage
    if not homepage_url.startswith("http"):
        homepage_url = "https://" + homepage_url
    parsed = urlparse(homepage_url)
    base = f"{parsed.scheme}://{parsed.netloc}"
    
    pages_to_try = [homepage_url]
    # Add common contact page paths
    for path in ["/contact", "/contact-us", "/about", "/about-us", "/team", "/reach-us", "/get-in-touch"]:
        pages_to_try.append(urljoin(base, path))
    
    visited = set()
    for page_url in pages_to_try:
        if page_url in visited:
            continue
        visited.add(page_url)
        html, final_url = fetch_page(page_url)
        if html:
            try:
                soup = BeautifulSoup(html, "html.parser")
                # Remove script/style
                for script in soup(["script", "style"]):
                    script.decompose()
                text = soup.get_text(separator=" ", strip=True)
                all_text += " " + text
                page_emails = find_emails(text)
                emails.extend(page_emails)
                # Also check mailto links
                for a in soup.find_all("a", href=True):
                    href = a["href"]
                    if href.startswith("mailto:"):
                        emails.append(href.replace("mailto:", "").split("?")[0].lower())
            except Exception:
                pass
        time.sleep(0.5)
    
    # Deduplicate while preserving order, prefer info@/hello@/contact@ style
    seen = set()
    result = []
    priority = ["info@", "hello@", "contact@", "admin@", "support@"]
    
    # First priority emails
    for e in emails:
        if e not in seen and any(e.startswith(p) for p in priority):
            seen.add(e)
            result.append(e)
    # Then others
    for e in emails:
        if e not in seen:
            seen.add(e)
            result.append(e)
    
    return result[:5], all_text[:5000]  # top 5 emails + text snippet

def main():
    service = get_service()
    sheet_meta = service.spreadsheets().get(spreadsheetId=SPREADSHEET_ID).execute()
    sheet = next(s for s in sheet_meta["sheets"] if s["properties"]["title"] == SHEET_NAME)
    grid = sheet["properties"]["gridProperties"]
    sheet_id = sheet["properties"]["sheetId"]
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
    
    # Find rows missing email (col E) but have website (col G)
    rows_to_enrich = []
    for idx, row in enumerate(data, start=2):
        padded = row + [""] * (15 - len(row)) if len(row) < 15 else row[:15]
        email = str(padded[4]).strip() if padded[4] else ""
        website = str(padded[6]).strip() if padded[6] else ""
        if not email and website:
            rows_to_enrich.append({"row": idx, "business": str(padded[2]).strip() if padded[2] else "", "website": website})
    
    print(f"Found {len(rows_to_enrich)} rows missing email with website")
    
    # Process in batches
    batch_size = 20
    all_results = []
    
    for i in range(0, len(rows_to_enrich), batch_size):
        batch = rows_to_enrich[i:i+batch_size]
        print(f"\n--- Batch {i//batch_size + 1}/{(len(rows_to_enrich) + batch_size - 1)//batch_size} ---")
        
        batch_updates = []
        for item in batch:
            print(f"Row {item['row']}: {item['business']} | {item['website']}")
            emails, snippet = extract_emails_from_site(item["website"])
            print(f"  Found emails: {emails}")
            all_results.append({
                "row": item["row"],
                "business": item["business"],
                "website": item["website"],
                "emails_found": emails,
                "snippet": snippet[:300]
            })
            if emails:
                best_email = emails[0]
                batch_updates.append({
                    "range": {
                        "sheetId": sheet_id,
                        "startRowIndex": item["row"] - 1,
                        "endRowIndex": item["row"],
                        "startColumnIndex": 4,
                        "endColumnIndex": 5
                    },
                    "rows": [{"values": [{"userEnteredValue": {"stringValue": best_email}}]}],
                    "fields": "userEnteredValue"
                })
            time.sleep(1)
        
        if batch_updates:
            body = {"requests": [{"updateCells": u} for u in batch_updates]}
            service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
            print(f"Updated {len(batch_updates)} rows in this batch")
        
        time.sleep(3)  # Pause between batches
    
    # Save results
    out_path = "/tmp/rankray_email_enrichment_results.json"
    with open(out_path, "w") as f:
        json.dump(all_results, f, indent=2)
    print(f"\n✅ Email enrichment complete. Results saved to {out_path}")
    print(f"Total rows processed: {len(rows_to_enrich)}")
    updated = len([r for r in all_results if r["emails_found"]])
    print(f"Emails found and updated: {updated}")
    print(f"No email found: {len(rows_to_enrich) - updated}")

if __name__ == "__main__":
    main()
