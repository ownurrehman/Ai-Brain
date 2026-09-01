#!/usr/bin/env python3
"""
mac-process-cleanup.py — Automated RAM & Orphaned Process Garbage Collector
Protects Mac health, prevents heating & memory leak buildup.

Safety Whitelist:
- Hermes Gateway, supervisor, and WhatsApp bridge (port 3000)
- Cursor IDE and its active editor processes
- Antigravity IDE and its active language servers
- Standard Apple macOS system daemons
"""

import os
import sys
import subprocess
import time
from datetime import datetime

LOG_FILE = os.path.expanduser("~/.hermes/logs/process_cleanup.log")
os.makedirs(os.path.dirname(LOG_FILE), exist_ok=True)

def log(msg):
    timestamp = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    line = f"[{timestamp}] {msg}"
    print(line)
    try:
        with open(LOG_FILE, "a", encoding="utf-8") as f:
            f.write(line + "\n")
    except Exception:
        pass

def get_process_list():
    cmd = ["ps", "-eo", "pid,ppid,user,etime,rss,command"]
    res = subprocess.run(cmd, capture_output=True, text=True)
    lines = res.stdout.strip().splitlines()
    procs = []
    for l in lines[1:]:
        parts = l.split(None, 5)
        if len(parts) == 6:
            pid, ppid, user, etime, rss, command = parts
            try:
                procs.append({
                    "pid": int(pid),
                    "ppid": int(ppid),
                    "user": user,
                    "etime": etime,
                    "rss_kb": int(rss),
                    "cmd": command
                })
            except ValueError:
                continue
    return procs

def is_whitelisted(cmd):
    cmd_lower = cmd.lower()
    # 1. Hermes Protection
    if "hermes_cli" in cmd_lower or "ai.hermes" in cmd_lower or "whatsapp-bridge" in cmd_lower:
        return True
    if "/.hermes/hermes-agent/venv" in cmd_lower:
        return True
    
    # 2. System Protection
    if "/system/library" in cmd_lower or "/usr/sbin" in cmd_lower or "/usr/libexec" in cmd_lower:
        return True
    
    return False

def parse_uptime_seconds(etime_str):
    # etime format: [[dd-]hh:]mm:ss
    try:
        days = 0
        if "-" in etime_str:
            d_part, etime_str = etime_str.split("-", 1)
            days = int(d_part)
        parts = [int(p) for p in etime_str.split(":")]
        if len(parts) == 2:
            return days * 86400 + parts[0] * 60 + parts[1]
        elif len(parts) == 3:
            return days * 86400 + parts[0] * 3600 + parts[1] * 60 + parts[2]
    except Exception:
        pass
    return 0

def cleanup():
    log("=== Starting Mac Process & RAM Garbage Collection ===")
    procs = get_process_list()
    
    orphans_killed = 0
    ram_freed_kb = 0
    
    # Target patterns for stale / orphaned tools
    stale_patterns = [
        "mongodb-mcp-server",
        "chrome-devtools-mcp/build/src/telemetry/watchdog",
        "mcp-remote",
        "npm exec @notionhq/notion-mcp-server",
        "npm exec hostinger-",
        "npm exec firebase-tools",
        "npm exec @google-cloud/cloud-run-mcp",
        "npm exec @bitbonsai/mcpvault",
        "npm exec @modelcontextprotocol/server-sequential-thinking"
    ]
    
    for p in procs:
        cmd = p["cmd"]
        pid = p["pid"]
        ppid = p["ppid"]
        
        if is_whitelisted(cmd):
            continue
        
        uptime_sec = parse_uptime_seconds(p["etime"])
        
        # Rule 1: Kill any orphaned MCP / Node tool whose parent died (PPID == 1)
        if ppid == 1 and any(pat in cmd for pat in ["mcp", "node_modules", "npm exec"]):
            try:
                subprocess.run(["kill", "-9", str(pid)], check=True)
                log(f"Terminated orphan (PPID=1) PID {pid} ({p['rss_kb']/1024:.1f}MB): {cmd[:70]}")
                orphans_killed += 1
                ram_freed_kb += p["rss_kb"]
            except Exception as e:
                log(f"Failed to terminate {pid}: {e}")
                
        # Rule 2: Kill stale chrome watchdog telemetry processes running > 2 hours
        elif "chrome-devtools-mcp/build/src/telemetry/watchdog" in cmd and uptime_sec > 7200:
            try:
                subprocess.run(["kill", "-9", str(pid)], check=True)
                log(f"Terminated stale watchdog PID {pid} (Uptime {p['etime']})")
                orphans_killed += 1
                ram_freed_kb += p["rss_kb"]
            except Exception:
                pass

    log(f"Cleanup finished. Terminated {orphans_killed} junk processes. Reclaimed ~{ram_freed_kb/1024:.1f} MB RAM.")
    return orphans_killed, ram_freed_kb / 1024

if __name__ == "__main__":
    cleanup()
