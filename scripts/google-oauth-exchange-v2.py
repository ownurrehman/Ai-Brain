#!/usr/bin/env python3
"""Exchange auth code for token - v2"""
import json
import requests

CLIENT_ID = "803355012183-bfgbc7g540isfs1pkno6f3fknb135cqb.apps.googleusercontent.com"
CLIENT_SECRET_PATH = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/client_secret.json"
TOKEN_PATH = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/token.json"

# Load client secret
with open(CLIENT_SECRET_PATH) as f:
    data = json.load(f)
    CLIENT_SECRET = data["installed"]["client_secret"]

AUTH_CODE = "4/0AeoWuM9GEVqWBXjA-OANMQDj9D1MP5zdmA4v-6qGETrnnwRS2dn2wfTafI3iR8xZmsLEAQ"
REDIRECT_URI = "http://localhost:8080"

print(f"Client ID: {CLIENT_ID[:30]}...")
print(f"Client secret: {CLIENT_SECRET[:15]}...{CLIENT_SECRET[-5:]}")
print(f"Auth code: {AUTH_CODE[:30]}...")

# Exchange code for token
payload = {
    "code": AUTH_CODE,
    "client_id": CLIENT_ID,
    "client_secret": CLIENT_SECRET,
    "redirect_uri": REDIRECT_URI,
    "grant_type": "authorization_code"
}

print("\nExchanging code for token...")
resp = requests.post("https://oauth2.googleapis.com/token", data=payload)
print(f"Status: {resp.status_code}")

if resp.status_code == 200:
    token_resp = resp.json()
    print(f"✅ SUCCESS!")
    print(f"Access token: {token_resp['access_token'][:50]}...")
    print(f"Refresh token: {token_resp.get('refresh_token', 'N/A')[:30]}...")
    print(f"Expires in: {token_resp.get('expires_in', 'N/A')}")
    print(f"Scope: {token_resp.get('scope', 'N/A')[:80]}...")
    
    # Save in our format
    token_data = {
        "token": token_resp["access_token"],
        "access_token": token_resp["access_token"],
        "refresh_token": token_resp.get("refresh_token", ""),
        "token_type": token_resp.get("token_type", "Bearer"),
        "scope": token_resp.get("scope", ""),
        "expires_in": token_resp.get("expires_in", 3599),
    }
    
    with open(TOKEN_PATH, 'w') as f:
        json.dump(token_data, f, indent=2)
    
    print(f"\n✅ Token saved to {TOKEN_PATH}")
else:
    print(f"❌ FAILED: {resp.text}")
