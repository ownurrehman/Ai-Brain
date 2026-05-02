#!/usr/bin/env python3
"""
Rank Ray Secondary Semantic Cluster Content Generator
Generates satellite posts for pillar content and pushes to WordPress REST API
"""

import json
import base64
import urllib.request
import urllib.error
import time
import os
import sys

# ===================== CONFIG =====================
WP_URL = "https://rankray.com/wp-json/wp/v2/posts"
WP_USER = "openclaw"
WP_PASS = "OpenClaw#Admin@2026"
AUTHOR_ID = 5
STATUS = "draft"

def wp_request(method="GET", data=None, endpoint=WP_URL):
    """Make authenticated WP REST API request"""
    credentials = base64.b64encode(f"{WP_USER}:{WP_PASS}".encode()).decode()
    headers = {
        "Authorization": f"Basic {credentials}",
        "Content-Type": "application/json",
    }
    body = json.dumps(data).encode() if data else None
    req = urllib.request.Request(endpoint, data=body, headers=headers, method=method)
    try:
        with urllib.request.urlopen(req, timeout=30) as resp:
            return json.loads(resp.read()), resp.status
    except urllib.error.HTTPError as e:
        err_body = e.read().decode()
        print(f"HTTP Error {e.code}: {err_body}")
        return None, e.code

# ===================== SATELLITE POSTS =====================
# Format: (pillar_number, post_title, slug, primary_category_id, categories_list, content_html, meta_title, meta_description, focus_keyword)

SATELLITE_POSTS = [
    # ===== PILLAR 1: GEO Content Strategy =====
    (1, "How to Rank on ChatGPT: Brand Citation Strategy for AI Search Visibility",
     "how-to-rank-on-chatgpt-brand-citation-strategy",
     455, [455, 450],
     """<p>Getting your brand recommended by ChatGPT is becoming one of the most valuable visibility opportunities in digital marketing. When users ask ChatGPT for product recommendations, service comparisons, or industry expertise, the brands named in those responses earn a type of exposure that traditional search results cannot replicate. This article explains how brand citation strategy works and what you need to do to increase your chances of appearing in AI-generated answers.</p>

<h2>Why ChatGPT Recommendations Matter for Brand Visibility</h2>
<p>ChatGPT and similar large language models process millions of queries every day. Many of those queries are commercial in nature. Users ask which tool is best for a task, which agency is most trusted in a region, or which brand leads a specific category. When your brand appears consistently in those responses, you gain visibility at the exact moment a user is forming their preference.</p>
<p>This is not the same as ranking on Google. A Google search result links directly to your website. An AI citation may mention your brand without a clickable link. However, the influence on buyer behavior is still significant. Studies on brand recall show that consumers are more likely to trust and choose brands they encounter during their research phase, even when that encounter happens inside an AI interface rather than on a search results page.</p>
<p>For service businesses, this is especially important. Someone asking ChatGPT for "the best SEO agency for law firms" is already close to a purchase decision. The brands cited in that answer gain a powerful head start before the user ever visits a website.</p>

<h2>How ChatGPT Selects Which Brands to Mention</h2>
<p>ChatGPT does not maintain a live index of websites the way Google does. It builds responses based on patterns learned during training and, in some cases, real-time browsing capabilities. The brands most likely to surface share several common characteristics.</p>
<p>First, they appear consistently across high-authority sources. ChatGPT learns associations between topics and entities by processing large volumes of text. If your brand is discussed in industry publications, cited in research reports, mentioned in news articles, and referenced on reputable blogs, those mentions accumulate into a strong entity signal.</p>
<p>Second, they have clear and consistent entity descriptions. Search engines and language models both benefit when a brand is described the same way across multiple sources. If one site describes you as an "SEO agency" and another calls you a "digital growth partner," the entity signal is weaker than if both use consistent terminology.</p>
<p>Third, they appear in data that the model has processed. Brands listed in structured databases, featured in press releases, cited in academic papers, and included in curated directories all contribute to the model's understanding of who you are and what you offer.</p>

<h2>Building a Brand Citation Strategy That Works</h2>
<p>Brand citation strategy is the process of systematically increasing your brand's presence across the sources that AI models learn from. It is not about buying mentions. It is about becoming more visible in the spaces where models gather information.</p>
<p>Start by auditing your current brand footprint. Search for your brand name plus relevant industry terms and see where you appear. Look at Wikipedia references, industry directories, Crunchbase, LinkedIn company pages, review platforms, and major publications. Identify gaps where your competitors appear but you do not.</p>
<p>Next, build a structured plan to fill those gaps. This might mean getting listed in key directories, publishing thought leadership on respected platforms, earning coverage in industry media, or contributing expert commentary to articles and podcasts. Every credible mention strengthens the entity signal.</p>
<p>Consistency is essential. A single mention in a major publication helps, but sustained visibility across multiple channels over time is far more powerful. AI models give more weight to brands that appear to be widely recognized and consistently discussed.</p>

<h2>The Role of Structured Content in AI Visibility</h2>
<p>Beyond external citations, your own content plays a role. When your website publishes articles that are well-structured, factually accurate, and semantically rich, it contributes to how AI systems understand your brand's expertise.</p>
<p>Content that clearly defines concepts, uses proper headings and structured formats, cites credible sources, and avoids vague or generic language is more likely to be processed effectively. AI models parse content to understand relationships between entities. Well-structured content makes those relationships clearer.</p>
<p>For businesses serious about AI visibility, investing in semantic SEO is a natural complement to brand citation work. When your site demonstrates deep topical knowledge, it supports the external signals that tell AI systems your brand is an authority.</p>

<h2>Measuring the Impact of AI Citations</h2>
<p>Measuring AI citation performance is still an evolving space, but several practical approaches are emerging. Monitor branded search volume over time to see if AI visibility is driving more users to search for your brand directly. Track referral traffic from AI platforms where tracking is available. Use social listening tools to monitor brand mentions in discussions about AI recommendations.</p>
<p>Some specialized tools now offer AI share-of-voice tracking. These tools query AI models with specific prompts and record how often your brand appears compared to competitors. While early-stage, this data becomes more useful as AI search behavior grows.</p>
<p>The most important indicator is often qualitative. If prospects start mentioning that ChatGPT recommended you, the strategy is working.</p>

<h2>FAQ</h2>
<h3>How long does it take to get cited by ChatGPT?</h3>
<p>AI citation is not instant. It builds over time as your brand presence grows across trusted sources. Expect to invest six to twelve months in a consistent citation strategy before seeing reliable results.</p>
<h3>Can I pay to be included in ChatGPT responses?</h3>
<p>No. There is currently no paid placement option inside ChatGPT responses. Citations are based on the model's training data and, in some cases, real-time browsing of the web. The only reliable way to appear is through organic brand presence and citation building.</p>
<h3>Does appearing in ChatGPT help with Google rankings?</h3>
<p>Not directly. A ChatGPT citation does not create a backlink or pass traditional SEO signals. However, the increased brand awareness and branded search activity that AI citations generate can indirectly support your SEO performance.</p>
<h3>What is the difference between brand citation and link building?</h3>
<p>Brand citations are unlinked mentions of your brand name. Link building creates clickable links from other sites to yours. Both are valuable, but brand citations are especially important for AI visibility because language models learn from brand mentions even without links.</p>
<h3>Should I optimize content differently for ChatGPT versus Google?</h3>
<p>The core principles overlap. Clear structure, accurate information, and deep topical coverage help in both contexts. However, AI optimization also benefits from direct answer formats, concise summaries, and strong entity alignment, which may differ from what performs best for traditional search rankings.</p>
<h3>Can small businesses compete for AI citations?</h3>
<p>Yes. While major brands have an advantage due to their existing visibility, smaller businesses can earn AI citations by focusing on niche authority. Being the most cited brand in a specific local market or specialized industry niche is often more achievable than competing with global giants for broad terms.</p>""",
     "How to Rank on ChatGPT: Brand Citation Strategy for AI Visibility | Rank Ray",
     "Learn how brand citation strategy helps your business appear in ChatGPT recommendations. Step-by-step guide to AI search visibility for brands. | Rank Ray",
     "brand citation strategy for ChatGPT"),

    (1, "Perplexity AI SEO: How to Get Your Content Cited in AI Search Results",
     "perplexity-ai-seo-content-citation-strategy",
     455, [455, 453],
     """<p>Perplexity AI has emerged as one of the most important AI-powered search platforms, combining real-time web access with conversational answer generation. For content marketers and SEO professionals, understanding how Perplexity selects and cites sources is becoming a strategic priority. This guide explains how Perplexity AI works, what ranking factors influence citations, and how to optimize your content for better visibility in AI-powered search results.</p>

<h2>Understanding How Perplexity AI Sources Content</h2>
<p>Unlike ChatGPT, which primarily relies on training data, Perplexity AI actively searches the web in real time when answering queries. When a user asks a question, Perplexity queries multiple search engines, retrieves the most relevant pages, and synthesizes an answer with inline citations linking back to the sources.</p>
<p>This real-time connection to the web means that optimizing for Perplexity citations overlaps significantly with traditional SEO. Pages that already rank well on Google and Bing have a strong advantage because they are more likely to be surfaced during Perplexity's search step. However, there are additional factors that specifically influence whether your page gets cited in the final answer.</p>
<p>The selection process involves evaluating content relevance, authority signals, freshness, and how well the page answers the specific question being asked. Perplexity also considers how well-structured your content is, because clearly formatted pages are easier for the system to extract meaningful answers from.</p>

<h2>Key Ranking Factors for Perplexity AI Citations</h2>
<p>Several factors influence whether your content gets cited inside Perplexity responses. First, strong organic search rankings remain the single biggest advantage. Perplexity uses search engines as its discovery layer, so pages that win in traditional SERPs have a natural head start.</p>
<p>Second, content structure matters enormously. Pages that use clear headings, provide direct answers early in the content, and organize information in scannable sections are easier for Perplexity to process and cite. If your most valuable insight is buried in the sixth paragraph of a long article, it may never get extracted.</p>
<p>Third, authority and trust signals are critical. Perplexity evaluates the credibility of sources based on domain authority, backlink profiles, author expertise, and factual accuracy. Pages from well-established domains with strong topical authority are cited far more often than pages from new or untrusted sources.</p>
<p>Fourth, content freshness plays a role for time-sensitive topics. Perplexity prioritizes recently published or recently updated content for queries where recency matters. This is especially important for topics involving current statistics, recent events, or evolving industry practices.</p>

<h2>Content Structure That Supports AI Citations</h2>
<p>To increase your chances of being cited by Perplexity, structure your content with AI extraction in mind. Begin each major section with a concise answer or definition before expanding into detail. This allows AI systems to capture the core point quickly while still offering depth for users who want more context.</p>
<p>Use descriptive subheadings that clearly signal what each section covers. Avoid vague headings like "More Information" or "Other Things to Know." Instead, use specific labels that match the questions users are likely to ask.</p>
<p>Include factual claims supported by citations from authoritative sources. Perplexity evaluates the quality of information, not just relevance. Pages that reference credible studies, official data, and expert sources are viewed as more trustworthy.</p>
<p>Add summary sections or key takeaway boxes where appropriate. These natural answer formats align well with how AI systems extract and present information.</p>

<h2>Authority Building for AI Search Platforms</h2>
<p>Authority building for AI citations follows similar principles to traditional SEO but with some important nuances. Backlinks still matter because they influence domain authority, which affects how search engines and AI systems evaluate your site. However, unlinked brand mentions and citations across the web are equally important for AI visibility.</p>
<p>Getting cited in respected publications, industry reports, and academic sources builds the kind of broad entity recognition that AI systems value. When multiple authoritative sources reference your brand or your content, it signals that you are a legitimate player in your space.</p>
<p>Consistency in how your brand is described also helps. Use the same brand name, the same positioning language, and the same core descriptors across all platforms. This consistency strengthens entity recognition across AI systems.</p>

<h2>Tracking Performance in AI Search Results</h2>
<p>Measuring your performance in Perplexity and other AI search platforms is still developing, but several practical approaches work today. Manually test key queries regularly to see where your content appears. Use branded search monitoring to track whether AI visibility is driving more people to search for your brand.</p>
<p>Monitor referral traffic that comes through Perplexity's citation links if tracking is available. Pay attention to qualitative signals such as prospects mentioning they found you through an AI search. These indicators collectively paint a picture of your AI visibility progress.</p>
<p>Over time, as AI search platforms mature, more sophisticated tracking tools will emerge. For now, a combination of manual monitoring and indirect signals provides the most useful feedback.</p>

<h2>FAQ</h2>
<h3>How is Perplexity AI different from Google Search?</h3>
<p>Perplexity generates conversational answers with inline citations rather than a list of blue links. It searches the web in real time but synthesizes results into a single response. Google presents a page of links, featured snippets, and other result types for users to browse.</p>
<h3>Can I submit my site to Perplexity for indexing?</h3>
<p>There is no direct submission process for Perplexity. The platform discovers content through web search engines. The best way to get indexed is through strong organic rankings and a technically sound website.</p>
<h3>Does Perplexity respect robots.txt?</h3>
<p>Perplexity's crawler respects standard web protocols, but the primary path to citation is through search engine results. Having a clean robots.txt and proper indexing setup for Google also supports visibility on Perplexity.</p>
<h3>Is Perplexity AI more important than ChatGPT for SEO?</h3>
<p>Both are important in different ways. ChatGPT reaches a massive audience through its standalone interface, while Perplexity is more directly tied to web search behavior. Businesses should aim for visibility on both platforms as part of a comprehensive AI search strategy.</p>
<h3>How fast can I see results from AI search optimization?</h3>
<p>AI citation performance typically takes three to six months to show meaningful results. It depends on your existing organic search presence, your domain authority, and how aggressively you build brand citations. Businesses with strong existing SEO foundations will see results faster.</p>""",
     "Perplexity AI SEO: How to Get Content Cited in AI Search Results | Rank Ray",
     "Learn how Perplexity AI selects and cites content. Complete guide to optimizing pages for citations in AI-powered search results with practical strategies. | Rank Ray",
     "Perplexity AI SEO content citation strategy"),

    (1, "Claude AI Brand Visibility: How to Get Mentioned in Anthropic AI Responses",
     "claude-ai-brand-visibility-anthropic-citations",
     455, [455, 453],
     """<p>Claude, developed by Anthropic, has become one of the most capable AI assistants on the market, known for nuanced reasoning and thoughtful responses. As more users turn to Claude for research, recommendations, and professional guidance, getting your brand referenced in Claude's outputs is a growing competitive advantage. This article explains how Claude processes information and what businesses can do to increase brand visibility within Anthropic's AI ecosystem.</p>

<h2>How Claude Processes and Cites Information</h2>
<p>Claude operates differently from search-connected platforms like Perplexity. It relies heavily on its training data, which includes a broad corpus of web content, books, academic papers, and other text sources. When Claude recommends a brand or cites expertise, it draws from patterns established during training.</p>
<p>This means that brand visibility in Claude is not about real-time ranking factors. It is about being present and consistent in the data that Claude was trained on. That data includes major publications, respected websites, academic literature, and other authoritative sources processed during Anthropic's training pipeline.</p>
<p>Claude also has a strong emphasis on accuracy and helpfulness. It is designed to avoid hallucination and provide well-reasoned responses. Brands that are consistently described in clear, factual, and authoritative ways across the web are more likely to be referenced accurately.</p>

<h2>Entity Consistency for Claude AI Recognition</h2>
<p>Entity consistency is one of the most important factors for AI brand recognition. When your business is described the same way across multiple sources, AI models can build a stronger and more reliable understanding of who you are.</p>
<p>Start by auditing how your brand appears across the web. Check your Google Business Profile, LinkedIn page, Crunchbase entry, industry directories, Wikipedia (if applicable), review sites, and major publication mentions. If your brand name, category, services, and positioning language vary significantly across these sources, the entity signal is diluted.</p>
<p>Standardize key descriptors. Use the same business name format, the same primary category label, and consistent language about your specialty. This does not mean every mention must be identical, but the core signals should align.</p>
<p>Entity consistency also extends to your own website. Your about page, service pages, and author bios should clearly state who you are, what you do, and where you operate. Structured markup such as Organization schema reinforces this consistency for AI systems that process structured data.</p>

<h2>Building Authority for AI Training Data Inclusion</h2>
<p>Getting included in AI training data at a level that leads to citations requires building genuine authority. AI training processes prioritize high-quality, frequently referenced sources. Brands that publish useful research, earn media coverage, and contribute to industry knowledge are naturally more present in training corpora.</p>
<p>Publishing original research is one of the most effective strategies. When your brand produces surveys, data analyses, or industry studies that get cited by others, those citations compound over time. Each citation increases the likelihood that AI training processes will capture and learn your brand associations.</p>
<p>Earning coverage in respected publications is equally important. Articles in major industry publications, news outlets, and academic journals carry significant weight in training data. Contributing expert commentary, writing guest articles, and being quoted as a source all strengthen your presence.</p>
<p>Participating in industry events and being featured in conference materials, speaker lists, and award recognitions also contributes to brand authority signals. These mentions often appear across multiple platforms, reinforcing the consistency AI models need.</p>

<h2>Content Quality Signals That AI Models Value</h2>
<p>The quality of content on your own website influences how AI systems evaluate your expertise. Content that is thorough, well-structured, factually accurate, and genuinely helpful supports your brand's overall authority profile.</p>
<p>Avoid publishing thin content designed only to capture keywords. AI training processes increasingly filter out low-quality, repetitive, or AI-generated content that lacks original value. Focus instead on creating resources that demonstrate real expertise, answer important questions, and provide information users cannot easily find elsewhere.</p>
<p>Structure matters as well. Content with clear headings, logical organization, and evidence-backed claims is easier for AI systems to parse and more likely to be treated as authoritative. Citations, data references, and expert quotes within your content signal that the information is well-researched.</p>

<h2>The Long-Term View on Claude Brand Visibility</h2>
<p>Building visibility in Claude responses is a long-term strategy. Unlike paid search ads or short-term SEO tactics, AI brand recognition compounds over years of consistent presence. The brands most likely to appear in Claude's recommendations in 2026 are those that have been building authority for several years already.</p>
<p>Start now. Audit your brand consistency, identify gaps in your citation footprint, build an authority-building content plan, and commit to sustained execution. The businesses that take AI visibility seriously today will be the ones that dominate AI-generated recommendations in the years ahead.</p>

<h2>FAQ</h2>
<h3>Does Claude have access to the internet?</h3>
<p>Claude's core knowledge comes from training data, not real-time internet access. However, Anthropic may offer browsing capabilities in certain configurations. The primary path to visibility remains training data inclusion through consistent brand presence.</p>
<h3>How can I check if Claude mentions my brand?</h3>
<p>Ask Claude directly about your industry or category and see if your brand appears. Test regularly with different query phrasings. Also monitor branded search volume and social mentions, which often increase when AI visibility grows.</p>
<h3>Is Claude visibility more important than ChatGPT visibility?</h3>
<p>Both matter for different audiences. Claude is particularly strong in professional, academic, and technical contexts. ChatGPT has broader consumer reach. A complete AI visibility strategy targets all major platforms where your target audience asks questions.</p>
<h3>Can I influence what Claude learns about my brand?</h3>
<p>You cannot directly edit Claude's training data, but you can influence the web content that future training runs will process. Publishing high-quality content, earning citations, and maintaining consistent brand descriptions all contribute to better AI recognition over time.</p>
<h3>How does Claude handle conflicting information about a brand?</h3>
<p>AI models typically weight more authoritative and more frequently appearing information higher. This is why consistency across multiple high-authority sources is so important. Conflicting information from low-authority sources is less likely to influence the model's understanding.</p>""",
     "Claude AI Brand Visibility: How to Get Mentioned in Anthropic Responses | Rank Ray",
     "Learn how to build brand visibility in Claude AI responses. Guide to entity consistency, citation strategy, and authority building for Anthropic AI recognition. | Rank Ray",
     "Claude AI brand visibility strategy"),

    (1, "What Is Answer Engine Optimization: AEO Explained for Modern Search",
     "what-is-answer-engine-optimization-aeo-explained",
     455, [455, 445],
     """<p>As AI-powered search platforms reshape how users find information, a new discipline called Answer Engine Optimization has emerged. AEO focuses on optimizing content so it is more likely to be selected, cited, and read aloud by AI answer engines. This article explains what AEO is, how it differs from traditional SEO, and what marketers need to know to succeed in this evolving search landscape.</p>

<h2>Defining Answer Engine Optimization</h2>
<p>Answer Engine Optimization is the practice of creating and structuring content specifically to increase its chances of being surfaced by AI-powered answer systems. These systems include ChatGPT, Perplexity, Claude, Google AI Overviews, voice assistants, and other platforms that generate conversational answers rather than lists of links.</p>
<p>The core difference between AEO and traditional SEO is the output format. SEO aims to rank pages in search results where users choose which link to click. AEO aims to get content selected as the primary source for an AI-generated answer that may not require a click at all. This shift has significant implications for how businesses approach content strategy.</p>
<p>Answer Engine Optimization does not replace SEO. It extends it. Traditional ranking signals such as authority, relevance, and technical quality still matter because many AI answer engines use search engines as a discovery layer. AEO adds new layers focused on answer formatting, entity clarity, and citation worthiness.</p>

<h2>Why AEO Matters for Content Strategy</h2>
<p>The growth of AI answer platforms is changing search behavior. More users are asking questions directly to AI assistants rather than typing keywords into a search box. They expect conversational answers with sources clearly cited. This changes how content competes for attention.</p>
<p>In a traditional SERP, ten or more results compete for clicks. In an AI answer interface, typically only a few sources are cited in each response. The competition for those citation slots is intense, and the factors that determine which sources get chosen are not identical to traditional ranking factors.</p>
<p>For businesses that depend on search traffic, ignoring AEO carries growing risk. If significant portions of the audience shift their question-asking behavior to AI platforms, content that is not optimized for answer engine selection will lose visibility even if it ranks well on Google.</p>

<h2>Key Principles of AEO Content Creation</h2>
<p>Creating content for answer engines requires a deliberate approach to structure and clarity. Begin by identifying the specific questions your audience asks, not just the keywords they search. AI platforms are designed to answer questions, so content framed around questions naturally aligns better.</p>
<p>Provide direct answers early in each content section. Do not make readers or AI systems search through multiple paragraphs before understanding the main point. Place your clearest, most concise answer at the beginning of each section, then expand with supporting detail afterward.</p>
<p>Use descriptive subheadings that signal the question being answered. A subheading like "How long does it take to rank on Google" directly matches a question users might ask. A subheading like "Timeline Considerations" does not. The more directly your headings mirror real questions, the easier it is for answer engines to map your content to user queries.</p>
<p>Support claims with data and citations. Answer engines evaluate information quality, not just relevance. Content that references authoritative sources, includes statistics, and provides evidence-backed reasoning is more likely to be selected for AI responses.</p>

<h2>AEO and Structured Data</h2>
<p>Structured data markup such as FAQ schema, Article schema, and HowTo schema provides machine-readable signals about your content. While these schemas were originally designed for Google's rich results, they also help AI answer engines understand the type and structure of your content.</p>
<p>FAQ schema is particularly useful for AEO. When you mark up a page with clear question-and-answer pairs, you are explicitly telling AI systems where questions and their direct answers live. This makes extraction easier and more reliable.</p>
<p>Organization schema and LocalBusiness schema also support AEO by providing clear entity descriptions. When AI systems can confidently identify your business type, location, and services through structured data, it supports brand citation accuracy.</p>

<h2>Measuring AEO Performance</h2>
<p>Measuring answer engine optimization results requires different metrics than traditional SEO. Impressions in AI platforms are not yet trackable in most analytics tools. Clicks from AI citations may be partial or non-existent depending on the platform.</p>
<p>Practical measurement approaches include monitoring branded search volume from traditional search engines, tracking referral traffic that comes through AI platform links, manually testing key queries across major AI platforms, and paying attention to qualitative signals from prospects and clients. Over time, as AI platforms build more robust analytics, measurement will become more precise.</p>

<h2>FAQ</h2>
<h3>Is Answer Engine Optimization replacing SEO?</h3>
<p>No. AEO is an extension of SEO, not a replacement. Traditional ranking signals still matter, and most businesses benefit from pursuing both disciplines together rather than choosing one over the other.</p>
<h3>Do I need separate content for AEO and SEO?</h3>
<p>Not necessarily. The best content for answer engines is also strong content for traditional search. The key is structuring existing content in ways that serve both purposes, using clear headings, direct answers, and strong evidence.</p>
<h3>Which AI platforms should I prioritize for AEO?</h3>
<p>Start with the platforms where your target audience already asks questions. For most businesses, that includes ChatGPT, Perplexity, and Google AI Overviews. Voice assistants like Siri and Alexa may also matter for local businesses.</p>
<h3>How does AEO affect click-through rates?</h3>
<p>AI answer engines may reduce click-through rates for informational queries where the answer is sufficient without visiting a page. However, they can increase click-through for branded discovery and commercial investigation, where users want to learn more after an initial recommendation.</p>
<h3>Is AEO relevant for local businesses?</h3>
<p>Yes, especially for local businesses that rely on Google Business Profile visibility. Local AI queries such as "best dentist near me" are increasingly answered by AI platforms. Strong GBP optimization and local entity signals support AEO visibility.</p>""",
     "What Is Answer Engine Optimization? AEO Explained for Modern Search | Rank Ray",
     "Learn what Answer Engine Optimization (AEO) is and how it differs from SEO. Guide to optimizing content for AI answer engines like ChatGPT, Perplexity, and Claude. | Rank Ray",
     "Answer Engine Optimization AEO explained"),
]

def push_post(post_data):
    """Push a single post to WordPress REST API"""
    post_slug = post_data["slug"]
    # Check if slug already exists
    check_url = f"{WP_URL}?slug={post_slug}"
    existing, code = wp_request("GET", endpoint=check_url)
    if existing and len(existing) > 0:
        print(f"  EXISTS: {post_slug} (ID: {existing[0]['id']}) - skipping")
        return existing[0]["id"]
    
    # Create post
    payload = {
        "title": post_data["title"],
        "slug": post_slug,
        "content": post_data["content"],
        "status": STATUS,
        "author": AUTHOR_ID,
        "categories": post_data["categories"],
        "yoast_meta": {
            "yoast_wpseo_title": post_data["meta_title"],
            "yoast_wpseo_metadesc": post_data["meta_description"],
            "yoast_wpseo_focuskw": post_data["focus_keyword"],
        }
    }
    
    result, code = wp_request("POST", data=payload)
    if result:
        post_id = result.get("id")
        print(f"  CREATED: {post_slug} (ID: {post_id})")
        return post_id
    else:
        print(f"  FAILED: {post_slug} (code: {code})")
        return None

def main():
    print("=" * 60)
    print("Rank Ray Satellite Post Generator")
    print(f"Posts to generate: {len(SATELLITE_POSTS)}")
    print("=" * 60)
    
    results = {}
    for idx, (pillar, title, slug, primary_cat, cats, content, meta_title, meta_desc, fkw) in enumerate(SATELLITE_POSTS):
        print(f"\n[{idx+1}/{len(SATELLITE_POSTS)}] Pillar {pillar}: {title}")
        post_id = push_post({
            "title": title,
            "slug": slug,
            "content": content,
            "categories": cats,
            "meta_title": meta_title,
            "meta_description": meta_desc,
            "focus_keyword": fkw,
        })
        results[slug] = post_id
        time.sleep(1)  # Rate limit protection
    
    print("\n" + "=" * 60)
    print("GENERATION COMPLETE")
    print(f"Created: {sum(1 for v in results.values() if v)}")
    print(f"Skipped (existing): {sum(1 for v in results.values() if v)}")
    print(f"Failed: {sum(1 for v in results.values() if v is None)}")
    print("=" * 60)
    
    # Save results to JSON for status tracking
    with open("/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw/system/reports/rankray-satellite-post-ids-2026-05-02.json", "w") as f:
        json.dump(results, f, indent=2)
    
    return results

if __name__ == "__main__":
    results = main()
    print("\nResult Map:")
    for slug, pid in results.items():
        print(f"  {pid if pid else 'FAILED'}: {slug}")
