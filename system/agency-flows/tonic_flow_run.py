"""Agency Growth Flow - Autonomous Multi-Agent Swarm Orchestration with CrewAI Flows.

Coordinated Swarm Hierarchy:
- Hermes / Manager (Strategist): Decomposes goals & plans execution.
- Scout (Research Agent): Market, competitor, and keyword intelligence.
- Enigma (SEO Content Creator): High-intent SEO blueprint & article drafting.
- Emilia (B2B Outreach Specialist): High-converting personalized cold email sequence.

Model Tiering:
- OpenRouter Free Models & Ollama Cloud for high speed and cost efficiency.
"""

import os
import sys
import json
import time
from datetime import datetime
from typing import Dict, List, Any, Optional
from pydantic import BaseModel, Field
from dotenv import load_dotenv

# Auto-load environment credentials
for env_path in [
    os.path.expanduser("~/.hermes/.env"),
    os.path.expanduser("~/.hermes/profiles/alpha/.env"),
    os.path.join(os.getcwd(), "master-env.env"),
]:
    if os.path.exists(env_path):
        load_dotenv(env_path, override=False)

from crewai.flow.flow import Flow, start, listen, router
from tonic_context import MISSION, NICHE, DOMAIN, build_context_block
from crewai import Agent, Task, Crew, Process, LLM


# ─────────────────────────────────────────────────────────────────────────────
# 1. State Definition
# ─────────────────────────────────────────────────────────────────────────────
class AgencyGrowthState(BaseModel):
    """Pydantic State tracking the entire lifecycle of the agency growth mission."""
    mission_goal: str = 'Create a cannibalization-free SEO content plan for tonicphysio.com (Tonic Physio, Milton, Ontario physiotherapy clinic). GOAL: strengthen service page rankings (physiotherapy, massage therapy, shockwave, MVA, WSIB, TMJ, osteopathy, pelvic floor, custom bracing, orthotics, acupuncture) with supporting blog clusters that pass link authority to service pages WITHOUT competing for the same keywords. EXISTING CONTENT FACTS (from live 97-post crawl): 97 published posts, avg 2251 words, zero thin posts. 17 service pages have ZERO supporting blogs: Graston Technique, Kinesio Taping, Electrical Stimulation, Return to Sport Program, Mulligan Concept, McKenzie Method, Myofascial Release, Running Injury Assessment, Golf Physiotherapy, Functional Movement Assessment, Balance and Fall Prevention, Cupping Therapy, Geriatric Physiotherapy, Relaxation Massage, Sports Massage, Nutrition Coaching, Pre-Surgical Rehabilitation. CANNIBALIZATION HAZARDS (blog-vs-blog and blog-vs-service overlap): WSIB posts (12441 vs 12892); Custom vs OTC Bracing (12438 vs 12889 vs 12437); Car Accident timeline (12439, 12890, 12440, 12372); Compression therapy (8315, 11750, 12844); Postpartum/Pelvic floor (11820, 13496, 13500, 11812); Clinic-choosing posts (10327, 11326, 13476); Osteopathy (8840, 13466, 11397, 11488); Shockwave (7748, 12840, 13470, 11194); Deep tissue massage (13036, 13474); TMJ (10929, 13516, 13482); Lymphatic massage (13039, 13478); Pediatric (13034, 13480); Posture (11649, 13514); Back pain (11302, 11842, 12725, 11847); Pregnancy massage (11308, 13040, 13468); Chronic pain (9245, 12698, 11635, 13030). CONTENT HYGIENE: 67 posts have duplicate internal links, 14 have FAQ headings (deprecated), 11 have generic Conclusion headings. New plan must avoid repeating these.'
    target_domain: str = "tonicphysio.com"
    target_niche: str = 'Local healthcare: physiotherapy & rehabilitation clinic (Milton, Ontario, Canada)'
    
    # Manager outputs
    strategic_plan: Dict[str, Any] = Field(default_factory=dict)
    
    # Research Agent outputs (Scout)
    research_intel: Dict[str, Any] = Field(default_factory=dict)
    
    # Content Agent outputs (Enigma)
    content_strategy: Dict[str, Any] = Field(default_factory=dict)
    
    # Outreach Agent outputs (Emilia)
    outreach_campaign: Dict[str, Any] = Field(default_factory=dict)
    
    # Final Output Metadata
    final_report_path: Optional[str] = None
    execution_timeline: List[str] = Field(default_factory=list)
    status: str = "INITIALIZED"


# ─────────────────────────────────────────────────────────────────────────────
# 2. Tiered LLM Provider Engine
# ─────────────────────────────────────────────────────────────────────────────
def get_tiered_llms():
    """Configure tiered LLMs for Manager vs Worker agents."""
    ollama_api_key = os.environ.get("OLLAMA_API_KEY", "")
    openrouter_api_key = os.environ.get("OPENROUTER_API_KEY", "")
    
    # Tier 1: Manager LLM (Reasoning & Strategic Planning)
    if ollama_api_key:
        manager_llm = LLM(
            model="openai/glm-5.3-flash:cloud",
            api_key=ollama_api_key,
            base_url="https://ollama.com/v1",
            temperature=0.2
        )
    elif openrouter_api_key:
        manager_llm = LLM(
            model="openai/glm-5.3-flash:cloud",
            api_key=ollama_api_key,
            base_url="https://ollama.com/v1",
            temperature=0.2
        )
    else:
        manager_llm = LLM(
            model="ollama/glm-5.3-flash:cloud",
            base_url="http://localhost:11434",
            temperature=0.2
        )
    
    # Tier 2: Worker LLMs (Fast Execution, Research & Drafting)
    if ollama_api_key:
        worker_llm = LLM(
            model="openai/glm-5.3-flash:cloud",
            api_key=ollama_api_key,
            base_url="https://ollama.com/v1",
            temperature=0.3
        )
    elif openrouter_api_key:
        worker_llm = LLM(
            model="openrouter/minimax/minimax-m3:free",
            api_key=openrouter_api_key,
            base_url="https://openrouter.ai/api/v1",
            temperature=0.3
        )
    else:
        worker_llm = manager_llm
        
    return manager_llm, worker_llm


# ─────────────────────────────────────────────────────────────────────────────
# 3. Agency Growth Flow Implementation
# ─────────────────────────────────────────────────────────────────────────────
class AgencyGrowthFlow(Flow[AgencyGrowthState]):
    """Autonomous CrewAI Flow orchestrating multi-agent collaboration."""

    def log_step(self, message: str):
        timestamp = datetime.now().strftime("%H:%M:%S")
        log_entry = f"[{timestamp}] {message}"
        self.state.execution_timeline.append(log_entry)
        print(f"\n⚡ {log_entry}")

    @start()
    def strategic_planning(self):
        """Step 1: Manager Agent breaks down the agency goal into sub-initiatives."""
        self.log_step(f"Manager Agent (Hermes/Strategist) analyzing: '{self.state.mission_goal}'")
        manager_llm, _ = get_tiered_llms()

        manager_agent = Agent(
            role="Agency Growth Director",
            goal=f"Formulate a high-impact growth strategy for {self.state.target_domain} in the {self.state.target_niche} space",
            backstory="You are an elite agency CEO and growth hacker specializing in SEO client acquisition, programmatic expansion, and offer positioning.",
            llm=manager_llm,
            verbose=True
        )

        planning_task = Task(
            description=f"""
            LIVE CRAWL CONTEXT (ground truth, do not contradict):
            

CRITICAL CONTEXT FROM LIVE SITE CRAWL (use this, do not invent):
Create a cannibalization-free SEO content plan for tonicphysio.com (Tonic Physio, Milton, Ontario physiotherapy clinic). GOAL: strengthen service page rankings (physiotherapy, massage therapy, shockwave, MVA, WSIB, TMJ, osteopathy, pelvic floor, custom bracing, orthotics, acupuncture) with supporting blog clusters that pass link authority to service pages WITHOUT competing for the same keywords. EXISTING CONTENT FACTS (from live 97-post crawl): 97 published posts, avg 2251 words, zero thin posts. 17 service pages have ZERO supporting blogs: Graston Technique, Kinesio Taping, Electrical Stimulation, Return to Sport Program, Mulligan Concept, McKenzie Method, Myofascial Release, Running Injury Assessment, Golf Physiotherapy, Functional Movement Assessment, Balance and Fall Prevention, Cupping Therapy, Geriatric Physiotherapy, Relaxation Massage, Sports Massage, Nutrition Coaching, Pre-Surgical Rehabilitation. CANNIBALIZATION HAZARDS (blog-vs-blog and blog-vs-service overlap): WSIB posts (12441 vs 12892); Custom vs OTC Bracing (12438 vs 12889 vs 12437); Car Accident timeline (12439, 12890, 12440, 12372); Compression therapy (8315, 11750, 12844); Postpartum/Pelvic floor (11820, 13496, 13500, 11812); Clinic-choosing posts (10327, 11326, 13476); Osteopathy (8840, 13466, 11397, 11488); Shockwave (7748, 12840, 13470, 11194); Deep tissue massage (13036, 13474); TMJ (10929, 13516, 13482); Lymphatic massage (13039, 13478); Pediatric (13034, 13480); Posture (11649, 13514); Back pain (11302, 11842, 12725, 11847); Pregnancy massage (11308, 13040, 13468); Chronic pain (9245, 12698, 11635, 13030). CONTENT HYGIENE: 67 posts have duplicate internal links, 14 have FAQ headings (deprecated), 11 have generic Conclusion headings. New plan must avoid repeating these.
Content rules: no FAQ sections, no FAQ schema, no generic 'Conclusion' headings, no duplicate internal links per post, max 3-column tables, no em-dashes, each new post must link to its primary service page with a natural contextual anchor. Local intent: Milton / Halton region Ontario.
            Analyze the agency goal: '{self.state.mission_goal}' for domain '{self.state.target_domain}'.
            Niche: {self.state.target_niche}.

            Deliver a structured plan including:
            1. Core Value Hook (Why clients should choose RankRay over generic SEO agencies)
            2. Top 3 High-Intent Buyer Personas (e.g. eCommerce CMOs, Health Clinics, Fintech)
            3. Concrete Directives for:
               - Scout (Market & Competitor Intel)
               - Enigma (SEO Content Architecture)
               - Emilia (B2B Cold Outreach)
            """,
            expected_output="A structured strategic growth plan with clear execution pillars.",
            agent=manager_agent
        )

        crew = Crew(
            agents=[manager_agent],
            tasks=[planning_task],
            process=Process.sequential,
            verbose=True
        )

        result = crew.kickoff()
        self.state.strategic_plan = {
            "summary": str(result),
            "planned_at": datetime.now().isoformat()
        }
        self.log_step("Strategic plan generated successfully by Manager Agent.")
        return self.state.strategic_plan

    @listen(strategic_planning)
    def execute_market_research(self, strategic_plan):
        """Step 2: Scout Agent executes market, keyword, and competitor research."""
        self.log_step("Scout Agent starting deep research and competitor gap analysis...")
        _, worker_llm = get_tiered_llms()

        scout_agent = Agent(
            role="Scout - Lead Intelligence & SERP Analyst",
            goal="Identify high-intent SEO keyword gaps and high-converting B2B target pain points",
            backstory="You are a data-driven intelligence analyst specializing in competitor reverse-engineering, SERP intent clustering, and technical audit pain points.",
            llm=worker_llm,
            verbose=True
        )

        research_task = Task(
            description=f"""
            Based on the Strategy:
            {strategic_plan.get('summary', '')[:800]}

            Provide actionable intelligence:
            1. 3 High-Buying-Intent search topics that prospective clients search before hiring an agency.
            2. The single biggest flaw in typical competitor audit deliverables that RankRay can fix.
            3. Target industries currently experiencing indexing/ranking volatility.
            """,
            expected_output="Market intelligence report with target topics and competitor weaknesses.",
            agent=scout_agent
        )

        crew = Crew(
            agents=[scout_agent],
            tasks=[research_task],
            process=Process.sequential,
            verbose=True
        )

        result = crew.kickoff()
        self.state.research_intel = {
            "findings": str(result),
            "researched_at": datetime.now().isoformat()
        }
        self.log_step("Market & competitor research completed by Scout.")
        return self.state.research_intel

    @router(execute_market_research)
    def route_execution_tracks(self, research_intel):
        """Step 3: Dynamic router deciding parallel or sequential execution."""
        self.log_step("Router evaluating research findings and dispatching execution crews...")
        return "execute_growth_engine"

    @listen("execute_growth_engine")
    def execute_content_and_outreach(self):
        """Step 4: Enigma (Content) and Emilia (Outreach) craft production-ready deliverables."""
        self.log_step("Enigma (Content) and Emilia (Outreach) running collaborative execution...")
        _, worker_llm = get_tiered_llms()

        # Enigma (Content Agent)
        enigma_agent = Agent(
            role="Enigma - SEO Content Architect",
            goal="Write authoritative, high-ranking SEO content blueprints that generate inbound leads",
            backstory="You are a master SEO content strategist known for crafting in-depth, fluff-free technical content that ranks #1 and converts visitors into paying clients.",
            llm=worker_llm,
            verbose=True
        )

        # Emilia (Outreach Agent)
        emilia_agent = Agent(
            role="Emilia - B2B Outreach Specialist",
            goal="Craft personalized, value-first cold outreach sequences and link-building hooks",
            backstory="You are a top-tier B2B outreach specialist who writes compelling, human, and conversion-optimized emails that get opened and answered.",
            llm=worker_llm,
            verbose=True
        )

        content_task = Task(
            description=f"""
            Using Scout's Research:
            {self.state.research_intel.get('findings', '')[:800]}

            Create a high-intent content deliverable for {self.state.target_domain}:
            MANDATORY: Produce a 12-post cannibalization-free blog plan. For each post give: Title | Target keyword (informational, NOT the service page keyword) | Target service page to support + suggested contextual anchor text | Unique angle vs the overlapping posts listed in the crawl context.
            1. Title & High-Conversion Hook for an authoritative guide: "The 7-Day Technical SEO Audit Checklist for B2B Founders".
            2. Core Value Framework showing how RankRay audits indexability, crawl budget, and Core Web Vitals.
            """,
            expected_output="High-converting SEO blog outline and authority framework.",
            agent=enigma_agent
        )

        outreach_task = Task(
            description=f"""
            Using the Strategy & Findings:
            {self.state.research_intel.get('findings', '')[:800]}

TASK REASSIGNED (clinic context): Draft a Local Authority & Backlink plan for a Milton ON physiotherapy clinic. Provide 10 concrete free link opportunities (local directories, Halton community sites, sports clubs, health partners) with pitch angle for each. Original template reference:
            - Email 1: Personalized hook offering 1 specific audit insight for free.
            - Email 2: Follow-up case study demonstrating tangible traffic & lead growth.
            - Email 3: Quick closure / low-friction call to action.
            """,
            expected_output="Complete 3-step cold email sequence with subject lines.",
            agent=emilia_agent
        )

        crew = Crew(
            agents=[enigma_agent, emilia_agent],
            tasks=[content_task, outreach_task],
            process=Process.sequential,
            verbose=True
        )

        results = crew.kickoff()
        
        self.state.content_strategy = {"deliverable": str(content_task.output)}
        self.state.outreach_campaign = {"deliverable": str(outreach_task.output)}
        self.log_step("Content blueprint and Outreach campaign created successfully.")
        return results

    @listen(execute_content_and_outreach)
    def persist_deliverables(self, results):
        """Step 5: Persist all deliverables into the shared workspace."""
        self.log_step("Synthesizing deliverables and writing to workspace reports...")
        
        workspace_root = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain"
        timestamp_str = datetime.now().strftime("%Y-%m-%d_%H-%M-%S")
        report_filename = f"growth-swarm-report-{timestamp_str}.md"
        report_path = os.path.join(workspace_root, "reports", report_filename)
        
        os.makedirs(os.path.join(workspace_root, "reports"), exist_ok=True)
        
        markdown_content = f"""# Autonomous Agency Growth Swarm Report

**Generated:** {datetime.now().strftime("%Y-%m-%d %H:%M:%S")}
**Target Domain:** [{self.state.target_domain}](https://{self.state.target_domain})
**Mission Goal:** {self.state.mission_goal}
**Swarm Architecture:** CrewAI Flows (Hierarchical Router + Multi-Agent Execution)

---

## 🎯 1. Strategic Master Plan (Manager / Hermes)
{self.state.strategic_plan.get('summary', 'N/A')}

---

## 🔍 2. Market Intelligence & Competitor Gaps (Scout)
{self.state.research_intel.get('findings', 'N/A')}

---

## ✍️ 3. High-Intent SEO Content Blueprint (Enigma)
{self.state.content_strategy.get('deliverable', 'N/A')}

---

## 📬 4. High-Conversion B2B Outreach Sequence (Emilia)
{self.state.outreach_campaign.get('deliverable', 'N/A')}

---

## ⏱️ Swarm Execution Timeline
{chr(10).join(['- ' + t for t in self.state.execution_timeline])}
"""
        
        with open(report_path, "w", encoding="utf-8") as f:
            f.write(markdown_content)
            
        self.state.final_report_path = report_path
        self.state.status = "COMPLETED"
        self.log_step(f"Deliverable saved to: {report_path}")
        
        print("\n" + "=" * 80)
        print(f"🎉 MISSION COMPLETE! Deliverable: {report_path}")
        print("=" * 80 + "\n")
        return report_path


# ─────────────────────────────────────────────────────────────────────────────
# 4. Entrypoint Runner
# ─────────────────────────────────────────────────────────────────────────────
def main():
    goal = sys.argv[1] if len(sys.argv) > 1 else None
    
    flow = AgencyGrowthFlow()
    if goal:
        flow.state.mission_goal = goal
        
    print("\n" + "=" * 80)
    print(f"🚀 LAUNCHING AUTONOMOUS CREWAI FLOW: '{flow.state.mission_goal}'")
    print("=" * 80)
    
    flow.kickoff()


if __name__ == "__main__":
    main()
