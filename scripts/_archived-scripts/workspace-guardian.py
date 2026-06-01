#!/usr/bin/env python3
"""
workspace-guardian.py — AI Brain Structural Integrity & Workspace Isolation Auditor
Enforces strict segregation bounds, validates sitemap indexes, and monitors swarm activity.

Usage:
  python3 workspace-guardian.py
"""

import os
import sys
import re
import json
import datetime

BRAIN_ROOT = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain"
AGENTS_DIR = os.path.join(BRAIN_ROOT, "agents")
INDEX_PATH = os.path.join(BRAIN_ROOT, "INDEX.md")
GLOBAL_LEDGER_PATH = os.path.join(BRAIN_ROOT, "system/ledgers/transactions.jsonl")
REGISTRY_PATH = os.path.join(BRAIN_ROOT, "system/ledgers/agents-registry.json")

def audit_workspace_isolation():
    """Scans all agent folders to ensure project/client/website directories haven't leaked inside."""
    violations = []
    if not os.path.exists(AGENTS_DIR):
        return violations
        
    forbidden_names = {"projects", "clients", "websites"}
    
    # Walk agents directory (shallow walk under each agent directory)
    for agent in os.listdir(AGENTS_DIR):
        agent_path = os.path.join(AGENTS_DIR, agent)
        if os.path.isdir(agent_path) and agent != "subagents":
            for subitem in os.listdir(agent_path):
                if subitem in forbidden_names:
                    violations.append(f"agents/{agent}/{subitem}")
                    
    # Scan inside subagents directory
    subagents_dir = os.path.join(AGENTS_DIR, "subagents")
    if os.path.exists(subagents_dir):
        for subagent in os.listdir(subagents_dir):
            sa_path = os.path.join(subagents_dir, subagent)
            if os.path.isdir(sa_path):
                for subitem in os.listdir(sa_path):
                    if subitem in forbidden_names:
                        violations.append(f"agents/subagents/{subagent}/{subitem}")
                        
    return violations

def audit_index_paths():
    """Parses INDEX.md for absolute paths and verifies their existence on disk."""
    drifts = []
    if not os.path.exists(INDEX_PATH):
        drifts.append("INDEX.md itself is missing!")
        return drifts
        
    # Pattern to match absolute paths with spaces inside the Brain Root
    path_pattern = re.compile(r'(/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/[^\)\`\|]+)')
    
    with open(INDEX_PATH, "r") as f:
        for idx, line in enumerate(f, 1):
            matches = path_pattern.findall(line)
            for path in matches:
                # Strip trailing whitespace and punctuation sometimes captured by regex
                clean_path = path.strip().rstrip(".,;:")
                if not os.path.exists(clean_path):
                    drifts.append(f"Line {idx}: Path does not exist -> `{clean_path}`")
                    
    return drifts

def audit_agent_staleness():
    """Checks the registry and ledger to flag registered agents inactive for >24 hours."""
    stale = []
    if not os.path.exists(REGISTRY_PATH) or not os.path.exists(GLOBAL_LEDGER_PATH):
        return stale
        
    try:
        with open(REGISTRY_PATH, "r") as f:
            registry = json.load(f)
            
        # Get active agents
        active_agents = {a["id"]: a["label"] for a in registry.get("agents", []) if a.get("status") == "active"}
        
        # Read transactions
        last_seen = {}
        with open(GLOBAL_LEDGER_PATH, "r") as f:
            for line in f:
                if line.strip():
                    try:
                        tx = json.loads(line)
                        agent_id = tx.get("agent_id")
                        ts_str = tx.get("timestamp")
                        if agent_id and ts_str:
                            last_seen[agent_id] = ts_str
                    except Exception:
                        continue
                        
        now = datetime.datetime.now(datetime.timezone.utc)
        for agent_id, label in active_agents.items():
            ts_str = last_seen.get(agent_id)
            if not ts_str:
                stale.append(f"Agent '{label}' ({agent_id}) has NEVER logged a transaction!")
                continue
                
            try:
                # Parse ISO timestamp
                ts = datetime.datetime.fromisoformat(ts_str.replace("Z", "+00:00"))
                delta = now - ts
                if delta.total_seconds() > 86400: # >24 hours
                    hours = delta.total_seconds() / 3600
                    stale.append(f"Agent '{label}' ({agent_id}) is inactive. Last transaction was {hours:.1f} hours ago.")
            except Exception as e:
                continue
    except Exception as e:
        print(f"WARNING: Staleness check failed: {e}")
        
    return stale

def main():
    print("# WORKSPACE GUARDIAN AUDIT REPORT")
    print(f"**Date:** {datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("---")
    
    # Run Audits
    isolation_violations = audit_workspace_isolation()
    index_drifts = audit_index_paths()
    stale_agents = audit_agent_staleness()
    
    success = True
    
    # 1. Isolation Report
    print("## 1. Workspace Isolation Bounds")
    if isolation_violations:
        success = False
        print("> [!CAUTION]")
        print("> **CRITICAL VIOLATION:** Leaked directories found inside agent workspaces. Segregation rules violated:")
        for v in isolation_violations:
            print(f"> - `[LEAK]` {v}")
    else:
        print("> [!NOTE]")
        print("> **COMPLIANT:** No segregation leaks found. Agent workspaces are strictly isolated.")
    print()
    
    # 2. Index Report
    print("## 2. INDEX.md Path Integrity")
    if index_drifts:
        success = False
        print("> [!WARNING]")
        print("> **DRIFT DETECTED:** Documentation references paths that do not exist:")
        for d in index_drifts:
            print(f"> - `{d}`")
    else:
        print("> [!NOTE]")
        print("> **COMPLIANT:** All absolute path references in `INDEX.md` exist and are valid.")
    print()
    
    # 3. Swarm Health Report
    print("## 3. Agent Swarm Health Status")
    if stale_agents:
        # Not a blocking failure, just a warning alert
        print("> [!TIP]")
        print("> **STALE AGENTS IDENTIFIED:** Active agents who have been inactive for over 24 hours:")
        for s in stale_agents:
            print(f"> - `{s}`")
    else:
        print("> [!NOTE]")
        print("> **HEALTHY:** All registered swarm agents have been active within the last 24 hours.")
    print()
    
    # Verdict
    print("---")
    if success:
        print("### **VERDICT: COMPLIANT (0 anomalies)**")
        sys.exit(0)
    else:
        print("### **VERDICT: NON-COMPLIANT (errors found)**")
        sys.exit(1)

if __name__ == "__main__":
    main()
