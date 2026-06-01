#!/usr/bin/env python3
"""
Weekly SEO Report Emailer for Rank Ray
Sends compiled weekly SEO reports from oliverjakeseo@gmail.com to rankrayofficial@gmail.com
Uses existing Google OAuth from master-env.env (NOT SMTP — uses Gmail API directly).
"""

import os
import sys
import json
import base64
from datetime import datetime, timedelta
from pathlib import Path
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

# Google OAuth imports
try:
    from google.oauth2.credentials import Credentials
    from googleapiclient.discovery import build
    from google.auth.transport.requests import Request
except ImportError:
    print("❌ google-auth and google-api-python-client required.")
    sys.exit(1)

# Configuration
# CANONICAL PATHS — oliverjakeseo@gmail.com OAuth credentials
# Do NOT change these. All agents must use these exact paths.
TOKEN_PATH = Path.home() / "Ai Works - Local" / "Ai Codes" / "Ai Brain" / "credentials" / "google-oauth" / "oliverjakeseo@gmail.com-oauth-token.json"
CLIENT_SECRET_PATH = Path.home() / "Ai Works - Local" / "Ai Codes" / "Ai Brain" / "credentials" / "google-oauth" / "oliverjakeseo@gmail.com-oauth-credentials.json"
SENDER_EMAIL = "oliverjakeseo@gmail.com"
RECIPIENT_EMAIL = "rankrayofficial@gmail.com"

# Memory and report paths
MEMORY_DIR = Path.home() / "Ai Works - Local" / "Ai Codes" / "Ai Brain" / "memory"
MASTERSHEET_PATH = Path.home() / "Ai Works - Local" / "Ai Codes" / "Ai Brain" / "mastersheet.md"
REPORTS_DIR = Path(__file__).resolve().parent.parent / "audits"


def get_gmail_service():
    """Initialize Gmail API service using OAuth token."""
    if not TOKEN_PATH.exists():
        print(f"❌ Token file not found: {TOKEN_PATH}")
        return None
    
    try:
        # Read token data
        with open(TOKEN_PATH, 'r') as f:
            token_data = json.load(f)
        
        # Read client secret for client_id and client_secret
        client_id = None
        client_secret = None
        if CLIENT_SECRET_PATH.exists():
            with open(CLIENT_SECRET_PATH, 'r') as f:
                client_data = json.load(f)
                # Handle both "web" and "installed" formats
                creds_obj = client_data.get('web', client_data.get('installed', {}))
                client_id = creds_obj.get('client_id')
                client_secret = creds_obj.get('client_secret')
        
        # Ensure token has required fields
        if 'client_id' not in token_data or not token_data.get('client_id'):
            token_data['client_id'] = client_id
        if 'client_secret' not in token_data or not token_data.get('client_secret'):
            token_data['client_secret'] = client_secret
        if 'token_uri' not in token_data:
            token_data['token_uri'] = creds_obj.get('token_uri', 'https://oauth2.googleapis.com/token')
        if 'token' not in token_data and 'access_token' in token_data:
            token_data['token'] = token_data['access_token']
        
        # Build credentials directly instead of from_authorized_user_file
        creds = Credentials(
            token=token_data.get('token') or token_data.get('access_token'),
            refresh_token=token_data.get('refresh_token'),
            token_uri=token_data.get('token_uri', 'https://oauth2.googleapis.com/token'),
            client_id=token_data['client_id'],
            client_secret=token_data['client_secret'],
            scopes=['https://www.googleapis.com/auth/gmail.send']
        )
        
        # Refresh if expired
        if creds.expired:
            if creds.refresh_token:
                print("🔄 Token expired, refreshing...")
                creds.refresh(Request())
                
                # Save refreshed token
                save_data = {
                    'token': creds.token,
                    'refresh_token': creds.refresh_token,
                    'token_uri': creds.token_uri,
                    'client_id': creds.client_id,
                    'client_secret': creds.client_secret,
                    'scopes': list(creds.scopes) if creds.scopes else ['https://www.googleapis.com/auth/gmail.send'],
                }
                with open(TOKEN_PATH, 'w') as f:
                    json.dump(save_data, f, indent=2)
                print("✅ Token refreshed and saved")
            else:
                print("❌ Token expired and no refresh_token available")
                return None
        
        service = build('gmail', 'v1', credentials=creds)
        return service
    except Exception as e:
        print(f"❌ Error initializing Gmail service: {e}")
        import traceback
        traceback.print_exc()
        return None


def get_last_7_days_memory():
    """Read memory files from last 7 days."""
    reports = []
    today = datetime.now()
    
    for i in range(7):
        date = today - timedelta(days=i)
        date_str = date.strftime('%Y-%m-%d')
        memory_file = MEMORY_DIR / f"{date_str}.md"
        
        if memory_file.exists():
            with open(memory_file, 'r') as f:
                content = f.read()
            reports.append({
                'date': date_str,
                'content': content
            })
    
    return reports


def compile_weekly_report():
    """Compile weekly SEO report from memory files and mastersheet."""
    reports = get_last_7_days_memory()
    
    if not reports:
        return None, "No memory files found for last 7 days"
    
    # Build report
    report_lines = []
    report_lines.append("WEEKLY SEO REPORT - RANK RAY")
    report_lines.append(f"Week of: {(datetime.now() - timedelta(days=6)).strftime('%Y-%m-%d')} to {datetime.now().strftime('%Y-%m-%d')}")
    report_lines.append("=" * 50)
    report_lines.append("")
    
    # Position tracking summary
    report_lines.append("POSITION TRACKING SUMMARY")
    report_lines.append("-" * 30)
    
    rankray_data = {"ranking": [], "not_ranking": []}
    tonicphysio_data = {"ranking": [], "not_ranking": []}
    coinsfera_data = {"ranking": [], "not_ranking": []}
    teammotorcycle_data = {"ranking": [], "not_ranking": []}
    
    for report in reports:
        content = report['content']
        lines = content.split('\n')
        
        current_site = None
        for line in lines:
            line = line.strip()
            if 'RANKRAY.COM' in line.upper() or '## RANKRAY' in line.upper():
                current_site = 'rankray'
            elif 'TONICPHYSIO.COM' in line.upper() or '## TONICPHYSIO' in line.upper():
                current_site = 'tonicphysio'
            elif 'COINSFERA.COM' in line.upper() or '## COINSFERA' in line.upper():
                current_site = 'coinsfera'
            elif 'TEAMMOTORCYCLE.COM' in line.upper() or '## TEAMMOTORCYCLE' in line.upper():
                current_site = 'teammotorcycle'
            elif '|' in line and current_site and ('Position' in line or 'position' in line):
                if 'Position 1' in line or 'Position 2' in line or 'Position 3' in line:
                    parts = line.split('|')
                    if len(parts) >= 3:
                        keyword = parts[1].strip()
                        rank = parts[2].strip()
                        data_dict = locals()[f"{current_site}_data"]
                        data_dict["ranking"].append(f"{keyword} ({rank})")
                elif 'Not in top' in line:
                    parts = line.split('|')
                    if len(parts) >= 2:
                        keyword = parts[1].strip()
                        data_dict = locals()[f"{current_site}_data"]
                        data_dict["not_ranking"].append(keyword)
    
    # Output position summaries
    for site_name, data in [
        ("rankray.com", rankray_data),
        ("tonicphysio.com", tonicphysio_data),
        ("coinsfera.com", coinsfera_data),
        ("teammotorcycle.com", teammotorcycle_data)
    ]:
        report_lines.append(f"\n{site_name.upper()}:")
        if data["ranking"]:
            report_lines.append(f"  Ranking: {', '.join(data['ranking'][:3])}")
        if data["not_ranking"]:
            report_lines.append(f"  Not ranking: {', '.join(data['not_ranking'][:3])}")
        if not data["ranking"] and not data["not_ranking"]:
            report_lines.append("  No position data this week")
    
    report_lines.append("")
    report_lines.append("TECHNICAL AUDIT SUMMARY")
    report_lines.append("-" * 30)
    
    # Read mastersheet for audit info
    if MASTERSHEET_PATH.exists():
        with open(MASTERSHEET_PATH, 'r') as f:
            mastersheet = f.read()
        
        if "Critical Issues" in mastersheet:
            lines = mastersheet.split('\n')
            for i, line in enumerate(lines):
                if "| Date | Site | Status |" in line:
                    if i + 2 < len(lines):
                        audit_line = lines[i + 2]
                        parts = [p.strip() for p in audit_line.split('|')]
                        if len(parts) >= 6:
                            report_lines.append(f"  Latest audit: {parts[1]} on {parts[2]} — Status: {parts[3]}")
                            report_lines.append(f"  Critical: {parts[4]}, Medium: {parts[5]}, Low: {parts[6] if len(parts) > 6 else 'N/A'}")
                    break
    
    report_lines.append("")
    report_lines.append("THIS WEEK'S ACTIVITY")
    report_lines.append("-" * 30)
    
    for report in reports:
        date = report['date']
        content = report['content']
        
        if "Position Tracker" in content or "position tracker" in content.lower():
            report_lines.append(f"  • {date}: Position tracking run")
        
        if "tech-audit" in content.lower() or "technical audit" in content.lower():
            report_lines.append(f"  • {date}: Technical audit completed")
        
        if "GSC" in content or "Search Console" in content:
            report_lines.append(f"  • {date}: GSC opportunity scan")
    
    report_lines.append("")
    report_lines.append("NEXT WEEK PRIORITIES")
    report_lines.append("-" * 30)
    report_lines.append("  1. Continue daily position tracking")
    report_lines.append("  2. Fix critical issues from latest audit")
    report_lines.append("  3. Content gap analysis and brief creation")
    
    report_lines.append("")
    report_lines.append("=" * 50)
    report_lines.append("Report generated by OpenClaw Agent")
    report_lines.append(f"Sent from: {SENDER_EMAIL}")
    
    return '\n'.join(report_lines), None


def send_email(service, subject, body, to_email, from_email):
    """Send email using Gmail API."""
    try:
        message = MIMEMultipart('alternative')
        message['to'] = to_email
        message['from'] = from_email
        message['subject'] = subject
        
        msg_text = MIMEText(body, 'plain')
        message.attach(msg_text)
        
        raw_message = base64.urlsafe_b64encode(message.as_bytes()).decode('utf-8')
        
        service.users().messages().send(
            userId='me',
            body={'raw': raw_message}
        ).execute()
        
        return True, None
    except Exception as e:
        return False, str(e)


def main():
    """Main execution function."""
    print("=" * 60)
    print("WEEKLY SEO REPORT EMAILER (Gmail API)")
    print("=" * 60)
    
    if not TOKEN_PATH.exists():
        print(f"❌ Google OAuth token not found at: {TOKEN_PATH}")
        sys.exit(1)
    
    print(f"✅ OAuth token found: {TOKEN_PATH}")
    print(f"✅ Sender: {SENDER_EMAIL}")
    print(f"✅ Recipient: {RECIPIENT_EMAIL}")
    
    # Initialize Gmail service
    print("\n🔌 Connecting to Gmail API...")
    service = get_gmail_service()
    if not service:
        print("❌ Failed to initialize Gmail service")
        sys.exit(1)
    print("✅ Gmail API connected")
    
    # Compile report
    print("\n📊 Compiling weekly SEO report...")
    report, error = compile_weekly_report()
    if not report:
        print(f"❌ Failed to compile report: {error}")
        sys.exit(1)
    print("✅ Report compiled")
    
    # Generate subject with week range
    today = datetime.now()
    week_start = today - timedelta(days=6)
    subject = f"Weekly SEO Report — {week_start.strftime('%b %d')} to {today.strftime('%b %d, %Y')}"
    
    # Send email
    print(f"\n📤 Sending email to {RECIPIENT_EMAIL}...")
    success, error = send_email(service, subject, report, RECIPIENT_EMAIL, SENDER_EMAIL)
    
    if success:
        print("✅ Email sent successfully!")
        
        # Save copy locally
        report_file = REPORTS_DIR / f"weekly_report_{today.strftime('%Y-%m-%d')}.txt"
        REPORTS_DIR.mkdir(parents=True, exist_ok=True)
        with open(report_file, 'w') as f:
            f.write(report)
        print(f"💾 Report saved to: {report_file}")
        
        return True
    else:
        print(f"❌ Failed to send email: {error}")
        
        # Save draft locally anyway
        draft_file = REPORTS_DIR / f"weekly_report_draft_{today.strftime('%Y-%m-%d')}.txt"
        REPORTS_DIR.mkdir(parents=True, exist_ok=True)
        with open(draft_file, 'w') as f:
            f.write(f"FAILED TO SEND\nError: {error}\n\n{report}")
        print(f"💾 Draft saved to: {draft_file}")
        
        return False


if __name__ == "__main__":
    success = main()
    sys.exit(0 if success else 1)
