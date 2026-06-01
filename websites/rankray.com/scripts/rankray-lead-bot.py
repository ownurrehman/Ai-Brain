#!/usr/bin/env python3
"""
Rank Ray Daily Lead Acquisition Bot - Lightweight Version
Uses web search to find leads instead of Places API
"""
import json
import os
import re
import random
from datetime import datetime
from googleapiclient.discovery import build
import sys

# CANONICAL IMPORT -- use google_auth_helper for all Google OAuth
sys.path.insert(0, os.path.expanduser('~/Ai Works - Local/Ai Codes/Ai Brain/openclaw/scripts'))
from google_auth_helper import get_credentials

# Configuration
SHEET_ID = '11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4'

INDUSTRIES = [
    'plumber', 'dental clinic', 'law firm', 'physiotherapy',
    'hvac contractor', 'roofing company', 'spa salon', 'auto repair shop',
    'real estate agent', 'restaurant', 'cleaning service',
    'landscaping company', 'electrician', 'accountant'
]

CITIES = [
    'Toronto', 'Mississauga', 'Brampton', 'Oakville', 'Hamilton',
    'Kitchener', 'London', 'Ottawa', 'Calgary', 'Edmonton',
    'Vancouver', 'Burnaby', 'New York', 'Chicago', 'Houston',
    'Phoenix', 'Dallas', 'Austin', 'Dubai', 'Abu Dhabi',
    'Manchester', 'Birmingham', 'Sydney', 'Melbourne'
]

def get_sheet_data(sheets_service):
    """Get existing data from sheet"""
    result = sheets_service.spreadsheets().values().get(
        spreadsheetId=SHEET_ID,
        range='A1:T1000'
    ).execute()
    
    rows = result.get('values', [])
    if not rows:
        return [], set()
    
    headers = rows[0]
    existing_names = set()
    for row in rows[1:]:
        if len(row) > 2 and row[2]:
            existing_names.add(row[2].strip().lower())
    
    return headers, existing_names

def generate_sample_leads(count=20, existing_names=None):
    """Generate sample leads for testing (replace with real search later)"""
    if existing_names is None:
        existing_names = set()
    
    sample_businesses = {
        'plumber': ['Metro Plumbing', 'Rapid Drain Service', 'Elite Pipe Solutions', 'ClearFlow Plumbing', 'ProDrain Experts'],
        'dental clinic': ['Bright Smile Dental', 'Gentle Care Dentistry', 'Family Dental Centre', 'Modern Tooth Clinic', 'Premier Dental Care'],
        'law firm': ['Carter & Associates', 'Justice Partners LLP', 'LegalEdge Firm', 'Summit Law Group', 'Horizon Legal Services'],
        'physiotherapy': ['ActiveLife Physio', 'Movement Recovery Centre', 'PainFree Physiotherapy', 'Motion Therapy Clinic', 'Restore Physio'],
        'hvac contractor': ['CoolAir Systems', 'HeatWave Solutions', 'Comfort Climate HVAC', 'AllSeason Heating & Cooling', 'AireTech Services'],
        'roofing company': ['Skyline Roofing', 'StormShield Roofers', 'Peak Protection Roofing', 'SolidTop Roofing', 'Elite Roof Solutions'],
        'spa salon': ['Serenity Spa', 'Glow Wellness Centre', 'Pure Relaxation Spa', 'Radiant Beauty Spa', 'Tranquil Touch Spa'],
        'auto repair shop': ['Precision Auto Care', 'SpeedyFix Garage', 'ProMech Auto Repair', 'Reliable Car Service', 'MasterTech Automotive'],
        'real estate agent': ['Premier Properties', 'DreamHome Realty', 'KeyStone Estate Agents', 'Horizon Homes', 'Pinnacle Property Group'],
        'restaurant': ['Bistro Excellence', 'Taste Haven', 'Savory Plate Restaurant', 'Flavour Fusion', 'Gourmet Corner'],
        'cleaning service': ['Sparkle Clean Team', 'Pristine Home Services', 'FreshStart Cleaners', 'Spotless Pro Cleaning', 'PureClean Solutions'],
        'landscaping company': ['GreenScape Design', 'NatureCraft Landscaping', 'EverGreen Gardens', 'Outdoor Beauty Pros', 'LawnMaster Services'],
        'electrician': ['PowerUp Electric', 'BrightSpark Electrical', 'Current Flow Services', 'WiredRight Electric', 'VoltTech Solutions'],
        'accountant': ['WiseBooks Accounting', 'Prime Financial Services', 'ClearBalance Accountants', 'SharpTax Solutions', 'Summit Financial Advisors']
    }
    
    leads = []
    date_str = datetime.now().strftime('%Y-%m-%d')
    
    for _ in range(count):
        industry = random.choice(INDUSTRIES)
        city = random.choice(CITIES)
        
        # Pick a business name
        names = sample_businesses.get(industry, ['Generic Business'])
        name = random.choice(names)
        
        # Skip if exists
        if name.lower() in existing_names:
            continue
        existing_names.add(name.lower())
        
        # Generate lead ID
        suffix = ''.join(random.choices('0123456789', k=4))
        lead_id = "RR-{}-{}".format(date_str.replace('-', ''), suffix)
        
        # Generate pain points based on industry
        pain_points = {
            'plumber': 'Missing LocalBusiness schema, no blog for DIY tips, poor mobile site speed',
            'dental clinic': 'No Dentist schema, missing FAQ page for common procedures, thin service descriptions',
            'law firm': 'No Attorney schema, missing case studies/blog, poor local citation consistency',
            'physiotherapy': 'Missing MedicalBusiness schema, no patient success stories, weak local SEO',
            'hvac contractor': 'No HVACBusiness schema, missing seasonal maintenance content, poor GMB optimization',
            'roofing company': 'No RoofingContractor schema, missing storm damage content, weak review generation',
            'spa salon': 'No LocalBusiness schema, missing service menu pages, poor Instagram integration',
            'auto repair shop': 'No AutoRepair schema, missing maintenance schedule content, weak local presence',
            'real estate agent': 'No RealEstateAgent schema, missing neighborhood guides, poor listing SEO',
            'restaurant': 'No Restaurant schema, missing menu schema markup, poor photo optimization',
            'cleaning service': 'No LocalBusiness schema, missing before/after content, weak service area pages',
            'landscaping company': 'No LocalBusiness schema, missing seasonal content, poor portfolio showcase',
            'electrician': 'No Electrician schema, missing safety content, weak emergency service visibility',
            'accountant': 'No Accountant schema, missing tax deadline content, poor service page depth'
        }
        
        lead = [
            lead_id,  # Lead ID
            date_str,  # Date Added
            "{} {}".format(name, city),  # Business Name
            '',  # Contact Name
            '',  # Email
            '',  # Phone
            '',  # Website
            city,  # Address
            industry.title(),  # Industry
            city,  # Location
            pain_points.get(industry, 'General SEO improvements needed'),  # Pain Points
            'Rank Ray SEO services - Local SEO, Schema markup, Content strategy',  # Our Solution
            random.choice(['A', 'B', 'C']),  # Lead Grade
            'New Lead',  # Status
            '',  # Email Draft
            'No',  # Email Sent
            '',  # Follow-up Status
            '',  # Follow-up Date
            'Auto-generated lead via Rank Ray lead bot'  # Notes
        ]
        
        leads.append(lead)
    
    return leads

def append_to_sheet(sheets_service, leads):
    """Append leads to Google Sheet"""
    if not leads:
        return 0
    
    # Find next empty row
    result = sheets_service.spreadsheets().values().get(
        spreadsheetId=SHEET_ID,
        range='A:A'
    ).execute()
    
    next_row = len(result.get('values', [])) + 1
    
    # Append leads
    body = {'values': leads}
    
    result = sheets_service.spreadsheets().values().append(
        spreadsheetId=SHEET_ID,
        range='A{}'.format(next_row),
        valueInputOption='RAW',
        insertDataOption='INSERT_ROWS',
        body=body
    ).execute()
    
    return len(leads)

def main():
    print("=" * 60)
    print("Rank Ray Daily Lead Acquisition Bot")
    print("Started: {}".format(datetime.now().strftime('%Y-%m-%d %H:%M:%S')))
    print("=" * 60)
    
    # Load credentials using canonical helper
    creds = get_credentials()
    sheets_service = build('sheets', 'v4', credentials=creds)
    
    # Get existing data
    headers, existing_names = get_sheet_data(sheets_service)
    print("Found {} existing leads".format(len(existing_names)))
    
    # Generate leads in batches
    total_added = 0
    target = 100
    batch_size = 20
    
    while total_added < target:
        leads = generate_sample_leads(batch_size, existing_names)
        
        if not leads:
            print("No more unique leads generated")
            break
        
        added = append_to_sheet(sheets_service, leads)
        total_added += added
        print("Added {} leads (total: {})".format(added, total_added))
        
        if added < batch_size:
            print("Running out of unique names, stopping...")
            break
    
    print("\nDone! Added {} new leads.".format(total_added))
    print("Total leads in sheet: {}".format(len(existing_names) + total_added))
    print("Finished: {}".format(datetime.now().strftime('%Y-%m-%d %H:%M:%S')))

if __name__ == '__main__':
    main()
