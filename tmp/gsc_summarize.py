import json
from pathlib import Path
d=json.loads(Path("tmp/gsc_fleet_20260907.json").read_text())
for site, data in d.items():
  print("\n###", site, data.get("range"))
  print("TOP QUERIES")
  for r in data.get("queries",[])[:12]:
    print(f"  {r[\"clicks\"]:4}c {r[\"impressions\"]:5}i ctr={r[\"ctr\"]:5}% pos={r[\"position\"]:5} | {r[\"query\"]}")
  print("TOP PAGES")
  for r in data.get("pages",[])[:8]:
    print(f"  {r[\"clicks\"]:4}c {r[\"impressions\"]:5}i ctr={r[\"ctr\"]:5}% pos={r[\"position\"]:5} | {r[\"page\"][:90]}")
  print("OPPS (pos 4-20, impr>=50)")
  for r in data.get("queries",[]):
    if 4 <= r["position"] <= 20 and r["impressions"]>=50:
      print(f"  {r[\"clicks\"]:4}c {r[\"impressions\"]:5}i ctr={r[\"ctr\"]:5}% pos={r[\"position\"]:5} | {r[\"query\"]}")
