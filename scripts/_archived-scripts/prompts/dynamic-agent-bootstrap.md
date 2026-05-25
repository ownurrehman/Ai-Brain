# 🧠 Agent Bootstrap Prompt: Dynamic Task Execution

**Context Base**: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/`

---

### 1. MISSION PARAMETERS
- **Task**: [DESCRIBE TASK HERE - e.g., Write 2000 words on Semantic SEO]
- **Target Project**: [DOMAIN NAME - e.g., rankray.com]
- **Topic/Focus**: [TOPIC]

---

### 2. DISCOVERY PROTOCOL (DO THIS FIRST)
To save tokens and ensure precision, follow this **Agentic Loading** sequence:

1.  **Read the Master Index**: Open `INDEX.md` at the Context Base.
2.  **Resolve Project**: Find the **Target Project** in the "Projects" table. Load its **Mastersheet** and any linked `protocol.md` or `map.md`.
3.  **Resolve Rules**: Identify the rules required for the **Task** from the "Content Rules" or "Core" tables.
    - *If Writing*: Load `Semantic SEO Writer`, `Quality Standards`, `Self-Audit Protocol`, and `Pre-Publish Checklist`.
    - *If Publishing*: Load `WordPress Credentials` from the Access table.
4.  **Resolve Skills**: Locate and load the specific **SKILL.md** files relevant to the task (e.g., `Content Writing`, `WordPress Publisher`).
5.  **Stop**: Do NOT read other project folders or unrelated rules.

---

### 3. EXECUTION GUIDELINES
Once context is resolved, execute following the **Universal Workflow** in `core/AGENTS.md`:
- **Research**: Use loaded project mastersheets for specific entities/tone.
- **Strict Adherence**: Follow the **Semantic SEO (Koray-style)** rules for structure.
- **Mistake Prevention**:
    - Convert Markdown to **HTML** before WP API calls.
    - **Search Media Library** first to avoid duplicate image uploads.
    - Push as **DRAFT** and wait for explicit user approval before publishing.

---

### 4. BRAIN HUB REFERENCE
- **Index**: `INDEX.md`
- **Workflow**: `core/AGENTS.md`
- **Identity**: `IDENTITY.md` | `SOUL.md`
