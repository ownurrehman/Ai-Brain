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
    url = url.lower().strip()
    url = re.sub(r"^(https?://)?(www\.)?", "", url)
    url = url.rstrip("/")
    url = re.sub(r"/$", "", url)
    url = url.split("?")[0]
    return url

def normalize_email(email):
    if not email:
        return ""
    return email.lower().strip()

def is_bugged(row):
    # Check if entire row is empty/whitespace after col A
    if len(row) <= 1:
        return True, "empty after Lead ID"
    # Find first non-empty cell after col A
    rest = [c for c in row[1:] if c and str(c).strip()]
    if not rest:
        return True, "all columns empty"
    
    # Check for repeated identical text across many columns (bot copy-paste bug)
    non_empty = [str(c).strip() for c in row[1:] if c and str(c).strip()]
    if len(non_empty) >= 3:
        # If more than 50% of non-empty cells are identical, it's bugged
        unique = set(non_empty)
        most_common = max(set(non_empty), key=non_empty.count)
        if non_empty.count(most_common) / len(non_empty) > 0.5 and len(most_common) > 15:
            return True, f"repeated text in {non_empty.count(most_common)}/{len(non_empty)} cells"
    
    # Check for obvious placeholder/fake data
    all_text = " ".join(non_empty).lower()
    if "lorem ipsum" in all_text or "example" in all_text or "test" in all_text:
        return True, "placeholder/test text"
    
    # Check if business name is same as email domain or generic
    business = str(row[2]).strip() if len(row) > 2 else ""
    email = str(row[4]).strip() if len(row) > 4 else ""
    if business and email:
        domain = email.split("@")[-1].lower()
        if business.lower() == domain:
            return True, "business name equals email domain"
    
    # Check if row has valid structure: at least Lead ID + Business Name + one contact
    if len(row) < 3 or not row[2]:
        return False, "missing business name (needs enrichment)"
    
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
    
    # Pad header to at least 15 columns
    while len(header) < 15:
        header.append("")
    
    print(f"Header ({len(header)} cols):")
    for i, h in enumerate(header[:15]):
        print(f"  {chr(65+i)}: {h}")
    
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
        
        # Check missing columns A-O
        missing_cols = []
        for c in range(15):
            if not str(padded[c]).strip():
                missing_cols.append(chr(65+c))
        
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
    
    # Print summary
    bugged_rows = [r for r in rows_analysis if r["bugged"]]
    dup_rows = [r for r in rows_analysis if r["duplicate"]]
    missing_rows = [r for r in rows_analysis if r["missing_cols"] and not r["bugged"] and not r["duplicate"]]
    
    print(f"\nTotal rows: {len(data)}")
    print(f"Bugged rows: {len(bugged_rows)}")
    print(f"Duplicate rows: {len(dup_rows)}")
    print(f"Rows needing enrichment: {len(missing_rows)}")
    print(f"Clean rows: {len(data) - len(bugged_rows) - len(dup_rows) - len(missing_rows)}")
    
    print("\n=== BUGGED ROWS ===")
    for r in bugged_rows[:20]:
        print(f"Row {r['row']}: {r['lead_id'] or '(no ID)'} | {r['business'] or '(no name)'} | Reason: {r['bug_reason']}")
        print(f"   Raw: {r['raw'][:8]}")
    if len(bugged_rows) > 20:
        print(f"... and {len(bugged_rows) - 20} more")
    
    print("\n=== DUPLICATE ROWS ===")
    for r in dup_rows[:20]:
        print(f"Row {r['row']}: {r['lead_id']} | {r['business']} | {r['duplicate']}")
    if len(dup_rows) > 20:
        print(f"... and {len(dup_rows) - 20} more")
    
    print("\n=== ROWS NEEDING ENRICHMENT (missing cols A-O) ===")
    for r in missing_rows[:30]:
        print(f"Row {r['row']}: {r['lead_id']} | {r['business']} | Missing: {', '.join(r['missing_cols'])}")
    if len(missing_rows) > 30:
        print(f"... and {len(missing_rows) - 30} more")
    
    # Save analysis
    out = "/tmp/rankray_leads_analysis.json"
    with open(out, "w") as f:
        json.dump(rows_analysis, f, indent=2)
    print(f"\nFull analysis saved to: {out}")

if __name__ == "__main__":
    main()
