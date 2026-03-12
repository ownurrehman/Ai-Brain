from __future__ import annotations

from dataclasses import dataclass
from pathlib import Path
from typing import Iterable


DEFAULT_ORDER = (
    "identity.md",
    "instructions.md",
    "coding_standards.md",
    "rankray-hq.md",
    "seo-engineering.md",
    "wordpress-plugin-development.md",
    "tasks.md",
    "planning.md",
    "tools.md",
    "verification.md",
    "memory.md",
)


@dataclass(frozen=True)
class AgentDocument:
    name: str
    path: Path
    content: str


@dataclass(frozen=True)
class AgentDefinition:
    name: str
    path: Path
    documents: tuple[AgentDocument, ...]


def repo_root() -> Path:
    return Path(__file__).resolve().parent.parent


def default_agents_root() -> Path:
    return repo_root() / "ai_brain" / "agents"


def list_agents(agents_root: str | Path | None = None) -> list[str]:
    root = Path(agents_root) if agents_root else default_agents_root()
    if not root.exists():
        return []
    return sorted(path.name for path in root.iterdir() if path.is_dir())


def resolve_agent_path(
    *,
    agent: str | None = None,
    agent_path: str | Path | None = None,
    agents_root: str | Path | None = None,
) -> Path:
    if bool(agent) == bool(agent_path):
        raise ValueError("Provide exactly one of 'agent' or 'agent_path'.")

    if agent_path:
        path = Path(agent_path)
        return path if path.is_absolute() else repo_root() / path

    root = Path(agents_root) if agents_root else default_agents_root()
    return root / str(agent)


def _ordered_markdown_files(agent_dir: Path) -> Iterable[Path]:
    files = [path for path in agent_dir.iterdir() if path.is_file() and path.suffix.lower() == ".md"]
    order_index = {name: index for index, name in enumerate(DEFAULT_ORDER)}
    return sorted(files, key=lambda path: (order_index.get(path.name, len(DEFAULT_ORDER)), path.name))


def load_agent(
    *,
    agent: str | None = None,
    agent_path: str | Path | None = None,
    agents_root: str | Path | None = None,
) -> AgentDefinition:
    path = resolve_agent_path(agent=agent, agent_path=agent_path, agents_root=agents_root)
    if not path.exists():
        raise FileNotFoundError(f"Agent folder not found: {path}")
    if not path.is_dir():
        raise NotADirectoryError(f"Agent path is not a directory: {path}")

    documents = tuple(
        AgentDocument(name=file_path.name, path=file_path, content=file_path.read_text(encoding="utf-8").strip())
        for file_path in _ordered_markdown_files(path)
    )

    if not documents:
        raise ValueError(f"Agent folder contains no markdown files: {path}")

    return AgentDefinition(name=path.name, path=path, documents=documents)


def compose_agent_payload(agent_definition: AgentDefinition) -> str:
    sections = [f"# Agent: {agent_definition.name}"]
    for document in agent_definition.documents:
        sections.append(f"## {document.name}\n{document.content}")
    return "\n\n".join(sections).strip() + "\n"
