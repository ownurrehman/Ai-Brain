#!/usr/bin/env python3
"""
Full-scope Google OAuth re-auth for oliverjakeseo@gmail.com.
Uses fixed localhost:8085 so the user can open the URL in their own browser.
Run this, open the printed URL, then paste the code this script prints after you authorize.
"""
import json, time
from pathlib import Path
from google_auth_oauthlib.flow import Flow

CREDENTIALS_PATH = Path.home() / "Ai Works - Local" / "Ai Codes" / "Ai Brain" / "credentials" / "google-oauth" / "oliverjakeseo@gmail.com-oauth-credentials.json"
TOKEN_PATH = Path.home() / "Ai Works - Local" / "Ai Codes" / "Ai Brain" / "credentials" / "google-oauth" / "oliverjakeseo@gmail.com-oauth-token.json"

SCOPES = [
    'https://www.googleapis.com/auth/gmail.send',
    'https://www.googleapis.com/auth/gmail.readonly',
    'https://www.googleapis.com/auth/gmail.modify',
    'https://www.googleapis.com/auth/analytics.readonly',
    'https://www.googleapis.com/auth/webmasters',
    'https://www.googleapis.com/auth/webmasters.readonly',
    'https://www.googleapis.com/auth/spreadsheets',
    'https://www.googleapis.com/auth/drive',
    'https://www.googleapis.com/auth/documents',
    'https://www.googleapis.com/auth/calendar',
    'https://www.googleapis.com/auth/tasks',
    'https://www.googleapis.com/auth/contacts',
    'https://www.googleapis.com/auth/indexing',
    'https://www.googleapis.com/auth/photoslibrary',
    'https://www.googleapis.com/auth/youtube',
]

flow = Flow.from_client_secrets_file(
    str(CREDENTIALS_PATH),
    scopes=SCOPES,
    redirect_uri='http://localhost:8085'
)
url, _ = flow.authorization_url(prompt='consent', access_type='offline', include_granted_scopes='true')
print("\nOpen this URL in your browser:\n")
print(url)
print("\nPaste the code from the redirect here and press Enter:")
code = input().strip()

flow.fetch_token(code=code)
creds = flow.credentials
out = {
    'token': creds.token,
    'refresh_token': creds.refresh_token,
    'token_uri': creds.token_uri,
    'client_id': creds.client_id,
    'client_secret': creds.client_secret,
    'scopes': creds.scopes,
    'expiry': creds.expiry.isoformat() if creds.expiry else None,
}
TOKEN_PATH.write_text(json.dumps(out, indent=2))
print("Saved token to", TOKEN_PATH)
print("Scopes count:", len(creds.scopes))
