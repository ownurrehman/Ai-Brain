import json, os

creds_path = os.path.expanduser("~/Ai Works - Local/Ai Codes/Ai Brain/credentials/google-oauth/oliverjakeseo@gmail.com-oauth-credentials.json")
token_path = os.path.expanduser("~/Ai Works - Local/Ai Codes/Ai Brain/credentials/google-oauth/oliverjakeseo@gmail.com-oauth-token.json")

with open(creds_path) as f:
    creds = json.load(f)

with open(token_path) as f:
    token = json.load(f)

if "installed" in creds:
    client = creds["installed"]
else:
    client = creds["web"]

# Merge required fields
update = {
    "client_id": client.get("client_id"),
    "client_secret": client.get("client_secret"),
    "refresh_token": token.get("refresh_token"),
    "token_uri": client.get("token_uri", "https://oauth2.googleapis.com/token"),
}

# Only update if missing
for k, v in update.items():
    if v and not token.get(k):
        token[k] = v

# Ensure these exist
if "token" not in token and token.get("access_token"):
    token["token"] = token["access_token"]

with open(token_path, "w") as f:
    json.dump(token, f, indent=2)

print("Token fixed:", token_path)
print(json.dumps(token, indent=2)[:500])
