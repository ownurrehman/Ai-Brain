#!/usr/bin/env python3
"""
Chaos On-Page SEO Auditor
Quick technical audit for a single page
"""

import sys
import re
from urllib.request import urlopen, Request
from urllib.error import HTTPError, URLError


def fetch_page(url):
    """Fetch page content"""
    headers = {
        'User-Agent': 'Mozilla/5.0 (compatible; ChaosSEO/1.0; +https://rankray.com)'
    }
    try:
        req = Request(url, headers=headers)
        response = urlopen(req, timeout=30)
        content = response.read().decode('utf-8', errors='ignore')
        return {
            'status': response.status,
            'content': content,
            'url': response.url  # Final URL after redirects
        }
    except HTTPError as e:
        return {'error': f'HTTP {e.code}: {e.reason}'}
    except URLError as e:
        return {'error': f'URL Error: {e.reason}'}
    except Exception as e:
        return {'error': str(e)}


def check_title(content):
    """Check title tag"""
    match = re.search(r'<title>(.*?)</title>', content, re.IGNORECASE | re.DOTALL)
    if not match:
        return {'status': 'CRITICAL', 'issue': 'Missing title tag'}
    
    title = match.group(1).strip()
    length = len(title)
    
    issues = []
    if length > 60:
        issues.append(f'Too long ({length}/60 chars)')
    elif length < 30:
        issues.append(f'Short ({length} chars)')
    
    return {
        'title': title,
        'length': length,
        'status': 'PASS' if length <= 60 else 'WARNING',
        'issues': issues
    }


def check_meta_description(content):
    """Check meta description"""
    match = re.search(r'<meta[^>]*name=["\']description["\'][^>]*content=["\']([^"\']*)["\']', content, re.IGNORECASE | re.DOTALL)
    if not match:
        match = re.search(r'<meta[^>]*content=["\']([^"\']*)[^>]*name=["\']description["\']', content, re.IGNORECASE | re.DOTALL)
    
    if not match:
        return {'status': 'WARNING', 'issue': 'Missing meta description'}
    
    desc = match.group(1).strip()
    length = len(desc)
    
    issues = []
    if length > 160:
        issues.append(f'Too long ({length}/160 chars)')
    elif length < 120:
        issues.append(f'Short ({length} chars)')
    
    if '--' in desc:
        issues.append('Contains double dashes')
    
    return {
        'description': desc,
        'length': length,
        'status': 'PASS' if not issues else 'WARNING',
        'issues': issues
    }


def check_headings(content):
    """Check heading structure"""
    h1s = re.findall(r'<h1[^>]*>(.*?)</h1>', content, re.IGNORECASE | re.DOTALL)
    h2s = re.findall(r'<h2[^>]*>(.*?)</h2>', content, re.IGNORECASE | re.DOTALL)
    h3s = re.findall(r'<h3[^>]*>(.*?)</h3>', content, re.IGNORECASE | re.DOTALL)
    
    issues = []
    status = 'PASS'
    
    if len(h1s) == 0:
        issues.append('Missing H1')
        status = 'CRITICAL'
    elif len(h1s) > 1:
        issues.append(f'Multiple H1s ({len(h1s)})')
        status = 'WARNING'
    
    # Strip HTML tags for display
    h1_text = re.sub(r'<[^>]+>', '', h1s[0]) if h1s else 'N/A'
    
    return {
        'h1_count': len(h1s),
        'h2_count': len(h2s),
        'h3_count': len(h3s),
        'h1_text': h1_text[:60] + '...' if len(h1_text) > 60 else h1_text,
        'status': status,
        'issues': issues
    }


def check_canonical(content):
    """Check canonical tag"""
    match = re.search(r'<link[^>]*rel=["\']canonical["\'][^>]*href=["\']([^"\']*)["\']', content, re.IGNORECASE)
    if not match:
        match = re.search(r'<link[^>]*href=["\']([^"\']*)[^>]*rel=["\']canonical["\']', content, re.IGNORECASE)
    
    if not match:
        return {'status': 'WARNING', 'issue': 'Missing canonical tag'}
    
    return {
        'canonical': match.group(1),
        'status': 'PASS'
    }


def check_images(content):
    """Check images for alt text"""
    images = re.findall(r'<img[^>]*>', content, re.IGNORECASE)
    
    total = len(images)
    missing_alt = 0
    
    for img in images:
        # Check for alt attribute (even if empty)
        alt_match = re.search(r'alt=["\'][^"\']*["\']', img, re.IGNORECASE)
        if not alt_match:
            # Check for alt with empty value
            alt_present = 'alt' in img.lower() and re.search(r'alt\s*=', img, re.IGNORECASE)
            if not alt_present:
                missing_alt += 1
    
    if total == 0:
        return {'status': 'INFO', 'message': 'No images found'}
    
    missing_pct = (missing_alt / total) * 100 if total else 0
    
    status = 'PASS'
    if missing_pct > 50:
        status = 'WARNING'
    elif missing_alt > 0:
        status = 'INFO'
    
    return {
        'total_images': total,
        'missing_alt': missing_alt,
        'missing_pct': round(missing_pct, 1),
        'status': status
    }


def check_schema(content):
    """Check for JSON-LD schema"""
    schemas = re.findall(r'<script[^>]*type=["\']application/ld\+json["\'][^>]*>(.*?)</script>', content, re.IGNORECASE | re.DOTALL)
    
    schema_types = []
    for schema in schemas:
        # Simple type detection
        type_match = re.search(r'["\']@type["\']\s*:\s*["\']([^"\']+)', schema)
        if type_match:
            schema_types.append(type_match.group(1))
    
    has_org = 'Organization' in schema_types or 'LocalBusiness' in schema_types or 'LegalService' in schema_types
    
    return {
        'schema_count': len(schemas),
        'types': schema_types,
        'has_org_schema': has_org,
        'status': 'PASS' if has_org else 'INFO'
    }


def check_robots_meta(content):
    """Check for blocking robots meta"""
    match = re.search(r'<meta[^>]*name=["\']robots["\'][^>]*content=["\']([^"\']*)["\']', content, re.IGNORECASE)
    if not match:
        match = re.search(r'<meta[^>]*content=["\']([^"\']*)[^>]*name=["\']robots["\']', content, re.IGNORECASE)
    
    if not match:
        return {'status': 'PASS', 'indexable': True}
    
    content_attr = match.group(1).lower()
    indexable = 'noindex' not in content_attr
    
    return {
        'robots': content_attr,
        'indexable': indexable,
        'status': 'CRITICAL' if not indexable else 'PASS'
    }


def calculate_score(results):
    """Calculate overall health score"""
    score = 100
    
    for key, result in results.items():
        if isinstance(result, dict):
            status = result.get('status', '')
            if status == 'CRITICAL':
                score -= 20
            elif status == 'WARNING':
                score -= 10
            elif status == 'INFO':
                score -= 5
    
    return max(0, score)


def main():
    if len(sys.argv) < 2:
        print("Usage: python audit_page.py <URL>")
        print("Example: python audit_page.py https://rankray.com")
        sys.exit(1)
    
    url = sys.argv[1]
    
    if not url.startswith(('http://', 'https://')):
        url = 'https://' + url
    
    print("=" * 60)
    print(f"CHAOS ON-PAGE SEO AUDIT")
    print(f"Target: {url}")
    print("=" * 60)
    print()
    
    # Fetch page
    fetch = fetch_page(url)
    if 'error' in fetch:
        print(f"ERROR: {fetch['error']}")
        sys.exit(1)
    
    content = fetch['content']
    final_url = fetch['url']
    
    if final_url != url:
        print(f"Redirected to: {final_url}")
        print()
    
    # Run checks
    results = {
        'title': check_title(content),
        'meta': check_meta_description(content),
        'headings': check_headings(content),
        'canonical': check_canonical(content),
        'images': check_images(content),
        'schema': check_schema        'robots': check_robots_meta(content)
    }
    
    # Calculate score
    score = calculate_score(results)
    
    # Display results
    print(f"HEALTH SCORE: {score}/100")
    print()
    
    print("TITLE TAG")
    print(f"  Content: {results['title'].get('title', 'N/A')}")
    print(f"  Length: {results['title'].get('length', 0)} chars")
    print(f"  Status: {results['title']['status']}")
    if results['title'].get('issues'):
        print(f"  Issues: {', '.join(results['title']['issues'])}")
    print()
    
    print("META DESCRIPTION")
    print(f"  Content: {results['meta'].get('description', 'N/A')[:60]}...")
    print(f"  Length: {results['meta'].get('length', 0)} chars")
    print(f"  Status: {results['meta']['status']}")
    if results['meta'].get('issues'):
        print(f"  Issues: {', '.join(results['meta']['issues'])}")
    print()
    
    print("HEADINGS")
    print(f"  H1: {results['headings']['h1_count']} | H2: {results['headings']['h2_count']} | H3: {results['headings']['h3_count']}")
    print(f"  H1 Text: {results['headings']['h1_text']}")
    print(f"  Status: {results['headings']['status']}")
    if results['headings'].get('issues'):
        print(f"  Issues: {', '.join(results['headings']['issues'])}")
    print()
    
    print("CANONICAL")
    print(f"  URL: {results['canonical'].get('canonical', 'N/A')}")
    print(f"  Status: {results['canonical']['status']}")
    print()
    
    print("IMAGES")
    if 'message' in results['images']:
        print(f"  {results['images']['message']}")
    else:
        print(f"  Total: {results['images']['total_images']}")
        print(f"  Missing Alt: {results['images']['missing_alt']} ({results['images']['missing_pct']}%)")
        print(f"  Status: {results['images']['status']}")
    print()
    
    print("SCHEMA MARKUP")
    print(f"  Schemas Found: {results['schema']['schema_count']}")
    print(f"  Types: {', '.join(results['schema']['types']) if results['schema']['types'] else 'None'}")
    print(f"  Has Org Schema: {results['schema']['has_org_schema']}")
    print(f"  Status: {results['schema']['status']}")
    print()
    
    print("ROBOTS META")
    print(f"  Content: {results['robots'].get('robots', 'index, follow')}")
    print(f"  Indexable: {results['robots']['indexable']}")
    print(f"  Status: {results['robots']['status']}")
    print()
    
    print("=" * 60)
    print(f"SCORE: {score}/100")
    print("=" * 60)


if __name__ == "__main__":
    main()
