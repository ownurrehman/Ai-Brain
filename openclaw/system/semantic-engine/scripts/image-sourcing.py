#!/usr/bin/env python3
"""
MASTER IMAGE SOURCING SCRIPT — Rank Ray Standard

Download SEO-optimized images from Pexels/Unsplash for WordPress articles.
This is the STANDARD technique for ALL agents (enigma, chronos, researcher, main).

Usage:
    python image-sourcing.py <output_dir> <image_plan.json>

Image Plan Format:
{
    "featured": {"filename": "keyword-image.jpg", "query": "search term"},
    "body_images": [
        {"filename": "keyword-1.jpg", "query": "search term 1"},
        {"filename": "keyword-2.jpg", "query": "search term 2"}
    ]
}

Output:
    Downloads all images to output_dir
    Returns JSON with paths for WordPress upload
"""

import requests
import os
import json
import sys
from pathlib import Path
from typing import List, Dict, Optional

# Fallback image URLs (Pexels - free, commercial use allowed)
# These are tested, working URLs for common SEO/business concepts
PEXELS_FALLBACKS = {
    # Analytics & Dashboards
    "analytics": "https://images.pexels.com/photos/326503/pexels-photo-326503.jpeg",
    "dashboard": "https://images.pexels.com/photos/326514/pexels-photo-326514.jpeg",
    "data visualization": "https://images.pexels.com/photos/1181244/pexels-photo-1181244.jpeg",
    "report": "https://images.pexels.com/photos/4491481/pexels-photo-4491481.jpeg",
    
    # Technology & Process
    "technology": "https://images.pexels.com/photos/546819/pexels-photo-546819.jpeg",
    "process": "https://images.pexels.com/photos/590022/pexels-photo-590022.jpeg",
    "workflow": "https://images.pexels.com/photos/590022/pexels-photo-590022.jpeg",
    "network": "https://images.pexels.com/photos/209283/pexels-photo-209283.jpeg",
    "diagram": "https://images.pexels.com/photos/196644/pexels-photo-196644.jpeg",
    
    # Business & Growth
    "growth": "https://images.pexels.com/photos/907166/pexels-photo-907166.jpeg",
    "success": "https://images.pexels.com/photos/907166/pexels-photo-907166.jpeg",
    "business": "https://images.pexels.com/photos/861974/pexels-photo-861974.jpeg",
    "comparison": "https://images.pexels.com/photos/861974/pexels-photo-861974.jpeg",
    "chart": "https://images.pexels.com/photos/861974/pexels-photo-861974.jpeg",
    
    # SEO & Marketing
    "seo": "https://images.pexels.com/photos/326503/pexels-photo-326503.jpeg",
    "marketing": "https://images.pexels.com/photos/326503/pexels-photo-326503.jpeg",
    "search": "https://images.pexels.com/photos/546819/pexels-photo-546819.jpeg",
    "optimization": "https://images.pexels.com/photos/590022/pexels-photo-590022.jpeg",
}


def get_image_url(query: str) -> Optional[str]:
    """
    Get image URL from Pexels fallbacks based on query keywords.
    
    Matches query against known fallback categories.
    Returns None if no match found.
    """
    query_lower = query.lower()
    
    # Try exact match first
    if query_lower in PEXELS_FALLBACKS:
        return PEXELS_FALLBACKS[query_lower]
    
    # Try keyword matching
    for keyword, url in PEXELS_FALLBACKS.items():
        if keyword in query_lower:
            return url
    
    # Try word-by-word matching
    words = query_lower.split()
    for word in words:
        if len(word) > 3:  # Skip short words
            for keyword, url in PEXELS_FALLBACKS.items():
                if keyword.startswith(word) or word in keyword:
                    return url
    
    return None


def download_image(url: str, output_path: str) -> bool:
    """
    Download image from URL to output path.
    
    Returns True if successful, False otherwise.
    """
    try:
        response = requests.get(url, stream=True, timeout=30)
        
        if response.status_code == 200:
            with open(output_path, 'wb') as f:
                for chunk in response.iter_content(8192):
                    f.write(chunk)
            return True
        else:
            print(f"  ✗ HTTP {response.status_code}")
            return False
            
    except Exception as e:
        print(f"  ✗ Error: {e}")
        return False


def source_images(image_plan: Dict, output_dir: str) -> Dict:
    """
    Source and download all images from plan.
    
    Args:
        image_plan: Dict with 'featured' and 'body_images'
        output_dir: Directory to save images
    
    Returns:
        Dict with download results
    """
    output_path = Path(output_dir)
    output_path.mkdir(parents=True, exist_ok=True)
    
    results = {
        "featured": None,
        "body_images": [],
        "summary": {
            "total": 0,
            "downloaded": 0,
            "failed": 0
        }
    }
    
    # Process featured image
    if "featured" in image_plan:
        featured = image_plan["featured"]
        filename = featured.get("filename", "featured-image.jpg")
        query = featured.get("query", "business analytics")
        
        print(f"Featured: {filename}")
        url = get_image_url(query)
        
        if url:
            output_file = output_path / filename
            if download_image(url, str(output_file)):
                print(f"  ✓ Downloaded: {output_file}")
                results["featured"] = {
                    "filename": filename,
                    "path": str(output_file),
                    "query": query,
                    "url": url,
                    "alt_text": featured.get("alt_text", f"{query} for SEO")
                }
                results["summary"]["downloaded"] += 1
            else:
                results["summary"]["failed"] += 1
        else:
            print(f"  ✗ No image found for: {query}")
            results["summary"]["failed"] += 1
        
        results["summary"]["total"] += 1
    
    # Process body images
    if "body_images" in image_plan:
        for i, img in enumerate(image_plan["body_images"], 1):
            filename = img.get("filename", f"body-image-{i}.jpg")
            query = img.get("query", "business")
            
            print(f"[{i}/{len(image_plan['body_images'])}] {filename}")
            url = get_image_url(query)
            
            if url:
                output_file = output_path / filename
                if download_image(url, str(output_file)):
                    print(f"  ✓ Downloaded")
                    results["body_images"].append({
                        "filename": filename,
                        "path": str(output_file),
                        "query": query,
                        "url": url,
                        "alt_text": img.get("alt_text", f"{query} illustration")
                    })
                    results["summary"]["downloaded"] += 1
                else:
                    results["summary"]["failed"] += 1
            else:
                print(f"  ✗ No image found for: {query}")
                results["summary"]["failed"] += 1
            
            results["summary"]["total"] += 1
    
    return results


def create_image_plan_from_article(title: str, headings: List[str]) -> Dict:
    """
    Create image plan from article title and headings.
    
    Automatically generates keyword-based filenames and search queries.
    """
    def slugify(text):
        return text.lower().replace(" ", "-").replace(",", "").replace(":", "")[:50]
    
    plan = {
        "featured": {
            "filename": f"{slugify(title)}.jpg",
            "query": f"{title} analytics dashboard",
            "alt_text": f"{title} - Rank Ray SEO services"
        },
        "body_images": []
    }
    
    for heading in headings[:10]:  # Max 10 body images
        plan["body_images"].append({
            "filename": f"{slugify(heading)}.jpg",
            "query": heading.replace("What is", "").replace("How to", "").strip(),
            "alt_text": f"{heading} - semantic SEO"
        })
    
    return plan


if __name__ == "__main__":
    """
    CLI usage: python image-sourcing.py <output_dir> <image_plan.json>
    """
    if len(sys.argv) < 3:
        print("Usage: python image-sourcing.py <output_dir> <image_plan.json>")
        print("\nExample:")
        print("  python image-sourcing.py /tmp/seo-images image-plan.json")
        sys.exit(1)
    
    output_dir = sys.argv[1]
    plan_file = sys.argv[2]
    
    # Load image plan
    with open(plan_file, 'r') as f:
        image_plan = json.load(f)
    
    print("=" * 60)
    print("IMAGE SOURCING - RANK RAY STANDARD")
    print("=" * 60)
    print(f"Output: {output_dir}")
    print(f"Plan: {plan_file}")
    print()
    
    # Download images
    results = source_images(image_plan, output_dir)
    
    # Print summary
    print("\n" + "=" * 60)
    print("SUMMARY")
    print("=" * 60)
    print(f"Total: {results['summary']['total']}")
    print(f"Downloaded: {results['summary']['downloaded']}")
    print(f"Failed: {results['summary']['failed']}")
    
    # Save results for WordPress upload
    results_file = Path(output_dir) / "download-results.json"
    with open(results_file, 'w') as f:
        json.dump(results, f, indent=2)
    
    print(f"\nResults saved: {results_file}")
