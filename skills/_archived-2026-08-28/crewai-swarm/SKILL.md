---
name: crewai-swarm
description: "Triggers autonomous CrewAI multi-agent growth flows (Manager -> Scout -> Enigma -> Emilia) for agency growth, competitor research, SEO content blueprints, and B2B cold outreach campaigns."
---

> **Parent Hub:** [[skills/_archived-2026-08-28/INDEX|📦 Archived Skills Hub]] · [[skills/_CATALOG_MAP|⚡ Skills Catalog]] · [[INDEX|🧠 Master Ai Brain Hub]]

# CrewAI Autonomous Agency Swarm

Use this skill whenever the user asks to run an agency growth mission, generate a market opportunity analysis, create an SEO content blueprint, or draft a cold email sequence using CrewAI Flows.

## Execution Workflow

1. **Clarify Goal**: Identify the target domain (default: `rankray.com`) and specific objective.
2. **Execute Flow**: Run the background bash script from the workspace root:
   ```bash
   "system/agency-flows/run.sh" "<user's objective>"
   ```
3. **Monitor Completion**: Once finished, read the newly created report in `reports/growth-swarm-report-*.md`.
4. **Respond in Discord**:
   - Provide a concise summary of the Strategic Master Plan.
   - Highlight the 3 top buyer personas.
   - Provide the key takeaways from Scout's competitor intelligence.
   - Present the 3-step cold email pitch from Emilia.
   - Give the exact path of the full report in `reports/`.
