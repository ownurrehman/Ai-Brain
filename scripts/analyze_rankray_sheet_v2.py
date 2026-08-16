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
    
    # Same long text copied across multiple columns (e.g. email draft in multiple columns)
    if len(non_empty) >= 4:
        most_common = max(set(non_empty), key=non_empty.count)
        count = non_empty.count(most_common)
        if count >= 4 and len(most_common) > 40:
            return True, f"same long text in {count} columns"
    
    # Bot errors / placeholders
    all_text = " ".join(non_empty).lower()
    if any(x in all_text for x in ["lorem ipsum", "example.com test", "test business"]):
        return True, "placeholder/test text"
    
    # Phone field has Excel formula error
    if any(str(c).strip().startswith("#ERROR") for c in non_empty):
        return False, "has #ERROR! but may be fixable"
    
    # Date column has weird number instead of date (e.g. 46150)
    if len(row) > 1:
        date_val = str(row[1]).strip()
        if date_val and date_val.isdigit() and len(date_val) == 5:
            return True, "date column has Excel serial number"
    
    # Business name equals full email address
    business = str(row[2]).strip() if len(row) > 2 else ""
    email = str(row[4]).strip() if len(row) > 4 else ""
    if business and email and business.lower() == email.lower():
        return True, "business name equals email address"
    
    # Row has no business name AND no email AND no website
    if not business and not email and (len(row) <= 6 or not str(row[6]).strip()):
        return True, "no business, email, or website"
    
    return False, "ok"

def main():
    service = get_service()
    sheet_meta = service.spreadsheets().get(spreadsheetId=SPREADSHEET_ID).execute()
    sheet = next(s for s in sheet_meta["sheets"] if s["properties"]["title"] == SHEET_NAME)
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
    header = values[0] if values else []
    data = values[1:] if len(values) > 1 else []
    
    while len(header) < 15:
        header.append("")
    
    seen_emails = {}
    seen_websites = {}
    seen_business = {}
    rows_analysis = []
    
    for idx, row in enumerate(data, start=2):
        padded = row + [""] * (15 - len(row)) if len(row) < 15 else row[:15]
        lead_id = str(padded[0]).strip() if padded[0] else ""
        business = str(padded[2]).strip() if padded[2] else ""
        email = normalize_email(padded[4])
        website = normalize_url(padded[6])
        
        bugged, reason = is_bugged(row)
        duplicate_of = None
        dup_type = None
        
        if not bugged:
            if email and email in seen_emails:
                duplicate_of = seen_emails[email]
                dup_type = f"duplicate email (first at row {duplicate_of})"
            elif website and website in seen_websites:
                duplicate_of = seen_websites[website]
                dup_type = f"duplicate website (first at row {duplicate_of})"
            elif business and business.lower() in seen_business:
                duplicate_of = seen_business[business.lower()]
                dup_type = f"duplicate business name (first at row {duplicate_of})"
            else:
                if email:
                    seen_emails[email] = idx
                if website:
                    seen_websites[website] = idx
                if business:
                    seen_business[business.lower()] = idx
        
        # Missing required columns A-O (only count meaningful ones)
        required = {
            "A": "Lead ID",
            "B": "Date Added",
            "C": "Business Name",
            "E": "Email",
            "G": "Website",
            "I": "Industry",
            "J": "Location",
            "M": "Lead Grade",
            "N": "Status",
        }
        missing_cols = []
        for col_letter, col_name in required.items():
            c = ord(col_letter) - 65
            if c >= len(padded) or not str(padded[c]).strip():
                missing_cols.append(f"{col_letter}({col_name})")
        
        rows_analysis.append({
            "row": idx,
            "lead_id": lead_id,
            "business": business,
            "email": email,
            "website": website,
            "bugged": bugged,
            "bug_reason": reason,
            "duplicate": dup_type,
            "missing_cols": missing_cols,
            "raw": row
        })
    
    bugged_rows = [r for r in rows_analysis if r["bugged"]]
    dup_rows = [r for r in rows_analysis if r["duplicate"]]
    missing_rows = [r for r in rows_analysis if r["missing_cols"] and not r["bugged"] and not r["duplicate"]]
    
    print(f"Total rows: {len(data)}")
    print(f"Bugged rows to REMOVE: {len(bugged_rows)}")
    print(f"Duplicate rows to REMOVE: {len(dup_rows)}")
    print(f"Rows needing ENRICHMENT (missing required cols): {len(missing_rows)}")
    print(f"Clean rows: {len(data) - len(bugged_rows) - len(dup_rows) - len(missing_rows)}")
    
    print("\n=== BUGGED ROWS TO REMOVE ===")
    for r in bugged_rows:
        print(f"Row {r['row']}: {r['lead_id'] or '(no ID)'} | {r['business'] or '(no name)'} | Reason: {r['bug_reason']}")
        print(f"   Raw: {r['raw'][:10]}")
    
    print("\n=== DUPLICATE ROWS TO REMOVE ===")
    for r in dup_rows:
        print(f"Row {r['row']}: {r['lead_id']} | {r['business']} | {r['duplicate']}")
    
    print("\n=== ROWS NEEDING ENRICHMENT (sample first 40) ===")
    for r in missing_rows[:40]:
        print(f"Row {r['row']}: {r['lead_id']} | {r['business']} | Missing: {', '.join(r['missing_cols'])}")
    if len(missing_rows) > 40:
        print(f"... and {len(missing_rows) - 40} more")
    
    out = "/tmp/rankray_leads_analysis_v2.json"
    with open(out, "w") as f:
        json.dump(rows_analysis, f, indent=2)
    print(f"\nFull analysis saved to: {out}")

if __name__ == "__main__":
    main()
