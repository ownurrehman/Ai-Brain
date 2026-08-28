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