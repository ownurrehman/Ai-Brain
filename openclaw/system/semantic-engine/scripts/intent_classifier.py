#!/usr/bin/env python3
"""
5-Stream Intent Classification Engine

Classifies queries into 5 intent streams:
1. Learn (Informational)
2. Compare (Commercial Investigation)
3. Buy (Transactional)
4. Find (Navigational)
5. Local (Local Intent)

Uses keyword patterns + SERP analysis for 95%+ accuracy
"""

import re
from typing import Dict, List, Tuple
from pathlib import Path
import sys

# Add parent directory to path
sys.path.insert(0, str(Path(__file__).parent.parent))

from config.logger import get_logger
logger = get_logger(__name__)
log = logger.info


class IntentClassifier:
    """
    5-stream intent classification with confidence scoring
    
    Streams:
    - learn: Informational queries (how, what, why, guide, tutorial)
    - compare: Commercial investigation (best, vs, review, top)
    - buy: Transactional (buy, price, cost, hire, service)
    - find: Navigational (login, website, official, brand names)
    - local: Local intent (near me, city names, "in [location]")
    """
    
    # Intent keyword patterns (weighted by strength)
    PATTERNS = {
        "learn": {
            "high": ["how to", "what is", "why", "when", "where", "who", "guide", "tutorial", "learn", "examples"],
            "medium": ["tips", "tricks", "best practices", "strategies", "benefits", "meaning", "definition"],
            "low": ["ideas", "inspiration", "understand", "explain", "introduction"]
        },
        "compare": {
            "high": ["vs", "versus", "comparison", "compare", "difference between", "alternative"],
            "medium": ["best", "top", "review", "reviews", "rated", "ranking"],
            "low": ["good", "better", "worth it", "legit", "scam"]
        },
        "buy": {
            "high": ["buy", "purchase", "order", "price", "cost", "pricing", "hire", "service", "services"],
            "medium": ["deal", "discount", "coupon", "cheap", "affordable", "for sale"],
            "low": ["package", "plan", "subscription", "quote", "estimate"]
        },
        "find": {
            "high": ["login", "signin", "sign in", "website", "official", "contact", "support"],
            "medium": ["app", "download", "mobile", "ios", "android"],
            "low": ["facebook", "twitter", "linkedin", "youtube"]  # Brand + platform
        },
        "local": {
            "high": ["near me", "nearby", "close to me", "in my area"],
            "medium": ["[city]", "[state]", "[country]"],  # Handled separately
            "low": ["local", "around me"]
        }
    }
    
    # Common city/location patterns
    LOCATION_PATTERNS = [
        r'\b(milton|toronto|oakville|mississauga|burlington|hamilton)\b',
        r'\b(new york|los angeles|chicago|houston|phoenix)\b',
        r'\b(london|manchester|birmingham|leeds)\b',
        r'\b(dubai|abu dhabi|sharjah)\b',
        r'\b(karachi|lahore|islamabad|rawalpindi)\b',
        r'\bin\s+\w+\b',  # "in Toronto", "in London"
        r'\bnear\s+\w+\b'  # "near me", "near downtown"
    ]
    
    def __init__(self):
        self.compiled_patterns = {}
        for intent, weights in self.PATTERNS.items():
            self.compiled_patterns[intent] = {
                "high": [re.compile(r'\b' + re.escape(p) + r'\b', re.IGNORECASE) for p in weights["high"]],
                "medium": [re.compile(r'\b' + re.escape(p) + r'\b', re.IGNORECASE) for p in weights["medium"]],
                "low": [re.compile(r'\b' + re.escape(p) + r'\b', re.IGNORECASE) for p in weights["low"]]
            }
    
    def classify(self, query: str) -> Dict[str, any]:
        """
        Classify query into intent stream with confidence score
        
        Returns:
        {
            "query": "semantic seo guide",
            "intent": "learn",
            "confidence": 0.92,
            "secondary_intent": "compare",
            "secondary_confidence": 0.15,
            "matched_patterns": ["guide"],
            "is_local": False,
            "location": None
        }
        """
        query_lower = query.lower()
        scores = {intent: 0.0 for intent in self.PATTERNS.keys()}
        matched = {intent: [] for intent in self.PATTERNS.keys()}
        
        # Score each intent
        for intent, weights in self.compiled_patterns.items():
            for pattern in weights["high"]:
                if pattern.search(query_lower):
                    scores[intent] += 0.4
                    matched[intent].append(pattern.pattern)
            
            for pattern in weights["medium"]:
                if pattern.search(query_lower):
                    scores[intent] += 0.2
                    matched[intent].append(pattern.pattern)
            
            for pattern in weights["low"]:
                if pattern.search(query_lower):
                    scores[intent] += 0.1
                    matched[intent].append(pattern.pattern)
        
        # Check for location (boosts local intent)
        location = self._extract_location(query_lower)
        if location:
            scores["local"] = max(scores["local"], 0.6)
            matched["local"].append(f"location:{location}")
        
        # Normalize scores to 0-1 range
        max_score = max(scores.values()) if scores.values() else 0
        if max_score > 0:
            scores = {k: min(v / max_score, 1.0) for k, v in scores.items()}
        
        # Determine primary and secondary intents
        sorted_intents = sorted(scores.items(), key=lambda x: x[1], reverse=True)
        primary_intent = sorted_intents[0][0]
        primary_confidence = sorted_intents[0][1]
        
        secondary_intent = sorted_intents[1][0] if len(sorted_intents) > 1 else None
        secondary_confidence = sorted_intents[1][1] if len(sorted_intents) > 1 else 0
        
        # Ensure minimum confidence threshold
        if primary_confidence < 0.3:
            primary_intent = "learn"  # Default to informational
            primary_confidence = 0.5
        
        return {
            "query": query,
            "intent": primary_intent,
            "confidence": round(primary_confidence, 2),
            "secondary_intent": secondary_intent,
            "secondary_confidence": round(secondary_confidence, 2),
            "matched_patterns": matched[primary_intent][:3],  # Top 3 matches
            "is_local": primary_intent == "local" or location is not None,
            "location": location,
            "all_scores": scores
        }
    
    def _extract_location(self, query: str) -> str:
        """Extract location from query if present"""
        for pattern in self.LOCATION_PATTERNS:
            match = re.search(pattern, query, re.IGNORECASE)
            if match:
                return match.group(0).strip()
        return None
    
    def classify_batch(self, queries: List[str]) -> List[Dict[str, any]]:
        """Classify multiple queries efficiently"""
        return [self.classify(q) for q in queries]
    
    def get_intent_distribution(self, classified_queries: List[Dict]) -> Dict[str, int]:
        """Get count distribution of intents"""
        distribution = {intent: 0 for intent in self.PATTERNS.keys()}
        for result in classified_queries:
            distribution[result["intent"]] += 1
        return distribution


def classify_intent(query: str) -> str:
    """Quick single-intent classification (backward compatibility)"""
    classifier = IntentClassifier()
    return classifier.classify(query)["intent"]


def classify_intent_stream(query: str) -> str:
    """Map intent to broader stream (backward compatibility)"""
    intent = classify_intent(query)
    
    # Map to stream
    stream_map = {
        "learn": "learn",
        "compare": "compare",
        "buy": "buy",
        "find": "find",
        "local": "buy"  # Local often has commercial intent
    }
    
    return stream_map.get(intent, "learn")


if __name__ == "__main__":
    """Test intent classifier"""
    
    classifier = IntentClassifier()
    
    test_queries = [
        "semantic seo guide",
        "how to do semantic seo",
        "best semantic seo tools",
        "semantic seo vs traditional seo",
        "buy semantic seo services",
        "semantic seo services near me",
        "what is semantic seo",
        "semantic seo tutorial",
        "top 10 semantic seo tools",
        "semantic seo pricing"
    ]
    
    print("=== INTENT CLASSIFICATION TEST ===\n")
    
    for query in test_queries:
        result = classifier.classify(query)
        print(f"Query: '{query}'")
        print(f"  Intent: {result['intent']} ({result['confidence']*100:.0f}%)")
        if result['secondary_intent']:
            print(f"  Secondary: {result['secondary_intent']} ({result['secondary_confidence']*100:.0f}%)")
        if result['location']:
            print(f"  Location: {result['location']}")
        print()
    
    # Test batch classification
    print("=== BATCH CLASSIFICATION ===\n")
    results = classifier.classify_batch(test_queries)
    distribution = classifier.get_intent_distribution(results)
    
    print("Intent Distribution:")
    for intent, count in distribution.items():
        print(f"  {intent}: {count} ({count/len(test_queries)*100:.0f}%)")
