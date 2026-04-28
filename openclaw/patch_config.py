import json
import os

CONFIG_PATH = os.path.expanduser('~/.openclaw/openclaw.json')
TEMP_PATH = '/Users/sheikhown/.openclaw/workspace/config_patch.json'

# The updated agents block
updated_agents = {
    "defaults": {
        "workspace": "/Users/sheikhown/.openclaw/workspace",
        "model": {
            "primary": "ollama/gemma4:31b:cloud",
            "fallbacks": [
                "ollama/qwen3.5:397b:cloud",
                "ollama/kimi-k2.6:cloud"
            ]
        },
        "subagents": {
            "allowAgents": ["main", "nemo"]
        }
    },
    "list": [
        {
            "id": "main",
            "default": True,
            "name": "Ranki - Unified Specialist",
            "workspace": "/Users/sheikhown/.openclaw/workspace",
            "model": {
                "primary": "ollama/gemma4:31b:cloud",
                "fallbacks": [
                    "ollama/qwen3.5:397b:cloud",
                    "ollama/kimi-k2.6:cloud"
                ]
            }
        },
        {
            "id": "nemo",
            "name": "Nemo - Elite Coding Specialist (NVIDIA)",
            "workspace": "/Users/sheikhown/.openclaw/workspace",
            "model": {
                "primary": "nvidia/qwen/qwen3-coder-480b-a35b-instruct",
                "fallbacks": [
                    "nvidia/deepseek-ai/deepseek-v4-pro",
                    "nvidia/llama-3.1-nemotron-70b-instruct",
                    "ollama/kimi-k2.6:cloud"
                ]
            }
        }
    ]
}

try:
    with open(CONFIG_PATH, 'r') as f:
        config = json.load(f)
    
    config['agents'] = updated_agents
    
    with open(TEMP_PATH, 'w') as f:
        json.dump(config, f, indent=1)
    
    print(f"SUCCESS: Patched config written to {TEMP_PATH}")
    print(f"NEXT STEP: mv {TEMP_PATH} {CONFIG_PATH}")
except Exception as e:
    print(f"ERROR: {str(e)}")
