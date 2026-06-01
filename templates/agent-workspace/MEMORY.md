# Agent Memory Trace

## Active Task
- **Task Description:** {{task_description}}
- **Task Contract ID:** {{task_id}}
- **Staged Outputs:** {{target_output_dir}}

## Non-Negotiables (Obsidian Persistence Protocol)
1. [ ] **Read INDEX.md first:** Always navigate the vault from `INDEX.md`.
2. [ ] **Ledger Check:** Run `python scripts/agent-ledger.py query --file <file>` before touching any file.
3. [ ] **Dynamic Skill Check:** Resolve skills directly using `/skills/_CATALOG_MAP.md`.
4. [ ] **Locking & Transaction Write:** Log every single completed action/delta using `scripts/agent-ledger.py log`.
5. [ ] **Output Isolation:** Never create local files/drafts in your workspace. Write all output artifacts directly to `projects/{{project}}/` or `websites/{{website}}/` or the designated directory.

## Persistent Learnings
- Focus on maintaining clean, entity-dense information.
- Minimize chat logs. Write detailed descriptions in transaction handoffs.
