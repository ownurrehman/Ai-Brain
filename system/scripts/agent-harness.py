#!/usr/bin/env python3
"""
Agent Harness Mode
Spawn subordinate agents to complete tasks and report back to Hermes/main.
Usage: python3 agent_harness.py --agent enigma --task "write blog post about X"
"""
import argparse, subprocess, json, time, os, re
from pathlib import Path

AI_BRAIN = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain"

def spawn_agent(agent, task, timeout_min=30):
    """Spawn a single agent with a task and capture its output."""
    print(f"[HARNESS] Spawning {agent} for task: {task[:80]}...")
    
    # Use hermes CLI to chat with the agent profile
    safe_task = task.replace('"', '\\"')
    cmd = [
        "hermes", "-p", agent, "chat", "-q", safe_task,
        "--no-stream"
    ]
    
    try:
        result = subprocess.run(
            cmd,
            cwd=AI_BRAIN,
            capture_output=True,
            text=True,
            timeout=timeout_min * 60
        )
        output = result.stdout
        if result.stderr:
            output += "\n[STDERR] " + result.stderr[:500]
        return {
            "agent": agent,
            "status": "ok" if result.returncode == 0 else "error",
            "output": output,
        }
    except subprocess.TimeoutExpired:
        return {"agent": agent, "status": "timeout", "output": f"Agent {agent} exceeded {timeout_min} minutes"}
    except Exception as e:
        return {"agent": agent, "status": "exception", "output": str(e)}


def run_harness(agents, task, timeout_min=30):
    """Run multiple agents in sequence and compile report."""
    results = []
    for agent in agents:
        res = spawn_agent(agent, task, timeout_min)
        results.append(res)
        time.sleep(2)  # Avoid rate limits
    
    # Compile final report
    report = f"# Agent Harness Report\n\n**Task:** {task}\n\n"
    for r in results:
        report += f"\n## {r['agent']} (status: {r['status']})\n\n"
        report += r['output'][:2000]  # Cap per-agent output
        report += "\n"
    
    return report




# --- Evidence gate (2026-09-06) — Grok-Bot parity ---
EVIDENCE_GATE_DOC = "/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/harness-evidence-gate.md"
MIN_REPORT_BYTES = 4000

def validate_evidence_gate(report_paths, require_seo_skill=False):
    """Return (ok: bool, issues: list[str]). Call before claiming harness audit complete."""
    issues = []
    if not report_paths:
        return False, ["no report paths provided"]
    for path in report_paths:
        path = Path(path)
        if not path.is_file():
            issues.append(f"missing file: {path}")
            continue
        size = path.stat().st_size
        if size < MIN_REPORT_BYTES:
            issues.append(f"too thin ({size}B < {MIN_REPORT_BYTES}B): {path.name}")
        body = path.read_text(errors="ignore")
        banned = ["no hallucinations", "No hallucinations"]
        for b in banned:
            if b in body:
                issues.append(f"banned phrase in {path.name}: {b}")
        # Weak hallucination tells
        if "Yoast SEO" in body and "rankray" in body.lower() and "verified via" not in body.lower():
            issues.append(f"unsourced Yoast claim in {path.name}")
        if require_seo_skill and "FULL-AUDIT-REPORT" not in body and "audit_runner" not in body and "robots_checker" not in body:
            issues.append(f"SEO audit missing skill/script citation: {path.name}")
        # Need at least one URL or absolute path citation
        import re
        if not re.search(r"https?://|/Users/|Ai Brain/|system/reports/", body):
            issues.append(f"no URL/path citations: {path.name}")
    return (len(issues) == 0), issues


def gate_or_fail(report_paths, require_seo_skill=False):
    ok, issues = validate_evidence_gate(report_paths, require_seo_skill=require_seo_skill)
    if ok:
        print("[HARNESS] Evidence gate PASS")
        return True
    print("[HARNESS] Evidence gate FAIL — do NOT claim complete:")
    for i in issues:
        print(f"  - {i}")
    print(f"[HARNESS] See {EVIDENCE_GATE_DOC}")
    return False

if __name__ == "__main__":
    parser = argparse.ArgumentParser()
    parser.add_argument("--agent", nargs="+", required=True, help="Agent profile(s) to spawn")
    parser.add_argument("--task", required=True, help="Task to delegate")
    parser.add_argument("--timeout", type=int, default=30, help="Timeout per agent in minutes")
    args = parser.parse_args()
    
    report = run_harness(args.agent, args.task, args.timeout)
    print(report)
    
    # Save to Ai Brain
    out_path = f"{AI_BRAIN}/system/reports/harness_{int(time.time())}.md"
    Path(out_path).write_text(report)
    print(f"\nReport saved: {out_path}")