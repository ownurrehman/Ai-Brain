#!/usr/bin/env python3
"""Extract REAL leads from Elementor form exports. Handles BOM in CSV headers."""

import csv
import os
import re
from datetime import datetime

LEADS_DIR = "/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Website Works/Leads/elementor-submissions-export-2026-05-16"
OUTPUT_DIR = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw"

# OWN emails to skip (tests)
OWN_EMAILS = [
    "rankrayofficial@gmail.com",
    "own-ur-rehman@hotmail.com",
    "contact@rankray.com",
    "test@gmail.com",
]

# Competitor/outreach emails (selling TO us, not buying FROM us)
COMPETITOR_EMAILS = [
    "annawilson.web@gmail.com", "gertssertos@gmail.com", "bagpackofficial.pk@gmail.com",
    "eberhart.archer@gmail.com", "latrice.jansen@msn.com", "emilygrace.studio@outlook.com",
    "lucygordon.mkt@gmail.com", "molly.short@yahoo.com", "golden.otto@goldenig.site",
    "daveasupp@cpaserviceshub.pro", "muhammad.saeed@aksa-sds.com",
    "krishna.linkbuilding@gmail.com", "craig.deberry@outlook.com",
    "info@bestaiseocompany.com", "world@unistarfashion.com",
    "kotai.jesenia@msn.com", "adam.liu@chinaregistry.org.cn",
    "info@speed-seo.net", "joriggs65@gmail.com", "letsgetuoptimize@gmail.com",
    "jack.richards@icopify.online", "cribb.hollie@yahoo.com",
    "mark.nickelson@growseo.com", "joannariggs07@gmail.com",
    "almeta_muller@creatbotd6002.com",
    "marketing-calculator.net@gmail.com", "radha.onlinemediapublisher@gmail.com",
]

# Spam email domain patterns
SPAM_EMAIL_PATTERNS = [
    "mail.ru", "yandex.ru", "yandex.com", "rambler.ru", "bk.ru", "list.ru",
    "inbox.ru", "gismail.online", "directinboxs.com", "xrumgo.site",
    "seoflagman.ru", "321mail.store", "lilycake.ru", "lvdskjn.store",
    "dezia.store", "anomalya.site", "stratosfera.site", "moscowfocus.ru",
    "bazavodolazov.ru", "bazavodolazov.store", "victoria-photographer.ru",
    "bonsoirmail.com", "bientotmail.com", "melssa.com", "sorok4.fun",
    "kirisbyforum.fun", "problemno.shop", "ahrefto.site", "autopages.site",
    "salpingomyu.ru", "farironalds.com", "inboxgate.rest",
    "konsultaciya-yurista-msk01.store", "konsultaciya-yurista-msk01.ru",
    "mailsco.online", "creatbotd6002.com", "chinaregistry.org.cn",
    "vinochok-dnz17.com.ua", "volunteplo.com.ua", "gazeta.pl",
    "nynecapital.com", "dmtstudents.com", "aksa-sds.com", "axabiztech.com",
    "bagpackofficial.pk", "unistarfashion.com", "pacsun-llc.com",
    "superpromise.mx", "gustavoliver.com", "tirefaster.com", "allmind.it.com",
    "roamistan.com", "medsitis.com", "ohsaqsydsmennx.com", "gphioeqgyz.com",
    "sfwvpcpuacnywq.com", "emvuegknbhfe.com", "wyfqmxmou.com",
    "fpuidiputzwzxs.com", "swy888xgb.net", "raystronics.com",
    "buildmyinfra.com", "pangolintools.com", "thecgpacalculator.com",
    "stanleypakistan.com.pk", "marketing-calculator.net",
    "digitallinkbuilding.com", "localseomarketingllc.com",
    "webdesignseocompany.com", "speed-seo.net", "icopify.online",
    "growseo.com", "bestaiseocompany.com", "bonusbacklinks.com",
    "moneyrobot.marketinghelp.guru", "marketinghelp.guru",
    "ahrefs.com", "localseo.com", "localrank.com",
    "webpageoptimization.com", "chinaregistry.org.cn",
]

# Spam URL patterns
SPAM_URL_PATTERNS = [
    "kwork.com", "dark.shopping", "plati.market", "digistore24.com",
    "kraken", "captcha-kra", "vulkan", "crushtop", "hop.cx", "hook.cx",
    "dating.yaroreviews", "hook-platform", "mrpancakenews", "megaweb",
    "americantone", "tumblr.com", "hashnode.dev", "link.darkshopping",
    "lenoralivingston", "vibrometro", "kuhnia-nazakaz", "taxi-prive",
    "canada-chiropractic", "cr8.vn", "vkpodar.ru", "online-video-downloader",
    "telegra.ph", "m.avito.ru", "grace-calipso.ru", "otzivik.ru",
    "dom186.ru", "xrumgo.site", "directinboxs.com", "karkasnye-doma",
    "proekty-domov3.ru", "avtomaticheskienozhi.ru", "3-d-zabor.ru",
    "karkasnye-doma-pod-klyuch7.ru", "pesok-dostawka.by", "smartdevice.by",
    "samoylovaoxana.ru", "mymoscow.forum24.ru", "rentbusspb.ru",
    "mobelmetall.ru", "drogal.ru", "print-classic.ru", "nsttv.ru",
    "rode-wireless-pro.ru", "servis-toyota-moskva.ru",
    "servisnyj-centr-ekaterinburg.ru", "remont-bytovoj-tehniki-ekaterinburg.ru",
    "bytovaya-tehnika-remont-ekaterinburg.ru", "pro-kontrakt.ru",
    "asiancatalog.ru", "radiokomponent.site", "key-open.site",
    "obrabotka-klopov.ru", "taxi-prive.com", "riskdynamics.hashnode.dev",
    "southwestspas7.wordpress.com", "mrpancakenews.wordpress.com",
    "americanstoneart.tumblr.com", "vinochok-dnz17.com.ua",
    "volunteplo.com.ua", "proekty-domov3.ru", "avtomaticheskienozhi.ru",
    "3-d-zabor.ru", "pesok-dostawka.by", "smartdevice.by",
    "samoylovaoxana.ru", "mymoscow.forum24.ru", "rentbusspb.ru",
    "mobelmetall.ru", "drogal.ru", "print-classic.ru", "nsttv.ru",
    "rode-wireless-pro.ru", "servis-toyota-moskva.ru",
    "servisnyj-centr-ekaterinburg.ru", "remont-bytovoj-tehniki-ekaterinburg.ru",
    "bytovaya-tehnika-remont-ekaterinburg.ru", "pro-kontrakt.ru",
    "asiancatalog.ru", "radiokomponent.site", "key-open.site",
    "obrabotka-klopov.ru", "taxi-prive.com", "riskdynamics.hashnode.dev",
    "southwestspas7.wordpress.com", "mrpancakenews.wordpress.com",
    "americanstoneart.tumblr.com", "vinochok-dnz17.com.ua",
    "volunteplo.com.ua",
]

# SEO competitor spam phrases
SPAM_PHRASES = [
    "guest post", "link building", "backlink", "we can help you rank",
    "we can place your website", "white label", "reseller program",
    "seo services", "negative seo", "google review cards", "citation builder",
    "daily backlinks", "seo audit", "boost your ranking", "increase traffic",
    "free report", "money robot", "icopify", "growseo", "speed-seo",
    "bestaiseo", "bonusbacklinks", "localseo", "localrank", "webpageoptimization",
    "ai seo assistant", "smart backlink", "real-time ranking", "grow your website",
    "we're sure in our product", "google maps rankings", "local rank tracker",
    "we provide daily backlinks", "bonus discount", "check backlinks deals",
    "enter directly", "reseller and affiliate", "web hosting with ongoing",
    "growseo has been in business", "growseo reviews card", "seo to boost",
    "improve rankray.com", "reliable seo services", "website's backlinks",
    "strong daily seo", "real web traffic", "prices as low as",
    "may i send you a quote", "best ai seo company", "let'sgetuoptimize",
    "letsgetuoptimize", "bestaiseocompany", "marketinghelp.guru",
    "i would like to discuss seo", "get on first page of google",
    "i can help your website", "increase the number of leads",
    "we offer seo", "our seo", "search engine optimization service",
    "link insertion", "guest posting platform", "authority-building links",
    "digital marketing agency", "ai automation", "app development",
    "video production", "i just visited rankray.com", "engaging video",
    "our videos cost just", "quick inquiry", "meta advertising accounts",
    "corporate testimonial videos", "quotation for", "vendor will have to visit",
    "tan-through swimwear", "premium design", "luxurious", "high-quality",
    "we have reviewed your website", "achieve the 1st position",
    "contact us on whatsapp", "very affordable cost", "remove bad google reviews",
    "digital insurance company", "reputation management", "reddit demand",
    "reddit reputation", "monitoring relevant subreddits", "aged-account",
    "structured reddit", "warm inbound interest", "china registry",
    "domain name registration center", "internet keyword", "cn domain names",
    "baokang holdings", "distributor or business partner in china",
    "trademark registration", "domain registration",
]

def clean_key(k):
    """Remove BOM and quotes from CSV header keys."""
    if k is None:
        return ""
    return str(k).strip().replace('\ufeff', '').replace('"', '').strip()

def normalize_row(raw_row):
    """Normalize CSV row keys (handle BOM, quotes, case)."""
    return {clean_key(k): str(v or "").strip() for k, v in raw_row.items()}

def has_cyrillic(text):
    return bool(re.search(r'[\u0400-\u04FF]', str(text)))

def is_real_name(name):
    """Check if name looks like a real person's name."""
    if not name or len(name) < 3:
        return False
    # No numbers or special chars except hyphens and periods
    if re.search(r'[0-9@#$%&*()_+=\[\]{}|;:,.<>/?~`]', name):
        return False
    # Must start with capital letter
    if not re.search(r'^[A-Z]', name):
        return False
    # Not all lowercase or all uppercase
    if name.islower() or name.isupper():
        return False
    # At least 2 words
    words = name.split()
    if len(words) < 2:
        return False
    # Each word should start with capital
    for w in words:
        if not re.match(r'^[A-Z][a-zA-Z\-\.]*$', w):
            return False
    return True

def is_spam_email(email):
    email_lower = email.lower()
    for pattern in SPAM_EMAIL_PATTERNS:
        if pattern in email_lower:
            return True
    for ce in COMPETITOR_EMAILS:
        if ce in email_lower:
            return True
    return False

def is_spam_message(text):
    text_lower = text.lower()
    for phrase in SPAM_PHRASES:
        if phrase in text_lower:
            return True
    return False

def is_spam_website(url):
    url_lower = url.lower()
    for pattern in SPAM_URL_PATTERNS:
        if pattern in url_lower:
            return True
    return False

def is_own_test(name, email, message):
    if email.lower() in OWN_EMAILS:
        return True
    if "test" in message.lower() and len(message) < 20:
        return True
    if name.lower() in ["own", "test", "rank ray"]:
        return True
    return False

def looks_like_bot(name, email, message, website):
    """Detect bot submissions."""
    full = f"{name} {email} {message} {website}".lower()
    
    # Fake Chrome versions (Chrome isn't at 140+ yet as of 2026)
    # But some real people might have edge versions, so be careful
    # Skip this for now
    
    # Gibberish email patterns
    if re.search(r'j\.28\.5\.6\.9\.9\.8|lum\.enj\.a\.so\.n|m\.el\.issa\.12\.90|20xrumer|freddiemoiva|btmxgdgbhkr|gsqvfasjrmn|ophoyodzkmn|syuapaomn|tececofoc|dkyvsgoxf|t36wf|p058o|jktu9978|155@kirisbyforum', email):
        return True
    
    # Numbers in name with no spaces
    if re.search(r'\d{2,}', name) and " " not in name:
        return True
    
    # Very long single-word lowercase names (bot-like)
    if name.islower() and " " not in name and len(name) > 12:
        return True
    
    return False

def is_real_lead(row):
    name = row.get("Name", "").strip()
    email = row.get("Email", "").strip().lower()
    website = row.get("Website", "").strip().lower()
    message = row.get("Message", "").strip()
    phone = row.get("Phone", "").strip()
    company = row.get("Company", "").strip()
    services = row.get("Services", "").strip()
    
    # Must have name and email
    if not name or not email or "@" not in email:
        return False
    
    # Must look like a real name
    if not is_real_name(name):
        return False
    
    # Skip own tests
    if is_own_test(name, email, message):
        return False
    
    # Skip cyrillic
    if has_cyrillic(f"{name} {email} {message} {website} {company}"):
        return False
    
    # Skip spam emails
    if is_spam_email(email):
        return False
    
    # Skip spam URLs
    if is_spam_website(website):
        return False
    if is_spam_website(message):
        return False
    
    # Skip spam messages
    if is_spam_message(f"{name} {email} {message} {website} {company}"):
        return False
    
    # Skip bot-like submissions
    if looks_like_bot(name, email, message, website):
        return False
    
    # Must have some substance
    if len(message) < 10 and not phone and not company:
        return False
    
    # Block pharma
    pharma = ["buy seroquel", "buy zyrtec", "diclofenac", "clonidine", "pharmavisuals"]
    full_text = f"{name} {email} {message} {website}".lower()
    for p in pharma:
        if p in full_text:
            return False
    
    return True

def read_all_csvs():
    all_leads = []
    
    for filename in sorted(os.listdir(LEADS_DIR)):
        if not filename.endswith(".csv"):
            continue
        filepath = os.path.join(LEADS_DIR, filename)
        
        with open(filepath, "r", encoding="utf-8-sig", errors="replace") as f:
            reader = csv.DictReader(f)
            for raw_row in reader:
                row = normalize_row(raw_row)
                if is_real_lead(row):
                    lead = {
                        "name": row.get("Name", "").strip(),
                        "email": row.get("Email", "").strip().lower(),
                        "phone": row.get("Phone", "").strip(),
                        "website": row.get("Website", "").strip(),
                        "company": row.get("Company", "").strip(),
                        "services": row.get("Services", "").strip(),
                        "message": row.get("Message", "").strip()[:500],
                        "form": row.get("Form Name (ID)", "").strip() or filename.replace("elementor-submissions-export-", "").replace("-2026-05-16.csv", ""),
                        "created_at": row.get("Created At", "").strip(),
                        "source_file": filename,
                    }
                    all_leads.append(lead)
    
    return all_leads

def main():
    all_leads = read_all_csvs()
    
    # Deduplicate by email
    seen = set()
    deduped = []
    for lead in all_leads:
        email = lead["email"]
        if email and email in seen:
            continue
        if email:
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
    out_csv = os.path.join(OUTPUT_DIR, "rankray_real_leads.csv")
    with open(out_csv, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=["name", "email", "phone", "website", "company", "services", "message", "form", "created_at", "source_file"])
        writer.writeheader()
        writer.writerows(deduped)
    
    # Write report
    report = f"""=== REAL LEADS REPORT ===
Date: 2026-05-16
Total files scanned: 14
Real leads found: {len(deduped)}

"""
    for i, lead in enumerate(deduped):
        report += f"{i+1}. {lead['name']} | {lead['email']} | {lead['phone']} | {lead['company']} | {lead['website']} | {lead['created_at'][:10]}\n"
    
    report_path = os.path.join(OUTPUT_DIR, "rankray_real_leads_report.md")
    with open(report_path, "w") as f:
        f.write(report)
    
    print(report)
    print(f"\nSaved to: {out_csv}")

if __name__ == "__main__":
    main()
