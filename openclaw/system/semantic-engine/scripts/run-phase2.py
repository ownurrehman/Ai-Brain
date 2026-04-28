#!/usr/bin/env python3
"""
Phase 2: Content Brief Generator

Takes Phase 1 research and generates:
1. Automated outline from entities + frames
2. 13-field brief specification
3. Content gap recommendations
4. Internal linking suggestions
5. Meta tags (title, description)
6. Featured image brief

Output: Complete brief ready for writer or AI content generation
"""

import os
import sys
import json
from datetime import datetime
from pathlib import Path
from typing import Dict, List

sys.path.insert(0, str(Path(__file__).parent.parent))

from config.logger import get_logger
logger = get_logger(__name__)
log = logger.info


class ContentBriefGenerator:
    """
    Generate complete content brief from Phase 1 research
    
    Follows Rank Ray SOP:
    - 2000-3500 words minimum
    - H1 + H2/H3 structure
    - 5-10 internal links
    - Featured image + H2 images
    - FAQ section (5-7 questions)
    - Meta title <60 chars, description <160 chars
    """
    
    def __init__(self, phase1_report: Dict):
        self.report = phase1_report
        self.topic = phase1_report["topic"]
        self.entities = phase1_report["data"]["entities"]
        self.queries = phase1_report["data"]["queries"]
        self.coverage = phase1_report["steps"]["frame_coverage"]
    
    def generate_brief(self) -> Dict:
        """Generate complete content brief"""
        
        log(f"Generating content brief for: '{self.topic}'")
        
        brief = {
            "topic": self.topic,
            "generated_at": datetime.now().isoformat(),
            "brief_version": "2.0",
            
            # Section 1: Meta Information
            "meta": self._generate_meta(),
            
            # Section 2: Content Structure
            "outline": self._generate_outline(),
            
            # Section 3: 13-Field Specification
            "specification": self._generate_specification(),
            
            # Section 4: Internal Linking
            "internal_links": self._generate_internal_links(),
            
            # Section 5: Image Brief
            "images": self._generate_image_brief(),
            
            # Section 6: FAQ Section
            "faqs": self._generate_faqs(),
            
            # Section 7: Content Recommendations
            "recommendations": self._generate_recommendations(),
            
            # Section 8: Quality Checklist
            "checklist": self._generate_checklist()
        }
        
        log(f"✓ Brief generated: {len(brief['outline']['sections'])} sections, {brief['meta']['target_word_count']} words")
        return brief
    
    def _generate_meta(self) -> Dict:
        """Generate meta title and description"""
        
        # Extract primary keyword
        primary_keyword = self.topic.title()
        
        # Meta title (under 60 chars)
        title_variations = [
            f"{primary_keyword} | Expert Services - Rank Ray",
            f"Professional {primary_keyword} - Rank Ray Agency",
            f"{primary_keyword}: Complete Guide & Services | Rank Ray",
            f"Best {primary_keyword} for Growth - Rank Ray"
        ]
        
        meta_title = title_variations[0]
        if len(meta_title) > 60:
            meta_title = f"{primary_keyword} Services - Rank Ray"
        
        # Meta description (under 160 chars, includes keyword + LSI + brand)
        lsi_terms = self._extract_lsi_terms()
        meta_description = f"Expert {primary_keyword} at Rank Ray. We specialize in {lsi_terms[0]}, {lsi_terms[1]}, and {lsi_terms[2]}. Boost rankings with semantic SEO. Get free audit."
        
        if len(meta_description) > 160:
            meta_description = meta_description[:157] + "..."
        
        return {
            "meta_title": meta_title,
            "meta_title_length": len(meta_title),
            "meta_description": meta_description,
            "meta_description_length": len(meta_description),
            "primary_keyword": primary_keyword,
            "target_word_count": 2500,
            "content_type": "service_page"
        }
    
    def _generate_outline(self) -> Dict:
        """Generate H1/H2/H3 outline from frame coverage"""
        
        sections = []
        
        # H1 (mandatory, one only)
        sections.append({
            "level": "H1",
            "text": f"{self.topic.title()}: Complete Guide & Professional Services",
            "word_count": "50-75",
            "notes": "Include primary keyword in first 100 words. Clarify intent, value, and scope quickly."
        })
        
        # H2 Sections based on frames
        frame_sections = [
            {
                "frame": "definition",
                "h2": "What is Semantic SEO?",
                "h3": ["Understanding Semantic Search", "How It Differs from Traditional SEO"],
                "entities": self._get_entities_for_frame("definition"),
                "word_count": "300-400"
            },
            {
                "frame": "benefit",
                "h2": "Why Semantic SEO Matters for Rankings",
                "h3": ["Key Benefits of Semantic Optimization", "Impact on Search Visibility"],
                "entities": self._get_entities_for_frame("benefit"),
                "word_count": "300-400"
            },
            {
                "frame": "process",
                "h2": "How Semantic SEO Works",
                "h3": ["The Semantic Optimization Process", "Step-by-Step Implementation"],
                "entities": self._get_entities_for_frame("process"),
                "word_count": "400-500"
            },
            {
                "frame": "component",
                "h2": "Core Components of Semantic SEO",
                "h3": ["Entity Optimization", "Topic Clusters", "Contextual Relevance"],
                "entities": self._get_entities_for_frame("component"),
                "word_count": "350-450"
            },
            {
                "frame": "tool",
                "h2": "Essential Semantic SEO Tools",
                "h3": ["AI-Powered Content Analysis", "Entity Extraction Tools", "Rank Tracking Software"],
                "entities": self._get_entities_for_frame("tool"),
                "word_count": "300-400"
            },
            {
                "frame": "example",
                "h2": "Semantic SEO Examples & Case Studies",
                "h3": ["Real-World Success Stories", "Before & After Comparisons"],
                "entities": self._get_entities_for_frame("example"),
                "word_count": "350-450"
            },
            {
                "frame": "comparison",
                "h2": "Semantic SEO vs Traditional Keyword SEO",
                "h3": ["Key Differences", "When to Use Each Approach"],
                "entities": self._get_entities_for_frame("comparison"),
                "word_count": "300-400"
            },
            {
                "frame": "challenge",
                "h2": "Common Semantic SEO Challenges",
                "h3": ["Implementation Obstacles", "How to Overcome Them"],
                "entities": self._get_entities_for_frame("challenge"),
                "word_count": "250-350"
            },
            {
                "frame": "buy",
                "h2": "Professional Semantic SEO Services",
                "h3": ["What We Offer", "Our Process", "Get Started Today"],
                "entities": self._get_entities_for_frame("buy"),
                "word_count": "300-400",
                "cta": True
            }
        ]
        
        for section in frame_sections:
            sections.append({
                "level": "H2",
                "text": section["h2"],
                "word_count": section["word_count"],
                "h3_sections": [
                    {"level": "H3", "text": h3, "entities": section["entities"][:5]}
                    for h3 in section["h3"]
                ],
                "target_entities": section["entities"],
                "notes": f"Cover {section['frame']} frame. Use entities: {', '.join(section['entities'][:3])}"
            })
        
        # FAQ Section (mandatory)
        sections.append({
            "level": "H2",
            "text": "Frequently Asked Questions",
            "word_count": "300-400",
            "faq_items": 7,
            "notes": "Include 5-7 FAQs with clear, concise answers"
        })
        
        return {
            "sections": sections,
            "total_sections": len(sections),
            "estimated_word_count": sum(
                int(s["word_count"].split("-")[1]) 
                for s in sections if "word_count" in s
            )
        }
    
    def _generate_specification(self) -> Dict:
        """Generate 13-field content specification"""
        
        return {
            "field_1_primary_keyword": self.topic,
            "field_2_secondary_keywords": self._extract_secondary_keywords(),
            "field_3_lsi_terms": self._extract_lsi_terms(),
            "field_4_search_intent": self._determine_primary_intent(),
            "field_5_target_audience": "SEO managers, content marketers, business owners seeking SEO services",
            "field_6_content_goal": "Educate about semantic SEO while promoting Rank Ray services",
            "field_7_tone": "Professional, authoritative, helpful",
            "field_8_reading_level": "Grade 8-10 (accessible but expert)",
            "field_9_competitor_angles": self._extract_competitor_angles(),
            "field_10_unique_value_prop": "Koray Tuğberk Gübür methodology + AI-powered entity optimization",
            "field_11_internal_link_targets": 8,
            "field_12_external_link_targets": 2,
            "field_13_cta_placement": ["After benefits section", "End of page", "In FAQ"]
        }
    
    def _generate_internal_links(self) -> List[Dict]:
        """Generate internal linking suggestions from sitemap"""
        
        # Rank Ray internal link opportunities
        links = [
            {
                "anchor": "SEO services",
                "url": "/services/seo-services/",
                "relevance": "high",
                "section": "What is Semantic SEO?"
            },
            {
                "anchor": "content optimization",
                "url": "/services/content-optimization/",
                "relevance": "high",
                "section": "How Semantic SEO Works"
            },
            {
                "anchor": "technical SEO audit",
                "url": "/services/technical-seo-audit/",
                "relevance": "medium",
                "section": "Core Components"
            },
            {
                "anchor": "keyword research",
                "url": "/services/keyword-research/",
                "relevance": "medium",
                "section": "Semantic SEO vs Traditional"
            },
            {
                "anchor": "link building services",
                "url": "/services/link-building/",
                "relevance": "low",
                "section": "Essential Tools"
            },
            {
                "anchor": "local SEO",
                "url": "/services/local-seo/",
                "relevance": "medium",
                "section": "Examples"
            },
            {
                "anchor": "SEO agency Pakistan",
                "url": "/services/seo-agency-pakistan/",
                "relevance": "high",
                "section": "Professional Services"
            },
            {
                "anchor": "contact us",
                "url": "/contact/",
                "relevance": "high",
                "section": "Get Started"
            }
        ]
        
        return links[:10]  # Top 10 most relevant
    
    def _generate_image_brief(self) -> Dict:
        """Generate image requirements"""
        
        return {
            "featured_image": {
                "type": "landscape",
                "dimensions": "1200x630px",
                "subject": "Semantic network visualization or SEO concept",
                "alt_text": f"Semantic SEO optimization showing entity relationships and topic clusters",
                "filename": "semantic-seo-services-rank-ray.jpg"
            },
            "h2_images": [
                {
                    "section": "What is Semantic SEO?",
                    "type": "diagram",
                    "alt_text": "Semantic SEO vs traditional keyword SEO comparison diagram"
                },
                {
                    "section": "How Semantic SEO Works",
                    "type": "process_flow",
                    "alt_text": "Semantic SEO optimization process workflow"
                },
                {
                    "section": "Core Components",
                    "type": "infographic",
                    "alt_text": "Key components of semantic SEO strategy"
                },
                {
                    "section": "Examples",
                    "type": "case_study",
                    "alt_text": "Semantic SEO case study results graph"
                }
            ],
            "total_images": 5,
            "notes": "Download and upload to WordPress media library. Do not hotlink. Optimize for web (WebP format preferred)."
        }
    
    def _generate_faqs(self) -> List[Dict]:
        """Generate FAQ questions from queries"""
        
        faqs = [
            {
                "question": "What is semantic SEO?",
                "answer": "Semantic SEO is the practice of optimizing content for topics and entities rather than individual keywords. It focuses on understanding user intent and creating comprehensive content that covers all aspects of a topic.",
                "intent": "learn"
            },
            {
                "question": "How is semantic SEO different from traditional SEO?",
                "answer": "Traditional SEO focuses on individual keyword placement and density. Semantic SEO optimizes for topic coverage, entity relationships, and user intent, resulting in more comprehensive and relevant content.",
                "intent": "compare"
            },
            {
                "question": "Why is semantic SEO important?",
                "answer": "Semantic SEO helps search engines understand your content's context and meaning, leading to better rankings for related queries, improved user experience, and higher conversion rates.",
                "intent": "learn"
            },
            {
                "question": "What are semantic SEO entities?",
                "answer": "Entities are distinct, well-defined concepts (people, places, things, ideas) that search engines recognize. Semantic SEO optimizes for entity relationships rather than just keyword matches.",
                "intent": "learn"
            },
            {
                "question": "How do you implement semantic SEO?",
                "answer": "Implement semantic SEO by creating comprehensive topic clusters, using structured data, optimizing for user intent, building entity relationships, and covering all 9 semantic frames in your content.",
                "intent": "learn"
            },
            {
                "question": "What tools are used for semantic SEO?",
                "answer": "Common semantic SEO tools include entity extraction software, topic modeling platforms, structured data generators, and AI-powered content analysis tools that identify semantic relationships.",
                "intent": "buy"
            },
            {
                "question": "How much do semantic SEO services cost?",
                "answer": "Semantic SEO services typically range from $500-$5000/month depending on scope, competition, and website size. Custom audits and one-time consultations start at $300-$800.",
                "intent": "buy"
            }
        ]
        
        return faqs
    
    def _generate_recommendations(self) -> List[str]:
        """Generate content recommendations based on gaps"""
        
        recommendations = []
        
        # Add frame gap recommendations
        for gap in self.coverage["gaps"]:
            recommendations.append(f"Add {gap} content: This frame is currently missing from SERP analysis")
        
        # Add entity recommendations
        top_entities = self.entities[:10]
        recommendations.append(f"Include top entities: {', '.join([e['text'] for e in top_entities[:5]])}")
        
        # Add intent recommendations
        intent_dist = self.report["summary"]["intent_distribution"]
        if intent_dist.get("buy", 0) < 5:
            recommendations.append("Add more commercial intent content (pricing, services, CTAs)")
        
        if intent_dist.get("compare", 0) < 3:
            recommendations.append("Add comparison content (vs alternatives, pros/cons)")
        
        # Add length recommendation
        recommendations.append("Target 2500-3500 words for comprehensive coverage")
        
        # Add internal linking
        recommendations.append("Include 8-10 internal links to related service pages")
        
        return recommendations
    
    def _generate_checklist(self) -> List[str]:
        """Generate quality checklist"""
        
        return [
            "✓ Meta title under 60 characters",
            "✓ Meta description under 160 characters with keyword + LSI + brand",
            "✓ Single H1 tag only",
            "✓ Primary keyword in H1 and first 100 words",
            "✓ H2/H3 structure with semantic hierarchy",
            "✓ 5-10 internal links to verified URLs",
            "✓ Featured image uploaded to media library",
            "✓ Images for each H2 section",
            "✓ 5-7 FAQs with clear answers",
            "✓ No double dashes anywhere",
            "✓ No emojis in content",
            "✓ Yoast SEO fields completed (focus keyphrase, meta description)",
            "✓ Yoast SEO analysis green/good",
            "✓ No '-draft' in permalink slug",
            "✓ 2500-3500 words total",
            "✓ Natural keyword placement (no stuffing)",
            "✓ Clear CTAs at strategic positions"
        ]
    
    # Helper methods
    def _extract_lsi_terms(self) -> List[str]:
        """Extract LSI terms from entities"""
        # Filter for property-type entities (good LSI candidates)
        lsi = [e["text"] for e in self.entities if e.get("ppr_class") == "Property"]
        return list(set(lsi))[:10]
    
    def _extract_secondary_keywords(self) -> List[str]:
        """Extract secondary keywords from queries"""
        secondary = []
        for q in self.queries:
            if self.topic not in q["query"].lower():
                secondary.append(q["query"])
        return list(set(secondary))[:10]
    
    def _determine_primary_intent(self) -> str:
        """Determine primary search intent"""
        intent_dist = self.report["summary"]["intent_distribution"]
        return max(intent_dist, key=intent_dist.get)
    
    def _extract_competitor_angles(self) -> List[str]:
        """Extract angles from SERP analysis"""
        # Would need SERP data for this
        return ["Comprehensive guides", "Step-by-step tutorials", "Tool comparisons", "Case studies"]
    
    def _get_entities_for_frame(self, frame: str) -> List[str]:
        """Get entities relevant to specific frame"""
        # Simplified mapping
        frame_keywords = {
            "definition": ["what", "is", "meaning", "definition"],
            "benefit": ["benefits", "advantages", "improves"],
            "process": ["how", "process", "steps"],
            "component": ["components", "parts", "elements"],
            "tool": ["tools", "software", "platform"],
            "example": ["examples", "cases", "studies"],
            "comparison": ["vs", "versus", "comparison"],
            "challenge": ["challenges", "problems", "issues"],
            "buy": ["services", "price", "cost", "hire"]
        }
        
        keywords = frame_keywords.get(frame, [])
        matched = []
        
        for entity in self.entities[:50]:
            entity_lower = entity["text"].lower()
            if any(kw in entity_lower for kw in keywords):
                matched.append(entity["text"])
        
        return matched[:10] if matched else [e["text"] for e in self.entities[:5]]


def generate_brief_from_report(report_path: str, output_dir: str = None) -> Dict:
    """Generate brief from Phase 1 report file"""
    
    log(f"Loading Phase 1 report: {report_path}")
    
    with open(report_path, 'r') as f:
        report = json.load(f)
    
    generator = ContentBriefGenerator(report)
    brief = generator.generate_brief()
    
    # Save brief
    if output_dir is None:
        output_dir = Path(report_path).parent
    
    output_dir = Path(output_dir)
    output_dir.mkdir(parents=True, exist_ok=True)
    
    topic_slug = report["topic"].replace(" ", "-")
    output_path = output_dir / f"brief-{topic_slug}-{datetime.now().strftime('%Y%m%d-%H%M%S')}.json"
    
    with open(output_path, 'w') as f:
        json.dump(brief, f, indent=2)
    
    log(f"✓ Brief saved: {output_path}")
    
    return brief, output_path


if __name__ == "__main__":
    """Generate brief from Phase 1 report"""
    
    if len(sys.argv) < 2:
        print("Usage: python run-phase2.py <phase1-report-path> [output-dir]")
        print("Example: python run-phase2.py reports/phase1-semantic-seo-20260421-144753.json")
        sys.exit(1)
    
    report_path = sys.argv[1]
    output_dir = sys.argv[2] if len(sys.argv) > 2 else None
    
    brief, output_path = generate_brief_from_report(report_path, output_dir)
    
    print(f"\n{'='*60}")
    print("CONTENT BRIEF SUMMARY")
    print(f"{'='*60}")
    print(f"Topic: {brief['topic']}")
    print(f"Target Words: {brief['meta']['target_word_count']}")
    print(f"Sections: {brief['outline']['total_sections']}")
    print(f"Internal Links: {len(brief['internal_links'])}")
    print(f"FAQs: {len(brief['faqs'])}")
    print(f"Images: {brief['images']['total_images']}")
    print(f"Meta Title: {brief['meta']['meta_title']} ({brief['meta']['meta_title_length']} chars)")
    print(f"Meta Description: {brief['meta']['meta_description']} ({brief['meta']['meta_description_length']} chars)")
    print(f"\nOutput: {output_path}")
