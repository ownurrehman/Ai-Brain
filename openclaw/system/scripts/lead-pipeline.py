#!/usr/bin/env python3
"""Rank Ray Lead Generation Pipeline"""
import json, sys, warnings
warnings.filterwarnings('ignore')

from google.oauth2 import service_account
from googleapiclient.discovery import build

SHEET_ID = "11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4"
SHEET = "Lead Pipeline"
CREDS_PATH = "/Users/sheikhown/.config/google-sheets/credentials.json"
SCOPES = ["https://www.googleapis.com/auth/spreadsheets"]

def svc():
    creds = service_account.Credentials.from_service_account_file(CREDS_PATH, scopes=SCOPES)
    return build("sheets", "v4", credentials=creds)

def get(rng):
    s = svc()
    return s.spreadsheets().values().get(spreadsheetId=SHEET_ID, range=f"{SHEET}!{rng}").execute().get("values", [])

def put(rng, vals):
    s = svc()
    s.spreadsheets().values().update(spreadsheetId=SHEET_ID, range=f"{SHEET}!{rng}",
                                      valueInputOption="USER_ENTERED", body={"values": vals}).execute()

def append(rng, vals):
    s = svc()
    s.spreadsheets().values().append(spreadsheetId=SHEET_ID, range=f"{SHEET}!{rng}",
                                      valueInputOption="USER_ENTERED", insertDataOption="INSERT_ROWS", body={"values": vals}).execute()

cmd = sys.argv[1] if len(sys.argv) > 1 else "help"

if cmd == "rotation":
    d = get("Z1")
    rot = int(d[0][0]) if d and d[0] else 0
    print(f"ROTATION={rot}")

elif cmd == "existing":
    names_raw = get("C:C")
    names = [r[0].strip().lower() for r in names_raw[1:] if r]
    sites_raw = get("G:G")
    sites = []
    for r in sites_raw[1:]:
        if r:
            u = r[0].strip().lower()
            u = u.replace("https://", "").replace("http://", "").replace("www.", "").rstrip("/")
            sites.append(u)
    print(f"COUNT={len(names)}")
    print("---NAMES---")
    for n in names: print(n)
    print("---SITES---")
    for w in sites: print(w)

elif cmd == "set_rotation":
    rot = int(sys.argv[2]) if len(sys.argv) > 2 else 0
    put("Z1", [[rot]])
    print(f"ROTATION={rot}")

elif cmd == "append":
    rows = json.loads(sys.stdin.read())
    if rows:
        append("A:T", rows)
        print(f"APPENDED={len(rows)}")

elif cmd == "get_all":
    d = get("A:T")
    print(json.dumps(d))

elif cmd == "count":
    d = get("A:A")
    print(len(d) - 1)  # minus header

elif cmd == "header":
    d = get("A1:T1")
    print(json.dumps(d[0] if d else []))
