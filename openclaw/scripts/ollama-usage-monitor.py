#!/usr/bin/env python3
"""
Ollama Cloud API Usage Monitor
Tracks token consumption and identifies heavy consumers
"""

import json
import re
import sys
from datetime import datetime, timedelta
from collections import defaultdict
from pathlib import Path

def parse_gateway_log(log_path="~/.openclaw/logs/gateway.log"):
    log_file = Path(log_path).expanduser()
    if not log_file.exists():
        print(f"❌ Log file not found: {log_file}")
        return
    
    # Patterns
    session_pattern = re.compile(r'(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}) .*?agent model: (\S+)')
    compaction_pattern = re.compile(r'(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}) .*?auto-compaction succeeded for (\S+)')
    retry_pattern = re.compile(r'(\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}) .*?retrying prompt')
    
    daily_sessions = defaultdict(int)
    daily_compactions = defaultdict(int)
    daily_models = defaultdict(lambda: defaultdict(int))
    
    with open(log_file, 'r') as f:
        for line in f:
            # Session starts
            match = session_pattern.search(line)
            if match:
                date = match.group(1)[:10]
                model = match.group(2)
                daily_sessions[date] += 1
                daily_models[date][model] += 1
            
            # Auto-compactions (expensive - retrying large prompts)
            match = compaction_pattern.search(line)
            if match:
                date = match.group(1)[:10]
                model = match.group(2)
                daily_compactions[date] += 1
                daily_models[date][model + " (compaction)"] += 1
            
            # Retry patterns
            match = retry_pattern.search(line)
            if match:
                date = match.group(1)[:10]
                daily_compactions[date] += 1
    
    # Report
    print("=" * 60)
    print("📊 OLLAMA CLOUD USAGE REPORT")
    print("=" * 60)
    
    dates = sorted(daily_sessions.keys())[-7:]  # Last 7 days
    for date in dates:
        print(f"\n📅 {date}:")
        print(f"   Sessions started: {daily_sessions[date]}")
        print(f"   Auto-compactions (expensive): {daily_compactions[date]}")
        print(f"   Estimated burn: {daily_sessions[date] * 2 + daily_compactions[date] * 5}K tokens")
        print("   Models:")
        for model, count in daily_models[date].items():
            print(f"      • {model}: {count}x")
    
    print("\n" + "=" * 60)
    print("⚠️  HIGH BURN INDICATORS:")
    print("   • Auto-compaction = retrying large context = ~5-10K tokens")
    print("   • Multiple sessions/day = repeated cold starts")
    print("   • kimi-k2.6 is expensive (30B+ params)")
    print("=" * 60)
    
    # Recommendations
    total_compactions = sum(daily_compactions.values())
    if total_compactions > 10:
        print("\n🔥 CRITICAL: High compaction rate detected!")
        print("   Fix: Reduce session size, enable auto-compact sooner,")
        print("   or switch to smaller model for background tasks")

if __name__ == "__main__":
    parse_gateway_log()
