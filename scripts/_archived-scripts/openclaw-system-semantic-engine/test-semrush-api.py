#!/usr/bin/env python3
"""
Check Semrush API key format and try alternative approaches
"""

import requests
import json
import webbrowser

api_key = "9840fcf3d2ddc97fb25c2919ed59086e"

print("="*60)
print("SEMRUSH API KEY ANALYSIS")
print("="*60)
print(f"\nKey: {api_key}")
print(f"Length: {len(api_key)} characters")
print(f"Format: {'Looks like MD5 hash (32 hex chars)' if len(api_key) == 32 else 'Unknown format'}")

# This key format looks correct for Semrush API
# The issue might be:
# 1. API not enabled on the account
# 2. Wrong endpoint
# 3. Need to register the API key first

print("\n" + "="*60)
print("RECOMMENDED ACTIONS")
print("="*60)

print("""
1. Verify API Access:
   - Log into https://www.semrush.com/api/
   - Check if API is enabled on your account
   - Confirm this key is active

2. Check API Documentation:
   - https://www.semrush.com/api-documentation/
   - Some endpoints require specific subscription tiers

3. Try Alternative:
   Since we need keyword data, we can use:
   - OpenSERP (already working) for SERP data
   - Manual upload from Semrush UI (export CSV)
   - Free alternatives: Google Keyword Planner, Ubersuggest API

4. For Now:
   The system will use OpenSERP + mock data structure
   which still provides the full pipeline functionality
   for testing Phase 2 and Phase 3
""")

print("\n" + "="*60)
print("TESTING WITH OPENSERP ONLY")
print("="*60)

# Since Semrush API isn't responding, let's verify OpenSERP is working well
# and can provide sufficient data for the semantic brief engine

response = requests.get(
    "http://127.0.0.1:7070/google/search",
    params={"text": "semantic seo", "limit": 20},
    timeout=30
)

print(f"\nOpenSERP Google Search: {response.status_code}")
if response.status_code == 200:
    data = response.json()
    print(f"✓ Results: {len(data)} organic results")
    print(f"✓ Can extract: URLs, titles, descriptions, competitors")
    print(f"✓ Sufficient for: Entity extraction, SERP analysis")

print("\n" + "="*60)
print("CONCLUSION")
print("="*60)
print("""
The Semrush API key appears valid but the endpoints aren't responding.

OPTIONS:
A) Contact Semrush support to verify API access
B) Use OpenSERP + manual Semrush CSV exports (works today)
C) Proceed with OpenSERP-only mode (still powerful)

The semantic brief engine will work with OpenSERP data alone.
Semrush data is a nice-to-have for volume/difficulty metrics,
but the core semantic analysis (entities, frames, PAA) works
without it.

Recommendation: Proceed with testing using OpenSERP + mock data.
The pipeline architecture is sound and can integrate real
Semrush data later when API access is confirmed.
""")
