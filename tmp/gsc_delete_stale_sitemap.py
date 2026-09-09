from pathlib import Path
from google.oauth2 import service_account
from googleapiclient.discovery import build
sa = Path.home()/".config/google-sheets/credentials.json"
creds = service_account.Credentials.from_service_account_file(str(sa), scopes=["https://www.googleapis.com/auth/webmasters"])
svc = build("searchconsole","v1",credentials=creds,cache_discovery=False)
site="https://rankray.com/"
feed="https://rankray.com/location-sitemap.xml"
print("deleting", feed)
svc.sitemaps().delete(siteUrl=site, feedpath=feed).execute()
print("deleted OK")
smaps=svc.sitemaps().list(siteUrl=site).execute()
for s in smaps.get("sitemap",[]):
  print(s.get("path"), "errors=", s.get("errors"), "warn=", s.get("warnings"))
