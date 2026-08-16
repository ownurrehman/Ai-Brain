import json, re
from urllib.parse import urlparse

# Sample of remaining rows missing email
samples = [
    {"row": 152, "business": "McArthur Dental", "website": "https://mcarthurdental.ca"},
    {"row": 189, "business": "Point Loma Dentist", "website": "https://www.pointlomadentist.com"},
    {"row": 193, "business": "Stoel Rives LLP", "website": "https://www.stoel.com"},
    {"row": 197, "business": "Nashville Smiles", "website": "https://www.nashvillesmiles.com"},
    {"row": 200, "business": "West Nashville Dental", "website": "https://westnashvilledental.com"},
    {"row": 239, "business": "Wolseley Law LLP", "website": "https://wolseleylaw.ca"},
    {"row": 243, "business": "Copperstone Dental", "website": "https://copperstonedental.ca"},
]

EMAIL_RE = re.compile(r"[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}")
BAD_DOMAINS = ["example.com", "domain.com", "test.com", "gmail.com", "yahoo.com", "hotmail.com", "outlook.com", "icloud.com", "gargle.com"]

def find_emails(text, domain):
    emails = EMAIL_RE.findall(text)
    filtered = []
    for e in emails:
        e = e.lower()
        if any(bad in e for bad in BAD_DOMAINS):
            continue
        if ".webp" in e or ".png" in e or ".jpg" in e:
            continue
        if domain and domain not in e:
            if not any(e.startswith(x) for x in ["info", "hello", "contact", "office", "admin", "support", "reception"]):
                continue
        filtered.append(e)
    return list(dict.fromkeys(filtered))[:5]

print("Testing Firecrawl on sample sites missing emails...")
