#!/usr/bin/env python3
"""Complete OAuth flow with PKCE - generates URL, waits for code input, exchanges token"""
import json, base64, hashlib, secrets, os
import requests
from pathlib import Path

# Load credentials from master-env.env (NEVER hardcode secrets)
_ENV_PATH = Path(__file__).parent.parent / "master-env.env"
_env = {}
if _ENV_PATH.exists():
    for line in _ENV_PATH.read_text().splitlines():
        line = line.strip()
        if line and not line.startswith("#") and "=" in line:
            k, _, v = line.partition("=")
            _env[k.strip()] = v.strip().strip('"').strip("'")

CLIENT_ID = _env.get("GOOGLE_CLIENT_ID", os.environ.get("GOOGLE_CLIENT_ID", ""))
CLIENT_SECRET = _env.get("GOOGLE_CLIENT_SECRET", os.environ.get("GOOGLE_CLIENT_SECRET", ""))
TOKEN_PATH = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/token.json"
REDIRECT_URI = "http://localhost:8080"

if not CLIENT_ID or not CLIENT_SECRET:
    raise SystemExit("❌ GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET not found in master-env.env")

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

# Generate PKCE
verifier = base64.urlsafe_b64encode(secrets.token_bytes(32)).rstrip(b'=').decode('ascii')
challenge = base64.urlsafe_b64encode(hashlib.sha256(verifier.encode()).digest()).rstrip(b'=').decode('ascii')
state = base64.urlsafe_b64encode(secrets.token_bytes(16)).rstrip(b'=').decode('ascii')

# Build auth URL
scope_str = " ".join(SCOPES)
auth_url = (
    f"https://accounts.google.com/o/oauth2/auth?"
    f"response_type=code&"
    f"client_id={CLIENT_ID}&"
    f"redirect_uri={REDIRECT_URI}&"
    f"scope={requests.utils.quote(scope_str)}&"
    f"state={state}&"
    f"code_challenge={challenge}&"
    f"code_challenge_method=S256&"
    f"access_type=offline&"
    f"include_granted_scopes=true&"
    f"prompt=consent"
)

print("=" * 70)
print("STEP 1: Copy this URL and open it in your browser")
print("=" * 70)
print(f"\n{auth_url}\n")
print("=" * 70)
print("STEP 2: After approving, copy the FULL redirect URL")
print("(it will have ?code=... even though the page shows an error)")
print("=" * 70)

# Get code from user
redirect_url = input("\nPaste the full redirect URL here: ").strip()

# Extract code
from urllib.parse import urlparse, parse_qs
parsed = urlparse(redirect_url)
params = parse_qs(parsed.query)
code = params.get('code', [None])[0]

if not code:
    print("❌ No code found in URL")
    exit(1)

print(f"\nExtracted code: {code[:40]}...")

# Exchange for token
payload = {
    "code": code,
    "client_id": CLIENT_ID,
    "client_secret": CLIENT_SECRET,
    "redirect_uri": REDIRECT_URI,
    "grant_type": "authorization_code",
    "code_verifier": verifier
}

print("Exchanging code for token...")
resp = requests.post("https://oauth2.googleapis.com/token", data=payload)
print(f"Status: {resp.status_code}")

if resp.status_code == 200:
    token_resp = resp.json()
    print(f"✅ SUCCESS!")
    print(f"Access token: {token_resp['access_token'][:50]}...")
    print(f"Refresh token: {token_resp.get('refresh_token', 'N/A')[:30]}...")
    
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
