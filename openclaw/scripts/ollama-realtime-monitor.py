#!/usr/bin/env python3
"""
Real-time Ollama API Usage Monitor
Run this as a cron job or manually to track burn
"""

import json
import time
from datetime import datetime
from pathlib import Path

class OllamaMonitor:
    def __init__(self, config_path="~/.openclaw/openclaw.json"):
        self.config = Path(config_path).expanduser()
        self.usage_file = Path("~/.openclaw/ollama-usage.json").expanduser()
        self.load_usage()
    
    def load_usage(self):
        if self.usage_file.exists():
            with open(self.usage_file) as f:
                self.usage = json.load(f)
        else:
            self.usage = {"sessions": [], "total_estimated_tokens": 0, "models": {}}
    
    def save_usage(self):
        with open(self.usage_file, 'w') as f:
            json.dump(self.usage, f, indent=2)
    
    def estimate_tokens(self, session_data):
        """Rough estimate based on model and message count"""
        model = session_data.get("model", "")
        msg_count = len(session_data.get("messages", []))
        
        # kimi-k2.6 is ~30B params = expensive
        if "kimi" in model:
            base_cost = 3000  # per message pair
        elif "deepseek" in model:
            base_cost = 2500
        elif "gemma" in model:
            base_cost = 2000
        else:
            base_cost = 1500
        
        return msg_count * base_cost
    
    def check_current_burn(self):
        """Check active sessions for current token burn"""
        sessions_dir = Path("~/.openclaw/sessions").expanduser()
        if not sessions_dir.exists():
            return 0
        
        today_burn = 0
        today = datetime.now().strftime("%Y-%m-%d")
        
        for session_file in sessions_dir.glob("*.json"):
            try:
                mtime = datetime.fromtimestamp(session_file.stat().st_mtime)
                if mtime.strftime("%Y-%m-%d") == today:
                    with open(session_file) as f:
                        data = json.load(f)
                    burn = self.estimate_tokens(data)
                    today_burn += burn
                    
                    # Track by session
                    session_id = session_file.stem
                    self.usage["sessions"].append({
                        "id": session_id,
                        "date": today,
                        "model": data.get("model", "unknown"),
                        "estimated_tokens": burn,
                        "messages": len(data.get("messages", []))
                    })
            except:
                pass
        
        return today_burn
    
    def get_status(self):
        burn = self.check_current_burn()
        print(f"🔥 Today's estimated burn: {burn:,} tokens")
        print(f"📊 Total tracked: {self.usage['total_estimated_tokens']:,} tokens")
        
        # Top models
        print("\n📈 Usage by model:")
        for model, count in self.usage.get("models", {}).items():
            print(f"   {model}: {count} sessions")
        
        # Warning thresholds
        if burn > 50000:
            print("\n⚠️  WARNING: High daily burn! Consider switching to cheaper model")
        if burn > 100000:
            print("\n🚨 CRITICAL: Very high burn! Switch to gemma4 or local model NOW")
        
        self.save_usage()

if __name__ == "__main__":
    monitor = OllamaMonitor()
    monitor.get_status()
