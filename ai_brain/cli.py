from __future__ import annotations

import argparse
import sys

from .agent_loader import compose_agent_payload, list_agents, load_agent


def build_parser() -> argparse.ArgumentParser:
    parser = argparse.ArgumentParser(description="Load folder-based markdown agents.")
    parser.add_argument("--agent", help="Agent name inside ai_brain/agents/")
    parser.add_argument("--agent-path", help="Relative or absolute path to an agent folder")
    parser.add_argument("--agents-root", help="Override the default agents root")
    parser.add_argument("--list-agents", action="store_true", help="List available agents and exit")
    parser.add_argument("--show-files", action="store_true", help="Print loaded markdown files before the payload")
    return parser


def main(argv: list[str] | None = None) -> int:
    parser = build_parser()
    args = parser.parse_args(argv)

    if args.list_agents:
        for agent_name in list_agents(args.agents_root):
            print(agent_name)
        return 0

    if not args.agent and not args.agent_path:
        parser.error("one of --agent or --agent-path is required unless --list-agents is used")

    definition = load_agent(agent=args.agent, agent_path=args.agent_path, agents_root=args.agents_root)

    if args.show_files:
        print("Loaded files:")
        for document in definition.documents:
            print(f"- {document.name}")
        print()

    sys.stdout.write(compose_agent_payload(definition))
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
