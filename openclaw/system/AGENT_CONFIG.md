# Agent Team Configuration

This file defines the mapping between specialized roles and the AI models best suited for those tasks.

## Team Mapping

| Agent Role | Label | Primary Model | Core Strength | Primary Responsibilities |
| :--- | :--- | :--- | :--- | :--- |
| **Coordinator** | `main` (Ranki) | `gemma4` | Generalist / Coordination | User communication, task routing, high-level strategy. |
| **SEO Specialist** | `enigma` | `gemma4` | Nuance / Content / SEO | Long-form blogs, landing pages, on-page SEO, meta copy. |
| **Dev Architect** | `chronos` | `qwen` | Coding / Logic / APIs | WordPress development, API integrations, server config, automation scripts. |
| **Researcher** | `researcher` | `gemma4` | Synthesis / Analysis | Keyword research, SERP analysis, competitor intelligence. |

## Model Logic
- **Gemma4**: Used for all "Language, Strategy, and Coordination" tasks. It is the generalist brain.
- **Qwen**: Used for all "Technical, Coding, and Structural" tasks. It is the coding specialist.

## Spawning Rules
When calling `sessions_spawn`, the `model` parameter must match the mapping above.
- Coding task $\rightarrow$ `model: "qwen"` (or latest Qwen Coder variant)
- Content/Research/Strategy $\rightarrow$ `model: "gemma4"`
