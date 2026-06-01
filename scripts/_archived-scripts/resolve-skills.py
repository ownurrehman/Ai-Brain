#!/usr/bin/env python3
"""
resolve-skills.py — Automated Task-to-Skill Router
Scans `skills/*/skill.json` files, matches a task description against their names, descriptions, and tags,
and generates the exact rules, playbooks, and guidelines required for the task.

Usage:
  python3 resolve-skills.py --task "Write physical therapy blog post for TonicPhysio"
  python3 resolve-skills.py --task "Audit the React code structure" --json
"""

import os
import sys
import json
import argparse
import re

BRAIN_ROOT = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain"
SKILLS_DIR = os.path.join(BRAIN_ROOT, "skills")
RULES_DIR = os.path.join(BRAIN_ROOT, "rules")

def clean_tokens(text):
    """Normalize text into lowercase alphabetic tokens."""
    if not text:
        return set()
    text = text.lower()
    # Remove punctuation
    text = re.sub(r'[^a-z0-9\s-]', '', text)
    # Split by whitespace and hyphens
    tokens = re.split(r'[\s-]+', text)
    return {t for t in tokens if len(t) > 2} # Skip tiny words

def scan_skills():
    """Find and parse all skill.json files in the skills directory."""
    skills_map = {}
    if not os.path.exists(SKILLS_DIR):
        return skills_map
        
    for item in os.listdir(SKILLS_DIR):
        item_path = os.path.join(SKILLS_DIR, item)
        if os.path.isdir(item_path):
            json_path = os.path.join(item_path, "skill.json")
            if os.path.isfile(json_path):
                try:
                    with open(json_path, "r") as f:
                        skill_data = json.load(f)
                        skills_map[item] = skill_data
                except Exception as e:
                    # Silently skip malformed JSON files to prevent breaking runtime
                    continue
    return skills_map

def calculate_score(task_tokens, explicit_tags, skill_name, skill_data):
    """Score a skill based on keyword overlaps and explicit tags."""
    score = 0
    
    # 1. Match explicit tags (high weight)
    skill_tags = {tag.lower() for tag in skill_data.get("tags", [])}
    for tag in explicit_tags:
        if tag.lower() in skill_tags:
            score += 15
            
    # 2. Match task tokens to skill tags
    tag_matches = task_tokens.intersection(skill_tags)
    score += len(tag_matches) * 5
    
    # 3. Match task tokens to skill name
    name_tokens = clean_tokens(skill_name)
    name_matches = task_tokens.intersection(name_tokens)
    score += len(name_matches) * 3
    
    # 4. Match task tokens to skill description
    desc_tokens = clean_tokens(skill_data.get("description", ""))
    desc_matches = task_tokens.intersection(desc_tokens)
    score += len(desc_matches) * 1
    
    return score

def get_rule_content(rule_rel_path):
    """Retrieve content of a rule file safely."""
    # Ensure it maps properly under BRAIN_ROOT or RULES_DIR
    path = os.path.join(BRAIN_ROOT, rule_rel_path)
    if not os.path.isfile(path) and rule_rel_path.startswith("rules/"):
        path = os.path.join(BRAIN_ROOT, rule_rel_path)
    elif not os.path.isfile(path):
        # Fallback if rule path doesn't have rules/ prefix
        path = os.path.join(RULES_DIR, rule_rel_path)
        
    if os.path.isfile(path):
        try:
            with open(path, "r") as f:
                return f.read()
        except Exception:
            return f"Error reading rule at: {rule_rel_path}"
    return f"Rule file not found: {rule_rel_path}"

def resolve(task_desc, explicit_tags_list, min_score=2):
    task_tokens = clean_tokens(task_desc)
    explicit_tags = set(explicit_tags_list or [])
    
    skills_map = scan_skills()
    scored_skills = []
    
    for name, data in skills_map.items():
        score = calculate_score(task_tokens, explicit_tags, name, data)
        if score >= min_score:
            scored_skills.append((score, name, data))
            
    # Sort by score descending
    scored_skills.sort(key=lambda x: x[0], reverse=True)
    return scored_skills

def main():
    parser = argparse.ArgumentParser(description="AI Brain Task-to-Skill Router")
    parser.add_argument("--task", required=True, help="Task description string")
    parser.add_argument("--tags", help="Comma-separated list of explicit tags")
    parser.add_argument("--json", action="store_true", help="Output raw JSON instead of Markdown")
    parser.add_argument("--min-score", type=int, default=2, help="Minimum scoring threshold (default 2)")
    
    args = parser.parse_args()
    
    tag_list = [t.strip() for t in args.tags.split(",")] if args.tags else []
    
    resolved = resolve(args.task, tag_list, args.min_score)
    
    if args.json:
        # Format resolved skills as structured JSON
        output = {
            "task": args.task,
            "resolved_skills": []
        }
        for score, name, data in resolved:
            output["resolved_skills"].append({
                "skill_name": name,
                "score": score,
                "description": data.get("description", ""),
                "rules": data.get("rules", []),
                "expected_outputs": data.get("expected_outputs", {})
            })
        print(json.dumps(output, indent=2))
        return
        
    # Render gorgeous Markdown block
    print("\n# resolved-skills-context")
    print("> **SYSTEM RESOLUTION:** The following specialized playbooks and strict rules have been dynamically mapped to your current task context.")
    print(f"> **Task:** *{args.task}*\n")
    
    if not resolved:
        print("### No Specialized Playbooks Resolved")
        print("No matching skills found above threshold. Follow general system instructions in `MASTER-SYSTEM-BOOTSTRAP.md`.")
        return
        
    print("## 1. Mapped Playbooks & Skills")
    for score, name, data in resolved:
        print(f"- **`skills/{name}/`** (Score: {score})")
        print(f"  *Description:* {data.get('description', '')}")
        expected = data.get("expected_outputs", {})
        if expected:
            print(f"  *Deliverable Type:* `{expected.get('deliverable_type', 'N/A')}` | *Path Pattern:* `{expected.get('path_pattern', 'N/A')}`")
            
    # Compile and de-duplicate rule files
    rules_to_load = []
    seen_rules = set()
    for _, _, data in resolved:
        for rule in data.get("rules", []):
            if rule not in seen_rules:
                seen_rules.add(rule)
                rules_to_load.append(rule)
                
    if rules_to_load:
        print("\n## 2. Enforced Rules & Guidelines")
        print("You MUST strictly comply with the following instructions resolved for this execution:")
        for rule in rules_to_load:
            print(f"\n### Rule: `{rule}`")
            print("```markdown")
            print(get_rule_content(rule).strip())
            print("```")
    print()

if __name__ == "__main__":
    main()
