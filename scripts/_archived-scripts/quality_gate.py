"""
Content Quality Gate
Scans HTML/Markdown for AI footprints and rule violations.
"""
import re, sys, json

def audit_content(content):
    issues = []
    
    # 1. Check for H1 in body
    if re.search(r'<h1[\s>]', content, re.IGNORECASE):
        issues.append("VIOLATION: H1 tag found in body content")
    
    # 2. Check for em-dashes
    if '—' in content or '–' in content:
        issues.append("VIOLATION: Em-dash or en-dash detected")
    
    # 3. Check for repeated words
    repeated = re.findall(r'\b(\w+)\s+\1\b', content, re.IGNORECASE)
    if repeated:
        issues.append(f"VIOLATION: Repeated words: {set(repeated)}")
    
    # 4. Check word count (rough)
    words = len(content.split())
    if words < 2000:
        issues.append(f"WARNING: Word count is {words}, minimum 2000 recommended")
    
    # 5. Check for internal links
    links = re.findall(r'href=["\'](https?://tonicphysio\.com[^"\']+)["\']', content)
    if len(links) < 10:
        issues.append(f"WARNING: Only {len(links)} internal links found, need 10+")
    
    # 6. Check for generic filler
    fillers = ["In today's digital landscape", "It is important to note", "As we know"]
    for filler in fillers:
        if filler.lower() in content.lower():
            issues.append(f"VIOLATION: Filler phrase detected: '{filler}'")
    
    return {
        "word_count": words,
        "internal_links": links,
        "issues": issues,
        "passed": len(issues) == 0
    }

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python3 quality_gate.py <file.html>")
        sys.exit(1)
    
    with open(sys.argv[1], 'r') as f:
        content = f.read()
    
    result = audit_content(content)
    print(json.dumps(result, indent=2))
    sys.exit(0 if result["passed"] else 1)
