#!/usr/bin/env python3
"""
Smart Model Router for OpenClaw
Auto-detects task complexity and routes to appropriate model
"""

import json
import re
import sys
from pathlib import Path

class ModelRouter:
    def __init__(self):
        self.config_path = Path("~/.openclaw/openclaw.json").expanduser()
        
    def analyze_task(self, prompt):
        """Analyze task complexity and return recommended model"""
        
        # Keywords indicating complex tasks
        complex_keywords = [
            'deep research', 'comprehensive audit', 'detailed analysis',
            'complex code', 'architecture', 'refactor', 'debug complex',
            'serp analysis', 'keyword clustering', 'competitor analysis',
            'technical seo', 'performance audit', 'security audit',
            'large dataset', 'batch processing', 'multi-step'
        ]
        
        # Keywords indicating simple tasks
        simple_keywords = [
            'quick', 'simple', 'brief', 'short', 'meta description',
            'title tag', 'small fix', 'one-liner', 'check',
            'verify', 'confirm', 'list', 'summary', 'status'
        ]
        
        prompt_lower = prompt.lower()
        
        # Check for explicit model requests
        if '/model kimi' in prompt_lower or '/model k2.6' in prompt_lower:
            return 'ollama/kimi-k2.6:cloud', 'explicit_kimi_request'
        
        if '/model gemma' in prompt_lower:
            return 'ollama/gemma4:31b-cloud', 'explicit_gemma_request'
        
        # Complexity scoring
        complex_score = sum(1 for kw in complex_keywords if kw in prompt_lower)
        simple_score = sum(1 for kw in simple_keywords if kw in prompt_lower)
        
        # Check message length as proxy for complexity
        is_long = len(prompt) > 2000
        
        # Decision logic
        if complex_score >= 2 or (complex_score >= 1 and is_long):
            return 'ollama/kimi-k2.6:cloud', f'complex_task (score: {complex_score})'
        elif simple_score >= 1 and not is_long:
            return 'ollama/gemma4:31b-cloud', f'simple_task (score: {simple_score})'
        else:
            # Default to gemma for cost savings
            return 'ollama/gemma4:31b-cloud', 'default_cost_optimized'
    
    def route_current_task(self, prompt=None):
        """Route the current task based on analysis"""
        if not prompt:
            # Read from stdin or last message
            print("Usage: python model-router.py '<task description>'")
            return
        
        model, reason = self.analyze_task(prompt)
        
        print(f"🎯 Task Analysis: {reason}")
        print(f"🤖 Recommended Model: {model}")
        
        if model == 'ollama/kimi-k2.6:cloud':
            print("💡 This is a COMPLEX task - using premium model")
            print("   Estimated cost: ~3-5x higher than gemma4")
        else:
            print("💰 This is a STANDARD task - using cost-optimized model")
            print("   Estimated savings: ~60-70% vs kimi-k2.6")
        
        return model

if __name__ == "__main__":
    router = ModelRouter()
    if len(sys.argv) > 1:
        router.route_current_task(sys.argv[1])
    else:
        print("Smart Model Router")
        print("==================")
        print("Examples:")
        print('  python model-router.py "write a meta description"')
        print('  python model-router.py "deep competitor analysis with serp data"')
        print()
        print("Current default: ollama/gemma4:31b-cloud")
        print("Complex tasks auto-switch to: ollama/kimi-k2.6:cloud")
