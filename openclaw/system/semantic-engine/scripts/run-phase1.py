#!/usr/bin/env python3
"""
Phase 1: Research Layer Orchestrator

Coordinates:
1. Query extraction (Semrush API with 7-day cache OR mock data)
2. SERP data capture (OpenSERP with caching)
3. Intent classification (5-stream model)
4. Entity extraction (PPR classification)
5. Frame coverage analysis (9 frames)

Output: JSON report to reports/ directory
Target duration: 4-5 minutes for 100 queries
"""

import os
import sys
import json
import time
from datetime import datetime
from pathlib import Path
from typing import List, Dict

# Add parent directory to path
sys.path.insert(0, str(Path(__file__).parent.parent))

from config.logger import get_logger
logger = get_logger(__name__)
log = logger.info

# Import extraction modules
from scripts.semrush_extractor import extract_queries_semrush, SemrushCache
from scripts.openserp_fetcher import fetch_serp
from scripts.intent_classifier import IntentClassifier
from scripts.entity_extractor import EntityExtractor, FrameCoverageAnalyzer


def run_phase1(topic: str, api_key: str = None, num_queries: int = 20) -> Dict:
    """
    Run complete Phase 1 research pipeline
    
    Args:
        topic: Target keyword/topic
        api_key: Semrush API key (optional, uses cache/mock if not provided)
        num_queries: Number of queries to process (default 20)
    
    Returns:
        Complete research report dict
    """
    
    start_time = time.time()
    log(f"{'='*60}")
    log(f"=== PHASE 1: RESEARCH LAYER ===")
    log(f"{'='*60}")
    log(f"Topic: {topic}")
    log(f"Target queries: {num_queries}")
    log(f"Timestamp: {datetime.now().isoformat()}")
    log("")
    
    report = {
        "topic": topic,
        "started_at": datetime.now().isoformat(),
        "steps": {},
        "summary": {}
    }
    
    # Step 1: Extract queries (Semrush with cache or mock)
    log("Step 1: Extracting queries...")
    step1_start = time.time()
    
    semrush_result = extract_queries_semrush(topic, api_key or "mock")
    
    # Limit to num_queries
    queries = semrush_result["queries"][:num_queries]
    
    step1_duration = time.time() - step1_start
    report["steps"]["query_extraction"] = {
        "duration_seconds": round(step1_duration, 2),
        "queries_extracted": len(queries),
        "source": semrush_result["metadata"]["source"],
        "cache_used": semrush_result["metadata"]["source"] != "mock_fallback",
        "api_calls_made": semrush_result["metadata"].get("api_calls_made", 0)
    }
    
    log(f"✓ Query extraction complete: {len(queries)} queries in {step1_duration:.1f}s")
    log(f"  Source: {semrush_result['metadata']['source']}")
    if semrush_result["metadata"]["source"] != "mock_fallback":
        log(f"  Cache TTL: 7 days")
    log("")
    
    # Step 2: Capture SERP data (OpenSERP)
    log("Step 2: Capturing SERP data...")
    step2_start = time.time()
    
    serp_results = []
    for i, query_data in enumerate(queries, 1):
        query = query_data["query"]
        log(f"  [{i}/{len(queries)}] Fetching SERP for '{query}'...")
        
        serp_data = fetch_serp(query, engine="google", limit=10)
        serp_results.append({
            "query": query,
            "serp_data": serp_data
        })
    
    step2_duration = time.time() - step2_start
    report["steps"]["serp_capture"] = {
        "duration_seconds": round(step2_duration, 2),
        "queries_analyzed": len(serp_results),
        "avg_time_per_query": round(step2_duration / len(serp_results), 2) if serp_results else 0
    }
    
    log(f"✓ SERP capture complete: {len(serp_results)} queries in {step2_duration:.1f}s")
    log(f"  Average: {step2_duration / len(serp_results):.1f}s per query")
    log("")
    
    # Step 3: Intent classification
    log("Step 3: Classifying search intent...")
    step3_start = time.time()
    
    classifier = IntentClassifier()
    intent_results = []
    
    for serp_result in serp_results:
        query = serp_result["query"]
        intent = classifier.classify(query)
        intent_results.append(intent)
        
        # Update query data with intent
        for q_data in queries:
            if q_data["query"] == query:
                q_data["intent"] = intent["intent"]
                q_data["intent_confidence"] = intent["confidence"]
                q_data["intent_stream"] = intent["intent"]
                break
    
    step3_duration = time.time() - step3_start
    
    # Get intent distribution
    intent_distribution = classifier.get_intent_distribution(intent_results)
    
    report["steps"]["intent_classification"] = {
        "duration_seconds": round(step3_duration, 2),
        "queries_classified": len(intent_results),
        "distribution": intent_distribution
    }
    
    log(f"✓ Intent classification complete: {len(intent_results)} queries in {step3_duration:.1f}s")
    log(f"  Distribution:")
    for intent, count in intent_distribution.items():
        log(f"    {intent}: {count} ({count/len(intent_results)*100:.0f}%)")
    log("")
    
    # Step 4: Entity extraction
    log("Step 4: Extracting entities...")
    step4_start = time.time()
    
    extractor = EntityExtractor()
    all_entities = []
    
    # Extract entities from all SERP results
    for serp_result in serp_results:
        serp_data = serp_result["serp_data"]
        entities = extractor.extract_from_serp(serp_data)
        all_entities.extend(entities)
    
    # Deduplicate entities
    unique_entities = {}
    for entity in all_entities:
        key = entity["text"].lower()
        if key not in unique_entities:
            unique_entities[key] = entity
        else:
            # Merge
            unique_entities[key]["frequency"] += entity["frequency"]
            unique_entities[key]["sources"] = list(set(unique_entities[key]["sources"] + entity["sources"]))
    
    entities_list = list(unique_entities.values())
    entities_list.sort(key=lambda x: x["frequency"], reverse=True)
    
    step4_duration = time.time() - step4_start
    
    # Get PPR distribution
    ppr_distribution = extractor.get_ppr_distribution(entities_list)
    
    report["steps"]["entity_extraction"] = {
        "duration_seconds": round(step4_duration, 2),
        "total_entities": len(entities_list),
        "ppr_distribution": ppr_distribution
    }
    
    log(f"✓ Entity extraction complete: {len(entities_list)} unique entities in {step4_duration:.1f}s")
    log(f"  PPR Distribution:")
    for ppr, count in ppr_distribution.items():
        log(f"    {ppr}: {count} ({count/len(entities_list)*100:.0f}%)")
    log("")
    
    # Step 5: Frame coverage analysis
    log("Step 5: Analyzing 9-frame coverage...")
    step5_start = time.time()
    
    analyzer = FrameCoverageAnalyzer()
    query_texts = [q["query"] for q in queries]
    
    coverage_report = analyzer.analyze_coverage(entities_list, query_texts)
    recommendations = analyzer.get_recommendations(coverage_report)
    
    step5_duration = time.time() - step5_start
    
    report["steps"]["frame_coverage"] = {
        "duration_seconds": round(step5_duration, 2),
        "overall_coverage": round(coverage_report["overall_coverage"], 2),
        "frames_covered": coverage_report["frames_covered"],
        "frames_missing": coverage_report["frames_missing"],
        "gaps": coverage_report["gaps"],
        "recommendations": recommendations
    }
    
    log(f"✓ Frame coverage analysis complete in {step5_duration:.1f}s")
    log(f"  Overall coverage: {coverage_report['overall_coverage']*100:.0f}%")
    log(f"  Frames covered: {coverage_report['frames_covered']}/9")
    if coverage_report["gaps"]:
        log(f"  Missing frames: {', '.join(coverage_report['gaps'])}")
    log("")
    
    # Generate summary
    total_duration = time.time() - start_time
    
    report["completed_at"] = datetime.now().isoformat()
    report["total_duration_seconds"] = round(total_duration, 2)
    
    report["summary"] = {
        "topic": topic,
        "queries_processed": len(queries),
        "entities_extracted": len(entities_list),
        "serp_analyses": len(serp_results),
        "intent_distribution": intent_distribution,
        "ppr_distribution": ppr_distribution,
        "frame_coverage": coverage_report["overall_coverage"],
        "data_source": semrush_result["metadata"]["source"],
        "api_efficiency": {
            "cache_used": semrush_result["metadata"]["source"] != "mock_fallback",
            "api_calls_made": semrush_result["metadata"].get("api_calls_made", 0),
            "cache_ttl_days": 7 if semrush_result["metadata"]["source"] != "mock_fallback" else 0
        }
    }
    
    # Add detailed data
    report["data"] = {
        "queries": queries,
        "serp_results": serp_results,
        "intent_results": intent_results,
        "entities": entities_list[:100],  # Top 100 only
        "coverage_frames": coverage_report["frames"]
    }
    
    log(f"{'='*60}")
    log(f"=== PHASE 1 COMPLETE ===")
    log(f"{'='*60}")
    log(f"Total duration: {total_duration:.1f}s")
    log(f"Queries processed: {len(queries)}")
    log(f"Entities extracted: {len(entities_list)}")
    log(f"Frame coverage: {coverage_report['overall_coverage']*100:.0f}%")
    log(f"Report saved to: reports/phase1-{topic.replace(' ', '-')}-{datetime.now().strftime('%Y%m%d-%H%M%S')}.json")
    log("")
    
    return report


def save_report(report: Dict, output_dir: str = None):
    """Save report to JSON file"""
    if output_dir is None:
        output_dir = Path(__file__).parent.parent / "reports"
    
    output_dir = Path(output_dir)
    output_dir.mkdir(parents=True, exist_ok=True)
    
    filename = f"phase1-{report['topic'].replace(' ', '-')}-{datetime.now().strftime('%Y%m%d-%H%M%S')}.json"
    output_path = output_dir / filename
    
    with open(output_path, 'w') as f:
        json.dump(report, f, indent=2)
    
    log(f"✓ Report saved: {output_path}")
    return output_path


if __name__ == "__main__":
    """Run Phase 1 pipeline"""
    
    if len(sys.argv) < 2:
        print("Usage: python run-phase1.py <topic> [api_key] [num_queries]")
        print("Example: python run-phase1.py 'semantic seo' YOUR_API_KEY 20")
        sys.exit(1)
    
    topic = sys.argv[1]
    api_key = sys.argv[2] if len(sys.argv) > 2 else os.environ.get("SEMRUSH_API_KEY")
    num_queries = int(sys.argv[3]) if len(sys.argv) > 3 else 20
    
    # Run Phase 1
    report = run_phase1(topic, api_key, num_queries)
    
    # Save report
    output_path = save_report(report)
    
    print(f"\n{'='*60}")
    print("PHASE 1 SUMMARY")
    print(f"{'='*60}")
    print(f"Topic: {report['summary']['topic']}")
    print(f"Queries: {report['summary']['queries_processed']}")
    print(f"Entities: {report['summary']['entities_extracted']}")
    print(f"SERP Analyses: {report['summary']['serp_analyses']}")
    print(f"Frame Coverage: {report['summary']['frame_coverage']*100:.0f}%")
    print(f"Duration: {report['total_duration_seconds']:.1f}s")
    print(f"Data Source: {report['summary']['data_source']}")
    print(f"Output: {output_path}")
