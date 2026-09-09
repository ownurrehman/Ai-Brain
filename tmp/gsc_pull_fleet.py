import json
from datetime import date, timedelta
from pathlib import Path
from google.oauth2 import service_account
from googleapiclient.discovery import build
sa = Path.home() / ".config/google-sheets/credentials.json"
creds = service_account.Credentials.from_service_account_file(str(sa), scopes=["https://www.googleapis.com/auth/webmasters.readonly"])
svc = build("searchconsole", "v1", credentials=creds, cache_discovery=False)
end = date.today() - timedelta(days=3)
start = end - timedelta(days=27)
sites = ["https://tonicphysio.com/", "https://www.coinsfera.com/", "https://rankray.com/"]
out = {}
for site in sites:
    body = {"startDate": start.isoformat(), "endDate": end.isoformat(), "dimensions": ["query"], "rowLimit": 20}
    try:
        r = svc.searchanalytics().query(siteUrl=site, body=body).execute()
        rows = [{"query": row["keys"][0], "clicks": row.get("clicks"), "impressions": row.get("impressions"), "ctr": round(row.get("ctr", 0) * 100, 1), "position": round(row.get("position", 0), 1)} for row in r.get("rows", [])]
        body_p = dict(body); body_p["dimensions"] = ["page"]
        rp = svc.searchanalytics().query(siteUrl=site, body=body_p).execute()
        pages = [{"page": row["keys"][0], "clicks": row.get("clicks"), "impressions": row.get("impressions"), "ctr": round(row.get("ctr", 0) * 100, 1), "position": round(row.get("position", 0), 1)} for row in rp.get("rows", [])]
        out[site] = {"queries": rows, "pages": pages, "range": f"{start}->{end}"}
        print("OK", site, len(rows), len(pages))
    except Exception as e:
        out[site] = {"error": str(e)}; print("ERR", site, e)
Path("/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/tmp/gsc_fleet_20260907.json").write_text(json.dumps(out, indent=2))
print("done")
