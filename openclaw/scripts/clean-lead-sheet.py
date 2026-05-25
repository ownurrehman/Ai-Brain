#!/usr/bin/env python3
"""
Clean the Rank Ray Lead Tracker Google Sheet by deleting junk rows in BATCHES.
Uses batch_update for much faster deletion.
"""
import os
import sys

try:
    import gspread
    from google.oauth2.service_account import Credentials
except ImportError:
    os.system("uv pip install gspread google-auth 2>/dev/null")
    import gspread
    from google.oauth2.service_account import Credentials

SCOPES = [
    "https://www.googleapis.com/auth/spreadsheets",
    "https://www.googleapis.com/auth/drive.readonly"
]

def identify_junk_rows(rows):
    """Find rows to delete: empty, no contact info, duplicates."""
    indices_to_delete = []
    seen_emails = set()
    seen_names = set()

    for i, row in enumerate(rows):
        while len(row) < 12:
            row.append("")

        lead_id = str(row[0]).strip()
        business = str(row[2]).strip()
        contact_name = str(row[3]).strip()
        email = str(row[4]).strip().lower()
        phone = str(row[5]).strip()
        website = str(row[6]).strip()

        # Empty row
        if not lead_id and not business and not email and not phone and not website:
            indices_to_delete.append(i)
            continue

        # Junk: business but no contact
        if business and not email and not phone and not website and not contact_name:
            indices_to_delete.append(i)
            continue

        # Duplicate email
        if email and email != "email":
            if email in seen_emails:
                indices_to_delete.append(i)
                continue
            seen_emails.add(email)

        # Duplicate business name
        name_key = business.lower().strip()
        if name_key and name_key != "business name" and name_key != "n/a":
            if name_key in seen_names:
                indices_to_delete.append(i)
                continue
            seen_names.add(name_key)

    return indices_to_delete

def batch_delete_rows(worksheet, indices_to_delete):
    """Delete rows in batches using batchUpdate for efficiency."""
    # Convert to 1-based and sort descending
    rows = sorted([i + 2 for i in indices_to_delete], reverse=True)

    # Build batch requests - delete contiguous ranges
    requests = []
    i = 0
    while i < len(rows):
        start = rows[i]
        end = start
        # Find contiguous block
        while i + 1 < len(rows) and rows[i + 1] == end - 1:
            end = rows[i + 1]
            i += 1
        # Add deleteDimension request for range [end, start]
        requests.append({
            "deleteDimension": {
                "range": {
                    "sheetId": worksheet.id,
                    "dimension": "ROWS",
                    "startIndex": end - 1,  # 0-based
                    "endIndex": start       # exclusive
                }
            }
        })
        i += 1

    print(f"Built {len(requests)} batch delete requests for {len(rows)} rows")

    # Execute in batches of 50 requests (Google Sheets limit)
    batch_size = 50
    deleted = 0
    for j in range(0, len(requests), batch_size):
        batch = requests[j:j + batch_size]
        try:
            worksheet.spreadsheet.batch_update({"requests": batch})
            deleted += len(batch)
            print(f"  Executed batch {deleted}/{len(requests)}")
        except Exception as e:
            print(f"  ERROR in batch {j//batch_size + 1}: {e}")

    return len(rows)

def main():
    import argparse
    parser = argparse.ArgumentParser()
    parser.add_argument('--yes', action='store_true')
    parser.add_argument('--dry-run', action='store_true')
    args = parser.parse_args()

    print(f"Mode: {'DRY RUN' if args.dry_run else 'LIVE'}")

    cred_path = os.path.expanduser("~/Downloads/openclaw-rank-ray-automation-6c8b1dbaa824.json")
    if not os.path.exists(cred_path):
        print(f"ERROR: Credentials not found at {cred_path}")
        sys.exit(1)

    creds = Credentials.from_service_account_file(cred_path, scopes=SCOPES)
    client = gspread.authorize(creds)

    try:
        spreadsheet = client.open("Rank Ray Lead Tracker")
    except gspread.SpreadsheetNotFound:
        print("ERROR: Sheet not found")
        sys.exit(1)

    worksheet = spreadsheet.sheet1
    print(f"Sheet: {spreadsheet.title} → {worksheet.title}")

    print("Reading data...")
    all_values = worksheet.get_all_values()
    header = all_values[0]
    data = all_values[1:]
    print(f"Rows: {len(data)} data + 1 header")

    print("Analyzing...")
    indices_to_delete = identify_junk_rows(data)
    print(f"Rows to delete: {len(indices_to_delete)}")

    if not indices_to_delete:
        print("Sheet is clean!")
        return

    # Show examples
    print("\nFirst 10 to delete:")
    for idx in indices_to_delete[:10]:
        row = data[idx]
        print(f"  Row {idx+2}: {row[0]} | {row[2]} | {row[4]}")

    print(f"\n{'='*60}")
    print(f"DELETE {len(indices_to_delete)} rows?")
    if args.dry_run:
        print("DRY RUN — no changes.")
        print(f"{'='*60}")
        return
    print(f"{'='*60}")

    if not args.yes:
        resp = input("Type 'yes' to confirm: ").strip().lower()
        if resp != 'yes':
            print("Cancelled.")
            return

    print("\nDeleting in batches...")
    deleted = batch_delete_rows(worksheet, indices_to_delete)

    print(f"\nDone! Deleted {deleted} rows.")
    remaining = worksheet.get_all_values()
    print(f"Remaining: {len(remaining)-1} data rows")

if __name__ == "__main__":
    main()
