#!/usr/bin/env python3
"""Exchange auth code for token"""
import json, os
from google_auth_oauthlib.flow import InstalledAppFlow

CLIENT_SECRET_PATH = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/client_secret.json"
TOKEN_PATH = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/token.json"

SCOPES = [
    "https://www.googleapis.com/auth/gmail.readonly",
    "https://www.googleapis.com/auth/gmail.send",
    "https://www.googleapis.com/auth/gmail.modify",
    "https://www.googleapis.com/auth/calendar",
    "https://www.googleapis.com/auth/drive",
    "https://www.googleapis.com/auth/contacts.readonly",
    "https://www.googleapis.com/auth/spreadsheets",
    "https://www.googleapis.com/auth/documents.readonly",
    "https://www.googleapis.com/auth/analytics",
    "https://www.googleapis.com/auth/analytics.readonly",
    "https://www.googleapis.com/auth/webmasters",
    "https://www.googleapis.com/auth/webmasters.readonly",
    "https://www.googleapis.com/auth/indexing",
]

# Build flow with same redirect_uri
flow = InstalledAppFlow.from_client_secrets_file(
    CLIENT_SECRET_PATH,
    scopes=SCOPES,
    redirect_uri="http://localhost:8080"
)

# The auth code from the redirect URL
code = "4/0AeoWuM-Ojpm25cMzPxW1WaM22CEqLvY78k45I0jS1vPTcc6yb9HLAc10sbHBG-2LZdw63Q"

print("Exchanging auth code for token...")
try:
    flow.fetch_token(code=code)
    creds = flow.credentials

    token_data = {
        "token": creds.token,
        "access_token": creds.token,
        "refresh_token": creds.refresh_token,
        "token_type": "Bearer",
        "scope": " ".join(creds.scopes),
        "expires_in": 3599,
    }

    with open(TOKEN_PATH, 'w') as f:
        json.dump(token_data, f, indent=2)

    print(f"✅ Token saved to {TOKEN_PATH}")
    print(f"Access token: {creds.token[:50]}...")
    print(f"Refresh token: {creds.refresh_token[:30]}...")
    print(f"Scopes: {len(creds.scopes)} granted")

except Exception as e:
    print(f"❌ Failed: {e}")
