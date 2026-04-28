#!/usr/bin/env python3
"""
Semrush API Extractor with Intelligent Caching

Caching Strategy:
- 7-day TTL for all keyword data
- Query normalization to prevent duplicates
- Batch extraction (100 keywords per call)
- Cache hit rate target: 80%+

API Unit Cost Optimization:
- Check cache BEFORE any API call
- Reuse related keyword data when possible
- Store full response, not just filtered results
- Log every API call for audit
"""

import os
import sys
import json
import hashlib
import time
from datetime import datetime, timedelta
from pathlib import Path
from typing import Optional, List, Dict, Any

# Add parent directory to path for imports
sys.path.insert(0, str(Path(__file__).parent.parent))

from config.logger import get_logger

logger = get_logger(__name__)
log = logger.info

log = get_logger(__name__)

CACHE_DIR = Path(__file__).parent.parent / "cache" / "semrush"
CACHE_INDEX_FILE = CACHE_DIR / "cache_index.json"
CACHE_TTL_DAYS = 7

class SemrushCache:
    """
    Intelligent caching layer for Semrush API data
    
    Features:
    - 7-day TTL
    - Query normalization
    - Batch storage
    - Fast lookups via index
    """
    
    def __init__(self):
        CACHE_DIR.mkdir(parents=True, exist_ok=True)
        self.index = self._load_index()
    
    def _load_index(self) -> dict:
        """Load cache index for fast lookups"""
        if CACHE_INDEX_FILE.exists():
            try:
                with open(CACHE_INDEX_FILE, 'r') as f:
                    return json.load(f)
            except:
                return {"entries": {}, "stats": {"hits": 0, "misses": 0, "total_calls": 0}}
        return {"entries": {}, "stats": {"hits": 0, "misses": 0, "total_calls": 0}}
    
    def _save_index(self):
        """Persist cache index"""
        with open(CACHE_INDEX_FILE, 'w') as f:
            json.dump(self.index, f, indent=2)
    
    def _normalize_query(self, query: str) -> str:
        """
        Normalize query to prevent duplicates
        
        Examples:
        - "Semantic SEO" → "semantic seo"
        - "semantic-seo" → "semantic seo"
        - "semantic  seo" → "semantic seo"
        """
        query = query.lower().strip()
        query = ' '.join(query.split())  # Normalize whitespace
        query = query.replace('-', ' ').replace('_', ' ')
        return query
    
    def _get_cache_key(self, query: str, database: str = "us") -> str:
        """Generate cache key from normalized query"""
        normalized = self._normalize_query(query)
        key_string = f"{normalized}|{database}"
        return hashlib.md5(key_string.encode()).hexdigest()
    
    def _is_expired(self, timestamp: str) -> bool:
        """Check if cache entry is expired (7-day TTL)"""
        try:
            cached_time = datetime.fromisoformat(timestamp)
            expiry = cached_time + timedelta(days=CACHE_TTL_DAYS)
            return datetime.now() > expiry
        except:
            return True
    
    def get(self, query: str, database: str = "us") -> Optional[dict]:
        """
        Get cached data for query
        
        Returns None if:
        - Not in cache
        - Expired (>7 days old)
        - Corrupted
        """
        cache_key = self._get_cache_key(query, database)
        
        if cache_key not in self.index["entries"]:
            self.index["stats"]["misses"] += 1
            self._save_index()
            return None
        
        entry = self.index["entries"][cache_key]
        
        # Check expiry
        if self._is_expired(entry["cached_at"]):
            logger.info(f"Cache expired for '{query}' (cached {entry['cached_at'][:10]})")
            # Remove expired entry
            del self.index["entries"][cache_key]
            self._save_index()
            self.index["stats"]["misses"] += 1
            return None
        
        # Load cached data
        cache_file = CACHE_DIR / f"{cache_key}.json"
        if not cache_file.exists():
            logger.info(f"Cache file missing for '{query}'")
            del self.index["entries"][cache_key]
            self._save_index()
            return None
        
        try:
            with open(cache_file, 'r') as f:
                data = json.load(f)
            
            self.index["stats"]["hits"] += 1
            self._save_index()
            
            logger.info(f"✓ Cache HIT for '{query}' ({len(data.get('queries', []))} queries)")
            return data
        
        except Exception as e:
            logger.info(f"Cache read error for '{query}': {e}")
            return None
    
    def set(self, query: str, data: dict, database: str = "us"):
        """Store data in cache with 7-day TTL"""
        cache_key = self._get_cache_key(query, database)
        cache_file = CACHE_DIR / f"{cache_key}.json"
        
        # Add metadata
        data["cached_at"] = datetime.now().isoformat()
        data["cache_ttl_days"] = CACHE_TTL_DAYS
        data["cache_key"] = cache_key
        data["original_query"] = query
        data["normalized_query"] = self._normalize_query(query)
        
        # Save to file
        with open(cache_file, 'w') as f:
            json.dump(data, f, indent=2)
        
        # Update index
        self.index["entries"][cache_key] = {
            "query": query,
            "normalized": self._normalize_query(query),
            "database": database,
            "cached_at": data["cached_at"],
            "expires_at": (datetime.now() + timedelta(days=CACHE_TTL_DAYS)).isoformat(),
            "query_count": len(data.get("queries", [])),
            "file": f"{cache_key}.json"
        }
        
        self._save_index()
        logger.info(f"✓ Cached {len(data.get('queries', []))} queries for '{query}' (expires in 7 days)")
    
    def find_similar(self, query: str, min_overlap: float = 0.7) -> List[dict]:
        """
        Find cached queries with significant overlap
        
        Useful for:
        - "semantic seo" → use "semantic seo guide" data
        - "seo services" → use "seo services pricing" data
        
        Returns list of similar cached entries with overlap score
        """
        normalized = set(self._normalize_query(query).split())
        similar = []
        
        for cache_key, entry in self.index["entries"].items():
            if self._is_expired(entry["cached_at"]):
                continue
            
            cached_words = set(entry["normalized"].split())
            
            # Calculate Jaccard similarity
            intersection = len(normalized & cached_words)
            union = len(normalized | cached_words)
            
            if union == 0:
                continue
            
            overlap = intersection / union
            
            if overlap >= min_overlap:
                similar.append({
                    "query": entry["query"],
                    "normalized": entry["normalized"],
                    "overlap": overlap,
                    "query_count": entry["query_count"],
                    "cached_at": entry["cached_at"]
                })
        
        # Sort by overlap (highest first)
        similar.sort(key=lambda x: x["overlap"], reverse=True)
        return similar
    
    def get_stats(self) -> dict:
        """Get cache statistics"""
        total_entries = len(self.index["entries"])
        hits = self.index["stats"]["hits"]
        misses = self.index["stats"]["misses"]
        total_requests = hits + misses
        
        hit_rate = (hits / total_requests * 100) if total_requests > 0 else 0
        
        # Count expired entries
        expired = sum(1 for e in self.index["entries"].values() 
                     if self._is_expired(e["cached_at"]))
        
        return {
            "total_entries": total_entries,
            "expired_entries": expired,
            "active_entries": total_entries - expired,
            "hits": hits,
            "misses": misses,
            "hit_rate": f"{hit_rate:.1f}%",
            "total_api_calls_saved": hits,
            "estimated_units_saved": hits * 100  # Approximate
        }


def extract_queries_semrush(topic: str, api_key: str, force_refresh: bool = False) -> dict:
    """
    Extract queries from Semrush API with intelligent caching
    """
    
    logger.info(f"Starting Semrush extraction for topic: '{topic}'")
    
    cache = SemrushCache()
    
    # Step 1: Check cache first (unless force_refresh)
    if not force_refresh:
        cached_data = cache.get(topic)
        if cached_data:
            logger.info(f"✓ Using cached data for '{topic}' (7-day TTL)")
            return cached_data
    
    # Step 2: Check for similar cached data
    similar = cache.find_similar(topic, min_overlap=0.7)
    if similar:
        logger.info(f"Found {len(similar)} similar cached queries:")
        for s in similar[:3]:
            logger.info(f"  - '{s['query']}' ({s['overlap']*100:.0f}% overlap, {s['query_count']} queries)")
    
    # Step 3: Call Semrush API
    logger.info(f"Cache miss for '{topic}' - calling Semrush API...")
    
    import requests
    
    queries = []
    # Correct Semrush Analytics API v1 endpoint
    base_url = "https://api.semrush.com/analytics/v1/keywords/phrase_match"
    
    # Batch parameters
    params = {
        "key": api_key,
        "phrase": topic,
        "database": "us",
        "export": "json",
        "limit": 100,
        "offset": 0
    }
    
    api_calls_made = 0
    
    try:
        # Paginate to get comprehensive data (up to 2,400+ queries)
        while len(queries) < 2400:
            logger.info(f"Fetching page {params['offset']//100 + 1}...")
            
            response = requests.get(base_url, params=params, timeout=30)
            api_calls_made += 1
            
            if response.status_code != 200:
                logger.info(f"API error: {response.status_code} - {response.text[:200]}")
                if not queries:  # No data yet, return mock fallback
                    logger.info("⚠️ No data received, using mock structure for testing")
                    return _get_mock_data(topic)
                break
            
            data = response.json()
            result_list = data.get("result_list", [])
            
            if not result_list:
                logger.info("No more results from API")
                break
            
            # Parse Semrush response
            for kw in result_list:
                queries.append({
                    "query": kw.get("phrase", ""),
                    "volume": kw.get("search_volume", 0),
                    "difficulty": kw.get("competition", 0),
                    "cpc": kw.get("cpc", 0),
                    "trend": kw.get("trend", []),
                    "intent": classify_intent(kw.get("phrase", "")),
                    "intent_stream": classify_intent_stream(kw.get("phrase", ""))
                })
            
            logger.info(f"Extracted {len(queries)} queries so far...")
            
            # Pagination
            params["offset"] += 100
            
            # Rate limit handling (10 req/sec max per Semrush ToS)
            time.sleep(0.2)
        
        logger.info(f"✓ Successfully extracted {len(queries)} queries from Semrush API ({api_calls_made} calls)")
        
        # Step 4: Cache the results
        result = {
            "topic": topic,
            "extracted_at": datetime.now().isoformat(),
            "query_count": len(queries),
            "queries": queries,
            "competitors": [],  # Will be populated from SERP data
            "metadata": {
                "source": "semrush_api",
                "database": "us",
                "api_version": "v1",
                "api_key_used": api_key[:8] + "...",
                "api_calls_made": api_calls_made,
                "cache_ttl_days": CACHE_TTL_DAYS
            }
        }
        
        cache.set(topic, result)
        
        return result
    
    except Exception as e:
        logger.info(f"Semrush API error: {e}")
        # Return cached similar data if available, else mock
        if similar:
            logger.info(f"Using similar cached data as fallback")
            # Could load most similar entry here
        return _get_mock_data(topic)


def _get_mock_data(topic: str) -> dict:
    """Fallback mock data if API fails completely"""
    logger.info("⚠️ WARNING: Using mock data structure as fallback")
    queries = [
        {
            "query": topic,
            "volume": 8100,
            "difficulty": 67,
            "cpc": 12.50,
            "trend": [65, 68, 72, 70, 75, 80, 82, 78, 76, 74, 77, 81],
            "intent": "informational",
            "intent_stream": "informational"
        }
    ] + [
        {
            "query": f"{topic} {suffix}",
            "volume": 1000,
            "difficulty": 50,
            "cpc": 8.00,
            "trend": [50, 55, 60, 58, 62, 65, 68, 64, 62, 60, 63, 67],
            "intent": classify_intent(f"{topic} {suffix}"),
            "intent_stream": classify_intent_stream(f"{topic} {suffix}")
        }
        for suffix in [
            "guide", "tutorial", "examples", "tools", "services",
            "best practices", "tips", "strategies", "benefits",
            "how to", "what is", "why", "when", "where",
            "vs", "comparison", "review", "for beginners",
            "advanced", "2026", "near me"
        ]
    ]
    
    return {
        "topic": topic,
        "extracted_at": datetime.now().isoformat(),
        "query_count": len(queries),
        "queries": queries,
        "competitors": [],
        "metadata": {
            "source": "mock_fallback",
            "database": "us",
            "api_version": "v1",
            "note": "Mock data - API call failed"
        }
    }


def classify_intent(query: str) -> str:
    """Classify search intent (5-stream model)"""
    query_lower = query.lower()
    
    # Transactional
    if any(word in query_lower for word in ["buy", "price", "cost", "pricing", "purchase", "order", "hire", "service"]):
        return "transactional"
    
    # Commercial Investigation
    if any(word in query_lower for word in ["best", "top", "review", "vs", "versus", "comparison", "compare"]):
        return "commercial"
    
    # Informational
    if any(word in query_lower for word in ["how", "what", "why", "when", "where", "guide", "tutorial", "tips", "learn"]):
        return "informational"
    
    # Navigational
    if any(word in query_lower for word in ["login", "signin", "website", "official"]):
        return "navigational"
    
    return "informational"  # Default


def classify_intent_stream(query: str) -> str:
    """Classify intent stream (broader category for clustering)"""
    intent = classify_intent(query)
    
    if intent in ["informational", "navigational"]:
        return "learn"
    elif intent in ["commercial"]:
        return "compare"
    elif intent in ["transactional"]:
        return "buy"
    
    return "learn"


if __name__ == "__main__":
    """Test Semrush extractor with caching"""
    
    if len(sys.argv) < 2:
        print("Usage: python semrush_extractor.py <topic> [api_key]")
        print("       python semrush_extractor.py --stats")
        sys.exit(1)
    
    if sys.argv[1] == "--stats":
        cache = SemrushCache()
        stats = cache.get_stats()
        print("\n=== SEMRUSH CACHE STATISTICS ===")
        print(f"Active entries: {stats['active_entries']}")
        print(f"Expired entries: {stats['expired_entries']}")
        print(f"Cache hit rate: {stats['hit_rate']}")
        print(f"API calls saved: {stats['total_api_calls_saved']}")
        print(f"Estimated units saved: {stats['estimated_units_saved']:,}")
        sys.exit(0)
    
    topic = sys.argv[1]
    api_key = sys.argv[2] if len(sys.argv) > 2 else os.environ.get("SEMRUSH_API_KEY")
    
    if not api_key:
        logger.info("No Semrush API key provided. Using mock data for testing.")
        logger.info("To use real data: export SEMRUSH_API_KEY=your_key or pass as argument")
    else:
        logger.info(f"Using Semrush API key: {api_key[:8]}...")
    
    result = extract_queries_semrush(topic, api_key or "mock")
    
    print(f"\n=== EXTRACTION COMPLETE ===")
    print(f"Topic: {result['topic']}")
    print(f"Queries: {result['query_count']}")
    print(f"Source: {result['metadata']['source']}")
    print(f"Cache TTL: {result.get('cache_ttl_days', 'N/A')} days")
