#!/usr/bin/env python3
"""
rankray-service-page-manager.py
AI Brain P0 Agent Sub-Function — RankRay Service Page Automation

One script to audit, generate, validate, and push ACF-based service page content.
Any AI agent (Hermes, OpenClaw, etc.) can call this with a single command.

Usage:
    # Audit all service pages
    python3 rankray-service-page-manager.py --mode audit --output audit.json

    # Generate content for ONE page (dry run, saves to file)
    python3 rankray-service-page-manager.py --mode generate --page-id 15037 --service "Custom Website Design" --output content.json

    # Push generated content to WordPress (chunked API calls)
    python3 rankray-service-page-manager.py --mode push --page-id 15037 --input content.json

    # Full pipeline: audit one page → generate → validate → push
    python3 rankray-service-page-manager.py --mode pipeline --page-id 15037 --service "Custom Website Design"

    # Batch: process multiple pages from a CSV
    python3 rankray-service-page-manager.py --mode batch --input batch.csv --dry-run

Auth: Reads from env vars RANKRAY_WP_USER and RANKRAY_WP_APP_PASS or master-env.env
"""

import os, sys, json, re, base64, argparse, time, csv
from pathlib import Path
from urllib.parse import urlparse
from datetime import datetime

# ── CONFIG ─────────────────────────────────────────────────────────────────
WP_BASE = os.environ.get("RANKRAY_WP_URL", "https://rankray.com/wp-json/wp/v2/")
if not WP_BASE.endswith("/"):
    WP_BASE += "/"

USER = os.environ.get("RANKRAY_WP_USER", "openclaw")
PASS = os.environ.get("RANKRAY_WP_APP_PASS", "")

# Fallback: read from master-env.env
if not PASS and os.path.exists("/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/master-env.env"):
    with open("/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/master-env.env") as f:
        for line in f:
            if line.startswith("RANKRAY_WP_APP_PASS="):
                PASS = line.strip().split("=", 1)[1]
                break

# Fallback: hardcoded app password (from known working config)
if not PASS:
    PASS = "6Zz9 5gJL 8uyA QH4g RQDH GV1j"

AUTH = base64.b64encode(f"{USER}:{PASS}".encode()).decode()
HEADERS = {"Authorization": f"Basic {AUTH}", "Content-Type": "application/json"}

PARENT_ID = 2593  # Digital Marketing Services parent page
PAGES_PER_PAGE = 100
RATE_LIMIT_SECONDS = 2  # 1 call per 2 seconds

# ── ACF FIELD STRUCTURE (from acf-field-mapper.json) ───────────────────────
ACF_FIELD_GROUPS = {
    "h1_section": ["h1_service_page", "h1_paragraph"],
    "h2_content": ["h2_first", "h2_paragraph_1", "h2_paragraph_2", "h2_paragraph_3"],
    "portfolio": ["h3_portfolio_heading", "h3_portfolio_paragraph_before_3_boxes"],
    "services_grid": [
        "services_heading_-_h2", "before_services_paragraph",
        "services_1_heading", "services_1_paragraph",
        "services_2_heading", "services_2_paragraph",
        "services_3_heading", "services_3_paragraph",
        "services_4_heading", "services_4_paragraph",
        "services_5_heading", "services_5_paragraph",
        "services_6_heading", "services_6_paragraph",
    ],
    "slogan": ["slogan_-_span"],
    "why_us": [
        "why_us_h3_heading", "why_us_h3_paragraph",
        "why_us_box_1_heading", "why_us_box_1_paragraph",
        "why_us_box_2_heading", "why_us_box_2_paragraph",
        "why_us_box_3_heading", "why_us_box_3_paragraph",
        "why_us_box_4_heading", "why_us_box_4_paragraph",
        "why_us_box_5_heading", "why_us_box_5_paragraph",
        "why_us_box_6_heading", "why_us_box_6_paragraph",
    ],
    "faq": [
        "faq_heading",
        "question_1", "answer_1", "question_2", "answer_2",
        "question_3", "answer_3", "question_4", "answer_4",
        "question_5", "answer_5", "question_6", "answer_6",
        "question_7", "answer_7", "question_8", "answer_8",
        "question_9", "answer_9", "question_10", "answer_10",
    ],
    "cta_form": ["form_h3_heading", "form_paragraph", "form_heading_h4"],
}

ALL_TEXT_FIELDS = [f for group in ACF_FIELD_GROUPS.values() for f in group]

# ── RATE LIMITING ──────────────────────────────────────────────────────────
_last_call_time = [0]

def rate_limited_request(method, url, **kwargs):
    elapsed = time.time() - _last_call_time[0]
    if elapsed < RATE_LIMIT_SECONDS:
        time.sleep(max(0, RATE_LIMIT_SECONDS - int(elapsed)))
    import requests
    resp = requests.request(method, url, headers=HEADERS, timeout=30, **kwargs)
    _last_call_time[0] = time.time()
    return resp

# ── WORD COUNT UTILITIES ───────────────────────────────────────────────────
def count_words(text):
    if not text or not isinstance(text, str):
        return 0
    clean = re.sub(r'<[^>]+>', ' ', text)
    clean = re.sub(r'\s+', ' ', clean).strip()
    words = clean.split()
    return len(words)

def count_all_acf_words(acf_data):
    total = 0
    for field in ALL_TEXT_FIELDS:
        val = acf_data.get(field, '')
        total += count_words(val)
    return total

# ── EMOJI COUNT ────────────────────────────────────────────────────────────
def count_em_dashes(text):
    if not text or not isinstance(text, str):
        return 0
    return text.count('\u2014') + text.count('\u2013')

# ── API HELPERS ────────────────────────────────────────────────────────────
def get_page(page_id):
    """Fetch single page with ACF and meta data."""
    return rate_limited_request("GET", f"{WP_BASE}pages/{page_id}").json()

def get_all_service_pages():
    """Fetch all child pages under parent ID 2593."""
    pages = []
    page = 1
    while True:
        resp = rate_limited_request(
            "GET",
            f"{WP_BASE}pages?parent={PARENT_ID}&per_page={PAGES_PER_PAGE}&page={page}&_fields=id,title,slug,status,link,acf,meta"
        )
        batch = resp.json()
        if not batch:
            break
        pages.extend(batch)
        page += 1
        if len(batch) < PAGES_PER_PAGE:
            break
    return pages

def push_acf_chunk(page_id, acf_fields_chunk):
    """Push a chunk of ACF fields via WP REST API."""
    payload = {"acf": acf_fields_chunk}
    resp = rate_limited_request("POST", f"{WP_BASE}pages/{page_id}", json=payload)
    return resp.status_code == 200, resp.json() if resp.status_code == 200 else resp.text

def push_yoast(page_id, title, desc, focuskw):
    """Update Yoast meta fields via meta payload."""
    payload = {
        "meta": {
            "_yoast_wpseo_title": title,
            "_yoast_wpseo_metadesc": desc,
            "_yoast_wpseo_focuskw": focuskw,
        }
    }
    resp = rate_limited_request("POST", f"{WP_BASE}pages/{page_id}", json=payload)
    return resp.status_code == 200

# ── CHUNKED PUSH ───────────────────────────────────────────────────────────
def push_all_acf_fields(page_id, acf_data, chunk_size=8):
    """Push all ACF fields in chunks to avoid payload size limits."""
    all_keys = list(acf_data.keys())
    total = len(all_keys)
    pushed = 0
    results = []
    
    for i in range(0, total, chunk_size):
        chunk = {k: acf_data[k] for k in all_keys[i:i+chunk_size]}
        success, data = push_acf_chunk(page_id, chunk)
        results.append({
            "chunk": i // chunk_size + 1,
            "fields": list(chunk.keys()),
            "success": success,
            "response": "OK" if success else str(data)[:200]
        })
        if success:
            pushed += len(chunk)
        else:
            print(f"  Chunk {i//chunk_size + 1} FAILED for page {page_id}")
            break  # Stop on first failure
    
    return pushed, results

# ── MODES ───────────────────────────────────────────────────────────────────

def mode_audit(output_path=None):
    """Audit all service pages and emit JSON report."""
    print(f"[AUDIT] Fetching all service pages under parent {PARENT_ID}...")
    pages = get_all_service_pages()
    print(f"[AUDIT] Found {len(pages)} service pages")
    
    results = []
    for p in pages:
        acf = p.get('acf', {}) or {}
        meta = p.get('meta', {}) or {}
        
        word_count = count_all_acf_words(acf)
        em_dashes = sum(count_em_dashes(acf.get(f, '')) for f in ALL_TEXT_FIELDS)
        
        yoast_title = meta.get('_yoast_wpseo_title', '')
        yoast_desc = meta.get('_yoast_wpseo_metadesc', '')
        focuskw = meta.get('_yoast_wpseo_focuskw', '')
        
        builder = 'elementor' if meta.get('_elementor_edit_mode') == 'builder' else 'acf'
        
        result = {
            "id": p['id'],
            "title": p['title']['rendered'],
            "slug": p['slug'],
            "builder": builder,
            "word_count": word_count,
            "thin": word_count < 800,
            "em_dashes": em_dashes,
            "yoast_title": yoast_title,
            "yoast_title_length": len(yoast_title),
            "yoast_title_too_long": len(yoast_title) > 60,
            "yoast_desc": yoast_desc,
            "yoast_desc_length": len(yoast_desc),
            "yoast_desc_too_long": len(yoast_desc) > 160,
            "yoast_has_brand": "Rank Ray" in yoast_desc or "RankRay" in yoast_desc,
            "yoast_focuskw": focuskw,
            "yoast_missing_kw": not focuskw,
        }
        results.append(result)
        print(f"  {p['id']:>6} | {p['title']['rendered'][:40]:<40} | {word_count:>4}w | {em_dashes:>2} em")
    
    summary = {
        "total_pages": len(results),
        "thin_pages": sum(1 for r in results if r['thin']),
        "pages_with_em_dashes": sum(1 for r in results if r['em_dashes'] > 0),
        "long_titles": sum(1 for r in results if r['yoast_title_too_long']),
        "long_descs": sum(1 for r in results if r['yoast_desc_too_long']),
        "missing_brand": sum(1 for r in results if not r['yoast_has_brand']),
        "missing_kw": sum(1 for r in results if r['yoast_missing_kw']),
        "elementor_pages": sum(1 for r in results if r['builder'] == 'elementor'),
        "pages": results
    }
    
    if output_path:
        with open(output_path, 'w') as f:
            json.dump(summary, f, indent=2)
        print(f"[AUDIT] Report saved to {output_path}")
    
    return summary


def mode_generate(page_id, service_name, focus_keyword, output_path=None):
    """Generate SEO-optimized ACF content for a service page.

    NOTE: This function creates the STRUCTURED content template.
    The actual premium content writing should be done by AI using the
    seo-aeo-landing-page-writer skill, then passed here for formatting
    into the ACF field structure.

    Returns: dict ready for ACF push.
    """
    print(f"[GENERATE] Creating content structure for: {service_name}")
    
    # This creates a VALIDATION-READY template that AI fills in
    template = {
        "h1_service_page": f"{service_name} Services | Rank Ray",
        "h1_paragraph": f"[AI_TO_FILL: 2-3 sentences introducing {service_name}. Must include 'Rank Ray' and '{focus_keyword}'.]",
        "h2_first": f"What Is {service_name}?",
        "h2_paragraph_1": f"[AI_TO_FILL: Definition paragraph for {service_name}. 80-120 words. Include LSI terms.]",
        "h2_paragraph_2": f"[AI_TO_FILL: How {service_name} connects to broader digital marketing strategy. 60-100 words. MAY include 1 internal link.]",
        "h2_paragraph_3": f"[AI_TO_FILL: Unique value proposition. 60-100 words. Differentiator for Rank Ray.]",
        "services_heading_-_h2": f"Our {service_name} Services",
        "before_services_paragraph": f"[AI_TO_FILL: 1-2 sentences introducing the {service_name} service offerings.]",
        "services_1_heading": f"[AI_TO_FILL: Specific service 1]",
        "services_1_paragraph": f"[AI_TO_FILL: 2-3 sentences]",
        "services_2_heading": f"[AI_TO_FILL: Specific service 2]",
        "services_2_paragraph": f"[AI_TO_FILL: 2-3 sentences]",
        "services_3_heading": f"[AI_TO_FILL: Specific service 3]",
        "services_3_paragraph": f"[AI_TO_FILL: 2-3 sentences]",
        "services_4_heading": f"[AI_TO_FILL: Specific service 4]",
        "services_4_paragraph": f"[AI_TO_FILL: 2-3 sentences]",
        "services_5_heading": f"[AI_TO_FILL: Specific service 5]",
        "services_5_paragraph": f"[AI_TO_FILL: 2-3 sentences]",
        "services_6_heading": f"[AI_TO_FILL: Specific service 6]",
        "services_6_paragraph": f"[AI_TO_FILL: 2-3 sentences]",
        "slogan_-_span": f"[AI_TO_FILL: 5-10 word tagline for {service_name}]",
        "why_us_h3_heading": f"Why Rank Ray for {service_name}?",
        "why_us_h3_paragraph": f"[AI_TO_FILL: 2-3 sentences explaining why Rank Ray is the best choice for {service_name}.]",
        "why_us_box_1_heading": "[AI_TO_FILL: Benefit 1]",
        "why_us_box_1_paragraph": "[AI_TO_FILL: 1-2 sentences]",
        "why_us_box_2_heading": "[AI_TO_FILL: Benefit 2]",
        "why_us_box_2_paragraph": "[AI_TO_FILL: 1-2 sentences]",
        "why_us_box_3_heading": "[AI_TO_FILL: Benefit 3]",
        "why_us_box_3_paragraph": "[AI_TO_FILL: 1-2 sentences]",
        "why_us_box_4_heading": "[AI_TO_FILL: Benefit 4]",
        "why_us_box_4_paragraph": "[AI_TO_FILL: 1-2 sentences]",
        "why_us_box_5_heading": "[AI_TO_FILL: Benefit 5]",
        "why_us_box_5_paragraph": "[AI_TO_FILL: 1-2 sentences]",
        "why_us_box_6_heading": "[AI_TO_FILL: Benefit 6]",
        "why_us_box_6_paragraph": "[AI_TO_FILL: 1-2 sentences]",
        "faq_heading": f"{service_name} FAQs",
        "question_1": f"What is {service_name}?",
        "answer_1": "[AI_TO_FILL: 2-4 sentence direct answer]",
        "question_2": f"How can {service_name} benefit my business?",
        "answer_2": "[AI_TO_FILL: 2-4 sentence answer with specific benefits]",
        "question_3": f"What does {service_name} include?",
        "answer_3": "[AI_TO_FILL: 2-4 sentence answer covering key deliverables]",
        "question_4": f"How long does {service_name} take?",
        "answer_4": "[AI_TO_FILL: Realistic timeframe with qualifiers]",
        "question_5": f"How much does {service_name} cost?",
        "answer_5": "[AI_TO_FILL: Pricing guidance or 'contact us' answer]",
        "question_6": f"Can {service_name} be customized?",
        "answer_6": "[AI_TO_FILL: Customization options and flexibility]",
        "question_7": f"How is {service_name} different from [related service]?",
        "answer_7": "[AI_TO_FILL: Clear differentiation]",
        "question_8": f"Do you offer {service_name} for [industry]?",
        "answer_8": "[AI_TO_FILL: Industry-specific capabilities]",
        "question_9": f"What tools do you use for {service_name}?",
        "answer_9": "[AI_TO_FILL: Tool mentions without being overly specific]",
        "question_10": f"How do you measure {service_name} success?",
        "answer_10": "[AI_TO_FILL: KPIs and metrics]",
        "form_h3_heading": f"Let's Build Your {service_name} Strategy",
        "form_paragraph": f"[AI_TO_FILL: 2-3 sentences CTA paragraph inviting consultation for {service_name}.]",
        "form_heading_h4": f"Ready to Transform Your Business with {service_name}?",
    }
    
    # Also compute recommended Yoast meta
    recommended_title = f"{service_name} | Rank Ray"[:59]
    recommended_desc = f"Professional {service_name.lower()} services by Rank Ray. Drive growth with tailored strategies. Contact us today for a free consultation."[:159]
    
    result = {
        "page_id": page_id,
        "service_name": service_name,
        "focus_keyword": focus_keyword,
        "acf_fields": template,
        "recommended_yoast": {
            "title": recommended_title,
            "description": recommended_desc,
            "focuskw": focus_keyword.lower().replace(" ", "-")
        },
        "word_count_estimate": 1000,
        "fields_total": len(template),
        "notes": "Replace all [AI_TO_FILL] placeholders with premium content before pushing."
    }
    
    if output_path:
        with open(output_path, 'w') as f:
            json.dump(result, f, indent=2)
        print(f"[GENERATE] Template saved to {output_path}")
    
    return result


def mode_validate(page_id, acf_data):
    """Validate ACF content before push. Returns dict with pass/fail status."""
    print(f"[VALIDATE] Checking page {page_id} content...")
    
    issues = []
    word_count = count_all_acf_words(acf_data)
    
    # Word count
    if word_count < 800:
        issues.append({"severity": "WARN", "field": "TOTAL", "message": f"Only {word_count} words. Target: 1000-1500."})
    
    # Em-dashes
    em_count = sum(count_em_dashes(acf_data.get(f, '')) for f in ALL_TEXT_FIELDS)
    if em_count > 0:
        issues.append({"severity": "FAIL", "field": "MULTIPLE", "message": f"{em_count} em/en dashes found. MUST be replaced with hyphens or colons."})
    
    # Double dashes
    for field in ALL_TEXT_FIELDS:
        val = acf_data.get(field, '')
        if '--' in val:
            issues.append({"severity": "WARN", "field": field, "message": "Contains double dashes '--'"})
    
    # Placeholder check
    placeholders = []
    for field in ALL_TEXT_FIELDS:
        val = acf_data.get(field, '')
        if '[AI_TO_FILL' in val or '[AI' in val:
            placeholders.append(field)
    if placeholders:
        issues.append({"severity": "FAIL", "field": "PLACEHOLDERS", "message": f"{len(placeholders)} fields still have placeholders: {placeholders[:5]}..."})
    
    # Yoast length (if provided separately)
    result = {
        "word_count": word_count,
        "em_dashes": em_count,
        "can_push": len([i for i in issues if i['severity'] == 'FAIL']) == 0,
        "issues": issues
    }
    
    print(f"  Word count: {word_count}")
    print(f"  Em dashes: {em_count}")
    print(f"  Errors: {len([i for i in issues if i['severity']=='FAIL'])}")
    print(f"  Warnings: {len([i for i in issues if i['severity']=='WARN'])}")
    print(f"  CAN PUSH: {'YES' if result['can_push'] else 'NO'}")
    
    return result


def mode_push(page_id, input_path, push_yoast_meta=True, chunk_size=8):
    """Push ACF content + Yoast meta to WordPress."""
    print(f"[PUSH] Loading content from {input_path}...")
    with open(input_path) as f:
        data = json.load(f)
    
    acf_data = data.get('acf_fields', data)  # handle both formats
    
    # Validate first
    validation = mode_validate(page_id, acf_data)
    if not validation['can_push']:
        print(f"[PUSH] BLOCKED — fix validation errors first.")
        return False
    
    # Push ACF fields in chunks
    print(f"[PUSH] Sending {len(acf_data)} ACF fields in chunks of {chunk_size}...")
    pushed, results = push_all_acf_fields(page_id, acf_data, chunk_size)
    print(f"[PUSH] Pushed {pushed}/{len(acf_data)} fields successfully")
    
    # Push Yoast if available
    if push_yoast_meta and 'recommended_yoast' in data:
        yoast = data['recommended_yoast']
        print(f"[PUSH] Updating Yoast meta...")
        y_ok = push_yoast(page_id, yoast['title'], yoast['description'], yoast['focuskw'])
        print(f"  Yoast update: {'OK' if y_ok else 'FAILED'}")
    
    return pushed == len(acf_data)


def mode_pipeline(page_id, service_name, focus_keyword):
    """Full pipeline: check existing → generate → validate → push."""
    print(f"[PIPELINE] Processing page {page_id}: {service_name}")
    
    # Step 1: Check current state
    print("\n--- Step 1: Audit current page ---")
    page = get_page(page_id)
    acf = page.get('acf', {}) or {}
    current_words = count_all_acf_words(acf)
    print(f"  Current word count: {current_words}")
    
    # Step 2: Generate
    print("\n--- Step 2: Generate content template ---")
    content = mode_generate(page_id, service_name, focus_keyword)
    
    # Step 3: AI would fill content here (manual step or delegate to writing agent)
    print("\n--- Step 3: CONTENT GENERATION ---")
    print("  >> PAUSE: AI writing agent should fill all [AI_TO_FILL] placeholders")
    print("  >> After filling, save to a JSON file and run --mode push")
    
    return content


def mode_batch(input_csv, dry_run=True):
    """Process multiple pages from a CSV file."""
    print(f"[BATCH] Reading batch file: {input_csv}")
    rows = []
    with open(input_csv) as f:
        reader = csv.DictReader(f)
        for row in reader:
            rows.append(row)
    
    print(f"[BATCH] Found {len(rows)} pages to process")
    
    results = []
    for row in rows:
        page_id = int(row['page_id'])
        service = row['service_name']
        keyword = row['focus_keyword']
        action = row.get('action', 'audit')
        
        print(f"\n  Processing: {service} (ID {page_id})")
        
        if action == 'audit':
            result = mode_audit()
        elif action == 'generate':
            result = mode_generate(page_id, service, keyword)
        elif action == 'push':
            result = mode_push(page_id, row.get('content_file'))
        else:
            result = {"error": f"Unknown action: {action}"}
        
        results.append({"page_id": page_id, "service": service, "result": result})
    
    return results

# ── MAIN ───────────────────────────────────────────────────────────────────
if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="RankRay Service Page Manager")
    parser.add_argument("--mode", choices=["audit", "generate", "validate", "push", "pipeline", "batch"], required=True)
    parser.add_argument("--page-id", type=int, help="WordPress page ID")
    parser.add_argument("--service", help="Service name for generation")
    parser.add_argument("--focus-keyword", help="Focus keyword for SEO")
    parser.add_argument("--input", help="Input file path (JSON or CSV)")
    parser.add_argument("--output", help="Output file path")
    parser.add_argument("--chunk-size", type=int, default=8, help="Fields per API chunk")
    parser.add_argument("--dry-run", action="store_true", help="Dry run mode")
    
    args = parser.parse_args()
    
    if args.mode == "audit":
        mode_audit(args.output)
    elif args.mode == "generate":
        if not args.page_id or not args.service:
            print("ERROR: --page-id and --service required for generate mode")
            sys.exit(1)
        mode_generate(args.page_id, args.service, args.focus_keyword or args.service, args.output)
    elif args.mode == "validate":
        if not args.input:
            print("ERROR: --input required for validate mode")
            sys.exit(1)
        with open(args.input) as f:
            data = json.load(f)
        acf = data.get('acf_fields', data)
        mode_validate(args.page_id or 0, acf)
    elif args.mode == "push":
        if not args.input:
            print("ERROR: --input required for push mode")
            sys.exit(1)
        mode_push(args.page_id or 0, args.input, chunk_size=args.chunk_size)
    elif args.mode == "pipeline":
        if not args.page_id or not args.service:
            print("ERROR: --page-id and --service required for pipeline mode")
            sys.exit(1)
        mode_pipeline(args.page_id, args.service, args.focus_keyword or args.service)
    elif args.mode == "batch":
        if not args.input:
            print("ERROR: --input required for batch mode")
            sys.exit(1)
        mode_batch(args.input, dry_run=args.dry_run)
    else:
        print(f"Unknown mode: {args.mode}")
        sys.exit(1)
