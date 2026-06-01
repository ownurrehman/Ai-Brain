#!/usr/bin/env python3
"""
Google OAuth Token Renewer for oliverjakeseo@gmail.com
Run this after resetting a client secret to get a fresh token.
"""
import json, os, webbrowser, socket
from urllib.parse import urlparse, parse_qs
from google_auth_oauthlib.flow import InstalledAppFlow
from google.auth.transport.requests import Request

BASE = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain"
CLIENT_SECRET_PATH = f"{BASE}/system/credentials/google-oauth/client_secret.json"
TOKEN_PATH = f"{BASE}/system/credentials/google-oauth/token.json"

# Scopes must match what the app needs
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

def find_free_port():
    s = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
    s.bind(("localhost", 0))
    port = s.getsockname()[1]
    s.close()
    return port

def main():
    print("=== Google OAuth Token Renewer ===")
    print(f"Account: oliverjakeseo@gmail.com")
    print(f"Client secret: {CLIENT_SECRET_PATH}")
    print(f"Token will be saved to: {TOKEN_PATH}")
    print()

    flow = InstalledAppFlow.from_client_secrets_file(
        CLIENT_SECRET_PATH,
        scopes=SCOPES
    )

    # Use port 8080 to match registered redirect URI
    creds = flow.run_local_server(port=8080, open_browser=True)

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

    print(f"\n✅ Token saved successfully to {TOKEN_PATH}")
    print(f"Access token: {creds.token[:50]}...")
    print(f"Refresh token: {creds.refresh_token[:30]}...")
    print("\nNext: Run your Google Sheets/Drive/Gmail scripts to verify.")

if __name__ == "__main__":
    main()
