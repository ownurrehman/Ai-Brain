import requests
import re
import time
import sys
import os
from base64 import b64encode
from datetime import datetime

# Load credentials from master-env.env (never hardcode passwords)
def load_env(env_path):
    env = {}
    if os.path.exists(env_path):
        with open(env_path) as f:
            for line in f:
                line = line.strip()
                if line and not line.startswith('#') and '=' in line:
                    key, _, value = line.partition('=')
                    env[key.strip()] = value.strip()
    return env

ENV_PATH = os.path.join(os.path.dirname(__file__), '../../master-env.env')
env = load_env(ENV_PATH)

WP_BASE = "https://rankray.com/wp-json/wp/v2/"
WP_USER = env.get("RANKRAY_WP_USER", "")
WP_PASS = env.get("RANKRAY_WP_APP_PASS", "")

if not WP_USER or not WP_PASS:
    print("ERROR: Missing RANKRAY_WP_USER or RANKRAY_WP_APP_PASS in master-env.env")
    sys.exit(1)

auth_str = b64encode(f"{WP_USER}:{WP_PASS}".encode()).decode()
headers = {
    "Authorization": f"Basic {auth_str}",
    "Content-Type": "application/json"
}

# Cluster definitions
CLUSTERS = {
    "semantic-seo": {
        "pillar": "/what-is-semantic-seo-complete-guide/",
        "service": "/digital-marketing-services/semantic-seo-services/",
        "peers": ["entity-seo-knowledge-graph-optimization", "information-gain-score-seo", "create-unique-content-google-information-gain-techniques", "originality-vs-relevance-balance-seo-content", "content-differentiation-strategy-saturated-search", "topical-map-seo-methodology", "topic-cluster-vs-keyword-silo-seo-structure", "pillar-cluster-model-topic-hubs-seo", "content-cluster-strategy-seo-step-by-step", "nlp-for-seo-natural-language-processing-optimization", "bert-mum-google-ai-models-search-queries", "entity-based-seo-modern-search-rankings", "google-knowledge-graph-optimization-brand-recognition"]
    },
    "geo-ai": {
        "pillar": "/geo-content-strategy-chatgpt-perplexity-claude/",
        "service": "/digital-marketing-services/generative-engine-optimization-geo/",
        "peers": ["how-to-rank-on-chatgpt-brand-citation-strategy", "perplexity-ai-seo-content-citation-strategy", "claude-ai-brand-visibility-anthropic-citations", "what-is-answer-engine-optimization-aeo-explained", "google-ai-overview-optimization-featured-answers", "ai-overview-optimization-guide", "the-2026-guide-to-enterprise-generative-engine-optimization-geo", "google-sge-seo-strategy-ai-powered-search", "ai-generated-search-results-source-selection-seo", "featured-snippet-to-ai-overview-evolution", "how-ai-is-changing-the-seo-world", "ais-role-in-seo", "agentic-seo-ai-driven-growth-canadian-businesses"]
    },
    "technical-seo": {
        "pillar": "/technical-seo-audit/",
        "service": "/digital-marketing-services/technical-seo/",
        "peers": ["core-web-vitals-guide", "schema-markup-structured-data-guide", "json-ld-schema-guide-structured-data-rich-results", "structured-data-testing-validation-fix-schema-errors", "site-architecture-seo-crawl-friendly-structures", "internal-linking-seo-architecture", "internal-linking-strategy-seo-link-equity-distribution", "fix-orphan-pages-recover-seo-value", "website-migration-seo-checklist-no-rankings-loss", "seo-migration-checklist", "301-redirect-mapping-guide-url-changes-seo", "cctld-vs-subdirectory-subdomain-international-seo", "hreflang-tags-guide-multilingual-seo-implementation", "international-seo-hreflang-guide", "fix-index-coverage-issues-google-search-console", "google-search-console-guide", "google-search-console-guide-master-gsc-seo", "search-console-performance-report-seo-opportunities", "seo-checklist-for-website-success", "comprehensive-seo-audit-checklist"]
    },
    "on-page-seo": {
        "pillar": "/on-page-seo-optimization/",
        "service": "/digital-marketing-services/search-engine-optimization-seo/",
        "peers": ["keyword-density-in-seo-still-matter", "keyword-research-guide", "what-is-keyword-research", "keyword-research-tools-for-seo", "content-refresh-strategy-seo", "content-refresh-strategy-update-old-posts-seo", "audit-blog-content-decay-pages-need-updating", "how-to-rank-first-on-google", "how-to-rank-your-blog-posts-on-page-one-of-google", "7-steps-to-rank-higher-on-google", "8-best-seo-practices-for-2025", "best-seo-tips-to-optimize-your-blog", "topic-authority-compound-seo-strategy"]
    },
    "content-marketing": {
        "pillar": "/seo-content-strategy-guide/",
        "service": "/digital-marketing-services/content-marketing/",
        "peers": ["content-marketing-trends-in-2023", "what-is-synergy-in-content-marketing", "5-steps-to-balance-seo-and-content-synergy", "how-to-grow-your-brand-with-content-creation", "what-is-digital-marketing", "digital-marketing-strategy-gaps-that-hurt-growth", "benefits-of-digital-marketing-strategy", "how-to-invest-in-digital-marketing", "9-techniques-for-smarter-digital-marketing-strategy", "what-are-the-top-digital-marketing-strategies", "top-digital-marketing-trends-for-2026", "competitive-analysis-in-digital-marketing", "steps-to-start-a-digital-marketing-career", "what-is-b2b-marketing", "what-is-enterprise-digital-marketing", "the-complete-guide-to-franchise-marketing"]
    },
    "off-page": {
        "pillar": "/link-building-guide/",
        "service": "/digital-marketing-services/link-building/",
        "peers": ["what-is-off-page-seo", "200-free-article-submission-sites-to-help-you-get-traffic", "best-200-profile-creation-backlinks", "free-business-directories-list-to-get-backlinks", "seo-backlink-software", "best-press-release-services"]
    },
    "b2b-saas": {
        "pillar": "/b2b-seo-guide-enterprise/",
        "service": "/digital-marketing-services/enterprise-seo/",
        "peers": ["b2b-seo-strategy-enterprise-buying-committees", "b2b-keyword-research-high-intent-convert", "long-sales-cycle-seo-content-nurture-b2b-buyers", "saas-seo-strategy-product-led-content", "free-seo-tools-saas-growth-content-flywheel", "law-firm-seo-guide"]
    },
    "local-seo": {
        "pillar": "/local-seo-complete-guide/",
        "service": "/digital-marketing-services/local-seo/",
        "peers": ["google-business-profile-optimization", "google-business-profile-optimization-local-pack-ranking", "gbp-categories-local-rankings-choose-right"]
    },
    "healthcare": {
        "pillar": "/healthcare-seo-medical-practice/",
        "service": "/digital-marketing-services/search-engine-optimization-seo/",
        "peers": ["healthcare-seo-medical-practice-optimization", "ymyl-seo-medical-content-google-quality-standards", "eeat-medical-websites-authority-signals-matter", "eeat-guide-trust-signals"]
    },
    "analytics": {
        "pillar": "/seo-analytics-ga4-looker-studio/",
        "service": "/digital-marketing-services/seo-audit-services/",
        "peers": ["looker-studio-seo-dashboard-performance-reports", "calculate-seo-roi-framework-search-returns", "calculate-organic-traffic-value-seo-roi", "seo-roi-calculation-guide", "seo-kpis-right-metrics-tracking", "seo-reporting-clients-show-real-value"]
    }
}

ANCHOR_MAP = {
    ("semantic-seo", "pillar"): "semantic SEO",
    ("semantic-seo", "service"): "semantic SEO services",
    ("geo-ai", "pillar"): "GEO content strategy",
    ("geo-ai", "service"): "Generative Engine Optimization",
    ("technical-seo", "pillar"): "technical SEO audit",
    ("technical-seo", "service"): "technical SEO services",
    ("on-page-seo", "pillar"): "on-page SEO optimization",
    ("on-page-seo", "service"): "SEO services",
    ("content-marketing", "pillar"): "SEO content strategy",
    ("content-marketing", "service"): "content marketing services",
    ("off-page", "pillar"): "link building guide",
    ("off-page", "service"): "link building services",
    ("b2b-saas", "pillar"): "B2B SEO guide",
    ("b2b-saas", "service"): "enterprise SEO services",
    ("local-seo", "pillar"): "local SEO guide",
    ("local-seo", "service"): "local SEO services",
    ("healthcare", "pillar"): "healthcare SEO",
    ("healthcare", "service"): "SEO services",
    ("analytics", "pillar"): "SEO analytics",
    ("analytics", "service"): "SEO audit services",
}

def get_cluster_for_slug(slug):
    for cluster_name, cluster in CLUSTERS.items():
        if cluster["pillar"].rstrip('/').endswith(slug):
            return cluster_name, "pillar"
        if cluster["service"].rstrip('/').endswith(slug):
            return cluster_name, "service"
        for peer in cluster["peers"]:
            if peer == slug:
                return cluster_name, "peer"
    return None, None

def count_internal_links(content):
    if not content:
        return 0, []
    links = re.findall(r'href="([^"]+)"', content)
    internal_links = [l for l in links if 'rankray.com' in l]
    return len(internal_links), links

def get_anchor_text(slug_or_cluster, link_type):
    key = (slug_or_cluster, link_type)
    return ANCHOR_MAP.get(key, slug_or_cluster.replace('-', ' ').title())

def get_post_data(slug):
    try:
        # Try posts endpoint
        resp = requests.get(f"{WP_BASE}posts", params={"slug": slug, "per_page": 1}, headers=headers, timeout=10)
        if resp.status_code == 200:
            data = resp.json()
            if data:
                return data[0]["id"], data[0].get("content", {}).get("rendered", ""), "post"
        
        # Try pages endpoint
        resp = requests.get(f"{WP_BASE}pages", params={"slug": slug, "per_page": 1}, headers=headers, timeout=10)
        if resp.status_code == 200:
            data = resp.json()
            if data:
                return data[0]["id"], data[0].get("content", {}).get("rendered", ""), "page"
        
        return None, None, None
    except Exception as e:
        print(f"Error getting post data for {slug}: {e}")
        return None, None, None

def insert_links(content, links_to_add, existing_links):
    if not content or not links_to_add:
        return content, 0
    
    paragraphs = re.findall(r'(?s)<p[^>]*>(.*?)</p>', content)
    if not paragraphs:
        return content, 0
    
    modified = content
    inserted = 0
    used = list(existing_links)
    
    # First paragraph
    if paragraphs and links_to_add:
        p = paragraphs[0]
        sentences = re.split(r'(?<=[.!?])\s+', p.strip())
        if len(sentences) > 1:
            link = links_to_add.pop(0)
            if link["url"] not in used:
                pt = len(sentences[0])
                new_p = p[:pt] + f' <a href="{link["url"]}">{link["anchor"]}</a>' + p[pt:]
                modified = modified.replace(p, new_p, 1)
                used.append(link["url"])
                inserted += 1
    
    # Last paragraph
    if paragraphs and links_to_add and len(paragraphs) > 1:
        p = paragraphs[-1]
        link = links_to_add.pop(0)
        if link["url"] not in used:
            sentences = re.split(r'(?<=[.!?])\s+', p.strip())
            if sentences:
                pt = len(sentences[0])
                new_p = p[:pt] + f' <a href="{link["url"]}">{link["anchor"]}</a>' + p[pt:]
                modified = modified.replace(p, new_p, 1)
                used.append(link["url"])
                inserted += 1
    
    # Middle paragraphs
    if links_to_add and len(paragraphs) > 2:
        for i in range(1, min(len(paragraphs)-1, 3)):
            if not links_to_add:
                break
            p = paragraphs[i]
            link = links_to_add.pop(0)
            if link["url"] not in used:
                sentences = re.split(r'(?<=[.!?])\s+', p.strip())
                if sentences:
                    mid = len(sentences) // 2
                    pt = sum(len(s) + 1 for s in sentences[:mid])
                    new_p = p[:pt] + f' <a href="{link["url"]}">{link["anchor"]}</a>' + p[pt:]
                    modified = modified.replace(p, new_p, 1)
                    used.append(link["url"])
                    inserted += 1
    
    return modified, inserted

def process_post(url):
    slug = url.replace('https://rankray.com/', '').rstrip('/')
    
    cluster_name, role = get_cluster_for_slug(slug)
    if not cluster_name:
        return {"url": url, "status": "NO_CLUSTER", "links_added": 0}
    
    post_id, content, post_type = get_post_data(slug)
    if not post_id:
        return {"url": url, "status": "POST_NOT_FOUND", "links_added": 0}
    
    internal_count, existing_links = count_internal_links(content)
    
    if internal_count >= 10:
        return {"url": url, "status": "SKIP", "before": internal_count, "after": internal_count, "links_added": 0}
    
    cluster = CLUSTERS[cluster_name]
    links_to_add = []
    
    if role != "pillar":
        links_to_add.append({"url": f"https://rankray.com{cluster['pillar']}", "anchor": get_anchor_text(cluster_name, "pillar")})
    
    if role != "service":
        links_to_add.append({"url": f"https://rankray.com{cluster['service']}", "anchor": get_anchor_text(cluster_name, "service")})
    
    for peer in cluster["peers"][:3]:
        if peer == slug:
            continue
        peer_url = f"https://rankray.com/{peer}/"
        if peer_url not in existing_links:
            links_to_add.append({"url": peer_url, "anchor": get_anchor_text(peer, "peer")})
        if len(links_to_add) >= 5:
            break
    
    new_content, links_inserted = insert_links(content, links_to_add[:5], existing_links)
    
    if links_inserted == 0:
        return {"url": url, "status": "NO_INSERTION", "before": internal_count, "after": internal_count, "links_added": 0}
    
    try:
        endpoint = f"{WP_BASE}{post_type}s/{post_id}"
        resp = requests.post(endpoint, headers=headers, json={"content": new_content}, timeout=10)
        if resp.status_code in [200, 201]:
            new_internal_count, _ = count_internal_links(new_content)
            return {"url": url, "status": "SUCCESS", "before": internal_count, "after": new_internal_count, "links_added": links_inserted}
        else:
            return {"url": url, "status": f"API_ERROR_{resp.status_code}", "before": internal_count, "after": internal_count, "links_added": 0}
    except Exception as e:
        return {"url": url, "status": f"ERROR: {str(e)}", "before": internal_count, "after": internal_count, "links_added": 0}

def main():
    with open('/tmp/rankray-post-urls.txt', 'r') as f:
        urls = [line.strip() for line in f.readlines() if line.strip()]
    
    print(f"Processing {len(urls)} posts...")
    sys.stdout.flush()
    
    results = []
    
    for i, url in enumerate(urls):
        result = process_post(url)
        results.append(result)
        short_slug = url.split('/')[-2] if '/' in url else url
        print(f"[{i+1}/{len(urls)}] {short_slug}: {result['status']} (+{result.get('links_added', 0)})")
        sys.stdout.flush()
        time.sleep(12)
    
    # Save results
    output_path = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/rankray/internal-link-execution-log-v2.md"
    with open(output_path, 'w') as f:
        f.write("# Internal Link Execution Log v2\n\n")
        f.write(f"Date: {datetime.now().strftime('%Y-%m-%d %H:%M')}\n\n")
        f.write("| Post URL | Before | After | Added | Status |\n")
        f.write("|----------|--------|-------|-------|--------|\n")
        
        for r in results:
            f.write(f"| {r['url']} | {r.get('before', 'N/A')} | {r.get('after', 'N/A')} | {r.get('links_added', 0)} | {r['status']} |\n")
        
        success_count = sum(1 for r in results if r['status'] == 'SUCCESS')
        skip_count = sum(1 for r in results if r['status'] == 'SKIP')
        total_added = sum(r.get('links_added', 0) for r in results)
        
        f.write(f"\n## Summary\n\n")
        f.write(f"- Total posts processed: {len(results)}\n")
        f.write(f"- Successfully updated: {success_count}\n")
        f.write(f"- Skipped (already sufficient links): {skip_count}\n")
        f.write(f"- Total links added: {total_added}\n")
    
    print(f"\n✅ Complete! Results saved to {output_path}")
    print(f"Summary: {success_count} updated, {skip_count} skipped, {total_added} links added")

if __name__ == "__main__":
    main()
