#!/usr/bin/env uv run python3
"""Lead Generation Pipeline - Rotation 3, Canada West"""
import gspread
from google.oauth2.service_account import Credentials
import datetime
import re

# Config
SHEET_ID = '11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4'
CREDENTIALS_FILE = '/Users/sheikhown/.config/google-sheets/credentials.json'
TODAY = '2026-05-03'
ROTATION = 3

# City configuration
CITIES = [
    ('Ottawa', 'ON'),
    ('Calgary', 'AB'),
    ('Edmonton', 'AB'),
    ('Vancouver', 'BC'),
    ('Montreal', 'QC'),
    ('Winnipeg', 'MB'),
    ('Halifax', 'NS'),
    ('Quebec City', 'QC'),
    ('London', 'ON'),
    ('Windsor', 'ON')
]

# Search queries per industry
INDUSTRIES = [
    'dentist',
    'law firm',
    'physiotherapy clinic',
    'real estate agent',
    'home renovation contractor',
    'restaurant',
    'hair salon',
    'auto repair shop',
    'construction company',
    'ecommerce store'
]

def get_sheet():
    scopes = ['https://www.googleapis.com/auth/spreadsheets']
    creds = Credentials.from_service_account_file(CREDENTIALS_FILE, scopes=scopes)
    client = gspread.authorize(creds)
    return client.open_by_key(SHEET_ID).sheet1

def get_existing_data(worksheet):
    all_data = worksheet.get_all_values()
    existing_businesses = set()
    existing_websites = set()
    
    for row in all_data[1:]:  # Skip header
        if len(row) > 2 and row[2]:
            existing_businesses.add(row[2].strip().lower())
        if len(row) > 6 and row[6]:
            website = row[6].strip().lower()
            website = re.sub(r'^(https?://)?(www\.)?', '', website).rstrip('/')
            existing_websites.add(website)
    
    return existing_businesses, existing_websites

def check_duplicate(business_name, website, existing_businesses, existing_websites):
    """Check if business or website already exists"""
    if business_name.strip().lower() in existing_businesses:
        return True
    
    normalized = re.sub(r'^(https?://)?(www\.)?', '', website.lower()).rstrip('/')
    if normalized in existing_websites:
        return True
    
    return False

def generate_lead_id(city, counter):
    """Generate lead ID: RR-CA-YYYYMMDD-{counter}"""
    return f"RR-CA-{TODAY.replace('-', '')}-{counter}"

def create_lead_row(lead_id, business_name, website, address, industry, city, province, pain_points, grade):
    """Create a lead row with all 20 columns"""
    
    # Generate personalized solution based on pain points
    solutions = []
    if 'missing schema' in pain_points.lower() or 'schema' in pain_points.lower():
        solutions.append("Our technical SEO team will implement comprehensive schema markup (LocalBusiness, Service, Review) to enhance search visibility and rich snippets")
    if 'h1' in pain_points.lower() or 'meta' in pain_points.lower():
        solutions.append("Complete on-page SEO overhaul including optimized H1/H2 structure, compelling meta descriptions, and keyword-rich title tags")
    if 'content' in pain_points.lower() or 'blog' in pain_points.lower() or 'thin' in pain_points.lower():
        solutions.append("Strategic content marketing with SEO-optimized blog posts, service pages, and location-based landing pages")
    if 'speed' in pain_points.lower() or 'slow' in pain_points.lower() or 'mobile' in pain_points.lower():
        solutions.append("Page speed optimization and mobile responsiveness improvements for better user experience and Core Web Vitals")
    if 'gmb' in pain_points.lower() or 'google business' in pain_points.lower() or 'local' in pain_points.lower():
        solutions.append("Complete Google Business Profile optimization and local SEO strategy to dominate local search results")
    if 'ssl' in pain_points.lower() or 'https' in pain_points.lower() or 'security' in pain_points.lower():
        solutions.append("SSL certificate implementation and technical security audit to protect user data and improve trust signals")
    if 'link' in pain_points.lower():
        solutions.append("Strategic link building campaign to improve domain authority and search rankings")
    
    if not solutions:
        solutions.append("Comprehensive website audit and SEO strategy tailored to your business goals")
    
    solution_text = "; ".join(solutions)
    
    # Generate personalized email draft
    email_draft = f"""Subject: Quick SEO Win for {business_name}

Hi there,

I was looking at {business_name}'s website and noticed a few opportunities that could significantly boost your online visibility in {city}:

{pain_points}

At Rank Ray, we specialize in helping {industry.lower()} businesses like yours dominate local search results. {solution_text.split('.')[0]}.

I'd love to share a quick, no-obligation audit of your site's current SEO performance. Would you be open to a brief 10-minute call this week?

Best regards,
Rank Ray Team"""
    
    return [
        lead_id,              # A: Lead ID
        TODAY,                # B: Date Added
        business_name,        # C: Business Name
        "To Be Researched",   # D: Contact Name
        "To Be Researched",   # E: Email
        "To Be Researched",   # F: Phone
        website,              # G: Website
        address,              # H: Address
        industry,             # I: Industry
        f"{city}, {province}", # J: Location
        pain_points,          # K: Pain Points Found
        solution_text,        # L: Our Solution
        grade,                # M: Lead Grade
        "New Lead",           # N: Status
        email_draft,          # O: Email Draft
        "No",                 # P: Email Sent
        "Pending",            # Q: Follow-up Status
        "",                   # R: Follow-up Date
        TODAY,                # S: Last Touchpoint
        f"Rotation {ROTATION} - Canada West batch, {city} target, duplicate check: passed"  # T: Notes
    ]

if __name__ == "__main__":
    print("Starting Lead Generation Pipeline - Rotation 3, Canada West")
    print(f"Date: {TODAY}")
    print(f"Cities: {len(CITIES)}")
    print("-" * 50)
    
    # Connect to sheet
    worksheet = get_sheet()
    existing_businesses, existing_websites = get_existing_data(worksheet)
    
    print(f"Existing businesses in sheet: {len(existing_businesses)}")
    print(f"Existing websites in sheet: {len(existing_websites)}")
    
    # Update rotation counter
    worksheet.update_acell('Z1', str(ROTATION + 1))
    print(f"Updated rotation counter to {ROTATION + 1}")
