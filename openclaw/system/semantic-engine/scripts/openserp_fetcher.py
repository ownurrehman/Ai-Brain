#!/usr/bin/env python3
"""
OpenSERP API Fetcher for Semantic Brief Engine
Captures organic SERP results from Google and DuckDuckGo via OpenSERP
"""

import os
import sys
import json
import requests
import hashlib
from typing import Optional
from datetime import datetime
from pathlib import Path

# Configuration
OPENSERP_BASE_URL = os.environ.get("OPENSERP_URL", "http://127.0.0.1:7070")
CACHE_DIR = Path(__file__).parent.parent / "cache"
LOG_DIR = Path(__file__).parent.parent / "logs"
CACHE_TTL_SECONDS = 24 * 60 * 60  # 24 hours

# Ensure directories exist
CACHE_DIR.mkdir(parents=True, exist_ok=True)
LOG_DIR.mkdir(parents=True, exist_ok=True)

def log(message: str):
    """Log message with timestamp"""
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    log_line = f"[{timestamp}] {message}"
    print(log_line)
    
    log_file = LOG_DIR / f"openserp-{datetime.now().strftime('%Y-%m-%d')}.log"
    with open(log_file, "a") as f:
        f.write(log_line + "\n")

def get_cache_key(query: str, engine: str) -> str:
    """Generate cache key from query and engine"""
    return hashlib.md5(f"{engine}:{query}".lower().encode()).hexdigest()

def get_cache_file(query: str, engine: str) -> Path:
    """Get cache file path"""
    cache_key = get_cache_key(query, engine)
    return CACHE_DIR / f"openserp-{engine}-{cache_key}.json"

def load_from_cache(query: str, engine: str) -> Optional[dict]:
    """Load data from cache if valid"""
    cache_file = get_cache_file(query, engine)
    
    if not cache_file.exists():
        return None
    
    try:
        with open(cache_file, "r") as f:
            data = json.load(f)
        
        cached_at = datetime.fromisoformat(data["cached_at"])
        age = datetime.now() - cached_at
        
        if age.total_seconds() > CACHE_TTL_SECONDS:
            log(f"Cache expired for '{query}' ({engine})")
            return None
        
        log(f"Cache hit for '{query}' ({engine}, age: {age})")
        return data
    
    except Exception as e:
        log(f"Cache read error: {e}")
        return None

def save_to_cache(query: str, engine: str, data: dict):
    """Save data to cache"""
    cache_file = get_cache_file(query, engine)
    data["cached_at"] = datetime.now().isoformat()
    data["query"] = query
    data["engine"] = engine
    
    with open(cache_file, "w") as f:
        json.dump(data, f, indent=2)
    
    log(f"Cache saved for '{query}' ({engine})")

def fetch_serp(query: str, engine: str = "google", limit: int = 10) -> dict:
    """
    Fetch SERP data from OpenSERP
    
    Args:
        query: Search query
        engine: Search engine (google, duckduckgo, bing)
        limit: Number of results to fetch
    
    Returns:
        SERP data with organic results, or None if failed
    """
    
    log(f"Fetching SERP for '{query}' from {engine}")
    
    # Check cache first
    cached = load_from_cache(query, engine)
    if cached:
        return cached
    
    # Build API URL
    endpoint = f"{OPENSERP_BASE_URL}/{engine}/search"
    params = {
        "text": query,
        "limit": limit
    }
    
    try:
        response = requests.get(endpoint, params=params, timeout=30)
        response.raise_for_status()
        
        data = response.json()
        
        if not data:
            log(f"No results for '{query}' from {engine}")
            return {
                "query": query,
                "engine": engine,
                "organic": [],
                "fetched_at": datetime.now().isoformat(),
                "error": "No results returned"
            }
        
        # Parse results
        organic = []
        for result in data:
            organic.append({
                "rank": result.get("rank", len(organic) + 1),
                "url": result.get("url", ""),
                "title": result.get("title", ""),
                "description": result.get("description", ""),
                "ad": result.get("ad", False)
            })
        
        log(f"Found {len(organic)} results for '{query}' from {engine}")
        
        result = {
            "query": query,
            "engine": engine,
            "organic": organic,
            "fetched_at": datetime.now().isoformat(),
            "total_results": len(organic)
        }
        
        # Save to cache
        save_to_cache(query, engine, result)
        
        return result
    
    except requests.exceptions.RequestException as e:
        log(f"API error for '{query}' ({engine}): {e}")
        
        # Fallback to DuckDuckGo if Google fails
        if engine == "google":
            log(f"Falling back to DuckDuckGo for '{query}'")
            return fetch_serp(query, "duckduckgo", limit)
        
        return {
            "query": query,
            "engine": engine,
            "organic": [],
            "fetched_at": datetime.now().isoformat(),
            "error": str(e)
        }

def fetch_multi_engine(query: str, engines: list = None, limit: int = 10) -> dict:
    """
    Fetch SERP data from multiple engines
    
    Args:
        query: Search query
        engines: List of engines to query (default: ["google", "duckduckgo"])
        limit: Number of results per engine
    
    Returns:
        Combined SERP data from all engines
    """
    
    if engines is None:
        engines = ["google", "duckduckgo"]
    
    log(f"Fetching multi-engine SERP for '{query}': {engines}")
    
    results = {
        "query": query,
        "fetched_at": datetime.now().isoformat(),
        "engines": {}
    }
    
    for engine in engines:
        serp_data = fetch_serp(query, engine, limit)
        results["engines"][engine] = serp_data
    
    # Aggregate organic results (deduplicated by URL)
    all_results = {}
    for engine, data in results["engines"].items():
        for result in data.get("organic", []):
            url = result["url"]
            if url not in all_results:
                all_results[url] = result
                all_results[url]["engines"] = [engine]
            else:
                all_results[url]["engines"].append(engine)
    
    results["aggregated"] = {
        "total_unique": len(all_results),
        "results": list(all_results.values())
    }
    
    return results

def main():
    """Main entry point"""
    if len(sys.argv) < 2:
        print("Usage: python openserp-fetcher.py <query> [engine] [limit]")
        print("  query: Search query")
        print("  engine: Search engine (google, duckduckgo, bing) or 'multi' for all")
        print("  limit: Number of results (default: 10)")
        sys.exit(1)
    
    query = sys.argv[1]
    engine = sys.argv[2] if len(sys.argv) > 2 else "google"
    limit = int(sys.argv[3]) if len(sys.argv) > 3 else 10
    
    if engine == "multi":
        result = fetch_multi_engine(query, limit=limit)
    else:
        result = fetch_serp(query, engine, limit)
    
    print(json.dumps(result, indent=2))
    log(f"Fetch complete for '{query}'")

if __name__ == "__main__":
    main()
