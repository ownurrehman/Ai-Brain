#!/usr/bin/env python3
"""
PAA (People Also Ask) Extractor for Semantic Brief Engine
Uses Playwright browser automation to capture PAA questions from Google SERP
"""

import os
import sys
import json
import time
from typing import Optional
from datetime import datetime
from pathlib import Path

# Check if playwright is available
try:
    from playwright.sync_api import sync_playwright
    PLAYWRIGHT_AVAILABLE = True
except ImportError:
    PLAYWRIGHT_AVAILABLE = False
    print("WARNING: Playwright not installed. Install with: pip install playwright")
    print("Then run: playwright install")

# Configuration
CACHE_DIR = Path(__file__).parent.parent / "cache"
LOG_DIR = Path(__file__).parent.parent / "logs"
CACHE_TTL_SECONDS = 7 * 24 * 60 * 60  # 7 days (PAA changes less frequently)
SCREENSHOT_DIR = Path(__file__).parent.parent / "screenshots"

# Ensure directories exist
CACHE_DIR.mkdir(parents=True, exist_ok=True)
LOG_DIR.mkdir(parents=True, exist_ok=True)
SCREENSHOT_DIR.mkdir(parents=True, exist_ok=True)

def log(message: str):
    """Log message with timestamp"""
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    log_line = f"[{timestamp}] {message}"
    print(log_line)
    
    log_file = LOG_DIR / f"paa-{datetime.now().strftime('%Y-%m-%d')}.log"
    with open(log_file, "a") as f:
        f.write(log_line + "\n")

def extract_paa_playwright(query: str, headless: bool = True) -> dict:
    """
    Extract PAA questions using Playwright browser automation
    
    Args:
        query: Search query
        headless: Run browser in headless mode
    
    Returns:
        Dictionary with PAA questions and metadata
    """
    
    if not PLAYWRIGHT_AVAILABLE:
        log("ERROR: Playwright not available")
        return {
            "query": query,
            "paa_questions": [],
            "error": "Playwright not installed",
            "fetched_at": datetime.now().isoformat()
        }
    
    log(f"Extracting PAA for '{query}' with Playwright")
    
    paa_questions = []
    screenshot_path = None
    
    try:
        with sync_playwright() as p:
            # Launch browser
            browser = p.chromium.launch(headless=headless)
            context = browser.new_context(
                viewport={"width": 1920, "height": 1080},
                user_agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36"
            )
            page = context.new_page()
            
            # Navigate to Google search
            url = f"https://www.google.com/search?q={query.replace(' ', '+')}&hl=en"
            log(f"Navigating to: {url}")
            
            page.goto(url, wait_until="networkidle", timeout=30000)
            
            # Wait for PAA section to load
            time.sleep(3)
            
            # Take screenshot for debugging
            screenshot_file = SCREENSHOT_DIR / f"paa-{hash(query)}-{datetime.now().strftime('%Y%m%d-%H%M%S')}.png"
            page.screenshot(path=str(screenshot_file), full_page=True)
            screenshot_path = str(screenshot_file)
            log(f"Screenshot saved: {screenshot_path}")
            
            # Find and click PAA questions to expand them
            # PAA questions are typically in divs with specific classes
            # Note: Google's HTML structure changes frequently, so selectors may need updates
            
            paa_selectors = [
                "div[role='heading']",  # Common PAA selector
                "div.related-question-pair",
                "div[data-attrid='kc:/web/search:PeopleAlsoAskSearch']",
                "g-accordion-expander"
            ]
            
            for selector in paa_selectors:
                try:
                    paa_elements = page.query_selector_all(selector)
                    log(f"Found {len(paa_elements)} PAA elements with selector: {selector}")
                    
                    for element in paa_elements:
                        try:
                            # Click to expand (sometimes needed)
                            element.click(timeout=2000)
                            time.sleep(0.5)
                            
                            # Extract question text
                            question = element.inner_text(timeout=2000).strip()
                            
                            if question and len(question) > 10 and question not in paa_questions:
                                paa_questions.append(question)
                                log(f"Extracted PAA: {question}")
                        
                        except Exception as e:
                            # Some elements might not be clickable or visible
                            continue
                
                except Exception as e:
                    log(f"Selector {selector} failed: {e}")
                    continue
            
            browser.close()
            
    except Exception as e:
        log(f"Playwright extraction error: {e}")
        return {
            "query": query,
            "paa_questions": paa_questions,  # Return partial results if any
            "error": str(e),
            "screenshot": screenshot_path,
            "fetched_at": datetime.now().isoformat()
        }
    
    log(f"Extracted {len(paa_questions)} PAA questions for '{query}'")
    
    return {
        "query": query,
        "paa_questions": paa_questions,
        "question_count": len(paa_questions),
        "screenshot": screenshot_path,
        "fetched_at": datetime.now().isoformat(),
        "method": "playwright_automation"
    }

def extract_paa_fallback(query: str) -> dict:
    """
    Fallback PAA extraction using OpenSERP + manual parsing
    Less reliable but doesn't require browser automation
    """
    
    log(f"Using fallback PAA extraction for '{query}'")
    
    # This is a placeholder - in production, you might:
    # 1. Use a paid API that provides PAA data
    # 2. Parse PAA from SERP HTML if available
    # 3. Use a pre-computed database of PAA questions
    
    return {
        "query": query,
        "paa_questions": [],
        "warning": "Fallback method - no PAA data available",
        "fetched_at": datetime.now().isoformat()
    }

def main():
    """Main entry point"""
    if len(sys.argv) < 2:
        print("Usage: python paa-extractor.py <query> [--headful]")
        print("  query: Search query to extract PAA questions for")
        print("  --headful: Run browser with UI visible (for debugging)")
        sys.exit(1)
    
    query = sys.argv[1]
    headless = "--headful" not in sys.argv
    
    # Try Playwright first
    result = extract_paa_playwright(query, headless=headless)
    
    # Fallback if Playwright failed
    if not result.get("paa_questions") and result.get("error"):
        log("Playwright failed, trying fallback")
        result = extract_paa_fallback(query)
    
    print(json.dumps(result, indent=2))
    log(f"PAA extraction complete for '{query}': {result.get('question_count', 0)} questions")

if __name__ == "__main__":
    main()
