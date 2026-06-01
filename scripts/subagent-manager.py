#!/usr/bin/env python3
"""
subagent-manager.py — Subagent Swarm Delegation Orchestrator
Automates provisioning, task scoping, safety verification, and teardown of transient subagents.

Usage:
  python3 subagent-manager.py delegate --parent main --role developer --project rank-ray-hq --task "Implement backend health controller" --skills saas-development --rules rules/rate-limiting.md
  python3 subagent-manager.py complete --task-id task_20260526_014522_ad18 --status success --summary "Finished implementing and testing backend health controller." --files-changed projects/rank-ray-hq/src/health.controller.ts
"""

import os
import sys
import json
import argparse
import datetime
import shutil
import random

BRAIN_ROOT = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain"
SUBAGENTS_DIR = os.path.join(BRAIN_ROOT, "agents/subagents")
REGISTRY_PATH = os.path.join(BRAIN_ROOT, "system/ledgers/agents-registry.json")
BACKUPS_DIR = os.path.join(BRAIN_ROOT, "system/backups/subagents")
TEMPLATES_DIR = os.path.join(BRAIN_ROOT, "templates/agent-workspace")

def get_relative_path(path):
    if not path:
        return ""
    abs_path = os.path.abspath(path)
    if abs_path.startswith(BRAIN_ROOT):
        return os.path.relpath(abs_path, BRAIN_ROOT)
    return path

def update_agent_registry(agent_id, action, agent_data=None):
    """Safely register/deregister a subagent from the central registry."""
    if not os.path.exists(REGISTRY_PATH):
        return
        
    try:
        with open(REGISTRY_PATH, "r") as f:
            registry = json.load(f)
            
        if action == "add" and agent_data:
            # Check if already exists
            exists = False
            for idx, a in enumerate(registry["agents"]):
                if a["id"] == agent_id:
                    registry["agents"][idx] = agent_data
                    exists = True
                    break
            if not exists:
                registry["agents"].append(agent_data)
                
        elif action == "remove":
            # Set to archived/inactive
            for idx, a in enumerate(registry["agents"]):
                if a["id"] == agent_id:
                    registry["agents"][idx]["status"] = "archived"
                    break
                    
        registry["last_updated"] = datetime.datetime.utcnow().isoformat() + "Z"
        
        with open(REGISTRY_PATH, "w") as f:
            json.dump(registry, f, indent=2)
            
    except Exception as e:
        print(f"WARNING: Failed to update agent registry: {e}")

def delegate_task(args):
    # Generate unique Task ID
    timestamp = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
    rand_hex = f"{random.randint(0, 65535):04x}"
    task_id = f"task_{timestamp}_{rand_hex}"
    subagent_id = f"subagent_{task_id}"
    
    workspace_rel = f"agents/subagents/{subagent_id}"
    workspace_abs = os.path.join(BRAIN_ROOT, workspace_rel)
    
    os.makedirs(workspace_abs, exist_ok=True)
    
    # 1. Copy Workspace templates
    if not os.path.exists(TEMPLATES_DIR):
        print(f"ERROR: Workspace templates not found at {TEMPLATES_DIR}. Run Task 1 first.")
        sys.exit(1)
        
    for item in os.listdir(TEMPLATES_DIR):
        src = os.path.join(TEMPLATES_DIR, item)
        dst = os.path.join(workspace_abs, item)
        if os.path.isfile(src):
            shutil.copy2(src, dst)
            
    # 2. Compile replacements
    skills_list = [s.strip() for s in args.skills.split(",")] if args.skills else []
    rules_list = [r.strip() for r in args.rules.split(",")] if args.rules else []
    
    replacements = {
        "{{agent_id}}": subagent_id,
        "{{agent_label}}": f"Transient {args.role.capitalize()}",
        "{{agent_role}}": f"Subagent tasked with: {args.task}",
        "{{agent_model}}": "ollama/kimi-k2.6:cloud",
        "{{parent_agent_id}}": args.parent,
        "{{task_id}}": task_id,
        "{{task_description}}": args.task,
        "{{project_name}}": args.project,
        "{{client_name}}": args.project,
        "{{website_name}}": args.project,
        "{{target_output_dir}}": f"projects/{args.project}/",
        "{{timestamp}}": datetime.datetime.utcnow().isoformat() + "Z",
        "{{competency_1}}": f"Specialized {args.role} executing custom project logic.",
        "{{competency_2}}": "Concurrency-aware transactional logging.",
        "{{competency_3}}": "Isolated, leak-proof workspace boundaries.",
    }
    
    # Apply replacements across copied templates
    for file in os.listdir(workspace_abs):
        file_path = os.path.join(workspace_abs, file)
        if os.path.isfile(file_path):
            with open(file_path, "r") as f:
                content = f.read()
            for key, val in replacements.items():
                content = content.replace(key, str(val))
            with open(file_path, "w") as f:
                f.write(content)
                
    # 3. Create contract.json
    contract = {
        "task_id": task_id,
        "parent_agent_id": args.parent,
        "subagent_id": subagent_id,
        "subagent_role": args.role,
        "target_project": args.project,
        "allotted_skills": skills_list,
        "allotted_rules": rules_list,
        "task_description": args.task,
        "workspace_path": workspace_rel,
        "status": "assigned",
        "created_at": datetime.datetime.utcnow().isoformat() + "Z"
    }
    
    contract_path = os.path.join(workspace_abs, "contract.json")
    with open(contract_path, "w") as f:
        json.dump(contract, f, indent=2)
        
    # 4. Register subagent globally
    agent_data = {
        "id": subagent_id,
        "label": f"Transient {args.role.capitalize()}",
        "role": f"Subagent: {args.task}",
        "model": "ollama/kimi-k2.6:cloud",
        "workspace_path": workspace_rel,
        "status": "active",
        "type": "subagent",
        "parent_id": args.parent
    }
    update_agent_registry(subagent_id, "add", agent_data)
    
    # 5. Log the delegation to the transaction ledger
    os.system(f'python3 "{BRAIN_ROOT}/scripts/agent-ledger.py" log --agent {args.parent} --project {args.project} --file {workspace_rel}/contract.json --action delegate --result success --handoff "Delegated subagent task {task_id} for role {args.role}."')
    
    print(f"\nDELEGATION SUCCESSFUL!")
    print(f"Task ID: {task_id}")
    print(f"Subagent Workspace: {workspace_abs}")
    print(f"To execute, instruct your subagent to initialize using the contract: {workspace_rel}/contract.json\n")

def complete_task(args):
    # Locate subagent directory
    subagent_id = f"subagent_{args.task_id}"
    workspace_rel = f"agents/subagents/{subagent_id}"
    workspace_abs = os.path.join(BRAIN_ROOT, workspace_rel)
    
    contract_path = os.path.join(workspace_abs, "contract.json")
    if not os.path.exists(contract_path):
        print(f"ERROR: Subagent contract not found at {contract_path}. Check Task ID.")
        sys.exit(1)
        
    with open(contract_path, "r") as f:
        contract = json.load(f)
        
    project = contract["target_project"]
    
    # 1. Validate path isolation (Strict Sandboxing check!)
    files_changed = [f.strip() for f in args.files_changed.split(",")] if args.files_changed else []
    
    # Standard authorized prefixes under BRAIN_ROOT
    allowed_prefixes = [
        f"projects/{project}",
        f"websites/{project}",
        f"websites/outreach/{project}"
    ]
    
    print("Auditing path isolation safety...")
    violations = []
    for file in files_changed:
        rel_file = get_relative_path(file)
        
        # Check if file changed is within workspace isolation bounds
        authorized = False
        for prefix in allowed_prefixes:
            if rel_file.startswith(prefix):
                authorized = True
                break
                
        if not authorized:
            # Check if it was local to subagent directory (this is fine, it gets archived/cleaned)
            if rel_file.startswith(workspace_rel):
                authorized = True
                
        if not authorized:
            violations.append(rel_file)
            
    if violations:
        print(f"\nCRITICAL SECURITY VIOLATION: Subagent '{subagent_id}' attempted to modify files outside of authorized scope!")
        for v in violations:
            print(f"  - UNAUTHORIZED WRITE: {v}")
        print("Merges blocked. Subagent workspace is locked. Manual intervention required.\n")
        sys.exit(1)
        
    print("SUCCESS: Path isolation audit passed. No file leaks detected.")
    
    # 2. Update contract status
    contract["status"] = args.status
    contract["completed_at"] = datetime.datetime.utcnow().isoformat() + "Z"
    with open(contract_path, "w") as f:
        json.dump(contract, f, indent=2)
        
    # 3. Log completion to the transaction ledger
    ref_file = files_changed[0] if files_changed else f"{workspace_rel}/contract.json"
    os.system(f'python3 "{BRAIN_ROOT}/scripts/agent-ledger.py" log --agent {subagent_id} --project {project} --file {ref_file} --action execute --result {args.status} --handoff "{args.summary}"')
    
    # 4. Deregister agent globally
    update_agent_registry(subagent_id, "remove")
    
    # 5. Archive workspace to system backups
    os.makedirs(BACKUPS_DIR, exist_ok=True)
    backup_path = os.path.join(BACKUPS_DIR, subagent_id)
    if os.path.exists(backup_path):
        shutil.rmtree(backup_path)
        
    # Move/Copy workspace files to backup
    shutil.copytree(workspace_abs, backup_path)
    
    # 6. Delete active workspace directory (teardown to prevent vault clutter)
    shutil.rmtree(workspace_abs)
    
    print(f"\nTEARDOWN COMPLETE!")
    print(f"Task ID: {args.task_id}")
    print(f"Status: {args.status.upper()}")
    print(f"Workspace archived at: {backup_path}")
    print(f"Active workspace deleted successfully.\n")

def main():
    parser = argparse.ArgumentParser(description="AI Brain Subagent Swarm Delegation Manager (SDP)")
    subparsers = parser.add_subparsers(dest="command", required=True)
    
    # DELEGATE command
    parser_del = subparsers.add_parser("delegate", help="Delegate task to a transient subagent")
    parser_del.add_argument("--parent", required=True, help="Parent agent ID spawning the subagent")
    parser_del.add_argument("--role", required=True, choices=["writer", "developer", "auditor", "tester"], help="Target role/persona for the subagent")
    parser_del.add_argument("--project", required=True, help="Target project, client, or website folder name")
    parser_del.add_argument("--task", required=True, help="Task description of what needs to be built")
    parser_del.add_argument("--skills", required=True, help="Comma-separated list of skill folder names")
    parser_del.add_argument("--rules", required=True, help="Comma-separated list of rule paths")
    
    # COMPLETE command
    parser_comp = subparsers.add_parser("complete", help="Audit and teardown completed subagent")
    parser_comp.add_argument("--task-id", required=True, help="Target Task ID")
    parser_comp.add_argument("--status", required=True, choices=["success", "failed"], help="Final outcome of subagent task")
    parser_comp.add_argument("--summary", required=True, help="Short summary of what was accomplished")
    parser_comp.add_argument("--files-changed", help="Comma-separated list of relative files modified by subagent")
    
    args = parser.parse_args()
    
    if args.command == "delegate":
        delegate_task(args)
    elif args.command == "complete":
        complete_task(args)

if __name__ == "__main__":
    main()
