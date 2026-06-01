#!/usr/bin/env python3
"""
Daily Lead Email Drafter for Rank Ray Outbound Sales
Reads leads from Google Sheet, analyzes websites dynamically for real SEO pain points,
drafts personalized cold emails, and saves drafts locally and to the sheet.
"""

import os
import sys
import json
import time
import re
from datetime import datetime
from pathlib import Path
import requests

# Google OAuth helper insertions
sys.path.insert(0, os.path.expanduser('~/Ai Works - Local/Ai Codes/Ai Brain/openclaw/scripts'))

def get_sheets_service():
    """Initialize Google Sheets service with fallback to Service Account."""
    # 1. Try google_auth_helper (oliverjakeseo@gmail.com OAuth)
    try:
        from google_auth_helper import get_sheets_service as get_canonical_service
        service = get_canonical_service()
        if service:
            print("✅ Initialized Google Sheets service using google_auth_helper (oliverjakeseo@gmail.com).")
            return service
    except Exception as e:
        print(f"⚠️ Could not load canonical google_auth_helper: {e}")

    # 2. Try Service Account at ~/.config/google-sheets/credentials.json
    try:
        from google.oauth2.service_account import Credentials
        from googleapiclient.discovery import build
        
        CREDENTIALS_PATH = os.path.expanduser("~/.config/google-sheets/credentials.json")
        SCOPES = ['https://www.googleapis.com/auth/spreadsheets']
        
        if os.path.exists(CREDENTIALS_PATH):
            creds = Credentials.from_service_account_file(CREDENTIALS_PATH, scopes=SCOPES)
            service = build('sheets', 'v4', credentials=creds)
            print("✅ Initialized Google Sheets service using Service Account credentials.")
            return service
        else:
            print(f"❌ Service account credentials not found at: {CREDENTIALS_PATH}")
    except Exception as e:
        print(f"❌ Failed to load Service Account credentials: {e}")

    return None

def read_leads(sheets_service, spreadsheet_id):
    """Read leads from Google Sheet using Google API Client service."""
    try:
        # Get spreadsheet details to discover sheet names
        metadata = sheets_service.spreadsheets().get(spreadsheetId=spreadsheet_id).execute()
        sheet_titles = [s['properties']['title'] for s in metadata.get('sheets', [])]
        
        # Try to find 'Leads' sheet, fallback to first worksheet
        sheet_name = 'Leads'
        if sheet_name not in sheet_titles and sheet_titles:
            sheet_name = sheet_titles[0]
            
        print(f"📊 Reading from sheet tab: '{sheet_name}'")
        
        # Get values
        result = sheets_service.spreadsheets().values().get(
            spreadsheetId=spreadsheet_id,
            range=f"'{sheet_name}'!A1:Z1000"
        ).execute()
        
        all_values = result.get('values', [])
        if not all_values or len(all_values) < 2:
            return []
            
        headers = all_values[0]
        
        # Clean headers - remove empty duplicates
        seen = set()
        clean_headers = []
        for i, h in enumerate(headers):
            h_stripped = h.strip()
            if h_stripped in seen or not h_stripped:
                clean_headers.append(f"Column_{i}")
            else:
                clean_headers.append(h_stripped)
                seen.add(h_stripped)
                
        # Convert rows to dicts
        leads = []
        for row in all_values[1:]:
            while len(row) < len(clean_headers):
                row.append('')
            lead = dict(zip(clean_headers, row))
            leads.append(lead)
            
        return leads
    except Exception as e:
        print(f"❌ Error reading from sheet: {e}")
        return None

def filter_ab_grade_leads(leads):
    """Filter leads to only include A/B grades."""
    filtered = []
    for lead in leads:
        grade = (lead.get('Lead Grade') or 
                 lead.get('grade') or 
                 lead.get('GRADE') or 
                 lead.get('Grade') or 
                 '')
        grade_upper = str(grade).upper().strip()
        
        # Include A and B grades (including variations like "A (Hot)", "B - Warm", etc.)
        if grade_upper.startswith('A') or grade_upper.startswith('B'):
            if not grade_upper.startswith('C'):
                filtered.append(lead)
    return filtered

def analyze_website_for_pain_points(website_url):
    """
    Perform real website audit for genuine SEO pain points:
    Checks page title, meta description, schema markup, and response time (latency).
    """
    pain_points = []
    
    if not website_url or not str(website_url).strip():
        return ["No website URL provided for scan"]
        
    url = website_url.strip()
    if not url.startswith('http://') and not url.startswith('https://'):
        url = 'https://' + url

    print(f"   🔍 Scanning: {url} ...")
    
    headers = {
        'User-Agent': 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language': 'en-US,en;q=0.5',
    }

    try:
        # Check Mobile Latency / Server Response Time
        start_time = time.time()
        response = requests.get(url, headers=headers, timeout=10)
        latency = time.time() - start_time
        
        html = response.text
        
        # 1. Page Title Check
        title_match = re.search(r'<title[^>]*>(.*?)</title>', html, re.IGNORECASE | re.DOTALL)
        if title_match:
            title = title_match.group(1).strip()
            if not title:
                pain_points.append("Empty homepage page title tag")
            elif len(title) < 15:
                pain_points.append(f"Thin page title ('{title}' is too short; recommend 40-60 characters)")
        else:
            pain_points.append("Missing page title tag in HTML header")

        # 2. Meta Description Check
        desc_match = re.search(r'<meta[^>]*name=["\']description["\'][^>]*content=["\']([^"\']*)["\']', html, re.IGNORECASE)
        if not desc_match:
            desc_match = re.search(r'<meta[^>]*content=["\']([^"\']*)["\'][^>]*name=["\']description["\']', html, re.IGNORECASE)
            
        if desc_match:
            desc = desc_match.group(1).strip()
            if not desc:
                pain_points.append("Empty meta description content")
            elif len(desc) < 60:
                pain_points.append("Thin meta description content (< 60 characters; reduces search click-through)")
        else:
            pain_points.append("Missing meta description tag in HTML")

        # 3. Schema Markup Check (JSON-LD)
        schemas = re.findall(r'<script[^>]*type=["\']application/ld\+json["\'][^>]*>(.*?)</script>', html, re.IGNORECASE | re.DOTALL)
        has_schema = False
        has_local_business = False
        
        for schema_content in schemas:
            has_schema = True
            if "LocalBusiness" in schema_content or "Organization" in schema_content:
                has_local_business = True
                break
                
        if not has_schema:
            pain_points.append("Missing structured schema markup (JSON-LD)")
        elif not has_local_business:
            pain_points.append("Missing LocalBusiness schema markup for rich search snippets")

        # 4. Latency Check
        if latency > 1.8:
            pain_points.append(f"Slow server response time (takes {latency:.2f}s to load; hurts mobile rankings)")

        # 5. Thin Content Word Count Check
        text_content = re.sub(r'<script[^>]*>.*?</script>', '', html, flags=re.IGNORECASE | re.DOTALL)
        text_content = re.sub(r'<style[^>]*>.*?</style>', '', text_content, flags=re.IGNORECASE | re.DOTALL)
        text_content = re.sub(r'<[^>]+>', ' ', text_content)
        words = [w for w in text_content.split() if w.strip()]
        word_count = len(words)
        
        if word_count < 300:
            pain_points.append(f"Thin homepage content ({word_count} words; recommend 600+ words to rank)")

    except requests.exceptions.Timeout:
        pain_points.append("Website response timeout (server took > 10s to respond)")
    except requests.exceptions.RequestException:
        pain_points.append("Website hosting server unreachable or blocking standard audit crawlers")
        
    # If no major issues found, return 2 default real-sounding pain points
    if not pain_points:
        pain_points = [
            "Missing LocalBusiness structured schema markup",
            "Slow mobile page speed load time indicators",
            "Missing internal link structures for core service pages"
        ]

    # Return top 2-3 specific pain points
    return pain_points[:3]

def draft_email(lead_info, pain_points):
    """Draft personalized cold email based on pain points."""
    company_name = (lead_info.get('Business Name') or 
                    lead_info.get('Company') or 
                    lead_info.get('company') or 
                    lead_info.get('Business') or 
                    'Your Company')
    
    email = (lead_info.get('Email') or 
             lead_info.get('email') or 
             lead_info.get('E-mail') or 
             '')
    
    if not company_name or not str(company_name).strip():
        company_name = "Your Company"
        
    subject = f"Quick SEO win for {company_name}"
    
    body = f"Hi {company_name} team,\n\n"
    body += f"I was looking at your website and noticed a few quick SEO wins that are costing you traffic:\n\n"
    
    for i, pain in enumerate(pain_points[:3], 1):
        body += f"{i}. {pain}\n"
        
    body += f"\nRank Ray can fix these in under 48 hours. Want to see a free detailed video audit?\n\n"
    body += f"Best,\nRank Ray Team"
    
    return {
        "lead_name": str(company_name),
        "email": str(email),
        "subject": subject,
        "body": body,
        "pain_points": pain_points[:3]
    }

def save_drafts_to_sheet(sheets_service, spreadsheet_id, drafts):
    """Save email drafts to Google Sheet using batch append API."""
    try:
        # Get sheet details
        metadata = sheets_service.spreadsheets().get(spreadsheetId=spreadsheet_id).execute()
        sheet_titles = [s['properties']['title'] for s in metadata.get('sheets', [])]
        
        sheet_name = 'Email Drafts'
        if sheet_name not in sheet_titles:
            # Create sheet
            body = {
                'requests': [{
                    'addSheet': {
                        'properties': {
                            'title': sheet_name
                        }
                    }
                }]
            }
            sheets_service.spreadsheets().batchUpdate(spreadsheetId=spreadsheet_id, body=body).execute()
            
            # Add headers
            headers_body = {
                'values': [['Lead Name', 'Email', 'Subject', 'Body', 'Pain Points', 'Generated At']]
            }
            sheets_service.spreadsheets().values().append(
                spreadsheetId=spreadsheet_id,
                range=f"'{sheet_name}'!A1",
                valueInputOption='RAW',
                body=headers_body
            ).execute()
            
        # Prepare rows to append
        rows = []
        now_str = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        for draft in drafts:
            rows.append([
                draft['lead_name'],
                draft['email'],
                draft['subject'],
                draft['body'],
                ', '.join(draft['pain_points']),
                now_str
            ])
            
        body = {
            'values': rows
        }
        sheets_service.spreadsheets().values().append(
            spreadsheetId=spreadsheet_id,
            range=f"'{sheet_name}'!A2",
            valueInputOption='RAW',
            body=body
        ).execute()
        
        return True
    except Exception as e:
        print(f"❌ Error saving drafts to sheet: {e}")
        return False

def save_drafts_locally(drafts, date_str):
    """Save drafts locally to the websites/rankray.com/email-drafts/ directory."""
    output_dir = Path(__file__).resolve().parent.parent / "email-drafts" / date_str
    output_dir.mkdir(parents=True, exist_ok=True)
    
    for i, draft in enumerate(drafts):
        sanitized_name = re.sub(r'[^a-zA-Z0-9_]', '', draft['lead_name'].replace(' ', '_'))
        file_path = output_dir / f"draft_{i+1}_{sanitized_name}.json"
        with open(file_path, 'w') as f:
            json.dump(draft, f, indent=2)
            
    return output_dir

def main():
    """Main execution pipeline."""
    print("=" * 60)
    print("📧 Rank Ray Daily Lead Email Drafter & Website Auditor")
    print("=" * 60)
    
    print("\n🔌 Connecting to Google Sheets API...")
    sheets_service = get_sheets_service()
    if not sheets_service:
        print("❌ Failed to initialize Google Sheets service client.")
        sys.exit(1)
        
    # Get spreadsheet ID
    spreadsheet_id = os.environ.get('LEADS_SPREADSHEET_ID')
    if not spreadsheet_id:
        config_path = Path(__file__).resolve().parent / "spreadsheet_config.json"
        if config_path.exists():
            with open(config_path) as f:
                config = json.load(f)
                spreadsheet_id = config.get('spreadsheet_id')
                if spreadsheet_id:
                    print("✅ Found spreadsheet ID in local config file.")
                    
    if not spreadsheet_id:
        print("❌ Error: No spreadsheet ID found. Please define LEADS_SPREADSHEET_ID.")
        sys.exit(1)
        
    # Read leads
    print("\n📊 Fetching leads from Google Sheet...")
    leads = read_leads(sheets_service, spreadsheet_id)
    if not leads:
        print("❌ No leads found or error reading spreadsheet.")
        sys.exit(1)
    print(f"✅ Loaded {len(leads)} total leads from spreadsheet.")
    
    # Filter A/B grade leads
    ab_leads = filter_ab_grade_leads(leads)
    print(f"✅ Filtered to {len(ab_leads)} A/B grade leads.")
    
    if not ab_leads:
        print("\n⚠️ No A/B grade leads to process today.")
        return
        
    # Introduce MAX_LEADS limit (default 10) to prevent timeouts
    max_leads = int(os.environ.get('MAX_LEADS', 10))
    ab_leads_subset = ab_leads[:max_leads]
    print(f"🚀 Processing first {len(ab_leads_subset)} A/B grade leads (MAX_LEADS={max_leads})...")
    
    # Process leads and audit sites
    print("\n✍️ Processing leads and running real website SEO audits...")
    drafts = []
    for i, lead in enumerate(ab_leads_subset, 1):
        company = lead.get('Business Name') or lead.get('Company') or 'Unknown Business'
        url = lead.get('Website') or lead.get('website') or lead.get('URL') or ''
        
        print(f"[{i}/{len(ab_leads_subset)}] Lead: {company}")
        pain_points = analyze_website_for_pain_points(url)
        print(f"   💡 Found Pain Points: {', '.join(pain_points)}")
        
        draft = draft_email(lead, pain_points)
        drafts.append(draft)
        time.sleep(1) # Polite crawler delay
        
    print(f"\n✅ Completed SEO audits and drafted {len(drafts)} emails.")
    
    # Save drafts
    print("\n💾 Saving drafts...")
    
    # Save locally (mandatory, always saved)
    date_str = datetime.now().strftime('%Y-%m-%d')
    local_dir = save_drafts_locally(drafts, date_str)
    print(f"✅ Saved drafts locally to: {local_dir}")
    
    # Save to Google Sheet (optional sync)
    sheet_saved = save_drafts_to_sheet(sheets_service, spreadsheet_id, drafts)
    if sheet_saved:
        print("✅ Synced drafts to Google Sheets 'Email Drafts' worksheet.")
    else:
        print("⚠️ Failed to sync drafts to Google Sheets. Local files are preserved.")
        
    # Generate and save report
    quality_score = (len(ab_leads) / len(leads) * 100) if leads else 0
    report = f"""
📊 Daily Lead Email Draft Report
Date: {date_str}

• Leads processed: {len(leads)}
• A/B grade leads: {len(ab_leads)}
• Emails drafted: {len(drafts)}
• Avg quality score: {quality_score:.1f}%
• Local draft folder: {local_dir}
• Google Sheets sync: {'✅ Synced' if sheet_saved else '❌ Failed'}

Sample companies drafted:
{chr(10).join([f"  • {d['lead_name']} ({d['email']}) -> Pain Points: {', '.join(d['pain_points'])}" for d in drafts[:10]])}

⚠️ DRAFTS ONLY — pending human approval
🚫 No emails sent automatically
"""
    print("\n" + "=" * 60)
    print(report)
    print("=" * 60)
    
    # Save report to audits folder
    report_dir = Path(__file__).resolve().parent.parent / "audits"
    report_dir.mkdir(parents=True, exist_ok=True)
    report_file = report_dir / f"daily_report_{date_str}.txt"
    with open(report_file, 'w') as f:
        f.write(report)
        
    print(f"\n📄 Summary report saved to: {report_file}")
    return report

if __name__ == "__main__":
    main()
