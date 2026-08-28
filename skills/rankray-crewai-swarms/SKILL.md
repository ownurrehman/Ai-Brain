---
name: rankray-crewai-swarms
description: "Architecture, state management, router tiers, and execution triggers for autonomous CrewAI multi-agent swarms."
---

> **Parent Hub:** [[skills/_CATALOG_MAP|⚡ Skills Catalog]] · [[INDEX|🧠 Master Ai Brain Hub]]

# 🤖 RankRay Autonomous CrewAI Swarm Engineering

> **Guidelines for building and executing multi-agent flows with CrewAI 1.15+ and Hermes.**

---

## 🏗️ 1. CrewAI Flow Architecture
```python
from crewai.flow.flow import Flow, start, listen, router
from pydantic import BaseModel

class SwarmState(BaseModel):
    objective: str = ""
    manager_plan: str = ""
    scout_intel: str = ""
    enigma_content: str = ""
    emilia_outreach: str = ""
    final_report_path: str = ""

class AgencyGrowthFlow(Flow[SwarmState]):
    @start()
    def plan_strategy(self):
        # Manager Agent generates 90-day growth plan
        ...
```

---

## ⚡ 2. Model Selection & Fallback Tiers
- **Tier 1 (Heavy Reasoning / Synthesis):** `glm-5.3-flash:cloud` or `deepseek-v4-pro`
- **Tier 2 (Forensics & Intel):** `gpt-oss:20b` or `qwen3.5:397b`
- **Tier 3 (Execution / Code):** `kimi-k2.7-code` or `nvidia/nemotron-3-ultra`

---

## 🚀 3. Execution Commands
```bash
# Direct Flow Runner
"system/agency-flows/run.sh" "Objective text here"
```
