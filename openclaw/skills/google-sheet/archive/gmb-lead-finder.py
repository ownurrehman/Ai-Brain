#!/usr/bin/env python3
"""
Daily GMB Lead Finder - Finds businesses without websites
Target: USA, Canada, Australia, UAE - Top 10 cities each
Appends to: Rank Ray Lead Tracker (Lead Pipeline sheet)
"""

import json
import os
import sys
import random
from datetime import datetime
from urllib.parse import quote_plus

# Top 10 cities by country
CITIES = {
    'USA': [
        'New York NY', 'Los Angeles CA', 'Chicago IL', 'Houston TX',
        'Phoenix AZ', 'Philadelphia PA', 'San Antonio TX', 'San Diego CA',
        'Dallas TX', 'San Jose CA'
    ],
    'Canada': [
        'Toronto ON', 'Montreal QC', 'Vancouver BC', 'Calgary AB',
        'Edmonton AB', 'Ottawa ON', 'Winnipeg MB', 'Quebec City QC',
        'Hamilton ON', 'Kitchener ON'
    ],
    'Australia': [
        'Sydney NSW', 'Melbourne VIC', 'Brisbane QLD', 'Perth WA',
        'Adelaide SA', 'Gold Coast QLD', 'Newcastle NSW', 'Canberra ACT',
        'Sunshine Coast QLD', 'Wollongong NSW'
    ],
    'UAE': [
        'Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Al Ain',
        'Ras Al Khaimah', 'Fujairah', 'Umm Al Quwain', 'Dibba', 'Khor Fakkan'
    ]
}

# Industries to search
INDUSTRIES = [
    'plumber', 'dentist', 'lawyer', 'physiotherapist', 'roofing contractor',
    'auto repair', 'salon', 'spa', 'electrician', 'hvac',
    'landscaping', 'pest control', 'moving company', 'cleaning service',
    'carpenter', 'painter', 'handyman', 'accountant', 'insurance agent'
]

def get_sheet_data():
    """Read existing leads from Google Sheet to avoid duplicates"""
    import subprocess
    result = subprocess.run(
        ['node', 'scripts/sheets.js', 'read', SHEET_ID, 'Lead Pipeline!E2:E1000'],
        capture_output=True, text=True, cwd=SKILL_DIR
    )
    try:
        data = json.loads(result.stdout)
        emails = set()
        for row in data:
            if row and len(row) > 0 and row[0]:
                emails.add(row[0].strip().lower())
        return emails
    except:
        return set()

def search_gmb_leads(city, industry):
    """Search for GMB leads without websites using web search"""
    query = f'"{industry}" "{city}" "no website" OR "no web site" business'
    # This would integrate with web_search or Firecrawl
    # For now, return placeholder structure
    return []

def append_to_sheet(leads):
    """Append new leads to Google Sheet"""
    if not leads:
        print("No new leads to append")
        return
    
    import subprocess
    
    # Prepare data rows
    rows = []
    today = datetime.now().strftime('%Y-%m-%d')
    
    for lead in leads:
        row = [
            lead.get('lead_id', ''),
            today,
            lead.get('business_name', ''),
            lead.get('contact_name', ''),
            lead.get('email', ''),
            lead.get('phone', ''),
            lead.get('website', ''),
            lead.get('address', ''),
            lead.get('industry', ''),
            lead.get('location', ''),
            lead.get('pain_points', ''),
            '',  # Our Solution
            lead.get('grade', 'B'),
            'New Lead',
            '',  # Email Draft
            'No',
            'Pending',
            '',  # Follow-up Date
            '',  # Last Touchpoint
            lead.get('notes', ''),
        ]
        rows.append(row)
    
    # Find next empty row
    result = subprocess.run(
        ['node', 'scripts/sheets.js', 'read', SHEET_ID, 'Lead Pipeline!A:A'],
        capture_output=True, text=True, cwd=SKILL_DIR
    )
    
    try:
        existing = json.loads(result.stdout)
        next_row = len(existing) + 1
    except:
        next_row = 2
    
    # Write in batches
    BATCH_SIZE = 50
    for i in range(0, len(rows), BATCH_SIZE):
        batch = rows[i:i+BATCH_SIZE]
        end_row = next_row + len(batch) - 1
        range_str = f"Lead Pipeline!A{next_row}:Z{end_row}"
        
        result = subprocess.run(
            ['node', 'scripts/sheets.js', 'write', SHEET_ID, range_str, json.dumps(batch)],
            capture_output=True, text=True, cwd=SKILL_DIR
        )
        
        if result.returncode == 0:
            print(f"Appended {len(batch)} leads to rows {next_row}-{end_row}")
        else:
            print(f"Error appending: {result.stderr}")
        
        next_row = end_row + 1

def main():
    print(f"Starting GMB Lead Finder - {datetime.now()}")
    
    # Pick random rotation of cities and industries
    # We process a subset each day to avoid overwhelming
    all_combos = []
    for country, cities in CITIES.items():
        for city in cities:
            for industry in INDUSTRIES:
                all_combos.append((country, city, industry))
    
    # Shuffle and pick ~50 combinations per day
    random.shuffle(all_combos)
    daily_batch = all_combos[:50]
    
    print(f"Processing {len(daily_batch)} city/industry combinations today")
    
    new_leads = []
    existing_emails = get_sheet_data()
    
    for country, city, industry in daily_batch:
        print(f"Searching: {industry} in {city}, {country}")
        leads = search_gmb_leads(city, industry)
        
        for lead in leads:
            email = lead.get('email', '').strip().lower()
            if email and email not in existing_emails:
                new_leads.append(lead)
                existing_emails.add(email)
    
    print(f"Found {len(new_leads)} new leads")
    append_to_sheet(new_leads)
    
    print(f"Done - {datetime.now()}")

if __name__ == '__main__':
    SHEET_ID = '11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4'
    SKILL_DIR = os.path.expanduser('~/Ai Works - Local/Ai Codes/Ai Brain/openclaw/skills/google-sheet')
    main()
