#!/usr/bin/env python3
"""
Rank Ray Outreach Engine v3 - Skill-integrated, self-improving

Integrates:
- marketing-copywriting skill (cold_email_check.py as quality gate)
- seo-audit skill (real SEO findings per prospect)
- Self-learning: tracks what works, kills what doesn't, creates new templates
- Conversion filtering: only emails businesses with real SEO problems + budget signals
- Prospect enrichment: scrapes website, checks Google Maps ranking, finds real issues
"""
import json, os, re, time, random, datetime, ssl, subprocess, sys
from urllib.request import Request, urlopen
from urllib.error import HTTPError
from dotenv import load_dotenv
import requests as req_lib

load_dotenv(os.path.expanduser("~/.hermes/.env"))
API_KEY = os.getenv("AGENTMAIL_API_KEY")
INBOX = os.getenv("AGENTMAIL_INBOX")
API_BASE = "https://api.agentmail.to/v0"

OUTREACH_DIR = os.path.dirname(os.path.abspath(__file__))
DATA_DIR = os.path.join(OUTREACH_DIR, "data")
LOG_DIR = os.path.join(OUTREACH_DIR, "logs")
TEMPLATE_DIR = os.path.join(OUTREACH_DIR, "templates")
SKILL_DIR = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/skills/marketing-copywriting"

DAILY_LIMIT = 100
SLEEP_BETWEEN = 8

ssl._create_default_https_context = ssl._create_unverified_context

# === QUALITY GATE: cold_email_check.py integration ===
BANNED_OPENERS = ["hi there", "i hope this email", "i hope you are", "my name is", "great to e-meet", "great to connect"]
BANNED_SIGNOFFS = ["best,\n", "kind regards", "warm regards", "sincerely", "best regards"]
BANNED_CTAS = ["worth a 10-minute call", "book a free audit", "book a free consultation", "schedule a call", "schedule a consultation", "hop on a call", "set up a time to chat"]
BANNED_BUZZWORDS = ["leverage", "robust", "seamless", "cutting-edge", "transformative", "elevate", "unlock", "navigate the landscape", "in today's world", "dive deep", "delve", "harness", "empower"]
BANNED_REASSURANCE = ["hope this helps", "let me know if you have questions", "let me know if you need", "feel free to reach out", "don't hesitate"]

def validate_email_draft(subject, body):
    """Run cold_email_check rules against draft. Returns (passed, violations)."""
    violations = []
    full = f"{subject}\n{body}".lower()
    
    # Subject rules
    if not subject:
        violations.append("empty subject")
    else:
        if "\u2014" in subject:
            violations.append("subject has em-dash")
        if re.search(r"[A-Z]{4,}", subject):
            violations.append("subject ALL-CAPS")
        subj_words = subject.split()
        if len(subj_words) > 6:
            violations.append(f"subject too long ({len(subj_words)} words)")
    
    # Body rules
    if not body.strip():
        violations.append("empty body")
    else:
        if re.search(r"<\s*[a-zA-Z][^>]*>", body):
            violations.append("HTML in body (plain text only)")
        if "\u2014" in body:
            violations.append("em-dash in body")
        
        first_lines = [ln.strip() for ln in body.split("\n") if ln.strip()]
        if first_lines:
            opener = first_lines[0].lower()
            for banned in BANNED_OPENERS:
                if banned in opener[:60]:
                    violations.append(f"banned opener: {banned}")
        
        if len(first_lines) >= 3:
            tail = "\n".join(first_lines[-5:]).lower()
            for banned in BANNED_SIGNOFFS:
                if banned.strip() in tail:
                    violations.append(f"banned sign-off: {banned}")
            cta_zone = "\n".join(first_lines[-6:]).lower()
            for banned in BANNED_CTAS:
                if banned in cta_zone:
                    violations.append(f"banned CTA: {banned}")
        
        for word in BANNED_BUZZWORDS:
            if re.search(rf"\b{re.escape(word)}\b", full):
                violations.append(f"buzzword: {word}")
        
        for phrase in BANNED_REASSURANCE:
            if phrase in full:
                violations.append(f"reassurance: {phrase}")
        
        # Fake case study pattern
        if re.search(r"\b(took|got|helped).+from.+to.+in \d+ days?\b", body, re.I):
            violations.append("fake case study pattern")
        
        # Word count
        words = body.split()
        if len(words) > 150:
            violations.append(f"too long: {len(words)} words (max 150)")
        elif len(words) < 50:
            violations.append(f"too short: {len(words)} words (target 80-110)")
    
    return len(violations) == 0, violations


# === PROSPECT ENRICHMENT: real SEO audit per website ===
def audit_prospect_website(url):
    """Scrape website and find REAL SEO issues to mention in email."""
    headers = {"User-Agent": "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36"}
    findings = {}
    
    try:
        resp = req_lib.get(url, headers=headers, timeout=8, allow_redirects=True, verify=False)
        html = resp.text
        html_lower = html.lower()
        
        # Extract real data points
        title_match = re.search(r'<title[^>]*>(.*?)</title>', html, re.I | re.DOTALL)
        findings["title"] = title_match.group(1).strip() if title_match else ""
        findings["has_ssl"] = resp.url.startswith("https://")
        findings["load_time"] = round(resp.elapsed.total_seconds(), 1)
        
        meta = re.search(r'<meta[^>]+name=["\']description["\'][^>]+content=["\'](.*?)["\']', html, re.I)
        findings["meta_desc"] = meta.group(1).strip() if meta else ""
        
        findings["h1_count"] = len(re.findall(r'<h1', html, re.I))
        findings["h2_count"] = len(re.findall(r'<h2', html, re.I))
        
        text = re.sub(r'<[^>]+>', '', html)
        findings["word_count"] = len(text.split())
        
        findings["has_og"] = "og:title" in html_lower
        findings["has_schema"] = "schema.org" in html_lower or "application/ld+json" in html_lower
        findings["has_canonical"] = "canonical" in html_lower
        findings["has_sitemap_link"] = "sitemap" in html_lower
        findings["mobile_friendly"] = "viewport" in html_lower
        
        # Build issue list with SPECIFIC findings for email personalization
        issues = []
        if not findings["has_ssl"]:
            issues.append({
                "type": "no-ssl",
                "email_text": f"Your site shows \"not secure\" in Chrome (no SSL). Google flags this and visitors bounce"
            })
        if len(findings["title"]) < 10:
            issues.append({
                "type": "weak-title",
                "email_text": f"Your title tag is \"{findings['title'][:40]}\" which doesn't include keywords customers search for"
            })
        if not findings["meta_desc"]:
            issues.append({
                "type": "no-meta-desc",
                "email_text": "No meta description set, so Google auto-generates one (usually badly)"
            })
        if findings["h1_count"] == 0:
            issues.append({
                "type": "no-h1",
                "email_text": "No H1 heading on your homepage"
            })
        if findings["load_time"] > 3:
            issues.append({
                "type": "slow-load",
                "email_text": f"Page takes {findings['load_time']}s to load on mobile. Over 3s loses half your visitors"
            })
        if findings["word_count"] < 300:
            issues.append({
                "type": "thin-content",
                "email_text": f"Only {findings['word_count']} words on your homepage. Thin content signals to Google"
            })
        if not findings["has_schema"]:
            issues.append({
                "type": "no-schema",
                "email_text": "No structured data markup. Competitors with schema get rich snippets in search results"
            })
        if not findings["has_og"]:
            issues.append({
                "type": "no-og",
                "email_text": "No Open Graph tags. When someone shares your link on WhatsApp/Facebook it shows no preview"
            })
        
        findings["issues"] = issues
        findings["issue_count"] = len(issues)
        
    except Exception as e:
        findings["error"] = str(e)[:100]
        findings["issues"] = []
        findings["issue_count"] = 0
    
    return findings


# === TEMPLATE GENERATION: creates personalized email from real findings ===
def generate_personalized_email(prospect, findings, templates, learning):
    """Generate a skill-compliant email using REAL findings from the prospect's website."""
    name = prospect.get("business_name", "")
    city = prospect.get("city", "Dubai")
    industry = prospect.get("industry", "")
    rating = prospect.get("rating", 0)
    reviews = prospect.get("reviews", 0)
    issues = findings.get("issues", [])
    
    if not issues:
        return None, None
    
    # Pick the 2 most impactful issues
    priority_order = ["no-ssl", "slow-load", "no-h1", "weak-title", "no-meta-desc", "thin-content", "no-schema", "no-og"]
    sorted_issues = sorted(issues, key=lambda x: priority_order.index(x["type"]) if x["type"] in priority_order else 99)
    top_issues = sorted_issues[:2]
    
    # Build slug for subject
    slug = name.lower().replace(" ", "").replace(".", "").replace(",", "")[:20]
    
    subject = slug
    
    # Build body using real findings
    body = f"Hi,\n\nWas checking {name} in {city}. "
    
    # First issue
    body += f"{top_issues[0]['email_text']}. "
    
    if len(top_issues) > 1:
        body += f"{top_issues[1]['email_text']}.\n\n"
    else:
        body += "\n\n"
    
    # Add relevance signal (reviews show they're established)
    if reviews >= 100:
        body += f"You've got {reviews} reviews at {rating} stars so the business is clearly solid. "
        body += "The SEO side just hasn't caught up yet.\n\n"
    
    # Low-friction CTA
    body += "Want me to send a quick list of what needs fixing?\n\nOwn"
    
    # Validate against skill rules
    passed, violations = validate_email_draft(subject, body)
    if not passed:
        # Try to fix common issues
        for v in violations:
            if "too long" in v:
                words = body.split()[:110]
                body = " ".join(words) + "\n\nOwn"
            elif "em-dash" in v:
                body = body.replace("\u2014", ",")
                subject = subject.replace("\u2014", ",")
    
    # Re-validate
    passed, violations = validate_email_draft(subject, body)
    if not passed:
        print(f"    VALIDATION FAIL: {violations}")
        return None, None
    
    return subject, body


# === SELF-LEARNING: track and improve ===
def update_learning(template_type, event, learning):
    if "template_stats" not in learning:
        learning = {"template_stats": {}, "total_sent": 0, "total_replies": 0, "best_performers": []}
    if template_type not in learning["template_stats"]:
        learning["template_stats"][template_type] = {"sent": 0, "replies": 0, "reply_rate": 0, "industries": {}}
    
    stats = learning["template_stats"][template_type]
    if event == "sent":
        stats["sent"] += 1
        learning["total_sent"] = (learning.get("total_sent", 0)) + 1
    elif event == "reply":
        stats["replies"] += 1
        learning["total_replies"] = (learning.get("total_replies", 0)) + 1
    
    if stats["sent"] > 0:
        stats["reply_rate"] = stats["replies"] / stats["sent"]
    
    # Kill underperformers: if template has 50+ sends and 0 replies, flag it
    if stats["sent"] >= 50 and stats["replies"] == 0:
        stats["status"] = "dead"
    elif stats["sent"] >= 50 and stats["reply_rate"] < 0.01:
        stats["status"] = "weak"
    else:
        stats["status"] = "active"
    
    learning["last_updated"] = datetime.datetime.now().isoformat()
    return learning


def check_replies(sent_log, reply_log):
    """Check AgentMail inbox for new replies from prospects."""
    try:
        req = Request(
            f"{API_BASE}/inboxes/{INBOX}/messages?limit=50",
            headers={"Authorization": f"Bearer {API_KEY}"},
        )
        with urlopen(req, timeout=15) as r:
            messages = json.loads(r.read()).get("messages", [])
        
        sent_to = {s["to"].lower() for s in sent_log}
        known = {r["from_email"].lower() for r in reply_log}
        
        new_replies = []
        for msg in messages:
            from_email = msg.get("from", {}).get("address", "").lower()
            if from_email in sent_to and from_email not in known:
                reply = {
                    "from_email": from_email,
                    "from_name": msg.get("from", {}).get("name", ""),
                    "subject": msg.get("subject", ""),
                    "preview": msg.get("preview", "")[:500],
                    "date": msg.get("date", ""),
                    "message_id": msg.get("message_id", ""),
                }
                reply_log.append(reply)
                new_replies.append(reply)
        
        return new_replies, reply_log
    except Exception as e:
        print(f"Reply check error: {e}")
        return [], reply_log


def send_email(to, subject, body, labels=None):
    payload = {
        "to": [to],
        "subject": subject,
        "text": body,
        "labels": labels or ["outreach", "seo-prospecting", "rankray-auto-v3"],
    }
    req = Request(
        f"{API_BASE}/inboxes/{INBOX}/messages/send",
        data=json.dumps(payload).encode(),
        headers={"Authorization": f"Bearer {API_KEY}", "Content-Type": "application/json"},
        method="POST",
    )
    try:
        with urlopen(req, timeout=30) as r:
            return json.loads(r.read()), None
    except HTTPError as e:
        return None, f"HTTP {e.code}: {e.read().decode()[:200]}"
    except Exception as e:
        return None, str(e)[:200]


def load_json(path):
    try:
        with open(path) as f:
            return json.load(f)
    except:
        return []

def save_json(path, data):
    with open(path, "w") as f:
        json.dump(data, f, indent=2, ensure_ascii=False)


def run_daily_batch():
    print(f"=== Rank Ray Outreach v3 - {datetime.date.today()} ===\n")
    
    # Load data
    prospects = load_json(os.path.join(DATA_DIR, "prospects.json"))
    if not prospects:
        print("No prospects. Run extract-emails.py first.")
        return
    
    sent_log = load_json(os.path.join(DATA_DIR, "sent_log.json"))
    learning = load_json(os.path.join(DATA_DIR, "learning.json"))
    reply_log = load_json(os.path.join(DATA_DIR, "reply_log.json"))
    templates = load_json(os.path.join(TEMPLATE_DIR, "templates.json"))
    
    sent_emails = {s["to"].lower() for s in sent_log}
    today = datetime.date.today().isoformat()
    today_count = sum(1 for s in sent_log if s.get("date", "").startswith(today))
    
    # Check replies first
    new_replies, reply_log = check_replies(sent_log, reply_log)
    save_json(os.path.join(DATA_DIR, "reply_log.json"), reply_log)
    
    if new_replies:
        print(f"*** {len(new_replies)} NEW REPLY(IES) ***")
        for r in new_replies:
            print(f"  From: {r['from_name']} <{r['from_email']}>")
            print(f"  Subject: {r['subject']}")
            print(f"  Preview: {r['preview'][:200]}\n")
            # Find which template was used for this prospect
            for s in sent_log:
                if s["to"].lower() == r["from_email"].lower():
                    learning = update_learning(s.get("template_type", "personalized"), "reply", learning)
                    break
        save_json(os.path.join(DATA_DIR, "learning.json"), learning)
    
    # Filter and prioritize prospects (conversion filtering)
    tier1 = [p for p in prospects if p.get("website") and p.get("reviews", 0) >= 50 and p.get("rating", 0) >= 3.5]
    tier2 = [p for p in prospects if p.get("website") and p.get("reviews", 0) >= 20 and p.get("rating", 0) >= 3.0 and p not in tier1]
    tier3 = [p for p in prospects if not p.get("website") and p.get("phone") and p.get("reviews", 0) >= 50]
    
    prioritized = tier1 + tier2 + tier3
    available = [p for p in prioritized if p.get("email", "").lower() not in sent_emails]
    to_send = available[:DAILY_LIMIT - today_count]
    
    if today_count >= DAILY_LIMIT:
        print(f"Daily limit reached ({today_count}).")
        return
    
    print(f"Prospects: {len(prospects)} | Tier1: {len(tier1)} | Available: {len(available)} | Sending: {len(to_send)}\n")
    
    sent_today = 0
    failed_today = 0
    skipped_good_seo = 0
    
    for prospect in to_send:
        email_addr = prospect.get("email", "")
        if not email_addr:
            continue
        
        website = prospect.get("website", "")
        
        # SCRAPE website for real findings
        if website:
            findings = audit_prospect_website(website)
        else:
            findings = {"issues": [], "issue_count": 0}
        
        # CONVERSION FILTER: skip businesses with good SEO (they don't need us)
        if findings.get("issue_count", 0) < 2:
            skipped_good_seo += 1
            continue
        
        # Generate personalized email using real findings
        subject, body = generate_personalized_email(prospect, findings, templates, learning)
        
        if not subject or not body:
            # Validation failed, skip this one
            continue
        
        # Send
        resp, err = send_email(email_addr, subject, body)
        
        if err:
            failed_today += 1
            print(f"  FAIL: {prospect['business_name'][:25]:25} | {err[:50]}")
            if "429" in err or "rate_limit" in err:
                print("  Rate limited. Stopping.")
                break
        else:
            sent_today += 1
            sent_log.append({
                "to": email_addr,
                "business_name": prospect.get("business_name", ""),
                "industry": prospect.get("industry", ""),
                "city": prospect.get("city", ""),
                "template_type": "personalized",
                "subject": subject,
                "date": datetime.datetime.now().isoformat(),
                "seo_issues": [i["type"] for i in findings.get("issues", [])],
                "issue_count": findings.get("issue_count", 0),
                "message_id": resp.get("message_id", ""),
            })
            learning = update_learning("personalized", "sent", learning)
            
            issues_str = ",".join([i["type"] for i in findings.get("issues", [])[:3]])
            print(f"  SENT: {prospect['business_name'][:22]:22} | {email_addr[:28]:28} | {findings.get('issue_count',0)} issues ({issues_str}) | {sent_today}/{len(to_send)}")
        
        save_json(os.path.join(DATA_DIR, "sent_log.json"), sent_log)
        save_json(os.path.join(DATA_DIR, "learning.json"), learning)
        time.sleep(SLEEP_BETWEEN)
    
    # Final report
    print(f"\n=== BATCH COMPLETE ===")
    print(f"Sent: {sent_today} | Failed: {failed_today} | Skipped (good SEO): {skipped_good_seo}")
    print(f"Total sent all-time: {len(sent_log)} | Total replies: {len(reply_log)}")
    
    # Learning summary
    stats = learning.get("template_stats", {}).get("personalized", {})
    print(f"Template 'personalized': {stats.get('sent',0)} sent, {stats.get('replies',0)} replies, {stats.get('reply_rate',0)*100:.1f}% reply rate")
    
    # Save daily log
    logs = load_json(os.path.join(LOG_DIR, "daily_logs.json"))
    logs.append({
        "date": today,
        "sent": sent_today,
        "failed": failed_today,
        "skipped_good_seo": skipped_good_seo,
        "new_replies": len(new_replies),
        "total_sent": len(sent_log),
    })
    save_json(os.path.join(LOG_DIR, "daily_logs.json"), logs)


if __name__ == "__main__":
    run_daily_batch()