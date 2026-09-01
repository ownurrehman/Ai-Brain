#!/usr/bin/env python3
"""
Browser-Use Test Script — v0.13.6 API
Uses extend_system_message to add JSON output instructions without replacing the default prompt.
"""

import asyncio
import os
from browser_use import Agent, BrowserProfile
from browser_use.llm import ChatOpenAI

OLLAMA_BASE_URL = "https://ollama.com/v1"
OLLAMA_API_KEY = os.environ.get("OLLAMA_API_KEY", "")

llm = ChatOpenAI(
    model="glm-5.2",
    base_url=OLLAMA_BASE_URL,
    api_key=OLLAMA_API_KEY,
)

browser_profile = BrowserProfile(
    headless=True,
    viewport={"width": 1280, "height": 800},
    keep_alive=False,
)

# Extend the default system message — do NOT override it
# Just add a note about raw JSON output
EXTRA_INSTRUCTIONS = """
CRITICAL: You MUST respond with ONLY raw JSON. Do NOT wrap your JSON response in markdown code blocks (```json...```). Do NOT add any text before or after the JSON object. Output the JSON directly as your complete response.
"""

async def run_test(name, task, max_steps=15):
    print(f"\n{'='*20} {name} {'='*20}")
    agent = Agent(
        task=task,
        llm=llm,
        browser_profile=browser_profile,
        use_vision=False,
        extend_system_message=EXTRA_INSTRUCTIONS,
    )
    result = await agent.run(max_steps=max_steps)
    print(f"\n{name} done. Steps: {len(result.all_results)}")
    for i, r in enumerate(result.all_results):
        if r.is_done:
            print(f"  Step {i}: DONE - {r.extracted_content[:200] if r.extracted_content else 'no content'}")
        elif r.error:
            print(f"  Step {i}: ERROR - {r.error[:200]}")
        else:
            print(f"  Step {i}: OK")
    return result

async def main():
    print("Browser-Use Test Suite (text-only, extended system msg)")
    print(f"Model: glm-5.2 via Ollama Cloud | Vision: OFF")
    print(f"API Key: {'Set' if OLLAMA_API_KEY else 'MISSING!'}")
    
    if not OLLAMA_API_KEY:
        print("ERROR: OLLAMA_API_KEY not set!")
        return

    # Test 1: Basic navigation
    try:
        await run_test(
            "TEST 1: Basic Navigation",
            "Navigate to https://example.com. Report the main heading, what the page is about, and list all links with their URLs.",
            max_steps=8,
        )
    except Exception as e:
        print(f"Test 1 failed: {e}")

    print("\n" + "=" * 40)
    print("Test suite completed!")

if __name__ == "__main__":
    asyncio.run(main())