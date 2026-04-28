# Zoho OAuth Setup for enigma@rankray.com

## Credentials Added to .env
- **Client ID:** 1000.EM2WZ7OV9O5OO4EW1RRJUSGQHZSKNR
- **Client Secret:** 0dd0a9ade704d3c526fe07d8672629c8186c293e23

## Next Step: Get Refresh Token

### Option 1: Manual (Recommended - 5 min)
1. Go to: https://accounts.zoho.com/oauth/v2/auth?scope=ZohoMail.messages.READ,ZohoMail.folders.READ,ZohoMail.accounts.READ&client_id=1000.EM2WZ7OV9O5OO4EW1RRJUSGQHZSKNR&response_type=code&access_type=offline&redirect_uri=https://rankray.com

2. Login with enigma@rankray.com / RR#Tonic@2026

3. Copy the `code=` parameter from the redirect URL

4. Run this command (replace YOUR_CODE):
```bash
curl -s -X POST "https://accounts.zoho.com/oauth/v2/token" \
  -d "code=YOUR_CODE" \
  -d "client_id=1000.EM2WZ7OV9O5OO4EW1RRJUSGQHZSKNR" \
  -d "client_secret=0dd0a9ade704d3c526fe07d8672629c8186c293e23" \
  -d "grant_type=authorization_code" | jq
```

5. Copy `refresh_token` from response and add to .env

### Option 2: Automated Script
Run the script at `/tmp/zoho-get-token.sh`

## After Getting Refresh Token
Add to .env:
```
ZOHO_REFRESH_TOKEN=<paste-from-response>
```

Then test with:
```bash
openclaw exec "source ~/.openclaw/.env && zoho-mail-skill test"
```
