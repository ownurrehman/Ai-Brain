import json, os, re
from google.oauth2.service_account import Credentials
from googleapiclient.discovery import build
from googleapiclient.errors import HttpError
from datetime import datetime, timedelta

SPREADSHEET_ID = "11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4"
SHEET_NAME = "Lead Pipeline"
CREDS_PATH = os.path.expanduser("~/.config/google-sheets/credentials.json")
SCOPES = ["https://www.googleapis.com/auth/spreadsheets"]

def get_service():
    creds = Credentials.from_service_account_file(CREDS_PATH, scopes=SCOPES)
    return build("sheets", "v4", credentials=creds, static_discovery=False)

def normalize_url(url):
    if not url:
        return ""
    url = str(url).lower().strip()
    url = re.sub(r"^(https?://)?(www\.)?", "", url)
    url = url.rstrip("/")
    url = url.split("?")[0]
    return url

def normalize_email(email):
    if not email:
        return ""
    return str(email).lower().strip()

def is_bugged(row):
    if len(row) <= 1:
        return True, "empty after Lead ID"
    rest = [c for c in row[1:] if c and str(c).strip()]
    if not rest:
        return True, "all columns empty"
    non_empty = [str(c).strip() for c in row[1:] if c and str(c).strip()]
    if len(non_empty) >= 4:
        most_common = max(set(non_empty), key=non_empty.count)
        count = non_empty.count(most_common)
        if count >= 4 and len(most_common) > 40:
            return True, f"same long text in {count} columns"
    all_text = " ".join(non_empty).lower()
    if any(x in all_text for x in ["lorem ipsum", "example.com test", "test business"]):
        return True, "placeholder/test text"
    if len(row) > 1:
        date_val = str(row[1]).strip()
        if date_val and date_val.isdigit() and len(date_val) == 5:
            return True, "date column has Excel serial number"
    business = str(row[2]).strip() if len(row) > 2 else ""
    email = str(row[4]).strip() if len(row) > 4 else ""
    if business and email and business.lower() == email.lower():
        return True, "business name equals email address"
    if not business and not email and (len(row) <= 6 or not str(row[6]).strip()):
        return True, "no business, email, or website"
    return False, "ok"

def excel_serial_to_date(serial):
    # Excel serial date base is 1899-12-30
    base = datetime(1899, 12, 30)
    return (base + timedelta(days=int(serial))).strftime("%Y-%m-%d")

def infer_fixed_row(row):
    """Infer missing fields for RR-FIXED rows"""
    lead_id = str(row[0]).strip()
    business = str(row[2]).strip() if len(row) > 2 else ""
    pain = str(row[10]).strip().lower() if len(row) > 10 else ""
    website = str(row[6]).strip().lower() if len(row) > 6 else ""
    
    # Date from lead ID
    m = re.search(r"\d{8}", lead_id)
    date_added = f"20{m.group(0)[:2]}-{m.group(0)[2:4]}-{m.group(0)[4:6]}" if m else ""
    
    # Industry inference
    industry = ""
    if any(x in business.lower() for x in ["plumbing", "plumber"]):
        industry = "Home Services - Plumbing"
    elif any(x in business.lower() for x in ["dental", "dentist", "dentistry"]):
        industry = "Dentistry"
    elif any(x in business.lower() for x in ["physio", "physical therapy", "physiotherapy"]):
        industry = "Physiotherapy"
    elif any(x in business.lower() for x in ["salon", "spa", "beauty"]):
        industry = "Beauty & Wellness"
    elif any(x in business.lower() for x in ["law", "legal", "attorney", "llp"]):
        industry = "Legal"
    elif any(x in business.lower() for x in ["auto", "car", "garage", "repairing"]):
        industry = "Automotive"
    elif any(x in business.lower() for x in ["restaurant", "cafe", "food", "kitchen"]):
        industry = "Restaurant / Food & Beverage"
    elif any(x in business.lower() for x in ["store", "fashion", "clothing", "wear", "shop"]):
        industry = "E-commerce / Fashion"
    elif any(x in pain for x in ["ai", "data", "infrastructure", "schema", "cloud", "software", "tech", "saas"]):
        industry = "Technology / SaaS"
    elif any(x in pain for x in ["networking", "it services", "cybersecurity"]):
        industry = "IT Services"
    elif any(x in pain for x in ["salon", "spa", "beauty"]):
        industry = "Beauty & Wellness"
    elif any(x in pain for x in ["restaurant", "food", "cafe"]):
        industry = "Restaurant / Food & Beverage"
    else:
        industry = "Technology / SaaS"
    
    # Location inference
    location = ""
    if ".pk" in website or any(x in business.lower() for x in ["khaadi", "gul ahmed", "souled store", "bewakoof"]):
        location = "Pakistan"
    elif ".ca" in website or ".ae" in website or ".us" in website:
        domain_tld = website.split(".")[-1] if "." in website else ""
        if domain_tld in ["ca"]:
            location = "Canada"
        elif domain_tld in ["ae"]:
            location = "Dubai, UAE"
        elif domain_tld in ["us"]:
            location = "USA"
        else:
            location = "Global / Remote"
    else:
        location = "Global / Remote"
    
    # Lead grade based on pain severity
    grade = "B"
    if any(x in pain for x in ["no h1", "zero schema", "missing h1", "no meta", "catastrophic", "no localbusiness", "js-only"]):
        grade = "A"
    elif any(x in pain for x in ["missing", "thin", "no blog", "no faq"]):
        grade = "B"
    else:
        grade = "B"
    
    return date_added, industry, location, grade

def main():
    service = get_service()
    sheet_meta = service.spreadsheets().get(spreadsheetId=SPREADSHEET_ID).execute()
    sheet = next(s for s in sheet_meta["sheets"] if s["properties"]["title"] == SHEET_NAME)
    grid = sheet["properties"]["gridProperties"]
    last_row = grid.get("rowCount", 1000)
    last_col = grid.get("columnCount", 47)
    sheet_id = sheet["properties"]["sheetId"]
    
    end_col = "A"
    if last_col > 26:
        end_col = "A" + chr(ord("A") + (last_col - 27))
    else:
        end_col = chr(ord("A") + last_col - 1)
    
    range_name = f"{SHEET_NAME}!A1:{end_col}{last_row}"
    result = service.spreadsheets().values().get(spreadsheetId=SPREADSHEET_ID, range=range_name).execute()
    values = result.get("values", [])
    header = values[0] if values else []
    data = values[1:] if len(values) > 1 else []
    
    while len(header) < 15:
        header.append("")
    
    seen_emails = {}
    seen_websites = {}
    seen_business = {}
    rows_to_delete = []
    rows_to_enrich = []
    
    for idx, row in enumerate(data, start=2):
        padded = row + [""] * (15 - len(row)) if len(row) < 15 else row[:15]
        lead_id = str(padded[0]).strip() if padded[0] else ""
        business = str(padded[2]).strip() if padded[2] else ""
        email = normalize_email(padded[4])
        website = normalize_url(padded[6])
        
        bugged, reason = is_bugged(row)
        if bugged:
            rows_to_delete.append({"row": idx, "reason": reason, "data": row})
            continue
        
        duplicate = None
        if email and email in seen_emails:
            duplicate = f"duplicate email (first row {seen_emails[email]})"
        elif website and website in seen_websites:
            duplicate = f"duplicate website (first row {seen_websites[website]})"
        elif business and business.lower() in seen_business:
            duplicate = f"duplicate business name (first row {seen_business[business.lower()]})"
        
        if duplicate:
            rows_to_delete.append({"row": idx, "reason": duplicate, "data": row})
            continue
        
        if email:
            seen_emails[email] = idx
        if website:
            seen_websites[website] = idx
        if business:
            seen_business[business.lower()] = idx
        
        # Check if RR-FIXED row needs enrichment
        if lead_id.startswith("RR-FIXED-") and (not str(padded[1]).strip() or not str(padded[8]).strip() or not str(padded[9]).strip() or not str(padded[12]).strip()):
            rows_to_enrich.append({"row": idx, "data": row})
    
    print(f"Rows to delete: {len(rows_to_delete)}")
    for r in rows_to_delete:
        print(f"  Row {r['row']}: {r['reason']}")
    
    print(f"\nRR-FIXED rows to enrich: {len(rows_to_enrich)}")
    for r in rows_to_enrich:
        print(f"  Row {r['row']}: {r['data'][2]}")
    
    # Build batch update requests
    requests = []
    
    # Delete rows (must be in descending order to avoid index shifting)
    for r in sorted(rows_to_delete, key=lambda x: x["row"], reverse=True):
        requests.append({
            "deleteDimension": {
                "range": {
                    "sheetId": sheet_id,
                    "dimension": "ROWS",
                    "startIndex": r["row"] - 1,
                    "endIndex": r["row"]
                }
            }
        })
    
    # Enrich RR-FIXED rows
    for r in rows_to_enrich:
        date_added, industry, location, grade = infer_fixed_row(r["data"])
        updates = []
        if not str(r["data"][1]).strip() and date_added:
            updates.append({"range": f"{SHEET_NAME}!B{r['row']}", "values": [[date_added]]})
        if not str(r["data"][8]).strip() and industry:
            updates.append({"range": f"{SHEET_NAME}!I{r['row']}", "values": [[industry]]})
        if not str(r["data"][9]).strip() and location:
            updates.append({"range": f"{SHEET_NAME}!J{r['row']}", "values": [[location]]})
        if not str(r["data"][12]).strip() and grade:
            updates.append({"range": f"{SHEET_NAME}!M{r['row']}", "values": [[grade]]})
        for u in updates:
            requests.append({"updateCells": {
                "range": {
                    "sheetId": sheet_id,
                    "startRowIndex": r["row"] - 1,
                    "endRowIndex": r["row"],
                    "startColumnIndex": ord(u["range"].split("!")[1][0]) - 65,
                    "endColumnIndex": ord(u["range"].split("!")[1][0]) - 64
                },
                "rows": [{"values": [{"userEnteredValue": {"stringValue": u["values"][0][0]}}]}],
                "fields": "userEnteredValue"
            }})
    
    if requests:
        body = {"requests": requests}
        service.spreadsheets().batchUpdate(spreadsheetId=SPREADSHEET_ID, body=body).execute()
        print(f"\n✅ Applied {len(requests)} batch update requests")
    else:
        print("\nNo changes needed.")
    
    # Save report
    report = {
        "deleted_rows": rows_to_delete,
        "enriched_rows": [{"row": r["row"], "business": r["data"][2]} for r in rows_to_enrich],
        "timestamp": datetime.now().isoformat()
    }
    with open("/tmp/rankray_cleanup_report_step1.json", "w") as f:
        json.dump(report, f, indent=2)
    print("Report saved to /tmp/rankray_cleanup_report_step1.json")

if __name__ == "__main__":
    main()
