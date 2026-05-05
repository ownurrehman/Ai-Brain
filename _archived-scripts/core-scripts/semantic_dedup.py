import os
import re
import sys
import json
from collections import defaultdict

# --- CONFIGURATION ---
CONTEXT_BASE = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/"
QUEUE_FILES = [
    os.path.join(CONTEXT_BASE, "hermes/rankray-30-day-blog-strategy.md"),
    # Add other queue sources here
]

# Stop words to ignore for semantic matching
STOP_WORDS = {"guide", "how", "to", "the", "in", "for", "with", "and", "a", "of", "best", "top", "complete", "checklist", "strategies", "2026"}

def clean_keyword(kwd):
    """Normalize keyword for entity extraction."""
    kwd = kwd.lower()
    kwd = re.sub(r'[^a-z0-9 ]', '', kwd)
    words = [w for w in kwd.split() if w not in STOP_WORDS and len(w) > 2]
    return set(words)

def calculate_similarity(set1, set2):
    """Jaccard similarity based on cleaned entity words."""
    if not set1 or not set2:
        return 0
    intersection = set1.intersection(set2)
    union = set1.union(set2)
    return len(intersection) / len(union)

def parse_markdown_queue(file_path):
    """Extract titles and keywords from markdown content."""
    items = []
    if not os.path.exists(file_path):
        return items
    
    with open(file_path, 'r') as f:
        content = f.read()
        
    # Pattern for Day X: Title
    days = re.findall(r'### (Day \d+:.*?)\n- \*\*Title:\*\* (.*?)\n- \*\*Target KWD:\*\* (.*?)\n', content)
    for day_label, title, kwd in days:
        items.append({
            "source": file_path,
            "label": day_label,
            "title": title,
            "keyword": kwd,
            "entities": clean_keyword(kwd)
        })
    return items

def deduplicate_queue(items, threshold=0.4):
    """Cluster items and merge duplicates into pillars."""
    clusters = []
    
    for item in items:
        matched = False
        for cluster in clusters:
            # Compare with the 'Primary' item of the cluster
            similarity = calculate_similarity(item["entities"], cluster["primary"]["entities"])
            if similarity >= threshold:
                cluster["children"].append(item)
                matched = True
                break
        
        if not matched:
            clusters.append({
                "primary": item,
                "children": []
            })
            
    return clusters

def generate_report(clusters):
    """Output the refactored queue."""
    report = ["# 🛡️ Content Deduplication & Entity Cluster Report", ""]
    report.append(f"**Total Clusters Identified:** {len(clusters)}")
    report.append("---")
    
    for cluster in clusters:
        primary = cluster["primary"]
        children = cluster["children"]
        
        if not children:
            report.append(f"### ✅ UNIQUE: {primary['title']}")
            report.append(f"- **Primary Keyword:** `{primary['keyword']}`")
            report.append("")
        else:
            report.append(f"### 🚀 PILLAR MERGE: {primary['title']}")
            report.append(f"- **Primary Keyword (H1/URL):** `{primary['keyword']}`")
            report.append(f"- **Merged Sub-Topics (H2/H3):**")
            for child in children:
                report.append(f"  - {child['title']} (Keyword: `{child['keyword']}`)")
            report.append("- **Action:** Merge into one Ultimate Guide.")
            report.append("")
            
    return "\n".join(report)

def main():
    print("🧠 Lead Systems Architect: Initializing Semantic Deduplication Step...")
    
    all_items = []
    for q_file in QUEUE_FILES:
        print(f"🔍 Scanning {os.path.basename(q_file)}...")
        all_items.extend(parse_markdown_queue(q_file))
        
    if not all_items:
        print("❌ No items found in queue.")
        return

    print(f"📊 Processing {len(all_items)} potential topics...")
    clusters = deduplicate_queue(all_items)
    
    report_content = generate_report(clusters)
    
    output_path = os.path.join(CONTEXT_BASE, "openclaw/DEDUPLICATED_QUEUE.md")
    with open(output_path, 'w') as f:
        f.write(report_content)
        
    print(f"✅ Success! Refactored queue saved to: {output_path}")

if __name__ == "__main__":
    main()
