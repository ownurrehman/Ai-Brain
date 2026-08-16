import asyncio, json, os, re, subprocess
from urllib.parse import urljoin, urlparse
from google.oauth2.service_account import Credentials
from googleapiclient.discovery import build
import requests
import time

SPREADSHEET_ID = "11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4"
SHEET_NAME = "Lead Pipeline"
CREDS_PATH = os.path.expanduser("~/.config/google-sheets/credentials.json")
SCOPES = ["https://www.googleapis.com/auth/spreadsheets"]
FIRECRAWL_API_KEY = os.environ.get("FIRECRAWL_API_KEY")

UA = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
PHONE_RE = re.compile(r"[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}")

def get_service():
    creds = Credentials.from_service_account_file(CREDS_PATH, scopes=SCOPES)
    return build("sheets", "v4", credentials=creds, static_discovery=False)

def log(msg):
    print(msg, flush=True)

async def curl_fetch(url):
    try:
        proc = await asyncio.create_subprocess_exec(
            "curl", "-sL", "--max-time", "7", "--connect-timeout", "4", "-A", UA, url,
            stdout=subprocess.PIPE, stderr=subprocess.PIPE
        )
        try:
            stdout, _ = await asyncio.wait_for(proc.communicate(), timeout=10)
        except asyncio.TimeoutError:
            proc.kill()
            await proc.wait()
            return ""
        if proc.returncode == 0 and len(stdout) > 100:
            return stdout.decode("utf-8", errors="ignore")
    except Exception:
        pass
    return ""

async def fetch_all_pages(start_url):
    if not start_url.startswith("http"):
        start_url = "https://" + start_url
    homepage = await curl_fetch(start_url)
    if not homepage:
        return ""
    all_html = homepage
    try:
        parsed = urlparse(start_url)
        base = f"{parsed.scheme}://{parsed.netloc}"
        for path in ["/contact", "/contact-us", "/about", "/about-us", "/team", "/our-team"]:
            page = await curl_fetch(urljoin(base, path))
            if page:
                all_html += " " + page
    except Exception:
        pass
    return all_html

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
    except Exception:
        pass
    return None

def extract_phone(text):
    phones = PHONE_RE.findall(text)
    cleaned = []
    for p in phones:
        digits = re.sub(r"\D", "", p)
        if len(digits) >= 10:
            core = digits[-10:]
            if core not in cleaned:
                cleaned.append(core)
    if cleaned:
        best = cleaned[0]
        return f"({best[:3]}) {best[3:6]}-{best[6:]}"
    return None

def extract_address(text, location):
    lines = text.split("\n")
    for line in lines:
        line = line.strip()
        if len(line) < 15 or len(line) > 200:
            continue
        has_street = re.search(r"\d+\s+[A-Za-z]", line)
        has_state_zip = re.search(r"[A-Za-z\s]+,\s*[A-Z]{2}[A-Za-z\s]*\d{0,5}", line)
        has_country = any(x in line.lower() for x in ["canada", "usa", "uae", "united states", "ontario", "british columbia", "alberta"])
        if has_street and (has_state_zip or has_country):
            if not any(bad in line.lower() for bad in ["@", "www.", "http", "tel:", "fax:", "monday", "tuesday", "wednesday", "thursday", "friday", "saturday", "sunday"]):
                return line
    return None

def extract_contact_person(text, business):
    titles = ["owner", "founder", "ceo", "president", "principal", "director", "managing partner", "partner", "manager"]
    lines = [l.strip() for l in text.split("\n") if l.strip()]
    for i, line in enumerate(lines):
        if len(line) < 3 or len(line) > 80:
            continue
        combined = (line + " " + (lines[i+1] if i+1 < len(lines) else "")).lower()
        if any(t in combined for t in titles):
            words = line.split()
            if 2 <= len(words) <= 4:
                if all(w[0].isupper() for w in words if w and w[0].isalpha()):
                    name = " ".join(words)
                    if business.split()[0].lower() not in name.lower() and not any(x in name.lower() for x in ["about", "team", "contact", "home", "services", "privacy", "terms"]):
                        return name
    return None

async def process_one(item):
    url = item["website"] if item["website"].startswith("http") else "https://" + item["website"]
    parsed = urlparse(url)
    domain = parsed.netloc.replace("www.", "")
    
    html = await fetch_all_pages(url)
    if not html:
        # Fallback to Firecrawl if curl fails
        html = firecrawl_scrape(url) or ""
    
    phone = extract_phone(html) if not item.get("has_phone") else None
    address = extract_address(html, item.get("location", "")) if not item.get("has_address") else None
    contact = extract_contact_person(html, item["business"]) if not item.get("has_contact") else None
    
    status = []
    if phone:
        status.append("phone")
    if address:
        status.append("address")
    if contact:
        status.append("contact")
    
    log(f"[{item['idx']}/{item['total']}] Row {item['row']}: {item['business'][:45]} | {', '.join(status) if status else 'nothing'}")
    
    return {
        "row": item["row"],
        "business": item["business"],
        "website": item["website"],
        "phone": phone,
        "address": address,
        "contact": contact
    }

async def main():
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
    log(f"Read {len(data)} rows")
    
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
    
    log(f"Found {len(rows_to_enrich)} rows needing contact/phone/address enrichment")
    
    progress_path = "/tmp/rankray_contact_enrichment_v2.json"
    all_results = []
    if os.path.exists(progress_path):
        with open(progress_path) as f:
            all_results = json.load(f)
        log(f"Resumed: {len(all_results)} already done")
    
    processed = {r["row"] for r in all_results}
    pending = [r for r in rows_to_enrich if r["row"] not in processed]
    
    semaphore = asyncio.Semaphore(12)
    async def bounded_process(item):
        async with semaphore:
            return await process_one(item)
    
    batch_updates = []
    for i, item in enumerate(pending):
        item["idx"] = i + 1
        item["total"] = len(pending)
    
    completed = 0
    for future in asyncio.as_completed([bounded_process(item) for item in pending]):
        res = await future
        all_results.append(res)
        completed += 1
        
        with open(progress_path, "w") as f:
            json.dump(all_results, f, indent=2)
        
        if res["phone"] or res["address"] or res["contact"]:
            updates = []
            if res["contact"]:
                updates.append((3, res["contact"]))
            if res["phone"]:
                updates.append((5, res["phone"]))
            if res["address"]:
                updates.append((7, res["address"]))
            for col, val in updates:
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
            if len(batch_updates) >= 30:
                body = {"requests": batch_updates}
                service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
                log(f"  -> Batch updated {len(batch_updates)} cells ({completed}/{len(pending)})")
                batch_updates = []
    
    if batch_updates:
        body = {"requests": batch_updates}
        service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
        log(f"  -> Final batch updated {len(batch_updates)} cells")
    
    updated = sum(1 for r in all_results if r["phone"] or r["address"] or r["contact"])
    log(f"\n✅ Done. Processed {len(all_results)} rows.")
    log(f"Rows with at least one field found: {updated}")

if __name__ == "__main__":
    asyncio.run(main())
