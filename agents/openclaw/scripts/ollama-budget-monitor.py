#!/usr/bin/env python3
"""
Ollama Cloud API Budget Monitor
Monitors token usage and alerts when approaching limits
"""

import json
import subprocess
from datetime import datetime, timedelta
from pathlib import Path

class OllamaBudgetMonitor:
    def __init__(self):
        self.config_path = Path("~/.openclaw/openclaw.json").expanduser()
        self.usage_log = Path("~/.openclaw/ollama-usage-tracker.jsonl").expanduser()
        self.daily_budget = 50000  # tokens per day (adjustable)
        self.weekly_budget = 300000  # tokens per week
        
    def get_session_stats(self):
        """Get current session token estimates"""
        try:
            result = subprocess.run(
                ["openclaw", "status"],
                capture_output=True,
                text=True,
                timeout=10
            )
            return result.stdout
        except:
            return "Unable to fetch status"
    
    def estimate_from_logs(self):
        """Estimate usage from gateway logs"""
        log_path = Path("~/.openclaw/logs/gateway.log").expanduser()
        if not log_path.exists():
            return 0
        
        # Count sessions and compactions in last 24h
        today = datetime.now().strftime("%Y-%m-%d")
        sessions = 0
        compactions = 0
        
        with open(log_path) as f:
            for line in f:
                if today in line:
                    if "agent model:" in line:
                        sessions += 1
                    if "auto-compaction" in line:
                        compactions += 1
        
        # Rough estimates: session start ~2K, compaction ~8K
        return (sessions * 2000) + (compactions * 8000)
    
    def alert_status(self):
        """Check current burn vs budgets"""
        estimated = self.estimate_from_logs()
        
        print(f"🔥 Today's estimated burn: {estimated:,} tokens")
        print(f"📊 Daily budget: {self.daily_budget:,} ({estimated/self.daily_budget*100:.1f}%)")
        print(f"📊 Weekly budget: {self.weekly_budget:,}")
        
        if estimated > self.daily_budget * 0.8:
            print("⚠️  WARNING: At 80% of daily budget!")
        if estimated > self.daily_budget:
            print("🚨 CRITICAL: Daily budget exceeded!")
            print("   Recommend: Switch to gemma4 or local model")
        
        # Check which agent/model is active
        print("\n📋 Active configuration:")
        print("   Default: ollama/kimi-k2.6:cloud (EXPENSIVE - 30B params)")
        print("   Fallback: ollama/gemma4:31b-cloud (cheaper)")
        print("   nemo agent: NVIDIA API (separate quota)")
        
        print("\n💡 Optimization tips:")
        print("   1. Use /compact to reduce context size")
        print("   2. Switch model: /model ollama/gemma4:31b-cloud")
        print("   3. Use nemo agent for coding (NVIDIA, not Ollama)")
        print("   4. Limit browser automation loops")
        print("   5. Shorter sessions = less retry/compaction")

if __name__ == "__main__":
    monitor = OllamaBudgetMonitor()
    print(f"📅 {datetime.now().strftime('%Y-%m-%d %H:%M')}")
    print("=" * 50)
    monitor.alert_status()
