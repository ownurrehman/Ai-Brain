# Agent Registry - DO NOT DELETE

This file documents all active agents and their configuration.
If an agent disappears from openclaw.json, restore it from here.

## Active Agents

### main (default)
- **Label:** Ranki (was Dark)
- **Model:** ollama/kimi-k2.6:cloud
- **Role:** Coordinator & General Operations
- **Workspace:** ~/Ai Works - Local/Ai Codes/Ai Brain/agents/openclaw
- **Tools:** browser, canvas, message, heartbeat_respond, nodes, agents_list, tts, file_fetch, dir_list, firecrawl_scrape, firecrawl_search

### enigma
- **Label:** SEO Expert
- **Model:** ollama/gemma4:31b-cloud (fallback: ollama/kimi-k2.6:cloud)
- **Role:** SEO & Content Strategy - Deep SEO, content planning, keyword research
- **Workspace:** ~/Ai Works - Local/Ai Codes/Ai Brain/agents/openclaw
- **Tools:** browser, canvas, message, heartbeat_respond, nodes, agents_list, tts, file_fetch, dir_list, firecrawl_scrape, firecrawl_search

### chronos
- **Label:** Main Developer
- **Model:** ollama/deepseek-v4-pro:cloud
- **Role:** Development - Code architecture, technical implementation, debugging
- **Workspace:** ~/Ai Works - Local/Ai Codes/Ai Brain/agents/openclaw/chronos
- **Tools:** browser, canvas, message, heartbeat_respond, nodes, agents_list, tts, file_fetch, dir_list, firecrawl_scrape, firecrawl_search

### nemo
- **Label:** Extreme Engineer
- **Model:** nvidia/qwen/qwen3-coder-480b-a35b-instruct
- **Role:** Extreme Engineering - Complex refactoring, high-level architecture, critical bug fixing
- **Workspace:** ~/Ai Works - Local/Ai Codes/Ai Brain/agents/openclaw/nemo
- **Tools:** browser, canvas, message, heartbeat_respond, nodes, agents_list, tts, file_fetch, dir_list

## Deprecated/Retired Agents
- dark - Renamed to main (Ranki)
- scout, emilia - Consolidated into main

## Config Location
~/.openclaw/openclaw.json -> agents.list array

## Recovery
If an agent is missing from config but directory exists:
1. Check ~/.openclaw/agents/<name>/agent/models.json for model info
2. Add entry to agents.list in openclaw.json
3. Restart gateway: openclaw gateway restart
