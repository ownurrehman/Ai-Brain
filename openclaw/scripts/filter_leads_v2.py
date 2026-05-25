import csv
import os
import re
import json
from datetime import datetime

LEADS_DIR = "/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Website Works/Leads/elementor-submissions-export-2026-05-16"
OUTPUT_DIR = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw"

# === BLACKLISTED DOMAINS / TLDs ===
BAD_TLDS = [".ru", ".ua", ".by", ".cn", ".vn", ".at", ".tk", ".ml", ".cf", ".ga"]
BAD_DOMAINS = [
    "movietut.top", "kpvysokovo.ru", "amanitaroom.ru", "kwork.com", "dark.shopping",
    "plati.market", "moneyrobot", "marketinghelp", "znayka.com.ua", "ohsaqsydsmennx.com",
    "t.me/", "telegram", "bit.ly", "tinyurl", "digistore24.com", "pharmavisuals.com",
    "kraken14-at.com", "megaweb-9at.com", "salpingomyu.ru", "farironalds.com", "inboxgate.rest",
    "konsultaciya-yurista-msk01.ru", "vibrometro1a.com", "kuhnia-nazakaz.by", "taxi-prive.com",
    "canada-chiropractic.com", "cr8.vn", "chinaregistry.org.cn", "creatbotd6002.com",
    "growseo.com", "webpageoptimization.com", "webdesignseocompany.com", "localseomarketingllc.com",
    "sfwvpcpuacnywq.com", "gphioeqgyz.com", "localrank.com", "citadigitalgroup.com",
    "seranking.com", "seoptimer.com", "ahrefs.com", "moz.com", "screamingfrog.co.uk",
    "seo.com", "searchenginejournal.com", "neilpatel.com", "backlinko.com", "yoast.com",
    "yoast-seo.com", "rankmath.com", "ahrefs.com", "ubersuggest.com", "similarweb.com",
    "localrank", "localseo", "seomarketing", "weboptimization", "digistore",
]

# Competitor SEO agency indicators in message/content
COMPETITOR_KEYWORDS = [
    "growseo", "local rank", "citation builder", "seo tool", "seo software",
    "backlink checker", "keyword research", "competitor analysis", "rank tracking",
    "we can help you rank", "boost your ranking", "increase traffic", "free seo audit",
    "we offer seo", "our seo services", "white label seo", "reseller", "affiliate program",
    "we're an seo agency", "we are an seo", "digital marketing agency", "seo marketing",
    "search engine optimization", "link building", "citation building"
]

# Pharma spam keywords
PHARMA_KEYWORDS = [
    "buy seroquel", "buy zyrtec", "diclofenac", "clonidine", "propranolol",
    "pill", "tablet", "pharmacy", "medication", "drug", "prescription",
    "cheapest", "generic", "viagra", "cialis", "xanax", "ambien"
]

SPAM_KEYWORDS = [
    "backlink", "money robot", "seo tool", "telegram group", "dark shopping",
    "user agent", "database", "спам", "мухомор", "шрек", "подборка", "сериалы",
    "торрент", "кино", "фильмы", "погода", "обмен валют", "Americans nearing retirement",
    "retirement", "stock market", "crypto exchange", "bitcoin", "ethereum",
    "3D printer", "analizador de vibraciones", "thi?t k? l?i logo", "chiropractic",
    "limousine", "limo service", "kitchen design", "taxi", "auto accident",
    "migraines", "OTC drug", "equipos de calibracion", "vibration analysis",
    "China Registry", "domain registration", "trademark registration",
]

SPAM_EMAIL_DOMAINS = [
    "mail.ru", "yandex.ru", "rambler.ru", "bk.ru", "list.ru", "inbox.ru",
    "temp-mail.org", "10minutemail.com", "guerrillamail.com", "throwaway.email",
    "inboxgate.rest", "salpingomyu.ru", "farironalds.com",
]

def is_spam(row):
    """Strict spam detection."""
    raw_text = " ".join(str(v) for v in row.values() if v).lower()
    name = str(row.get("Name", "") or "").strip()
    email = str(row.get("Email", "") or "").strip().lower()
    website = str(row.get("Website", "") or "").strip().lower()
    message = str(row.get("Message", "") or "").strip().lower()
    company = str(row.get("Company", "") or "").strip().lower()
    phone = str(row.get("Phone", "") or "").strip()
    ua = str(row.get("User Agent", "") or "").strip()

    # 1. Empty or near-empty submissions
    if not name and not email:
        return True, "empty_name_email"
    if not email or "@" not in email:
        return True, "no_email"

    # 2. Bad TLDs in website
    if website:
        for tld in BAD_TLDS:
            if tld in website:
                return True, f"bad_tld: {tld}"
        for domain in BAD_DOMAINS:
            if domain in website:
                return True, f"bad_domain: {domain}"

    # 3. Bad email domains
    email_domain = email.split("@")[-1] if "@" in email else ""
    if email_domain in SPAM_EMAIL_DOMAINS:
        return True, f"spam_email_domain: {email_domain}"
    if any(bad in email for bad in ["mail.ru", "yandex", "rambler", "inboxgate", "farironalds", "salpingomyu"]):
        return True, "spam_email_pattern"

    # 4. Competitor/SEO spam keywords
    for kw in COMPETITOR_KEYWORDS:
        if kw.lower() in raw_text:
            return True, f"competitor_kw: {kw}"

    # 5. Pharma spam
    for kw in PHARMA_KEYWORDS:
        if kw.lower() in raw_text:
            return True, f"pharma: {kw}"

    # 6. General spam keywords
    for kw in SPAM_KEYWORDS:
        if kw.lower() in raw_text:
            return True, f"spam_kw: {kw}"

    # 7. Excessive URLs in message
    url_count = len(re.findall(r'https?://[^\s\"<>]+', raw_text))
    if url_count > 2:
        return True, f"excessive_urls: {url_count}"

    # 8. Cyrillic text (Russian spam)
    cyrillic_chars = len(re.findall(r'[\u0400-\u04FF]', raw_text))
    if cyrillic_chars > 5:
        return True, f"cyrillic: {cyrillic_chars}"

    # 9. Fake Chrome versions
    chrome_match = re.search(r'Chrome/(\d+)\.', ua)
    if chrome_match and int(chrome_match.group(1)) > 140:
        return True, f"fake_chrome: {chrome_match.group(1)}"

    # 10. Suspicious name patterns
    if name:
        # All lowercase with no spaces (bot-like)
        if name.islower() and " " not in name and len(name) > 8:
            return True, "bot_name_lower"
        # All uppercase
        if name.isupper() and len(name) > 8:
            return True, "bot_name_upper"
        # No vowels
        vowels = set("aeiouAEIOU")
        if len(name) > 3 and not any(c in vowels for c in name):
            return True, "no_vowels"
        # Numbers in name (suspicious)
        if re.search(r'\d{2,}', name):
            return True, "numbers_in_name"
        # Single word very long (gibberish)
        if " " not in name and len(name) > 20:
            return True, "long_gibberish_name"

    # 11. Suspicious phone
    phone_digits = re.sub(r'\D', '', phone)
    if phone_digits:
        # Repeated digits
        if re.search(r'(\d)\1{5,}', phone_digits):
            return True, "repeated_digits_phone"
        # Too short or too long
        if len(phone_digits) < 7 or len(phone_digits) > 15:
            return True, "suspicious_phone_length"

    # 12. Gibberish email patterns
    if re.search(r'[a-zA-Z]\d+[a-zA-Z]\d+', email):  # Like j.28.5.6.9.9.8.
        return True, "gibberish_email"

    # 13. Blank messages with just website or generic text
    if len(message) < 10 and not company and not phone:
        return True, "low_effort"

    return False, ""

def normalize_row(row):
    norm = {}
    for k, v in row.items():
        if not k:
            continue
        key = k.strip().lower().replace(" ", "_")
        norm[key] = v.strip() if isinstance(v, str) else v
    return norm

def main():
    all_leads = []
    spam_count = 0
    legit_count = 0
    file_stats = []

    for filename in sorted(os.listdir(LEADS_DIR)):
        if not filename.endswith(".csv"):
            continue
        filepath = os.path.join(LEADS_DIR, filename)
        total = spam = legit = 0

        with open(filepath, "r", encoding="utf-8", errors="replace") as f:
            reader = csv.DictReader(f)
            for row in reader:
                total += 1
                spam, reason = is_spam(row)
                if spam:
                    spam_count += 1
                    spam += 1
                else:
                    legit_count += 1
                    legit += 1
                    all_leads.append({
                        "name": str(row.get("Name", "") or "").strip(),
                        "email": str(row.get("Email", "") or "").strip().lower(),
                        "phone": str(row.get("Phone", "") or "").strip(),
                        "website": str(row.get("Website", "") or "").strip(),
                        "company": str(row.get("Company", "") or "").strip(),
                        "services": str(row.get("Services", "") or "").strip(),
                        "message": str(row.get("Message", "") or "").strip()[:500],
                        "form": str(row.get("Form Name (ID)", "") or "").strip(),
                        "created_at": str(row.get("Created At", "") or "").strip(),
                        "source_file": filename,
                    })

        file_stats.append({"file": filename, "total": total, "spam": spam, "legit": legit})

    # Deduplicate by email
    seen = set()
    deduped = []
    for lead in all_leads:
        email = lead["email"]
        if not email or email in seen:
            continue
        seen.add(email)
        deduped.append(lead)

    # Sort by date desc
    def sort_key(l):
        try:
            return datetime.strptime(l["created_at"], "%Y-%m-%d %H:%M:%S")
        except:
            return datetime.min
    deduped.sort(key=sort_key, reverse=True)

    # Write CSV
    out_csv = os.path.join(OUTPUT_DIR, "legit_leads_filtered.csv")
    with open(out_csv, "w", newline="", encoding="utf-8") as f:
        w = csv.DictWriter(f, fieldnames=["name","email","phone","website","company","services","message","form","created_at","source_file"])
        w.writeheader()
        w.writerows(deduped)

    # Write JSON
    out_json = os.path.join(OUTPUT_DIR, "legit_leads_filtered.json")
    with open(out_json, "w") as f:
        json.dump(deduped, f, indent=2, ensure_ascii=False)

    # Report
    report = f"""=== LEAD FILTERING v2 ===
Date: 2026-05-16
Files: {len(file_stats)}
Total submissions: {spam_count + legit_count}
Spam removed: {spam_count}
Legit kept: {legit_count}
After dedup: {len(deduped)}
"""
    for s in file_stats:
        report += f"\n{s['file']}: {s['total']} total | {s['spam']} spam | {s['legit']} legit"

    report += f"\n\nOutput: {out_csv}\n"

    with open(os.path.join(OUTPUT_DIR, "lead_filter_v2_report.md"), "w") as f:
        f.write(report)

    print(report)
    print(f"\n--- Top 20 Legit Leads ---")
    for i, l in enumerate(deduped[:20]):
        print(f"{i+1}. {l['name'][:30]:30s} | {l['email'][:35]:35s} | {l['company'][:25]:25s} | {l['phone'][:18]:18s} | {l['website'][:40]:40s}")

if __name__ == "__main__":
    main()
