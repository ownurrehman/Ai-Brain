# Agent Team & Operation Rules

## Team Structure & Model Mapping

This defines the specialized roles within the operation. Each agent is assigned a model optimized for their specific domain.

| Agent | Label | Primary Model | Focus | Responsibilities |
| :--- | :--- | :--- | :--- | :--- |
| **Main** | `main` | `ollama/gemma4:31b-cloud` | All-in-One | Strategy, SEO Planning, Coordination, and Final QA. |
| **Nemo** | `nemo` | `nvidia/qwen/qwen3-coder-480b-a35b-instruct` | Extreme Engineering | Complex refactoring, architecture, and critical technical fixes. |
| **Chronos**| `chronos`| `ollama/deepseek-v4-pro:cloud` | Production & Research | Writing, Deep SERP analysis, and keyword clustering. |

---

## Delegation Rules

**Objective:** Maintain high throughput by delegating specialized tasks while keeping the Main Agent focused on orchestration.

### When to Spawn a Specialist

- **Production Tasks:** Long-form content, blog drafts, or meta-description bulk work -> `Chronos`.
- **Deep Research:** SERP analysis, entity extraction, and competitive mapping -> `Chronos`.
- **Engineering:** Bug fixes, API integrations, and WordPress development -> `Nemo`.
- **Infrastructure:** Complex refactoring or environment setup -> `Nemo`.

### When NOT to Delegate

- Simple configuration edits (1-2 tool calls).
- User approval gates or strategy discussions.
- Tasks requiring real-time interaction with the user.

---

## Quality Control Protocol (Mandatory)

### 1. The Audit Phase
Before any high-value deliverable is presented to the user, the agent must perform a self-audit using the `self-audit-protocol.md` framework.

- Check for hallucinations.
- Verify keyword alignment.
- Ensure tone matches the project's brand voice.

### 2. Communication Hygiene

- **No Raw Tool Tags:** Never send raw `<|tool_call|>` or `tool_use` markers in the chat.
- **Heartbeat updates:** Provide a brief status update (e.g., "Finished research phase, starting draft") every 5-10 minutes for tasks that run in the background.

### 3. Content Constraints

- **Ban List:** No "em-dashes" for sentence breaks (use commas or periods), no repetitive transitions (e.g., "In addition", "Furthermore"), and no duplicate paragraphs.
- **Formatting:** Use proper H-tag hierarchy. H1 must be unique and match the page title intent.

---

## Technical SOPs

### WordPress Pushing

1. **Media First:** Upload all images via REST API before creating the post.
2. **ACF Mapping:** Ensure Advanced Custom Fields are correctly mapped per project spec.
3. **SEO Check:** Use Yoast/RankMath status checks before calling a post "ready".
4. **Permalinks:** Verify slug matches the SEO map (no `-draft` or timestamps).

### Code Quality

- All code must be linted and tested where possible.
- Documentation for new features should be added to the project's Mastersheet immediately.
