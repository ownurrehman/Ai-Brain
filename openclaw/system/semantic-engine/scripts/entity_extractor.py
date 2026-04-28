#!/usr/bin/env python3
"""
Entity Extraction with PPR Classification

Extracts entities from SERP data and classifies them using Koray's PPR framework:
- Purpose (P): What the entity does/achieves
- Property (P): Attributes/characteristics
- Relationship (R): Connections to other entities

Target: 180+ entities per topic with 9-frame coverage
"""

import re
from typing import Dict, List, Set, Tuple
from pathlib import Path
import sys
from collections import Counter

sys.path.insert(0, str(Path(__file__).parent.parent))

from config.logger import get_logger
logger = get_logger(__name__)
log = logger.info


class EntityExtractor:
    """
    Extract and classify entities using PPR framework
    
    PPR Classes:
    - Purpose (P): Goals, functions, outcomes (e.g., "optimization", "ranking")
    - Property (P): Attributes, qualities (e.g., "semantic", "relevant", "high-quality")
    - Relationship (R): Connections (e.g., "part of", "related to", "subset of")
    """
    
    # Entity patterns for extraction
    ENTITY_PATTERNS = {
        "noun_phrases": r'\b([A-Z][a-z]+(?:\s+[A-Z][a-z]+)+)\b',  # Capitalized phrases
        "technical_terms": r'\b([a-z]+(?:\s+[a-z]+){1,3})\b',  # Multi-word terms
        "concepts": r'\b(\w+(?:ion|ity|ness|ment|ance|ence))\b',  # Abstract concepts
    }
    
    # PPR classification keywords
    PPR_KEYWORDS = {
        "Purpose": [
            "optimization", "ranking", "improvement", "enhancement", "strategy",
            "goal", "objective", "target", "purpose", "function", "role",
            "achievement", "outcome", "result", "effect", "impact",
            "increase", "boost", "maximize", "improve", "optimize"
        ],
        "Property": [
            "semantic", "relevant", "related", "similar", "contextual",
            "high-quality", "authoritative", "trustworthy", "comprehensive",
            "accurate", "precise", "specific", "general", "broad", "narrow",
            "primary", "secondary", "main", "core", "essential", "critical"
        ],
        "Relationship": [
            "part", "component", "element", "aspect", "feature", "attribute",
            "connection", "link", "association", "correlation", "relationship",
            "subset", "category", "type", "kind", "class", "group",
            "parent", "child", "sibling", "ancestor", "descendant"
        ]
    }
    
    # Stop words to filter out
    STOP_WORDS = {
        "the", "a", "an", "and", "or", "but", "in", "on", "at", "to", "for",
        "of", "with", "by", "from", "as", "is", "was", "are", "were", "been",
        "be", "have", "has", "had", "do", "does", "did", "will", "would",
        "could", "should", "may", "might", "must", "shall", "can", "need",
        "this", "that", "these", "those", "it", "its", "they", "them",
        "what", "which", "who", "whom", "whose", "when", "where", "why", "how"
    }
    
    def __init__(self):
        self.compiled_patterns = {
            name: re.compile(pattern) for name, pattern in self.ENTITY_PATTERNS.items()
        }
    
    def extract_from_serp(self, serp_data: Dict) -> List[Dict]:
        """
        Extract entities from SERP data
        
        Args:
            serp_data: Dict with 'organic' results from OpenSERP
        
        Returns:
            List of entities with PPR classification
        """
        entities = []
        entity_set = set()  # Track unique entities
        
        # Extract from titles
        for result in serp_data.get("organic", []):
            title = result.get("title", "")
            description = result.get("description", "")
            
            # Extract from title
            title_entities = self._extract_from_text(title)
            for entity in title_entities:
                if entity["text"] not in entity_set:
                    entity_set.add(entity["text"])
                    entity["source"] = "title"
                    entity["frequency"] = 1
                    entities.append(entity)
                else:
                    # Update existing entity
                    for e in entities:
                        if e["text"] == entity["text"]:
                            e["frequency"] += 1
                            if "title" not in e["sources"]:
                                e["sources"].append("title")
            
            # Extract from description
            desc_entities = self._extract_from_text(description)
            for entity in desc_entities:
                if entity["text"] not in entity_set:
                    entity_set.add(entity["text"])
                    entity["source"] = "description"
                    entity["frequency"] = 1
                    entities.append(entity)
                else:
                    for e in entities:
                        if e["text"] == entity["text"]:
                            e["frequency"] += 1
                            if "description" not in e["sources"]:
                                e["sources"].append("description")
        
        # Sort by frequency (most common first)
        entities.sort(key=lambda x: x["frequency"], reverse=True)
        
        log(f"Extracted {len(entities)} unique entities from SERP")
        return entities
    
    def _extract_from_text(self, text: str) -> List[Dict]:
        """Extract entities from text with PPR classification"""
        entities = []
        
        # Extract noun phrases (capitalized)
        for match in self.compiled_patterns["noun_phrases"].finditer(text):
            entity_text = match.group(1)
            if len(entity_text) > 3 and entity_text.lower() not in self.STOP_WORDS:
                entities.append({
                    "text": entity_text,
                    "type": "noun_phrase",
                    "ppr_class": self._classify_ppr(entity_text),
                    "sources": [],
                    "frequency": 0
                })
        
        # Extract technical terms
        for match in self.compiled_patterns["technical_terms"].finditer(text):
            entity_text = match.group(1)
            words = entity_text.split()
            
            # Filter: 2-4 words, not stop words
            if (2 <= len(words) <= 4 and 
                entity_text.lower() not in self.STOP_WORDS and
                len(entity_text) > 5):
                entities.append({
                    "text": entity_text,
                    "type": "technical_term",
                    "ppr_class": self._classify_ppr(entity_text),
                    "sources": [],
                    "frequency": 0
                })
        
        # Extract abstract concepts
        for match in self.compiled_patterns["concepts"].finditer(text):
            entity_text = match.group(1)
            if len(entity_text) > 4 and entity_text.lower() not in self.STOP_WORDS:
                entities.append({
                    "text": entity_text,
                    "type": "concept",
                    "ppr_class": self._classify_ppr(entity_text),
                    "sources": [],
                    "frequency": 0
                })
        
        return entities
    
    def _classify_ppr(self, entity_text: str) -> str:
        """
        Classify entity into PPR category
        
        Returns: "Purpose", "Property", or "Relationship"
        """
        entity_lower = entity_text.lower()
        scores = {"Purpose": 0, "Property": 0, "Relationship": 0}
        
        # Score against each PPR category
        for category, keywords in self.PPR_KEYWORDS.items():
            for keyword in keywords:
                if keyword in entity_lower:
                    scores[category] += 1
        
        # Return highest scoring category
        max_category = max(scores, key=scores.get)
        
        # If no clear match, default based on word patterns
        if scores[max_category] == 0:
            if entity_text.endswith(("ion", "ment", "ance", "ence")):
                return "Purpose"  # Usually action/outcome nouns
            elif entity_text.endswith(("ity", "ness", "al", "ic")):
                return "Property"  # Usually descriptive adjectives
            else:
                return "Relationship"  # Default to relationship
        
        return max_category
    
    def get_ppr_distribution(self, entities: List[Dict]) -> Dict[str, int]:
        """Get count distribution of PPR classes"""
        distribution = {"Purpose": 0, "Property": 0, "Relationship": 0}
        for entity in entities:
            distribution[entity["ppr_class"]] += 1
        return distribution
    
    def filter_by_relevance(self, entities: List[Dict], min_frequency: int = 2) -> List[Dict]:
        """Filter entities by minimum frequency threshold"""
        return [e for e in entities if e["frequency"] >= min_frequency]
    
    def get_top_entities(self, entities: List[Dict], limit: int = 50) -> List[Dict]:
        """Get top N entities by frequency"""
        return entities[:limit]


class FrameCoverageAnalyzer:
    """
    9-Frame Coverage Analysis
    
    Semantic frames that must be covered for complete topic coverage:
    1. Definition Frame (What is it?)
    2. Purpose Frame (Why use it?)
    3. Process Frame (How does it work?)
    4. Component Frame (What parts?)
    5. Tool Frame (What tools?)
    6. Benefit Frame (What advantages?)
    7. Challenge Frame (What difficulties?)
    8. Example Frame (What examples?)
    9. Comparison Frame (What alternatives?)
    """
    
    FRAMES = {
        "definition": {
            "question": "What is it?",
            "keywords": ["what is", "definition", "meaning", "concept", "understanding"],
            "entities": []
        },
        "purpose": {
            "question": "Why use it?",
            "keywords": ["why", "benefits", "advantages", "importance", "goals"],
            "entities": []
        },
        "process": {
            "question": "How does it work?",
            "keywords": ["how to", "process", "steps", "method", "technique", "workflow"],
            "entities": []
        },
        "component": {
            "question": "What parts?",
            "keywords": ["components", "elements", "parts", "features", "aspects"],
            "entities": []
        },
        "tool": {
            "question": "What tools?",
            "keywords": ["tools", "software", "platform", "system", "technology"],
            "entities": []
        },
        "benefit": {
            "question": "What advantages?",
            "keywords": ["benefits", "advantages", "improvements", "results", "outcomes"],
            "entities": []
        },
        "challenge": {
            "question": "What difficulties?",
            "keywords": ["challenges", "problems", "issues", "difficulties", "obstacles"],
            "entities": []
        },
        "example": {
            "question": "What examples?",
            "keywords": ["examples", "cases", "instances", "scenarios", "use cases"],
            "entities": []
        },
        "comparison": {
            "question": "What alternatives?",
            "keywords": ["vs", "versus", "comparison", "alternative", "different"],
            "entities": []
        }
    }
    
    def analyze_coverage(self, entities: List[Dict], queries: List[str]) -> Dict:
        """
        Analyze 9-frame coverage
        
        Returns coverage report with gaps identified
        """
        coverage = {frame: {"covered": False, "entities": [], "score": 0.0} 
                   for frame in self.FRAMES.keys()}
        
        # Check each frame for coverage
        for frame, config in self.FRAMES.items():
            # Check if queries contain frame keywords
            for query in queries:
                query_lower = query.lower()
                for keyword in config["keywords"]:
                    if keyword in query_lower:
                        coverage[frame]["covered"] = True
                        coverage[frame]["score"] += 0.3
            
            # Check if entities relate to frame
            for entity in entities:
                entity_lower = entity["text"].lower()
                for keyword in config["keywords"]:
                    if keyword in entity_lower:
                        coverage[frame]["entities"].append(entity["text"])
                        coverage[frame]["score"] += 0.1
            
            # Normalize score
            coverage[frame]["score"] = min(coverage[frame]["score"], 1.0)
        
        # Calculate overall coverage
        total_covered = sum(1 for f in coverage.values() if f["covered"])
        overall_coverage = total_covered / 9.0
        
        return {
            "frames": coverage,
            "overall_coverage": overall_coverage,
            "frames_covered": total_covered,
            "frames_missing": 9 - total_covered,
            "gaps": [frame for frame, data in coverage.items() if not data["covered"]]
        }
    
    def get_recommendations(self, coverage_report: Dict) -> List[str]:
        """Generate content recommendations based on gaps"""
        recommendations = []
        
        for frame in coverage_report["gaps"]:
            frame_config = self.FRAMES[frame]
            recommendations.append(
                f"Add {frame} content: Address '{frame_config['question']}' "
                f"using keywords: {', '.join(frame_config['keywords'][:3])}"
            )
        
        return recommendations


if __name__ == "__main__":
    """Test entity extraction and PPR classification"""
    
    # Mock SERP data for testing
    mock_serp = {
        "organic": [
            {"title": "Semantic SEO: Complete Guide to Optimization", "description": "Learn semantic SEO strategies and techniques for better rankings"},
            {"title": "What is Semantic SEO? Definition and Benefits", "description": "Understanding semantic search optimization and its importance"},
            {"title": "Best Semantic SEO Tools and Software", "description": "Compare top semantic SEO tools for content optimization"},
            {"title": "Semantic SEO vs Traditional SEO: Key Differences", "description": "Comparison of semantic and keyword-based SEO approaches"},
            {"title": "How to Implement Semantic SEO Strategy", "description": "Step-by-step process for semantic SEO implementation"}
        ]
    }
    
    extractor = EntityExtractor()
    analyzer = FrameCoverageAnalyzer()
    
    print("=== ENTITY EXTRACTION TEST ===\n")
    
    entities = extractor.extract_from_serp(mock_serp)
    
    print(f"Total entities extracted: {len(entities)}\n")
    
    # Show PPR distribution
    ppr_dist = extractor.get_ppr_distribution(entities)
    print("PPR Distribution:")
    for ppr, count in ppr_dist.items():
        print(f"  {ppr}: {count} ({count/len(entities)*100:.0f}%)")
    
    # Show top entities
    print("\nTop 10 Entities:")
    for entity in extractor.get_top_entities(entities, 10):
        print(f"  - {entity['text']} ({entity['ppr_class']}, freq: {entity['frequency']})")
    
    # Test frame coverage
    print("\n=== FRAME COVERAGE ANALYSIS ===\n")
    
    test_queries = [
        "what is semantic seo",
        "semantic seo benefits",
        "how to do semantic seo",
        "best semantic seo tools",
        "semantic seo vs traditional seo"
    ]
    
    coverage = analyzer.analyze_coverage(entities, test_queries)
    
    print(f"Overall Coverage: {coverage['overall_coverage']*100:.0f}%")
    print(f"Frames Covered: {coverage['frames_covered']}/9")
    print(f"Gaps: {', '.join(coverage['gaps']) if coverage['gaps'] else 'None'}")
    
    print("\nFrame Breakdown:")
    for frame, data in coverage["frames"].items():
        status = "✓" if data["covered"] else "✗"
        print(f"  {status} {frame}: {data['score']*100:.0f}%")
    
    recommendations = analyzer.get_recommendations(coverage)
    if recommendations:
        print("\nRecommendations:")
        for rec in recommendations:
            print(f"  - {rec}")
