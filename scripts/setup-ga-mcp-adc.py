#!/usr/bin/env python3
"""
Setup Google Application Default Credentials (ADC) for analytics-mcp.
Uses the existing oliverjakeseo@gmail.com OAuth client to get a token
with analytics.readonly scope, then writes an ADC-compatible JSON.
"""

import json
import os
import sys
import webbrowser
import urllib.parse
import urllib.request
from http.server import HTTPServer, BaseHTTPRequestHandler

# Paths
CREDENTIALS_FILE = os.path.expanduser(
    "~/Ai Works - Local/Ai Codes/Ai Brain/credentials/google-oauth/oliverjakeseo@gmail.com-oauth-credentials.json"
)
ADC_FILE = os.path.expanduser(
    "~/Ai Works - Local/Ai Codes/Ai Brain/credentials/google-oauth/ga-mcp-adc.json"
)

SCOPES = [
    "https://www.googleapis.com/auth/analytics.readonly",
    "https://www.googleapis.com/auth/cloud-platform",
]

auth_code = None

class CallbackHandler(BaseHTTPRequestHandler):
    def do_GET(self):
        global auth_code
        qs = urllib.parse.urlparse(self.path).query
        params = urllib.parse.parse_qs(qs)
        auth_code = params.get("code", [None])[0]
        self.send_response(200)
        self.send_header("Content-Type", "text/html")
        self.end_headers()
        self.wfile.write(
            b"<h1>Authorization complete!</h1><p>You can close this window and return to the terminal.</p>"
        )

def main():
    with open(CREDENTIALS_FILE, "r") as f:
        creds = json.load(f)["web"]

    client_id = creds["client_id"]
    client_secret = creds["client_secret"]
    redirect_uri = "http://localhost:8085"

    # Build auth URL
    auth_url = (
        f"{creds['auth_uri']}"
        f"?client_id={urllib.parse.quote(client_id)}"
        f"&redirect_uri={urllib.parse.quote(redirect_uri)}"
        f"&scope={urllib.parse.quote(' '.join(SCOPES))}"
        f"&response_type=code"
        f"&access_type=offline"
        f"&prompt=consent"
    )

    print("Opening browser for Google Analytics authorization...")
    print(f"If browser doesn't open, visit: {auth_url}")
    webbrowser.open(auth_url)

    # Wait for callback
    server = HTTPServer(("localhost", 8085), CallbackHandler)
    server.handle_request()

    if not auth_code:
        print("No auth code received.", file=sys.stderr)
        sys.exit(1)

    # Exchange code for tokens
    data = urllib.parse.urlencode({
        "code": auth_code,
        "client_id": client_id,
        "client_secret": client_secret,
        "redirect_uri": redirect_uri,
        "grant_type": "authorization_code",
    }).encode()

    req = urllib.request.Request(creds["token_uri"], data=data, method="POST")
    req.add_header("Content-Type", "application/x-www-form-urlencoded")

    with urllib.request.urlopen(req) as resp:
        token_data = json.loads(resp.read().decode())

    # Build ADC-compatible JSON ("authorized_user" type)
    adc_data = {
        "type": "authorized_user",
        "client_id": client_id,
        "client_secret": client_secret,
        "refresh_token": token_data.get("refresh_token", token_data.get("access_token")),
        "quota_project_id": "openclaw-rank-ray-automation",
    }

    os.makedirs(os.path.dirname(ADC_FILE), exist_ok=True)
    with open(ADC_FILE, "w") as f:
        json.dump(adc_data, f, indent=2)

    # Also write to default ADC location
    default_adc = os.path.expanduser("~/.config/gcp/application_default_credentials.json")
    os.makedirs(os.path.dirname(default_adc), exist_ok=True)
    with open(default_adc, "w") as f:
        json.dump(adc_data, f, indent=2)

    print(f"ADC credentials written to: {ADC_FILE}")
    print(f"Also mirrored to: {default_adc}")
    print(f"Refresh token obtained: {bool(token_data.get('refresh_token'))}")

if __name__ == "__main__":
    main()
