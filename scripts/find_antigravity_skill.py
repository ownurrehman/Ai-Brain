#!/usr/bin/env python3
"""Search technical library index by keyword (id, name, description, category)."""
from __future__ import annotations

import json
import sys
from pathlib import Path

BRAIN_ROOT = Path(__file__).resolve().parents[1]

# Canonical: library repositories in repositories/.
_ANTIGRAVITY_REL_PATHS = (
    "repositories/everything-claude-code",
    "repositories/awesome-claude-skills",
    "antigravity-awesome-skills",
)
MAX_PRINT = 30


def resolve_antigravity_root() -> tuple[Path, str] | None:
    """Return (root_dir, display_prefix) for the installed catalog, or None."""
    for rel in _ANTIGRAVITY_REL_PATHS:
        root = BRAIN_ROOT / rel
        if (root / "skills_index.json").is_file():
            return root, rel
    return None


def main() -> None:
    if len(sys.argv) < 2:
        print(
            "Usage: python3 scripts/find_antigravity_skill.py <keywords...>\n"
            "Example: python3 scripts/find_antigravity_skill.py security audit",
            file=sys.stderr,
        )
        sys.exit(2)

    resolved = resolve_antigravity_root()
    if resolved is None:
        print(
            "Missing skills_index.json under repositories/everything-claude-code/\n"
            "Ensure the repository is cloned and contains a skills_index.json file.",
            file=sys.stderr,
        )
        sys.exit(1)

    antigravity_root, prefix = resolved
    index_file = antigravity_root / "skills_index.json"

    query = " ".join(sys.argv[1:]).lower()
    data = json.loads(index_file.read_text(encoding="utf-8"))
    hits: list[dict] = []
    for item in data:
        blob = " ".join(
            str(item.get(k, "") or "")
            for k in ("id", "name", "description", "category", "risk")
        ).lower()
        if all(part in blob for part in query.split()):
            hits.append(item)

    if not hits:
        print("No matches. Try fewer or broader keywords.")
        return

    for item in hits[:MAX_PRINT]:
        sid = item.get("id", "")
        rel = item.get("path") or f"skills/{sid}"
        desc = (item.get("description") or "").strip()
        if len(desc) > 220:
            desc = desc[:217] + "..."
        print(sid)
        print(f"  file: {prefix}/{rel}/SKILL.md")
        print(f"  risk: {item.get('risk', 'n/a')}  category: {item.get('category', 'n/a')}")
        if desc:
            print(f"  desc: {desc}")
        print()

    if len(hits) > MAX_PRINT:
        print(f"... {len(hits) - MAX_PRINT} more matches (narrow your keywords).")


if __name__ == "__main__":
    main()
