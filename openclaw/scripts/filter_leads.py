import csv
import os
import re
import json
from datetime import datetime
from collections import Counter

LEADS_DIR = "/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Website Works/Leads/elementor-submissions-export-2026-05-16"
OUTPUT_DIR = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw"

# Spam indicators
SPAM_DOMAINS = [
    "movietut.top", "kpvysokovo.ru", "amanitaroom.ru", "kwork.com", "dark.shopping",
    "plati.market", "moneyrobot", "marketinghelp", "znayka.com.ua", "ohsaqsydsmennx.com",
    "t.me/", "telegram", "bit.ly", "tinyurl"
]

SPAM_KEYWORDS = [
    "backlink", "money robot", "seo tool", "telegram group", "dark shopping",
    "user agent", "database", "спам", "мухомор", "шрек", "подборка", "сериалы",
    "торрент", "кино", "фильмы", "погода", "обмен валют"
]

SPAM_EMAIL_PATTERNS = [
    r"mail\.ru$", r"@yandex\.", r"@rambler\.", r"temp-mail", r"10minutemail",
    r"guerrillamail", r"throwaway"
]

def is_spam(row):
    """Check if a submission is spam."""
    text = " ".join(str(v) for v in row.values()).lower()

    # Check spam domains in website field
    website = str(row.get("Website", "")).lower()
    for domain in SPAM_DOMAINS:
        if domain in website:
            return True, f"spam_domain: {domain}"

    # Check spam keywords in message
    for kw in SPAM_KEYWORDS:
        if kw.lower() in text:
            return True, f"spam_kw: {kw}"

    # Check spam email patterns
    email = str(row.get("Email", "")).lower()
    for pattern in SPAM_EMAIL_PATTERNS:
        if re.search(pattern, email):
            return True, f"spam_email_pattern: {pattern}"

    # Check for excessive URLs in message (link spam)
    message = str(row.get("Message", ""))
    url_count = len(re.findall(r'https?://[^\s\"<>]+', message))
    if url_count > 3:
        return True, f"excessive_urls: {url_count}"

    # Check for non-ASCII spam (Russian/Cyrillic)
    cyrillic_chars = len(re.findall(r'[\u0400-\u04FF]', text))
    if cyrillic_chars > 10:
        return True, f"cyrillic_text: {cyrillic_chars} chars"

    # Fake Chrome versions (e.g., 143.0.0.0 doesn't exist yet)
    ua = str(row.get("User Agent", ""))
    chrome_version_match = re.search(r'Chrome/(\d+)\.', ua)
    if chrome_version_match:
        version = int(chrome_version_match.group(1))
        if version > 140:  # Chrome isn't at 140+ yet
            return True, f"fake_chrome: {version}"

    # Check if name is gibberish or suspicious
    name = str(row.get("Name", ""))
    if len(name) > 0:
        # All lowercase or all uppercase (bot-like)
        if name.islower() or name.isupper():
            return True, "suspicious_name_format"
        # No vowels (gibberish)
        vowels = set("aeiouAEIOU")
        if not any(c in vowels for c in name):
            return True, "no_vowels_in_name"

    # Check if phone is suspicious
    phone = str(row.get("Phone", "")).replace(" ", "").replace("-", "").replace("+", "")
    if phone and not phone.isdigit():
        return True, "invalid_phone"
    if phone and len(phone) < 7:
        return True, "phone_too_short"

    return False, ""

def normalize_row(row):
    """Normalize column names across different forms."""
    normalized = {}
    for k, v in row.items():
        if not k:
            continue
        key = k.strip().lower().replace(" ", "_")
        normalized[key] = v.strip() if isinstance(v, str) else v
    return normalized

def extract_name(norm_row):
    return norm_row.get("name", "")

def extract_email(norm_row):
    return norm_row.get("email", "")

def extract_phone(norm_row):
    return norm_row.get("phone", "")

def extract_website(norm_row):
    return norm_row.get("website", "")

def extract_company(norm_row):
    return norm_row.get("company", "")

def extract_services(norm_row):
    return norm_row.get("services", "")

def extract_message(norm_row):
    return norm_row.get("message", "")

def extract_created(norm_row):
    return norm_row.get("created_at", "")

def extract_form(norm_row):
    return norm_row.get("form_name_(id)", "")

def main():
    all_leads = []
    spam_count = 0
    legit_count = 0
    file_stats = []

    for filename in sorted(os.listdir(LEADS_DIR)):
        if not filename.endswith(".csv"):
            continue
        filepath = os.path.join(LEADS_DIR, filename)
        total_rows = 0
        file_spam = 0
        file_legit = 0

        with open(filepath, "r", encoding="utf-8", errors="replace") as f:
            reader = csv.DictReader(f)
            for row in reader:
                total_rows += 1
                norm = normalize_row(row)
                spam, reason = is_spam(norm)
                if spam:
                    spam_count += 1
                    file_spam += 1
                else:
                    legit_count += 1
                    file_legit += 1
                    lead = {
                        "name": extract_name(norm),
                        "email": extract_email(norm),
                        "phone": extract_phone(norm),
                        "website": extract_website(norm),
                        "company": extract_company(norm),
                        "services": extract_services(norm),
                        "message": extract_message(norm),
                        "form": extract_form(norm),
                        "created_at": extract_created(norm),
                        "source_file": filename,
                    }
                    all_leads.append(lead)

        file_stats.append({
            "file": filename,
            "total": total_rows,
            "spam": file_spam,
            "legit": file_legit
        })

    # Remove exact duplicates by email
    seen_emails = set()
    deduped = []
    for lead in all_leads:
        email = lead["email"].lower().strip()
        if email and email in seen_emails:
            continue
        if email:
            seen_emails.add(email)
        deduped.append(lead)

    # Sort by date descending
    def parse_date(d):
        try:
            return datetime.strptime(d, "%Y-%m-%d %H:%M:%S")
        except:
            return datetime.min
    deduped.sort(key=lambda x: parse_date(x["created_at"]), reverse=True)

    # Write CSV output
    output_csv = os.path.join(OUTPUT_DIR, "legit_leads_2026-05-16.csv")
    with open(output_csv, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=["name","email","phone","website","company","services","message","form","created_at","source_file"])
        writer.writeheader()
        writer.writerows(deduped)

    # Write JSON for inspection
    output_json = os.path.join(OUTPUT_DIR, "legit_leads_2026-05-16.json")
    with open(output_json, "w", encoding="utf-8") as f:
        json.dump(deduped, f, indent=2, ensure_ascii=False)

    # Summary report
    report = f"""
=== LEAD FILTERING REPORT ===
Date: 2026-05-16

Files processed: {len(file_stats)}
Total submissions: {spam_count + legit_count}
Spam removed: {spam_count}
Legit kept: {legit_count}
After dedup: {len(deduped)}

--- Per File ---
"""
    for s in file_stats:
        report += f"{s['file']}: {s['total']} total | {s['spam']} spam | {s['legit']} legit\n"

    report += f"\nOutput files:\n- {output_csv}\n- {output_json}\n"

    report_path = os.path.join(OUTPUT_DIR, "lead_filter_report.md")
    with open(report_path, "w") as f:
        f.write(report)

    print(report)
    print(f"\nFirst 5 legit leads:")
    for i, lead in enumerate(deduped[:5]):
        print(f"{i+1}. {lead['name']} | {lead['email']} | {lead['company']} | {lead['phone']} | {lead['website']}")

if __name__ == "__main__":
    main()
