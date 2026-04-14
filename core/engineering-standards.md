# Engineering Standards (Core Practice)

**Status**: Active | **Domain**: Engineering / Implementation | **Ownership**: Shared (Opus Standards)

Standardized engineering practices for the AI Codes workspace. Load this during any implementation or debugging task.

---

## 🏗️ 1. Implementation Protocol (TDD-Lite)

1.  **Understand**: Read the `AGENTS.md` in the project folder. Ensure you know the stack.
2.  **Plan**: Draft a plan in the chat or a `/tmp/plan.md`. Get confirmation if complex.
3.  **Implement**: Small, atomic changes. No logic-bombing.
4.  **Verify**: Always verify after changes. Use `run_command` to run tests or `ls` to confirm files.
5.  **Refactor**: Only once it works. Keep it DRY and follow existing patterns.

## 🗃️ 2. File Hygiene

- **No Placeholders**: Never leave `// ... existing code ...` unless you are using a specific `multi_replace` tool that requires it.
- **Naming**: Use camelCase for TS/JS, snake_case for Python/PHP (context dependent).
- **Imports**: Always use absolute paths or defined aliases (e.g. `@/components` in RankRay-HQ).

## 🛡️ 3. Security & Safety

- **DRY RUN**: Always assume `DRY_RUN=true` for any script that can spend money or move crypto.
- **Environment**: Never log real API keys. Use `varkey` or `.env` placeholders.
- **Deletion**: Use extreme caution with `rm`. Favor `mv` to a backup if unsure.

## 🧪 4. Verification Standards

- **Linting**: If a project has a linter, run it after changes.
- **Testing**: Run relevant unit tests (`npm test`, `pytest`, etc.).
- **Manual Check**: If it's UI, describe what the change looks like and how to verify it visually.

---

## 📈 Engineering Quality Checklist

- [ ] Does this meet the "Rich Aesthetics" bar for UI?
- [ ] Are there any side effects on other workspace projects?
- [ ] Is the `Mastersheet.md` updated if this was a major fix?
- [ ] Is the code "Human Readable"? (Clean comments, logical flow).
