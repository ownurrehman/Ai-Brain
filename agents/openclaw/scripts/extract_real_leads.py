#!/usr/bin/env python3
"""Extract REAL leads from the massive spam dump. Very conservative."""

import csv
import os
import re

LEADS_DIR = "/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Website Works/Leads/elementor-submissions-export-2026-05-16"
OUTPUT_DIR = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw"

def has_cyrillic(text):
    return bool(re.search(r'[\u0400-\u04FF]', str(text)))

def is_real_lead(row):
    """Ultra-conservative: only accept leads that look 100% real."""
    
    # Get all fields
    name = str(row.get("Name", "") or "").strip()
    email = str(row.get("Email", "") or "").strip().lower()
    website = str(row.get("Website", "") or "").strip().lower()
    message = str(row.get("Message", "") or "").strip()
    phone = str(row.get("Phone", "") or "").strip()
    company = str(row.get("Company", "") or "").strip()
    
    # Must have real-looking name (First Last format)
    if not re.match(r'^[A-Z][a-z]+ [A-Z][a-z]+$', name):
        return False
    
    # Must have email
    if not email or "@" not in email:
        return False
    
    # No cyrillic anywhere
    if has_cyrillic(name) or has_cyrillic(email) or has_cyrillic(message) or has_cyrillic(website):
        return False
    
    # Block known spam domains in email
    spam_domains = [
        "mail.ru", "yandex.ru", "yandex.com", "rambler.ru", "bk.ru", "list.ru",
        "inbox.ru", "gismail.online", "directinboxs.com", "xrumgo.site",
        "seoflagman.ru", "321mail.store", "lilycake.ru", "lvdskjn.store",
        "dezia.store", "anomalya.site", "stratosfera.site", "moscowfocus.ru",
        "bazavodolazov.ru", "bazavodolazov.store", "victoria-photographer.ru",
        "bonsoirmail.com", "bientotmail.com", "melssa.com", "sorok4.fun",
        "kirisbyforum.fun", "problemno.shop", "ahrefto.site", "autopages.site",
        "salpingomyu.ru", "farironalds.com", "inboxgate.rest", "konsultaciya-yurista-msk01.store",
        "konsultaciya-yurista-msk01.ru", "mailsco.online", "creatbotd6002.com",
        "chinaregistry.org.cn", "vinochok-dnz17.com.ua", "volunteplo.com.ua",
        "gazeta.pl", "nynecapital.com", "dmtstudents.com", "aksa-sds.com",
        "axabiztech.com", "bagpackofficial.pk", "unistarfashion.com",
        "pacsun-llc.com", "superpromise.mx", "gustavoliver.com", "tirefaster.com",
        "allmind.it.com", "roamistan.com", "medsitis.com", "ohsaqsydsmennx.com",
        "gphioeqgyz.com", "sfwvpcpuacnywq.com", "emvuegknbhfe.com", "wyfqmxmou.com",
        "fpuidiputzwzxs.com", "swy888xgb.net", "raystronics.com", "buildmyinfra.com",
        "pangolintools.com", "thecgpacalculator.com", "stanleypakistan.com.pk",
        "marketing-calculator.net", "digitallinkbuilding.com", "localseomarketingllc.com",
        "webdesignseocompany.com", "speed-seo.net", "icopify.online", "growseo.com",
        "bestaiseocompany.com", "bonusbacklinks.com", "moneyrobot.marketinghelp.guru",
        "marketinghelp.guru", "ahrefs.com", "localseo.com",
        "localrank.com", "webpageoptimization.com", "chinaregistry.org.cn"
    ]
    email_domain = email.split("@")[-1]
    for sd in spam_domains:
        if sd in email_domain:
            return False
    
    # Block spam URLs
    spam_urls = [
        "kwork.com", "dark.shopping", "plati.market", "digistore24.com",
        "kraken", "captcha-kra", "vulkan", "crushtop", "hop.cx", "hook.cx",
        "dating.yaroreviews", "hook-platform", "mrpancakenews", "megaweb",
        "americantone", "tumblr", "hashnode", "link.darkshopping",
        "lenoralivingston", "vibrometro", "kuhnia-nazakaz", "taxi-prive",
        "canada-chiropractic", "cr8.vn", "vkpodar.ru", "online-video-downloader",
        "telegra.ph", "m.avito.ru", "grace-calipso.ru", "otzivik.ru",
        "dom186.ru", "xrumgo.site", "directinboxs.com", "karkasnye-doma",
        "proekty-domov3.ru", "avtomaticheskienozhi.ru", "3-d-zabor.ru",
        "karkasnye-doma-pod-klyuch7.ru", "pesok-dostawka.by", "smartdevice.by",
        "samoylovaoxana.ru", "mymoscow.forum24.ru", "rentbusspb.ru",
        "mobelmetall.ru", "drogal.ru", "print-classic.ru", "nsttv.ru",
        "rode-wireless-pro.ru", "servis-toyota-moskva.ru", "servisnyj-centr-ekaterinburg.ru",
        "remont-bytovoj-tehniki-ekaterinburg.ru", "bytovaya-tehnika-remont-ekaterinburg.ru",
        "pro-kontrakt.ru", "asiancatalog.ru", "radiokomponent.site",
        "key-open.site", "vskrytie-avto.ru", "obrabotka-klopov.ru",
        "taxi-prive.com", "riskdynamics.hashnode.dev", "southwestspas7.wordpress.com",
        "mrpancakenews.wordpress.com", "americanstoneart.tumblr.com",
        "vinochok-dnz17.com.ua", "volunteplo.com.ua"
    ]
    for su in spam_urls:
        if su in website.lower() or su in message.lower():
            return False
    
    # Block SEO competitor spam messages
    spam_phrases = [
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
        "trademark registration", "domain registration"
    ]
    full_text = f"{name} {email} {website} {message} {company}".lower()
    for sp in spam_phrases:
        if sp in full_text:
            return False
    
    # Block pharma
    pharma = ["buy seroquel", "buy zyrtec", "diclofenac", "clonidine", "propranolol",
              "pill", "tablet", "pharmacy", "medication", "viagra", "cialis", "xanax"]
    for p in pharma:
        if p in full_text:
            return False
    
    # Block own tests
    if "test" in message.lower() and len(message) < 20:
        return False
    if email in ["rankrayofficial@gmail.com", "own-ur-rehman@hotmail.com", "contact@rankray.com"]:
        return False
    if name.lower() in ["own", "test", "rank ray"]:
        return False
    
    # Must have some substance in the message (or a real phone/company)
    if len(message) < 10 and not phone and not company:
        return False
    
    return True

def read_csv_file(filepath):
    leads = []
    with open(filepath, "r", encoding="utf-8", errors="replace") as f:
        reader = csv.DictReader(f)
        for row in reader:
            if is_real_lead(row):
                leads.append({
                    "name": str(row.get("Name", "") or "").strip(),
                    "email": str(row.get("Email", "") or "").strip().lower(),
                    "phone": str(row.get("Phone", "") or "").strip(),
                    "website": str(row.get("Website", "") or "").strip(),
                    "company": str(row.get("Company", "") or "").strip(),
                    "services": str(row.get("Services", "") or "").strip(),
                    "message": str(row.get("Message", "") or "").strip()[:500],
                    "form": str(row.get("Form Name (ID)", "") or "").strip(),
                    "created_at": str(row.get("Created At", "") or "").strip(),
                    "source_file": os.path.basename(filepath),
                })
    return leads

def main():
    all_leads = []
    
    for filename in sorted(os.listdir(LEADS_DIR)):
        if not filename.endswith(".csv"):
            continue
        filepath = os.path.join(LEADS_DIR, filename)
        leads = read_csv_file(filepath)
        print(f"{filename}: {leads} found")
        all_leads.extend(leads)
    
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
    
    # Write CSV
    out_csv = os.path.join(OUTPUT_DIR, "rankray_real_leads.csv")
    with open(out_csv, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=["name", "email", "phone", "website", "company", "services", "message", "form", "created_at", "source_file"])
        writer.writeheader()
        writer.writerows(deduped)
    
    print(f"\n=== RESULT ===")
    print(f"Total real leads: {len(deduped)}")
    for i, lead in enumerate(deduped):
        print(f"{i+1}. {lead['name']} | {lead['email']} | {lead['phone']} | {lead['company']} | {lead['website']} | {lead['created_at'][:10]}")
    
    print(f"\nSaved to: {out_csv}")

if __name__ == "__main__":
    main()
