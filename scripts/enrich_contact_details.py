import json, os, re
from concurrent.futures import ThreadPoolExecutor, as_completed
from urllib.parse import urlparse
from google.oauth2.service_account import Credentials
from googleapiclient.discovery import build
import requests
import time

SPREADSHEET_ID = "11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4"
SHEET_NAME = "Lead Pipeline"
CREDS_PATH = os.path.expanduser("~/.config/google-sheets/credentials.json")
SCOPES = ["https://www.googleapis.com/auth/spreadsheets"]
FIRECRAWL_API_KEY = ***"FIRECRAWL_API_KEY")

PHONE_RE = re.compile(r"[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}")

def get_service():
    creds = Credentials.from_service_account_file(CREDS_PATH, scopes=SCOPES)
    return build("sheets", "v4", credentials=creds, static_discovery=False)

def firecrawl_scrape(url):
    if not FIRECRAWL_API_KEY:
        *** None
    try:
        headers = {"Authorization": f"Bearer {FIRECRAWL_API_KEY}", "Content-Type": "application/json"}
        payload = {"url": url, "formats": ["markdown"]}
        resp = requests.post("https://api.firecrawl.dev/v1/scrape", headers=headers, json=payload, timeout=30)
        if resp.status_code == 200:
            data = resp.json()
            if data.get("success") and data.get("data"):
                return data["data"].get("markdown", "")
    except Exception:
        pass
    return None

def extract_phone(text):
    phones = PHONE_RE.findall(text)
    cleaned = []
    for p in phones:
        digits = re.sub(r"\D", "", p)
        if len(digits) >= 10:
            formatted = digits[-10:] if len(digits) <= 10 else digits
            if formatted not in cleaned:
                cleaned.append(formatted)
    if cleaned:
        best = cleaned[0]
        if len(best) == 10:
            return f"({best[:3]}) {best[3:6]}-{best[6:]}"
        return best
    return None

def extract_address(text, location):
    # Try to find address patterns
    lines = text.split("\n")
    for line in lines:
        line = line.strip()
        if len(line) < 20:
            continue
        # Look for street + city/state/zip
        if re.search(r"\d+\s+[A-Za-z]+", line) and any(x in line.lower() for x in ["st", "ave", "road", "rd", "blvd", "suite", "unit", "floor"]):
            if re.search(r"[A-Z]{2}\s?\d{5}", line) or re.search(r"[A-Za-z]+,\s*[A-Z]{2}", line) or re.search(r"[A-Za-z]+,\s*[A-Za-z]+", line):
                return line[:200]
    return None

def extract_contact_person(text, business, website):
    # Best-effort: look for names on team/about page
    # Pattern: name followed by title (CEO, Owner, Founder, Partner, President, Principal, Director)
    titles = ["owner", "founder", "ceo", "president", "principal", "director", "managing partner", "partner", "manager"]
    lines = text.split("\n")
    candidates = []
    for i, line in enumerate(lines):
        line = line.strip()
        if len(line) < 3 or len(line) > 80:
            continue
        # Check if next or same line has a title
        combined = (line + " " + (lines[i+1] if i+1 < len(lines) else "")).lower()
        if any(t in combined for t in titles):
            # Extract name-like string (2-3 capitalized words)
            words = line.split()
            if 2 <= len(words) <= 4:
                if all(w[0].isupper() for w in words if w):
                    name = " ".join(words)
                    # Filter out business names and common non-names
                    if business.split()[0].lower() not in name.lower() and not any(x in name.lower() for x in ["about", "team", "contact", "home", "services"]):
                        candidates.append(name)
    if candidates:
        return candidates[0]
    return None

def process_one(item):
    url = item["website"] if item["website"].startswith("http") else "https://" + item["website"]
    print(f"[{item['idx']}/{item['total']}] Row {item['row']}: {item['business'][:45]}")
    markdown = firecrawl_scrape(url)
    if not markdown:
        print(f"    -> Firecrawl failed")
        return {"row": item["row"], "business": item["business"], "website": item["website"], "phone": None, "address": None, "contact": None}
    
    phone = extract_phone(markdown) if not item.get("has_phone") else None
    address = extract_address(markdown, item.get("location", "")) if not item.get("has_address") else None
    contact = extract_contact_person(markdown, item["business"], item["website"]) if not item.get("has_contact") else None
    
    print(f"    -> Phone: {phone} | Address: {address[:60] if address else None} | Contact: {contact}")
    return {"row": item["row"], "business": item["business"], "website": item["website"], "phone": phone, "address": address, "contact": contact}

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
    
    rows_to_enrich = []
    for idx, row in enumerate(data, start=2):
        padded = row + [""] * (15 - len(row)) if len(row) < 15 else row[:15]
        website = str(padded[6]).strip() if padded[6] else ""
        if not website:
            continue
        contact = str(padded[3]).strip() if len(padded) > 3 and padded[3] else ""
        phone = str(padded[5]).strip() if len(padded) > 5 and padded[5] else ""
        address = str(padded[7]).strip() if len(padded) > 7 and padded[7] else ""
        if contact and phone and address:
            continue
        rows_to_enrich.append({
            "row": idx,
            "business": str(padded[2]).strip() if padded[2] else "",
            "website": website,
            "location": str(padded[9]).strip() if len(padded) > 9 else "",
            "has_contact": bool(contact),
            "has_phone": bool(phone),
            "has_address": bool(address)
        })
    
    print(f"Found {len(rows_to_enrich)} rows needing contact/phone/address enrichment")
    
    progress_path = "/tmp/rankray_contact_enrichment.json"
    all_results = []
    if os.path.exists(progress_path):
        with open(progress_path) as f:
            all_results = json.load(f)
        print(f"Resumed: {len(all_results)} already done")
    
    processed = {r["row"] for r in all_results}
    pending = [r for r in rows_to_enrich if r["row"] not in processed]
    
    batch_updates = []
    with ThreadPoolExecutor(max_workers=5) as executor:
        futures = []
        for i, item in enumerate(pending):
            item["idx"] = i + 1
            item["total"] = len(pending)
            futures.append(executor.submit(process_one, item))
        for future in as_completed(futures):
            res = future.result()
            all_results.append(res)
            with open(progress_path, "w") as f:
                json.dump(all_results, f, indent=2)
            
            if res["phone"] or res["address"] or res["contact"]:
                row_updates = []
                if res["contact"]:
                    row_updates.append((3, res["contact"]))
                if res["phone"]:
                    row_updates.append((5, res["phone"]))
                if res["address"]:
                    row_updates.append((7, res["address"]))
                for col, val in row_updates:
                    batch_updates.append({
                        "updateCells": {
                            "range": {
                                "sheetId": sheet_id,
                                "startRowIndex": res["row"] - 1,
                                "endRowIndex": res["row"],
                                "startColumnIndex": col,
                                "endColumnIndex": col + 1
                            },
                            "rows": [{"values": [{"userEnteredValue": {"stringValue": str(val)}}]}],
                            "fields": "userEnteredValue"
                        }
                    })
                if len(batch_updates) >= 15:
                    body = {"requests": batch_updates}
                    service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
                    print(f"    -> Batch updated {len(batch_updates)} cells")
                    batch_updates = []
            time.sleep(0.5)
    
    if batch_updates:
        body = {"requests": batch_updates}
        service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
        print(f"    -> Final batch updated {len(batch_updates)} cells")
    
    updated = sum(1 for r in all_results if r["phone"] or r["address"] or r["contact"])
    print(f"\n✅ Done. Processed {len(all_results)} rows.")
    print(f"Rows with at least one field found: {updated}")

if __name__ == "__main__":
    main()
