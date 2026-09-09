#!/usr/bin/env python3
"""
Agent Harness Tool -- Grok Bot style peer-to-peer delegation and status sharing.

Allows agents in the Hermes fleet (Chronos, Enigma, Alpha, Scout, Emilia, Nemo, Hermes Main)
to communicate directly with each other, delegate tasks to specialized agent profiles
running their own dedicated models, and read each other's status and notes.
"""

import json
import logging
import os
import subprocess
import sys
import tempfile
import time
from pathlib import Path
from typing import Any, Dict, Optional

from tools.registry import registry, tool_error

logger = logging.getLogger(__name__)

# Discord channel map for the agent fleet
FLEET_CHANNELS = {
    "chronos": "1272860753535307817",
    "enigma": "1482488418532589712",
    "alpha": "1541753228105093241",
    "scout": "1541761805469225021",
    "nemo": "1521550430654431324",
    "emilia": "1496584632026796112",
    "atlas": "1546964526325436516",
    "main": "1476025453599789191",
    "hermes": "1476025453599789191",
}

FLEET_EMOJIS = {
    "chronos": "👑",
    "enigma": "🟣",
    "alpha": "🔵",
    "scout": "⚪",
    "emilia": "🟠",
    "nemo": "🟢",
    "atlas": "🌐",
    "main": "⚡",
    "hermes": "⚡",
}

FLEET_ROSTER = {
    "chronos": "CTO & Lead Fleet Captain (glm-5.3-flash:cloud)",
    "enigma": "SEO Content Architect & On-Page Specialist (qwen3.5:397b)",
    "alpha": "Developer & Technical Fixes Specialist (gh/gpt-4.1)",
    "scout": "Intel, SERP & Competitor Intelligence (minimax-m3)",
    "emilia": "Outreach & Conversion Specialist (nemotron-3.5-lightning)",
    "nemo": "Observability, Deep Code Guard & Architecture (nemotron-3-super)",
    "atlas": "Cloud TPU Operations Specialist (qwen3.8-27b)",
    "main": "Chief of Staff & General Strategist (gemma4:31b)",
}

DELEGATE_TO_AGENT_SCHEMA = {
    "type": "object",
    "properties": {
        "target": {
            "type": "string",
            "description": (
                "The target agent profile to message: 'chronos', 'enigma', 'alpha', "
                "'scout', 'emilia', 'nemo', 'main' (or 'hermes')."
            ),
        },
        "task": {
            "type": "string",
            "description": (
                "The specific task, query, or instruction for that agent. "
                "Be clear, concise, and provide any needed context."
            ),
        },
        "wait": {
            "type": "boolean",
            "description": (
                "If true (default), wait synchronously for the target agent to run with its "
                "own model and return its real response. If false, dispatches in the background."
            ),
        },
        "notify_channel": {
            "type": "boolean",
            "description": (
                "If true, post a notice of this delegation to the target agent's dedicated Discord channel."
            ),
        },
    },
    "required": ["target", "task"],
}

READ_AGENT_NOTES_SCHEMA = {
    "type": "object",
    "properties": {
        "agent_name": {
            "type": "string",
            "description": "Agent profile name: 'chronos', 'enigma', 'alpha', 'scout', 'emilia', 'nemo', 'atlas', 'main'",
        },
        "category": {
            "type": "string",
            "enum": ["recent", "memory", "reports", "all"],
            "description": "Category of notes to read: 'recent' (default), 'memory', 'reports', or 'all'.",
        },
    },
    "required": ["agent_name"],
}


def _clean_response_output(text: str) -> str:
    """Strip internal CLI session IDs and warnings from subprocess response."""
    lines = text.splitlines()
    filtered = []
    for line in lines:
        stripped = line.strip()
        if stripped.startswith("session_id:") or stripped.startswith("⚠ tirith"):
            continue
        filtered.append(line)
    return "\n".join(filtered).strip()


def delegate_to_agent_tool(
    target: str,
    task: str,
    wait: bool = True,
    notify_channel: bool = False,
    **kwargs,
) -> str:
    """Execute a task with a peer agent profile using that agent's own model and persona."""
    target_clean = (target or "").strip().lstrip("@").lower()
    if target_clean == "hermes":
        target_clean = "main"

    valid_targets = set(FLEET_ROSTER.keys())
    if target_clean not in valid_targets:
        return tool_error(
            f"Unknown target agent '{target}'. Available fleet agents: {', '.join(sorted(valid_targets))}"
        )

    caller_home = os.getenv("HERMES_HOME", "")
    if caller_home and Path(caller_home).name == target_clean:
        return tool_error(
            f"You are already running as @{target_clean}. Do not delegate to yourself; perform the task directly."
        )

    profile_arg = "default" if target_clean == "main" else target_clean
    emoji = FLEET_EMOJIS.get(target_clean, "🤖")

    # Optional Discord notification
    if notify_channel and target_clean in FLEET_CHANNELS:
        try:
            from tools.discord_tool import _discord_request, _get_bot_token
            token = _get_bot_token()
            if token:
                channel_id = FLEET_CHANNELS[target_clean]
                notice = f"📩 **[Inter-Agent Delegation]** Inbound task from peer fleet:\n> {task[:200]}"
                _discord_request(
                    method="POST",
                    path=f"/channels/{channel_id}/messages",
                    token=token,
                    body={"content": notice},
                    timeout=5,
                )
        except Exception as exc:
            logger.debug("Failed to send Discord channel notification: %s", exc)

    if not wait:
        from tools.terminal_tool import terminal_tool
        cmd = f"hermes -p {profile_arg} chat --in ~ -c 'Inter-Agent' --create-if-missing -Q -q {json.dumps(task)}"
        try:
            res = terminal_tool(cmd, background=True, notify_on_complete=True)
            return json.dumps({
                "status": "dispatched",
                "target": target_clean,
                "emoji": emoji,
                "detail": f"Task dispatched to @{target_clean} in background.",
                "terminal_info": res
            })
        except Exception as exc:
            return tool_error(f"Background delegation failed: {exc}")

    # Synchronous execution
    temp_query_file = None
    try:
        with tempfile.NamedTemporaryFile(mode="w", encoding="utf-8", suffix=".txt", delete=False) as f:
            f.write(task)
            temp_query_file = f.name

        cmd = [
            "hermes",
            "-p",
            profile_arg,
            "chat",
            "--in",
            "~",
            "-c",
            "Inter-Agent",
            "--create-if-missing",
            "-Q",
            "--query-file",
            temp_query_file,
        ]

        proc = subprocess.run(
            cmd,
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            text=True,
            timeout=180,
            check=False,
        )

        raw_output = proc.stdout or proc.stderr or ""
        cleaned = _clean_response_output(raw_output)

        if proc.returncode != 0 and not cleaned:
            return tool_error(f"Agent @{target_clean} execution failed (exit code {proc.returncode}): {proc.stderr}")

        return json.dumps({
            "status": "completed",
            "target": target_clean,
            "emoji": emoji,
            "role": FLEET_ROSTER.get(target_clean, ""),
            "response": cleaned,
        })
    except subprocess.TimeoutExpired:
        return tool_error(f"Agent @{target_clean} timed out after 180 seconds.")
    except Exception as exc:
        return tool_error(f"Delegation to @{target_clean} failed: {exc}")
    finally:
        if temp_query_file and os.path.exists(temp_query_file):
            try:
                os.remove(temp_query_file)
            except Exception:
                pass


def read_agent_notes_tool(
    agent_name: str,
    category: str = "recent",
    **kwargs,
) -> str:
    """Read the dossier, memory, or recent reports of an agent in the fleet."""
    target = (agent_name or "").strip().lstrip("@").lower()
    if target == "hermes":
        target = "main"

    workspace_base = Path("/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain")
    hermes_home = Path(os.getenv("HERMES_HOME") or os.path.expanduser("~/.hermes"))

    results = {}

    # 1. Agent Memory
    if category in ("memory", "all", "recent"):
        mem_paths = [
            workspace_base / "agents" / target / "MEMORY.md",
            hermes_home / "profiles" / target / "MEMORY.md",
        ]
        for p in mem_paths:
            if p.is_file():
                try:
                    results["memory"] = p.read_text(encoding="utf-8", errors="replace")[:4000]
                    break
                except Exception:
                    pass

    # 2. Reports
    if category in ("reports", "all", "recent"):
        reports_dir = workspace_base / "agents" / "reports"
        if reports_dir.is_dir():
            recent_reports = []
            for r in sorted(reports_dir.glob("*.md"), key=lambda x: x.stat().st_mtime, reverse=True)[:3]:
                try:
                    content = r.read_text(encoding="utf-8", errors="replace")[:3000]
                    recent_reports.append({"filename": r.name, "snippet": content})
                except Exception:
                    pass
            if recent_reports:
                results["recent_reports"] = recent_reports

    if not results:
        return json.dumps({
            "target": target,
            "status": "not_found",
            "message": f"No specific dossier or reports found for agent @{target}."
        })

    return json.dumps({
        "target": target,
        "status": "success",
        "data": results
    })


# --- Registry ---

registry.register(
    name="delegate_to_agent",
    toolset="delegation",
    schema=DELEGATE_TO_AGENT_SCHEMA,
    handler=lambda args, **kw: delegate_to_agent_tool(
        target=args.get("target", ""),
        task=args.get("task", ""),
        wait=args.get("wait", True),
        notify_channel=args.get("notify_channel", False),
        **kw,
    ),
    emoji="🤝",
    description="Delegate a task to a peer agent profile running its dedicated model and return the real answer.",
)

registry.register(
    name="read_agent_notes",
    toolset="delegation",
    schema=READ_AGENT_NOTES_SCHEMA,
    handler=lambda args, **kw: read_agent_notes_tool(
        agent_name=args.get("agent_name", ""),
        category=args.get("category", "recent"),
        **kw,
    ),
    emoji="📋",
    description="Read notes, memory, and recent reports filed by another agent profile in the fleet.",
)
