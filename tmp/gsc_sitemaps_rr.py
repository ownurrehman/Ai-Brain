import json
from pathlib import Path
from google.oauth2 import service_account
from googleapiclient.discovery import build
sa = Path.home()/".config/google-sheets/credentials.json"
creds = service_account.Credentials.from_service_account_file(str(sa), scopes=["https://www.googleapis.com/auth/webmasters.readonly"])
svc = build("searchconsole","v1",credentials=creds,cache_discovery=False)
site="https://rankray.com/"
smaps = svc.sitemaps().list(siteUrl=site).execute()
print(json.dumps(smaps, indent=2)[:3000])
