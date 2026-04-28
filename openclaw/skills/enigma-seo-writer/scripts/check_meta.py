#!/usr/bin/env python3
"""
Meta Description Validator for Enigma SEO Writer
Checks meta description against Rank Ray non-negotiable standards
"""

import sys
import re

def validate_meta_description(description: str, keyword: str = "", brand: str = "") -> dict:
    """
    Validate a meta description against SEO best practices
    
    Args:
        description: The meta description text
        keyword: Primary target keyword (optional)
        brand: Brand name to include (optional)
    
    Returns:
        dict with validation results
    """
    results = {
        "valid": True,
        "issues": [],
        "stats": {},
    }
    
    # Check 1: Length must be under 160 characters
    length = len(description)
    results["stats"]["char_count"] = length
    results["stats"]["pixel_est"] = length * 8  # rough pixel estimate
    
    if length > 160:
        results["valid"] = False
        results["issues"].append(f"❌ Too long: {length}/160 characters")
    elif length < 120:
        results["issues"].append(f"⚠️ Short: {length} characters (120-155 recommended)")
    else:
        results["issues"].append(f"✅ Length: {length}/160 characters")
    
    # Check 2: No double dashes
    if "--" in description:
        results["valid"] = False
        results["issues"].append("❌ Contains double dashes -- (not allowed)")
    else:
        results["issues"].append("✅ No double dashes")
    
    # Check 3: No emojis
    emoji_pattern = re.compile("["
        "\U0001F600-\U0001F64F"  # emoticons
        "\U0001F300-\U0001F5FF"  # symbols & pictographs
        "\U0001F680-\U0001F6FF"  # transport & map symbols
        "\U0001F1E0-\U0001F1FF"  # flags
        "\U00002702-\U000027B0"
        "\U000024C2-\U0001F251"
        "]+")
    
    if emoji_pattern.search(description):
        results["valid"] = False
        results["issues"].append("❌ Contains emojis (not allowed)")
    else:
        results["issues"].append("✅ No emojis")
    
    # Check 4: Keyword included (if provided)
    if keyword:
        keyword_lower = keyword.lower()
        desc_lower = description.lower()
        
        if keyword_lower in desc_lower:
            results["issues"].append(f"✅ Contains keyword: '{keyword}'")
        else:
            results["issues"].append(f"⚠️ Missing keyword: '{keyword}'")
    
    # Check 5: Brand included (if provided)
    if brand:
        brand_lower = brand.lower()
        desc_lower = description.lower()
        
        if brand_lower in desc_lower:
            results["issues"].append(f"✅ Contains brand: '{brand}'")
        else:
            results["issues"].append(f"⚠️ Missing brand: '{brand}'")
    
    # Check 6: Proper punctuation (no dashes used as separators)
    dash_separators = re.search(r'\s+-\s+|^-|-$', description)
    if dash_separators:
        results["issues"].append("⚠️ Dashes used as separators (replace with proper punctuation)")
    else:
        results["issues"].append("✅ Proper punctuation usage")
    
    return results


def main():
    if len(sys.argv) < 2:
        print("Usage: python check_meta.py \"<meta description>\" [keyword] [brand]")
        print()
        print("Example:")
        print('  python check_meta.py "Learn SEO from Rank Ray. Best SEO agency in Pakistan. Rank Ray." "SEO" "Rank Ray"')
        sys.exit(1)
    
    description = sys.argv[1]
    keyword = sys.argv[2] if len(sys.argv) > 2 else ""
    brand = sys.argv[3] if len(sys.argv) > 3 else ""
    
    results = validate_meta_description(description, keyword, brand)
    
    print("=" * 50)
    print("META DESCRIPTION VALIDATION")
    print("=" * 50)
    print()
    print(f"Input: \"{description}\"")
    print()
    
    for issue in results["issues"]:
        print(f"  {issue}")
    
    print()
    print("=" * 50)
    if results["valid"]:
        print("STATUS: ✅ VALID")
    else:
        print("STATUS: ❌ INVALID - Fix issues before using")
    print("=" * 50)


if __name__ == "__main__":
    main()
