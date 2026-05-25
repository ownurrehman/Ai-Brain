#!/usr/bin/env python3
"""
Rank Ray Daily Lead Acquisition Bot
Finds 100+ hot leads daily and adds them to Google Sheet
"""
import json
import os
import re
import random
import requests
from datetime import datetime
from google.auth.transport.requests import Request
from google.oauth2.credentials import Credentials
from googleapiclient.discovery import build

# Configuration
SHEET_ID = '11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4'
INDUSTRIES = [
    'law firm', 'dental clinic', 'plumber', 'hvac contractor', 
    'roofing contractor', 'physiotherapy', 'spa', 'auto repair',
    'real estate agency', 'restaurant', 'ecommerce', 'fitness center',
    'salon', 'cleaning service', 'landscaping'
]

CITIES = [
    'Toronto', 'Mississauga', 'Brampton', 'Oakville', 'Vaughan',
    'Milton', 'Burlington', 'Hamilton', 'Waterloo', 'Kitchener',
    'London', 'Windsor', 'Ottawa', 'Calgary', 'Edmonton',
    'Vancouver', 'Burnaby', 'Surrey', 'Richmond', 'Coquitlam',
    'New York', 'Chicago', 'Houston', 'Phoenix', 'Philadelphia',
    'San Antonio', 'San Diego', 'Dallas', 'San Jose', 'Austin',
    'Dubai', 'Abu Dhabi', 'Sharjah', 'Ajman', 'Fujairah'
]

def load_credentials():
    token_path = os.path.expanduser('~/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/token.json')
    with open(token_path) as f:
        token_data = json.load(f)
    
    creds = Credentials(
        token=token_data['token'],
        refresh_token=token_data['refresh_token'],
        token_uri=token_data['token_uri'],
        client_id=token_data['client_id'],
        client_secret=token_data['client_secret'],
        scopes=token_data['scopes']
    )
    
    if creds.expired:
        creds.refresh(Request())
        token_data['token'] = creds.token
        with open(token_path, 'w') as f:
            json.dump(token_data, f, indent=2)
    
    return creds

def get_existing_business_names(sheets_service):
    """Get list of existing business names to avoid duplicates"""
    result = sheets_service.spreadsheets().values().get(
        spreadsheetId=SHEET_ID,
        range='C2:C1000'
    ).execute()
    
    names = set()
    for row in result.get('values', []):
        if row and row[0].strip():
            names.add(row[0].strip().lower())
    return names

def search_google_places(query, location, api_key):
    """Search Google Places for businesses"""
    url = "https://maps.googleapis.com/maps/api/place/textsearch/json"
    params = {
        'query': f"{query} in {location}",
        'key': api_key,
        'type': 'business'
    }
    
    try:
        response = requests.get(url, params=params, timeout=10)
        data = response.json()
        
        if data.get('status') == 'OK':
            return data.get('results', [])
        else:
            print(f"Places API error: {data.get('status')}")
            return []
    except Exception as e:
        print(f"Error searching places: {e}")
        return []

def generate_lead_id(existing_ids):
    """Generate unique lead ID"""
    date_str = datetime.now().strftime('%Y%m%d')
    while True:
        suffix = ''.join(random.choices('0123456789', k=4))
        lead_id = f"RR-{date_str}-{suffix}"
        if lead_id not in existing_ids:
            return lead_id

def assess_lead_quality(place):
    """Assess lead quality based on available data"""
    score = 0
    reasons = []
    
    # Has website = good signal
    if place.get('website'):
        score += 20
    else:
        reasons.append("No website")
        score += 40  # They NEED a website!
    
    # Low rating = needs help
    rating = place.get('rating', 0)
    if rating < 4.0:
        score += 30
        reasons.append(f"Low rating: {rating}")
    
    # Few reviews = needs visibility
    reviews = place.get('user_ratings_total', 0)
    if reviews < 10:
        score += 20
        reasons.append("Few reviews")
    
    # No phone = harder to reach
    if not place.get('formatted_phone_number'):
        score += 10
        reasons.append("No phone listed")
    
    # Business is operational
    if place.get('business_status') == 'OPERATIONAL':
        score += 10
    
    # Determine grade
    if score >= 80:
        grade = 'A'
    elif score >= 60:
        grade = 'B'
    elif score >= 40:
        grade = 'C'
    else:
        grade = 'D'
    
    pain_points = " | ".join(reasons) if reasons else "Standard SEO opportunity"
    
    return grade, pain_points, score

def find_leads_batch(sheets_service, places_api_key, batch_size=20):
    """Find a batch of leads using Google Places API"""
    existing_names = get_existing_business_names(sheets_service)
    existing_ids = set()
    
    leads = []
    attempts = 0
    max_attempts = 10
    
    while len(leads) < batch_size and attempts < max_attempts:
        attempts += 1
        
        # Random industry and city
        industry = random.choice(INDUSTRIES)
        city = random.choice(CITIES)
        
        print(f"Searching: {industry} in {city}...")
        
        places = search_google_places(industry, city, places_api_key)
        
        for place in places:
            name = place.get('name', '').strip()
            
            # Skip if already in sheet
            if name.lower() in existing_names:
                continue
            
            # Skip chains and big brands
            if any(chain in name.lower() for chain in ['walmart', 'starbucks', 'mcdonald', 'subway', 'tim hortons']):
                continue
            
            grade, pain_points, score = assess_lead_quality(place)
            
            lead = [
                generate_lead_id(existing_ids),  # Lead ID
                datetime.now().strftime('%Y-%m-%d'),  # Date Added
                name,  # Business Name
                '',  # Contact Name
                '',  # Email
                place.get('formatted_phone_number', ''),  # Phone
                place.get('website', ''),  # Website
                place.get('formatted_address', ''),  # Address
                industry.title(),  # Industry
                city,  # Location
                pain_points,  # Pain Points Found
                'Rank Ray SEO services',  # Our Solution
                grade,  # Lead Grade
                'New Lead',  # Status
                '',  # Email Draft
                'No',  # Email Sent
                '',  # Follow-up Status
                '',  # Follow-up Date
                f"Found via Google Places. Score: {score}/100"  # Notes
            ]
            
            leads.append(lead)
            existing_ids.add(lead[0])
            existing_names.add(name.lower())
            
            if len(leads) >= batch_size:
                break
        
        print(f"  Found {len(leads)} leads so far")
    
    return leads

def append_leads_to_sheet(sheets_service, leads):
    """Append leads to Google Sheet"""
    if not leads:
        return
    
    # Find next empty row
    result = sheets_service.spreadsheets().values().get(
        spreadsheetId=SHEET_ID,
        range='A:A'
    ).execute()
    
    next_row = len(result.get('values', [])) + 1
    
    # Append leads
    range_name = f'A{next_row}'
    body = {'values': leads}
    
    result = sheets_service.spreadsheets().values().append(
        spreadsheetId=SHEET_ID,
        range=range_name,
        valueInputOption='RAW',
        insertDataOption='INSERT_ROWS',
        body=body
    ).execute()
    
    print(f"Appended {len(leads)} leads starting at row {next_row}")
    return result

def main():
    print("=" * 60)
    print("Rank Ray Daily Lead Acquisition Bot")
    print(f"Started: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("=" * 60)
    
    # Load credentials
    creds = load_credentials()
    sheets_service = build('sheets', 'v4', credentials=creds)
    
    # Get Google Places API key from env
    places_api_key = os.environ.get('GOOGLE_PLACES_API_KEY', '')
    if not places_api_key:
        # Try to get from master env
        env_path = os.path.expanduser('~/Ai Works - Local/Ai Codes/Ai Brain/master-env.env')
        if os.path.exists(env_path):
            with open(env_path) as f:
                for line in f:
                    if 'GOOGLE_PLACES_API_KEY' in line:
                        places_api_key = line.split('=', 1)[1].strip().strip('"').strip("'")
                        break
    
    if not places_api_key:
        print("ERROR: No Google Places API key found!")
        return
    
    # Find leads in batches (to avoid rate limits)
    total_leads = 0
    target_leads = 100
    batch_size = 20
    
    while total_leads < target_leads:
        print(f"\nFinding batch of {batch_size} leads...")
        leads = find_leads_batch(sheets_service, places_api_key, batch_size)
        
        if leads:
            append_leads_to_sheet(sheets_service, leads)
            total_leads += len(leads)
            print(f"Total leads added: {total_leads}")
        else:
            print("No leads found in this batch, trying different search...")
            break
    
    print(f"\nDone! Added {total_leads} new leads today.")
    print(f"Finished: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")

if __name__ == '__main__':
    main()
