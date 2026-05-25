import csv
import os
import re
from datetime import datetime

LEADS_DIR = "/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Website Works/Leads/elementor-submissions-export-2026-05-16"
OUTPUT_DIR = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw"

def has_cyrillic(text):
    return bool(re.search(r'[\u0400-\u04FF]', str(text)))

def is_own_test(name, email, message):
    own_emails = ["rankrayofficial@gmail.com", "own-ur-rehman@hotmail.com", "contact@rankray.com"]
    if email and email.lower().strip() in own_emails:
        return True
    if name and "own" in name.lower() and "test" in str(message).lower():
        return True
    return False

def looks_like_spam(name, email, website, message):
    text = f"{name} {email} {website} {message}".lower()
    
    # Cyrillic text
    if has_cyrillic(text):
        return True
    
    # Bad domains
    bad_domains = [
        "moneyrobot", "marketinghelp", "icopify", "growseo", "bestaiseo",
        "bonusbacklinks", "speed-seo", "localseo", "localrank", "webpageoptimization",
        "digistore24", "ahrefto.site", "autopages.site", "kwork.com", "dark.shopping",
        "plati.market", "salpingomyu", "farironalds", "inboxgate", "melssa",
        "sorok4", "kirisbyforum", "problemno", "creatbot", "vulkan", "kraken",
        "captcha-kra", "konsultaciya", "pharmavisuals", "ohsaqsydsmennx",
        "gphioeqgyz", "sfwvpcpuacnywq", "emvuegknbhfe", "wyfqmxmou", "fpuidiputzwzxs",
        "swy888xgb", "raystronics", "buildmyinfra", "pangolintools", "thecgpacalculator",
        "stanleypakistan", "cr8.vn", "vibrometro", "kuhnia-nazakaz", "taxi-prive",
        "canada-chiropractic", "mrpancakenews", "hook-platform", "megaweb", "americantone",
        "tumblr", "hashnode", "hook.cx", "dating.yaroreviews", "hop.cx", "crushtop",
        "link.darkshopping", "lenoralivingston", "gazeta.pl", "vinochok-dnz17",
        "volunteplo", "xnecapital", "marketing-calculator", "digitallinkbuilding",
        "chinaregistry", "kpvysokovo", "amanitaroom", "znayka", "trassir",
        "builder-spb", "rentbusspb", "mobelmetall", "drogal", "print-classic",
        "3-d-zabor", "proekty-domov", "avtomaticheskienozhi", "karkasnye-doma",
        "pesok-dostawka", "smartdevice", "samoylovaoxana", "mymoscow.forum24",
        "m.avito", "bazavodolazov", "seoflagman", "321mail", "lilycake",
        "victoria-photographer", "moscowfocus", "stratosfera", "anomalya",
        "dezia", "lvdskjn", "otzivik", "xrumgo", "dom186", "grace-calipso",
        "directinboxs", "list.ru", "bk.ru", "mail.ru", "yandex", "rambler",
        "gismail", "inbox.ru", "farironalds", "salpingomyu", "t36wf", "p058o",
        "lum.enj.a.so.n038", "m.el.issa.12.90.9.9", "155@kirisbyforum",
        "20xrumer", "freddiemoiva", "btmxgdgbhkr", "gsqvfasjrmn", "ophoyodzkmn",
        "syuapaomn", "tiffanysmith1991", "311dhdsjoooter", "xxriimorhpt",
        "denis.krasnikovvlb", "goicrdostkr", "pwhijzsbbpn", "xkbshksgnel",
        "wgsylknysoi", "havdbapujpi", "kzizxbohksr", "pbyvvmsmgpr", "rnxjywyqrmi",
        "dcbfpriidml", "ofgepmugfsr", "w.oo.d.f.o.r.d.j.a.m.e.s.o.n4",
        "w.o.o.d.f.o.r.d.j.a.m.e.s.o.n4", "pomgmk9oheivabfooksm", "yknvafazksn",
        "eaqgimichmr", "kvxmkayhjmt", "wgsylknysoi", "kina3axjglime", "rey.tim",
        "ponomaryov.borya", "vmavrina46", "goldboy1931", "fortcentracot",
        "tim.rey", "sboqtzkveel", "pomgmk9oheivabfooksm", "dobro@vkpodar",
        "7gei2e2d2222wkh", "ta.yahschwarz", "fgbrox", "lampsidcgrun",
        "jonikaquecy", "me1@topcrush", "dianasquecy", "darkto#quecy", "darkzho#quecy",
        "darkman#quecy", "jktu9978", "gusto@vinochok", "finance@volunteplo",
        "adam.liu@chinaregistry", "duocsi.letruongan", "ops@goldenig",
        "joannariggs07", "mark.nickelson", "cribb.hollie", "craig.deberry",
        "ankit.s", "sarah.collins", "jesenia.kotai", "jo.riggs", "sanfordchots",
        "benvak", "jack.richards", "darrenfat", "hollie.cribb", "paramazanov",
        "almeta_muller", "20xrumer", "katerinejones", "dkyvsgoxfKt",
        "j.28.5.6.9.9.8", "lum.enj.a.so.n038", "elizabethzackery",
        "tececofoc33", "gsqvfasjrmn", "ophoyodzkmn", "btmxgdgbhkr",
        "p058o", "jktu9978", "t36wf", "m.el.issa", "155@kirisbyforum",
        "syuapaomn", "karen.white@pacsun-llc", "marketing-calculator",
        "radha.onlinemediapublisher", "latrice.jansen", "emilygrace.studio",
        "lucygordon.mkt", "molly.short", "golden.otto", "david.a",
        "muhammad.saeed", "arooj", "krishna.mandal", "nightingalepdfexisP",
        "craig.deberry", "ankit.s", "sarah.collins", "jesenia.kotai",
        "australiangameshowsexisP", "australiangamepex", "adam.liu",
        "carey.hutson", "jo.riggs", "sanfordchots", "benvak",
        "jack.richards", "darrenfat", "hollie.cribb", "mark.nickelson",
        "joanna.riggs", "almeta_muller", "20xrumer", "katerinejones",
        "dkyvsgoxfKt", "j.28.5.6.9.9.8", "lum.enj.a.so.n038",
        "elizabethzackery", "tececofoc33", "gsqvfasjrmn", "ophoyodzkmn",
        "btmxgdgbhkr", "p058o", "jktu9978", "t36wf", "m.el.issa",
        "155@kirisbyforum", "syuapaomn", "edward.sam", "shermanjen",
        "andrewtib", "timothytor", "robertdew", "frankneete", "3d_bsmr",
        "karkasnyy_rrmt", "gotovye_icpi", "vskrytie_bgsr", "maketnaya_szpr",
        "karolinawzk", "josephdaync", "vyvod_zymi", "calvinblits",
        "kontrakt_nhoi", "siseeliance", "karkasnyy_ipml", "avtomatich_sbsr",
        "eldonliz", "andrewcem", "tiffanytoure", "darryltitte", "ruslantam",
        "gustavooliver", "albertnes", "unichtozhe_supt", "avtoservis_jtkr",
        "sistema_ijpn", "karkasnyy_wqel", "carpetaya", "denis.krasnikovvlb",
        "mizrox", "nathanher", "leonardjoupt", "georgeroumn", "victorblurn",
        "winstonhaize", "andrewtib", "jonikaquecy", "alfreddalry", "fgbrox"
    ]
    for bd in bad_domains:
        if bd.lower() in text:
            return True
    
    # SEO spam / competitor outreach
    seo_spam_phrases = [
        "guest post", "link building", "backlink", "we can help you rank",
        "we can place your website", "white label", "reseller program",
        "seo services", "negative seo", "google review cards", "citation builder",
        "daily backlinks", "seo audit", "boost your ranking", "increase traffic",
        "free report", "we offer", "our services", "grow your website",
        "icopify", "growseo", "speed-seo", "bestaiseo", "bonusbacklinks",
        "money robot", "marketing help guru", "localseomarketingllc",
        "webpageoptimization", "localrank", "we provide", "we are an seo",
        "we're an seo", "digital marketing agency", "ai seo assistant"
    ]
    for phrase in seo_spam_phrases:
        if phrase.lower() in text:
            return True
    
    # Fake Chrome versions
    chrome_match = re.search(r'Chrome/(\d+)\.', text)
    if chrome_match and int(chrome_match.group(1)) > 140:
        return True
    
    # Suspicious email patterns
    if re.search(r'j\.28\.5\.6\.9\.9\.8|lum\.enj\.a\.so\.n|m\.el\.issa\.12\.90|20xrumer|freddiemoiva|btmxgdgbhkr|gsqvfasjrmn|ophoyodzkmn|syuapaomn|tececofoc|dkyvsgoxf', text):
        return True
    
    return False

def looks_legit(row):
    name = str(row.get("Name", "") or "").strip()
    email = str(row.get("Email", "") or "").strip().lower()
    website = str(row.get("Website", "") or "").strip().lower()
    message = str(row.get("Message", "") or "").strip()
    phone = str(row.get("Phone", "") or "").strip()
    company = str(row.get("Company", "") or "").strip()
    
    # Must have name and email
    if not name or not email or "@" not in email:
        return False
    
    # Skip Own's tests
    if is_own_test(name, email, message):
        return False
    
    # Skip spam
    if looks_like_spam(name, email, website, message):
        return False
    
    # Skip suspicious names
    if re.match(r'^[a-z]+[0-9]+[a-z]+[0-9]+$', name.lower()):  # bot names like "fgbrox"
        return False
    if name.islower() and len(name) > 12 and " " not in name:
        return False  # long lowercase single word
    if name.isupper() and len(name) > 8:
        return False  # all caps
    
    # Must have some substance
    if len(message) < 15 and not company and not phone:
        return False
    
    return True

def read_all_csvs():
    all_rows = []
    
    for filename in sorted(os.listdir(LEADS_DIR)):
        if not filename.endswith(".csv"):
            continue
        filepath = os.path.join(LEADS_DIR, filename)
        
        with open(filepath, "r", encoding="utf-8", errors="replace") as f:
            reader = csv.DictReader(f)
            for row in reader:
                # Standardize field names
                clean_row = {}
                for k, v in row.items():
                    if not k:
                        continue
                    clean_k = k.strip().lower().replace(" ", "_")
                    clean_row[clean_k] = str(v or "").strip()
                
                # Map various column names
                name = clean_row.get("name", "")
                email = clean_row.get("email", "")
                phone = clean_row.get("phone", "")
                website = clean_row.get("website", "")
                company = clean_row.get("company", "")
                message = clean_row.get("message", "")
                services = clean_row.get("services", "")
                form_name = clean_row.get("form_name_(id)", "")
                created_at = clean_row.get("created_at", "")
                
                # Some forms have different column names
                if not name:
                    name = clean_row.get("your_name", "")
                if not email:
                    email = clean_row.get("your_email", "")
                if not email:
                    email = clean_row.get("your_email", "")
                if not email:
                    email = clean_row.get("email_address", "")
                if not website:
                    website = clean_row.get("website_address", "")
                if not website:
                    website = clean_row.get("enter_website_here", "")
                if not message:
                    message = clean_row.get("tell_us_about_your_business", "")
                if not message:
                    message = clean_row.get("how_did_you_hear_about_rank_ray?", "")
                if not services:
                    services = clean_row.get("which_services_can_we_provide_you?*", "")
                if not services:
                    services = clean_row.get("what_services_can_we_provide_you?*", "")
                if not services:
                    services = clean_row.get("service", "")
                if not company:
                    company = clean_row.get("company_size", "")
                if not phone:
                    phone = clean_row.get("whatsapp", "")
                
                # Normalize message (some have field name as key)
                for key in clean_row:
                    if key not in ["name", "email", "phone", "website", "company", "services", 
                                   "form_name_(id)", "submission_id", "created_at", "user_id",
                                   "user_agent", "user_ip", "referrer", "your_name", "your_email",
                                   "website_address", "enter_website_here", "tell_us_about_your_business",
                                   "how_did_you_hear_about_rank_ray?", "which_services_can_we_provide_you?*",
                                   "what_services_can_we_provide_you?*", "service", "company_size",
                                   "whatsapp", "email_address"]:
                        if not message and len(clean_row[key]) > 5:
                            message = clean_row[key]
                
                unified = {
                    "name": name,
                    "email": email,
                    "phone": phone,
                    "website": website,
                    "company": company,
                    "services": services,
                    "message": message[:300],  # Truncate for CSV
                    "form": form_name or filename.replace("elementor-submissions-export-", "").replace("-2026-05-16.csv", ""),
                    "created_at": created_at,
                    "source_file": filename,
                }
                
                all_rows.append(unified)
    
    return all_rows

def main():
    all_rows = read_all_csvs()
    
    # Filter legit
    legit = [r for r in all_rows if looks_legit(r)]
    
    # Deduplicate by email
    seen = set()
    deduped = []
    for r in legit:
        email = r["email"].lower().strip()
        if email and email in seen:
            continue
        if email:
            seen.add(email)
        deduped.append(r)
    
    # Sort by date
    def sort_key(r):
        try:
            return datetime.strptime(r["created_at"], "%Y-%m-%d %H:%M:%S")
        except:
            return datetime.min
    deduped.sort(key=sort_key, reverse=True)
    
    # Write CSV
    out_csv = os.path.join(OUTPUT_DIR, "rankray_clean_leads.csv")
    with open(out_csv, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=["name", "email", "phone", "website", "company", "services", "message", "form", "created_at", "source_file"])
        writer.writeheader()
        writer.writerows(deduped)
    
    # Write report
    report = f"""=== CLEAN LEADS REPORT ===
Total submissions scanned: {len(all_rows)}
Legit after filtering: {len(legit)}
After deduplication: {len(deduped)}

"""
    for i, r in enumerate(deduped):
        report += f"{i+1}. {r['name']} | {r['email']} | {r['phone']} | {r['company']} | {r['website']} | {r['created_at'][:10]}\n"
    
    report_path = os.path.join(OUTPUT_DIR, "rankray_clean_leads_report.md")
    with open(report_path, "w") as f:
        f.write(report)
    
    print(report)

if __name__ == "__main__":
    main()
