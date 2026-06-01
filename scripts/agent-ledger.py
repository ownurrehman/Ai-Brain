#!/usr/bin/env python3
"""
agent-ledger.py — Concurrency-safe Distributed Agent Transaction Ledger (DATL)
Enforces a transactional audit log of all agent file and project accesses inside the AI Brain.

Usage:
  python3 agent-ledger.py log --agent enigma --project rank-ray-hq --file projects/rank-ray-hq/src/app.module.ts --action write --result success --handoff "Fixed NestJS 11 serve-static router crash."
  python3 agent-ledger.py query --file projects/rank-ray-hq/src/app.module.ts
  python3 agent-ledger.py query --project rank-ray-hq
"""

import os
import sys
import json
import argparse
import datetime
import fcntl

BRAIN_ROOT = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain"
GLOBAL_LEDGER_PATH = os.path.join(BRAIN_ROOT, "system/ledgers/transactions.jsonl")

def get_relative_path(path):
    """Normalize file path to be relative to the Brain Root for cross-environment safety."""
    if not path:
        return ""
    
    # If it's already absolute
    if os.path.isabs(path):
        abs_path = os.path.abspath(path)
    else:
        # Try to resolve it relative to Brain Root first if it starts with a standard Brain directory
        parts = path.split(os.sep)
        first_part = parts[0] if parts else ""
        if first_part in ["projects", "clients", "websites", "rules", "prompts", "skills", "system", "memory", "templates"]:
            abs_path = os.path.abspath(os.path.join(BRAIN_ROOT, path))
        else:
            abs_path = os.path.abspath(path)
            
    if abs_path.startswith(BRAIN_ROOT):
        return os.path.relpath(abs_path, BRAIN_ROOT)
    return path

def atomic_append_jsonl(filepath, data_dict):
    """Concurrency-safe write using fcntl exclusive lock (locks file descriptor before appending)."""
    dir_name = os.path.dirname(filepath)
    if not os.path.exists(dir_name):
        os.makedirs(dir_name, exist_ok=True)
        
    # Open in append mode (or write mode if doesn't exist)
    with open(filepath, "a+") as f:
        # Obtain an exclusive lock
        fcntl.flock(f.fileno(), fcntl.LOCK_EX)
        try:
            # Ensure we are at the end of the file
            f.seek(0, os.SEEK_END)
            # Write JSON line
            f.write(json.dumps(data_dict) + "\n")
        finally:
            # Release lock
            fcntl.flock(f.fileno(), fcntl.LOCK_UN)

def load_transactions(filepath):
    """Load transactions safely with a shared read lock."""
    if not os.path.exists(filepath):
        return []
    
    transactions = []
    with open(filepath, "r") as f:
        # Obtain a shared lock for reading
        fcntl.flock(f.fileno(), fcntl.LOCK_SH)
        try:
            for line in f:
                line = line.strip()
                if line:
                    try:
                        transactions.append(json.loads(line))
                    except json.JSONDecodeError:
                        continue
        finally:
            fcntl.flock(f.fileno(), fcntl.LOCK_UN)
    return transactions

def log_action(args):
    # Normalize paths
    rel_file = get_relative_path(args.file)
    
    # Compile transaction metadata
    transaction = {
        "timestamp": datetime.datetime.utcnow().isoformat() + "Z",
        "agent_id": args.agent,
        "project": args.project,
        "file_path": rel_file,
        "action": args.action,
        "result": args.result,
        "handoff_notes": args.handoff or ""
    }
    
    # 1. Log to Global Ledger
    atomic_append_jsonl(GLOBAL_LEDGER_PATH, transaction)
    
    # 2. Log to Local Project/Client Ledger if applicable
    project_dir = None
    # Check under projects/
    p_path = os.path.join(BRAIN_ROOT, "projects", args.project)
    c_path = os.path.join(BRAIN_ROOT, "clients", args.project)
    w_path = os.path.join(BRAIN_ROOT, "websites", args.project)
    
    if os.path.isdir(p_path):
        project_dir = p_path
    elif os.path.isdir(c_path):
        project_dir = c_path
    elif os.path.isdir(w_path):
        project_dir = w_path
        
    if project_dir:
        local_ledger = os.path.join(project_dir, ".agent-ledger.jsonl")
        atomic_append_jsonl(local_ledger, transaction)
        
    print(f"SUCCESS: Transaction logged atomically to {GLOBAL_LEDGER_PATH}")
    if project_dir:
        print(f"SUCCESS: Local project ledger updated at {local_ledger}")

def query_action(args):
    if not os.path.exists(GLOBAL_LEDGER_PATH):
        print("ERROR: Global transaction ledger does not exist yet. Log a transaction first.")
        return
        
    transactions = load_transactions(GLOBAL_LEDGER_PATH)
    
    # Filtering
    filtered = []
    for tx in transactions:
        # Filter by file path
        if args.file:
            query_rel = get_relative_path(args.file)
            if tx.get("file_path") != query_rel:
                continue
        # Filter by project
        if args.project:
            if tx.get("project", "").lower() != args.project.lower():
                continue
        # Filter by agent
        if args.agent:
            if tx.get("agent_id", "").lower() != args.agent.lower():
                continue
        filtered.append(tx)
        
    # Order by newest first
    filtered.reverse()
    
    # Limit results
    results = filtered[:args.limit]
    
    if not results:
        print("\n### Transaction History")
        print("No matching transactions found in ledger.")
        return
        
    # Render gorgeous Markdown table
    print("\n### Transaction History (Newest First)")
    print("| Timestamp | Agent | Project | Action | File Touched | Result | Handoff / Details |")
    print("| :--- | :--- | :--- | :--- | :--- | :--- | :--- |")
    for tx in results:
        ts = tx.get("timestamp", "")
        # Format timestamp slightly for readability
        try:
            dt = datetime.datetime.fromisoformat(ts.replace("Z", "+00:00"))
            ts_display = dt.strftime("%Y-%m-%d %H:%M:%S")
        except Exception:
            ts_display = ts
            
        file_path = tx.get("file_path", "")
        if not file_path:
            file_path = "N/A"
            
        handoff = tx.get("handoff_notes", "").replace("\n", " ")
        print(f"| {ts_display} | **{tx.get('agent_id', 'unknown')}** | `{tx.get('project', 'general')}` | `{tx.get('action', '')}` | `{file_path}` | **{tx.get('result', '')}** | {handoff} |")
    print()

def main():
    parser = argparse.ArgumentParser(description="AI Brain Distributed Agent Transaction Ledger (DATL)")
    subparsers = parser.add_subparsers(dest="command", required=True)
    
    # LOG command
    parser_log = subparsers.add_parser("log", help="Record a new transaction to the ledger")
    parser_log.add_argument("--agent", required=True, help="Agent ID performing the action")
    parser_log.add_argument("--project", required=True, help="Target project, client, or website folder name")
    parser_log.add_argument("--file", required=True, help="Path to the file being accessed/modified")
    parser_log.add_argument("--action", required=True, choices=["read", "write", "create", "delete", "execute", "delegate"], help="Action type")
    parser_log.add_argument("--result", required=True, choices=["success", "failure", "blocked", "warning"], help="Outcome of action")
    parser_log.add_argument("--handoff", help="Description of work, findings, and notes for the next agent")
    
    # QUERY command
    parser_query = subparsers.add_parser("query", help="Query transaction logs")
    parser_query.add_argument("--file", help="Filter by file path")
    parser_query.add_argument("--project", help="Filter by project/client folder name")
    parser_query.add_argument("--agent", help="Filter by agent ID")
    parser_query.add_argument("--limit", type=int, default=15, help="Number of records to display (default 15)")
    
    args = parser.parse_args()
    
    if args.command == "log":
        log_action(args)
    elif args.command == "query":
        query_action(args)

if __name__ == "__main__":
    main()
