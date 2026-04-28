#!/usr/bin/env python3
"""Quick test of all Semrush API endpoint variations"""

import requests
import json

api_key = "9840fcf3d2ddc97fb25c2919ed59086e"
topic = "semantic seo"

# Try every documented Semrush API endpoint format
endpoints = [
    # Analytics API v1 (most common)
    {
        "name": "Analytics v1 - Phrase Match",
        "url": "https://api.semrush.com/analytics/v1/keywords/phrase_match",
        "params": {"key": api_key, "phrase": topic, "database": "us", "export": "json", "limit": 5}
    },
    {
        "name": "Analytics v1 - Related Keywords",
        "url": "https://api.semrush.com/analytics/v1/keywords/related",
        "params": {"key": api_key, "phrase": topic, "database": "us", "export": "json", "limit": 5}
    },
    {
        "name": "Analytics v1 - Domain Organic",
        "url": "https://api.semrush.com/analytics/v1/domain/organic",
        "params": {"key": api_key, "domain": "rankray.com", "database": "us", "export": "json", "limit": 5}
    },
    # Keywords API (alternative)
    {
        "name": "Keywords API - Ideas",
        "url": "https://api.semrush.com/keywords/keyword_ideas",
        "params": {"key": api_key, "phrase": topic, "database": "us", "export": "json", "limit": 5}
    },
    {
        "name": "Keywords API - Overview",
        "url": "https://api.semrush.com/keywords/keyword_overview",
        "params": {"key": api_key, "phrase": topic, "database": "us", "domain": "rankray.com", "export": "json", "limit": 5}
    },
    # Try CSV export (some endpoints only support CSV)
    {
        "name": "Analytics v1 - CSV Export",
        "url": "https://api.semrush.com/analytics/v1/keywords/phrase_match",
        "params": {"key": api_key, "phrase": topic, "database": "us", "export": "csv", "limit": 5}
    }
]

print("Testing all Semrush API endpoints...\n")

for i, endpoint in enumerate(endpoints, 1):
    print(f"{i}. {endpoint['name']}")
    try:
        response = requests.get(endpoint['url'], params=endpoint['params'], timeout=15)
        
        if response.status_code == 200:
            print(f"   ✅ SUCCESS ({response.status_code})")
            # Try to parse as JSON first
            try:
                data = response.json()
                if isinstance(data, dict) and 'result_list' in data:
                    print(f"   Results: {len(data['result_list'])} items")
                    if data['result_list']:
                        print(f"   Sample: {json.dumps(data['result_list'][0], indent=2, default=str)[:200]}")
                else:
                    print(f"   Response: {json.dumps(data, indent=2, default=str)[:300]}")
            except:
                # CSV or other format
                print(f"   Response (first 300 chars): {response.text[:300]}")
            print(f"\n   🎯 FOUND WORKING ENDPOINT!")
            break
        else:
            print(f"   ❌ Error {response.status_code}: {response.text[:100]}")
    except Exception as e:
        print(f"   ❌ Exception: {e}")
    print()

print("\n" + "="*60)
print("TEST COMPLETE")
print("="*60)
