#!/usr/bin/env python3
"""Generate OAuth URL for manual browser auth - FIXED for localhost:8080"""
from google_auth_oauthlib.flow import InstalledAppFlow

CLIENT_SECRET_PATH = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/client_secret.json"

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

# CRITICAL: Must match EXACTLY what's in Google Cloud Console
# The client_secret.json has "http://localhost:8080" but we need to override
# to match what you registered in the console
flow = InstalledAppFlow.from_client_secrets_file(
    CLIENT_SECRET_PATH,
    scopes=SCOPES,
    redirect_uri="http://localhost:8080"
)

# Use urn:ietf:wg:oauth:2.0:oob for manual copy-paste (no local server needed)
# This opens a page that shows the code directly
auth_url, state = flow.authorization_url(
    access_type='offline',
    include_granted_scopes='true',
    prompt='consent'
)

print("=== GO TO THIS URL IN YOUR BROWSER ===")
print(f"Make sure you are signed in as: oliverjakeseo@gmail.com")
print()
print(auth_url)
print()
print("=" * 60)
print("IMPORTANT:")
print("1. Sign in as OLIVERJAKESEO@GMAIL.COM (not rankrayofficial)")
print("2. After approving, you'll see a page that says 'This site can't be reached'")
print("3. COPY the FULL URL from the browser address bar")
print("4. Paste it here and I'll extract the auth code")
print("=" * 60)
