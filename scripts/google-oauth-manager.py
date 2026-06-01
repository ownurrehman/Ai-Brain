#!/usr/bin/env python3
"""
google-oauth-manager.py — Unified Google OAuth Manager for Ai Brain
Consolidates URL generation, PKCE token exchange, manual renewal, and status reporting.

Usage:
  python3 google-oauth-manager.py url
  python3 google-oauth-manager.py exchange [redirect_url_or_code]
  python3 google-oauth-manager.py renew
  python3 google-oauth-manager.py status
"""

import sys
import os
import json
import base64
import hashlib
import secrets
import argparse
import datetime
from pathlib import Path
import requests

# Load credentials from master-env.env
ENV_PATH = Path(__file__).parent.parent / "master-env.env"
TOKEN_PATH = Path("/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/oliverjakeseo@gmail.com-oauth-token.json")
CREDENTIALS_PATH = Path("/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/oliverjakeseo@gmail.com-oauth-credentials.json")
REDIRECT_URI = "http://localhost:8080"

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

def load_env():
    env = {}
    if ENV_PATH.exists():
        for line in ENV_PATH.read_text().splitlines():
            line = line.strip()
            if line and not line.startswith("#") and "=" in line:
                k, _, v = line.partition("=")
                env[k.strip()] = v.strip().strip('"').strip("'")
    return env

def get_client_creds():
    env = load_env()
    client_id = env.get("GOOGLE_CLIENT_ID", os.environ.get("GOOGLE_CLIENT_ID", ""))
    client_secret = env.get("GOOGLE_CLIENT_SECRET", os.environ.get("GOOGLE_CLIENT_SECRET", ""))
    
    # Fallback to reading from credentials file if env vars are missing
    if (not client_id or not client_secret) and CREDENTIALS_PATH.exists():
        try:
            with open(CREDENTIALS_PATH, "r") as f:
                data = json.load(f)
                web = data.get("web", {})
                client_id = web.get("client_id", "")
                client_secret = web.get("client_secret", "")
        except Exception:
            pass
            
    if not client_id or not client_secret:
        print("❌ ERROR: GOOGLE_CLIENT_ID / GOOGLE_CLIENT_SECRET not found in master-env.env or client credentials file.")
        sys.exit(1)
        
    return client_id, client_secret

def generate_pkce():
    # Write PKCE elements to a temporary file in brain folder to carry state to exchange
    temp_dir = Path("/Users/sheikhown/.gemini/antigravity-ide/brain")
    temp_dir.mkdir(parents=True, exist_ok=True)
    state_file = temp_dir / "oauth_pkce_state.json"
    
    verifier = base64.urlsafe_b64encode(secrets.token_bytes(32)).rstrip(b'=').decode('ascii')
    challenge = base64.urlsafe_b64encode(hashlib.sha256(verifier.encode()).digest()).rstrip(b'=').decode('ascii')
    state = base64.urlsafe_b64encode(secrets.token_bytes(16)).rstrip(b'=').decode('ascii')
    
    with open(state_file, "w") as f:
        json.dump({"verifier": verifier, "state": state}, f)
        
    return verifier, challenge, state

def read_pkce():
    state_file = Path("/Users/sheikhown/.gemini/antigravity-ide/brain/oauth_pkce_state.json")
    if state_file.exists():
        try:
            with open(state_file, "r") as f:
                data = json.load(f)
                return data.get("verifier"), data.get("state")
        except Exception:
            pass
    return None, None

def cmd_url(args):
    client_id, _ = get_client_creds()
    verifier, challenge, state = generate_pkce()
    
    scope_str = " ".join(SCOPES)
    auth_url = (
        f"https://accounts.google.com/o/oauth2/auth?"
        f"response_type=code&"
        f"client_id={client_id}&"
        f"redirect_uri={REDIRECT_URI}&"
        f"scope={requests.utils.quote(scope_str)}&"
        f"state={state}&"
        f"code_challenge={challenge}&"
        f"code_challenge_method=S256&"
        f"access_type=offline&"
        f"include_granted_scopes=true&"
        f"prompt=consent"
    )
    
    print("=" * 80)
    print("STEP 1: Copy this URL and open it in your browser")
    print("=" * 80)
    print(f"\n{auth_url}\n")
    print("=" * 80)
    print("STEP 2: Sign in as OLIVERJAKESEO@GMAIL.COM")
    print("STEP 3: After approving, copy the full redirect URL (even if it shows an error)")
    print("STEP 4: Run: python3 google-oauth-manager.py exchange [redirect_url_or_code]")
    print("=" * 80)

def cmd_exchange(args):
    client_id, client_secret = get_client_creds()
    verifier, stored_state = read_pkce()
    
    if not verifier:
        print("⚠️ WARNING: No stored PKCE code_verifier state found. Generating custom flow...")
        verifier = base64.urlsafe_b64encode(secrets.token_bytes(32)).rstrip(b'=').decode('ascii')
        
    input_str = args.input
    if not input_str:
        input_str = input("\nPaste the full redirect URL or auth code here: ").strip()
        
    # Extract code if a full URL was pasted
    code = input_str
    if "code=" in input_str:
        from urllib.parse import urlparse, parse_qs
        parsed = urlparse(input_str)
        params = parse_qs(parsed.query)
        code = params.get('code', [None])[0]
        
    if not code:
        print("❌ ERROR: No authorization code could be parsed from input.")
        sys.exit(1)
        
    print(f"Exchanging auth code: {code[:30]}...")
    
    payload = {
        "code": code,
        "client_id": client_id,
        "client_secret": client_secret,
        "redirect_uri": REDIRECT_URI,
        "grant_type": "authorization_code",
        "code_verifier": verifier
    }
    
    resp = requests.post("https://oauth2.googleapis.com/token", data=payload)
    if resp.status_code == 200:
        token_resp = resp.json()
        print("✅ Success! Token exchanged.")
        
        # Save token
        token_data = {
            "token": token_resp["access_token"],
            "access_token": token_resp["access_token"],
            "refresh_token": token_resp.get("refresh_token", ""),
            "token_type": token_resp.get("token_type", "Bearer"),
            "scope": token_resp.get("scope", ""),
            "expires_in": token_resp.get("expires_in", 3599),
            "updated_at": datetime.datetime.now().isoformat()
        }
        
        TOKEN_PATH.parent.mkdir(parents=True, exist_ok=True)
        with open(TOKEN_PATH, 'w') as f:
            json.dump(token_data, f, indent=2)
            
        print(f"✅ Token canonically saved to: {TOKEN_PATH}")
    else:
        print(f"❌ Token exchange FAILED: {resp.text}")
        sys.exit(1)

def cmd_renew(args):
    client_id, client_secret = get_client_creds()
    if not TOKEN_PATH.exists():
        print(f"❌ ERROR: Token file not found at {TOKEN_PATH}. Run 'url' and 'exchange' first.")
        sys.exit(1)
        
    try:
        with open(TOKEN_PATH, 'r') as f:
            token_data = json.load(f)
    except Exception as e:
        print(f"❌ ERROR: Failed to read token file: {e}")
        sys.exit(1)
        
    refresh_token = token_data.get("refresh_token")
    if not refresh_token:
        print("❌ ERROR: No refresh_token found in token file. Need to do full re-auth.")
        sys.exit(1)
        
    print(f"Renewing access token using refresh token...")
    payload = {
        "client_id": client_id,
        "client_secret": client_secret,
        "refresh_token": refresh_token,
        "grant_type": "refresh_token"
    }
    
    resp = requests.post("https://oauth2.googleapis.com/token", data=payload)
    if resp.status_code == 200:
        res = resp.json()
        token_data["token"] = res["access_token"]
        token_data["access_token"] = res["access_token"]
        token_data["expires_in"] = res.get("expires_in", 3599)
        token_data["updated_at"] = datetime.datetime.now().isoformat()
        
        # Keep original refresh_token if new one not issued
        if "refresh_token" in res:
            token_data["refresh_token"] = res["refresh_token"]
            
        with open(TOKEN_PATH, 'w') as f:
            json.dump(token_data, f, indent=2)
            
        print("✅ Access token successfully renewed!")
        print(f"New access token: {res['access_token'][:50]}...")
    else:
        print(f"❌ Renewal FAILED: {resp.text}")
        sys.exit(1)

def cmd_status(args):
    print("=== GOOGLE OAUTH CONFIGURATION STATUS ===")
    print(f"Credentials File: {CREDENTIALS_PATH} ({'FOUND' if CREDENTIALS_PATH.exists() else 'MISSING'})")
    print(f"Token File: {TOKEN_PATH} ({'FOUND' if TOKEN_PATH.exists() else 'MISSING'})")
    
    if TOKEN_PATH.exists():
        try:
            with open(TOKEN_PATH, 'r') as f:
                data = json.load(f)
            print(f"  Access Token: {data.get('access_token', 'N/A')[:40]}...")
            print(f"  Refresh Token: {data.get('refresh_token', 'N/A')[:30]}...")
            print(f"  Scopes: {data.get('scope', 'None')[:60]}...")
            print(f"  Last Updated: {data.get('updated_at', 'Unknown')}")
        except Exception as e:
            print(f"  [ERROR parsing token file: {e}]")
            
    try:
        id, secret = get_client_creds()
        print(f"Client Credentials: VALID (ID={id[:20]}...)")
    except Exception:
        print(f"Client Credentials: ERROR loading")

def main():
    parser = argparse.ArgumentParser(description="Unified Google OAuth Manager")
    subparsers = parser.add_subparsers(dest="command", help="OAuth command to run")
    
    # URL command
    subparsers.add_parser("url", help="Generate authorization URL")
    
    # Exchange command
    ex_parser = subparsers.add_parser("exchange", help="Exchange auth code for access token")
    ex_parser.add_argument("input", nargs="?", help="Paste full redirect URL or raw authorization code")
    
    # Renew command
    subparsers.add_parser("renew", help="Renew access token")
    
    # Status command
    subparsers.add_parser("status", help="Show credentials status")
    
    args = parser.parse_args()
    
    if args.command == "url":
        cmd_url(args)
    elif args.command == "exchange":
        cmd_exchange(args)
    elif args.command == "renew":
        cmd_renew(args)
    elif args.command == "status":
        cmd_status(args)
    else:
        parser.print_help()

if __name__ == "__main__":
    main()
